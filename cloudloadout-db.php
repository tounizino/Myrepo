<?php
/**
 * Plugin Name: CloudLoadout DB
 * Plugin URI: https://cloudloadout.com
 * Description: Enterprise-grade cloud gaming database engine for WordPress.
 * Version: 1.0.0
 * Author: CloudLoadout
 * Text Domain: cloudloadout-db
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('CL_DB_VERSION', '1.0.0');
define('CL_DB_PATH', plugin_dir_path(__FILE__));
define('CL_DB_URL', plugin_dir_url(__FILE__));

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'CloudLoadout\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $path_parts = explode('\\', strtolower($relative_class));
    
    // Mapping namespaces to directories
    $map = [
        'scrapers' => 'scrapers/',
        'api' => 'api/',
        'admin' => 'admin/',
        'matchengine' => 'match-engine/',
        'syncengine' => 'sync-engine/',
    ];

    $filename = end($path_parts) . '.php';
    $sub_path = count($path_parts) > 1 ? $path_parts[0] : '';
    
    $base_dir = CL_DB_PATH;
    if (isset($map[$sub_path])) {
        $file = $base_dir . $map[$sub_path] . $filename;
    } elseif (isset($map[strtolower($relative_class)])) {
        $file = $base_dir . $map[strtolower($relative_class)] . $filename;
    } else {
        $file = $base_dir . 'includes/' . $filename;
    }

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize Database
register_activation_hook(__FILE__, ['CloudLoadout\Database', 'install']);

// Main Plugin Class
class CloudLoadoutDB {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init() {
        // Initialize components
        add_action('init', [$this, 'init_components']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    public function init_components() {
        \CloudLoadout\API::get_instance();
        \CloudLoadout\MatchEngine::get_instance();
        \CloudLoadout\SyncEngine::get_instance();
        \CloudLoadout\SEO::get_instance();
        \CloudLoadout\Shortcodes::get_instance();
        
        // Register custom rewrites for /g/ slug
        $this->register_rewrites();
    }

    private function register_rewrites() {
        add_rewrite_rule('^g/([^/]+)/?$', 'index.php?cl_game=$matches[1]', 'top');
        add_filter('query_vars', function($vars) {
            $vars[] = 'cl_game';
            return $vars;
        });
        add_action('template_redirect', [$this, 'template_loader']);
    }

    public function template_loader() {
        $game_slug = get_query_var('cl_game');
        if ($game_slug) {
            include CL_DB_PATH . 'templates/single-game.php';
            exit;
        }
    }

    public function add_admin_menu() {
        add_menu_page(
            'CloudLoadout DB',
            'CloudLoadout',
            'manage_options',
            'cloudloadout-db',
            ['CloudLoadout\Admin', 'render_dashboard'],
            'dashicons-games'
        );
        add_submenu_page('cloudloadout-db', 'Games', 'Games', 'manage_options', 'cl-games', ['CloudLoadout\Admin', 'render_games']);
        add_submenu_page('cloudloadout-db', 'Providers', 'Providers', 'manage_options', 'cl-providers', ['CloudLoadout\Admin', 'render_providers']);
        add_submenu_page('cloudloadout-db', 'Sync Logs', 'Sync Logs', 'manage_options', 'cl-sync-logs', ['CloudLoadout\Admin', 'render_logs']);
        add_submenu_page('cloudloadout-db', 'Settings', 'Settings', 'manage_options', 'cl-settings', ['CloudLoadout\Admin', 'render_settings']);
    }

    public function enqueue_assets() {
        wp_enqueue_style('cl-db-style', CL_DB_URL . 'assets/css/style.css', [], CL_DB_VERSION);
        wp_enqueue_script('cl-db-script', CL_DB_URL . 'assets/js/script.js', ['jquery'], CL_DB_VERSION, true);
        
        wp_localize_script('cl-db-script', 'clDb', [
            'apiUrl' => rest_url('cl/v1'),
            'nonce' => wp_create_nonce('wp_rest')
        ]);
    }

    public function enqueue_admin_assets() {
        wp_enqueue_style('cl-db-admin-style', CL_DB_URL . 'assets/css/admin.css', [], CL_DB_VERSION);
    }
}

CloudLoadoutDB::get_instance();
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
