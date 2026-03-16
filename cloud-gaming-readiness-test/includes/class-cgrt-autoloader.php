<?php
/**
 * Autoloader for the Cloud Gaming Readiness Test plugin.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Autoloader class.
 *
 * @since 1.0.0
 */
class CGRT_Autoloader {

    /**
     * Registered classes map.
     *
     * @since 1.0.0
     * @var array
     */
    private static $class_map = array(
        'CGRT_Plugin'            => 'class-cgrt-plugin.php',
        'CGRT_Activator'         => 'class-cgrt-activator.php',
        'CGRT_Deactivator'       => 'class-cgrt-deactivator.php',
        'CGRT_Loader'            => 'class-cgrt-loader.php',
        'CGRT_I18n'              => 'class-cgrt-i18n.php',
        'CGRT_Admin'             => 'admin/class-cgrt-admin.php',
        'CGRT_Settings'          => 'admin/class-cgrt-settings.php',
        'CGRT_Public'            => 'public/class-cgrt-public.php',
        'CGRT_Shortcode'         => 'public/class-cgrt-shortcode.php',
        'CGRT_REST_API'          => 'rest-api/class-cgrt-rest-api.php',
        'CGRT_Cloudflare_API'    => 'rest-api/class-cgrt-cloudflare-api.php',
    );

    /**
     * Register the autoloader.
     *
     * @since 1.0.0
     * @return void
     */
    public static function register() {
        spl_autoload_register( array( __CLASS__, 'autoload' ) );
    }

    /**
     * Autoload classes.
     *
     * @since 1.0.0
     * @param string $class Class name.
     * @return void
     */
    public static function autoload( $class ) {
        // Check if the class is in our map.
        if ( isset( self::$class_map[ $class ] ) ) {
            $file = CGRT_PLUGIN_DIR . 'includes/' . self::$class_map[ $class ];

            // Require the file if it exists.
            if ( file_exists( $file ) ) {
                require_once $file;
            }
        }
    }
}
