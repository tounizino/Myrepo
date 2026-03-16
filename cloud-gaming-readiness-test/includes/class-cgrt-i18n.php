<?php
/**
 * Internationalization handler.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Internationalization class.
 *
 * @since 1.0.0
 */
class CGRT_I18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since 1.0.0
     * @return void
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'cloud-gaming-readiness-test',
            false,
            dirname( CGRT_PLUGIN_BASENAME ) . '/languages/'
        );
    }
}
