<?php
/**
 * Uninstall Script for Cloud Loadout Latency Tester
 * Cleans up all plugin data when deleted
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove database tables
global $wpdb;

$table_name = $wpdb->prefix . 'cloudloadout_latency_tests';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Remove plugin options
delete_option('cloudloadout_latency_options');

// Remove user meta data
$wpdb->delete(
    $wpdb->usermeta,
    array('meta_key' => 'cloudloadout_admin_preferences')
);

// Remove transients
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_cloudloadout_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_cloudloadout_%'");

// Clean up any scheduled hooks
wp_clear_scheduled_hook('cloudloadout_cleanup_old_results');
wp_clear_scheduled_hook('cloudloadout_platform_status_check');

// Remove uploaded files (if any)
$upload_dir = wp_upload_dir();
$plugin_uploads_dir = $upload_dir['basedir'] . '/cloudloadout-latency-tester/';
if (is_dir($plugin_uploads_dir)) {
    cloudloadout_recursive_rmdir($plugin_uploads_dir);
}

/**
 * Recursively remove directory and all contents
 */
function cloudloadout_recursive_rmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object)) {
                    cloudloadout_recursive_rmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }
}