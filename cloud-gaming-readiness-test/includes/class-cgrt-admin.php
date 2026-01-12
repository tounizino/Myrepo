<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CGRT_Admin {

	/**
	 * @var CGRT_Admin
	 */
	private static $instance;

	/**
	 * @return CGRT_Admin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_post_cgrt_save_settings', array( $this, 'handle_save_settings' ) );
		add_action( 'admin_post_cgrt_save_platform', array( $this, 'handle_save_platform' ) );
		add_action( 'admin_post_cgrt_delete_platform', array( $this, 'handle_delete_platform' ) );
		add_action( 'admin_post_cgrt_save_endpoint', array( $this, 'handle_save_endpoint' ) );
		add_action( 'admin_post_cgrt_delete_endpoint', array( $this, 'handle_delete_endpoint' ) );
	}

	public function add_menu() {
		add_menu_page(
			__( 'Cloud Gaming Readiness', 'cloud-gaming-readiness-test' ),
			__( 'CG Readiness', 'cloud-gaming-readiness-test' ),
			'manage_options',
			'cgrt-admin',
			array( $this, 'render_dashboard' ),
			'dashicons-performance',
			85
		);

		add_submenu_page(
			'cgrt-admin',
			__( 'Dashboard', 'cloud-gaming-readiness-test' ),
			__( 'Dashboard', 'cloud-gaming-readiness-test' ),
			'manage_options',
			'cgrt-admin',
			array( $this, 'render_dashboard' )
		);

		add_submenu_page(
			'cgrt-admin',
			__( 'Platforms & Endpoints', 'cloud-gaming-readiness-test' ),
			__( 'Platforms', 'cloud-gaming-readiness-test' ),
			'manage_options',
			'cgrt-platforms',
			array( $this, 'render_platforms' )
		);

		add_submenu_page(
			'cgrt-admin',
			__( 'Settings', 'cloud-gaming-readiness-test' ),
			__( 'Settings', 'cloud-gaming-readiness-test' ),
			'manage_options',
			'cgrt-settings',
			array( $this, 'render_settings' )
		);
	}

	public function enqueue_assets( $hook ) {
		if ( strpos( $hook, 'cgrt-' ) === false && strpos( $hook, 'page_cgrt' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'cgrt-admin',
			CGRT_PLUGIN_URL . 'assets/admin/admin.css',
			array(),
			CGRT_VERSION
		);

		wp_enqueue_script(
			'cgrt-admin',
			CGRT_PLUGIN_URL . 'assets/admin/admin.js',
			array( 'jquery' ),
			CGRT_VERSION,
			true
		);
	}

	public function render_dashboard() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'cloud-gaming-readiness-test' ) );
		}

		$stats = CGRT_Database::get_stats();

		include CGRT_PLUGIN_DIR . 'templates/admin-dashboard.php';
	}

	public function render_platforms() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'cloud-gaming-readiness-test' ) );
		}

		$platforms = CGRT_Database::get_platforms_with_endpoints();
		$action    = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
		$id        = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;

		if ( 'edit_platform' === $action && $id > 0 ) {
			$platform = CGRT_Database::get_platform( $id );
			if ( $platform ) {
				include CGRT_PLUGIN_DIR . 'templates/admin-edit-platform.php';
				return;
			}
		}

		if ( 'new_platform' === $action ) {
			$platform = array(
				'id'      => 0,
				'name'    => '',
				'slug'    => '',
				'enabled' => 1,
				'weights_json' => null,
			);
			include CGRT_PLUGIN_DIR . 'templates/admin-edit-platform.php';
			return;
		}

		if ( 'edit_endpoint' === $action && $id > 0 ) {
			global $wpdb;
			$tables   = CGRT_Database::tables();
			$endpoint = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tables['endpoints']} WHERE id = %d", $id ), ARRAY_A );
			if ( $endpoint ) {
				$platform_id = (int) $endpoint['platform_id'];
				include CGRT_PLUGIN_DIR . 'templates/admin-edit-endpoint.php';
				return;
			}
		}

		if ( 'new_endpoint' === $action && isset( $_GET['platform_id'] ) ) {
			$platform_id = (int) $_GET['platform_id'];
			$endpoint    = array(
				'id'           => 0,
				'platform_id'  => $platform_id,
				'region'       => '',
				'endpoint_url' => '',
				'method'       => 'HEAD',
				'enabled'      => 1,
				'timeout_ms'   => 2500,
				'notes'        => '',
			);
			include CGRT_PLUGIN_DIR . 'templates/admin-edit-endpoint.php';
			return;
		}

		include CGRT_PLUGIN_DIR . 'templates/admin-platforms.php';
	}

	public function render_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'cloud-gaming-readiness-test' ) );
		}

		$settings = CGRT_Database::get_settings();

		include CGRT_PLUGIN_DIR . 'templates/admin-settings.php';
	}

	public function handle_save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'cloud-gaming-readiness-test' ) );
		}

		check_admin_referer( 'cgrt_save_settings' );

		$settings = array(
			'test_duration_seconds' => isset( $_POST['test_duration_seconds'] ) ? max( 10, min( 120, (int) $_POST['test_duration_seconds'] ) ) : 18,
			'sample_interval_ms'    => isset( $_POST['sample_interval_ms'] ) ? max( 100, min( 2000, (int) $_POST['sample_interval_ms'] ) ) : 350,
			'endpoint_pick_pings'   => isset( $_POST['endpoint_pick_pings'] ) ? max( 3, min( 20, (int) $_POST['endpoint_pick_pings'] ) ) : 5,
			'spike_ms'              => isset( $_POST['spike_ms'] ) ? max( 10, min( 200, (int) $_POST['spike_ms'] ) ) : 35,
			'store_results'         => ! empty( $_POST['store_results'] ),
			'tiers'                 => array(
				'excellent' => isset( $_POST['tier_excellent'] ) ? max( 0, min( 100, (int) $_POST['tier_excellent'] ) ) : 85,
				'good'      => isset( $_POST['tier_good'] ) ? max( 0, min( 100, (int) $_POST['tier_good'] ) ) : 70,
				'fair'      => isset( $_POST['tier_fair'] ) ? max( 0, min( 100, (int) $_POST['tier_fair'] ) ) : 55,
				'poor'      => 0,
			),
			'default_weights'       => array(
				'latency'   => isset( $_POST['weight_latency'] ) ? (float) $_POST['weight_latency'] : 0.40,
				'jitter'    => isset( $_POST['weight_jitter'] ) ? (float) $_POST['weight_jitter'] : 0.25,
				'loss'      => isset( $_POST['weight_loss'] ) ? (float) $_POST['weight_loss'] : 0.25,
				'stability' => isset( $_POST['weight_stability'] ) ? (float) $_POST['weight_stability'] : 0.10,
			),
		);

		$sum = array_sum( $settings['default_weights'] );
		if ( $sum > 0.001 ) {
			foreach ( $settings['default_weights'] as &$val ) {
				$val = $val / $sum;
			}
		}

		CGRT_Database::update_settings( $settings );

		wp_safe_redirect( add_query_arg( 'updated', '1', admin_url( 'admin.php?page=cgrt-settings' ) ) );
		exit;
	}

	public function handle_save_platform() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'cloud-gaming-readiness-test' ) );
		}

		check_admin_referer( 'cgrt_save_platform' );

		$data = array(
			'id'      => isset( $_POST['id'] ) ? (int) $_POST['id'] : 0,
			'name'    => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'slug'    => isset( $_POST['slug'] ) ? sanitize_title( wp_unslash( $_POST['slug'] ) ) : '',
			'enabled' => ! empty( $_POST['enabled'] ),
			'weights' => array(
				'latency'   => isset( $_POST['weight_latency'] ) && $_POST['weight_latency'] !== '' ? (float) $_POST['weight_latency'] : null,
				'jitter'    => isset( $_POST['weight_jitter'] ) && $_POST['weight_jitter'] !== '' ? (float) $_POST['weight_jitter'] : null,
				'loss'      => isset( $_POST['weight_loss'] ) && $_POST['weight_loss'] !== '' ? (float) $_POST['weight_loss'] : null,
				'stability' => isset( $_POST['weight_stability'] ) && $_POST['weight_stability'] !== '' ? (float) $_POST['weight_stability'] : null,
			),
		);

		CGRT_Database::save_platform( $data );

		wp_safe_redirect( add_query_arg( 'updated', '1', admin_url( 'admin.php?page=cgrt-platforms' ) ) );
		exit;
	}

	public function handle_delete_platform() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'cloud-gaming-readiness-test' ) );
		}

		check_admin_referer( 'cgrt_delete_platform' );

		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
		if ( $id > 0 ) {
			CGRT_Database::delete_platform( $id );
		}

		wp_safe_redirect( add_query_arg( 'deleted', '1', admin_url( 'admin.php?page=cgrt-platforms' ) ) );
		exit;
	}

	public function handle_save_endpoint() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'cloud-gaming-readiness-test' ) );
		}

		check_admin_referer( 'cgrt_save_endpoint' );

		$data = array(
			'id'           => isset( $_POST['id'] ) ? (int) $_POST['id'] : 0,
			'platform_id'  => isset( $_POST['platform_id'] ) ? (int) $_POST['platform_id'] : 0,
			'region'       => isset( $_POST['region'] ) ? sanitize_text_field( wp_unslash( $_POST['region'] ) ) : '',
			'endpoint_url' => isset( $_POST['endpoint_url'] ) ? esc_url_raw( wp_unslash( $_POST['endpoint_url'] ) ) : '',
			'method'       => isset( $_POST['method'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['method'] ) ) ) : 'HEAD',
			'enabled'      => ! empty( $_POST['enabled'] ),
			'timeout_ms'   => isset( $_POST['timeout_ms'] ) ? (int) $_POST['timeout_ms'] : 2500,
			'notes'        => isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '',
		);

		CGRT_Database::save_endpoint( $data );

		wp_safe_redirect( add_query_arg( 'updated', '1', admin_url( 'admin.php?page=cgrt-platforms' ) ) );
		exit;
	}

	public function handle_delete_endpoint() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions.', 'cloud-gaming-readiness-test' ) );
		}

		check_admin_referer( 'cgrt_delete_endpoint' );

		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
		if ( $id > 0 ) {
			CGRT_Database::delete_endpoint( $id );
		}

		wp_safe_redirect( add_query_arg( 'deleted', '1', admin_url( 'admin.php?page=cgrt-platforms' ) ) );
		exit;
	}
}
