<?php
/**
 * Cloud Gaming Readiness Test - Uninstall
 *
 * Fired when the plugin is uninstalled.
 *
 * @package Cloud_Gaming_Readiness_Test
 */

// Exit if accessed directly
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('cgrt_settings');

// Delete any transients
delete_transient('cgrt_test_cache');

// Clear any cached data
wp_cache_flush();
