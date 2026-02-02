<?php
/**
 * Plugin Name: Cloud Gaming Readiness Test
 * Plugin URI: https://example.com/cloud-gaming-test
 * Description: A professional-grade diagnostic tool to assess cloud gaming performance.
 * Version: 1.0.0
 * Author: Cloud Gaming Performance Brand
 * Author URI: https://example.com
 * Text Domain: cloud-gaming-test
 * License: GPL2
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Plugin Class
 */
final class CGRT_Cloud_Gaming_Test {

    /**
     * Instance of this class.
     */
    private static $instance = null;

    /**
     * Get the instance of this class.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define constants.
     */
    private function define_constants() {
        define( 'CGRT_VERSION', '1.0.0' );
        define( 'CGRT_PATH', plugin_dir_path( __FILE__ ) );
        define( 'CGRT_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include required files.
     */
    private function includes() {
        require_once CGRT_PATH . 'includes/settings.php';
        require_once CGRT_PATH . 'includes/platforms.php';
        require_once CGRT_PATH . 'includes/admin.php';
        require_once CGRT_PATH . 'includes/ajax-handlers.php';
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'register_shortcodes' ) );
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
    }

    /**
     * Register shortcodes.
     */
    public function register_shortcodes() {
        add_shortcode( 'cloud_gaming_test', 'cgrt_render_test_shortcode' );
    }

    /**
     * Activation logic.
     */
    public function activate() {
        // Ensure functions are available.
        require_once CGRT_PATH . 'includes/settings.php';
        require_once CGRT_PATH . 'includes/platforms.php';

        if ( function_exists( 'cgrt_get_default_settings' ) ) {
            add_option( 'cgrt_settings', cgrt_get_default_settings() );
        }
        if ( function_exists( 'cgrt_get_default_platforms' ) ) {
            add_option( 'cgrt_platforms', cgrt_get_default_platforms() );
        }
    }
}

// Start the plugin.
CGRT_Cloud_Gaming_Test::get_instance();

/**
 * Render the test UI
 */
function cgrt_render_test_shortcode( $atts ) {
    // Enqueue assets
    wp_enqueue_style( 'cgrt-frontend-css', CGRT_URL . 'assets/css/frontend.css', array(), CGRT_VERSION );
    wp_enqueue_script( 'cgrt-frontend-js', CGRT_URL . 'assets/js/frontend.js', array( 'jquery' ), CGRT_VERSION, true );

    // Localize script with settings and platform data
    if ( ! function_exists( 'cgrt_get_default_settings' ) ) {
        return '';
    }

    $settings = get_option( 'cgrt_settings', cgrt_get_default_settings() );
    $platforms = get_option( 'cgrt_platforms', cgrt_get_default_platforms() );

    wp_localize_script( 'cgrt-frontend-js', 'cgrt_data', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'settings' => $settings,
        'platforms' => $platforms,
        'nonce'    => wp_create_nonce( 'cgrt_nonce' )
    ) );

    ob_start();
    if ( file_exists( CGRT_PATH . 'templates/test-ui.php' ) ) {
        include CGRT_PATH . 'templates/test-ui.php';
    }
    return ob_get_clean();
}
