<?php
/**
 * Plugin Name: Cloud Gaming Speed Test - LibreSpeed
 * Plugin URI: https://example.com/cloud-gaming-speed-test
 * Description: Ultimate advanced speed test plugin for cloud gamers using LibreSpeed backend with stunning animations and cloud gaming assessment.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * Text Domain: cloud-gaming-speed-test
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CGST_VERSION', '1.0.0');
define('CGST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CGST_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CGST_PLUGIN_BASENAME', plugin_basename(__FILE__));

class CloudGamingSpeedTest {
    
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
    }
    
    private function load_dependencies() {
        require_once CGST_PLUGIN_DIR . 'includes/database.php';
        require_once CGST_PLUGIN_DIR . 'includes/ajax-handlers.php';
    }
    
    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        
        add_shortcode('cloudspeedtest', array($this, 'render_speed_test'));
        
        new CGST_AJAX_Handlers();
    }
    
    public function activate() {
        CGST_Database::create_tables();
        CGST_Database::insert_default_servers();
        CGST_Database::insert_default_articles();
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('cloud-gaming-speed-test', false, dirname(CGST_PLUGIN_BASENAME) . '/languages/');
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('Cloud Gaming Speed Test', 'cloud-gaming-speed-test'),
            __('Speed Test', 'cloud-gaming-speed-test'),
            'manage_options',
            'cgst-dashboard',
            array($this, 'render_dashboard_page'),
            'dashicons-performance',
            30
        );
        
        add_submenu_page(
            'cgst-dashboard',
            __('Test History', 'cloud-gaming-speed-test'),
            __('Test History', 'cloud-gaming-speed-test'),
            'manage_options',
            'cgst-history',
            array($this, 'render_history_page')
        );
        
        add_submenu_page(
            'cgst-dashboard',
            __('Server Presets', 'cloud-gaming-speed-test'),
            __('Server Presets', 'cloud-gaming-speed-test'),
            'manage_options',
            'cgst-servers',
            array($this, 'render_servers_page')
        );
        
        add_submenu_page(
            'cgst-dashboard',
            __('Articles & Tips', 'cloud-gaming-speed-test'),
            __('Articles & Tips', 'cloud-gaming-speed-test'),
            'manage_options',
            'cgst-articles',
            array($this, 'render_articles_page')
        );
        
        add_submenu_page(
            'cgst-dashboard',
            __('Settings', 'cloud-gaming-speed-test'),
            __('Settings', 'cloud-gaming-speed-test'),
            'manage_options',
            'cgst-settings',
            array($this, 'render_settings_page')
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'cgst-') === false) {
            return;
        }
        
        wp_enqueue_style('cgst-admin-style', CGST_PLUGIN_URL . 'assets/css/admin-style.css', array(), CGST_VERSION);
        wp_enqueue_script('cgst-admin-script', CGST_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), CGST_VERSION, true);
        
        $servers  = CGST_Database::get_servers();
        $articles = CGST_Database::get_articles();
        
        wp_localize_script('cgst-admin-script', 'cgstAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('cgst_admin_nonce'),
            'servers' => $servers,
            'articles'=> $articles,
            'i18n'   => array(
                'noServers'    => __('No servers configured yet.', 'cloud-gaming-speed-test'),
                'noArticles'   => __('No articles configured yet.', 'cloud-gaming-speed-test'),
                'edit'         => __('Edit', 'cloud-gaming-speed-test'),
                'delete'       => __('Delete', 'cloud-gaming-speed-test'),
                'deleteConfirm'=> __('Are you sure you want to delete this item?', 'cloud-gaming-speed-test'),
                'genericError' => __('Something went wrong. Please try again.', 'cloud-gaming-speed-test'),
            ),
        ));
    }
    
    public function enqueue_frontend_scripts() {
        wp_enqueue_style('cgst-frontend-style', CGST_PLUGIN_URL . 'assets/css/style.css', array(), CGST_VERSION);
        wp_enqueue_script('cgst-speed-test', CGST_PLUGIN_URL . 'assets/js/speed-test.js', array('jquery'), CGST_VERSION, true);
        
        $servers     = CGST_Database::get_active_servers();
        $thresholds  = CGST_Database::get_rating_thresholds();
        
        wp_localize_script('cgst-speed-test', 'cgstData', array(
            'ajaxurl'     => admin_url('admin-ajax.php'),
            'nonce'       => wp_create_nonce('cgst_nonce'),
            'servers'     => $servers,
            'pluginUrl'   => CGST_PLUGIN_URL,
            'thresholds'  => $thresholds,
            'i18n'        => array(
                'selectServer'      => __('Select a server...', 'cloud-gaming-speed-test'),
                'pleaseSelectServer'=> __('Please select a server first.', 'cloud-gaming-speed-test'),
                'findingFastest'    => __('Finding the fastest server...', 'cloud-gaming-speed-test'),
                'testingLatency'    => __('Testing latency and jitter...', 'cloud-gaming-speed-test'),
                'testingDownload'   => __('Testing download speed...', 'cloud-gaming-speed-test'),
                'testingUpload'     => __('Testing upload speed...', 'cloud-gaming-speed-test'),
                'initializing'      => __('Initializing test...', 'cloud-gaming-speed-test'),
                'errorNoServer'     => __('No server available for testing. Please configure server presets.', 'cloud-gaming-speed-test'),
                'complete'          => __('Test complete!', 'cloud-gaming-speed-test'),
            ),
            'tips'        => array(
                'Excellent' => array(
                    __('Enable native 4K streaming profiles in your cloud gaming app.', 'cloud-gaming-speed-test'),
                    __('Use wired Ethernet or Wi-Fi 6E to maintain ultra-low latency.', 'cloud-gaming-speed-test'),
                    __('Consider enabling HDR and surround sound for maximum immersion.', 'cloud-gaming-speed-test'),
                ),
                'Good' => array(
                    __('Lock games to 1440p/60fps for a perfect balance of quality and responsiveness.', 'cloud-gaming-speed-test'),
                    __('Keep background downloads paused to stabilize your connection.', 'cloud-gaming-speed-test'),
                    __('Use router QoS to prioritize your cloud gaming traffic.', 'cloud-gaming-speed-test'),
                ),
                'Fair' => array(
                    __('Drop resolution to 1080p/60fps and enable performance mode.', 'cloud-gaming-speed-test'),
                    __('Switch to a wired connection or move closer to your router.', 'cloud-gaming-speed-test'),
                    __('Close bandwidth-heavy apps (backups, streaming, downloads).', 'cloud-gaming-speed-test'),
                ),
                'Poor' => array(
                    __('Set your cloud gaming app to 720p/60fps or lower for smoother play.', 'cloud-gaming-speed-test'),
                    __('Test during off-peak hours when your ISP network is less congested.', 'cloud-gaming-speed-test'),
                    __('Upgrade your router firmware or contact your ISP for potential issues.', 'cloud-gaming-speed-test'),
                ),
            ),
        ));
    }
    
    public function render_speed_test($atts) {
        ob_start();
        include CGST_PLUGIN_DIR . 'templates/speed-test-template.php';
        return ob_get_clean();
    }
    
    public function render_dashboard_page() {
        include CGST_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }
    
    public function render_history_page() {
        include CGST_PLUGIN_DIR . 'templates/admin-history.php';
    }
    
    public function render_servers_page() {
        include CGST_PLUGIN_DIR . 'templates/admin-servers.php';
    }
    
    public function render_articles_page() {
        include CGST_PLUGIN_DIR . 'templates/admin-articles.php';
    }
    
    public function render_settings_page() {
        include CGST_PLUGIN_DIR . 'templates/admin-settings.php';
    }
}

function cgst_init() {
    return CloudGamingSpeedTest::get_instance();
}

cgst_init();
