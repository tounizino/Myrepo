<?php

if (!defined('ABSPATH')) {
    exit;
}

class CGA_Settings {

    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        register_setting('cga_settings', 'cga_settings', array(
            'sanitize_callback' => array($this, 'sanitize_settings'),
            'default' => $this->get_default_settings(),
        ));

        register_setting('cga_settings', 'cga_group_by_availability', array(
            'default' => 'no',
        ));

        register_setting('cga_settings', 'cga_default_columns', array(
            'default' => 3,
        ));
    }

    private function get_default_settings() {
        return array(
            'primary_color' => '#6366f1',
            'secondary_color' => '#8b5cf6',
            'card_background' => 'rgba(255, 255, 255, 0.9)',
            'card_background_dark' => 'rgba(30, 30, 30, 0.9)',
            'button_style' => 'filled',
            'border_radius' => 12,
            'card_spacing' => 16,
            'show_price' => true,
            'show_tier' => true,
            'show_notes' => true,
            'theme_mode' => 'light',
            'glassmorphism' => true,
            'hover_effect' => 'lift',
            'custom_css' => '',
        );
    }

    public function sanitize_settings($input) {
        $sanitized = array();
        $defaults = $this->get_default_settings();

        foreach ($defaults as $key => $default) {
            switch ($key) {
                case 'primary_color':
                case 'secondary_color':
                    $sanitized[$key] = sanitize_hex_color($input[$key] ?? $default);
                    break;
                case 'card_background':
                case 'card_background_dark':
                    $sanitized[$key] = sanitize_text_field($input[$key] ?? $default);
                    break;
                case 'button_style':
                case 'theme_mode':
                case 'hover_effect':
                    $sanitized[$key] = sanitize_text_field($input[$key] ?? $default);
                    break;
                case 'border_radius':
                case 'card_spacing':
                    $sanitized[$key] = intval($input[$key] ?? $default);
                    break;
                case 'show_price':
                case 'show_tier':
                case 'show_notes':
                case 'glassmorphism':
                    $sanitized[$key] = isset($input[$key]) ? 1 : 0;
                    break;
                case 'custom_css':
                    $sanitized[$key] = wp_kses_post($input[$key] ?? '');
                    break;
                default:
                    $sanitized[$key] = sanitize_text_field($input[$key] ?? $default);
            }
        }

        return $sanitized;
    }
}
