<?php
/**
 * Plugin Name: Cloud Loadout Latency Tester
 * Plugin URI: https://cloudloadout.com/cloud-loadout-latency-tester
 * Description: The ultimate latency testing tool for cloud gamers. Test your ping to popular cloud gaming platforms including Xbox Cloud, Amazon Luna, Shadow, Boosteroid, PlayStation Cloud and more.
 * Version: 1.0.0
 * Author: Cloud Loadout Team
 * Author URI: https://cloudloadout.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-loadout-latency-tester
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CLOUDLOADOUT_LATENCY_VERSION', '1.0.0');
define('CLOUDLOADOUT_LATENCY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CLOUDLOADOUT_LATENCY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CLOUDLOADOUT_LATENCY_PLUGIN_FILE', __FILE__);

/**
 * Main Cloud Loadout Latency Tester Class
 */
class CloudLoadoutLatencyTester {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_shortcode('cloudloadout_latency_test', array($this, 'latency_test_shortcode'));
        add_action('wp_ajax_cloudloadout_run_test', array($this, 'ajax_run_test'));
        add_action('wp_ajax_nopriv_cloudloadout_run_test', array($this, 'ajax_run_test'));
        add_action('wp_ajax_cloudloadout_get_results', array($this, 'ajax_get_results'));
        add_action('wp_ajax_nopriv_cloudloadout_get_results', array($this, 'ajax_get_results'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        load_plugin_textdomain('cloud-loadout-latency-tester', false, dirname(plugin_basename(__FILE__)) . '/languages');
        $this->create_database_tables();
    }
    
    public function activate() {
        $this->create_database_tables();
        $this->set_default_options();
    }
    
    public function deactivate() {
        // Clean up if needed
    }
    
    private function create_database_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'cloudloadout_latency_tests';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            test_date datetime DEFAULT CURRENT_TIMESTAMP,
            user_ip varchar(45),
            user_agent text,
            results longtext,
            average_latency decimal(8,2),
            platform varchar(50),
            server_location varchar(50),
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    private function set_default_options() {
        $default_options = array(
            'test_timeout' => 5000,
            'test_count' => 5,
            'enable_detailed_logs' => true,
            'admin_theme' => 'light',
            'show_results_chart' => true,
            'enable_analytics' => true,
            'supported_platforms' => array(
                'xbox_cloud' => array('name' => 'Xbox Cloud Gaming', 'enabled' => true),
                'amazon_luna' => array('name' => 'Amazon Luna', 'enabled' => true),
                'shadow' => array('name' => 'Shadow', 'enabled' => true),
                'boosteroid' => array('name' => 'Boosteroid', 'enabled' => true),
                'playstation_cloud' => array('name' => 'PlayStation Cloud', 'enabled' => true),
                'nvidia_geforce' => array('name' => 'NVIDIA GeForce NOW', 'enabled' => true),
                'microsoft_cloud' => array('name' => 'Microsoft Cloud PC', 'enabled' => true)
            )
        );
        
        add_option('cloudloadout_latency_options', $default_options);
    }
    
    public function enqueue_frontend_scripts() {
        wp_enqueue_script(
            'cloudloadout-latency-tester-frontend',
            CLOUDLOADOUT_LATENCY_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            CLOUDLOADOUT_LATENCY_VERSION,
            true
        );
        
        wp_enqueue_style(
            'cloudloadout-latency-tester-frontend',
            CLOUDLOADOUT_LATENCY_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            CLOUDLOADOUT_LATENCY_VERSION
        );
        
        wp_localize_script('cloudloadout-latency-tester-frontend', 'cloudloadout_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cloudloadout_latency_nonce'),
            'strings' => array(
                'testing' => __('Testing...', 'cloud-loadout-latency-tester'),
                'completed' => __('Test Completed', 'cloud-loadout-latency-tester'),
                'error' => __('Test Failed', 'cloud-loadout-latency-tester'),
                'excellent' => __('Excellent', 'cloud-loadout-latency-tester'),
                'good' => __('Good', 'cloud-loadout-latency-tester'),
                'fair' => __('Fair', 'cloud-loadout-latency-tester'),
                'poor' => __('Poor', 'cloud-loadout-latency-tester')
            )
        ));
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'cloudloadout') === false) return;
        
        wp_enqueue_script(
            'cloudloadout-latency-tester-admin',
            CLOUDLOADOUT_LATENCY_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            CLOUDLOADOUT_LATENCY_VERSION,
            true
        );
        
        wp_enqueue_style(
            'cloudloadout-latency-tester-admin',
            CLOUDLOADOUT_LATENCY_PLUGIN_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            CLOUDLOADOUT_LATENCY_VERSION
        );
        
        wp_localize_script('cloudloadout-latency-tester-admin', 'cloudloadout_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cloudloadout_admin_nonce'),
            'strings' => array(
                'confirm_reset' => __('Are you sure you want to reset all settings?', 'cloud-loadout-latency-tester'),
                'saved' => __('Settings saved successfully!', 'cloud-loadout-latency-tester')
            )
        ));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('Cloud Loadout Latency Tester', 'cloud-loadout-latency-tester'),
            __('Latency Tester', 'cloud-loadout-latency-tester'),
            'manage_options',
            'cloudloadout-latency-tester',
            array($this, 'admin_page'),
            'dashicons-chart-line',
            30
        );
        
        add_submenu_page(
            'cloudloadout-latency-tester',
            __('Settings', 'cloud-loadout-latency-tester'),
            __('Settings', 'cloud-loadout-latency-tester'),
            'manage_options',
            'cloudloadout-latency-tester-settings',
            array($this, 'admin_settings_page')
        );
        
        add_submenu_page(
            'cloudloadout-latency-tester',
            __('Results', 'cloud-loadout-latency-tester'),
            __('Results', 'cloud-loadout-latency-tester'),
            'manage_options',
            'cloudloadout-latency-tester-results',
            array($this, 'admin_results_page')
        );
    }
    
    public function admin_page() {
        include CLOUDLOADOUT_LATENCY_PLUGIN_DIR . 'admin/dashboard.php';
    }
    
    public function admin_settings_page() {
        include CLOUDLOADOUT_LATENCY_PLUGIN_DIR . 'admin/settings.php';
    }
    
    public function admin_results_page() {
        include CLOUDLOADOUT_LATENCY_PLUGIN_DIR . 'admin/results.php';
    }
    
    public function latency_test_shortcode($atts) {
        $atts = shortcode_atts(array(
            'platform' => 'all',
            'theme' => 'light'
        ), $atts);
        
        ob_start();
        include CLOUDLOADOUT_LATENCY_PLUGIN_DIR . 'templates/shortcode.php';
        return ob_get_clean();
    }
    
    public function ajax_run_test() {
        check_ajax_referer('cloudloadout_latency_nonce', 'nonce');
        
        $platform = sanitize_text_field($_POST['platform']);
        $server_url = sanitize_text_field($_POST['server_url']);
        $test_count = intval($_POST['test_count']);
        $timeout = intval($_POST['timeout']);
        
        $results = $this->perform_latency_test($server_url, $test_count, $timeout);
        
        // Store results in database
        $this->store_test_results($platform, $results);
        
        wp_send_json_success($results);
    }
    
    private function perform_latency_test($server_url, $test_count = 5, $timeout = 5000) {
        $results = array();
        
        for ($i = 0; $i < $test_count; $i++) {
            $start_time = microtime(true);
            
            // Create a ping request using various methods
            $response = wp_remote_get($server_url, array(
                'timeout' => $timeout / 1000,
                'headers' => array(
                    'User-Agent' => 'CloudLoadout Latency Tester 1.0'
                )
            ));
            
            $end_time = microtime(true);
            $latency = ($end_time - $start_time) * 1000; // Convert to milliseconds
            
            if (is_wp_error($response)) {
                $results[] = array(
                    'success' => false,
                    'latency' => -1,
                    'error' => $response->get_error_message()
                );
            } else {
                $results[] = array(
                    'success' => true,
                    'latency' => round($latency, 2),
                    'status_code' => wp_remote_retrieve_response_code($response)
                );
            }
            
            // Small delay between tests
            usleep(100000); // 100ms
        }
        
        return $results;
    }
    
    private function store_test_results($platform, $results) {
        global $wpdb;
        
        $latencies = array();
        foreach ($results as $result) {
            if ($result['success']) {
                $latencies[] = $result['latency'];
            }
        }
        
        $average_latency = !empty($latencies) ? array_sum($latencies) / count($latencies) : 0;
        
        $table_name = $wpdb->prefix . 'cloudloadout_latency_tests';
        
        $wpdb->insert(
            $table_name,
            array(
                'test_date' => current_time('mysql'),
                'user_ip' => $this->get_user_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'results' => json_encode($results),
                'average_latency' => $average_latency,
                'platform' => $platform,
                'server_location' => $this->get_server_location()
            ),
            array('%s', '%s', '%s', '%s', '%f', '%s', '%s')
        );
    }
    
    private function get_user_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
    
    private function get_server_location() {
        // Simple location detection based on IP or hosting info
        return 'Unknown'; // Could be enhanced with IP geolocation APIs
    }
    
    public function ajax_get_results() {
        check_ajax_referer('cloudloadout_latency_nonce', 'nonce');
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'cloudloadout_latency_tests';
        
        $results = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY test_date DESC LIMIT 50",
            ARRAY_A
        );
        
        wp_send_json_success($results);
    }
    
    public function get_default_servers($platform_key) {
        $default_servers = array(
            'xbox_cloud' => "https://test.xbox.com\nhttps://xbox.com\nhttps://www.xbox.com/en-US/xbox-game-pass/cloud-gaming",
            'amazon_luna' => "https://luna.amazon.com\nhttps://www.amazon.com/luna",
            'shadow' => "https://shadow.tech\nhttps://www.shadow.tech",
            'boosteroid' => "https://boosteroid.com\nhttps://www.boosteroid.com",
            'playstation_cloud' => "https://playstation.com\nhttps://store.playstation.com",
            'nvidia_geforce' => "https://play.geforcenow.com\nhttps://www.nvidia.com/en-us/geforce-now",
            'microsoft_cloud' => "https://windows.microsoft.com\nhttps://docs.microsoft.com/en-us/azure/windows-365"
        );
        
        return $default_servers[$platform_key] ?? '';
    }
}

// Initialize the plugin
new CloudLoadoutLatencyTester();

// Include additional functionality
require_once CLOUDLOADOUT_LATENCY_PLUGIN_DIR . 'includes/widget.php';
require_once CLOUDLOADOUT_LATENCY_PLUGIN_DIR . 'data/platforms.php';