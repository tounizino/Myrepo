<?php
/**
 * Plugin Name: Amazon Affiliate Product Displays Pro
 * Plugin URI: https://example.com/amazon-affiliate-product-displays-pro
 * Description: Modern, responsive Amazon affiliate product displays powered by the PA-API 5.0.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: amazon-affiliate-displays
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AAPDP_VERSION', '1.0.0');
define('AAPDP_PLUGIN_FILE', __FILE__);
define('AAPDP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AAPDP_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once AAPDP_PLUGIN_DIR . 'includes/class-aapd-plugin.php';

register_activation_hook(__FILE__, array('AAPD_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('AAPD_Plugin', 'deactivate'));

function aapd() {
    return AAPD_Plugin::instance();
}

aapd();
