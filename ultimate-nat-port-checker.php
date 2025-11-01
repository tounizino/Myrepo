<?php
/**
 * Plugin Name: Ultimate NAT & Port Checker
 * Plugin URI: https://cloudgamingblog.com
 * Description: Ultimate NAT and Port checking tool for cloud gamers with device information, multi-port support, and comprehensive admin settings
 * Version: 1.0.0
 * Author: Cloud Gaming Blog
 * Author URI: https://cloudgamingblog.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ultimate-nat-port-checker
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('UNPC_VERSION', '1.0.0');
define('UNPC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UNPC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('UNPC_PLUGIN_FILE', __FILE__);

// Include required files
require_once UNPC_PLUGIN_DIR . 'includes/class-admin-settings.php';
require_once UNPC_PLUGIN_DIR . 'includes/class-shortcodes.php';
require_once UNPC_PLUGIN_DIR . 'includes/ajax-handlers.php';

// Initialize the plugin
class Ultimate_NAT_Port_Checker {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Initialize admin settings
        UNPC_Admin_Settings::get_instance();
        
        // Initialize shortcodes
        UNPC_Shortcodes::get_instance();
    }
    
    public function init() {
        // Load plugin text domain
        load_plugin_textdomain('ultimate-nat-port-checker', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function enqueue_scripts() {
        // Enqueue Font Awesome
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            array(),
            '6.4.0'
        );
        
        // Enqueue main styles
        wp_enqueue_style(
            'unpc-styles',
            UNPC_PLUGIN_URL . 'assets/css/main.css',
            array(),
            UNPC_VERSION
        );
        
        // Enqueue main scripts
        wp_enqueue_script(
            'unpc-scripts',
            UNPC_PLUGIN_URL . 'assets/js/main.js',
            array('jquery'),
            UNPC_VERSION,
            true
        );
        
        $settings = $this->get_frontend_settings();
        $settings['ajaxUrl'] = admin_url('admin-ajax.php');
        $settings['nonce'] = wp_create_nonce('unpc_nonce');
        
        // Localize script with settings and AJAX URL
        wp_localize_script('unpc-scripts', 'unpcSettings', $settings);
        
        $custom_js = get_option('unpc_custom_js', '');
        if (!empty($custom_js)) {
            wp_add_inline_script('unpc-scripts', $custom_js);
        }
    }
    
    public function admin_enqueue_scripts($hook) {
        // Only load on our admin page
        if ($hook !== 'toplevel_page_ultimate-nat-port-checker') {
            return;
        }
        
        wp_enqueue_style('wp-color-picker');
        
        wp_enqueue_style(
            'unpc-admin-styles',
            UNPC_PLUGIN_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            UNPC_VERSION
        );
        
        wp_enqueue_script(
            'unpc-admin-scripts',
            UNPC_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            UNPC_VERSION,
            true
        );
    }
    
    private function get_frontend_settings() {
        $defaults = UNPC_Admin_Settings::get_defaults();
        
        $stun_servers = get_option('unpc_nat_stun_servers', $defaults['unpc_nat_stun_servers']);
        $stun_array = array_filter(array_map('trim', explode("\n", $stun_servers)));
        
        return array(
            'theme' => get_option('unpc_default_theme', $defaults['unpc_default_theme']),
            'fontSize' => get_option('unpc_font_size', $defaults['unpc_font_size']),
            'containerWidth' => get_option('unpc_container_width', $defaults['unpc_container_width']),
            'showDeviceInfo' => get_option('unpc_show_device_info', $defaults['unpc_show_device_info']),
            'showRouterLogins' => get_option('unpc_show_router_logins', $defaults['unpc_show_router_logins']),
            'showGuides' => get_option('unpc_show_guides', $defaults['unpc_show_guides']),
            'enableAnimations' => get_option('unpc_enable_animations', $defaults['unpc_enable_animations']),
            'customCSS' => get_option('unpc_custom_css', ''),
            'showSecurityNote' => get_option('unpc_show_security_note', $defaults['unpc_show_security_note']),
            'showFooter' => get_option('unpc_show_footer', $defaults['unpc_show_footer']),
            'showTechnicalLog' => get_option('unpc_show_technical_log', $defaults['unpc_show_technical_log']),
            'stunServers' => !empty($stun_array) ? $stun_array : array($defaults['unpc_nat_stun_servers']),
            'natTimeout' => get_option('unpc_nat_timeout', $defaults['unpc_nat_timeout']),
            'portTimeout' => get_option('unpc_port_timeout', $defaults['unpc_port_timeout']),
            'maxPorts' => get_option('unpc_max_ports_check', $defaults['unpc_max_ports_check'])
        );
    }
}

// Initialize the plugin
function unpc_init() {
    return Ultimate_NAT_Port_Checker::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'unpc_init');

// Activation hook
register_activation_hook(__FILE__, 'unpc_activate');
function unpc_activate() {
    // Set default options
    $defaults = UNPC_Admin_Settings::get_defaults();
    foreach ($defaults as $key => $value) {
        if (get_option($key) === false) {
            add_option($key, $value);
        }
    }
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'unpc_deactivate');
function unpc_deactivate() {
    // Clean up if needed
}
