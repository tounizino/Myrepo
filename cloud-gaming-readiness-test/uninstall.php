<?php
/**
 * Plugin uninstall handler.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete all plugin options.
$option_names = array(
    'cgrt_version',
    'cgrt_home_url',
    'cgrt_show_shortcode',
    'cgrt_enable_page_mode',
    'cgrt_theme_color',
    'cgrt_default_mode',
    'cgrt_custom_css',
    'cgrt_cloudflare_enabled',
    'cgrt_cloudflare_api_key',
    'cgrt_cloudflare_email',
    'cgrt_latency_excellent',
    'cgrt_latency_good',
    'cgrt_latency_fair',
    'cgrt_jitter_excellent',
    'cgrt_jitter_good',
    'cgrt_jitter_fair',
    'cgrt_packet_loss_good',
    'cgrt_packet_loss_fair',
    'cgrt_ping_count',
    'cgrt_test_timeout',
    'cgrt_enable_advanced',
);

foreach ( $option_names as $option_name ) {
    delete_option( $option_name );
}

// Clear all transients.
global $wpdb;
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like( '_transient_cgrt_' ) . '%'
    )
);

$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like( '_transient_timeout_cgrt_' ) . '%'
    )
);
