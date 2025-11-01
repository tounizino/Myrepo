<?php
if (!defined('ABSPATH')) {
    exit;
}

class CGNPC_Port_Checker {
    public static function get_platform_presets() {
        return array(
            'xbox' => array(
                'label' => __('Xbox Cloud Gaming', 'cloud-nat-port-checker'),
                'ports' => array(
                    array('port' => 3074, 'protocol' => 'udp', 'label' => 'Xbox Live (UDP)'),
                    array('port' => 3074, 'protocol' => 'tcp', 'label' => 'Xbox Live (TCP)'),
                    array('port' => 53, 'protocol' => 'udp', 'label' => 'DNS'),
                    array('port' => 80, 'protocol' => 'tcp', 'label' => 'HTTP'),
                    array('port' => 443, 'protocol' => 'tcp', 'label' => 'HTTPS'),
                ),
            ),
            'gfn' => array(
                'label' => __('GeForce NOW', 'cloud-nat-port-checker'),
                'ports' => array(
                    array('port' => 47984, 'protocol' => 'udp', 'label' => 'Streaming Core (UDP)'),
                    array('port' => 47984, 'protocol' => 'tcp', 'label' => 'Streaming Core (TCP)'),
                    array('port' => 47989, 'protocol' => 'udp', 'label' => 'Adaptive Streaming (UDP)'),
                    array('port' => 48010, 'protocol' => 'tcp', 'label' => 'Control / RTSP'),
                ),
            ),
            'psremote' => array(
                'label' => __('PS Remote Play', 'cloud-nat-port-checker'),
                'ports' => array(
                    array('port' => 9295, 'protocol' => 'tcp', 'label' => 'Control'),
                    array('port' => 9296, 'protocol' => 'udp', 'label' => 'Video Stream'),
                    array('port' => 9297, 'protocol' => 'udp', 'label' => 'Auxiliary Data'),
                    array('port' => 987, 'protocol' => 'udp', 'label' => 'Wake-on-LAN'),
                ),
            ),
            'steam' => array(
                'label' => __('Steam Link', 'cloud-nat-port-checker'),
                'ports' => array(
                    array('port' => 27031, 'protocol' => 'udp', 'label' => 'Discovery'),
                    array('port' => 27036, 'protocol' => 'udp', 'label' => 'Streaming UDP'),
                    array('port' => 27037, 'protocol' => 'tcp', 'label' => 'Streaming TCP'),
                ),
            ),
            'shadow' => array(
                'label' => __('Shadow PC', 'cloud-nat-port-checker'),
                'ports' => array(
                    array('port' => 41182, 'protocol' => 'udp', 'label' => 'Video Stream'),
                    array('port' => 41182, 'protocol' => 'tcp', 'label' => 'Control'),
                    array('port' => 50036, 'protocol' => 'tcp', 'label' => 'Audio'),
                    array('port' => 443, 'protocol' => 'tcp', 'label' => 'HTTPS Fallback'),
                ),
            ),
            'stadia' => array(
                'label' => __('Amazon Luna / Stadia', 'cloud-nat-port-checker'),
                'ports' => array(
                    array('port' => 443, 'protocol' => 'tcp', 'label' => 'HTTPS / Signaling'),
                    array('port' => 3478, 'protocol' => 'udp', 'label' => 'STUN'),
                    array('port' => 44700, 'protocol' => 'udp', 'label' => 'WebRTC Media'),
                ),
            ),
        );
    }
    
    public function check_multiple_ports($items, $host = null, $attempts = 3) {
        $results = array();
        foreach ($items as $item) {
            if (!isset($item['port'])) {
                continue;
            }
            $port = intval($item['port']);
            $protocol = isset($item['protocol']) ? strtolower($item['protocol']) : 'tcp';
            $results[] = $this->check_port($port, $protocol, $host, $attempts);
        }
        return $results;
    }
    
    public function check_port($port, $protocol = 'tcp', $host = null, $attempts = 3) {
        $port = intval($port);
        $protocol = strtolower($protocol);
        $attempts = max(1, intval($attempts));
        
        if ($port < 1 || $port > 65535) {
            return $this->format_result($port, $protocol, $host, 'error', 'red', __('Invalid port number.', 'cloud-nat-port-checker'));
        }
        if (!in_array($protocol, array('tcp', 'udp'), true)) {
            return $this->format_result($port, $protocol, $host, 'error', 'red', __('Unsupported protocol.', 'cloud-nat-port-checker'));
        }
        
        $host = $this->sanitize_host($host);
        if (!$host) {
            $host = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '127.0.0.1';
        }
        
        if ('tcp' === $protocol) {
            return $this->check_tcp($host, $port, $attempts);
        }
        return $this->check_udp($host, $port, $attempts);
    }
    
    private function check_tcp($host, $port, $attempts) {
        if (!function_exists('fsockopen')) {
            return $this->format_result($port, 'tcp', $host, 'unavailable', 'gray', __('fsockopen() is disabled on this server.', 'cloud-nat-port-checker'));
        }
        $success = 0;
        $latencies = array();
        $errors = array();
        for ($i = 0; $i < $attempts; $i++) {
            $start = microtime(true);
            $errno = 0;
            $errstr = '';
            $timeout = 2.5;
            $conn = @fsockopen($host, $port, $errno, $errstr, $timeout);
            $elapsed = microtime(true) - $start;
            if ($conn) {
                fclose($conn);
                $success++;
                $latencies[] = $elapsed * 1000;
            } else {
                $errors[] = $errno . ' ' . trim($errstr);
            }
            usleep(150000); // 150ms pause to avoid rapid-fire connections
        }
        $packet_loss = round((($attempts - $success) / $attempts) * 100, 2);
        $latency = $success > 0 ? round(array_sum($latencies) / count($latencies), 2) : null;
        if ($success > 0) {
            $result = $this->format_result($port, 'tcp', $host, 'open', 'green', __('Port is reachable.', 'cloud-nat-port-checker'));
            $result['latency_ms'] = $latency;
            $result['packet_loss'] = $packet_loss;
            $result['attempts'] = $attempts;
            $result['successful_attempts'] = $success;
            $result['details'] = sprintf(__('Successful connections: %d/%d. Avg latency: %sms.', 'cloud-nat-port-checker'), $success, $attempts, $latency);
        } else {
            $result = $this->format_result($port, 'tcp', $host, 'closed', 'red', __('Port is closed or filtered.', 'cloud-nat-port-checker'));
            $result['latency_ms'] = null;
            $result['packet_loss'] = 100;
            $result['attempts'] = $attempts;
            $result['successful_attempts'] = 0;
            $result['details'] = !empty($errors) ? implode('; ', array_unique($errors)) : __('Connection attempts timed out.', 'cloud-nat-port-checker');
        }
        $this->maybe_log($result);
        return $result;
    }
    
    private function check_udp($host, $port, $attempts) {
        if (!function_exists('socket_create')) {
            return $this->format_result($port, 'udp', $host, 'unavailable', 'gray', __('PHP sockets extension is disabled.', 'cloud-nat-port-checker'));
        }
        $socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (!$socket) {
            return $this->format_result($port, 'udp', $host, 'error', 'red', __('Unable to create UDP socket.', 'cloud-nat-port-checker'));
        }
        @socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 2, 'usec' => 0));
        @socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec' => 2, 'usec' => 0));
        $latencies = array();
        $responses = 0;
        for ($i = 0; $i < $attempts; $i++) {
            $payload = 'cgnpc-ping-' . time();
            $start = microtime(true);
            $sent = @socket_sendto($socket, $payload, strlen($payload), 0, $host, $port);
            if (false === $sent) {
                continue;
            }
            $response = '';
            $from = '';
            $from_port = 0;
            $bytes = @socket_recvfrom($socket, $response, 2048, 0, $from, $from_port);
            $elapsed = microtime(true) - $start;
            if (false !== $bytes && !empty($response)) {
                $responses++;
                $latencies[] = $elapsed * 1000;
            }
            usleep(150000);
        }
        @socket_close($socket);
        if ($responses > 0) {
            $latency = round(array_sum($latencies) / count($latencies), 2);
            $result = $this->format_result($port, 'udp', $host, 'open', 'green', __('UDP port responded to probes.', 'cloud-nat-port-checker'));
            $result['latency_ms'] = $latency;
            $result['packet_loss'] = round((($attempts - $responses) / $attempts) * 100, 2);
            $result['attempts'] = $attempts;
            $result['successful_attempts'] = $responses;
            $result['details'] = sprintf(__('Responses: %d/%d. Avg latency: %sms.', 'cloud-nat-port-checker'), $responses, $attempts, $latency);
        } else {
            $result = $this->format_result($port, 'udp', $host, 'unknown', 'yellow', __('No UDP response received (could still be open).', 'cloud-nat-port-checker'));
            $result['latency_ms'] = null;
            $result['packet_loss'] = __('N/A', 'cloud-nat-port-checker');
            $result['attempts'] = $attempts;
            $result['successful_attempts'] = 0;
            $result['details'] = __('UDP is connectionless; many services stay silent even when reachable.', 'cloud-nat-port-checker');
        }
        $this->maybe_log($result);
        return $result;
    }
    
    private function format_result($port, $protocol, $host, $status, $color, $message) {
        return array(
            'port' => intval($port),
            'protocol' => strtoupper($protocol),
            'host' => $host,
            'status' => $status,
            'color' => $color,
            'message' => $message,
            'latency_ms' => null,
            'packet_loss' => null,
            'attempts' => 0,
            'successful_attempts' => 0,
            'details' => '',
        );
    }
    
    private function sanitize_host($host) {
        if (empty($host)) {
            return '';
        }
        $host = sanitize_text_field($host);
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $host;
        }
        if (preg_match('/^([a-z0-9\.-]+)$/i', $host)) {
            return $host;
        }
        return '';
    }
    
    private function maybe_log($result) {
        $settings = get_option('cgnpc_settings', array());
        if (empty($settings['logging_enabled'])) {
            return;
        }
        $logs = get_option('cgnpc_logs', array());
        if (!is_array($logs)) {
            $logs = array();
        }
        $logs[] = wp_json_encode(array(
            'timestamp' => current_time('mysql'),
            'port' => $result['port'],
            'protocol' => $result['protocol'],
            'host' => $result['host'],
            'status' => $result['status'],
            'latency_ms' => $result['latency_ms'],
            'packet_loss' => $result['packet_loss'],
        ));
        if (count($logs) > 120) {
            $logs = array_slice($logs, -120);
        }
        update_option('cgnpc_logs', $logs);
    }
}
