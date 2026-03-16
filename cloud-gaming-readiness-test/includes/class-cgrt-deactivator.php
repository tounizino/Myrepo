<?php
/**
 * Plugin deactivation handler.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Plugin deactivator class.
 *
 * @since 1.0.0
 */
class CGRT_Deactivator {

    /**
     * Deactivate the plugin.
     *
     * @since 1.0.0
     * @return void
     */
    public static function deactivate() {
        // Flush rewrite rules.
        flush_rewrite_rules();

        // Clear scheduled cron.
        wp_clear_scheduled_hook( 'cgrt_cleanup_cron' );

        // Clear transients.
        self::clear_transients();
    }

    /**
     * Clear plugin transients.
     *
     * @since 1.0.0
     * @return void
     */
    private static function clear_transients() {
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
    }
}
