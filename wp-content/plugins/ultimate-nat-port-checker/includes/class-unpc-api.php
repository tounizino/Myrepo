<?php
/**
 * Utility and data access class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UNPC_API {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {}
    
    public static function get_default_settings() {
        return array(
            'primary_color' => '#00ff88',
            'secondary_color' => '#ff0055',
            'nat_type1_color' => '#00ff88',
            'nat_type2_color' => '#ffaa00',
            'nat_type3_color' => '#ff0055',
            'port_open_color' => '#00ff88',
            'port_closed_color' => '#ff0055',
            'container_width' => '1200',
            'font_size_preset' => 'small',
            'header_text' => 'Ultimate NAT & Port Checker',
            'footer_text' => '© 2026 Your Cloud Gaming Blog. All Rights Reserved.',
            'privacy_note' => 'This tool does not store any personal information. All checks are performed in real-time and data is not saved on our servers.',
            'home_url' => home_url(),
            'guides' => json_encode(array())
        );
    }
    
    public static function get_settings() {
        $options = get_option('unpc_settings', array());
        return wp_parse_args($options, self::get_default_settings());
    }
    
    public static function get_guides() {
        $settings = self::get_settings();
        $guides = isset($settings['guides']) ? json_decode($settings['guides'], true) : array();
        if (!is_array($guides)) {
            $guides = array();
        }
        return $guides;
    }
    
    public static function get_port_presets() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'unpc_port_presets';
        $results = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY platform_name ASC", ARRAY_A);
        if (!$results) {
            return array();
        }
        return $results;
    }
    
    public static function sanitize_host($host) {
        $host = trim($host);
        if (empty($host)) {
            return '';
        }
        if (filter_var($host, FILTER_VALIDATE_IP) || filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return $host;
        }
        return '';
    }
    
    public static function sanitize_ports_input($ports_input) {
        $ports_input = sanitize_text_field($ports_input);
        $ports_input = str_replace(array(';', '\\', ':', "\n"), ',', $ports_input);
        $ports_input = preg_replace('/\s+/', '', $ports_input);
        return $ports_input;
    }
    
    public static function expand_ports($ports_input) {
        $ports = array();
        $chunks = explode(',', $ports_input);
        foreach ($chunks as $chunk) {
            if (empty($chunk)) {
                continue;
            }
            if (strpos($chunk, '-') !== false) {
                list($start, $end) = array_map('intval', explode('-', $chunk));
                if ($start > 0 && $end >= $start && $end <= 65535) {
                    for ($i = $start; $i <= $end; $i++) {
                        $ports[] = $i;
                    }
                }
            } else {
                $port = intval($chunk);
                if ($port > 0 && $port <= 65535) {
                    $ports[] = $port;
                }
            }
        }
        $ports = array_unique($ports);
        sort($ports);
        return $ports;
    }
    
    public static function check_port($host, $port, $protocol = 'TCP', $timeout = 1.5) {
        $protocol = strtoupper($protocol);
        $status = array(
            'port' => (int) $port,
            'protocol' => $protocol,
            'status' => 'unknown',
            'latency' => null,
            'message' => __('Unable to determine port status', 'ultimate-nat-port-checker')
        );
        
        $port = (int) $port;
        if ($port < 1 || $port > 65535) {
            $status['message'] = __('Invalid port number', 'ultimate-nat-port-checker');
            return $status;
        }
        
        $host = self::sanitize_host($host);
        if (empty($host)) {
            $status['message'] = __('Invalid host or IP address', 'ultimate-nat-port-checker');
            return $status;
        }
        
        $start_time = microtime(true);
        
        if ('UDP' === $protocol) {
            $connection = @stream_socket_client('udp://' . $host . ':' . $port, $errno, $errstr, $timeout);
        } else {
            $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
        }
        
        $latency = round((microtime(true) - $start_time) * 1000, 2);
        
        if ($connection) {
            $status['status'] = 'open';
            $status['message'] = __('Port is open and reachable', 'ultimate-nat-port-checker');
            $status['latency'] = $latency;
            if ('UDP' === $protocol) {
                fclose($connection);
            } else {
                fclose($connection);
            }
        } else {
            $status['status'] = 'closed';
            $status['message'] = sprintf(__('Port closed: %s', 'ultimate-nat-port-checker'), $errstr ?: __('No response', 'ultimate-nat-port-checker'));
            $status['latency'] = $latency;
        }
        
        return $status;
    }
}
