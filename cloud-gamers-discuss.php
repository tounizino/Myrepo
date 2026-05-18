<?php
/**
 * Plugin Name: CLOUD GAMERS DISCUSS
 * Plugin URI:  https://cloudloadout.com
 * Description: The ultimate modern commenting system for cloud gaming sites.
 * Version:     1.0.0
 * Author:      Cloud Loadout
 * Author URI:  https://cloudloadout.com
 * License:     GPL-3.0-or-later
 * Text Domain: cloud-gamers-discuss
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define Constants
define( 'CGD_VERSION', '1.0.0' );
define( 'CGD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CGD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CGD_BASENAME', plugin_basename( __FILE__ ) );

// Autoloader
if ( file_exists( CGD_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once CGD_PLUGIN_DIR . 'vendor/autoload.php';
} else {
	// Fallback autoloader if composer hasn't been run (though it should be)
	spl_autoload_register( function ( $class ) {
		$prefix = 'CloudGamersDiscuss\\';
		$base_dir = CGD_PLUGIN_DIR . 'src/';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	} );
}

/**
 * Initialize the plugin
 */
function cgd_init() {
	return \CloudGamersDiscuss\Plugin::getInstance();
}

add_action( 'plugins_loaded', 'cgd_init' );

// Activation / Deactivation hooks
register_activation_hook( __FILE__, [ \CloudGamersDiscuss\Plugin::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ \CloudGamersDiscuss\Plugin::class, 'deactivate' ] );
