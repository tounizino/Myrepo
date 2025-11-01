<?php
if (!defined('ABSPATH')) {
    exit;
}

class CGNPC_Device_Detector {
    
    public function get_device_info() {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : __('Unknown', 'cloud-nat-port-checker');
        $info = array(
            'ip' => $ip,
            'user_agent' => $user_agent,
            'browser' => $this->detect_browser($user_agent),
            'os' => $this->detect_os($user_agent),
            'device' => $this->detect_device($user_agent),
            'is_mobile' => wp_is_mobile(),
            'server_time' => current_time('mysql'),
            'accept_language' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? sanitize_text_field($_SERVER['HTTP_ACCEPT_LANGUAGE']) : '',
            'connection_hint' => $this->get_connection_hint(),
        );
        return $info;
    }
    
    private function detect_browser($agent) {
        if (empty($agent)) {
            return __('Unknown Browser', 'cloud-nat-port-checker');
        }
        $browsers = array(
            'Edge' => 'Edge',
            'OPR' => 'Opera',
            'Opera' => 'Opera',
            'Chrome' => 'Chrome',
            'CriOS' => 'Chrome (iOS)',
            'Firefox' => 'Firefox',
            'Safari' => 'Safari',
            'MSIE' => 'Internet Explorer',
            'Trident/7' => 'Internet Explorer 11',
        );
        foreach ($browsers as $needle => $label) {
            if (stripos($agent, $needle) !== false) {
                return $label;
            }
        }
        return __('Unknown Browser', 'cloud-nat-port-checker');
    }
    
    private function detect_os($agent) {
        if (empty($agent)) {
            return __('Unknown OS', 'cloud-nat-port-checker');
        }
        $oses = array(
            'Windows 11' => array('Windows NT 10.0', 'Win64'),
            'Windows 10' => array('Windows NT 10.0'),
            'Windows 8.1' => array('Windows NT 6.3'),
            'Windows 8' => array('Windows NT 6.2'),
            'Windows 7' => array('Windows NT 6.1'),
            'macOS' => array('Macintosh', 'Mac OS X'),
            'iOS' => array('iPhone', 'iPad'),
            'Android' => array('Android'),
            'Linux' => array('Linux'),
            'Chrome OS' => array('CrOS'),
        );
        foreach ($oses as $label => $needles) {
            foreach ($needles as $needle) {
                if (stripos($agent, $needle) !== false) {
                    return $label;
                }
            }
        }
        return __('Unknown OS', 'cloud-nat-port-checker');
    }
    
    private function detect_device($agent) {
        if (empty($agent)) {
            return __('Unknown Device', 'cloud-nat-port-checker');
        }
        if (stripos($agent, 'tablet') !== false || stripos($agent, 'iPad') !== false) {
            return __('Tablet', 'cloud-nat-port-checker');
        }
        if (stripos($agent, 'mobile') !== false || stripos($agent, 'iphone') !== false || stripos($agent, 'android') !== false) {
            return __('Smartphone', 'cloud-nat-port-checker');
        }
        if (stripos($agent, 'smart-tv') !== false || stripos($agent, 'hbbtv') !== false) {
            return __('Smart TV', 'cloud-nat-port-checker');
        }
        return __('Desktop / Laptop', 'cloud-nat-port-checker');
    }
    
    private function get_connection_hint() {
        $https = is_ssl();
        $http2 = isset($_SERVER['SERVER_PROTOCOL']) ? sanitize_text_field($_SERVER['SERVER_PROTOCOL']) : '';
        $hint = array();
        $hint[] = $https ? __('Secure HTTPS connection detected.', 'cloud-nat-port-checker') : __('HTTP connection detected. HTTPS recommended.', 'cloud-nat-port-checker');
        if (stripos($http2, 'HTTP/2') !== false) {
            $hint[] = __('HTTP/2 enabled for faster multiplexed streams.', 'cloud-nat-port-checker');
        }
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $hint[] = __('Traffic is routed through Cloudflare or CDN.', 'cloud-nat-port-checker');
        }
        return $hint;
    }
}
