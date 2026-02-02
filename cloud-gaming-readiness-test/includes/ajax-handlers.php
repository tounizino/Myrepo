<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handle platform updates via AJAX
 */
add_action( 'wp_ajax_cgrt_save_platform', 'cgrt_ajax_save_platform' );
function cgrt_ajax_save_platform() {
    check_ajax_referer( 'cgrt_admin_nonce', 'nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }

    $platforms = get_option( 'cgrt_platforms', array() );
    $platform_data = $_POST['platform'];

    // Simple validation and sanitization
    $platform_id = sanitize_title( $platform_data['name'] );
    if ( isset( $platform_data['id'] ) && ! empty( $platform_data['id'] ) ) {
        $platform_id = sanitize_text_field( $platform_data['id'] );
    }

    $new_platform = array(
        'id' => $platform_id,
        'name' => sanitize_text_field( $platform_data['name'] ),
        'enabled' => $platform_data['enabled'] === 'true',
        'servers' => array()
    );

    if ( isset( $platform_data['servers'] ) && is_array( $platform_data['servers'] ) ) {
        foreach ( $platform_data['servers'] as $server ) {
            $new_platform['servers'][] = array(
                'region' => sanitize_text_field( $server['region'] ),
                'url' => esc_url_raw( $server['url'] ),
                'protocol' => sanitize_text_field( $server['protocol'] )
            );
        }
    }

    // Update or Add
    $found = false;
    foreach ( $platforms as &$p ) {
        if ( $p['id'] === $platform_id ) {
            $p = $new_platform;
            $found = true;
            break;
        }
    }

    if ( ! $found ) {
        $platforms[] = $new_platform;
    }

    update_option( 'cgrt_platforms', $platforms );
    wp_send_json_success( array( 'platforms' => $platforms ) );
}

add_action( 'wp_ajax_cgrt_delete_platform', 'cgrt_ajax_delete_platform' );
function cgrt_ajax_delete_platform() {
    check_ajax_referer( 'cgrt_admin_nonce', 'nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }

    $platform_id = sanitize_text_field( $_POST['id'] );
    $platforms = get_option( 'cgrt_platforms', array() );

    $platforms = array_filter( $platforms, function( $p ) use ( $platform_id ) {
        return $p['id'] !== $platform_id;
    });

    update_option( 'cgrt_platforms', array_values( $platforms ) );
    wp_send_json_success( array( 'platforms' => array_values( $platforms ) ) );
}

add_action( 'wp_ajax_cgrt_record_test', 'cgrt_ajax_record_test' );
add_action( 'wp_ajax_nopriv_cgrt_record_test', 'cgrt_ajax_record_test' );
function cgrt_ajax_record_test() {
    check_ajax_referer( 'cgrt_nonce', 'nonce' );
    $stats = get_option( 'cgrt_stats', array( 'total_tests' => 0 ) );
    $stats['total_tests']++;
    update_option( 'cgrt_stats', $stats );
    wp_send_json_success();
}
