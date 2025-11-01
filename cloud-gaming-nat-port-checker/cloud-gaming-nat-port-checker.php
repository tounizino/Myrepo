<?php
/**
 * Plugin Name: Cloud Gaming NAT & Port Checker
 * Plugin URI: https://your-cloud-gaming-blog.com
 * Description: A premium NAT & Port Checker Tool for cloud gaming platforms with real-time network diagnostics, device detection, and router configuration tips.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://your-cloud-gaming-blog.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-nat-port-checker
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CGNPC_VERSION', '1.0.0');
define('CGNPC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CGNPC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CGNPC_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once CGNPC_PLUGIN_DIR . 'includes/class-admin-settings.php';
require_once CGNPC_PLUGIN_DIR . 'includes/class-nat-checker.php';
require_once CGNPC_PLUGIN_DIR . 'includes/class-port-checker.php';
require_once CGNPC_PLUGIN_DIR . 'includes/class-device-detector.php';
require_once CGNPC_PLUGIN_DIR . 'includes/class-shortcodes.php';

/**
 * Main Plugin Class
 */
class Cloud_Gaming_NAT_Port_Checker {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
        $this->init_components();
    }
    
    private function init_hooks() {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'register_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Ajax handlers
        add_action('wp_ajax_cgnpc_check_nat', array($this, 'ajax_check_nat'));
        add_action('wp_ajax_nopriv_cgnpc_check_nat', array($this, 'ajax_check_nat'));
        
        add_action('wp_ajax_cgnpc_check_port', array($this, 'ajax_check_port'));
        add_action('wp_ajax_nopriv_cgnpc_check_port', array($this, 'ajax_check_port'));
        
        add_action('wp_ajax_cgnpc_get_device_info', array($this, 'ajax_get_device_info'));
        add_action('wp_ajax_nopriv_cgnpc_get_device_info', array($this, 'ajax_get_device_info'));
        
        add_action('init', array($this, 'load_textdomain'));
        
        // Add settings link
        add_filter('plugin_action_links_' . CGNPC_PLUGIN_BASENAME, array($this, 'add_settings_link'));
    }
    
    public function init_components() {
        if (is_admin()) {
            CGNPC_Admin_Settings::get_instance();
        }
        
        CGNPC_Shortcodes::get_instance();
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('cloud-nat-port-checker', false, dirname(CGNPC_PLUGIN_BASENAME) . '/languages');
    }
    
    public function register_frontend_assets() {
        wp_register_style(
            'cgnpc-frontend',
            CGNPC_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            CGNPC_VERSION
        );
        
        wp_register_script(
            'cgnpc-frontend',
            CGNPC_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            CGNPC_VERSION,
            true
        );
    }
    
    public function enqueue_admin_assets($hook) {
        if ('settings_page_cgnpc-settings' !== $hook) {
            return;
        }
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_media();
        
        wp_enqueue_style(
            'cgnpc-admin',
            CGNPC_PLUGIN_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            CGNPC_VERSION
        );
        
        wp_enqueue_script(
            'cgnpc-admin',
            CGNPC_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            CGNPC_VERSION,
            true
        );
    }
    
    public function ajax_check_nat() {
        check_ajax_referer('cgnpc_nonce', 'nonce');
        
        $nat_checker = new CGNPC_NAT_Checker();
        $result = $nat_checker->check_nat();
        
        wp_send_json_success($result);
    }
    
    public function ajax_check_port() {
        check_ajax_referer('cgnpc_nonce', 'nonce');
        
        $port_checker = new CGNPC_Port_Checker();
        $host = isset($_POST['host']) ? sanitize_text_field(wp_unslash($_POST['host'])) : null;
        $attempts = isset($_POST['attempts']) ? intval(wp_unslash($_POST['attempts'])) : 3;
        
        $raw_ports = isset($_POST['ports']) ? wp_unslash($_POST['ports']) : null;
        if (is_array($raw_ports)) {
            $ports = array();
            foreach ($raw_ports as $entry) {
                if (!is_array($entry) || !isset($entry['port'])) {
                    continue;
                }
                $ports[] = array(
                    'port' => intval(wp_unslash($entry['port'])),
                    'protocol' => isset($entry['protocol']) ? sanitize_text_field(wp_unslash($entry['protocol'])) : 'tcp',
                    'label' => isset($entry['label']) ? sanitize_text_field(wp_unslash($entry['label'])) : ''
                );
            }
            if (empty($ports)) {
                wp_send_json_error(array('message' => __('No ports provided', 'cloud-nat-port-checker')));
            }
            $results = $port_checker->check_multiple_ports($ports, $host, $attempts);
            wp_send_json_success($results);
        }
        
        $port = isset($_POST['port']) ? intval(wp_unslash($_POST['port'])) : 0;
        $protocol = isset($_POST['protocol']) ? sanitize_text_field(wp_unslash($_POST['protocol'])) : 'tcp';
        
        if ($port < 1 || $port > 65535) {
            wp_send_json_error(array('message' => __('Invalid port number', 'cloud-nat-port-checker')));
        }
        
        $result = $port_checker->check_port($port, $protocol, $host, $attempts);
        
        wp_send_json_success($result);
    }
    
    public function ajax_get_device_info() {
        check_ajax_referer('cgnpc_nonce', 'nonce');
        
        $device_detector = new CGNPC_Device_Detector();
        $result = $device_detector->get_device_info();
        
        wp_send_json_success($result);
    }
    
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=cgnpc-settings') . '">' . __('Settings', 'cloud-nat-port-checker') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}

// Initialize the plugin
function cgnpc_init() {
    return Cloud_Gaming_NAT_Port_Checker::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'cgnpc_init');

// Activation hook
register_activation_hook(__FILE__, 'cgnpc_activate');
function cgnpc_activate() {
    $existing = get_option('cgnpc_settings', false);
    if (false === $existing) {
        $defaults = CGNPC_Admin_Settings::get_default_settings();
        add_option('cgnpc_settings', $defaults);
    }
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'cgnpc_deactivate');
function cgnpc_deactivate() {
    // Cleanup if needed
}
