<?php
/**
 * Plugin Name: Cloud Gaming Search Engine
 * Plugin URI: https://cloudloadout.com
 * Description: A premium cloud gaming discovery system with Netflix-style UI, RAWG-powered search, and multi-platform cloud availability detection.
 * Version: 1.0.0
 * Author: Cloud Loadout
 * License: GPL v2 or later
 * Text Domain: cloud-gaming-search
 */

defined('ABSPATH') || exit;

define('CGS_VERSION', '1.0.0');
define('CGS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CGS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CGS_CACHE_TTL', HOUR_IN_SECONDS);
define('CGS_TOP100_CACHE_TTL', DAY_IN_SECONDS);

require_once CGS_PLUGIN_DIR . 'includes/class-cache.php';
require_once CGS_PLUGIN_DIR . 'includes/class-rawg.php';
require_once CGS_PLUGIN_DIR . 'includes/class-cloud-engine.php';
require_once CGS_PLUGIN_DIR . 'includes/class-scraper-adapters.php';
require_once CGS_PLUGIN_DIR . 'includes/class-top100.php';
require_once CGS_PLUGIN_DIR . 'includes/class-rest.php';

class CloudGamingSearchEngine {

    private static $instance = null;
    private $rawg;
    private $cloud_engine;
    private $cache;
    private $top100;
    private $rest;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->cache = new CGS_Cache();
        $this->rawg = new CGS_RAWG($this->cache);
        $this->cloud_engine = new CGS_Cloud_Engine($this->cache);
        $this->top100 = new CGS_Top100($this->rawg, $this->cloud_engine, $this->cache);
        $this->rest = new CGS_REST($this->rawg, $this->cloud_engine, $this->top100, $this->cache);

        add_action('rest_api_init', [$this->rest, 'register_routes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_shortcode('cloud_game_search', [$this, 'render_shortcode']);
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_cgs_save_settings', [$this, 'ajax_save_settings']);
        add_action('wp_ajax_cgs_clear_cache', [$this, 'ajax_clear_cache']);
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'cloud-gaming-search',
            CGS_PLUGIN_URL . 'assets/css/style.css',
            [],
            CGS_VERSION
        );

        wp_enqueue_script(
            'cloud-gaming-search',
            CGS_PLUGIN_URL . 'assets/js/app.js',
            [],
            CGS_VERSION,
            true
        );

        wp_localize_script('cloud-gaming-search', 'CGS', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => rest_url('cloud-gaming/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'theme' => get_user_option('cgs_theme', get_current_user_id()) ?: 'dark',
            'strings' => [
                'search_placeholder' => __('Search thousands of cloud games...', 'cloud-gaming-search'),
                'no_results' => __('No games found. Try a different search.', 'cloud-gaming-search'),
                'loading' => __('Loading...', 'cloud-gaming-search'),
                'load_more' => __('Load More Games', 'cloud-gaming-search'),
                'available' => __('Available', 'cloud-gaming-search'),
                'not_available' => __('Not Available', 'cloud-gaming-search'),
                'unknown' => __('Unknown', 'cloud-gaming-search'),
                'top_100_title' => __('Top 100 Cloud Games', 'cloud-gaming-search'),
                'hero_title' => __('Cloud Gaming Search Engine', 'cloud-gaming-search'),
                'hero_subtitle' => __('Discover where to stream any game across every cloud platform', 'cloud-gaming-search'),
            ]
        ]);
    }

    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_cloud-gaming-search' !== $hook) {
            return;
        }
        wp_enqueue_style('cloud-gaming-search-admin', CGS_PLUGIN_URL . 'assets/css/admin.css', [], CGS_VERSION);
        wp_enqueue_script('cloud-gaming-search-admin', CGS_PLUGIN_URL . 'assets/js/admin.js', [], CGS_VERSION, true);
        wp_localize_script('cloud-gaming-search-admin', 'CGS_Admin', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cgs_admin_nonce'),
        ]);
    }

    public function render_shortcode($atts = []) {
        $atts = shortcode_atts([
            'show_top100' => 'true',
            'theme' => 'dark',
        ], $atts);

        wp_enqueue_style('cloud-gaming-search');
        wp_enqueue_script('cloud-gaming-search');

        ob_start();
        ?>
        <div id="cloud-gaming-app" data-theme="<?php echo esc_attr($atts['theme']); ?>" data-show-top100="<?php echo esc_attr($atts['show_top100']); ?>">
            <div class="cgs-skeleton">
                <div class="cgs-skeleton-header"></div>
                <div class="cgs-skeleton-grid">
                    <div class="cgs-skeleton-card"></div>
                    <div class="cgs-skeleton-card"></div>
                    <div class="cgs-skeleton-card"></div>
                    <div class="cgs-skeleton-card"></div>
                    <div class="cgs-skeleton-card"></div>
                    <div class="cgs-skeleton-card"></div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function register_admin_menu() {
        add_menu_page(
            __('Cloud Gaming Search', 'cloud-gaming-search'),
            __('Cloud Gaming', 'cloud-gaming-search'),
            'manage_options',
            'cloud-gaming-search',
            [$this, 'render_admin_page'],
            'dashicons-cloud',
            30
        );
    }

    public function render_admin_page() {
        $rawg_key = get_option('cgs_rawg_api_key', '');
        $cache_ttl = get_option('cgs_cache_ttl', CGS_CACHE_TTL);
        $enabled_platforms = get_option('cgs_enabled_platforms', [
            'geforce_now' => true,
            'xbox_cloud' => true,
            'ps_plus_cloud' => true,
            'amazon_luna' => true,
            'boosteroid' => true,
            'shadow_pc' => true,
        ]);
        ?>
        <div class="wrap cgs-admin">
            <h1><?php _e('Cloud Gaming Search Engine Settings', 'cloud-gaming-search'); ?></h1>
            <form id="cgs-settings-form" method="post">
                <?php wp_nonce_field('cgs_admin_nonce', 'cgs_nonce'); ?>
                
                <div class="cgs-admin-section">
                    <h2><?php _e('RAWG API Configuration', 'cloud-gaming-search'); ?></h2>
                    <p><?php _e('Enter your RAWG API key. Get one at https://rawg.io/apidocs', 'cloud-gaming-search'); ?></p>
                    <input type="text" id="cgs_rawg_api_key" name="cgs_rawg_api_key" 
                           value="<?php echo esc_attr($rawg_key); ?>" class="regular-text" />
                </div>

                <div class="cgs-admin-section">
                    <h2><?php _e('Cache Settings', 'cloud-gaming-search'); ?></h2>
                    <p><?php _e('Cache TTL in seconds (default: 3600)', 'cloud-gaming-search'); ?></p>
                    <input type="number" id="cgs_cache_ttl" name="cgs_cache_ttl" 
                           value="<?php echo esc_attr($cache_ttl); ?>" min="60" max="86400" />
                    <button type="button" id="cgs-clear-cache" class="button"><?php _e('Clear Cache', 'cloud-gaming-search'); ?></button>
                </div>

                <div class="cgs-admin-section">
                    <h2><?php _e('Cloud Platforms', 'cloud-gaming-search'); ?></h2>
                    <p><?php _e('Enable or disable cloud platforms', 'cloud-gaming-search'); ?></p>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Platform', 'cloud-gaming-search'); ?></th>
                                <th><?php _e('Enabled', 'cloud-gaming-search'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $platforms = [
                                'geforce_now' => 'GeForce NOW',
                                'xbox_cloud' => 'Xbox Cloud Gaming',
                                'ps_plus_cloud' => 'PlayStation Plus Cloud',
                                'amazon_luna' => 'Amazon Luna',
                                'boosteroid' => 'Boosteroid',
                                'shadow_pc' => 'Shadow PC'
                            ];
                            foreach ($platforms as $key => $label): 
                                $checked = isset($enabled_platforms[$key]) ? $enabled_platforms[$key] : true;
                            ?>
                            <tr>
                                <td><?php echo esc_html($label); ?></td>
                                <td>
                                    <input type="checkbox" name="enabled_platforms[<?php echo esc_attr($key); ?>]" 
                                           value="1" <?php checked($checked, true); ?> />
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cgs-admin-section">
                    <h2><?php _e('Shortcode', 'cloud-gaming-search'); ?></h2>
                    <p><?php _e('Use this shortcode on any page or post:', 'cloud-gaming-search'); ?></p>
                    <code>[cloud_game_search]</code>
                    <p><?php _e('With options:', 'cloud-gaming-search'); ?></p>
                    <code>[cloud_game_search show_top100="true" theme="dark"]</code>
                </div>

                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Save Settings', 'cloud-gaming-search'); ?></button>
                </p>
            </form>
        </div>
        <?php
    }

    public function ajax_save_settings() {
        if (!wp_verify_nonce($_POST['cgs_nonce'], 'cgs_admin_nonce') || !current_user_can('manage_options')) {
            wp_die(-1);
        }

        if (isset($_POST['cgs_rawg_api_key'])) {
            update_option('cgs_rawg_api_key', sanitize_text_field($_POST['cgs_rawg_api_key']));
        }
        if (isset($_POST['cgs_cache_ttl'])) {
            update_option('cgs_cache_ttl', intval($_POST['cgs_cache_ttl']));
        }
        if (isset($_POST['enabled_platforms'])) {
            update_option('cgs_enabled_platforms', array_map('boolval', $_POST['enabled_platforms']));
        }

        wp_send_json_success(['message' => __('Settings saved.', 'cloud-gaming-search')]);
    }

    public function ajax_clear_cache() {
        if (!wp_verify_nonce($_POST['cgs_nonce'], 'cgs_admin_nonce') || !current_user_can('manage_options')) {
            wp_die(-1);
        }
        $this->cache->clear_all();
        wp_send_json_success(['message' => __('Cache cleared.', 'cloud-gaming-search')]);
    }
}

function CGS() {
    return CloudGamingSearchEngine::instance();
}

add_action('plugins_loaded', 'CGS');

register_activation_hook(__FILE__, function () {
    if (!get_option('cgs_enabled_platforms')) {
        update_option('cgs_enabled_platforms', [
            'geforce_now' => true,
            'xbox_cloud' => true,
            'ps_plus_cloud' => true,
            'amazon_luna' => true,
            'boosteroid' => true,
            'shadow_pc' => true,
        ]);
    }
    if (!get_option('cgs_cache_ttl')) {
        update_option('cgs_cache_ttl', CGS_CACHE_TTL);
    }
});