<?php
/**
 * Plugin Name: Cloud Gaming Readiness Test
 * Plugin URI: https://cloudegaming.io
 * Description: Professional-grade cloud gaming network diagnostic tool that analyzes latency, jitter, packet loss, and connection stability to determine cloud gaming readiness.
 * Version: 1.0.0
 * Author: Cloud Gaming Performance
 * License: GPL v2 or later
 * Text Domain: cloud-gaming-test
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CGRT_VERSION', '1.0.0');
define('CGRT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CGRT_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
class CloudGamingReadinessTest {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Initialize plugin
        add_action('plugins_loaded', array($this, 'init'));
        
        // Register activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Load text domain
        load_plugin_textdomain('cloud-gaming-test', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        
        // Register shortcode
        add_shortcode('cloud_gaming_test', array($this, 'render_shortcode'));
        
        // Add custom block for Gutenberg
        add_action('init', array($this, 'register_block'));
    }
    
    public function activate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        $default_options = array(
            'test_duration' => 30,
            'sample_rate' => 1000,
            'enable_advanced_mode' => true,
            'test_endpoints' => array(
                'https://cloudflare.com/cdn-cgi/trace',
                'https://www.google.com/generate_204',
                'https://www.cloudflare.com/cdn-cgi/trace',
                'https://1.1.1.1/cdn-cgi/trace'
            )
        );
        
        add_option('cgrt_settings', $default_options);
    }
    
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    public function enqueue_assets() {
        // Enqueue styles
        wp_enqueue_style(
            'cgrt-styles',
            CGRT_PLUGIN_URL . 'assets/css/style.css',
            array(),
            CGRT_VERSION
        );
        
        // Enqueue main script
        wp_enqueue_script(
            'cgrt-main',
            CGRT_PLUGIN_URL . 'assets/js/main.js',
            array(),
            CGRT_VERSION,
            true
        );
        
        // Localize script with settings
        wp_localize_script('cgrt-main', 'cgrtSettings', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cgrt_nonce'),
            'testDuration' => get_option('cgrt_settings')['test_duration'] ?? 30,
            'sampleRate' => get_option('cgrt_settings')['sample_rate'] ?? 1000,
            'testEndpoints' => get_option('cgrt_settings')['test_endpoints'] ?? array(),
            'strings' => array(
                'startingTest' => __('Initializing test...', 'cloud-gaming-test'),
                'measuringLatency' => __('Measuring latency...', 'cloud-gaming-test'),
                'analyzingJitter' => __('Analyzing jitter...', 'cloud-gaming-test'),
                'checkingStability' => __('Checking connection stability...', 'cloud-gaming-test'),
                'calculatingScore' => __('Calculating readiness score...', 'cloud-gaming-test'),
            )
        ));
    }
    
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'width' => '100%',
            'height' => 'auto',
            'theme' => 'light',
            'advanced' => 'true'
        ), $atts, 'cloud_gaming_test');
        
        ob_start();
        include CGRT_PLUGIN_DIR . 'templates/test-widget.php';
        return ob_get_clean();
    }
    
    public function register_block() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        register_block_type('cloud-gaming-test/widget', array(
            'editor_script' => 'cgrt-block-editor',
            'editor_style' => 'cgrt-block-editor-styles',
            'style' => 'cgrt-styles',
            'render_callback' => array($this, 'render_shortcode'),
            'attributes' => array(
                'width' => array(
                    'type' => 'string',
                    'default' => '100%'
                ),
                'height' => array(
                    'type' => 'string',
                    'default' => 'auto'
                ),
                'theme' => array(
                    'type' => 'string',
                    'default' => 'light'
                ),
                'advanced' => array(
                    'type' => 'boolean',
                    'default' => true
                )
            )
        ));
    }
}

// Initialize the plugin
function cgrt_init() {
    return CloudGamingReadinessTest::get_instance();
}

// Bootstrap the plugin
cgrt_init();
