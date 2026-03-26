<?php

if (!defined('ABSPATH')) {
    exit;
}

class CGA_Admin {

    private $database;

    public function __construct() {
        $this->database = new CGA_Database();
        
        // AJAX handlers
        add_action('wp_ajax_cga_save_platform', array($this, 'ajax_save_platform'));
        add_action('wp_ajax_cga_delete_platform', array($this, 'ajax_delete_platform'));
        add_action('wp_ajax_cga_save_game', array($this, 'ajax_save_game'));
        add_action('wp_ajax_cga_delete_game', array($this, 'ajax_delete_game'));
        add_action('wp_ajax_cga_save_availability', array($this, 'ajax_save_availability'));
        add_action('wp_ajax_cga_save_settings', array($this, 'ajax_save_settings'));
    }

    public function ajax_save_platform() {
        check_ajax_referer('cga_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }

        $data = array(
            'id' => isset($_POST['id']) ? intval($_POST['id']) : 0,
            'name' => sanitize_text_field($_POST['name']),
            'slug' => sanitize_title($_POST['name']),
            'icon_url' => esc_url_raw($_POST['icon_url']),
            'description' => sanitize_textarea_field($_POST['description']),
            'base_price' => !empty($_POST['base_price']) ? floatval($_POST['base_price']) : 0,
            'price_currency' => sanitize_text_field($_POST['price_currency']),
            'price_period' => sanitize_text_field($_POST['price_period']),
            'cta_text' => sanitize_text_field($_POST['cta_text']),
            'cta_url' => esc_url_raw($_POST['cta_url']),
            'tier_name' => sanitize_text_field($_POST['tier_name']),
            'notes' => sanitize_textarea_field($_POST['notes']),
            'status' => sanitize_text_field($_POST['status']),
            'display_order' => intval($_POST['display_order']),
        );

        $id = $this->database->save_platform($data);
        
        wp_send_json_success(array('id' => $id, 'message' => __('Platform saved successfully', 'cloud-games-availability-v2')));
    }

    public function ajax_delete_platform() {
        check_ajax_referer('cga_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }

        $id = intval($_POST['id']);
        $this->database->delete_platform($id);
        
        wp_send_json_success(array('message' => __('Platform deleted successfully', 'cloud-games-availability-v2')));
    }

    public function ajax_save_game() {
        check_ajax_referer('cga_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }

        $data = array(
            'id' => isset($_POST['id']) ? intval($_POST['id']) : 0,
            'name' => sanitize_text_field($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description']),
            'cover_image' => esc_url_raw($_POST['cover_image']),
            'developer' => sanitize_text_field($_POST['developer']),
            'publisher' => sanitize_text_field($_POST['publisher']),
            'release_date' => !empty($_POST['release_date']) ? sanitize_text_field($_POST['release_date']) : null,
            'status' => sanitize_text_field($_POST['status']),
        );

        $id = $this->database->save_game($data);
        
        wp_send_json_success(array('id' => $id, 'message' => __('Game saved successfully', 'cloud-games-availability-v2')));
    }

    public function ajax_delete_game() {
        check_ajax_referer('cga_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }

        $id = intval($_POST['id']);
        $this->database->delete_game($id);
        
        wp_send_json_success(array('message' => __('Game deleted successfully', 'cloud-games-availability-v2')));
    }

    public function ajax_save_availability() {
        check_ajax_referer('cga_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }

        $game_id = intval($_POST['game_id']);
        $platforms_data = isset($_POST['platforms']) ? $_POST['platforms'] : array();
        
        $this->database->save_availability($game_id, $platforms_data);
        
        wp_send_json_success(array('message' => __('Availability saved successfully', 'cloud-games-availability-v2')));
    }

    public function ajax_save_settings() {
        check_ajax_referer('cga_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Permission denied'));
        }

        $settings = array(
            'primary_color' => sanitize_hex_color($_POST['primary_color']),
            'secondary_color' => sanitize_hex_color($_POST['secondary_color']),
            'card_background' => sanitize_text_field($_POST['card_background']),
            'card_background_dark' => sanitize_text_field($_POST['card_background_dark']),
            'button_style' => sanitize_text_field($_POST['button_style']),
            'border_radius' => intval($_POST['border_radius']),
            'card_spacing' => intval($_POST['card_spacing']),
            'show_price' => isset($_POST['show_price']) ? 1 : 0,
            'show_tier' => isset($_POST['show_tier']) ? 1 : 0,
            'show_notes' => isset($_POST['show_notes']) ? 1 : 0,
            'theme_mode' => sanitize_text_field($_POST['theme_mode']),
            'glassmorphism' => isset($_POST['glassmorphism']) ? 1 : 0,
            'hover_effect' => sanitize_text_field($_POST['hover_effect']),
            'custom_css' => wp_kses_post($_POST['custom_css']),
        );

        update_option('cga_settings', $settings);
        update_option('cga_group_by_availability', isset($_POST['group_by_availability']) ? 'yes' : 'no');
        update_option('cga_default_columns', intval($_POST['default_columns']));
        
        wp_send_json_success(array('message' => __('Settings saved successfully', 'cloud-games-availability-v2')));
    }
}
