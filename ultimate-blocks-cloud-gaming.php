<?php
/**
 * Plugin Name: Ultimate Blocks for Cloud Gaming
 * Plugin URI: https://example.com/ultimate-blocks-cloud-gaming
 * Description: The ultimate WordPress plugin with multiple blocks, widgets, and customization options for cloud gaming blogs. Features latest articles grid, category displays, tag filters, and more.
 * Version: 1.0.0
 * Author: Cloud Gaming Pro
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ubcg
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('UBCG_VERSION', '1.0.0');
define('UBCG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UBCG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('UBCG_PLUGIN_BASENAME', plugin_basename(__FILE__));

class Ultimate_Blocks_Cloud_Gaming {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('init', array($this, 'register_blocks'));
        add_action('widgets_init', array($this, 'register_widgets'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }
    
    private function load_dependencies() {
        require_once UBCG_PLUGIN_DIR . 'includes/class-helpers.php';
        require_once UBCG_PLUGIN_DIR . 'includes/class-block-renderer.php';
        require_once UBCG_PLUGIN_DIR . 'includes/class-post-queries.php';
        require_once UBCG_PLUGIN_DIR . 'includes/class-ajax-handlers.php';
        require_once UBCG_PLUGIN_DIR . 'includes/widgets/class-widget-latest-posts.php';
        require_once UBCG_PLUGIN_DIR . 'includes/widgets/class-widget-featured-posts.php';
        require_once UBCG_PLUGIN_DIR . 'includes/widgets/class-widget-category-list.php';
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('ubcg', false, dirname(UBCG_PLUGIN_BASENAME) . '/languages');
    }
    
    public function register_blocks() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        $blocks = array(
            'latest-posts-grid',
            'featured-posts',
            'category-showcase',
            'tag-cloud-gaming',
            'gaming-hero',
            'review-card',
            'game-specs',
            'streaming-platforms',
            'performance-stats',
            'newsletter-signup'
        );
        
        foreach ($blocks as $block) {
            register_block_type(
                UBCG_PLUGIN_DIR . 'blocks/' . $block,
                array(
                    'render_callback' => array('UBCG_Block_Renderer', 'render_' . str_replace('-', '_', $block))
                )
            );
        }
    }
    
    public function register_widgets() {
        register_widget('UBCG_Widget_Latest_Posts');
        register_widget('UBCG_Widget_Featured_Posts');
        register_widget('UBCG_Widget_Category_List');
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('Ultimate Blocks Cloud Gaming', 'ubcg'),
            __('UBCG Settings', 'ubcg'),
            'manage_options',
            'ubcg-settings',
            array($this, 'render_admin_page'),
            'dashicons-grid-view',
            30
        );
    }
    
    public function register_settings() {
        register_setting('ubcg_settings_group', 'ubcg_primary_color', array(
            'type' => 'string',
            'default' => '#2563eb',
            'sanitize_callback' => 'sanitize_hex_color'
        ));
        
        register_setting('ubcg_settings_group', 'ubcg_secondary_color', array(
            'type' => 'string',
            'default' => '#1e40af',
            'sanitize_callback' => 'sanitize_hex_color'
        ));
        
        register_setting('ubcg_settings_group', 'ubcg_accent_color', array(
            'type' => 'string',
            'default' => '#60a5fa',
            'sanitize_callback' => 'sanitize_hex_color'
        ));
        
        register_setting('ubcg_settings_group', 'ubcg_text_color', array(
            'type' => 'string',
            'default' => '#1e293b',
            'sanitize_callback' => 'sanitize_hex_color'
        ));
        
        register_setting('ubcg_settings_group', 'ubcg_posts_per_page', array(
            'type' => 'integer',
            'default' => 9,
            'sanitize_callback' => 'absint'
        ));
        
        register_setting('ubcg_settings_group', 'ubcg_enable_seo', array(
            'type' => 'boolean',
            'default' => true
        ));
    }
    
    public function render_admin_page() {
        include UBCG_PLUGIN_DIR . 'admin/settings-page.php';
    }
    
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'ubcg-frontend-styles',
            UBCG_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            UBCG_VERSION
        );
        
        $custom_css = $this->generate_custom_css();
        wp_add_inline_style('ubcg-frontend-styles', $custom_css);
        
        wp_enqueue_script(
            'ubcg-frontend-scripts',
            UBCG_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            UBCG_VERSION,
            true
        );
        
        wp_localize_script('ubcg-frontend-scripts', 'ubcgData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ubcg_nonce')
        ));
    }
    
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_ubcg-settings' !== $hook) {
            return;
        }
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        wp_enqueue_style(
            'ubcg-admin-styles',
            UBCG_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            UBCG_VERSION
        );
        
        wp_enqueue_script(
            'ubcg-admin-scripts',
            UBCG_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            UBCG_VERSION,
            true
        );
    }
    
    public function enqueue_block_editor_assets() {
        wp_enqueue_style(
            'ubcg-editor-styles',
            UBCG_PLUGIN_URL . 'assets/css/editor.css',
            array('wp-edit-blocks'),
            UBCG_VERSION
        );
        
        wp_enqueue_script(
            'ubcg-blocks',
            UBCG_PLUGIN_URL . 'assets/js/blocks.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-data'),
            UBCG_VERSION,
            true
        );
    }
    
    private function generate_custom_css() {
        $primary_color = get_option('ubcg_primary_color', '#2563eb');
        $secondary_color = get_option('ubcg_secondary_color', '#1e40af');
        $accent_color = get_option('ubcg_accent_color', '#60a5fa');
        $text_color = get_option('ubcg_text_color', '#1e293b');
        
        return "
            :root {
                --ubcg-primary: {$primary_color};
                --ubcg-secondary: {$secondary_color};
                --ubcg-accent: {$accent_color};
                --ubcg-text: {$text_color};
            }
        ";
    }
}

function ubcg_init() {
    return Ultimate_Blocks_Cloud_Gaming::get_instance();
}

ubcg_init();

register_activation_hook(__FILE__, 'ubcg_activate');
function ubcg_activate() {
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'ubcg_deactivate');
function ubcg_deactivate() {
    flush_rewrite_rules();
}
