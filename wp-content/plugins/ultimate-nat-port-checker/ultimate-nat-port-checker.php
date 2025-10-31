<?php
/**
 * Plugin Name: Ultimate NAT & Port Checker
 * Plugin URI: https://yourdomain.com
 * Description: Advanced NAT Type and Port Checker tool for cloud gaming with comprehensive device information, router guides, and customizable admin panel
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourdomain.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ultimate-nat-port-checker
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('UNPC_VERSION', '1.0.0');
define('UNPC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UNPC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('UNPC_PLUGIN_FILE', __FILE__);

// Include required files
require_once UNPC_PLUGIN_DIR . 'includes/class-unpc-api.php';
require_once UNPC_PLUGIN_DIR . 'includes/class-unpc-admin.php';
require_once UNPC_PLUGIN_DIR . 'includes/class-unpc-frontend.php';
require_once UNPC_PLUGIN_DIR . 'includes/class-unpc-ajax.php';

/**
 * Main plugin class
 */
class Ultimate_NAT_Port_Checker {
    
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Initialize plugin
        add_action('plugins_loaded', array($this, 'init'));
        
        // Register activation hook
        register_activation_hook(__FILE__, array($this, 'activate'));
        
        // Register deactivation hook
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize classes
        UNPC_Admin::get_instance();
        UNPC_Frontend::get_instance();
        UNPC_Ajax::get_instance();
        UNPC_API::get_instance();
        
        // Load text domain
        load_plugin_textdomain('ultimate-nat-port-checker', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        $default_options = array(
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
            'guides' => json_encode(array(
                array(
                    'title' => 'How to Open NAT Type for Gaming',
                    'url' => '#',
                    'description' => 'Complete guide to achieve Open NAT for optimal gaming experience'
                ),
                array(
                    'title' => 'Port Forwarding Guide',
                    'url' => '#',
                    'description' => 'Step-by-step instructions for port forwarding on popular routers'
                ),
                array(
                    'title' => 'QoS Setup for Cloud Gaming',
                    'url' => '#',
                    'description' => 'Optimize your router QoS settings for the best cloud gaming performance'
                )
            ))
        );
        
        add_option('unpc_settings', $default_options);
        
        // Create custom table for port presets if needed
        global $wpdb;
        $table_name = $wpdb->prefix . 'unpc_port_presets';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            platform_name varchar(100) NOT NULL,
            ports text NOT NULL,
            protocol varchar(10) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Insert default port presets
        $default_presets = array(
            array('platform_name' => 'PlayStation Network', 'ports' => '80,443,3478,3479,3480', 'protocol' => 'TCP/UDP'),
            array('platform_name' => 'Xbox Live', 'ports' => '53,80,88,500,3074,3544,4500', 'protocol' => 'TCP/UDP'),
            array('platform_name' => 'Steam', 'ports' => '27015-27030,27036-27037', 'protocol' => 'TCP/UDP'),
            array('platform_name' => 'GeForce NOW', 'ports' => '47998-48000', 'protocol' => 'TCP/UDP'),
            array('platform_name' => 'Google Stadia', 'ports' => '44700-44899', 'protocol' => 'UDP'),
            array('platform_name' => 'Xbox Cloud Gaming', 'ports' => '3074', 'protocol' => 'TCP/UDP'),
            array('platform_name' => 'Amazon Luna', 'ports' => '443', 'protocol' => 'TCP')
        );
        
        $existing_presets = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if (0 === $existing_presets) {
            foreach ($default_presets as $preset) {
                $wpdb->insert($table_name, $preset);
            }
        }
        
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize plugin
function unpc_init() {
    return Ultimate_NAT_Port_Checker::get_instance();
}

// Start the plugin
unpc_init();
