<?php
/**
 * Plugin Name: Cloud Gaming Speed Test
 * Plugin URI: https://github.com/yourusername/cloud-gaming-speedtest
 * Description: Advanced internet speed tester specifically designed for cloud gaming with customizable themes and comprehensive analytics
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-gaming-speedtest
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CGST_VERSION', '1.0.0');
define('CGST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CGST_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CGST_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once CGST_PLUGIN_DIR . 'includes/class-speed-test.php';
require_once CGST_PLUGIN_DIR . 'includes/class-admin.php';
require_once CGST_PLUGIN_DIR . 'includes/class-shortcode.php';
require_once CGST_PLUGIN_DIR . 'includes/ajax-handlers.php';

class Cloud_Gaming_SpeedTest {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        load_plugin_textdomain('cloud-gaming-speedtest', false, dirname(CGST_PLUGIN_BASENAME) . '/languages');
        
        CGST_Admin::get_instance();
        CGST_Shortcode::get_instance();
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'cgst-frontend-style',
            CGST_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            CGST_VERSION
        );
        
        wp_enqueue_script(
            'cgst-frontend-script',
            CGST_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            CGST_VERSION,
            true
        );
        
        wp_localize_script('cgst-frontend-script', 'cgstAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cgst_nonce'),
            'homeUrl' => home_url('/'),
            'settings' => array(
                'theme' => get_option('cgst_color_theme', 'dark')
            )
        ));
    }
    
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_cloud-gaming-speedtest' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'cgst-admin-style',
            CGST_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            CGST_VERSION
        );
        
        wp_enqueue_script(
            'cgst-admin-script',
            CGST_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-i18n'),
            CGST_VERSION,
            true
        );
        
        wp_set_script_translations('cgst-admin-script', 'cloud-gaming-speedtest', CGST_PLUGIN_DIR . 'languages');
    }
    
    public function activate() {
        if (false === get_option('cgst_color_theme', false)) {
            add_option('cgst_color_theme', 'dark');
        }
        
        if (false === get_option('cgst_custom_resources', false)) {
            add_option('cgst_custom_resources', CGST_Speed_Test::get_default_resources());
        }
        
        if (false === get_option('cgst_intro_text', false)) {
            add_option('cgst_intro_text', __('Run the full cloud gaming connectivity check to see if your network can keep up with ultra responsive streaming.', 'cloud-gaming-speedtest'));
        }
        
        if (false === get_option('cgst_footer_note', false)) {
            add_option('cgst_footer_note', __('Tip: For the most accurate results, test on a wired connection and close bandwidth-heavy applications.', 'cloud-gaming-speedtest'));
        }
        
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
}

Cloud_Gaming_SpeedTest::get_instance();
