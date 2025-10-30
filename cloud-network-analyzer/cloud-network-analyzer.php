<?php
/**
 * Plugin Name: Cloud Network Analyzer
 * Plugin URI: https://cloudnetworkanalyzer.com
 * Description: Professional network diagnostics tool for cloud gaming - NAT checker, port scanner, connection analyzer with educational guides
 * Version: 1.0.0
 * Author: Cloud Gaming Tools Team
 * Author URI: https://cloudnetworkanalyzer.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-network-analyzer
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CNA_VERSION', '1.0.0');
define('CNA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CNA_PLUGIN_URL', plugin_dir_url(__FILE__));

class CloudNetworkAnalyzer {
    
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
        require_once CNA_PLUGIN_DIR . 'includes/class-shortcodes.php';
        require_once CNA_PLUGIN_DIR . 'includes/class-api-handler.php';
        require_once CNA_PLUGIN_DIR . 'includes/helpers.php';
    }
    
    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_cna_check_nat', array($this, 'ajax_check_nat'));
        add_action('wp_ajax_nopriv_cna_check_nat', array($this, 'ajax_check_nat'));
        add_action('wp_ajax_cna_check_port', array($this, 'ajax_check_port'));
        add_action('wp_ajax_nopriv_cna_check_port', array($this, 'ajax_check_port'));
        add_action('wp_ajax_cna_get_device_info', array($this, 'ajax_get_device_info'));
        add_action('wp_ajax_nopriv_cna_get_device_info', array($this, 'ajax_get_device_info'));
        add_action('wp_ajax_cna_reset_analytics', array($this, 'ajax_reset_analytics'));
        add_action('wp_ajax_cna_ping_test', array($this, 'ajax_ping_test'));
        add_action('wp_ajax_nopriv_cna_ping_test', array($this, 'ajax_ping_test'));
        
        CNA_Shortcodes::init();
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('Cloud Network Analyzer', 'cloud-network-analyzer'),
            __('Network Analyzer', 'cloud-network-analyzer'),
            'manage_options',
            'cloud-network-analyzer',
            array($this, 'render_admin_page'),
            'dashicons-networking',
            30
        );
    }
    
    public function register_settings() {
        register_setting('cna_settings_group', 'cna_settings', array($this, 'sanitize_settings'));
        
        add_settings_section(
            'cna_main_settings',
            __('Main Settings', 'cloud-network-analyzer'),
            array($this, 'settings_section_callback'),
            'cloud-network-analyzer'
        );
        
        $fields = array(
            'enable_nat_checker' => __('Enable NAT Checker', 'cloud-network-analyzer'),
            'enable_port_checker' => __('Enable Port Checker', 'cloud-network-analyzer'),
            'enable_device_info' => __('Enable Device Info', 'cloud-network-analyzer'),
            'enable_guides' => __('Enable Educational Guides', 'cloud-network-analyzer'),
            'enable_advanced_details' => __('Enable Advanced Details', 'cloud-network-analyzer'),
            'enable_router_links' => __('Enable Router Login Links', 'cloud-network-analyzer'),
            'theme_mode' => __('Theme Mode', 'cloud-network-analyzer'),
            'color_palette' => __('Color Palette', 'cloud-network-analyzer'),
            'enable_analytics' => __('Enable Analytics', 'cloud-network-analyzer'),
        );
        
        foreach ($fields as $field_id => $field_label) {
            add_settings_field(
                $field_id,
                $field_label,
                array($this, 'render_field_' . $field_id),
                'cloud-network-analyzer',
                'cna_main_settings'
            );
        }
        
        add_settings_field(
            'router_links',
            __('Router Login Links', 'cloud-network-analyzer'),
            array($this, 'render_field_router_links'),
            'cloud-network-analyzer',
            'cna_main_settings'
        );
        
        add_settings_field(
            'custom_guides',
            __('Custom Guide Content', 'cloud-network-analyzer'),
            array($this, 'render_field_custom_guides'),
            'cloud-network-analyzer',
            'cna_main_settings'
        );
    }
    
    public function settings_section_callback() {
        echo '<p>' . __('Configure your Cloud Network Analyzer settings below.', 'cloud-network-analyzer') . '</p>';
    }
    
    public function render_field_enable_nat_checker() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['enable_nat_checker']) ? $settings['enable_nat_checker'] : '1';
        echo '<input type="checkbox" name="cna_settings[enable_nat_checker]" value="1" ' . checked(1, $value, false) . ' />';
    }
    
    public function render_field_enable_port_checker() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['enable_port_checker']) ? $settings['enable_port_checker'] : '1';
        echo '<input type="checkbox" name="cna_settings[enable_port_checker]" value="1" ' . checked(1, $value, false) . ' />';
    }
    
    public function render_field_enable_device_info() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['enable_device_info']) ? $settings['enable_device_info'] : '1';
        echo '<input type="checkbox" name="cna_settings[enable_device_info]" value="1" ' . checked(1, $value, false) . ' />';
    }
    
    public function render_field_enable_guides() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['enable_guides']) ? $settings['enable_guides'] : '1';
        echo '<input type="checkbox" name="cna_settings[enable_guides]" value="1" ' . checked(1, $value, false) . ' />';
    }
    
    public function render_field_enable_advanced_details() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['enable_advanced_details']) ? $settings['enable_advanced_details'] : '1';
        echo '<input type="checkbox" name="cna_settings[enable_advanced_details]" value="1" ' . checked(1, $value, false) . ' />';
    }
    
    public function render_field_enable_router_links() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['enable_router_links']) ? $settings['enable_router_links'] : '1';
        echo '<input type="checkbox" name="cna_settings[enable_router_links]" value="1" ' . checked(1, $value, false) . ' />';
    }
    
    public function render_field_theme_mode() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['theme_mode']) ? $settings['theme_mode'] : 'light';
        ?>
        <select name="cna_settings[theme_mode]">
            <option value="light" <?php selected($value, 'light'); ?>>Light</option>
            <option value="dark" <?php selected($value, 'dark'); ?>>Dark</option>
            <option value="auto" <?php selected($value, 'auto'); ?>>Auto</option>
        </select>
        <?php
    }
    
    public function render_field_color_palette() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['color_palette']) ? $settings['color_palette'] : 'blue-gray';
        ?>
        <select name="cna_settings[color_palette]">
            <option value="blue-gray" <?php selected($value, 'blue-gray'); ?>>Blue Gray (Default)</option>
            <option value="purple" <?php selected($value, 'purple'); ?>>Purple</option>
            <option value="green" <?php selected($value, 'green'); ?>>Green</option>
            <option value="red" <?php selected($value, 'red'); ?>>Red</option>
            <option value="orange" <?php selected($value, 'orange'); ?>>Orange</option>
        </select>
        <?php
    }
    
    public function render_field_enable_analytics() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['enable_analytics']) ? $settings['enable_analytics'] : '0';
        echo '<input type="checkbox" name="cna_settings[enable_analytics]" value="1" ' . checked(1, $value, false) . ' />';
        echo '<p class="description">' . __('Track how many tests are run (no personal data collected)', 'cloud-network-analyzer') . '</p>';
    }
    
    public function render_field_router_links() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['router_links']) ? $settings['router_links'] : $this->get_default_router_links_text();
        echo '<textarea name="cna_settings[router_links]" rows="6" cols="60" class="large-text">' . esc_textarea($value) . '</textarea>';
        echo '<p class="description">' . __('One router per line using the format: Brand | Default IP | Username | Password', 'cloud-network-analyzer') . '</p>';
    }
    
    public function render_field_custom_guides() {
        $settings = get_option('cna_settings', array());
        $value = isset($settings['custom_guides']) ? $settings['custom_guides'] : '';
        wp_editor($value, 'cna_custom_guides', array(
            'textarea_name' => 'cna_settings[custom_guides]',
            'textarea_rows' => 6,
            'media_buttons' => false
        ));
        echo '<p class="description">' . __('Add additional educational content or custom instructions for your audience.', 'cloud-network-analyzer') . '</p>';
    }
    
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (isset($_GET['settings-updated'])) {
            add_settings_error('cna_messages', 'cna_message', __('Settings Saved', 'cloud-network-analyzer'), 'updated');
        }
        
        settings_errors('cna_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="cna-admin-header">
                <p><?php _e('Professional network diagnostics tool for cloud gaming enthusiasts.', 'cloud-network-analyzer'); ?></p>
            </div>
            
            <div class="cna-admin-tabs">
                <h2 class="nav-tab-wrapper">
                    <a href="#settings" class="nav-tab nav-tab-active"><?php _e('Settings', 'cloud-network-analyzer'); ?></a>
                    <a href="#shortcodes" class="nav-tab"><?php _e('Shortcodes', 'cloud-network-analyzer'); ?></a>
                    <a href="#analytics" class="nav-tab"><?php _e('Analytics', 'cloud-network-analyzer'); ?></a>
                </h2>
                
                <div id="settings" class="cna-tab-content">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('cna_settings_group');
                        do_settings_sections('cloud-network-analyzer');
                        submit_button(__('Save Settings', 'cloud-network-analyzer'));
                        ?>
                    </form>
                </div>
                
                <div id="shortcodes" class="cna-tab-content" style="display:none;">
                    <h2><?php _e('Available Shortcodes', 'cloud-network-analyzer'); ?></h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Shortcode', 'cloud-network-analyzer'); ?></th>
                                <th><?php _e('Description', 'cloud-network-analyzer'); ?></th>
                                <th><?php _e('Usage', 'cloud-network-analyzer'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>[cloud_network_analyzer]</code></td>
                                <td><?php _e('Full network analyzer with all tools', 'cloud-network-analyzer'); ?></td>
                                <td><button class="button button-small cna-copy-shortcode" data-shortcode="[cloud_network_analyzer]"><?php _e('Copy', 'cloud-network-analyzer'); ?></button></td>
                            </tr>
                            <tr>
                                <td><code>[cloud_nat_checker]</code></td>
                                <td><?php _e('NAT Type Checker only', 'cloud-network-analyzer'); ?></td>
                                <td><button class="button button-small cna-copy-shortcode" data-shortcode="[cloud_nat_checker]"><?php _e('Copy', 'cloud-network-analyzer'); ?></button></td>
                            </tr>
                            <tr>
                                <td><code>[cloud_port_checker]</code></td>
                                <td><?php _e('Port Checker only', 'cloud-network-analyzer'); ?></td>
                                <td><button class="button button-small cna-copy-shortcode" data-shortcode="[cloud_port_checker]"><?php _e('Copy', 'cloud-network-analyzer'); ?></button></td>
                            </tr>
                            <tr>
                                <td><code>[cloud_device_info]</code></td>
                                <td><?php _e('Device & Connection Info only', 'cloud-network-analyzer'); ?></td>
                                <td><button class="button button-small cna-copy-shortcode" data-shortcode="[cloud_device_info]"><?php _e('Copy', 'cloud-network-analyzer'); ?></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div id="analytics" class="cna-tab-content" style="display:none;">
                    <h2><?php _e('Usage Analytics', 'cloud-network-analyzer'); ?></h2>
                    <?php
                    $analytics = get_option('cna_analytics', array());
                    $total_tests = isset($analytics['total_tests']) ? $analytics['total_tests'] : 0;
                    $nat_tests = isset($analytics['nat_tests']) ? $analytics['nat_tests'] : 0;
                    $port_tests = isset($analytics['port_tests']) ? $analytics['port_tests'] : 0;
                    ?>
                    <div class="cna-analytics-stats">
                        <div class="cna-stat-box">
                            <h3><?php echo number_format($total_tests); ?></h3>
                            <p><?php _e('Total Tests', 'cloud-network-analyzer'); ?></p>
                        </div>
                        <div class="cna-stat-box">
                            <h3><?php echo number_format($nat_tests); ?></h3>
                            <p><?php _e('NAT Checks', 'cloud-network-analyzer'); ?></p>
                        </div>
                        <div class="cna-stat-box">
                            <h3><?php echo number_format($port_tests); ?></h3>
                            <p><?php _e('Port Checks', 'cloud-network-analyzer'); ?></p>
                        </div>
                    </div>
                    <?php $history = get_option('cna_analytics_history', array()); ?>
                    <?php if (!empty($history)) : ?>
                        <h3><?php _e('Recent Activity', 'cloud-network-analyzer'); ?></h3>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php _e('Timestamp', 'cloud-network-analyzer'); ?></th>
                                    <th><?php _e('Type', 'cloud-network-analyzer'); ?></th>
                                    <th><?php _e('Details', 'cloud-network-analyzer'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_reverse($history) as $entry) : ?>
                                    <tr>
                                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($entry['timestamp']))); ?></td>
                                        <td><?php echo esc_html(ucwords(str_replace('_', ' ', $entry['type']))); ?></td>
                                        <td>
                                            <?php if (!empty($entry['data']) && is_array($entry['data'])) : ?>
                                                <ul>
                                                    <?php foreach ($entry['data'] as $key => $value) : ?>
                                                        <li><strong><?php echo esc_html(ucwords(str_replace('_', ' ', $key))); ?>:</strong> <?php echo esc_html(is_scalar($value) ? $value : wp_json_encode($value)); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else : ?>
                                                <em><?php _e('No additional details', 'cloud-network-analyzer'); ?></em>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p><?php _e('Analytics history will appear here once tests have been run.', 'cloud-network-analyzer'); ?></p>
                    <?php endif; ?>
                    <p><button class="button" id="cna-reset-analytics"><?php _e('Reset Analytics', 'cloud-network-analyzer'); ?></button></p>
                </div>
            </div>
            
            <style>
                .cna-admin-header {
                    background: #f0f0f1;
                    padding: 20px;
                    border-radius: 5px;
                    margin: 20px 0;
                }
                .cna-analytics-stats {
                    display: flex;
                    gap: 20px;
                    margin: 20px 0;
                }
                .cna-stat-box {
                    background: #fff;
                    padding: 30px;
                    border-radius: 5px;
                    border: 1px solid #ddd;
                    text-align: center;
                    flex: 1;
                }
                .cna-stat-box h3 {
                    font-size: 32px;
                    margin: 0 0 10px 0;
                    color: #2271b1;
                }
                .cna-stat-box p {
                    margin: 0;
                    color: #666;
                }
            </style>
            
            <script>
                jQuery(document).ready(function($) {
                    $('.nav-tab').on('click', function(e) {
                        e.preventDefault();
                        var target = $(this).attr('href');
                        $('.nav-tab').removeClass('nav-tab-active');
                        $(this).addClass('nav-tab-active');
                        $('.cna-tab-content').hide();
                        $(target).show();
                    });
                    
                    $('.cna-copy-shortcode').on('click', function(e) {
                        e.preventDefault();
                        var shortcode = $(this).data('shortcode');
                        navigator.clipboard.writeText(shortcode).then(function() {
                            alert('Shortcode copied to clipboard!');
                        });
                    });
                    
                    $('#cna-reset-analytics').on('click', function() {
                        if (confirm('Are you sure you want to reset all analytics data?')) {
                            $.post(ajaxurl, {
                                action: 'cna_reset_analytics',
                                nonce: '<?php echo wp_create_nonce('cna_reset_analytics'); ?>'
                            }, function() {
                                location.reload();
                            });
                        }
                    });
                });
            </script>
        </div>
        <?php
    }
    
    public function enqueue_frontend_assets() {
        wp_enqueue_style('cna-frontend-styles', CNA_PLUGIN_URL . 'assets/css/frontend-styles.css', array(), CNA_VERSION);
        wp_enqueue_script('cna-frontend-scripts', CNA_PLUGIN_URL . 'assets/js/frontend-scripts.js', array('jquery'), CNA_VERSION, true);
        
        wp_localize_script('cna-frontend-scripts', 'cnaAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cna_nonce'),
            'settings' => get_option('cna_settings', array())
        ));
    }
    
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_cloud-network-analyzer' !== $hook) {
            return;
        }
        wp_enqueue_style('cna-admin-styles', CNA_PLUGIN_URL . 'assets/css/admin-styles.css', array(), CNA_VERSION);
    }
    
    public function ajax_check_nat() {
        check_ajax_referer('cna_nonce', 'nonce');
        
        $api_handler = new CNA_API_Handler();
        $result = $api_handler->check_nat();
        
        $this->track_analytics('nat_tests', array(
            'nat_type' => isset($result['nat_type']) ? $result['nat_type'] : 'Unknown',
            'public_ip' => isset($result['public_ip']) ? $result['public_ip'] : ''
        ));
        
        wp_send_json_success($result);
    }
    
    public function ajax_check_port() {
        check_ajax_referer('cna_nonce', 'nonce');
        
        $port = isset($_POST['port']) ? intval($_POST['port']) : 0;
        $protocol = isset($_POST['protocol']) ? sanitize_text_field($_POST['protocol']) : 'tcp';
        $target_ip = isset($_POST['target_ip']) ? sanitize_text_field($_POST['target_ip']) : '';
        
        if ($port <= 0 || $port > 65535) {
            wp_send_json_error(array('message' => 'Invalid port number'));
        }
        
        $api_handler = new CNA_API_Handler();
        $result = $api_handler->check_port($port, $protocol, $target_ip);
        
        $this->track_analytics('port_tests', array(
            'port' => $result['port'],
            'protocol' => $result['protocol'],
            'status' => $result['status']
        ));
        
        wp_send_json_success($result);
    }
    
    public function ajax_get_device_info() {
        check_ajax_referer('cna_nonce', 'nonce');
        
        $api_handler = new CNA_API_Handler();
        $result = $api_handler->get_device_info();
        
        wp_send_json_success($result);
    }
    
    public function ajax_reset_analytics() {
        check_ajax_referer('cna_reset_analytics', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        delete_option('cna_analytics');
        wp_send_json_success();
    }
    
    public function ajax_ping_test() {
        wp_send_json_success(array('pong' => true, 'time' => current_time('timestamp')));
    }
    
    private function track_analytics($type, $data = array()) {
        $settings = get_option('cna_settings', array());
        if (!isset($settings['enable_analytics']) || !$settings['enable_analytics']) {
            return;
        }
        
        $analytics = get_option('cna_analytics', array());
        
        if (!isset($analytics['total_tests'])) {
            $analytics['total_tests'] = 0;
        }
        if (!isset($analytics[$type])) {
            $analytics[$type] = 0;
        }
        
        $analytics['total_tests']++;
        $analytics[$type]++;
        
        $analytics['last_test'] = current_time('mysql');
        $analytics['last_test_type'] = $type;
        
        update_option('cna_analytics', $analytics);
        $this->log_history($type, $data);
    }
    
    private function log_history($type, $data = array()) {
        $history = get_option('cna_analytics_history', array());
        $history[] = array(
            'type' => $type,
            'timestamp' => current_time('mysql'),
            'data' => $data
        );
        if (count($history) > 100) {
            $history = array_slice($history, -100);
        }
        update_option('cna_analytics_history', $history);
    }
    
    public function sanitize_settings($input) {
        $sanitized = array();
        
        $sanitized['enable_nat_checker'] = isset($input['enable_nat_checker']) ? 1 : 0;
        $sanitized['enable_port_checker'] = isset($input['enable_port_checker']) ? 1 : 0;
        $sanitized['enable_device_info'] = isset($input['enable_device_info']) ? 1 : 0;
        $sanitized['enable_guides'] = isset($input['enable_guides']) ? 1 : 0;
        $sanitized['enable_advanced_details'] = isset($input['enable_advanced_details']) ? 1 : 0;
        $sanitized['enable_router_links'] = isset($input['enable_router_links']) ? 1 : 0;
        $sanitized['enable_analytics'] = isset($input['enable_analytics']) ? 1 : 0;
        
        $sanitized['theme_mode'] = isset($input['theme_mode']) ? sanitize_text_field($input['theme_mode']) : 'light';
        $sanitized['color_palette'] = isset($input['color_palette']) ? sanitize_text_field($input['color_palette']) : 'blue-gray';
        
        if (isset($input['router_links'])) {
            $sanitized['router_links'] = sanitize_textarea_field($input['router_links']);
        }
        
        if (isset($input['custom_guides'])) {
            $sanitized['custom_guides'] = wp_kses_post($input['custom_guides']);
        }
        
        return $sanitized;
    }
    
    private function get_default_router_links_text() {
        return "Netgear | 192.168.1.1 | admin | password\nTP-Link | 192.168.0.1 | admin | admin\nLinksys | 192.168.1.1 | admin | admin\nAsus | 192.168.1.1 | admin | admin\nD-Link | 192.168.0.1 | admin | (blank)\nBelkin | 192.168.2.1 | (blank) | (blank)";
    }
}

function cloud_network_analyzer_init() {
    return CloudNetworkAnalyzer::get_instance();
}

add_action('plugins_loaded', 'cloud_network_analyzer_init');

register_activation_hook(__FILE__, 'cna_activate_plugin');
function cna_activate_plugin() {
    $default_settings = array(
        'enable_nat_checker' => 1,
        'enable_port_checker' => 1,
        'enable_device_info' => 1,
        'enable_guides' => 1,
        'enable_advanced_details' => 1,
        'enable_router_links' => 1,
        'theme_mode' => 'light',
        'color_palette' => 'blue-gray',
        'enable_analytics' => 0,
    );
    
    if (!get_option('cna_settings')) {
        update_option('cna_settings', $default_settings);
    }
    
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'cna_deactivate_plugin');
function cna_deactivate_plugin() {
    flush_rewrite_rules();
}
