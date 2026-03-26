<?php

if (!defined('ABSPATH')) {
    exit;
}

class CGA_Activator {

    public static function activate() {
        $database = new CGA_Database();
        $database->create_tables();
        $database->insert_default_data();

        // Set default options
        add_option('cga_version', CGA_VERSION);
        add_option('cga_group_by_availability', 'no');
        add_option('cga_default_columns', 3);
        
        $default_settings = array(
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
        );
        
        add_option('cga_settings', $default_settings);

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
