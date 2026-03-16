<?php
/**
 * Plugin activation handler.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Plugin activator class.
 *
 * @since 1.0.0
 */
class CGRT_Activator {

    /**
     * Activate the plugin.
     *
     * @since 1.0.0
     * @return void
     */
    public static function activate() {
        // Set default options.
        self::set_default_options();

        // Set version.
        update_option( 'cgrt_version', CGRT_VERSION );

        // Flush rewrite rules.
        flush_rewrite_rules();

        // Schedule cleanup cron if needed.
        if ( ! wp_next_scheduled( 'cgrt_cleanup_cron' ) ) {
            wp_schedule_event( time(), 'daily', 'cgrt_cleanup_cron' );
        }
    }

    /**
     * Set default plugin options.
     *
     * @since 1.0.0
     * @return void
     */
    private static function set_default_options() {
        $defaults = array(
            // General settings.
            'home_url'           => home_url(),
            'show_shortcode'     => true,
            'enable_page_mode'   => true,

            // Appearance settings.
            'theme_color'        => '#6366f1',
            'default_mode'       => 'dark',
            'custom_css'         => '',

            // Cloudflare API settings.
            'cloudflare_enabled' => false,
            'cloudflare_api_key' => '',
            'cloudflare_email'   => '',

            // Results thresholds.
            'latency_excellent'  => 20,
            'latency_good'       => 50,
            'latency_fair'       => 100,
            'jitter_excellent'   => 5,
            'jitter_good'        => 10,
            'jitter_fair'        => 20,
            'packet_loss_good'   => 1,
            'packet_loss_fair'   => 3,

            // Test settings.
            'ping_count'         => 10,
            'test_timeout'       => 30,
            'enable_advanced'    => false,
        );

        foreach ( $defaults as $key => $value ) {
            if ( get_option( 'cgrt_' . $key ) === false ) {
                add_option( 'cgrt_' . $key, $value );
            }
        }
    }
}
