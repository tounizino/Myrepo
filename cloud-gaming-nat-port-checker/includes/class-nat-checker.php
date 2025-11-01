<?php
if (!defined('ABSPATH')) {
    exit;
}

class CGNPC_NAT_Checker {
    private $stun_servers = array(
        array('host' => 'stun.l.google.com', 'port' => 19302),
        array('host' => 'stun1.l.google.com', 'port' => 19302),
        array('host' => 'stun2.l.google.com', 'port' => 19302),
        array('host' => 'stun3.l.google.com', 'port' => 19302),
        array('host' => 'stun4.l.google.com', 'port' => 19302),
    );
    
    public function check_nat() {
        $client_ip = $this->get_client_ip();
        $headers_snapshot = $this->get_headers_snapshot();
        $stun_details = $this->run_stun_suite();
        $geo = $this->get_geo_info($client_ip);
        $port_mapping = $this->analyze_port_mapping_behavior();
        $nat_profile = $this->classify_nat($client_ip, $headers_snapshot, $stun_details, $port_mapping);
        
        $result = array(
            'public_ip' => $client_ip,
            'isp' => $geo['isp'],
            'country' => $geo['country'],
            'region' => $geo['region'],
            'city' => $geo['city'],
            'nat_type' => $nat_profile['code'],
            'nat_status' => $nat_profile['label'],
            'nat_color' => $nat_profile['color'],
            'nat_description' => $nat_profile['description'],
            'confidence' => $nat_profile['confidence'],
            'advice' => $nat_profile['suggestions'],
            'fallback' => $nat_profile['fallback'],
            'advanced_details' => array(
                'detection_method' => $nat_profile['method'],
                'reasoning' => $nat_profile['reason'],
                'headers' => $headers_snapshot,
                'stun' => $stun_details,
                'port_mapping' => $port_mapping,
            ),
            'timestamp' => current_time('mysql'),
        );
        
        $this->maybe_log_check($result);
        
        return $result;
    }
    
    private function get_client_ip() {
        $keys = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        );
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $value = $_SERVER[$key];
                if ('HTTP_X_FORWARDED_FOR' === $key && strpos($value, ',') !== false) {
                    $parts = explode(',', $value);
                    $value = trim($parts[0]);
                }
                if (filter_var($value, FILTER_VALIDATE_IP)) {
                    return sanitize_text_field($value);
                }
            }
        }
        return __('Unavailable', 'cloud-nat-port-checker');
    }
    
    private function get_geo_info($ip) {
        if (empty($ip) || __('Unavailable', 'cloud-nat-port-checker') === $ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return array(
                'country' => __('Unknown', 'cloud-nat-port-checker'),
                'region' => __('Unknown', 'cloud-nat-port-checker'),
                'city' => __('Unknown', 'cloud-nat-port-checker'),
                'isp' => __('Unknown', 'cloud-nat-port-checker'),
            );
        }
        
        $cache_key = 'cgnpc_geo_' . md5($ip);
        $cached = get_transient($cache_key);
        if ($cached) {
            return $cached;
        }
        
        $response = wp_remote_get('https://ipapi.co/' . rawurlencode($ip) . '/json/', array(
            'timeout' => 6,
            'sslverify' => true,
        ));
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return array(
                'country' => __('Unknown', 'cloud-nat-port-checker'),
                'region' => __('Unknown', 'cloud-nat-port-checker'),
                'city' => __('Unknown', 'cloud-nat-port-checker'),
                'isp' => __('Unknown', 'cloud-nat-port-checker'),
            );
        }
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($data)) {
            return array(
                'country' => __('Unknown', 'cloud-nat-port-checker'),
                'region' => __('Unknown', 'cloud-nat-port-checker'),
                'city' => __('Unknown', 'cloud-nat-port-checker'),
                'isp' => __('Unknown', 'cloud-nat-port-checker'),
            );
        }
        $geo = array(
            'country' => !empty($data['country_name']) ? sanitize_text_field($data['country_name']) : __('Unknown', 'cloud-nat-port-checker'),
            'region' => !empty($data['region']) ? sanitize_text_field($data['region']) : (!empty($data['region_code']) ? sanitize_text_field($data['region_code']) : __('Unknown', 'cloud-nat-port-checker')),
            'city' => !empty($data['city']) ? sanitize_text_field($data['city']) : __('Unknown', 'cloud-nat-port-checker'),
            'isp' => !empty($data['org']) ? sanitize_text_field($data['org']) : __('Unknown', 'cloud-nat-port-checker'),
        );
        set_transient($cache_key, $geo, MINUTE_IN_SECONDS * 30);
        return $geo;
    }
    
    private function get_headers_snapshot() {
        $snapshot = array(
            'forward_chain' => array(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
            'accept_language' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? sanitize_text_field($_SERVER['HTTP_ACCEPT_LANGUAGE']) : '',
            'via' => isset($_SERVER['HTTP_VIA']) ? sanitize_text_field($_SERVER['HTTP_VIA']) : '',
            'x_forwarded_for' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']) : '',
        );
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarded = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($forwarded as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    $snapshot['forward_chain'][] = $ip;
                }
            }
        }
        return $snapshot;
    }
    
    private function run_stun_suite() {
        if (!function_exists('socket_create')) {
            return array(
                'supported' => false,
                'message' => __('PHP sockets extension is not available on this server.', 'cloud-nat-port-checker'),
            );
        }
        foreach ($this->stun_servers as $server) {
            $base_test = $this->stun_binding_test($server['host'], $server['port']);
            if (!$base_test['success']) {
                continue;
            }
            $change_all = $this->stun_binding_test($server['host'], $server['port'], 0x06);
            $change_port = $this->stun_binding_test($server['host'], $server['port'], 0x02);
            $inference = $this->infer_stun_nat_profile($base_test, $change_all, $change_port);
            return array(
                'supported' => true,
                'server' => $server,
                'tests' => array(
                    'binding' => $base_test,
                    'change_all' => $change_all,
                    'change_port' => $change_port,
                ),
                'inference' => $inference,
            );
        }
        return array(
            'supported' => true,
            'error' => __('No STUN response from available servers. Network may block outbound UDP.', 'cloud-nat-port-checker'),
        );
    }
    
    private function stun_binding_test($host, $port, $change_flags = 0x00) {
        $result = array(
            'success' => false,
            'error' => '',
            'mapped_ip' => null,
            'mapped_port' => null,
            'rtt_ms' => null,
            'change_flags' => $change_flags,
            'response_ip' => null,
            'response_port' => null,
        );
        
        if (!function_exists('random_bytes')) {
            $result['error'] = __('Random bytes function unavailable.', 'cloud-nat-port-checker');
            return $result;
        }
        
        $socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            $result['error'] = __('Unable to create UDP socket.', 'cloud-nat-port-checker');
            return $result;
        }
        
        @socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 2, 'usec' => 0));
        @socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 2, 'usec' => 0));
        
        $transaction_id = random_bytes(12);
        $magic_cookie = 0x2112A442;
        $attributes = '';
        if ($change_flags > 0) {
            $attributes = pack('nnN', 0x0003, 0x0004, $change_flags);
        }
        $message_length = strlen($attributes);
        $packet = pack('nnN', 0x0001, $message_length, $magic_cookie) . $transaction_id . $attributes;
        
        $start_time = microtime(true);
        $bytes = @socket_sendto($socket, $packet, strlen($packet), 0, $host, $port);
        if (false === $bytes) {
            $result['error'] = __('Failed to send STUN request.', 'cloud-nat-port-checker');
            @socket_close($socket);
            return $result;
        }
        
        $response = '';
        $from = '';
        $from_port = 0;
        $bytes_received = @socket_recvfrom($socket, $response, 2048, 0, $from, $from_port);
        $end_time = microtime(true);
        @socket_close($socket);
        
        if (false === $bytes_received || empty($response)) {
            $result['error'] = __('STUN server did not respond.', 'cloud-nat-port-checker');
            return $result;
        }
        
        $parsed = $this->parse_stun_response($response, $transaction_id);
        if (!$parsed['valid']) {
            $result['error'] = __('Invalid STUN response.', 'cloud-nat-port-checker');
            return $result;
        }
        
        $result['success'] = true;
        $result['mapped_ip'] = $parsed['mapped_ip'];
        $result['mapped_port'] = $parsed['mapped_port'];
        $result['rtt_ms'] = round(($end_time - $start_time) * 1000, 2);
        $result['response_ip'] = $from;
        $result['response_port'] = $from_port;
        return $result;
    }
    
    private function parse_stun_response($response, $transaction_id) {
        $parsed = array(
            'valid' => false,
            'mapped_ip' => null,
            'mapped_port' => null,
        );
        if (strlen($response) < 20) {
            return $parsed;
        }
        $header = unpack('ntype/nlength/Ncookie', substr($response, 0, 8));
        $received_transaction = substr($response, 8, 12);
        if (empty($header['type']) || empty($header['cookie']) || $received_transaction !== $transaction_id) {
            return $parsed;
        }
        $offset = 20;
        $total_length = 20 + $header['length'];
        while ($offset + 4 <= strlen($response) && $offset < $total_length) {
            $attr_header = unpack('ntype/nlength', substr($response, $offset, 4));
            $offset += 4;
            $length = $attr_header['length'];
            $value = substr($response, $offset, $length);
            if (0x0001 === $attr_header['type'] && $length >= 8) { // MAPPED-ADDRESS
                $parsed['mapped_ip'] = $this->decode_stun_address($value, false);
                $parsed['mapped_port'] = $this->decode_stun_port($value, false);
                $parsed['valid'] = true;
            }
            if (0x0020 === $attr_header['type'] && $length >= 8) { // XOR-MAPPED-ADDRESS
                $parsed['mapped_ip'] = $this->decode_stun_address($value, true);
                $parsed['mapped_port'] = $this->decode_stun_port($value, true);
                $parsed['valid'] = true;
            }
            $offset += $length;
            if ($length % 4 !== 0) {
                $offset += 4 - ($length % 4);
            }
        }
        return $parsed;
    }
    
    private function decode_stun_address($value, $xor = true) {
        $family = ord($value[1]);
        if (0x01 !== $family || strlen($value) < 8) {
            return null;
        }
        $address_bytes = substr($value, 4, 4);
        if ($xor) {
            $magic = pack('N', 0x2112A442);
            $address_bytes = $address_bytes ^ $magic;
        }
        $ip = inet_ntop($address_bytes);
        return $ip ? $ip : null;
    }
    
    private function decode_stun_port($value, $xor = true) {
        $port_data = substr($value, 2, 2);
        $port = unpack('n', $port_data)[1];
        if ($xor) {
            $port ^= 0x2112;
        }
        return intval($port);
    }
    
    private function infer_stun_nat_profile($binding, $change_all, $change_port) {
        if (!$binding['success']) {
            return array(
                'profile' => 'blocked',
                'description' => __('Unable to reach STUN server. UDP may be blocked.', 'cloud-nat-port-checker'),
                'confidence' => 'low',
            );
        }
        if ($change_all['success']) {
            return array(
                'profile' => 'full_cone',
                'description' => __('Change-IP request succeeded. Connection behaves like full-cone/open NAT.', 'cloud-nat-port-checker'),
                'confidence' => 'medium',
            );
        }
        if ($change_port['success']) {
            return array(
                'profile' => 'restricted_cone',
                'description' => __('Change-port request succeeded. Connection resembles restricted cone NAT.', 'cloud-nat-port-checker'),
                'confidence' => 'medium',
            );
        }
        return array(
            'profile' => 'port_restricted',
            'description' => __('STUN change requests failed. NAT likely port-restricted or symmetric.', 'cloud-nat-port-checker'),
            'confidence' => 'medium',
        );
    }
    
    private function analyze_port_mapping_behavior() {
        $remote_port = isset($_SERVER['REMOTE_PORT']) ? intval($_SERVER['REMOTE_PORT']) : 0;
        $server_port = isset($_SERVER['SERVER_PORT']) ? intval($_SERVER['SERVER_PORT']) : 0;
        $behavior = array(
            'description' => __('Unknown', 'cloud-nat-port-checker'),
            'remote_port' => $remote_port,
            'server_port' => $server_port,
        );
        if ($remote_port === $server_port && $remote_port > 0) {
            $behavior['description'] = __('Direct mapping detected (ideal).', 'cloud-nat-port-checker');
        } elseif ($remote_port > 0 && $remote_port >= 49152) {
            $behavior['description'] = __('Ephemeral port detected. Likely PAT with dynamic mapping.', 'cloud-nat-port-checker');
        } elseif ($remote_port > 0) {
            $behavior['description'] = __('Translated port mapping detected.', 'cloud-nat-port-checker');
        }
        return $behavior;
    }
    
    private function classify_nat($ip, $headers, $stun, $port_mapping) {
        $result = array(
            'code' => 'unknown',
            'label' => __('Unknown NAT', 'cloud-nat-port-checker'),
            'color' => 'gray',
            'description' => __('We could not confidently determine your NAT type. Review the advanced diagnostics below.', 'cloud-nat-port-checker'),
            'reason' => __('Insufficient data collected.', 'cloud-nat-port-checker'),
            'confidence' => 'low',
            'suggestions' => array(
                __('Verify router UPnP is enabled and gaming ports are forwarded.', 'cloud-nat-port-checker'),
                __('Disable VPNs or proxy services during testing.', 'cloud-nat-port-checker'),
            ),
            'fallback' => __('Manual verification recommended. Run built-in console/network tests for confirmation.', 'cloud-nat-port-checker'),
            'method' => __('Heuristic (headers + STUN)', 'cloud-nat-port-checker'),
        );
        if (empty($ip) || __('Unavailable', 'cloud-nat-port-checker') === $ip) {
            return $result;
        }
        $is_ipv6 = strpos($ip, ':') !== false;
        $is_private = $this->is_private_ip($ip);
        $is_cgn = $this->is_carrier_grade_nat($ip);
        $forward_hops = isset($headers['forward_chain']) ? count($headers['forward_chain']) : 0;
        $stun_profile = isset($stun['inference']['profile']) ? $stun['inference']['profile'] : 'unknown';
        
        if ($is_ipv6 && !$forward_hops) {
            return array(
                'code' => 'open',
                'label' => __('Open NAT (Type 1)', 'cloud-nat-port-checker'),
                'color' => 'green',
                'description' => __('Native IPv6 address detected with direct routing. Perfect for cloud gaming.', 'cloud-nat-port-checker'),
                'reason' => __('IPv6 connectivity without proxy headers implies end-to-end reachability.', 'cloud-nat-port-checker'),
                'confidence' => 'high',
                'suggestions' => array(
                    __('Keep IPv6 enabled to maintain open connectivity.', 'cloud-nat-port-checker'),
                    __('You are ready for zero-lag cloud streaming.', 'cloud-nat-port-checker'),
                ),
                'fallback' => __('No fallback needed. Enjoy your optimized connection!', 'cloud-nat-port-checker'),
                'method' => __('Direct IPv6 detection', 'cloud-nat-port-checker'),
            );
        }
        
        if ($is_private || $is_cgn || $forward_hops > 1) {
            $reason_bits = array();
            if ($is_private) {
                $reason_bits[] = __('Private or RFC1918 address observed at edge.', 'cloud-nat-port-checker');
            }
            if ($is_cgn) {
                $reason_bits[] = __('Carrier-grade NAT address range detected (100.64.0.0/10).', 'cloud-nat-port-checker');
            }
            if ($forward_hops > 1) {
                $reason_bits[] = sprintf(__('Multiple proxy hops detected (%d).', 'cloud-nat-port-checker'), $forward_hops);
            }
            return array(
                'code' => 'strict',
                'label' => __('Strict NAT (Type 3)', 'cloud-nat-port-checker'),
                'color' => 'red',
                'description' => __('Connections are heavily restricted. Expect issues with P2P matches and streaming.', 'cloud-nat-port-checker'),
                'reason' => implode(' ', $reason_bits),
                'confidence' => 'high',
                'suggestions' => array(
                    __('Enable full-cone NAT or DMZ for your device.', 'cloud-nat-port-checker'),
                    __('Contact your ISP to request public IPv4 or enable bridge mode.', 'cloud-nat-port-checker'),
                    __('Review router firewall rules and disable double-NAT setups.', 'cloud-nat-port-checker'),
                ),
                'fallback' => __('Consider tethering over mobile data or using a VPN with port forwarding as a temporary workaround.', 'cloud-nat-port-checker'),
                'method' => __('Header chain analysis', 'cloud-nat-port-checker'),
            );
        }
        
        if ('full_cone' === $stun_profile && !$forward_hops) {
            return array(
                'code' => 'open',
                'label' => __('Open NAT (Type 1)', 'cloud-nat-port-checker'),
                'color' => 'green',
                'description' => __('Ports appear fully reachable with minimal translation. Ideal for low-latency streaming.', 'cloud-nat-port-checker'),
                'reason' => __('STUN change-all request succeeded indicating full-cone behavior.', 'cloud-nat-port-checker'),
                'confidence' => 'medium',
                'suggestions' => array(
                    __('Maintain UPnP/IGD enabled and avoid double NAT setups.', 'cloud-nat-port-checker'),
                    __('You are ready for competitive cloud play at maximum fidelity.', 'cloud-nat-port-checker'),
                ),
                'fallback' => __('No fallback required. Keep monitoring latency with the auto-refresh toggle.', 'cloud-nat-port-checker'),
                'method' => __('STUN inference', 'cloud-nat-port-checker'),
            );
        }
        
        if ('restricted_cone' === $stun_profile || 'port_restricted' === $stun_profile) {
            return array(
                'code' => 'moderate',
                'label' => __('Moderate NAT (Type 2)', 'cloud-nat-port-checker'),
                'color' => 'yellow',
                'description' => __('Good overall compatibility. Some peer-hosted lobbies may need port forwarding.', 'cloud-nat-port-checker'),
                'reason' => __('STUN change request partially blocked indicating restricted cone behavior.', 'cloud-nat-port-checker'),
                'confidence' => 'medium',
                'suggestions' => array(
                    __('Enable UPnP or manually forward recommended cloud gaming ports.', 'cloud-nat-port-checker'),
                    __('Prefer wired connections and disable router-level SIP ALG.', 'cloud-nat-port-checker'),
                ),
                'fallback' => __('If matchmaking fails, temporarily switch your device to DMZ or try VPN port forwarding.', 'cloud-nat-port-checker'),
                'method' => __('STUN inference', 'cloud-nat-port-checker'),
            );
        }
        
        return array(
            'code' => 'moderate',
            'label' => __('Moderate NAT (Type 2)', 'cloud-nat-port-checker'),
            'color' => 'yellow',
            'description' => __('Your address looks public but we detected some translation. Gameplay should be stable after minor tuning.', 'cloud-nat-port-checker'),
            'reason' => __('Public IPv4 detected without proxy chain, but STUN tests inconclusive.', 'cloud-nat-port-checker'),
            'confidence' => 'low',
            'suggestions' => array(
                __('Verify that UPnP is active and no firewall rules block outbound UDP.', 'cloud-nat-port-checker'),
                __('Optionally map critical ports (3074, 47984-48010, 1935, 3478-3480).', 'cloud-nat-port-checker'),
            ),
            'fallback' => __('Run console/PC NAT diagnostic utilities for confirmation if issues persist.', 'cloud-nat-port-checker'),
            'method' => __('Heuristic (public IPv4)', 'cloud-nat-port-checker'),
        );
    }
    
    private function is_private_ip($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }
        $private_ranges = array(
            '10.0.0.0|10.255.255.255',
            '172.16.0.0|172.31.255.255',
            '192.168.0.0|192.168.255.255',
            '169.254.0.0|169.254.255.255',
        );
        $ip_long = ip2long($ip);
        foreach ($private_ranges as $range) {
            list($start, $end) = explode('|', $range);
            if ($ip_long >= ip2long($start) && $ip_long <= ip2long($end)) {
                return true;
            }
        }
        return false;
    }
    
    private function is_carrier_grade_nat($ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }
        $ip_long = ip2long($ip);
        $start = ip2long('100.64.0.0');
        $end = ip2long('100.127.255.255');
        return ($ip_long >= $start && $ip_long <= $end);
    }
    
    private function maybe_log_check($result) {
        $settings = get_option('cgnpc_settings', array());
        if (empty($settings['logging_enabled'])) {
            return;
        }
        $logs = get_option('cgnpc_logs', array());
        if (!is_array($logs)) {
            $logs = array();
        }
        $logs[] = wp_json_encode(array(
            'timestamp' => $result['timestamp'],
            'nat' => $result['nat_type'],
            'ip' => $this->truncate_ip($result['public_ip']),
            'confidence' => $result['confidence'],
        ));
        if (count($logs) > 120) {
            $logs = array_slice($logs, -120);
        }
        update_option('cgnpc_logs', $logs);
    }
    
    private function truncate_ip($ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            return sprintf('%s.%s.xxx.xxx', $parts[0], $parts[1]);
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            $first = array_slice($parts, 0, 3);
            return implode(':', $first) . ':xxxx';
        }
        return 'x.x.x.x';
    }
}
