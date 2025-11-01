<?php
/**
 * Admin Settings Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UNPC_Admin_Settings {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('NAT & Port Checker', 'ultimate-nat-port-checker'),
            __('NAT & Port Checker', 'ultimate-nat-port-checker'),
            'manage_options',
            'ultimate-nat-port-checker',
            array($this, 'render_settings_page'),
            'dashicons-networking',
            30
        );
    }
    
    public function register_settings() {
        // General Settings
        register_setting('unpc_general_settings', 'unpc_default_theme');
        register_setting('unpc_general_settings', 'unpc_font_size');
        register_setting('unpc_general_settings', 'unpc_container_width');
        register_setting('unpc_general_settings', 'unpc_enable_animations');
        
        // Display Settings
        register_setting('unpc_display_settings', 'unpc_show_device_info');
        register_setting('unpc_display_settings', 'unpc_show_router_logins');
        register_setting('unpc_display_settings', 'unpc_show_guides');
        register_setting('unpc_display_settings', 'unpc_show_security_note');
        register_setting('unpc_display_settings', 'unpc_show_footer');
        
        // NAT Checker Settings
        register_setting('unpc_nat_settings', 'unpc_nat_stun_servers');
        register_setting('unpc_nat_settings', 'unpc_nat_timeout');
        register_setting('unpc_nat_settings', 'unpc_show_technical_log');
        
        // Port Checker Settings
        register_setting('unpc_port_settings', 'unpc_port_timeout');
        register_setting('unpc_port_settings', 'unpc_max_ports_check');
        register_setting('unpc_port_settings', 'unpc_custom_presets');
        
        // Colors Settings
        register_setting('unpc_color_settings', 'unpc_color_primary');
        register_setting('unpc_color_settings', 'unpc_color_success');
        register_setting('unpc_color_settings', 'unpc_color_warning');
        register_setting('unpc_color_settings', 'unpc_color_error');
        register_setting('unpc_color_settings', 'unpc_color_info');
        
        // Advanced Settings
        register_setting('unpc_advanced_settings', 'unpc_custom_css');
        register_setting('unpc_advanced_settings', 'unpc_custom_js');
        register_setting('unpc_advanced_settings', 'unpc_enable_cache');
        register_setting('unpc_advanced_settings', 'unpc_cache_duration');
    }
    
    public static function get_defaults() {
        return array(
            'unpc_default_theme' => 'dark',
            'unpc_font_size' => 'medium',
            'unpc_container_width' => '1200',
            'unpc_enable_animations' => '1',
            'unpc_show_device_info' => '1',
            'unpc_show_router_logins' => '1',
            'unpc_show_guides' => '1',
            'unpc_show_security_note' => '1',
            'unpc_show_footer' => '1',
            'unpc_nat_stun_servers' => 'stun:stun.l.google.com:19302',
            'unpc_nat_timeout' => '5000',
            'unpc_show_technical_log' => '1',
            'unpc_port_timeout' => '3000',
            'unpc_max_ports_check' => '50',
            'unpc_custom_presets' => '',
            'unpc_color_primary' => '#0f3460',
            'unpc_color_success' => '#10b981',
            'unpc_color_warning' => '#f59e0b',
            'unpc_color_error' => '#ef4444',
            'unpc_color_info' => '#3b82f6',
            'unpc_custom_css' => '',
            'unpc_custom_js' => '',
            'unpc_enable_cache' => '0',
            'unpc_cache_duration' => '300'
        );
    }
    
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Handle form submission
        if (isset($_POST['unpc_settings_submit'])) {
            check_admin_referer('unpc_settings_nonce');
            $this->save_settings();
            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'ultimate-nat-port-checker') . '</p></div>';
        }
        
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        
        if (isset($_POST['active_tab'])) {
            $active_tab = sanitize_text_field(wp_unslash($_POST['active_tab']));
        }
        
        include UNPC_PLUGIN_DIR . 'templates/admin-page.php';
    }
    
    private function save_settings() {
        $settings = array(
            'unpc_default_theme',
            'unpc_font_size',
            'unpc_container_width',
            'unpc_enable_animations',
            'unpc_show_device_info',
            'unpc_show_router_logins',
            'unpc_show_guides',
            'unpc_show_security_note',
            'unpc_show_footer',
            'unpc_nat_stun_servers',
            'unpc_nat_timeout',
            'unpc_show_technical_log',
            'unpc_port_timeout',
            'unpc_max_ports_check',
            'unpc_custom_presets',
            'unpc_color_primary',
            'unpc_color_success',
            'unpc_color_warning',
            'unpc_color_error',
            'unpc_color_info',
            'unpc_custom_css',
            'unpc_custom_js',
            'unpc_enable_cache',
            'unpc_cache_duration'
        );

        $textarea_settings = array(
            'unpc_nat_stun_servers',
            'unpc_custom_presets'
        );

        foreach ($settings as $setting) {
            if (isset($_POST[$setting])) {
                $value = wp_unslash($_POST[$setting]);

                if ($setting === 'unpc_custom_css' || $setting === 'unpc_custom_js') {
                    $value = wp_kses_post($value);
                } elseif (in_array($setting, $textarea_settings, true)) {
                    $value = sanitize_textarea_field($value);
                } else {
                    $value = sanitize_text_field($value);
                }

                update_option($setting, $value);
            } else {
                // For checkboxes
                update_option($setting, '0');
            }
        }
    }
}
