<?php
/**
 * Plugin Name: Cloud Game Explorer
 * Description: A modern game discovery interface with cloud availability integration.
 * Version: 1.0.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CGE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once CGE_PLUGIN_DIR . 'includes/api-handler.php';
require_once CGE_PLUGIN_DIR . 'includes/settings.php';

class CloudGameExplorer {
	public function __construct() {
		add_shortcode( 'cloud_game_explorer', array( $this, 'render_ui' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_ajax_cge_search_games', array( $this, 'ajax_search_games' ) );
		add_action( 'wp_ajax_nopriv_cge_search_games', array( $this, 'ajax_search_games' ) );
	}

	public function enqueue_assets() {
		wp_enqueue_style( 'cge-style', CGE_PLUGIN_URL . 'assets/css/style.css', array(), '1.0.0' );
		wp_enqueue_script( 'cge-script', CGE_PLUGIN_URL . 'assets/js/app.js', array( 'jquery' ), '1.0.0', true );

		wp_localize_script( 'cge-script', 'cgeData', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'cge_nonce' ),
		) );
	}

	public function render_ui() {
		ob_start();
		include CGE_PLUGIN_DIR . 'templates/main-ui.php';
		return ob_get_clean();
	}

	public function ajax_search_games() {
		check_ajax_referer( 'cge_nonce', 'nonce' );

		$search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$platform = isset( $_POST['platform'] ) ? sanitize_text_field( $_POST['platform'] ) : '';
		$page = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

		$api_handler = new CGE_API_Handler();
		$results = $api_handler->search_games( $search, $platform, $page );

		wp_send_json_success( $results );
	}
}

new CloudGameExplorer();
