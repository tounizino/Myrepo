<?php
/**
 * AJAX Handlers for Port Checking
 */

if (!defined('ABSPATH')) {
    exit;
}

// Port checking handler
add_action('wp_ajax_unpc_check_port', 'unpc_check_port_handler');
add_action('wp_ajax_nopriv_unpc_check_port', 'unpc_check_port_handler');

function unpc_check_port_handler() {
    check_ajax_referer('unpc_nonce', 'nonce');
    
    $ports = isset($_POST['ports']) ? sanitize_text_field($_POST['ports']) : '';
    
    if (empty($ports)) {
        wp_send_json_error(array('message' => 'No ports provided'));
    }
    
    $result = unpc_check_ports($ports);
    
    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }
}

function unpc_check_ports($ports_string) {
    $ports = array();
    $port_ranges = explode(',', $ports_string);
    
    foreach ($port_ranges as $range) {
        $range = trim($range);
        if (strpos($range, '-') !== false) {
            // Handle port range
            list($start, $end) = explode('-', $range);
            $start = intval($start);
            $end = intval($end);
            
            if ($start > 0 && $end > 0 && $start <= $end && ($end - $start) <= 100) {
                for ($i = $start; $i <= $end; $i++) {
                    $ports[] = $i;
                }
            }
        } else {
            // Single port
            $port = intval($range);
            if ($port > 0 && $port <= 65535) {
                $ports[] = $port;
            }
        }
    }
    
    if (empty($ports)) {
        return array('success' => false, 'message' => 'Invalid port format');
    }
    
    // Limit number of ports to check
    $max_ports = intval(get_option('unpc_max_ports_check', 50));
    if (count($ports) > $max_ports) {
        return array('success' => false, 'message' => "Too many ports. Maximum is {$max_ports}");
    }
    
    // Check ports
    $results = array();
    $open_count = 0;
    $closed_count = 0;
    
    foreach ($ports as $port) {
        $is_open = unpc_is_port_open($port);
        $results[$port] = $is_open;
        if ($is_open) {
            $open_count++;
        } else {
            $closed_count++;
        }
    }
    
    return array(
        'success' => true,
        'ports' => $ports_string,
        'results' => $results,
        'open_count' => $open_count,
        'closed_count' => $closed_count,
        'total' => count($ports),
        'open' => $open_count > 0
    );
}

function unpc_is_port_open($port) {
    // Note: This is a simulated check since WordPress can't actually check if ports
    // are open on the client's network. In a real implementation, you would need
    // to use an external service or WebSocket server.
    
    // For demonstration, we'll use a probabilistic approach based on common ports
    $common_open_ports = array(80, 443, 8080, 3000, 3074, 3478, 3479, 3480);
    
    if (in_array($port, $common_open_ports)) {
        return (bool) rand(0, 1); // 50% chance for common ports
    }
    
    // Less common ports have lower chance of being open
    return (bool) (rand(0, 100) < 30); // 30% chance
}

// Get device information
add_action('wp_ajax_unpc_get_device_info', 'unpc_get_device_info_handler');
add_action('wp_ajax_nopriv_unpc_get_device_info', 'unpc_get_device_info_handler');

function unpc_get_device_info_handler() {
    check_ajax_referer('unpc_nonce', 'nonce');
    
    // Get IP address
    $ip = unpc_get_client_ip();
    
    // Get User Agent
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
    
    // Parse browser and OS
    $browser = unpc_get_browser($user_agent);
    $os = unpc_get_os($user_agent);
    
    // Get connection type (if available)
    $connection_type = isset($_SERVER['HTTP_CONNECTION']) ? $_SERVER['HTTP_CONNECTION'] : 'Unknown';
    
    // Get IP info (using ipapi.co - free tier)
    $ip_info = unpc_get_ip_info($ip);
    
    wp_send_json_success(array(
        'ip' => $ip,
        'country' => $ip_info['country'] ?? 'Unknown',
        'city' => $ip_info['city'] ?? 'Unknown',
        'isp' => $ip_info['org'] ?? 'Unknown',
        'browser' => $browser,
        'os' => $os,
        'connection' => $connection_type,
        'user_agent' => $user_agent
    ));
}

function unpc_get_client_ip() {
    $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return 'Unknown';
}

function unpc_get_browser($user_agent) {
    $browsers = array(
        '/edge/i' => 'Edge',
        '/chrome/i' => 'Chrome',
        '/safari/i' => 'Safari',
        '/firefox/i' => 'Firefox',
        '/opera/i' => 'Opera',
        '/msie/i' => 'Internet Explorer',
    );
    
    foreach ($browsers as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            return $value;
        }
    }
    
    return 'Unknown';
}

function unpc_get_os($user_agent) {
    $os_array = array(
        '/windows nt 10/i' => 'Windows 10/11',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/macintosh|mac os x/i' => 'Mac OS',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
    );
    
    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            return $value;
        }
    }
    
    return 'Unknown';
}

function unpc_get_ip_info($ip) {
    // Check if caching is enabled
    $enable_cache = get_option('unpc_enable_cache', '0');
    $cache_key = 'unpc_ip_info_' . md5($ip);
    
    if ($enable_cache === '1') {
        $cached = get_transient($cache_key);
        if ($cached !== false) {
            return $cached;
        }
    }
    
    // Try to get IP info from ipapi.co (free, no API key required)
    $response = wp_remote_get("https://ipapi.co/{$ip}/json/", array('timeout' => 5));
    
    if (is_wp_error($response)) {
        return array();
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if ($enable_cache === '1') {
        $cache_duration = intval(get_option('unpc_cache_duration', 300));
        set_transient($cache_key, $data, $cache_duration);
    }
    
    return $data;
}
