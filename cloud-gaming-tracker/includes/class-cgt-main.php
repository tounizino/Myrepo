<?php
/**
 * Main Plugin Class
 * 
 * @package Cloud_Gaming_Tracker
 */

if (!defined('ABSPATH')) exit;

final class Cloud_Gaming_Tracker {
    
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize all hooks
     */
    private function init_hooks() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize components
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'register_block'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // Initialize admin
        if (is_admin()) {
            CGT_Admin::init();
        }
        
        // Shortcode
        add_shortcode('cloud_gaming_tracker', array($this, 'render_shortcode'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        CGT_Database::create_tables();
        CGT_Database::insert_default_platforms();
        
        $default_settings = array(
            'theme' => 'auto',
            'primary_color' => '#3b82f6',
            'success_color' => '#10b981',
            'danger_color' => '#ef4444',
            'card_radius' => '6',
            'button_radius' => '4',
            'spacing' => '16',
            'show_price' => true,
            'show_tier' => true,
            'group_platforms' => true,
            'glassmorphism' => true,
        );
        
        if (!get_option('cgt_settings')) {
            add_option('cgt_settings', $default_settings);
        }
        
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('cloud-gaming-tracker', false, dirname(CGT_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        CGT_Frontend::enqueue_assets();
    }
    
    /**
     * Render shortcode
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'game_id' => 0,
            'game_slug' => '',
            'show_unavailable' => 'true',
            'group_by' => 'availability',
        ), $atts, 'cloud_gaming_tracker');
        return CGT_Frontend::render_platforms($atts);
    }
    
    /**
     * Register Gutenberg block
     */
    public function register_block() {
        if (!function_exists('register_block_type')) return;
        register_block_type('cgt/platforms', array(
            'editor_script' => 'cgt-block-js',
            'editor_style' => 'cgt-frontend-css',
            'style' => 'cgt-frontend-css',
            'script' => 'cgt-frontend-js',
            'attributes' => array(
                'gameId' => array('type' => 'number', 'default' => 0),
                'gameSlug' => array('type' => 'string', 'default' => ''),
                'showUnavailable' => array('type' => 'boolean', 'default' => true),
                'groupBy' => array('type' => 'string', 'default' => 'availability'),
            ),
            'render_callback' => array($this, 'render_block'),
        ));
    }
    
    /**
     * Render block
     */
    public function render_block($attributes) {
        $atts = array(
            'game_id' => $attributes['gameId'] ?? 0,
            'game_slug' => $attributes['gameSlug'] ?? '',
            'show_unavailable' => $attributes['showUnavailable'] ? 'true' : 'false',
            'group_by' => $attributes['groupBy'] ?? 'availability',
        );
        return $this->render_shortcode($atts);
    }
}
