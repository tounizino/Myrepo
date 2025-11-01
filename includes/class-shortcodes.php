<?php
/**
 * Shortcodes Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UNPC_Shortcodes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('ultimate_nat_port_checker', array($this, 'render_full_tool'));
        add_shortcode('ultimate_nat_checker', array($this, 'render_nat_checker'));
        add_shortcode('ultimate_port_checker', array($this, 'render_port_checker'));
    }
    
    private function get_settings() {
        $defaults = UNPC_Admin_Settings::get_defaults();
        return array(
            'theme' => get_option('unpc_default_theme', $defaults['unpc_default_theme']),
            'fontSize' => get_option('unpc_font_size', $defaults['unpc_font_size']),
            'containerWidth' => get_option('unpc_container_width', $defaults['unpc_container_width']),
            'showDeviceInfo' => get_option('unpc_show_device_info', $defaults['unpc_show_device_info']),
            'showRouterLogins' => get_option('unpc_show_router_logins', $defaults['unpc_show_router_logins']),
            'showGuides' => get_option('unpc_show_guides', $defaults['unpc_show_guides']),
            'showSecurityNote' => get_option('unpc_show_security_note', $defaults['unpc_show_security_note']),
            'showFooter' => get_option('unpc_show_footer', $defaults['unpc_show_footer']),
            'showTechnicalLog' => get_option('unpc_show_technical_log', $defaults['unpc_show_technical_log']),
            'enableAnimations' => get_option('unpc_enable_animations', $defaults['unpc_enable_animations']),
            'colorPrimary' => get_option('unpc_color_primary', $defaults['unpc_color_primary']),
            'colorSuccess' => get_option('unpc_color_success', $defaults['unpc_color_success']),
            'colorWarning' => get_option('unpc_color_warning', $defaults['unpc_color_warning']),
            'colorError' => get_option('unpc_color_error', $defaults['unpc_color_error']),
            'colorInfo' => get_option('unpc_color_info', $defaults['unpc_color_info']),
            'customCSS' => get_option('unpc_custom_css', ''),
        );
    }
    
    public function render_full_tool($atts, $content = null) {
        $settings = $this->get_settings();
        ob_start();
        include UNPC_PLUGIN_DIR . 'templates/full-tool.php';
        return ob_get_clean();
    }
    
    public function render_nat_checker($atts, $content = null) {
        $settings = $this->get_settings();
        ob_start();
        include UNPC_PLUGIN_DIR . 'templates/nat-checker.php';
        return ob_get_clean();
    }
    
    public function render_port_checker($atts, $content = null) {
        $settings = $this->get_settings();
        ob_start();
        include UNPC_PLUGIN_DIR . 'templates/port-checker.php';
        return ob_get_clean();
    }
}
