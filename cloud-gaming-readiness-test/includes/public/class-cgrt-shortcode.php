<?php
/**
 * Shortcode handler for the plugin.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Shortcode class.
 *
 * @since 1.0.0
 */
class CGRT_Shortcode {

    /**
     * Render the cloud gaming test interface.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public static function render( $atts ) {
        $atts = shortcode_atts( array(
            'mode'     => get_option( 'cgrt_default_mode', 'dark' ),
            'theme'    => get_option( 'cgrt_theme_color', '#6366f1' ),
            'full_page' => false,
        ), $atts, 'cloud_gaming_test' );

        // Enqueue assets.
        wp_enqueue_style( 'cloud-gaming-readiness-test-public' );
        wp_enqueue_style( 'cloud-gaming-readiness-test-' . $atts['mode'] );
        wp_enqueue_script( 'cloud-gaming-readiness-test-app' );

        ob_start();
        require CGRT_PLUGIN_DIR . 'includes/public/views/test-page.php';
        return ob_get_clean();
    }

    /**
     * Get supported shortcode attributes.
     *
     * @since 1.0.0
     * @return array Supported attributes.
     */
    public static function get_supported_attributes() {
        return array(
            'mode' => array(
                'default' => 'dark',
                'values'  => array( 'dark', 'light' ),
                'description' => __( 'Theme mode (dark or light)', 'cloud-gaming-readiness-test' ),
            ),
            'theme' => array(
                'default' => '#6366f1',
                'description' => __( 'Primary theme color (hex)', 'cloud-gaming-readiness-test' ),
            ),
            'full_page' => array(
                'default' => false,
                'values'  => array( 'true', 'false' ),
                'description' => __( 'Enable full-page mode', 'cloud-gaming-readiness-test' ),
            ),
        );
    }
}
