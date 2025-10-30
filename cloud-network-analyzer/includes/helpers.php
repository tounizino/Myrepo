<?php

if (!defined('ABSPATH')) {
    exit;
}

function cna_sanitize_checkbox($value) {
    return !empty($value) ? 1 : 0;
}

function cna_parse_router_links($input) {
    if (empty($input)) {
        return array();
    }
    
    $lines = array_filter(array_map('trim', explode("\n", $input)));
    $links = array();
    
    foreach ($lines as $line) {
        $parts = array_map('trim', explode('|', $line));
        if (count($parts) >= 4) {
            $links[] = array(
                'brand' => sanitize_text_field($parts[0]),
                'ip' => sanitize_text_field($parts[1]),
                'username' => sanitize_text_field($parts[2]),
                'password' => sanitize_text_field($parts[3])
            );
        }
    }
    
    return $links;
}

function cna_parse_ports_input($ports_input) {
    $ports_input = trim($ports_input);
    if (empty($ports_input)) {
        return array();
    }
    
    $parts = preg_split('/[,\s]+/', $ports_input);
    $ports = array();
    
    foreach ($parts as $part) {
        if (strpos($part, '-') !== false) {
            list($start, $end) = array_map('intval', explode('-', $part));
            if ($start > 0 && $end > $start && $end - $start <= 1000) {
                for ($i = $start; $i <= $end; $i++) {
                    if ($i > 0 && $i <= 65535) {
                        $ports[] = $i;
                    }
                }
            }
        } else {
            $port = intval($part);
            if ($port > 0 && $port <= 65535) {
                $ports[] = $port;
            }
        }
    }
    
    return array_values(array_unique($ports));
}

function cna_get_default_router_links() {
    $defaults = array(
        array('brand' => 'Netgear', 'ip' => '192.168.1.1', 'username' => 'admin', 'password' => 'password'),
        array('brand' => 'TP-Link', 'ip' => '192.168.0.1', 'username' => 'admin', 'password' => 'admin'),
        array('brand' => 'Linksys', 'ip' => '192.168.1.1', 'username' => 'admin', 'password' => 'admin'),
        array('brand' => 'Asus', 'ip' => '192.168.1.1', 'username' => 'admin', 'password' => 'admin'),
        array('brand' => 'D-Link', 'ip' => '192.168.0.1', 'username' => 'admin', 'password' => 'blank'),
        array('brand' => 'Belkin', 'ip' => '192.168.2.1', 'username' => 'blank', 'password' => 'blank'),
    );
    
    return $defaults;
}
