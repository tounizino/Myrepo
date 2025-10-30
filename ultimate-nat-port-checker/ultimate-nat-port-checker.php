<?php
/**
 * Plugin Name: Ultimate NAT & Port Checker
 * Plugin URI: https://example.com/ultimate-nat-port-checker
 * Description: The ultimate NAT and Port checking tool for cloud gaming with comprehensive network diagnostics, device info, and router configuration guides.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ultimate-nat-port-checker
 */

if (!defined('ABSPATH')) {
    exit;
}

define('UNPC_VERSION', '1.0.0');
define('UNPC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UNPC_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Retrieve the plugin default settings.
 *
 * @return array
 */
function unpc_default_settings() {
    return array(
        'theme' => 'light',
        'accent_color' => '#3a7afe',
        'enable_nat_checker' => true,
        'enable_port_checker' => true,
        'enable_router_logins' => true,
        'enable_device_info' => true,
        'enable_useful_links' => true,
        'enable_guides' => true,
        'useful_link_1_text' => __('Advanced Router Configuration Guide', 'ultimate-nat-port-checker'),
        'useful_link_1_url' => 'https://example.com/advanced-router-configuration',
        'useful_link_2_text' => __('Cloud Gaming Optimization Tips', 'ultimate-nat-port-checker'),
        'useful_link_2_url' => 'https://example.com/cloud-gaming-optimization',
        'animation_speed' => 'normal',
        'enable_animations' => true,
        'container_width' => '1400',
        'api_timeout' => '10',
        'custom_quick_tip' => __('Remember to reboot your router after applying configuration changes to ensure everything takes effect.', 'ultimate-nat-port-checker'),
    );
}

/**
 * Retrieve merged plugin settings (saved settings + defaults).
 *
 * @return array
 */
function unpc_get_settings() {
    $defaults = unpc_default_settings();
    $settings = get_option('unpc_settings', array());

    return wp_parse_args($settings, $defaults);
}

require_once UNPC_PLUGIN_DIR . 'includes/class-unpc-core.php';
require_once UNPC_PLUGIN_DIR . 'includes/class-unpc-admin.php';
require_once UNPC_PLUGIN_DIR . 'includes/class-unpc-ajax.php';

/**
 * Bootstrap the plugin.
 *
 * @return void
 */
function unpc_init() {
    new UNPC_Core();
    new UNPC_Admin();
    new UNPC_Ajax();
}
add_action('plugins_loaded', 'unpc_init');

/**
 * Plugin activation callback.
 *
 * @return void
 */
function unpc_activate() {
    $settings = get_option('unpc_settings');

    if (false === $settings) {
        add_option('unpc_settings', unpc_default_settings());
    } else {
        update_option('unpc_settings', wp_parse_args($settings, unpc_default_settings()));
    }
}
register_activation_hook(__FILE__, 'unpc_activate');

/**
 * Render shortcode output.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function unpc_render_checker($atts) {
    ob_start();
    include UNPC_PLUGIN_DIR . 'includes/template-checker.php';
    return ob_get_clean();
}
add_shortcode('nat_port_checker', 'unpc_render_checker');
