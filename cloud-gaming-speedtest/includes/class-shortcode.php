<?php
/**
 * Shortcode Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class CGST_Shortcode {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('cloud_gaming_speedtest', array($this, 'render_shortcode'));
    }
    
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'theme' => get_option('cgst_color_theme', 'dark')
        ), $atts, 'cloud_gaming_speedtest');
        
        $theme = sanitize_key($atts['theme']);
        if (!in_array($theme, array('dark', 'sky', 'light'), true)) {
            $theme = 'dark';
        }
        
        $intro = get_option('cgst_intro_text', __('Run the full cloud gaming connectivity check to see if your network can keep up with ultra responsive streaming.', 'cloud-gaming-speedtest'));
        $footer = get_option('cgst_footer_note', __('Tip: For the most accurate results, test on a wired connection and close bandwidth-heavy applications.', 'cloud-gaming-speedtest'));
        $resources = get_option('cgst_custom_resources', CGST_Speed_Test::get_default_resources());
        $quality_levels = CGST_Speed_Test::get_quality_levels();
        
        ob_start();
        include CGST_PLUGIN_DIR . 'templates/speedtest-display.php';
        return ob_get_clean();
    }
}
