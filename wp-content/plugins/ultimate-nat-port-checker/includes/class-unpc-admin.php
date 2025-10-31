<?php
/**
 * Admin functionality
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class UNPC_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'handle_port_preset_actions'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('NAT & Port Checker', 'ultimate-nat-port-checker'),
            __('NAT & Port Checker', 'ultimate-nat-port-checker'),
            'manage_options',
            'unpc-settings',
            array($this, 'settings_page'),
            'dashicons-admin-tools',
            30
        );
    }
    
    public function register_settings() {
        register_setting('unpc_settings_group', 'unpc_settings', array($this, 'sanitize_settings'));
    }
    
    public function handle_port_preset_actions() {
        // Reserved for future custom port preset management
        // Currently port presets are configured during plugin activation
    }
    
    public function sanitize_settings($input) {
        $defaults = UNPC_API::get_default_settings();
        $sanitized = array();
        
        $color_fields = array(
            'primary_color',
            'secondary_color',
            'nat_type1_color',
            'nat_type2_color',
            'nat_type3_color',
            'port_open_color',
            'port_closed_color'
        );
        foreach ($color_fields as $field) {
            $color = isset($input[$field]) ? sanitize_hex_color($input[$field]) : '';
            $sanitized[$field] = $color ? $color : $defaults[$field];
        }
        
        $container_width = isset($input['container_width']) ? intval($input['container_width']) : intval($defaults['container_width']);
        $sanitized['container_width'] = min(1920, max(800, $container_width));
        
        $allowed_fonts = array('small', 'medium', 'large');
        $font_size = isset($input['font_size_preset']) ? sanitize_text_field($input['font_size_preset']) : $defaults['font_size_preset'];
        $sanitized['font_size_preset'] = in_array($font_size, $allowed_fonts, true) ? $font_size : $defaults['font_size_preset'];
        
        $sanitized['header_text'] = isset($input['header_text']) ? sanitize_text_field($input['header_text']) : $defaults['header_text'];
        $sanitized['footer_text'] = isset($input['footer_text']) ? sanitize_text_field($input['footer_text']) : $defaults['footer_text'];
        $sanitized['privacy_note'] = isset($input['privacy_note']) ? sanitize_textarea_field($input['privacy_note']) : $defaults['privacy_note'];
        $sanitized['home_url'] = isset($input['home_url']) ? esc_url_raw($input['home_url']) : esc_url_raw($defaults['home_url']);
        
        if (isset($input['guides']) && is_array($input['guides'])) {
            $guides = array();
            foreach ($input['guides'] as $guide) {
                $title = isset($guide['title']) ? sanitize_text_field($guide['title']) : '';
                $url = isset($guide['url']) ? esc_url_raw($guide['url']) : '';
                $description = isset($guide['description']) ? sanitize_textarea_field($guide['description']) : '';
                if (!empty($title) && !empty($url)) {
                    $guides[] = array(
                        'title' => $title,
                        'url' => $url,
                        'description' => $description
                    );
                }
            }
            $sanitized['guides'] = wp_json_encode($guides);
        } else {
            $sanitized['guides'] = wp_json_encode(array());
        }
        
        return $sanitized;
    }
    
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_unpc-settings' !== $hook) {
            return;
        }
        
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        wp_enqueue_style(
            'unpc-admin-style',
            UNPC_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            UNPC_VERSION
        );
        
        wp_enqueue_script(
            'unpc-admin-script',
            UNPC_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            UNPC_VERSION,
            true
        );
    }
    
    public function settings_page() {
        $settings = UNPC_API::get_settings();
        $guides = UNPC_API::get_guides();
        if (!is_array($guides)) {
            $guides = array();
        }
        ?>
        <div class="wrap unpc-admin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?php
            settings_errors('unpc_settings');
            settings_errors('unpc_settings_messages');
            ?>
            
            <div class="unpc-admin-header">
                <h2>Advanced Configuration Panel</h2>
                <p>Customize your NAT & Port Checker tool appearance and functionality</p>
            </div>
            
            <form method="post" action="options.php" class="unpc-settings-form">
                <?php
                settings_fields('unpc_settings_group');
                ?>
                
                <div class="unpc-admin-tabs">
                    <nav class="nav-tab-wrapper">
                        <a href="#colors" class="nav-tab nav-tab-active">Colors</a>
                        <a href="#layout" class="nav-tab">Layout</a>
                        <a href="#content" class="nav-tab">Content</a>
                        <a href="#guides" class="nav-tab">Guides</a>
                        <a href="#shortcodes" class="nav-tab">Shortcodes</a>
                    </nav>
                    
                    <!-- Colors Tab -->
                    <div id="colors" class="tab-content active">
                        <h2>Color Settings</h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="primary_color">Primary Color</label>
                                </th>
                                <td>
                                    <input type="text" name="unpc_settings[primary_color]" id="primary_color" value="<?php echo esc_attr($settings['primary_color']); ?>" class="color-picker" />
                                    <p class="description">Main accent color for the tool</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="secondary_color">Secondary Color</label>
                                </th>
                                <td>
                                    <input type="text" name="unpc_settings[secondary_color]" id="secondary_color" value="<?php echo esc_attr($settings['secondary_color']); ?>" class="color-picker" />
                                    <p class="description">Secondary accent color</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="nat_type1_color">NAT Type 1 Color (Open)</label>
                                </th>
                                <td>
                                    <input type="text" name="unpc_settings[nat_type1_color]" id="nat_type1_color" value="<?php echo esc_attr($settings['nat_type1_color']); ?>" class="color-picker" />
                                    <p class="description">Color for Open NAT (Type 1)</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="nat_type2_color">NAT Type 2 Color (Moderate)</label>
                                </th>
                                <td>
                                    <input type="text" name="unpc_settings[nat_type2_color]" id="nat_type2_color" value="<?php echo esc_attr($settings['nat_type2_color']); ?>" class="color-picker" />
                                    <p class="description">Color for Moderate NAT (Type 2)</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="nat_type3_color">NAT Type 3 Color (Strict)</label>
                                </th>
                                <td>
                                    <input type="text" name="unpc_settings[nat_type3_color]" id="nat_type3_color" value="<?php echo esc_attr($settings['nat_type3_color']); ?>" class="color-picker" />
                                    <p class="description">Color for Strict NAT (Type 3)</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="port_open_color">Port Open Color</label>
                                </th>
                                <td>
                                    <input type="text" name="unpc_settings[port_open_color]" id="port_open_color" value="<?php echo esc_attr($settings['port_open_color']); ?>" class="color-picker" />
                                    <p class="description">Color for open ports</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="port_closed_color">Port Closed Color</label>
                                </th>
                                <td>
                                    <input type="text" name="unpc_settings[port_closed_color]" id="port_closed_color" value="<?php echo esc_attr($settings['port_closed_color']); ?>" class="color-picker" />
                                    <p class="description">Color for closed ports</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Layout Tab -->
                    <div id="layout" class="tab-content">
                        <h2>Layout Settings</h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="container_width">Container Width (px)</label>
                                </th>
                                <td>
                                    <input type="number" name="unpc_settings[container_width]" id="container_width" value="<?php echo esc_attr($settings['container_width']); ?>" min="800" max="1920" step="10" />
                                    <p class="description">Maximum width of the tool container (800-1920px)</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="font_size_preset">Font Size Preset</label>
                                </th>
                                <td>
                                    <select name="unpc_settings[font_size_preset]" id="font_size_preset">
                                        <option value="small" <?php selected($settings['font_size_preset'], 'small'); ?>>Small (Compact)</option>
                                        <option value="medium" <?php selected($settings['font_size_preset'], 'medium'); ?>>Medium (Standard)</option>
                                        <option value="large" <?php selected($settings['font_size_preset'], 'large'); ?>>Large (Accessible)</option>
                                    </select>
                                    <p class="description">Choose font size preset for the tool</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Content Tab -->
                    <div id="content" class="tab-content">
                        <h2>Content Settings</h2>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="header_text">Header Text</label>
                                </th>
                                <td>
                                    <input type="text" name="unpc_settings[header_text]" id="header_text" value="<?php echo esc_attr($settings['header_text']); ?>" class="regular-text" />
                                    <p class="description">Main header text for the tool</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="footer_text">Footer Text</label>
                                </th>
                                <td>
                                    <input type="text" name="unpc_settings[footer_text]" id="footer_text" value="<?php echo esc_attr($settings['footer_text']); ?>" class="regular-text" />
                                    <p class="description">Footer copyright text</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="privacy_note">Privacy Note</label>
                                </th>
                                <td>
                                    <textarea name="unpc_settings[privacy_note]" id="privacy_note" rows="3" class="large-text"><?php echo esc_textarea($settings['privacy_note']); ?></textarea>
                                    <p class="description">Privacy message displayed to users</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="home_url">Home Button URL</label>
                                </th>
                                <td>
                                    <input type="url" name="unpc_settings[home_url]" id="home_url" value="<?php echo esc_url($settings['home_url']); ?>" class="regular-text" />
                                    <p class="description">URL for the home button</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Guides Tab -->
                    <div id="guides" class="tab-content">
                        <h2>Guide Cards</h2>
                        <p>Add helpful guides from your website to display in the tool</p>
                        
                        <div id="guides-container">
                            <?php
                            if (!empty($guides)) {
                                foreach ($guides as $index => $guide) {
                                    $this->render_guide_row($index, $guide);
                                }
                            } else {
                                $this->render_guide_row(0, array('title' => '', 'url' => '', 'description' => ''));
                            }
                            ?>
                        </div>
                        
                        <button type="button" class="button button-secondary" id="add-guide">Add Another Guide</button>
                    </div>
                    
                    <!-- Shortcodes Tab -->
                    <div id="shortcodes" class="tab-content">
                        <h2>Available Shortcodes</h2>
                        
                        <div class="shortcode-box">
                            <h3>Full Tool</h3>
                            <code>[ultimate_nat_port_checker]</code>
                            <p>Displays the complete NAT & Port Checker with all features</p>
                        </div>
                        
                        <div class="shortcode-box">
                            <h3>NAT Checker Only</h3>
                            <code>[nat_checker_only]</code>
                            <p>Displays only the NAT checker component (no header, footer, or other sections)</p>
                        </div>
                        
                        <div class="shortcode-box">
                            <h3>Port Checker Only</h3>
                            <code>[port_checker_only]</code>
                            <p>Displays only the Port checker component (no header, footer, or other sections)</p>
                        </div>
                    </div>
                </div>
                
                <?php submit_button('Save Settings', 'primary large'); ?>
            </form>
        </div>
        <?php
    }
    
    private function render_guide_row($index, $guide) {
        ?>
        <div class="guide-row">
            <div class="guide-fields">
                <div class="guide-field">
                    <label>Title</label>
                    <input type="text" name="unpc_settings[guides][<?php echo $index; ?>][title]" value="<?php echo esc_attr($guide['title']); ?>" placeholder="Guide Title" />
                </div>
                
                <div class="guide-field">
                    <label>URL</label>
                    <input type="url" name="unpc_settings[guides][<?php echo $index; ?>][url]" value="<?php echo esc_url($guide['url']); ?>" placeholder="https://yoursite.com/guide" />
                </div>
                
                <div class="guide-field">
                    <label>Description</label>
                    <textarea name="unpc_settings[guides][<?php echo $index; ?>][description]" placeholder="Brief description of the guide"><?php echo esc_textarea($guide['description']); ?></textarea>
                </div>
            </div>
            
            <button type="button" class="button button-secondary remove-guide">Remove</button>
        </div>
        <?php
    }
}
