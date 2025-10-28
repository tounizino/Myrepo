<?php

if (!defined('ABSPATH')) {
    exit;
}

class AAPD_Plugin {

    /**
     * @var AAPD_Plugin|null
     */
    private static $instance = null;

    /**
     * Flag to mark front-end assets enqueue
     *
     * @var bool
     */
    private $assets_enqueued = false;

    /**
     * Singleton instance
     *
     * @return AAPD_Plugin
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    private function includes() {
        require_once AAPDP_PLUGIN_DIR . 'includes/class-aapd-settings.php';
        require_once AAPDP_PLUGIN_DIR . 'includes/class-aapd-api-client.php';
        require_once AAPDP_PLUGIN_DIR . 'includes/class-aapd-layout-renderer.php';
        require_once AAPDP_PLUGIN_DIR . 'includes/class-aapd-shortcodes.php';
    }

    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init_modules'));
        add_action('wp_enqueue_scripts', array($this, 'register_frontend_assets'));
    }

    public function load_textdomain() {
        load_plugin_textdomain('amazon-affiliate-displays', false, dirname(plugin_basename(AAPDP_PLUGIN_FILE)) . '/languages');
    }

    public function init_modules() {
        AAPD_Settings::instance();
        AAPD_Shortcodes::instance();
    }

    public function register_frontend_assets() {
        wp_register_style(
            'aapd-frontend',
            AAPDP_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            AAPDP_VERSION
        );

        wp_register_script(
            'aapd-frontend',
            AAPDP_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            AAPDP_VERSION,
            true
        );
    }

    public function enqueue_frontend_assets() {
        if ($this->assets_enqueued) {
            return;
        }

        wp_enqueue_style('aapd-frontend');
        wp_enqueue_script('aapd-frontend');

        $settings = AAPD_Settings::instance()->get_settings();

        $accent = $this->sanitize_hex($settings['accent_color'], '#146eb4');
        $button_start = $this->sanitize_hex($settings['button_gradient_start'], '#ff9900');
        $button_end = $this->sanitize_hex($settings['button_gradient_end'], '#ffcc00');
        $hover = $this->sanitize_hex($settings['hover_color'], '#f37300');
        $font_stack = $this->get_font_stack($settings);

        $css_vars = sprintf(
            ':root { --aapd-accent:%1$s; --aapd-button-start:%2$s; --aapd-button-end:%3$s; --aapd-hover:%4$s; }
            .aapd-wrapper { font-family: %5$s; }
        ',
            $accent,
            $button_start,
            $button_end,
            $hover,
            $font_stack
        );

        wp_add_inline_style('aapd-frontend', $css_vars);

        if (!empty($settings['custom_css'])) {
            wp_add_inline_style('aapd-frontend', $settings['custom_css']);
        }

        if (!empty($settings['dark_mode'])) {
            wp_add_inline_style('aapd-frontend', '.aapd-wrapper.aapd-dark { --aapd-bg: #0f1419; --aapd-text: #f7f7f7; --aapd-text-secondary: #a0a0a0; --aapd-border: #2a2e32; --aapd-shadow: rgba(18, 24, 32, 0.6); }');
        }

        $this->maybe_enqueue_google_font($settings);

        $this->assets_enqueued = true;
    }

    private function sanitize_hex($color, $fallback) {
        $sanitized = sanitize_hex_color($color);
        return $sanitized ? $sanitized : $fallback;
    }

    private function maybe_enqueue_google_font($settings) {
        if (empty($settings['font_choice']) || 'system' === $settings['font_choice']) {
            return;
        }

        if (empty($settings['google_font_family'])) {
            return;
        }

        $font_family = trim($settings['google_font_family']);

        $url = add_query_arg(
            array(
                'family' => $font_family,
                'display' => 'swap',
            ),
            'https://fonts.googleapis.com/css2'
        );

        wp_enqueue_style('aapd-google-fonts', $url, array(), null);
    }

    private function get_font_stack($settings) {
        $system_stack = '"Inter", "SF Pro Text", "Segoe UI", system-ui, -apple-system, BlinkMacSystemFont, "Helvetica Neue", Arial, sans-serif';

        if (!empty($settings['font_choice']) && 'google' === $settings['font_choice'] && !empty($settings['google_font_family'])) {
            $font_name = explode(':', $settings['google_font_family']);
            $primary = $this->sanitize_font_family($font_name[0]);
            return sprintf('"%s", %s', $primary, $system_stack);
        }

        return $system_stack;
    }

    private function sanitize_font_family($font) {
        $font = preg_replace('/[^A-Za-z0-9\s\-]/', ' ', $font);
        $font = preg_replace('/\s+/', ' ', $font);
        return trim($font);
    }

    public static function activate() {
        $defaults = AAPD_Settings::get_default_settings();
        $existing = get_option('aapd_settings');

        if (!is_array($existing)) {
            add_option('aapd_settings', $defaults);
        } else {
            $merged = wp_parse_args($existing, $defaults);
            update_option('aapd_settings', $merged);
        }
    }

    public static function deactivate() {
        // No action required currently.
    }
}
