<?php
/**
 * Admin interface for Sections AutoPosts
 */

if (!defined('ABSPATH')) {
    exit;
}

class SAP_Admin {

    /**
     * @var SAP_Settings
     */
    private $settings;

    public function __construct() {
        $this->settings = SAP_Settings::instance();

        add_action('admin_menu', array($this, 'register_menu')); 
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('sap_settings_group', SAP_Settings::OPTION_KEY, array($this->settings, 'sanitize'));
    }

    /**
     * Register menu entry
     */
    public function register_menu() {
        add_menu_page(
            __('Sections AutoPosts', 'sections-autoposts'),
            __('Sections AutoPosts', 'sections-autoposts'),
            'manage_options',
            'sections-autoposts',
            array($this, 'render_settings_page'),
            'dashicons-layout',
            58
        );
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $sections_config = $this->settings->get_sections_config();
        $settings = $this->settings->get_settings();

        include SAP_PLUGIN_DIR . 'admin/views/settings-page.php';
    }
}

new SAP_Admin();
