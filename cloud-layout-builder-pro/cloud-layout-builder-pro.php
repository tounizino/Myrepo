<?php
/**
 * Plugin Name: Cloud Layout Builder Pro
 * Plugin URI: https://cloudlayoutbuilder.pro
 * Description: A dynamic, block-based homepage and content-layout plugin for modern WordPress sites. Build stunning layouts with drag-and-drop blocks, widgets, and global customization options.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Cloud Layout Builder Team
 * Author URI: https://cloudlayoutbuilder.pro
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-layout-builder-pro
 * Domain Path: /languages
 *
 * @package CloudLayoutBuilderPro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'CLBP_VERSION', '1.0.0' );
define( 'CLBP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CLBP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CLBP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'CLBP_TEXT_DOMAIN', 'cloud-layout-builder-pro' );

/**
 * Main Cloud Layout Builder Pro Class
 *
 * @class CloudLayoutBuilderPro
 * @version 1.0.0
 */
final class CloudLayoutBuilderPro {

    /**
     * Single instance of the class
     *
     * @var CloudLayoutBuilderPro
     */
    protected static $_instance = null;

    /**
     * Main CloudLayoutBuilderPro Instance
     *
     * Ensures only one instance of CloudLayoutBuilderPro is loaded or can be loaded.
     *
     * @static
     * @return CloudLayoutBuilderPro - Main instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * CloudLayoutBuilderPro Constructor.
     */
    public function __construct() {
        $this->init_hooks();
        $this->includes();
    }

    /**
     * Hook into actions and filters
     */
    private function init_hooks() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'init', array( $this, 'init' ), 0 );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    /**
     * Include required core files
     */
    public function includes() {
        // Utils
        require_once CLBP_PLUGIN_DIR . 'includes/utils/class-clbp-settings.php';
        require_once CLBP_PLUGIN_DIR . 'includes/utils/class-clbp-helpers.php';
        require_once CLBP_PLUGIN_DIR . 'includes/utils/class-clbp-query.php';
        require_once CLBP_PLUGIN_DIR . 'includes/utils/class-clbp-analytics.php';

        // Admin
        require_once CLBP_PLUGIN_DIR . 'includes/admin/class-clbp-admin.php';
        require_once CLBP_PLUGIN_DIR . 'includes/admin/class-clbp-customizer.php';
        require_once CLBP_PLUGIN_DIR . 'includes/admin/class-clbp-block-manager.php';

        // Frontend
        require_once CLBP_PLUGIN_DIR . 'includes/frontend/class-clbp-shortcodes.php';
        require_once CLBP_PLUGIN_DIR . 'includes/frontend/class-clbp-schema.php';
        require_once CLBP_PLUGIN_DIR . 'includes/frontend/class-clbp-ajax.php';

        // Blocks
        require_once CLBP_PLUGIN_DIR . 'includes/blocks/class-clbp-block-base.php';
        require_once CLBP_PLUGIN_DIR . 'includes/blocks/class-clbp-latest-articles.php';
        require_once CLBP_PLUGIN_DIR . 'includes/blocks/class-clbp-featured-hero.php';
        require_once CLBP_PLUGIN_DIR . 'includes/blocks/class-clbp-category-highlight.php';
        require_once CLBP_PLUGIN_DIR . 'includes/blocks/class-clbp-tag-topic.php';
        require_once CLBP_PLUGIN_DIR . 'includes/blocks/class-clbp-carousel.php';
        require_once CLBP_PLUGIN_DIR . 'includes/blocks/class-clbp-mixed-content.php';
        require_once CLBP_PLUGIN_DIR . 'includes/blocks/class-clbp-custom-links.php';
        require_once CLBP_PLUGIN_DIR . 'includes/blocks/class-clbp-newsletter.php';
        require_once CLBP_PLUGIN_DIR . 'includes/blocks/class-clbp-quote-tip.php';

        // Widgets
        require_once CLBP_PLUGIN_DIR . 'includes/widgets/class-clbp-latest-posts-widget.php';
        require_once CLBP_PLUGIN_DIR . 'includes/widgets/class-clbp-category-widget.php';
        require_once CLBP_PLUGIN_DIR . 'includes/widgets/class-clbp-editors-picks-widget.php';
        require_once CLBP_PLUGIN_DIR . 'includes/widgets/class-clbp-search-widget.php';
        require_once CLBP_PLUGIN_DIR . 'includes/widgets/class-clbp-tag-cloud-widget.php';
        require_once CLBP_PLUGIN_DIR . 'includes/widgets/class-clbp-custom-html-widget.php';

        // Initialize
        CLBP_Admin::instance();
        CLBP_Customizer::instance();
        CLBP_Block_Manager::instance();
        CLBP_Shortcodes::instance();
        CLBP_Schema::instance();
        CLBP_Ajax::instance();
    }

    /**
     * Init CloudLayoutBuilderPro when WordPress Initialises
     */
    public function init() {
        // Register widgets
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );

        // Set up localisation
        $this->load_textdomain();

        do_action( 'clbp_init' );
    }

    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'cloud-layout-builder-pro', false, dirname( CLBP_PLUGIN_BASENAME ) . '/languages' );
    }

    /**
     * Register widgets
     */
    public function register_widgets() {
        register_widget( 'CLBP_Latest_Posts_Widget' );
        register_widget( 'CLBP_Category_Widget' );
        register_widget( 'CLBP_Editors_Picks_Widget' );
        register_widget( 'CLBP_Search_Widget' );
        register_widget( 'CLBP_Tag_Cloud_Widget' );
        register_widget( 'CLBP_Custom_HTML_Widget' );
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Main CSS
        wp_enqueue_style(
            'clbp-frontend',
            CLBP_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            CLBP_VERSION
        );

        // Dynamic CSS
        $this->add_dynamic_css();

        // Main JS
        wp_enqueue_script(
            'clbp-frontend',
            CLBP_PLUGIN_URL . 'assets/js/frontend.js',
            array( 'jquery' ),
            CLBP_VERSION,
            true
        );

        // Localize script
        wp_localize_script(
            'clbp-frontend',
            'clbpData',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'clbp_nonce' ),
            )
        );

        $settings = CLBP_Settings::get_all();
        if ( ! empty( $settings['custom_js'] ) ) {
            wp_add_inline_script( 'clbp-frontend', $settings['custom_js'] );
        }
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on plugin pages
        if ( strpos( $hook, 'cloud-layout-builder' ) === false ) {
            return;
        }

        // Admin CSS
        wp_enqueue_style(
            'clbp-admin',
            CLBP_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            CLBP_VERSION
        );

        // Admin JS
        wp_enqueue_script(
            'clbp-admin',
            CLBP_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery', 'jquery-ui-sortable' ),
            CLBP_VERSION,
            true
        );

        // Color picker
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        // Localize admin script
        wp_localize_script(
            'clbp-admin',
            'clbpAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'clbp_admin_nonce' ),
            )
        );
    }

    /**
     * Add dynamic CSS based on settings
     */
    private function add_dynamic_css() {
        $settings = CLBP_Settings::get_all();

        $css = ':root {';
        $css .= '--clbp-primary: ' . esc_attr( $settings['primary_color'] ?? '#1E88E5' ) . ';';
        $css .= '--clbp-secondary: ' . esc_attr( $settings['secondary_color'] ?? '#1565C0' ) . ';';
        $css .= '--clbp-background: ' . esc_attr( $settings['background_color'] ?? '#F9FAFB' ) . ';';
        $css .= '--clbp-text: ' . esc_attr( $settings['text_color'] ?? '#1F2937' ) . ';';
        $css .= '--clbp-font-family: ' . esc_attr( $settings['font_family'] ?? '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif' ) . ';';
        $css .= '--clbp-font-size-base: ' . esc_attr( $settings['font_size_base'] ?? '16px' ) . ';';
        $css .= '--clbp-font-size-title: ' . esc_attr( $settings['font_size_title'] ?? '32px' ) . ';';
        $css .= '--clbp-spacing: ' . esc_attr( $settings['spacing'] ?? '24px' ) . ';';
        $css .= '--clbp-padding: ' . esc_attr( $settings['padding'] ?? '24px' ) . ';';
        $css .= '--clbp-border-radius: ' . esc_attr( $settings['border_radius'] ?? '12px' ) . ';';
        $css .= '}';

        $width_type  = $settings['block_width'] ?? 'full';
        $custom_width = $settings['custom_width'] ?? '1200px';
        $max_width   = '100%';

        if ( 'boxed' === $width_type ) {
            $max_width = '1200px';
        } elseif ( 'custom' === $width_type && ! empty( $custom_width ) ) {
            $max_width = esc_attr( $custom_width );
        }

        if ( 'full' === $width_type ) {
            $css .= '.clbp-homepage-wrapper{max-width:100%;margin:0 auto;padding-left:calc(var(--clbp-spacing)/2);padding-right:calc(var(--clbp-spacing)/2);}';
        } else {
            $css .= '.clbp-homepage-wrapper{max-width:' . $max_width . ';margin:0 auto;padding-left:calc(var(--clbp-spacing)/2);padding-right:calc(var(--clbp-spacing)/2);}';
        }

        $css .= '.clbp-block{padding:var(--clbp-padding);}';

        // Add custom CSS
        if ( ! empty( $settings['custom_css'] ) ) {
            $css .= "\n" . wp_kses_post( $settings['custom_css'] );
        }

        wp_add_inline_style( 'clbp-frontend', $css );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options
        $defaults = array(
            'primary_color'      => '#1E88E5',
            'secondary_color'    => '#1565C0',
            'background_color'   => '#F9FAFB',
            'text_color'         => '#1F2937',
            'font_family'        => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
            'font_size_base'     => '16px',
            'font_size_title'    => '32px',
            'spacing'            => '24px',
            'border_radius'      => '12px',
            'layout_style'       => 'modern',
            'enable_shadows'     => false,
            'dark_mode'          => false,
            'lazy_load'          => true,
            'enable_schema'      => true,
        );

        if ( ! get_option( 'clbp_settings' ) ) {
            update_option( 'clbp_settings', $defaults );
        }

        // Create default homepage blocks
        if ( ! get_option( 'clbp_homepage_blocks' ) ) {
            $default_blocks = array(
                array(
                    'id'       => uniqid( 'block_' ),
                    'type'     => 'featured_hero',
                    'enabled'  => true,
                    'order'    => 1,
                    'settings' => array(),
                ),
                array(
                    'id'       => uniqid( 'block_' ),
                    'type'     => 'latest_articles',
                    'enabled'  => true,
                    'order'    => 2,
                    'settings' => array(),
                ),
            );
            update_option( 'clbp_homepage_blocks', $default_blocks );
        }

        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

/**
 * Returns the main instance of CloudLayoutBuilderPro
 *
 * @return CloudLayoutBuilderPro
 */
function CLBP() {
    return CloudLayoutBuilderPro::instance();
}

// Initialize the plugin
CLBP();
