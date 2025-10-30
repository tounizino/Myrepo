<?php
/**
 * Cloud Layout Builder Pro - Admin Panel
 *
 * @package CloudLayoutBuilderPro\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Admin
 */
class CLBP_Admin {

    /**
     * Instance
     *
     * @var CLBP_Admin
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return CLBP_Admin
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'wp_ajax_clbp_save_homepage_blocks', array( $this, 'ajax_save_homepage_blocks' ) );
        add_action( 'wp_ajax_clbp_export_settings', array( $this, 'ajax_export_settings' ) );
        add_action( 'wp_ajax_clbp_import_settings', array( $this, 'ajax_import_settings' ) );
    }

    /**
     * Add menu pages
     */
    public function add_menu_pages() {
        add_menu_page(
            __( 'Cloud Layout Builder Pro', 'cloud-layout-builder-pro' ),
            __( 'Layout Builder', 'cloud-layout-builder-pro' ),
            'manage_options',
            'cloud-layout-builder',
            array( $this, 'render_main_page' ),
            'dashicons-layout',
            30
        );

        add_submenu_page(
            'cloud-layout-builder',
            __( 'Homepage Builder', 'cloud-layout-builder-pro' ),
            __( 'Homepage Builder', 'cloud-layout-builder-pro' ),
            'manage_options',
            'cloud-layout-builder',
            array( $this, 'render_main_page' )
        );

        add_submenu_page(
            'cloud-layout-builder',
            __( 'Settings', 'cloud-layout-builder-pro' ),
            __( 'Settings', 'cloud-layout-builder-pro' ),
            'manage_options',
            'cloud-layout-builder-settings',
            array( $this, 'render_settings_page' )
        );

        add_submenu_page(
            'cloud-layout-builder',
            __( 'Import/Export', 'cloud-layout-builder-pro' ),
            __( 'Import/Export', 'cloud-layout-builder-pro' ),
            'manage_options',
            'cloud-layout-builder-import-export',
            array( $this, 'render_import_export_page' )
        );

        add_submenu_page(
            'cloud-layout-builder',
            __( 'Analytics', 'cloud-layout-builder-pro' ),
            __( 'Analytics', 'cloud-layout-builder-pro' ),
            'manage_options',
            'cloud-layout-builder-analytics',
            array( $this, 'render_analytics_page' )
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'clbp_settings_group',
            'clbp_settings',
            array( $this, 'sanitize_settings' )
        );

        register_setting(
            'clbp_homepage_blocks_group',
            'clbp_homepage_blocks',
            array( $this, 'sanitize_blocks' )
        );
    }

    /**
     * Sanitize settings
     *
     * @param array $input Input values.
     *
     * @return array
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();

        $color_fields = array( 'primary_color', 'secondary_color', 'background_color', 'text_color' );
        foreach ( $color_fields as $field ) {
            if ( isset( $input[ $field ] ) ) {
                $sanitized[ $field ] = sanitize_hex_color( $input[ $field ] );
            }
        }

        $text_fields = array( 'font_family', 'font_size_base', 'font_size_title', 'spacing', 'padding', 'border_radius', 'block_width', 'custom_width', 'layout_style' );
        foreach ( $text_fields as $field ) {
            if ( isset( $input[ $field ] ) ) {
                $sanitized[ $field ] = sanitize_text_field( $input[ $field ] );
            }
        }

        $bool_fields = array( 'dark_mode', 'enable_shadows', 'lazy_load', 'enable_schema' );
        foreach ( $bool_fields as $field ) {
            if ( isset( $input[ $field ] ) ) {
                $sanitized[ $field ] = (bool) $input[ $field ];
            }
        }

        if ( isset( $input['custom_css'] ) ) {
            $sanitized['custom_css'] = wp_strip_all_tags( $input['custom_css'] );
        }

        if ( isset( $input['custom_js'] ) ) {
            $sanitized['custom_js'] = wp_strip_all_tags( $input['custom_js'] );
        }

        return $sanitized;
    }

    /**
     * Sanitize blocks
     *
     * @param array $input Input values.
     *
     * @return array
     */
    public function sanitize_blocks( $input ) {
        if ( ! is_array( $input ) ) {
            return array();
        }

        return CLBP_Helpers::sanitize_array( $input );
    }

    /**
     * Render main page
     */
    public function render_main_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $blocks = get_option( 'clbp_homepage_blocks', array() );

        include CLBP_PLUGIN_DIR . 'templates/admin/homepage-builder.php';
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $settings = CLBP_Settings::get_all();

        include CLBP_PLUGIN_DIR . 'templates/admin/settings.php';
    }

    /**
     * Render import/export page
     */
    public function render_import_export_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        include CLBP_PLUGIN_DIR . 'templates/admin/import-export.php';
    }

    /**
     * Render analytics page
     */
    public function render_analytics_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        include CLBP_PLUGIN_DIR . 'templates/admin/analytics.php';
    }

    /**
     * AJAX: Save homepage blocks
     */
    public function ajax_save_homepage_blocks() {
        check_ajax_referer( 'clbp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'cloud-layout-builder-pro' ) ) );
        }

        $blocks = isset( $_POST['blocks'] ) ? json_decode( stripslashes( $_POST['blocks'] ), true ) : array();

        if ( ! is_array( $blocks ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid blocks data', 'cloud-layout-builder-pro' ) ) );
        }

        update_option( 'clbp_homepage_blocks', $blocks );

        wp_send_json_success( array( 'message' => __( 'Blocks saved successfully', 'cloud-layout-builder-pro' ) ) );
    }

    /**
     * AJAX: Export settings
     */
    public function ajax_export_settings() {
        check_ajax_referer( 'clbp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'cloud-layout-builder-pro' ) ) );
        }

        $data = array(
            'settings' => CLBP_Settings::get_all(),
            'blocks'   => get_option( 'clbp_homepage_blocks', array() ),
            'version'  => CLBP_VERSION,
            'date'     => gmdate( 'Y-m-d H:i:s' ),
        );

        wp_send_json_success( array( 'data' => $data ) );
    }

    /**
     * AJAX: Import settings
     */
    public function ajax_import_settings() {
        check_ajax_referer( 'clbp_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied', 'cloud-layout-builder-pro' ) ) );
        }

        $data = isset( $_POST['data'] ) ? json_decode( stripslashes( $_POST['data'] ), true ) : null;

        if ( ! $data || ! is_array( $data ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid import data', 'cloud-layout-builder-pro' ) ) );
        }

        if ( isset( $data['settings'] ) ) {
            CLBP_Settings::update( $data['settings'] );
        }

        if ( isset( $data['blocks'] ) ) {
            update_option( 'clbp_homepage_blocks', $data['blocks'] );
        }

        wp_send_json_success( array( 'message' => __( 'Settings imported successfully', 'cloud-layout-builder-pro' ) ) );
    }
}
