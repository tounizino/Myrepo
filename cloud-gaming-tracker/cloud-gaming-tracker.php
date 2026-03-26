<?php
/**
 * Plugin Name: Cloud Gaming Tracker
 * Plugin URI: https://cloudloadout.com/cloud-gaming-tracker
 * Description: Track and display cloud gaming platform availability for your games. Modern card-based UI with full admin control.
 * Version: 3.1.0
 * Author: Cloud Loadout
 * Author URI: https://cloudloadout.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-gaming-tracker
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

define('CGT_VERSION', '3.1.0');
define('CGT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CGT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CGT_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once CGT_PLUGIN_DIR . 'includes/class-cgt-database.php';
require_once CGT_PLUGIN_DIR . 'includes/class-cgt-frontend.php';
require_once CGT_PLUGIN_DIR . 'includes/class-cgt-admin.php';
require_once CGT_PLUGIN_DIR . 'includes/class-cgt-main.php';

/**
 * Initialize Plugin
 */
function cgt_init() {
    return Cloud_Gaming_Tracker::get_instance();
}
cgt_init();
