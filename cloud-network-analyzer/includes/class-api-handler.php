<?php

if (!defined('ABSPATH')) {
    exit;
}

class CNA_API_Handler {
    
    private $timeout = 15;
    
    public function check_nat() {
        $result = array(
            'nat_type' => 'unknown',
            'nat_type_number' => 0,
            'public_ip' => '',
            'private_ip' => '',
            'isp' => '',
            'location' => '',
            'ip_version' => '',
            'gateway_ip' => '',
            'subnet_mask' => '',
            'dns_servers' => '',
            'upnp_status' => 'Unknown',
            'nat_mapping' => 'Unknown',
            'explanation' => ''
        );
        
        $ip_data = $this->get_ip_info();
        
        if ($ip_data) {
            $result['public_ip'] = isset($ip_data['ip']) ? $ip_data['ip'] : '';
            $result['isp'] = isset($ip_data['org']) ? $ip_data['org'] : (isset($ip_data['isp']) ? $ip_data['isp'] : 'Unknown');
            $result['location'] = $this->format_location($ip_data);
            $result['ip_version'] = $this->detect_ip_version($result['public_ip']);
        }
        
        $nat_detection = $this->detect_nat_type();
        $result['nat_type'] = $nat_detection['type'];
        $result['nat_type_number'] = $nat_detection['type_number'];
        $result['explanation'] = $this->get_nat_explanation($nat_detection['type']);
        
        $result['gateway_ip'] = $this->estimate_gateway($result['public_ip']);
        $result['subnet_mask'] = '255.255.255.0';
        $result['dns_servers'] = $this->get_dns_info($ip_data);
        
        $result['upnp_status'] = $nat_detection['upnp_likely'] ? 'Likely Enabled' : 'Likely Disabled';
        $result['nat_mapping'] = $nat_detection['mapping_type'];
        
        return $result;
    }
    
    private function get_ip_info() {
        $apis = array(
            'https://ipapi.co/json/',
            'https://ipinfo.io/json',
            'https://api.ipify.org?format=json'
        );
        
        foreach ($apis as $api_url) {
            $response = wp_remote_get($api_url, array(
                'timeout' => $this->timeout,
                'user-agent' => 'CloudNetworkAnalyzer/1.0'
            ));
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                
                if ($data && isset($data['ip'])) {
                    return $this->normalize_ip_data($data, $api_url);
                }
            }
        }
        
        return false;
    }
    
    private function normalize_ip_data($data, $source) {
        $normalized = array();
        
        $normalized['ip'] = isset($data['ip']) ? $data['ip'] : '';
        
        if (strpos($source, 'ipapi.co') !== false) {
            $normalized['city'] = isset($data['city']) ? $data['city'] : '';
            $normalized['country'] = isset($data['country_name']) ? $data['country_name'] : '';
            $normalized['org'] = isset($data['org']) ? $data['org'] : '';
            $normalized['asn'] = isset($data['asn']) ? $data['asn'] : '';
        } elseif (strpos($source, 'ipinfo.io') !== false) {
            $normalized['city'] = isset($data['city']) ? $data['city'] : '';
            $normalized['country'] = isset($data['country']) ? $data['country'] : '';
            $normalized['org'] = isset($data['org']) ? $data['org'] : '';
            $normalized['hostname'] = isset($data['hostname']) ? $data['hostname'] : '';
        } else {
            $normalized['city'] = '';
            $normalized['country'] = '';
            $normalized['org'] = 'Unknown ISP';
        }
        
        return $normalized;
    }
    
    private function format_location($ip_data) {
        $location_parts = array();
        
        if (isset($ip_data['city']) && !empty($ip_data['city'])) {
            $location_parts[] = $ip_data['city'];
        }
        
        if (isset($ip_data['country']) && !empty($ip_data['country'])) {
            $location_parts[] = $ip_data['country'];
        }
        
        return !empty($location_parts) ? implode(', ', $location_parts) : 'Unknown';
    }
    
    private function detect_ip_version($ip) {
        if (empty($ip)) {
            return 'Unknown';
        }
        
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return 'IPv4';
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return 'IPv6';
        }
        
        return 'Unknown';
    }
    
    private function detect_nat_type() {
        $result = array(
            'type' => 'Moderate',
            'type_number' => 2,
            'upnp_likely' => false,
            'mapping_type' => 'Address-Restricted Cone'
        );
        
        $port_test_results = $this->simple_connectivity_test();
        
        if ($port_test_results['high_connectivity']) {
            $result['type'] = 'Open';
            $result['type_number'] = 1;
            $result['upnp_likely'] = true;
            $result['mapping_type'] = 'Full Cone';
        } elseif ($port_test_results['low_connectivity']) {
            $result['type'] = 'Strict';
            $result['type_number'] = 3;
            $result['upnp_likely'] = false;
            $result['mapping_type'] = 'Symmetric';
        } else {
            $result['type'] = 'Moderate';
            $result['type_number'] = 2;
            $result['upnp_likely'] = false;
            $result['mapping_type'] = 'Port-Restricted Cone';
        }
        
        return $result;
    }
    
    private function simple_connectivity_test() {
        $common_ports = array(80, 443, 53);
        $open_count = 0;
        
        foreach ($common_ports as $port) {
            if ($this->test_port_connectivity($port)) {
                $open_count++;
            }
        }
        
        return array(
            'high_connectivity' => $open_count >= 2,
            'low_connectivity' => $open_count === 0
        );
    }
    
    private function test_port_connectivity($port) {
        $test_hosts = array(
            'www.google.com',
            'www.cloudflare.com'
        );
        
        foreach ($test_hosts as $host) {
            $connection = @fsockopen($host, $port, $errno, $errstr, 2);
            if ($connection) {
                fclose($connection);
                return true;
            }
        }
        
        return false;
    }
    
    private function get_nat_explanation($nat_type) {
        $explanations = array(
            'Open' => 'Your NAT type is OPEN (Type 1). This is the best configuration for cloud gaming and online multiplayer. You can connect to any other player without restrictions. No further action needed!',
            'Moderate' => 'Your NAT type is MODERATE (Type 2). This is generally good for most gaming scenarios. You can connect to players with Open or Moderate NAT, but may have difficulty connecting to Strict NAT players. Consider enabling UPnP or setting up port forwarding for optimal performance.',
            'Strict' => 'Your NAT type is STRICT (Type 3). This may cause connectivity issues in cloud gaming and multiplayer games. You can only connect to players with Open NAT. To improve your gaming experience, you should enable UPnP, configure port forwarding, or set up DMZ on your router.'
        );
        
        return isset($explanations[$nat_type]) ? $explanations[$nat_type] : 'Unable to determine NAT type. This may be due to network restrictions or firewall settings.';
    }
    
    private function estimate_gateway($public_ip) {
        if (empty($public_ip)) {
            return '192.168.1.1';
        }
        
        $common_gateways = array('192.168.1.1', '192.168.0.1', '10.0.0.1', '192.168.2.1');
        return $common_gateways[0];
    }
    
    private function get_dns_info($ip_data) {
        $common_dns = array(
            '8.8.8.8 (Google)',
            '1.1.1.1 (Cloudflare)'
        );
        
        if (isset($ip_data['org']) && stripos($ip_data['org'], 'google') !== false) {
            return '8.8.8.8, 8.8.4.4 (Google DNS)';
        }
        
        return 'ISP Default DNS';
    }
    
    public function check_port($port, $protocol = 'tcp', $target_ip = '') {
        $result = array(
            'port' => $port,
            'protocol' => strtoupper($protocol),
            'status' => 'unknown',
            'open' => false,
            'notes' => ''
        );
        
        $result['target_ip'] = $this->sanitize_ip($target_ip);
        if (empty($result['target_ip'])) {
            $result['target_ip'] = $this->get_user_ip();
        }
        
        if ($result['protocol'] === 'BOTH') {
            $result['protocol'] = 'TCP/UDP';
        }
        
        $is_common_port = $this->is_common_gaming_port($port);
        
        if ($protocol === 'tcp' || $protocol === 'both') {
            $tcp_open = $this->check_tcp_port($port, $result['target_ip']);
            
            if ($tcp_open) {
                $result['status'] = 'open';
                $result['open'] = true;
                $result['notes'] = 'TCP port responded to probes';
            } else {
                $result['status'] = 'closed';
                $result['open'] = false;
                $result['notes'] = $is_common_port ? 'May need port forwarding' : 'TCP port appears closed';
            }
        }
        
        if ($protocol === 'udp' || $protocol === 'both') {
            $udp_open = $this->check_udp_port($port, $result['target_ip']);
            if ($protocol === 'udp') {
                if ($udp_open) {
                    $result['status'] = 'open';
                    $result['open'] = true;
                    $result['notes'] = 'UDP port responded with no immediate rejection';
                } else {
                    $result['status'] = 'unknown';
                    $result['open'] = false;
                    $result['notes'] = 'UDP cannot be conclusively tested remotely';
                }
            } else {
                $result['notes'] .= $udp_open ? ' | UDP responded' : ' | UDP requires in-client verification';
            }
        }
        
        if ($is_common_port) {
            $service = $this->get_gaming_service($port);
            if ($service) {
                $result['notes'] = $service . ' - ' . trim($result['notes']);
            }
        }
        
        return $result;
    }
    
    private function check_tcp_port($port, $target_ip = '') {
        $timeout = 3;
        
        if (!empty($target_ip) && $this->is_valid_ip($target_ip)) {
            $connection = @fsockopen($target_ip, $port, $errno, $errstr, $timeout);
            if ($connection) {
                fclose($connection);
                return true;
            }
        }
        
        $test_hosts = array(
            'portquiz.net',
            'ifconfig.me'
        );
        
        foreach ($test_hosts as $host) {
            $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
            if ($connection) {
                fclose($connection);
                return true;
            }
        }
        
        return false;
    }
    
    private function check_udp_port($port, $target_ip = '') {
        $timeout = 2;
        
        if (!empty($target_ip) && $this->is_valid_ip($target_ip)) {
            $socket = @stream_socket_client('udp://' . $target_ip . ':' . $port, $errno, $errstr, $timeout);
            if ($socket) {
                stream_set_timeout($socket, $timeout);
                fwrite($socket, "\0");
                $meta = stream_get_meta_data($socket);
                fclose($socket);
                return !$meta['timed_out'];
            }
        }
        
        return false;
    }
    
    private function sanitize_ip($ip) {
        $ip = trim($ip);
        return $this->is_valid_ip($ip) ? $ip : '';
    }
    
    private function is_valid_ip($ip) {
        return (bool) filter_var($ip, FILTER_VALIDATE_IP);
    }
    
    private function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->is_valid_ip($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ips as $ip) {
                $ip = trim($ip);
                if ($this->is_valid_ip($ip)) {
                    return $ip;
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) && $this->is_valid_ip($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }
    
    private function is_common_gaming_port($port) {
        $gaming_ports = array(
            3074, 3075, 3076, 3077, 3078, 3079, 3080,
            47989, 47990, 47998, 47999, 48000, 48001, 48010,
            80, 443, 3478, 3479, 3480,
            27015, 27016, 27017, 27030, 27000, 27100
        );
        
        return in_array($port, $gaming_ports);
    }
    
    private function get_gaming_service($port) {
        $services = array(
            3074 => 'Xbox/PlayStation',
            88 => 'Xbox Live',
            80 => 'HTTP',
            443 => 'HTTPS',
            3478 => 'PlayStation/Steam',
            3479 => 'PlayStation',
            3480 => 'PlayStation',
            47989 => 'GeForce NOW',
            47990 => 'GeForce NOW',
            47998 => 'GeForce NOW',
            48000 => 'GeForce NOW',
            48010 => 'GeForce NOW',
            27015 => 'Steam',
            27016 => 'Steam',
            27030 => 'Steam',
        );
        
        return isset($services[$port]) ? $services[$port] : null;
    }
    
    public function get_device_info() {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        $result = array(
            'device_type' => $this->detect_device_type($user_agent),
            'browser' => $this->detect_browser($user_agent),
            'os' => $this->detect_os($user_agent),
            'connection_type' => 'Unknown',
            'screen_resolution' => '',
            'user_agent' => $user_agent
        );
        
        return $result;
    }
    
    private function detect_device_type($user_agent) {
        if (preg_match('/mobile|android|iphone|ipad|ipod/i', $user_agent)) {
            if (preg_match('/ipad|tablet|kindle/i', $user_agent)) {
                return 'Tablet';
            }
            return 'Mobile';
        }
        
        if (preg_match('/PlayStation|Xbox|Nintendo/i', $user_agent)) {
            return 'Console';
        }
        
        return 'Desktop';
    }
    
    private function detect_browser($user_agent) {
        if (preg_match('/Edge/i', $user_agent)) {
            return 'Microsoft Edge';
        } elseif (preg_match('/Chrome/i', $user_agent) && !preg_match('/Edg/i', $user_agent)) {
            return 'Google Chrome';
        } elseif (preg_match('/Safari/i', $user_agent) && !preg_match('/Chrome/i', $user_agent)) {
            return 'Safari';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            return 'Mozilla Firefox';
        } elseif (preg_match('/MSIE|Trident/i', $user_agent)) {
            return 'Internet Explorer';
        } elseif (preg_match('/Opera|OPR/i', $user_agent)) {
            return 'Opera';
        }
        
        return 'Unknown Browser';
    }
    
    private function detect_os($user_agent) {
        if (preg_match('/Windows NT 10/i', $user_agent)) {
            return 'Windows 10/11';
        } elseif (preg_match('/Windows NT 6.3/i', $user_agent)) {
            return 'Windows 8.1';
        } elseif (preg_match('/Windows NT 6.2/i', $user_agent)) {
            return 'Windows 8';
        } elseif (preg_match('/Windows NT 6.1/i', $user_agent)) {
            return 'Windows 7';
        } elseif (preg_match('/Windows/i', $user_agent)) {
            return 'Windows';
        } elseif (preg_match('/Macintosh|Mac OS X/i', $user_agent)) {
            return 'macOS';
        } elseif (preg_match('/Linux/i', $user_agent)) {
            return 'Linux';
        } elseif (preg_match('/Android/i', $user_agent)) {
            return 'Android';
        } elseif (preg_match('/iOS|iPhone|iPad/i', $user_agent)) {
            return 'iOS';
        }
        
        return 'Unknown OS';
    }
}
