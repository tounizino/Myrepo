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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'CGRT_VERSION', '1.0.0' );
define( 'CGRT_PATH', plugin_dir_path( __FILE__ ) );
define( 'CGRT_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files
require_once CGRT_PATH . 'includes/settings.php';
require_once CGRT_PATH . 'includes/platforms.php';
require_once CGRT_PATH . 'includes/admin.php';
require_once CGRT_PATH . 'includes/ajax-handlers.php';

/**
 * Initialize the plugin
 */
function cgrt_init() {
	// Register shortcode
	add_shortcode( 'cloud_gaming_test', 'cgrt_render_test_shortcode' );
}
add_action( 'init', 'cgrt_init' );

/**
 * Render the test UI
 */
function cgrt_render_test_shortcode( $atts ) {
	// Enqueue assets
	wp_enqueue_style( 'cgrt-frontend-css', CGRT_URL . 'assets/css/frontend.css', array(), CGRT_VERSION );
	wp_enqueue_script( 'cgrt-frontend-js', CGRT_URL . 'assets/js/frontend.js', array( 'jquery' ), CGRT_VERSION, true );

	// Localize script with settings and platform data
	$settings = get_option( 'cgrt_settings', cgrt_get_default_settings() );
	$platforms = get_option( 'cgrt_platforms', cgrt_get_default_platforms() );

	wp_localize_script( 'cgrt-frontend-js', 'cgrt_data', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'settings' => $settings,
		'platforms' => $platforms,
		'nonce'    => wp_create_nonce( 'cgrt_nonce' )
	) );

	ob_start();
	include CGRT_PATH . 'templates/test-ui.php';
	return ob_get_clean();
}

/**
 * Activation Hook
 */
register_activation_hook( __FILE__, 'cgrt_activate' );
function cgrt_activate() {
	if ( ! get_option( 'cgrt_settings' ) ) {
		update_option( 'cgrt_settings', cgrt_get_default_settings() );
	}
	if ( ! get_option( 'cgrt_platforms' ) ) {
		update_option( 'cgrt_platforms', cgrt_get_default_platforms() );
	}
}
