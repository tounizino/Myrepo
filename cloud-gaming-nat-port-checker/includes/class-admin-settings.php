<?php
if (!defined('ABSPATH')) {
    exit;
}

class CGNPC_Admin_Settings {
    private static $instance = null;
    private $option_name = 'cgnpc_settings';
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public static function get_default_settings() {
        return array(
            'primary_color' => '#00d4ff',
            'secondary_color' => '#ff5f9e',
            'bg_color_dark' => '#050819',
            'bg_color_light' => '#f6f9ff',
            'text_color_dark' => '#f3f6ff',
            'text_color_light' => '#0b0e17',
            'font_size' => 'small',
            'font_size_small' => '13px',
            'font_size_medium' => '15px',
            'font_size_large' => '17px',
            'container_width' => '1180px',
            'container_padding' => '24px',
            'section_title_font' => 'Space Grotesk, "Segoe UI", sans-serif',
            'section_title_size' => '1.35em',
            'header_title_size' => '2.25em',
            'guides' => array(
                array(
                    'title' => __('Optimize Your Router for Cloud Gaming', 'cloud-nat-port-checker'),
                    'image' => '',
                    'excerpt' => __('Step-by-step NAT, QoS, and bandwidth tuning for flawless streaming.', 'cloud-nat-port-checker'),
                    'link' => home_url('/cloud-gaming-router-optimization')
                ),
                array(
                    'title' => __('Essential Ports for Every Cloud Gaming Platform', 'cloud-nat-port-checker'),
                    'image' => '',
                    'excerpt' => __('Quick reference to Xbox Cloud, GeForce NOW, PS Remote Play, Steam Link, and Shadow.', 'cloud-nat-port-checker'),
                    'link' => home_url('/cloud-gaming-port-forwarding')
                ),
                array(
                    'title' => __('Troubleshoot Lag & Packet Loss in Minutes', 'cloud-nat-port-checker'),
                    'image' => '',
                    'excerpt' => __('Pinpoint ping spikes, Wi-Fi issues, and ISP throttling with pro tips.', 'cloud-nat-port-checker'),
                    'link' => home_url('/cloud-gaming-network-troubleshooting')
                )
            ),
            'logging_enabled' => 0,
        );
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_cgnpc_clear_logs', array($this, 'handle_clear_logs'));
        add_action('admin_notices', array($this, 'maybe_show_notices'));
    }
    
    public function add_settings_page() {
        add_options_page(
            __('Cloud Gaming NAT & Port Checker Settings', 'cloud-nat-port-checker'),
            __('NAT & Port Checker', 'cloud-nat-port-checker'),
            'manage_options',
            'cgnpc-settings',
            array($this, 'render_settings_page')
        );
    }
    
    public function register_settings() {
        register_setting('cgnpc_settings_group', $this->option_name, array($this, 'sanitize_settings'));
        
        // Design & Layout
        add_settings_section(
            'cgnpc_design_section',
            __('Design & Styling', 'cloud-nat-port-checker'),
            array($this, 'design_section_intro'),
            'cgnpc-settings'
        );
        
        $design_fields = array(
            'primary_color' => __('Primary Color', 'cloud-nat-port-checker'),
            'secondary_color' => __('Secondary Color', 'cloud-nat-port-checker'),
            'bg_color_dark' => __('Dark Background', 'cloud-nat-port-checker'),
            'bg_color_light' => __('Light Background', 'cloud-nat-port-checker'),
            'text_color_dark' => __('Dark Mode Text', 'cloud-nat-port-checker'),
            'text_color_light' => __('Light Mode Text', 'cloud-nat-port-checker'),
        );
        
        foreach ($design_fields as $field => $label) {
            add_settings_field(
                $field,
                $label,
                array($this, 'color_field_callback'),
                'cgnpc-settings',
                'cgnpc_design_section',
                array('field' => $field)
            );
        }
        
        add_settings_field(
            'font_size',
            __('Default Font Size', 'cloud-nat-port-checker'),
            array($this, 'font_size_choice_callback'),
            'cgnpc-settings',
            'cgnpc_design_section'
        );
        
        add_settings_field(
            'font_size_small',
            __('Small Font Size Value', 'cloud-nat-port-checker'),
            array($this, 'font_scale_field_callback'),
            'cgnpc-settings',
            'cgnpc_design_section',
            array('field' => 'font_size_small', 'placeholder' => '13px')
        );
        
        add_settings_field(
            'font_size_medium',
            __('Medium Font Size Value', 'cloud-nat-port-checker'),
            array($this, 'font_scale_field_callback'),
            'cgnpc-settings',
            'cgnpc_design_section',
            array('field' => 'font_size_medium', 'placeholder' => '15px')
        );
        
        add_settings_field(
            'font_size_large',
            __('Large Font Size Value', 'cloud-nat-port-checker'),
            array($this, 'font_scale_field_callback'),
            'cgnpc-settings',
            'cgnpc_design_section',
            array('field' => 'font_size_large', 'placeholder' => '17px')
        );
        
        add_settings_field(
            'container_width',
            __('Container Max Width', 'cloud-nat-port-checker'),
            array($this, 'text_field_callback'),
            'cgnpc-settings',
            'cgnpc_design_section',
            array('field' => 'container_width', 'placeholder' => '1180px')
        );
        
        add_settings_field(
            'container_padding',
            __('Container Padding', 'cloud-nat-port-checker'),
            array($this, 'text_field_callback'),
            'cgnpc-settings',
            'cgnpc_design_section',
            array('field' => 'container_padding', 'placeholder' => '24px')
        );
        
        add_settings_field(
            'header_title_size',
            __('Hero Title Size', 'cloud-nat-port-checker'),
            array($this, 'text_field_callback'),
            'cgnpc-settings',
            'cgnpc_design_section',
            array('field' => 'header_title_size', 'placeholder' => '2.25em')
        );
        
        add_settings_field(
            'section_title_size',
            __('Section Heading Size', 'cloud-nat-port-checker'),
            array($this, 'text_field_callback'),
            'cgnpc-settings',
            'cgnpc_design_section',
            array('field' => 'section_title_size', 'placeholder' => '1.35em')
        );
        
        add_settings_field(
            'section_title_font',
            __('Section Heading Font', 'cloud-nat-port-checker'),
            array($this, 'font_family_field_callback'),
            'cgnpc-settings',
            'cgnpc_design_section',
            array('field' => 'section_title_font')
        );
        
        // Guides Section
        add_settings_section(
            'cgnpc_guides_section',
            __('Useful Guides', 'cloud-nat-port-checker'),
            array($this, 'guides_section_intro'),
            'cgnpc-settings'
        );
        
        add_settings_field(
            'guides',
            __('Guide Cards', 'cloud-nat-port-checker'),
            array($this, 'guides_field_callback'),
            'cgnpc-settings',
            'cgnpc_guides_section'
        );
        
        // Advanced / Logging
        add_settings_section(
            'cgnpc_advanced_section',
            __('Advanced & Diagnostics', 'cloud-nat-port-checker'),
            array($this, 'advanced_section_intro'),
            'cgnpc-settings'
        );
        
        add_settings_field(
            'logging_enabled',
            __('Enable Diagnostic Logging', 'cloud-nat-port-checker'),
            array($this, 'checkbox_field_callback'),
            'cgnpc-settings',
            'cgnpc_advanced_section',
            array('field' => 'logging_enabled')
        );
        
        add_settings_field(
            'logs_view',
            __('Current Log Snapshot', 'cloud-nat-port-checker'),
            array($this, 'logs_field_callback'),
            'cgnpc-settings',
            'cgnpc_advanced_section'
        );
    }
    
    public function sanitize_settings($input) {
        $defaults = self::get_default_settings();
        $sanitized = array();
        
        $color_fields = array('primary_color', 'secondary_color', 'bg_color_dark', 'bg_color_light', 'text_color_dark', 'text_color_light');
        foreach ($color_fields as $field) {
            $sanitized[$field] = $this->sanitize_color(isset($input[$field]) ? $input[$field] : '', $defaults[$field]);
        }
        
        $sanitized['font_size'] = isset($input['font_size']) && in_array($input['font_size'], array('small', 'medium', 'large'), true)
            ? $input['font_size']
            : $defaults['font_size'];
        
        $font_value_fields = array('font_size_small', 'font_size_medium', 'font_size_large');
        foreach ($font_value_fields as $field) {
            $sanitized[$field] = $this->sanitize_font_size(isset($input[$field]) ? $input[$field] : '', $defaults[$field]);
        }
        
        $sanitized['container_width'] = $this->sanitize_dimension(isset($input['container_width']) ? $input['container_width'] : '', $defaults['container_width']);
        $sanitized['container_padding'] = $this->sanitize_dimension(isset($input['container_padding']) ? $input['container_padding'] : '', $defaults['container_padding']);
        $sanitized['header_title_size'] = $this->sanitize_dimension(isset($input['header_title_size']) ? $input['header_title_size'] : '', $defaults['header_title_size']);
        $sanitized['section_title_size'] = $this->sanitize_dimension(isset($input['section_title_size']) ? $input['section_title_size'] : '', $defaults['section_title_size']);
        $sanitized['section_title_font'] = $this->sanitize_font_family(isset($input['section_title_font']) ? $input['section_title_font'] : '', $defaults['section_title_font']);
        
        $sanitized['guides'] = array();
        if (!empty($input['guides']) && is_array($input['guides'])) {
            foreach ($input['guides'] as $guide) {
                $title = isset($guide['title']) ? sanitize_text_field($guide['title']) : '';
                if ('' === $title) {
                    continue;
                }
                $sanitized['guides'][] = array(
                    'title' => $title,
                    'image' => isset($guide['image']) ? esc_url_raw($guide['image']) : '',
                    'excerpt' => isset($guide['excerpt']) ? sanitize_textarea_field($guide['excerpt']) : '',
                    'link' => isset($guide['link']) ? esc_url_raw($guide['link']) : ''
                );
            }
        }
        if (empty($sanitized['guides'])) {
            $sanitized['guides'] = $defaults['guides'];
        }
        
        $sanitized['logging_enabled'] = isset($input['logging_enabled']) ? 1 : 0;
        
        return $sanitized;
    }
    
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        $options = $this->get_options();
        ?>
        <div class="wrap cgnpc-admin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div class="cgnpc-admin-header">
                <p><?php esc_html_e('Craft a premium experience for your readers. Adjust the visual design, typography scale, and supporting guides for the Cloud Gaming NAT & Port Checker.', 'cloud-nat-port-checker'); ?></p>
            </div>
            <form method="post" action="options.php">
                <?php
                settings_fields('cgnpc_settings_group');
                do_settings_sections('cgnpc-settings');
                submit_button(__('Save Plugin Settings', 'cloud-nat-port-checker'));
                ?>
            </form>
            
            <h2 class="cgnpc-shortcode-heading"><?php esc_html_e('Available Shortcodes', 'cloud-nat-port-checker'); ?></h2>
            <div class="cgnpc-shortcode-grid">
                <div class="cgnpc-shortcode-card">
                    <code>[cloud_nat_port_checker]</code>
                    <p><?php esc_html_e('Full interface with NAT checker, port checker, device info, router tips, and guides.', 'cloud-nat-port-checker'); ?></p>
                </div>
                <div class="cgnpc-shortcode-card">
                    <code>[cloud_nat_checker]</code>
                    <p><?php esc_html_e('Isolated NAT checker module for lightweight embeds.', 'cloud-nat-port-checker'); ?></p>
                </div>
                <div class="cgnpc-shortcode-card">
                    <code>[cloud_port_checker]</code>
                    <p><?php esc_html_e('Focused port analyzer for preset and custom port scans.', 'cloud-nat-port-checker'); ?></p>
                </div>
            </div>
            <?php if (!empty($options['logging_enabled'])) : ?>
                <div class="cgnpc-logging-note">
                    <p><?php esc_html_e('Diagnostic logging is enabled. Logs are stored in the WordPress database and can be cleared at any time.', 'cloud-nat-port-checker'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    public function design_section_intro() {
        echo '<p>' . esc_html__('Define your brand palette, typography rhythm, and responsive layout controls. All values cascade through CSS variables on the front end.', 'cloud-nat-port-checker') . '</p>';
    }
    
    public function guides_section_intro() {
        echo '<p>' . esc_html__('Showcase high-impact tutorials or news from your cloud gaming blog. These cards display beneath the diagnostic tools.', 'cloud-nat-port-checker') . '</p>';
    }
    
    public function advanced_section_intro() {
        echo '<p>' . esc_html__('Enable lightweight diagnostic logging to capture anonymized checker events for troubleshooting. Disable to avoid additional storage.', 'cloud-nat-port-checker') . '</p>';
    }
    
    public function color_field_callback($args) {
        $options = $this->get_options();
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        printf(
            '<input type="text" class="cgnpc-color-field" name="%1$s[%2$s]" value="%3$s" data-default-color="%4$s" />',
            esc_attr($this->option_name),
            esc_attr($field),
            esc_attr($value),
            esc_attr(self::get_default_settings()[$field])
        );
    }
    
    public function text_field_callback($args) {
        $options = $this->get_options();
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        printf(
            '<input type="text" class="regular-text" name="%1$s[%2$s]" value="%3$s" placeholder="%4$s" />',
            esc_attr($this->option_name),
            esc_attr($field),
            esc_attr($value),
            esc_attr($placeholder)
        );
    }
    
    public function font_size_choice_callback() {
        $options = $this->get_options();
        $value = isset($options['font_size']) ? $options['font_size'] : 'small';
        ?>
        <select name="<?php echo esc_attr($this->option_name); ?>[font_size]">
            <option value="small" <?php selected($value, 'small'); ?>><?php esc_html_e('Small (default)', 'cloud-nat-port-checker'); ?></option>
            <option value="medium" <?php selected($value, 'medium'); ?>><?php esc_html_e('Medium', 'cloud-nat-port-checker'); ?></option>
            <option value="large" <?php selected($value, 'large'); ?>><?php esc_html_e('Large', 'cloud-nat-port-checker'); ?></option>
        </select>
        <p class="description"><?php esc_html_e('Visitors can switch between presets instantly. Adjust the base values below.', 'cloud-nat-port-checker'); ?></p>
        <?php
    }
    
    public function font_scale_field_callback($args) {
        $options = $this->get_options();
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        printf(
            '<input type="text" class="regular-text" name="%1$s[%2$s]" value="%3$s" placeholder="%4$s" />',
            esc_attr($this->option_name),
            esc_attr($field),
            esc_attr($value),
            esc_attr($placeholder)
        );
        echo '<p class="description">' . esc_html__('Supports px, rem, or em units.', 'cloud-nat-port-checker') . '</p>';
    }
    
    public function guides_field_callback() {
        $options = $this->get_options();
        $guides = isset($options['guides']) && is_array($options['guides']) ? $options['guides'] : array();
        if (empty($guides)) {
            $guides[] = array('title' => '', 'image' => '', 'excerpt' => '', 'link' => '');
        }
        ?>
        <div id="cgnpc-guides-container">
            <?php foreach ($guides as $index => $guide) : ?>
                <div class="cgnpc-guide-item" data-index="<?php echo esc_attr($index); ?>">
                    <header>
                        <strong><?php printf(esc_html__('Guide #%d', 'cloud-nat-port-checker'), $index + 1); ?></strong>
                        <button type="button" class="button button-link-delete cgnpc-remove-guide" aria-label="<?php esc_attr_e('Remove guide', 'cloud-nat-port-checker'); ?>">&times;</button>
                    </header>
                    <div class="cgnpc-guide-fields">
                        <label>
                            <span><?php esc_html_e('Title', 'cloud-nat-port-checker'); ?></span>
                            <input type="text" name="<?php echo esc_attr($this->option_name); ?>[guides][<?php echo esc_attr($index); ?>][title]" value="<?php echo esc_attr($guide['title']); ?>" class="regular-text" />
                        </label>
                        <label>
                            <span><?php esc_html_e('Image URL', 'cloud-nat-port-checker'); ?></span>
                            <input type="text" name="<?php echo esc_attr($this->option_name); ?>[guides][<?php echo esc_attr($index); ?>][image]" value="<?php echo esc_url($guide['image']); ?>" class="regular-text cgnpc-image-url" />
                            <button type="button" class="button cgnpc-upload-image"><?php esc_html_e('Upload', 'cloud-nat-port-checker'); ?></button>
                        </label>
                        <label>
                            <span><?php esc_html_e('Excerpt', 'cloud-nat-port-checker'); ?></span>
                            <textarea name="<?php echo esc_attr($this->option_name); ?>[guides][<?php echo esc_attr($index); ?>][excerpt]" rows="3" class="large-text"><?php echo esc_textarea($guide['excerpt']); ?></textarea>
                        </label>
                        <label>
                            <span><?php esc_html_e('Link', 'cloud-nat-port-checker'); ?></span>
                            <input type="url" name="<?php echo esc_attr($this->option_name); ?>[guides][<?php echo esc_attr($index); ?>][link]" value="<?php echo esc_url($guide['link']); ?>" class="regular-text" />
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="cgnpc-add-guide" class="button">+ <?php esc_html_e('Add Guide Card', 'cloud-nat-port-checker'); ?></button>
        <p class="description"><?php esc_html_e('Guides render as responsive cards. Keep excerpts concise (max ~120 characters).', 'cloud-nat-port-checker'); ?></p>
        <?php
    }
    
    public function checkbox_field_callback($args) {
        $options = $this->get_options();
        $field = $args['field'];
        $value = !empty($options[$field]) ? 1 : 0;
        printf(
            '<label><input type="checkbox" name="%1$s[%2$s]" value="1" %3$s /> %4$s</label>',
            esc_attr($this->option_name),
            esc_attr($field),
            checked($value, 1, false),
            esc_html__('Record anonymized checker activity (results, timestamps, IP truncated).', 'cloud-nat-port-checker')
        );
    }
    
    public function logs_field_callback() {
        $logs = get_option('cgnpc_logs', array());
        if (!empty($logs) && is_array($logs)) {
            $logs_text = implode("\n", array_slice($logs, -50));
        } else {
            $logs_text = __('No diagnostic events captured yet.', 'cloud-nat-port-checker');
        }
        echo '<textarea readonly rows="6" class="large-text code">' . esc_textarea($logs_text) . '</textarea>';
        $clear_url = wp_nonce_url(admin_url('admin-post.php?action=cgnpc_clear_logs'), 'cgnpc_clear_logs');
        echo '<p><a class="button" href="' . esc_url($clear_url) . '">' . esc_html__('Clear Logs', 'cloud-nat-port-checker') . '</a></p>';
    }
    
    public function handle_clear_logs() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'cloud-nat-port-checker'));
        }
        check_admin_referer('cgnpc_clear_logs');
        delete_option('cgnpc_logs');
        $redirect = add_query_arg('cgnpc_logs_cleared', 1, wp_get_referer() ? wp_get_referer() : admin_url('options-general.php?page=cgnpc-settings'));
        wp_safe_redirect($redirect);
        exit;
    }
    
    public function maybe_show_notices() {
        if (!isset($_GET['page']) || 'cgnpc-settings' !== $_GET['page']) { // phpcs:ignore WordPress.Security.NonceVerification
            return;
        }
        if (isset($_GET['cgnpc_logs_cleared'])) { // phpcs:ignore WordPress.Security.NonceVerification
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Cloud Gaming NAT & Port Checker logs have been cleared.', 'cloud-nat-port-checker') . '</p></div>';
        }
    }
    
    private function sanitize_color($color, $default) {
        $sanitized = sanitize_hex_color($color);
        return $sanitized ? $sanitized : $default;
    }
    
    private function sanitize_font_size($value, $default) {
        if (empty($value)) {
            return $default;
        }
        $value = trim($value);
        if (preg_match('/^\d+(\.\d+)?(px|rem|em)$/', $value)) {
            return $value;
        }
        return $default;
    }
    
    private function sanitize_dimension($value, $default) {
        if (empty($value)) {
            return $default;
        }
        $value = trim($value);
        if (preg_match('/^\d+(\.\d+)?(px|rem|em|%)$/', $value)) {
            return $value;
        }
        return $default;
    }
    
    private function sanitize_font_family($value, $default) {
        if (empty($value)) {
            return $default;
        }
        $value = trim($value);
        $value = preg_replace('/[^a-zA-Z0-9\s,\-\'"]/', '', $value);
        return !empty($value) ? $value : $default;
    }
    
    public function font_family_field_callback($args) {
        $options = $this->get_options();
        $field = $args['field'];
        $value = isset($options[$field]) ? $options[$field] : '';
        printf(
            '<input type="text" class="large-text" name="%1$s[%2$s]" value="%3$s" placeholder="%4$s" />',
            esc_attr($this->option_name),
            esc_attr($field),
            esc_attr($value),
            'Space Grotesk, "Segoe UI", sans-serif'
        );
        echo '<p class="description">' . esc_html__('CSS font-family value. Use quotes for multi-word fonts.', 'cloud-nat-port-checker') . '</p>';
    }
    
    private function get_options() {
        $options = get_option($this->option_name, array());
        return wp_parse_args($options, self::get_default_settings());
    }
}
