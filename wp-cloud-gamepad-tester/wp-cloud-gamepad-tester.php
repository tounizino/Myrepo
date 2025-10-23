<?php
/**
 * Plugin Name: Cloud Gamepad Tester WP
 * Description: High-end cloud gaming controller tester with responsive visualization and vibration testing.
 * Version: 1.0.0
 * Author: Cloud Gaming Tools
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * @package CloudGamepadTesterWP
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Cloud_Gamepad_Tester_WP')) {
    final class Cloud_Gamepad_Tester_WP
    {
        private const VERSION = '1.0.0';
        private const HANDLE = 'cloud-gamepad-tester-wp';

        public static function init(): void
        {
            add_action('init', [__CLASS__, 'register_shortcodes']);
            add_action('wp_enqueue_scripts', [__CLASS__, 'register_assets']);
        }

        public static function register_assets(): void
        {
            $base_url = plugin_dir_url(__FILE__);

            wp_register_style(
                self::HANDLE,
                $base_url . 'assets/css/cloud-gamepad-tester.css',
                [],
                self::VERSION
            );

            wp_register_script(
                self::HANDLE,
                $base_url . 'assets/js/cloud-gamepad-tester.js',
                [],
                self::VERSION,
                true
            );
        }

        public static function register_shortcodes(): void
        {
            add_shortcode('cloud_gamepad_tester', [__CLASS__, 'render_shortcode']);
        }

        public static function render_shortcode($atts = []): string
        {
            wp_enqueue_style(self::HANDLE);
            wp_enqueue_script(self::HANDLE);

            $instance_id = uniqid('cgtwp-', false);

            return sprintf(
                '<div id="%1$s" class="cgtwp-root" data-instance="%1$s"></div>',
                esc_attr($instance_id)
            );
        }
    }

    Cloud_Gamepad_Tester_WP::init();
}
