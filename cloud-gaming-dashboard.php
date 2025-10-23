<?php
/**
 * Plugin Name: Cloud Gaming Status & Launcher Dashboard
 * Plugin URI: https://cloudloadout.com
 * Description: Premium cloud gaming status dashboard with service status monitoring and quick launcher for GeForce NOW, Xbox Cloud Gaming, Boosteroid, Shadow, and more. Perfect for cloud gaming enthusiasts!
 * Version: 1.0.0
 * Author: CloudLoadout
 * Author URI: https://cloudloadout.com
 * License: GPL2
 * Text Domain: cloud-gaming-dashboard
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class CloudGamingDashboard {
    
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new CloudGamingDashboard();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'registerShortcodes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueAssets'));
        add_action('wp_ajax_check_service_status', array($this, 'checkServiceStatus'));
        add_action('wp_ajax_nopriv_check_service_status', array($this, 'checkServiceStatus'));
    }
    
    public function registerShortcodes() {
        add_shortcode('cloud_gaming_dashboard', array($this, 'renderDashboard'));
        add_shortcode('cloud_gaming_launcher', array($this, 'renderLauncher'));
        add_shortcode('cloud_gaming_combined', array($this, 'renderCombined'));
    }
    
    public function enqueueAssets() {
        if (is_singular() || is_page()) {
            wp_enqueue_style(
                'cloud-gaming-dashboard-css',
                plugin_dir_url(__FILE__) . 'assets/css/cloud-gaming-dashboard.css',
                array(),
                '1.0.0'
            );
            
            wp_enqueue_script(
                'cloud-gaming-dashboard-js',
                plugin_dir_url(__FILE__) . 'assets/js/cloud-gaming-dashboard.js',
                array(),
                '1.0.0',
                true
            );
            
            wp_localize_script('cloud-gaming-dashboard-js', 'cloudGamingAjax', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('cloud-gaming-nonce')
            ));
        }
    }
    
    public function renderDashboard($atts) {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/dashboard.php';
        return ob_get_clean();
    }
    
    public function renderLauncher($atts) {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/launcher.php';
        return ob_get_clean();
    }
    
    public function renderCombined($atts) {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/combined.php';
        return ob_get_clean();
    }
    
    public function checkServiceStatus() {
        check_ajax_referer('cloud-gaming-nonce', 'nonce');
        
        $service = sanitize_text_field($_POST['service']);
        $status = $this->fetchServiceStatus($service);
        
        wp_send_json_success($status);
    }
    
    private function fetchServiceStatus($service) {
        // This is a simplified status check
        // In production, you'd implement actual API calls or status page scraping
        $statuses = array(
            'geforce-now' => array('url' => 'https://status.nvidia.com/', 'status' => 'online'),
            'xbox-cloud' => array('url' => 'https://support.xbox.com/xbox-live-status', 'status' => 'online'),
            'boosteroid' => array('url' => 'https://boosteroid.com/', 'status' => 'online'),
            'shadow' => array('url' => 'https://status.shadow.tech/', 'status' => 'online'),
            'amazon-luna' => array('url' => 'https://www.amazon.com/luna', 'status' => 'online'),
            'playstation-plus' => array('url' => 'https://status.playstation.com/', 'status' => 'online'),
        );
        
        return isset($statuses[$service]) ? $statuses[$service] : array('status' => 'unknown');
    }
}

// Initialize the plugin
CloudGamingDashboard::getInstance();
