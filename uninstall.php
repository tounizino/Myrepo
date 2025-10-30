<?php
/**
 * Uninstall Script
 * Fired when the plugin is uninstalled
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('ubcg_primary_color');
delete_option('ubcg_secondary_color');
delete_option('ubcg_accent_color');
delete_option('ubcg_text_color');
delete_option('ubcg_posts_per_page');
delete_option('ubcg_enable_seo');

// Clean up post meta
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_ubcg_%'");

// Clear any cached data
wp_cache_flush();
