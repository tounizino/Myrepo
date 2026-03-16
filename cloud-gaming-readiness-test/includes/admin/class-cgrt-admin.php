<?php
/**
 * Admin-facing functionality handler.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Admin class.
 *
 * @since 1.0.0
 */
class CGRT_Admin {

    /**
     * The plugin name.
     *
     * @since 1.0.0
     * @var string
     */
    private $plugin_name;

    /**
     * The plugin version.
     *
     * @since 1.0.0
     * @var string
     */
    private $version;

    /**
     * Initialize the class.
     *
     * @since 1.0.0
     * @param string $plugin_name The plugin name.
     * @param string $version The plugin version.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     * @param string $hook The current admin page hook.
     */
    public function enqueue_styles( $hook ) {
        // Only load on our settings page.
        if ( 'settings_page_cloud-gaming-readiness-test' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name . '-admin',
            CGRT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     * @param string $hook The current admin page hook.
     */
    public function enqueue_scripts( $hook ) {
        // Only load on our settings page.
        if ( 'settings_page_cloud-gaming-readiness-test' !== $hook ) {
            return;
        }

        wp_enqueue_script(
            $this->plugin_name . '-admin',
            CGRT_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        wp_localize_script(
            $this->plugin_name . '-admin',
            'cgrtAdmin',
            array(
                'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'cgrt_admin_nonce' ),
                'strings'  => array(
                    'saved' => __( 'Settings saved successfully!', 'cloud-gaming-readiness-test' ),
                    'error' => __( 'An error occurred. Please try again.', 'cloud-gaming-readiness-test' ),
                ),
            )
        );
    }

    /**
     * Add the plugin admin menu.
     *
     * @since 1.0.0
     */
    public function add_plugin_admin_menu() {
        add_submenu_page(
            'options-general.php',
            __( 'Cloud Gaming Readiness Test', 'cloud-gaming-readiness-test' ),
            __( 'Cloud Gaming Test', 'cloud-gaming-readiness-test' ),
            'manage_options',
            'cloud-gaming-readiness-test',
            array( $this, 'display_plugin_admin_page' )
        );
    }

    /**
     * Register plugin settings.
     *
     * @since 1.0.0
     */
    public function register_settings() {
        // Register settings sections and fields.
        $settings = new CGRT_Settings();
        $settings->register();
    }

    /**
     * Render the admin settings page.
     *
     * @since 1.0.0
     */
    public function display_plugin_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'cloud-gaming-readiness-test' ) );
        }

        require_once CGRT_PLUGIN_DIR . 'includes/admin/views/admin-settings-page.php';
    }
}
