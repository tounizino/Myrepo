<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add admin menu
 */
function cgrt_add_admin_menu() {
    add_menu_page(
        __( 'Cloud Gaming Test', 'cloud-gaming-test' ),
        __( 'Cloud Gaming Test', 'cloud-gaming-test' ),
        'manage_options',
        'cloud-gaming-test',
        'cgrt_render_admin_page',
        'dashicons-performance'
    );
}
add_action( 'admin_menu', 'cgrt_add_admin_menu' );

/**
 * Render admin page
 */
function cgrt_render_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Save settings if posted
    if ( isset( $_POST['cgrt_save_settings'] ) && check_admin_referer( 'cgrt_settings_nonce' ) ) {
        $settings = array(
            'test_duration' => isset( $_POST['test_duration'] ) ? intval( $_POST['test_duration'] ) : 15,
            'test_intensity' => isset( $_POST['test_intensity'] ) ? intval( $_POST['test_intensity'] ) : 5,
            'weights' => array(
                'latency' => isset( $_POST['weight_latency'] ) ? intval( $_POST['weight_latency'] ) : 40,
                'jitter' => isset( $_POST['weight_jitter'] ) ? intval( $_POST['weight_jitter'] ) : 30,
                'packet_loss' => isset( $_POST['weight_packet_loss'] ) ? intval( $_POST['weight_packet_loss'] ) : 20,
                'stability' => isset( $_POST['weight_stability'] ) ? intval( $_POST['weight_stability'] ) : 10
            ),
            'thresholds' => array(
                'excellent' => isset( $_POST['threshold_excellent'] ) ? intval( $_POST['threshold_excellent'] ) : 90,
                'good' => isset( $_POST['threshold_good'] ) ? intval( $_POST['threshold_good'] ) : 75,
                'fair' => isset( $_POST['threshold_fair'] ) ? intval( $_POST['threshold_fair'] ) : 50,
                'poor' => 0
            ),
            'latency_thresholds' => array(
                'excellent' => isset( $_POST['latency_excellent'] ) ? intval( $_POST['latency_excellent'] ) : 30,
                'good' => isset( $_POST['latency_good'] ) ? intval( $_POST['latency_good'] ) : 60,
                'fair' => isset( $_POST['latency_fair'] ) ? intval( $_POST['latency_fair'] ) : 100
            ),
            'jitter_thresholds' => array(
                'excellent' => isset( $_POST['jitter_excellent'] ) ? intval( $_POST['jitter_excellent'] ) : 5,
                'good' => isset( $_POST['jitter_good'] ) ? intval( $_POST['jitter_good'] ) : 15,
                'fair' => isset( $_POST['jitter_fair'] ) ? intval( $_POST['jitter_fair'] ) : 30
            )
        );
        update_option( 'cgrt_settings', $settings );
        echo '<div class="updated"><p>' . __( 'Settings saved.', 'cloud-gaming-test' ) . '</p></div>';
    }

    $settings = get_option( 'cgrt_settings', cgrt_get_default_settings() );
    $platforms = get_option( 'cgrt_platforms', cgrt_get_default_platforms() );
    $stats = get_option( 'cgrt_stats', array( 'total_tests' => 0 ) );

    // Enqueue admin assets
    wp_enqueue_style( 'cgrt-admin-css', CGRT_URL . 'assets/css/admin.css', array(), CGRT_VERSION );
    wp_enqueue_script( 'cgrt-admin-js', CGRT_URL . 'assets/js/admin.js', array( 'jquery' ), CGRT_VERSION, true );

    wp_localize_script( 'cgrt-admin-js', 'cgrt_admin', array(
        'nonce' => wp_create_nonce( 'cgrt_admin_nonce' )
    ) );

    if ( file_exists( CGRT_PATH . 'templates/admin-page.php' ) ) {
        include CGRT_PATH . 'templates/admin-page.php';
    }
}
