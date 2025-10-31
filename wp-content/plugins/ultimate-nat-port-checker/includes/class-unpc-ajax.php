<?php
/**
 * AJAX handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class UNPC_Ajax {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_unpc_check_nat', array($this, 'check_nat'));
        add_action('wp_ajax_nopriv_unpc_check_nat', array($this, 'check_nat'));
        
        add_action('wp_ajax_unpc_check_port', array($this, 'check_port'));
        add_action('wp_ajax_nopriv_unpc_check_port', array($this, 'check_port'));
        
        add_action('wp_ajax_unpc_get_ip_info', array($this, 'get_ip_info'));
        add_action('wp_ajax_nopriv_unpc_get_ip_info', array($this, 'get_ip_info'));
    }
    
    public function check_nat() {
        check_ajax_referer('unpc_nonce', 'nonce');
        
        $user_ip = $this->get_user_ip();
        
        $ip_info = $this->get_ip_geolocation($user_ip);
        
        $nat_type = $this->determine_nat_type($user_ip);
        
        $response = array(
            'success' => true,
            'data' => array(
                'ip' => $user_ip,
                'nat_type' => $nat_type['type'],
                'nat_type_name' => $nat_type['name'],
                'nat_description' => $nat_type['description'],
                'country' => isset($ip_info['country']) ? $ip_info['country'] : 'Unknown',
                'country_code' => isset($ip_info['country_code']) ? $ip_info['country_code'] : '',
                'region' => isset($ip_info['region']) ? $ip_info['region'] : 'Unknown',
                'city' => isset($ip_info['city']) ? $ip_info['city'] : 'Unknown',
                'timezone' => isset($ip_info['timezone']) ? $ip_info['timezone'] : 'Unknown',
                'isp' => isset($ip_info['isp']) ? $ip_info['isp'] : 'Unknown',
                'org' => isset($ip_info['org']) ? $ip_info['org'] : 'Unknown',
                'as' => isset($ip_info['as']) ? $ip_info['as'] : 'Unknown',
                'lat' => isset($ip_info['lat']) ? $ip_info['lat'] : '',
                'lon' => isset($ip_info['lon']) ? $ip_info['lon'] : '',
                'postal' => isset($ip_info['postal']) ? $ip_info['postal'] : '',
                'connection_type' => $this->detect_connection_type()
            )
        );
        
        wp_send_json($response);
    }
    
    public function check_port() {
        check_ajax_referer('unpc_nonce', 'nonce');
        
        $port = isset($_POST['port']) ? sanitize_text_field($_POST['port']) : '';
        $host = isset($_POST['host']) ? UNPC_API::sanitize_host($_POST['host']) : '';
        $protocol = isset($_POST['protocol']) ? strtoupper(sanitize_text_field($_POST['protocol'])) : 'TCP';
        
        if (empty($host)) {
            $host = $this->get_user_ip();
        }
        
        if (empty($port)) {
            wp_send_json_error(array('message' => 'Port number is required'));
            return;
        }
        
        $ports_input = UNPC_API::sanitize_ports_input($port);
        $ports = UNPC_API::expand_ports($ports_input);
        
        if (empty($ports)) {
            wp_send_json_error(array('message' => 'Invalid port number or range'));
            return;
        }
        
        if (count($ports) > 20) {
            wp_send_json_error(array('message' => 'Too many ports to check at once. Maximum is 20.'));
            return;
        }
        
        $results = array();
        foreach ($ports as $single_port) {
            $result = UNPC_API::check_port($host, $single_port, $protocol);
            $results[] = $result;
        }
        
        wp_send_json_success(array(
            'host' => $host,
            'results' => $results
        ));
    }
    
    public function get_ip_info() {
        check_ajax_referer('unpc_nonce', 'nonce');
        
        $user_ip = $this->get_user_ip();
        $ip_info = $this->get_ip_geolocation($user_ip);
        
        wp_send_json_success(array(
            'ip' => $user_ip,
            'info' => $ip_info
        ));
    }
    
    private function get_user_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ip_list[0]);
        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) ? $ip : '127.0.0.1';
    }
    
    private function get_ip_geolocation($ip) {
        if ('127.0.0.1' === $ip || '::1' === $ip || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            return array(
                'country' => 'Local Network',
                'country_code' => 'LAN',
                'region' => 'Private',
                'city' => 'Local',
                'timezone' => wp_timezone_string(),
                'isp' => 'Local Network',
                'org' => 'Private Network',
                'as' => 'N/A',
                'lat' => '0',
                'lon' => '0',
                'postal' => ''
            );
        }
        
        $transient_key = 'unpc_ip_info_' . md5($ip);
        $cached_info = get_transient($transient_key);
        
        if (false !== $cached_info) {
            return $cached_info;
        }
        
        $response = wp_remote_get('http://ip-api.com/json/' . $ip . '?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as', array(
            'timeout' => 5,
            'sslverify' => false
        ));
        
        if (is_wp_error($response)) {
            return $this->get_fallback_ip_info();
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['status']) && 'success' === $data['status']) {
            $info = array(
                'country' => isset($data['country']) ? $data['country'] : 'Unknown',
                'country_code' => isset($data['countryCode']) ? $data['countryCode'] : '',
                'region' => isset($data['regionName']) ? $data['regionName'] : 'Unknown',
                'city' => isset($data['city']) ? $data['city'] : 'Unknown',
                'timezone' => isset($data['timezone']) ? $data['timezone'] : wp_timezone_string(),
                'isp' => isset($data['isp']) ? $data['isp'] : 'Unknown',
                'org' => isset($data['org']) ? $data['org'] : 'Unknown',
                'as' => isset($data['as']) ? $data['as'] : 'Unknown',
                'lat' => isset($data['lat']) ? (string) $data['lat'] : '',
                'lon' => isset($data['lon']) ? (string) $data['lon'] : '',
                'postal' => isset($data['zip']) ? $data['zip'] : ''
            );
            
            set_transient($transient_key, $info, HOUR_IN_SECONDS);
            
            return $info;
        }
        
        return $this->get_fallback_ip_info();
    }
    
    private function get_fallback_ip_info() {
        return array(
            'country' => 'Unknown',
            'country_code' => '',
            'region' => 'Unknown',
            'city' => 'Unknown',
            'timezone' => wp_timezone_string(),
            'isp' => 'Unknown',
            'org' => 'Unknown',
            'as' => 'Unknown',
            'lat' => '',
            'lon' => '',
            'postal' => ''
        );
    }
    
    private function determine_nat_type($ip) {
        if ('127.0.0.1' === $ip || '::1' === $ip) {
            return array(
                'type' => 2,
                'name' => 'Type 2 - Moderate',
                'description' => 'Local network detected'
            );
        }
        
        if (strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0 || strpos($ip, '172.') === 0) {
            return array(
                'type' => 3,
                'name' => 'Type 3 - Strict',
                'description' => 'Private IP detected - Behind NAT/Firewall'
            );
        }
        
        $has_upnp = $this->check_upnp_support();
        $port_47998_open = $this->quick_port_check($ip, 47998);
        $port_3074_open = $this->quick_port_check($ip, 3074);
        
        if ($port_47998_open || $port_3074_open) {
            return array(
                'type' => 1,
                'name' => 'Type 1 - Open',
                'description' => 'Optimal gaming NAT with direct internet connection'
            );
        }
        
        if ($has_upnp) {
            return array(
                'type' => 2,
                'name' => 'Type 2 - Moderate',
                'description' => 'Good for gaming - UPnP enabled'
            );
        }
        
        return array(
            'type' => 2,
            'name' => 'Type 2 - Moderate',
            'description' => 'Standard NAT configuration suitable for most gaming'
        );
    }
    
    private function check_upnp_support() {
        return false;
    }
    
    private function quick_port_check($host, $port) {
        $connection = @fsockopen($host, $port, $errno, $errstr, 0.3);
        if ($connection) {
            fclose($connection);
            return true;
        }
        return false;
    }
    
    private function detect_connection_type() {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        if (preg_match('/mobile|android|iphone|ipad|tablet/i', $user_agent)) {
            return 'Mobile/Cellular';
        }
        
        return 'Broadband/Ethernet';
    }
}
