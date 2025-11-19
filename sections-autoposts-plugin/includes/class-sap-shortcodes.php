<?php
/**
 * Shortcode Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class SAP_Shortcodes {
    
    public function __construct() {
        add_shortcode('sap_section', array($this, 'render_section'));
        add_shortcode('sap_all_sections', array($this, 'render_all_sections'));
    }
    
    /**
     * Render single section via shortcode
     * Usage: [sap_section type="featured"]
     */
    public function render_section($atts) {
        $atts = shortcode_atts(array(
            'type' => 'featured',
        ), $atts);
        
        $section_type = sanitize_key($atts['type']);
        $settings = SAP_Settings::instance();
        $section = $settings->get_section($section_type);
        
        if (empty($section) || !$section['enabled']) {
            return '';
        }
        
        ob_start();
        SAP_Templates::render($section_type, $section);
        return ob_get_clean();
    }
    
    /**
     * Render all enabled sections in order
     * Usage: [sap_all_sections]
     */
    public function render_all_sections($atts) {
        $settings = SAP_Settings::instance();
        $all_settings = $settings->get_settings();
        
        // Sort by order
        uasort($all_settings, function($a, $b) {
            $order_a = isset($a['order']) ? $a['order'] : 999;
            $order_b = isset($b['order']) ? $b['order'] : 999;
            return $order_a - $order_b;
        });
        
        ob_start();
        
        foreach ($all_settings as $key => $section) {
            if (!empty($section['enabled'])) {
                SAP_Templates::render($key, $section);
            }
        }
        
        return ob_get_clean();
    }
}

new SAP_Shortcodes();
