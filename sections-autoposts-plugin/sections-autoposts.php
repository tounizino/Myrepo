<?php
/**
 * Plugin Name: Sections AutoPosts
 * Plugin URI: https://cloudloadout.com
 * Description: Advanced post layout sections with automatic post population, dark/light themes, and full customization - similar to PostX
 * Version: 1.0.0
 * Author: CloudLoadout
 * Author URI: https://cloudloadout.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sections-autoposts
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SAP_VERSION', '1.0.0');
define('SAP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SAP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SAP_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class Sections_AutoPosts {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Get instance
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
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        require_once SAP_PLUGIN_DIR . 'includes/class-sap-settings.php';
        require_once SAP_PLUGIN_DIR . 'includes/class-sap-utils.php';
        require_once SAP_PLUGIN_DIR . 'includes/class-sap-post-query.php';
        require_once SAP_PLUGIN_DIR . 'includes/class-sap-shortcodes.php';
        require_once SAP_PLUGIN_DIR . 'includes/class-sap-templates.php';
        
        if (is_admin()) {
            require_once SAP_PLUGIN_DIR . 'admin/class-sap-admin.php';
        }
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain('sections-autoposts', false, dirname(SAP_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'sap-frontend-styles',
            SAP_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            SAP_VERSION
        );
        
        wp_enqueue_script(
            'sap-frontend-scripts',
            SAP_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            SAP_VERSION,
            true
        );
        
        // Pass data to JavaScript
        wp_localize_script('sap-frontend-scripts', 'sapData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sap_nonce')
        ));
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_sections-autoposts' !== $hook && strpos($hook, 'sections-autoposts') === false) {
            return;
        }
        
        wp_enqueue_style('wp-color-picker');
        
        wp_enqueue_style(
            'sap-admin-styles',
            SAP_PLUGIN_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            SAP_VERSION
        );
        
        wp_enqueue_script(
            'sap-admin-scripts',
            SAP_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            SAP_VERSION,
            true
        );
    }
}

/**
 * Initialize the plugin
 */
function sap_init() {
    return Sections_AutoPosts::get_instance();
}

// Start the plugin
sap_init();
