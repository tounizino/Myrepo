<?php
/**
 * Cloud Layout Builder Pro - Uninstall Script
 *
 * This file runs when the plugin is deleted from WordPress.
 *
 * @package CloudLayoutBuilderPro
 */

// Exit if accessed directly or if not uninstalling
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Clean up plugin data on uninstall
 */
function clbp_uninstall_cleanup() {
    // Remove plugin options
    delete_option( 'clbp_settings' );
    delete_option( 'clbp_homepage_blocks' );
    delete_option( 'clbp_newsletter_leads' );
    delete_option( 'clbp_version' );

    // Remove all post meta created by the plugin
    delete_metadata( 'post', 0, '_clbp_featured', '', true );

    // Remove all term meta created by the plugin
    delete_metadata( 'term', 0, 'clbp_category_color', '', true );
    delete_metadata( 'term', 0, 'clbp_icon', '', true );

    // Clean up transients
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_clbp_%'" );
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_clbp_%'" );

    // Optional: Remove all user meta created by the plugin (if any)
    // delete_metadata( 'user', 0, 'clbp_preferences', '', true );

    // Clear any scheduled cron jobs
    wp_clear_scheduled_hook( 'clbp_daily_cleanup' );
}

// Run cleanup
clbp_uninstall_cleanup();
