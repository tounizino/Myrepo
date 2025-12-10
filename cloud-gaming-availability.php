<?php
/**
 * Plugin Name: Cloud Gaming Availability
 * Plugin URI: https://example.com/cloud-gaming-availability
 * Description: Display game availability across 9 cloud gaming platforms with customizable themes and responsive design.
 * Version: 1.0.0
 * Author: Cloud Gaming
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-gaming-availability
 * Domain Path: /languages
 *
 * @package CloudGamingAvailability
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants
define( 'CGA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CGA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CGA_PLUGIN_VERSION', '1.0.0' );

// Include necessary files
require_once CGA_PLUGIN_PATH . 'includes/class-cga-loader.php';
require_once CGA_PLUGIN_PATH . 'includes/class-cga-cpt.php';
require_once CGA_PLUGIN_PATH . 'includes/class-cga-settings.php';
require_once CGA_PLUGIN_PATH . 'admin/class-cga-admin.php';
require_once CGA_PLUGIN_PATH . 'frontend/class-cga-shortcode.php';
require_once CGA_PLUGIN_PATH . 'includes/functions.php';

/**
 * Main plugin class
 */
class CloudGamingAvailability {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        load_plugin_textdomain( 'cloud-gaming-availability', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

        // Initialize classes
        new CGA_Loader();
        new CGA_CPT();
        new CGA_Settings();
        new CGA_Admin();
        new CGA_Shortcode();
    }

    /**
     * Activate the plugin
     */
    public function activate() {
        // Register CPT and flush rewrite rules
        $cpt = new CGA_CPT();
        $cpt->register();
        flush_rewrite_rules();

        // Create default settings
        if ( ! get_option( 'cga_settings' ) ) {
            $default_settings = array(
                'theme'           => 'light',
                'button_color'    => '#007cba',
                'text_color_light'  => '#000000',
                'text_color_dark'   => '#ffffff',
                'border_radius'   => '4',
                'spacing'         => '12',
                'padding'         => '8',
                'logo_size'       => '48',
                'enable_filters'  => '1',
            );
            update_option( 'cga_settings', $default_settings );
        }
    }

    /**
     * Deactivate the plugin
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
new CloudGamingAvailability();
