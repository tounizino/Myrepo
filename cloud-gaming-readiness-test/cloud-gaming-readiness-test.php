<?php
/**
 * Plugin Name: Cloud Gaming Readiness Test
 * Plugin URI: https://cloudgamingreadiness.com
 * Description: Professional cloud gaming network diagnostic system. Provides real-time analysis of latency, jitter, packet loss, and connection stability to assess readiness for cloud gaming platforms.
 * Version: 1.0.0
 * Author: Cloud Gaming Readiness Team
 * Author URI: https://cloudgamingreadiness.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-gaming-readiness-test
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * @package CloudGamingReadinessTest
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'CGRT_VERSION', '1.0.0' );
define( 'CGRT_PLUGIN_FILE', __FILE__ );
define( 'CGRT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CGRT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CGRT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main plugin class.
 */
final class Cloud_Gaming_Readiness_Test {

    /**
     * Single instance of the class.
     *
     * @var Cloud_Gaming_Readiness_Test
     */
    private static $instance = null;

    /**
     * Get the single instance of the class.
     *
     * @return Cloud_Gaming_Readiness_Test
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files.
     */
    private function includes() {
        require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-database.php';
        require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-admin.php';
        require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-frontend.php';
        require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-api.php';
        require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-test-engine.php';
        require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-score-calculator.php';
        require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-block.php';
        require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-network-tester.php';
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        register_activation_hook( CGRT_PLUGIN_FILE, array( $this, 'activate' ) );
        register_deactivation_hook( CGRT_PLUGIN_FILE, array( $this, 'deactivate' ) );
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Initialize plugin components.
     */
    public function init() {
        // Initialize components.
        CGRT_Database::instance();
        CGRT_Admin::instance();
        CGRT_Frontend::instance();
        CGRT_API::instance();
        CGRT_Block::instance();

        // Load textdomain.
        load_plugin_textdomain( 'cloud-gaming-readiness-test', false, dirname( CGRT_PLUGIN_BASENAME ) . '/languages' );
    }

    /**
     * Plugin activation.
     */
    public function activate() {
        CGRT_Database::create_tables();
        CGRT_Database::insert_default_data();
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation.
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

/**
 * Returns the main instance of Cloud_Gaming_Readiness_Test.
 *
 * @return Cloud_Gaming_Readiness_Test
 */
function cgrt() {
    return Cloud_Gaming_Readiness_Test::instance();
}

// Initialize the plugin.
cgrt();
