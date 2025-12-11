<?php
/**
 * Scripts and Styles Loader
 *
 * @package CloudGamingAvailability
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CGA_Loader class
 */
class CGA_Loader {

    /**
     * List of 9 cloud gaming platforms
     */
    private static $platforms = array(
        'geforce-now'         => array(
            'name'  => 'GeForce NOW',
            'url'   => 'https://www.nvidia.com/en-us/geforce-now/',
            'color' => '#76b900',
        ),
        'xbox-cloud-gaming'   => array(
            'name'  => 'Xbox Cloud Gaming',
            'url'   => 'https://www.xbox.com/en-US/play',
            'color' => '#107c10',
        ),
        'ps-plus-premium'     => array(
            'name'  => 'PlayStation Plus Premium',
            'url'   => 'https://www.playstation.com/en-us/ps-plus/',
            'color' => '#003087',
        ),
        'amazon-luna'         => array(
            'name'  => 'Amazon Luna',
            'url'   => 'https://www.amazon.com/Luna/',
            'color' => '#FF9900',
        ),
        'boosteroid'          => array(
            'name'  => 'Boosteroid',
            'url'   => 'https://boosteroid.com/',
            'color' => '#6f1dff',
        ),
        'shadow-pc'           => array(
            'name'  => 'Shadow PC',
            'url'   => 'https://shadow.tech/',
            'color' => '#000000',
        ),
        'air-gpu'             => array(
            'name'  => 'Air GPU',
            'url'   => 'https://airgpu.tech/',
            'color' => '#1a73e8',
        ),
        'blacknut'            => array(
            'name'  => 'Blacknut',
            'url'   => 'https://www.blacknut.com/',
            'color' => '#ff0000',
        ),
        'clouddeck'           => array(
            'name'  => 'CloudDeck',
            'url'   => 'https://clouddeck.me/',
            'color' => '#00a4ef',
        ),
    );

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Enqueue frontend CSS
        wp_enqueue_style(
            'cga-frontend',
            CGA_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            CGA_PLUGIN_VERSION
        );

        // Enqueue frontend JS
        wp_enqueue_script(
            'cga-frontend',
            CGA_PLUGIN_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            CGA_PLUGIN_VERSION,
            true
        );

        // Pass data to frontend script
        wp_localize_script(
            'cga-frontend',
            'cgaData',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'cga_frontend_nonce' ),
            )
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets() {
        $screen = get_current_screen();

        // Only enqueue on our custom post type and settings pages
        if ( ! isset( $screen ) ) {
            return;
        }

        if ( 'cloud_games' !== $screen->post_type && 'settings_page_cga-settings' !== $screen->id && 'cloud_games_page_cga-game-manager' !== $screen->id ) {
            return;
        }

        // Enqueue admin CSS
        wp_enqueue_style(
            'cga-admin',
            CGA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            CGA_PLUGIN_VERSION
        );

        // Enqueue game manager CSS
        wp_enqueue_style(
            'cga-admin-game-manager',
            CGA_PLUGIN_URL . 'assets/css/admin-game-manager.css',
            array(),
            CGA_PLUGIN_VERSION
        );

        // Enqueue admin JS
        wp_enqueue_script(
            'cga-admin',
            CGA_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            CGA_PLUGIN_VERSION,
            true
        );

        // Pass platform data to admin script
        wp_localize_script(
            'cga-admin',
            'cgaAdmin',
            array(
                'platforms' => self::get_platforms(),
                'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'cga_admin_nonce' ),
            )
        );

        // Enqueue media uploader for logo management
        wp_enqueue_media();
    }

    /**
     * Get all platforms
     *
     * @return array
     */
    public static function get_platforms() {
        return self::$platforms;
    }

    /**
     * Get platform by ID
     *
     * @param string $platform_id Platform ID.
     * @return array|null
     */
    public static function get_platform( $platform_id ) {
        return isset( self::$platforms[ $platform_id ] ) ? self::$platforms[ $platform_id ] : null;
    }

    /**
     * Get platform IDs
     *
     * @return array
     */
    public static function get_platform_ids() {
        return array_keys( self::$platforms );
    }
}
