<?php
/**
 * Core plugin class.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
class CGRT_Plugin {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     *
     * @since 1.0.0
     * @var CGRT_Loader
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since 1.0.0
     * @var string
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since 1.0.0
     * @var string
     */
    protected $version;

    /**
     * Initialize the plugin.
     *
     * @since 1.0.0
     */
    public function __construct() {
        if ( ! defined( 'CGRT_VERSION' ) ) {
            define( 'CGRT_VERSION', '1.0.0' );
        }

        $this->version = CGRT_VERSION;
        $this->plugin_name = 'cloud-gaming-readiness-test';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_rest_api_hooks();
    }

    /**
     * Load the required dependencies.
     *
     * @since 1.0.0
     * @return void
     */
    private function load_dependencies() {
        $this->loader = new CGRT_Loader();
    }

    /**
     * Define the locale for internationalization.
     *
     * @since 1.0.0
     * @return void
     */
    private function set_locale() {
        $plugin_i18n = new CGRT_I18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since 1.0.0
     * @return void
     */
    private function define_admin_hooks() {
        $plugin_admin = new CGRT_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since 1.0.0
     * @return void
     */
    private function define_public_hooks() {
        $plugin_public = new CGRT_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );

        // Register page template.
        $this->loader->add_filter( 'page_template', $plugin_public, 'load_page_template' );
        $this->loader->add_filter( 'theme_page_templates', $plugin_public, 'add_page_template' );
    }

    /**
     * Register all of the hooks related to the REST API functionality.
     *
     * @since 1.0.0
     * @return void
     */
    private function define_rest_api_hooks() {
        $rest_api = new CGRT_REST_API( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'rest_api_init', $rest_api, 'register_routes' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since 1.0.0
     * @return void
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it.
     *
     * @since 1.0.0
     * @return string The plugin name.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The version number of the plugin.
     *
     * @since 1.0.0
     * @return string The version number.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since 1.0.0
     * @return CGRT_Loader Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }
}

/**
 * Code that runs during plugin activation.
 *
 * @since 1.0.0
 */
function activate_cloud_gaming_readiness_test() {
    require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-activator.php';
    CGRT_Activator::activate();
}

/**
 * Code that runs during plugin deactivation.
 *
 * @since 1.0.0
 */
function deactivate_cloud_gaming_readiness_test() {
    require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-deactivator.php';
    CGRT_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cloud_gaming_readiness_test' );
register_deactivation_hook( __FILE__, 'deactivate_cloud_gaming_readiness_test' );
