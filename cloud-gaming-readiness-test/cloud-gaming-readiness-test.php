<?php
/**
 * Plugin Name: Cloud Gaming Readiness Test
 * Plugin URI: https://example.com/cloud-gaming-readiness-test
 * Description: Professional network diagnostics and Cloudflare speed test for cloud gaming readiness assessment with modern 2026 UI/UX
 * Version: 1.0.0
 * Author: Cloud Gaming Tools
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: cloud-gaming-readiness-test
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package Cloud_Gaming_Readiness_Test
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define plugin constants.
define( 'CGRT_VERSION', '1.0.0' );
define( 'CGRT_PLUGIN_NAME', 'cloud-gaming-readiness-test' );
define( 'CGRT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CGRT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CGRT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Require the autoloader.
require_once CGRT_PLUGIN_DIR . 'includes/class-cgrt-autoloader.php';

// Initialize the autoloader.
CGRT_Autoloader::register();

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run_cloud_gaming_readiness_test() {
    $plugin = new CGRT_Plugin();
    $plugin->run();
}

// Initialize the plugin.
run_cloud_gaming_readiness_test();
