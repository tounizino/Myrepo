<?php
/**
 * Plugin Name: Internet Speed & Device Info Tool
 * Plugin URI: https://example.com/internet-speed-device-info-tool
 * Description: A comprehensive internet speed checker with IP lookup, device information, and location mapping. Perfect for cloud gaming blogs.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: isdit
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

if (!defined('ABSPATH')) {
    exit;
}

define('ISDIT_VERSION', '1.0.0');
define('ISDIT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ISDIT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ISDIT_NONCE_ACTION', 'isdt_nonce');

class Internet_Speed_Device_Info_Tool {

    /** @var Internet_Speed_Device_Info_Tool|null */
    private static $instance = null;

    /** @var ISDIT_API_Handler|null */
    private $api_handler = null;

    /**
     * Retrieve plugin singleton.
     *
     * @return Internet_Speed_Device_Info_Tool
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set up hooks.
     */
    private function __construct() {
        add_action('plugins_loaded', array($this, 'bootstrap'));
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));

        $this->register_shortcodes();
    }

    /**
     * Load translations and instantiate helpers.
     */
    public function bootstrap() {
        load_plugin_textdomain('isdit', false, dirname(plugin_basename(__FILE__)) . '/languages');

        require_once ISDIT_PLUGIN_DIR . 'includes/class-api-handler.php';
        $this->api_handler = new ISDIT_API_Handler($this);
    }

    /**
     * Register plugin shortcodes.
     */
    private function register_shortcodes() {
        add_shortcode('speed_device_dark', array($this, 'render_dark_theme'));
        add_shortcode('speed_device_light', array($this, 'render_light_theme'));
        add_shortcode('speed_device_skyblue', array($this, 'render_skyblue_theme'));
        add_shortcode('speed_test_dark', array($this, 'render_speed_only_dark'));
        add_shortcode('speed_test_skyblue', array($this, 'render_speed_only_skyblue'));
    }

    /**
     * Register frontend assets so they can be enqueued on demand.
     */
    public function register_assets() {
        wp_register_style('isdit-leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
        wp_register_style('isdit-frontend', ISDIT_PLUGIN_URL . 'assets/css/styles.css', array(), ISDIT_VERSION);

        wp_register_script('isdit-leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
        wp_register_script('isdit-speedtest', ISDIT_PLUGIN_URL . 'assets/js/speedtest.js', array(), ISDIT_VERSION, true);
        wp_register_script('isdit-frontend', ISDIT_PLUGIN_URL . 'assets/js/main.js', array('jquery', 'isdit-speedtest', 'isdit-leaflet'), ISDIT_VERSION, true);
    }

    /**
     * Enqueue assets for the shortcode output.
     */
    private function enqueue_frontend_assets() {
        wp_enqueue_style('isdit-leaflet');
        wp_enqueue_style('isdit-frontend');

        wp_enqueue_script('isdit-leaflet');
        wp_enqueue_script('isdit-speedtest');
        wp_enqueue_script('isdit-frontend');

        wp_localize_script('isdit-frontend', 'isdtAjax', $this->get_frontend_settings());
    }

    /**
     * Prepare configuration payload for the frontend script.
     *
     * @return array
     */
    private function get_frontend_settings() {
        $download_size = (float) $this->get_setting('download_size_mb', 5);
        $upload_size   = (float) $this->get_setting('upload_size_mb', 3);

        return array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce($this->get_nonce_action()),
            'config'  => array(
                'downloadSize'     => $download_size,
                'uploadSize'       => $upload_size,
                'testDuration'     => (int) $this->get_setting('test_duration_ms', 10000),
                'speedIterations'  => (int) $this->get_setting('speed_iterations', 2),
                'ipLookupEndpoint' => $this->sanitize_endpoint_for_frontend($this->get_setting('ip_lookup_endpoint', 'https://ipapi.co/json/')),
                'mapTileUrl'       => $this->sanitize_map_tile_option($this->get_setting('map_tile_url', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')),
                'mapAttribution'   => wp_kses_post($this->get_setting('map_attribution', '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors')),
                'features'         => array(
                    'speed'  => $this->get_setting('enable_speed_test', '1') === '1',
                    'ip'     => $this->get_setting('enable_ip_lookup', '1') === '1',
                    'device' => $this->get_setting('enable_device_info', '1') === '1',
                    'map'    => $this->get_setting('enable_location_map', '1') === '1',
                ),
            ),
        );
    }

    /**
     * Admin styles for the settings page.
     */
    public function admin_enqueue_scripts($hook) {
        if ('toplevel_page_isdit-settings' !== $hook) {
            return;
        }
        wp_enqueue_style('isdit-admin-styles', ISDIT_PLUGIN_URL . 'assets/css/admin.css', array(), ISDIT_VERSION);
    }

    /**
     * Register the plugin settings page.
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Speed & Device Info Tool', 'isdit'),
            __('Speed Tool', 'isdit'),
            'manage_options',
            'isdit-settings',
            array($this, 'render_admin_page'),
            'dashicons-performance',
            30
        );
    }

    /**
     * Register plugin settings and fields.
     */
    public function register_settings() {
        register_setting('isdit_settings_group', 'isdit_enable_speed_test', array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default'           => '1',
        ));

        register_setting('isdit_settings_group', 'isdit_enable_ip_lookup', array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default'           => '1',
        ));

        register_setting('isdit_settings_group', 'isdit_enable_device_info', array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default'           => '1',
        ));

        register_setting('isdit_settings_group', 'isdit_enable_location_map', array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default'           => '1',
        ));

        register_setting('isdit_settings_group', 'isdit_download_size_mb', array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_download_size'),
            'default'           => '5',
        ));

        register_setting('isdit_settings_group', 'isdit_upload_size_mb', array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_upload_size'),
            'default'           => '3',
        ));

        register_setting('isdit_settings_group', 'isdit_test_duration_ms', array(
            'type'              => 'integer',
            'sanitize_callback' => array($this, 'sanitize_test_duration'),
            'default'           => 10000,
        ));

        register_setting('isdit_settings_group', 'isdit_speed_iterations', array(
            'type'              => 'integer',
            'sanitize_callback' => array($this, 'sanitize_speed_iterations'),
            'default'           => 2,
        ));

        register_setting('isdit_settings_group', 'isdit_ip_lookup_endpoint', array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_ip_endpoint'),
            'default'           => 'https://ipapi.co/json/',
        ));

        register_setting('isdit_settings_group', 'isdit_map_tile_url', array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_map_tile_input'),
            'default'           => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        ));

        register_setting('isdit_settings_group', 'isdit_map_attribution', array(
            'type'              => 'string',
            'sanitize_callback' => array($this, 'sanitize_map_attribution'),
            'default'           => '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        ));

        add_settings_section(
            'isdit_features_section',
            __('Feature Toggles', 'isdit'),
            array($this, 'section_features_callback'),
            'isdit-settings'
        );

        add_settings_field(
            'isdit_enable_speed_test',
            __('Enable Speed Test', 'isdit'),
            array($this, 'checkbox_field_callback'),
            'isdit-settings',
            'isdit_features_section',
            array(
                'option_name' => 'enable_speed_test',
                'description' => __('Allow visitors to run multi-step download and upload tests.', 'isdit'),
            )
        );

        add_settings_field(
            'isdit_enable_ip_lookup',
            __('Enable IP Lookup', 'isdit'),
            array($this, 'checkbox_field_callback'),
            'isdit-settings',
            'isdit_features_section',
            array(
                'option_name' => 'enable_ip_lookup',
                'description' => __('Display IP address, ISP, and geographic metadata.', 'isdit'),
            )
        );

        add_settings_field(
            'isdit_enable_device_info',
            __('Enable Device Details', 'isdit'),
            array($this, 'checkbox_field_callback'),
            'isdit-settings',
            'isdit_features_section',
            array(
                'option_name' => 'enable_device_info',
                'description' => __('Show browser, operating system, display, and hardware information.', 'isdit'),
            )
        );

        add_settings_field(
            'isdit_enable_location_map',
            __('Enable Location Map', 'isdit'),
            array($this, 'checkbox_field_callback'),
            'isdit-settings',
            'isdit_features_section',
            array(
                'option_name' => 'enable_location_map',
                'description' => __('Embed a Leaflet map centered on the visitor location.', 'isdit'),
            )
        );

        add_settings_section(
            'isdit_performance_section',
            __('Speed Test Calibration', 'isdit'),
            array($this, 'section_performance_callback'),
            'isdit-settings'
        );

        add_settings_field(
            'isdit_download_size_mb',
            __('Download Chunk Size (MB)', 'isdit'),
            array($this, 'number_field_callback'),
            'isdit-settings',
            'isdit_performance_section',
            array(
                'option_name' => 'download_size_mb',
                'min'         => 0.5,
                'max'         => 50,
                'step'        => 0.5,
                'default'     => 5,
                'description' => __('Each iteration downloads this amount of data to measure throughput.', 'isdit'),
            )
        );

        add_settings_field(
            'isdit_upload_size_mb',
            __('Upload Payload Size (MB)', 'isdit'),
            array($this, 'number_field_callback'),
            'isdit-settings',
            'isdit_performance_section',
            array(
                'option_name' => 'upload_size_mb',
                'min'         => 0.25,
                'max'         => 20,
                'step'        => 0.25,
                'default'     => 3,
                'description' => __('Each upload iteration pushes this many megabytes to WordPress.', 'isdit'),
            )
        );

        add_settings_field(
            'isdit_speed_iterations',
            __('Test Iterations', 'isdit'),
            array($this, 'number_field_callback'),
            'isdit-settings',
            'isdit_performance_section',
            array(
                'option_name' => 'speed_iterations',
                'min'         => 1,
                'max'         => 5,
                'step'        => 1,
                'default'     => 2,
                'description' => __('Number of repeated download/upload measurements to average.', 'isdit'),
            )
        );

        add_settings_field(
            'isdit_test_duration_ms',
            __('Iteration Timeout (ms)', 'isdit'),
            array($this, 'number_field_callback'),
            'isdit-settings',
            'isdit_performance_section',
            array(
                'option_name' => 'test_duration_ms',
                'min'         => 3000,
                'max'         => 60000,
                'step'        => 500,
                'default'     => 10000,
                'description' => __('Maximum time allotted per download/upload measurement.', 'isdit'),
            )
        );

        add_settings_section(
            'isdit_geo_section',
            __('Geolocation & Map', 'isdit'),
            array($this, 'section_geo_callback'),
            'isdit-settings'
        );

        add_settings_field(
            'isdit_ip_lookup_endpoint',
            __('IP Lookup Endpoint', 'isdit'),
            array($this, 'text_field_callback'),
            'isdit-settings',
            'isdit_geo_section',
            array(
                'option_name' => 'ip_lookup_endpoint',
                'placeholder' => 'https://ipapi.co/json/',
                'description' => __('Use {ip} placeholder if the service expects it, e.g. https://ipapi.co/{ip}/json/.', 'isdit'),
            )
        );

        add_settings_field(
            'isdit_map_tile_url',
            __('Map Tile URL', 'isdit'),
            array($this, 'text_field_callback'),
            'isdit-settings',
            'isdit_geo_section',
            array(
                'option_name' => 'map_tile_url',
                'placeholder' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                'description' => __('Leaflet tile provider URL. Keep {s} {z} {x} {y} placeholders intact.', 'isdit'),
            )
        );

        add_settings_field(
            'isdit_map_attribution',
            __('Map Attribution', 'isdit'),
            array($this, 'textarea_field_callback'),
            'isdit-settings',
            'isdit_geo_section',
            array(
                'option_name' => 'map_attribution',
                'description' => __('Displayed beneath the map to comply with tile provider requirements. HTML allowed.', 'isdit'),
            )
        );
    }

    /**
     * Feature section description.
     */
    public function section_features_callback() {
        echo '<p>' . esc_html__('Toggle the modules you wish to expose on the frontend widget.', 'isdit') . '</p>';
    }

    /**
     * Performance section description.
     */
    public function section_performance_callback() {
        echo '<p>' . esc_html__('Fine tune the amount of data transferred for speed testing to balance precision and bandwidth.', 'isdit') . '</p>';
    }

    /**
     * Geolocation section description.
     */
    public function section_geo_callback() {
        echo '<p>' . esc_html__('Customize the IP intelligence provider and Leaflet map tiles.', 'isdit') . '</p>';
    }

    /**
     * Render checkbox field.
     */
    public function checkbox_field_callback($args) {
        $key = isset($args['option_name']) ? $args['option_name'] : '';
        if (empty($key)) {
            return;
        }

        $option_name = 'isdit_' . $key;
        $value = $this->get_setting($key, '1');

        echo '<input type="hidden" name="' . esc_attr($option_name) . '" value="0" />';
        echo '<label for="' . esc_attr($option_name) . '">';
        echo '<input type="checkbox" id="' . esc_attr($option_name) . '" name="' . esc_attr($option_name) . '" value="1" ' . checked('1', $value, false) . ' />';
        echo ' </label>';

        if (!empty($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    /**
     * Render numeric field.
     */
    public function number_field_callback($args) {
        $key         = isset($args['option_name']) ? $args['option_name'] : '';
        $min         = isset($args['min']) ? $args['min'] : 0;
        $max         = isset($args['max']) ? $args['max'] : '';
        $step        = isset($args['step']) ? $args['step'] : 1;
        $default     = isset($args['default']) ? $args['default'] : '';
        $option_name = 'isdit_' . $key;
        $value       = $this->get_setting($key, $default);

        echo '<input type="number" class="small-text" id="' . esc_attr($option_name) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '"';
        if ($min !== '') {
            echo ' min="' . esc_attr($min) . '"';
        }
        if ($max !== '') {
            echo ' max="' . esc_attr($max) . '"';
        }
        echo ' step="' . esc_attr($step) . '" />';

        if (!empty($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    /**
     * Render text field.
     */
    public function text_field_callback($args) {
        $key         = isset($args['option_name']) ? $args['option_name'] : '';
        $option_name = 'isdit_' . $key;
        $value       = $this->get_setting($key, '');
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';

        echo '<input type="text" class="regular-text" id="' . esc_attr($option_name) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '" />';

        if (!empty($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    /**
     * Render textarea field.
     */
    public function textarea_field_callback($args) {
        $key         = isset($args['option_name']) ? $args['option_name'] : '';
        $option_name = 'isdit_' . $key;
        $value       = $this->get_setting($key, '');

        echo '<textarea class="large-text" rows="3" id="' . esc_attr($option_name) . '" name="' . esc_attr($option_name) . '">' . esc_textarea($value) . '</textarea>';

        if (!empty($args['description'])) {
            echo '<p class="description">' . esc_html($args['description']) . '</p>';
        }
    }

    /**
     * Sanitize checkbox values.
     */
    public function sanitize_checkbox($value) {
        return $value === '1' ? '1' : '0';
    }

    /**
     * Sanitize download size option (MB).
     */
    public function sanitize_download_size($value) {
        $value = floatval($value);
        if ($value < 0.5) {
            $value = 0.5;
        }
        if ($value > 50) {
            $value = 50;
        }
        return (string) $value;
    }

    /**
     * Sanitize upload size option (MB).
     */
    public function sanitize_upload_size($value) {
        $value = floatval($value);
        if ($value < 0.25) {
            $value = 0.25;
        }
        if ($value > 20) {
            $value = 20;
        }
        return (string) $value;
    }

    /**
     * Sanitize iteration duration (milliseconds).
     */
    public function sanitize_test_duration($value) {
        $value = absint($value);
        if ($value < 3000) {
            $value = 3000;
        }
        if ($value > 60000) {
            $value = 60000;
        }
        return (string) $value;
    }

    /**
     * Sanitize iteration count.
     */
    public function sanitize_speed_iterations($value) {
        $value = absint($value);
        if ($value < 1) {
            $value = 1;
        }
        if ($value > 5) {
            $value = 5;
        }
        return (string) $value;
    }

    /**
     * Sanitize IP lookup endpoint.
     */
    public function sanitize_ip_endpoint($value) {
        $value = trim($value);
        if (empty($value)) {
            return 'https://ipapi.co/json/';
        }

        $placeholder = '{ip}';
        $token = 'ISDIT_IP_PLACEHOLDER';

        if (strpos($value, $placeholder) !== false) {
            $sanitized = esc_url_raw(str_replace($placeholder, $token, $value));
            return str_replace($token, $placeholder, $sanitized);
        }

        return esc_url_raw($value);
    }

    /**
     * Sanitize map tile URL input.
     */
    public function sanitize_map_tile_input($value) {
        $value = trim($value);
        if (empty($value)) {
            $value = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        }
        return sanitize_text_field($value);
    }

    /**
     * Sanitize map attribution allowing limited HTML.
     */
    public function sanitize_map_attribution($value) {
        $value = trim($value);
        if (empty($value)) {
            $value = '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors';
        }
        return wp_kses_post($value);
    }

    /**
     * Sanitize endpoint for frontend usage.
     */
    private function sanitize_endpoint_for_frontend($value) {
        $value = trim($value);
        if (empty($value)) {
            return 'https://ipapi.co/json/';
        }
        return esc_url_raw($value);
    }

    /**
     * Ensure map tile value has placeholders intact.
     */
    private function sanitize_map_tile_option($value) {
        $value = trim($value);
        if (empty($value)) {
            $value = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        }
        return $value;
    }

    /**
     * Render admin page.
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error('isdit_messages', 'isdit_message', __('Settings Saved', 'isdit'), 'updated');
        }

        settings_errors('isdit_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="isdit-admin-container">
                <div class="isdit-admin-main">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('isdit_settings_group');
                        do_settings_sections('isdit-settings');
                        submit_button(__('Save Settings', 'isdit'));
                        ?>
                    </form>
                </div>

                <div class="isdit-admin-sidebar">
                    <div class="isdit-sidebar-box">
                        <h2><?php esc_html_e('Available Shortcodes', 'isdit'); ?></h2>
                        <div class="shortcode-item">
                            <h3><?php esc_html_e('Full Tool – Dark Theme', 'isdit'); ?></h3>
                            <code>[speed_device_dark]</code>
                        </div>
                        <div class="shortcode-item">
                            <h3><?php esc_html_e('Full Tool – Light Theme', 'isdit'); ?></h3>
                            <code>[speed_device_light]</code>
                        </div>
                        <div class="shortcode-item">
                            <h3><?php esc_html_e('Full Tool – Sky Blue Theme', 'isdit'); ?></h3>
                            <code>[speed_device_skyblue]</code>
                        </div>
                        <div class="shortcode-item">
                            <h3><?php esc_html_e('Speed Test Only – Dark', 'isdit'); ?></h3>
                            <code>[speed_test_dark]</code>
                        </div>
                        <div class="shortcode-item">
                            <h3><?php esc_html_e('Speed Test Only – Sky Blue', 'isdit'); ?></h3>
                            <code>[speed_test_skyblue]</code>
                        </div>
                    </div>
                    <div class="isdit-sidebar-box">
                        <h2><?php esc_html_e('Usage Tips', 'isdit'); ?></h2>
                        <p><?php esc_html_e('Embed the shortcodes into posts, pages, or modern Gutenberg blocks. Each shortcode automatically loads optimized assets and adapts to the selected theme.', 'isdit'); ?></p>
                        <p><?php esc_html_e('Calibrate the test sizes to match your hosting bandwidth and expected visitor traffic.', 'isdit'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render full tool (dark theme).
     */
    public function render_dark_theme($atts) {
        return $this->render_tool('dark', true);
    }

    /**
     * Render full tool (light theme).
     */
    public function render_light_theme($atts) {
        return $this->render_tool('light', true);
    }

    /**
     * Render full tool (sky blue theme).
     */
    public function render_skyblue_theme($atts) {
        return $this->render_tool('skyblue', true);
    }

    /**
     * Render speed-only widget (dark theme).
     */
    public function render_speed_only_dark($atts) {
        return $this->render_tool('dark', false);
    }

    /**
     * Render speed-only widget (sky blue theme).
     */
    public function render_speed_only_skyblue($atts) {
        return $this->render_tool('skyblue', false);
    }

    /**
     * Core rendering logic shared by all shortcodes.
     */
    private function render_tool($theme, $full_version = true) {
        $this->enqueue_frontend_assets();

        $enable_speed   = $this->get_setting('enable_speed_test', '1') === '1';
        $enable_ip      = $this->get_setting('enable_ip_lookup', '1') === '1';
        $enable_device  = $this->get_setting('enable_device_info', '1') === '1';
        $enable_map     = $this->get_setting('enable_location_map', '1') === '1';

        ob_start();
        include ISDIT_PLUGIN_DIR . 'includes/template-tool.php';
        return ob_get_clean();
    }

    /**
     * Helper for retrieving plugin option values.
     */
    public function get_setting($key, $default = '') {
        return get_option('isdit_' . $key, $default);
    }

    /**
     * Nonce action name used for AJAX endpoints.
     */
    public function get_nonce_action() {
        return ISDIT_NONCE_ACTION;
    }

    /**
     * Activate plugin defaults.
     */
    public static function activate() {
        $defaults = array(
            'enable_speed_test'   => '1',
            'enable_ip_lookup'    => '1',
            'enable_device_info'  => '1',
            'enable_location_map' => '1',
            'download_size_mb'    => '5',
            'upload_size_mb'      => '3',
            'test_duration_ms'    => '10000',
            'speed_iterations'    => '2',
            'ip_lookup_endpoint'  => 'https://ipapi.co/json/',
            'map_tile_url'        => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'map_attribution'     => '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
        );

        foreach ($defaults as $key => $value) {
            if (false === get_option('isdit_' . $key, false)) {
                add_option('isdit_' . $key, $value);
            }
        }
    }
}

Internet_Speed_Device_Info_Tool::get_instance();

register_activation_hook(__FILE__, array('Internet_Speed_Device_Info_Tool', 'activate'));
