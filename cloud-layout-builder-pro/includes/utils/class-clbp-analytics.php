<?php
/**
 * Cloud Layout Builder Pro - Analytics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CLBP_Analytics {

    /**
     * Record block view
     *
     * @param string $block Block slug.
     */
    public static function record( $block ) {
        if ( empty( $block ) ) {
            return;
        }

        $settings  = CLBP_Settings::get_all();
        $analytics = $settings['analytics'] ?? array();

        if ( ! isset( $analytics[ $block ] ) ) {
            $analytics[ $block ] = 0;
        }

        $analytics[ $block ]++;
        $settings['analytics'] = $analytics;

        update_option( CLBP_Settings::OPTION_KEY, $settings );
    }
}
