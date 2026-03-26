<?php
/**
 * Plugin Name: Cloud Games Availability v2
 * Plugin URI: https://example.com/cloud-games-availability
 * Description: Display cloud gaming platform availability with modern card-based UI. Supports GeForce NOW, Xbox Cloud Gaming, PlayStation Cloud, Luna, Boosteroid, Shadow PC, and more.
 * Version: 2.0.0
 * Author: Cloud Games
 * Text Domain: cloud-games-availability-v2
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CGA_VERSION', '2.0.0');
define('CGA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CGA_PLUGIN_URL', plugin_dir_url(__FILE__));

class Cloud_Games_Availability_v2 {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
        $this->init_database();
    }

    private function load_dependencies() {
        require_once CGA_PLUGIN_DIR . 'includes/class-cga-activator.php';
        require_once CGA_PLUGIN_DIR . 'includes/class-cga-deactivator.php';
        require_once CGA_PLUGIN_DIR . 'includes/class-cga-database.php';
        require_once CGA_PLUGIN_DIR . 'includes/class-cga-admin.php';
        require_once CGA_PLUGIN_DIR . 'includes/class-cga-frontend.php';
        require_once CGA_PLUGIN_DIR . 'includes/class-cga-rest-api.php';
        require_once CGA_PLUGIN_DIR . 'includes/class-cga-settings.php';
    }

    private function init_hooks() {
        register_activation_hook(__FILE__, array('CGA_Activator', 'activate'));
        register_deactivation_hook(__FILE__, array('CGA_Deactivator', 'deactivate'));

        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('admin_init', array($this, 'init_database'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));

        add_shortcode('cloud_games_availability', array($this, 'render_shortcode'));
        
        // Gutenberg block support
        add_action('init', array($this, 'register_gutenberg_block'));
    }

    public function load_textdomain() {
        load_plugin_textdomain('cloud-games-availability-v2', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function init_database() {
        $database = new CGA_Database();
        $database->create_tables();
        $database->insert_default_data();
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Cloud Games Availability', 'cloud-games-availability-v2'),
            __('Cloud Games', 'cloud-games-availability-v2'),
            'manage_options',
            'cloud-games-availability',
            array($this, 'render_admin_page'),
            'dashicons-cloud',
            30
        );

        add_submenu_page(
            'cloud-games-availability',
            __('Games', 'cloud-games-availability-v2'),
            __('Games', 'cloud-games-availability-v2'),
            'manage_options',
            'cloud-games-games',
            array($this, 'render_games_page')
        );

        add_submenu_page(
            'cloud-games-availability',
            __('Platforms', 'cloud-games-availability-v2'),
            __('Platforms', 'cloud-games-availability-v2'),
            'manage_options',
            'cloud-games-platforms',
            array($this, 'render_platforms_page')
        );

        add_submenu_page(
            'cloud-games-availability',
            __('Settings', 'cloud-games-availability-v2'),
            __('Settings', 'cloud-games-availability-v2'),
            'manage_options',
            'cloud-games-settings',
            array($this, 'render_settings_page')
        );
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'cloud-games') === false) {
            return;
        }

        wp_enqueue_style(
            'cga-admin-css',
            CGA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            CGA_VERSION
        );

        wp_enqueue_script(
            'cga-admin-js',
            CGA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            CGA_VERSION,
            true
        );

        wp_localize_script('cga-admin-js', 'cgaAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cga_nonce'),
            'strings' => array(
                'confirmDelete' => __('Are you sure you want to delete this item?', 'cloud-games-availability-v2'),
                'saveSuccess' => __('Saved successfully!', 'cloud-games-availability-v2'),
                'error' => __('An error occurred. Please try again.', 'cloud-games-availability-v2'),
                'addPlatform' => __('Add Platform', 'cloud-games-availability-v2'),
                'editPlatform' => __('Edit Platform', 'cloud-games-availability-v2'),
                'addGame' => __('Add Game', 'cloud-games-availability-v2'),
                'editGame' => __('Edit Game', 'cloud-games-availability-v2'),
                'saveGame' => __('Save Game', 'cloud-games-availability-v2'),
                'saveAvailability' => __('Save Availability', 'cloud-games-availability-v2'),
            )
        ));
    }

    public function enqueue_frontend_assets() {
        $settings = get_option('cga_settings', array());
        
        wp_enqueue_style(
            'cga-frontend-css',
            CGA_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            CGA_VERSION
        );

        wp_enqueue_script(
            'cga-frontend-js',
            CGA_PLUGIN_URL . 'assets/js/frontend.js',
            array(),
            CGA_VERSION,
            true
        );

        wp_add_inline_style('cga-frontend-css', $this->get_custom_css());
    }

    private function get_custom_css() {
        $settings = get_option('cga_settings', array());
        $css = '';

        if (!empty($settings['custom_css'])) {
            $css .= $settings['custom_css'];
        }

        return $css;
    }

    public function render_admin_page() {
        include CGA_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }

    public function render_games_page() {
        include CGA_PLUGIN_DIR . 'templates/admin-games.php';
    }

    public function render_platforms_page() {
        include CGA_PLUGIN_DIR . 'templates/admin-platforms.php';
    }

    public function render_settings_page() {
        include CGA_PLUGIN_DIR . 'templates/admin-settings.php';
    }

    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'game_id' => 0,
            'group_by_availability' => get_option('cga_group_by_availability', 'no'),
            'columns' => get_option('cga_default_columns', 3),
        ), $atts, 'cloud_games_availability');

        $frontend = new CGA_Frontend();
        return $frontend->render_cards($atts);
    }

    public function register_gutenberg_block() {
        if (!function_exists('register_block_type')) {
            return;
        }

        wp_register_script(
            'cga-block-js',
            CGA_PLUGIN_URL . 'assets/js/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
            CGA_VERSION,
            true
        );

        wp_register_style(
            'cga-block-css',
            CGA_PLUGIN_URL . 'assets/css/block.css',
            array(),
            CGA_VERSION
        );

        register_block_type('cloud-games-availability/availability-cards', array(
            'editor_script' => 'cga-block-js',
            'editor_style' => 'cga-block-css',
            'style' => 'cga-frontend-css',
            'render_callback' => array($this, 'render_shortcode'),
            'attributes' => array(
                'game_id' => array(
                    'type' => 'number',
                    'default' => 0,
                ),
                'group_by_availability' => array(
                    'type' => 'string',
                    'default' => 'no',
                ),
                'columns' => array(
                    'type' => 'number',
                    'default' => 3,
                ),
            ),
        ));
    }
}

function cga_init() {
    return Cloud_Games_Availability_v2::get_instance();
}

cga_init();
