<?php
/**
 * Cloud Layout Builder Pro - Settings Handler
 *
 * @package CloudLayoutBuilderPro\Utils
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Settings
 */
class CLBP_Settings {

    /**
     * Option key for plugin settings
     *
     * @var string
     */
    const OPTION_KEY = 'clbp_settings';

    /**
     * Retrieve all settings
     *
     * @return array
     */
    public static function get_all() {
        $defaults = self::defaults();
        $settings = get_option( self::OPTION_KEY, array() );

        if ( ! is_array( $settings ) ) {
            $settings = array();
        }

        return wp_parse_args( $settings, $defaults );
    }

    /**
     * Retrieve a single setting
     *
     * @param string $key Setting key.
     * @param mixed  $default Default value.
     *
     * @return mixed
     */
    public static function get( $key, $default = null ) {
        $settings = self::get_all();
        return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
    }

    /**
     * Update settings
     *
     * @param array $values Values to update.
     *
     * @return bool
     */
    public static function update( $values ) {
        $settings = self::get_all();
        $settings = wp_parse_args( $values, $settings );
        return update_option( self::OPTION_KEY, $settings );
    }

    /**
     * Reset settings to defaults
     */
    public static function reset() {
        update_option( self::OPTION_KEY, self::defaults() );
    }

    /**
     * Default settings
     *
     * @return array
     */
    public static function defaults() {
        return array(
            'primary_color'        => '#1E88E5',
            'secondary_color'      => '#1565C0',
            'background_color'     => '#F9FAFB',
            'text_color'           => '#1F2937',
            'font_family'          => 'Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
            'font_size_base'       => '16px',
            'font_size_title'      => '32px',
            'block_width'          => 'full',
            'custom_width'         => '1200px',
            'spacing'              => '24px',
            'padding'              => '24px',
            'border_radius'        => '12px',
            'layout_style'         => 'modern',
            'dark_mode'            => false,
            'enable_shadows'       => false,
            'lazy_load'            => true,
            'enable_schema'        => true,
            'device_visibility'    => array( 'desktop' => true, 'tablet' => true, 'mobile' => true ),
            'custom_css'           => '',
            'custom_js'            => '',
            'saved_presets'        => array(),
            'analytics'            => array(),
            'last_export'          => '',
        );
    }
}
