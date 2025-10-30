<?php
/**
 * Admin settings page for the Ultimate NAT & Port Checker plugin.
 *
 * @package UltimateNATPortChecker
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('UNPC_Admin')) {
    /**
     * Admin settings and configuration.
     */
    class UNPC_Admin {
        /**
         * Constructor.
         */
        public function __construct() {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_init', array($this, 'register_settings'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        }

        /**
         * Add menu items to WordPress admin.
         *
         * @return void
         */
        public function add_admin_menu() {
            add_options_page(
                __('NAT & Port Checker Settings', 'ultimate-nat-port-checker'),
                __('NAT & Port Checker', 'ultimate-nat-port-checker'),
                'manage_options',
                'unpc-settings',
                array($this, 'render_settings_page')
            );
        }

        /**
         * Register plugin settings.
         *
         * @return void
         */
        public function register_settings() {
            register_setting('unpc_settings_group', 'unpc_settings', array($this, 'sanitize_settings'));

            add_settings_section(
                'unpc_general_section',
                __('General Settings', 'ultimate-nat-port-checker'),
                array($this, 'render_general_section_description'),
                'unpc-settings'
            );

            add_settings_field(
                'theme',
                __('Default Theme', 'ultimate-nat-port-checker'),
                array($this, 'render_theme_field'),
                'unpc-settings',
                'unpc_general_section'
            );

            add_settings_field(
                'accent_color',
                __('Accent Color', 'ultimate-nat-port-checker'),
                array($this, 'render_accent_color_field'),
                'unpc-settings',
                'unpc_general_section'
            );

            add_settings_field(
                'enable_animations',
                __('Enable Animations', 'ultimate-nat-port-checker'),
                array($this, 'render_enable_animations_field'),
                'unpc-settings',
                'unpc_general_section'
            );

            add_settings_field(
                'animation_speed',
                __('Animation Speed', 'ultimate-nat-port-checker'),
                array($this, 'render_animation_speed_field'),
                'unpc-settings',
                'unpc_general_section'
            );

            add_settings_field(
                'container_width',
                __('Container Width (px)', 'ultimate-nat-port-checker'),
                array($this, 'render_container_width_field'),
                'unpc-settings',
                'unpc_general_section'
            );

            add_settings_section(
                'unpc_modules_section',
                __('Feature Modules', 'ultimate-nat-port-checker'),
                array($this, 'render_modules_section_description'),
                'unpc-settings'
            );

            $modules = array(
                'enable_nat_checker' => __('NAT Checker', 'ultimate-nat-port-checker'),
                'enable_port_checker' => __('Port Checker', 'ultimate-nat-port-checker'),
                'enable_router_logins' => __('Popular Router Logins', 'ultimate-nat-port-checker'),
                'enable_device_info' => __('Device Information', 'ultimate-nat-port-checker'),
                'enable_useful_links' => __('Useful Links', 'ultimate-nat-port-checker'),
                'enable_guides' => __('Quick Guides & Tips', 'ultimate-nat-port-checker'),
            );

            foreach ($modules as $key => $label) {
                add_settings_field(
                    $key,
                    $label,
                    array($this, 'render_checkbox_field'),
                    'unpc-settings',
                    'unpc_modules_section',
                    array('field' => $key)
                );
            }

            add_settings_section(
                'unpc_links_section',
                __('Useful Links Configuration', 'ultimate-nat-port-checker'),
                array($this, 'render_links_section_description'),
                'unpc-settings'
            );

            add_settings_field(
                'useful_link_1_text',
                __('Link 1 – Text', 'ultimate-nat-port-checker'),
                array($this, 'render_text_field'),
                'unpc-settings',
                'unpc_links_section',
                array('field' => 'useful_link_1_text')
            );

            add_settings_field(
                'useful_link_1_url',
                __('Link 1 – URL', 'ultimate-nat-port-checker'),
                array($this, 'render_text_field'),
                'unpc-settings',
                'unpc_links_section',
                array('field' => 'useful_link_1_url')
            );

            add_settings_field(
                'useful_link_2_text',
                __('Link 2 – Text', 'ultimate-nat-port-checker'),
                array($this, 'render_text_field'),
                'unpc-settings',
                'unpc_links_section',
                array('field' => 'useful_link_2_text')
            );

            add_settings_field(
                'useful_link_2_url',
                __('Link 2 – URL', 'ultimate-nat-port-checker'),
                array($this, 'render_text_field'),
                'unpc-settings',
                'unpc_links_section',
                array('field' => 'useful_link_2_url')
            );

            add_settings_section(
                'unpc_advanced_section',
                __('Advanced Configuration', 'ultimate-nat-port-checker'),
                array($this, 'render_advanced_section_description'),
                'unpc-settings'
            );

            add_settings_field(
                'custom_quick_tip',
                __('Custom Quick Tip', 'ultimate-nat-port-checker'),
                array($this, 'render_textarea_field'),
                'unpc-settings',
                'unpc_advanced_section',
                array('field' => 'custom_quick_tip')
            );

            add_settings_field(
                'api_timeout',
                __('API Timeout (seconds)', 'ultimate-nat-port-checker'),
                array($this, 'render_number_field'),
                'unpc-settings',
                'unpc_advanced_section',
                array('field' => 'api_timeout', 'min' => 5, 'max' => 60)
            );
        }

        /**
         * Sanitize and validate settings on save.
         *
         * @param array $input Raw settings input.
         * @return array Sanitized settings.
         */
        public function sanitize_settings($input) {
            $sanitized = array();
            $defaults = unpc_default_settings();

            $theme_input = isset($input['theme']) ? $input['theme'] : $defaults['theme'];
            $sanitized['theme'] = in_array($theme_input, array('light', 'dark'), true) ? $theme_input : $defaults['theme'];

            $accent_input = isset($input['accent_color']) ? $input['accent_color'] : $defaults['accent_color'];
            $sanitized['accent_color'] = sanitize_hex_color($accent_input) ? sanitize_hex_color($accent_input) : $defaults['accent_color'];

            $sanitized['enable_animations'] = !empty($input['enable_animations']);

            $animation_input = isset($input['animation_speed']) ? $input['animation_speed'] : $defaults['animation_speed'];
            $sanitized['animation_speed'] = in_array($animation_input, array('slow', 'normal', 'fast'), true) ? $animation_input : $defaults['animation_speed'];

            $width_input = isset($input['container_width']) ? absint($input['container_width']) : (int) $defaults['container_width'];
            $sanitized['container_width'] = ($width_input >= 768 && $width_input <= 2400) ? $width_input : $defaults['container_width'];

            $sanitized['enable_nat_checker'] = !empty($input['enable_nat_checker']);
            $sanitized['enable_port_checker'] = !empty($input['enable_port_checker']);
            $sanitized['enable_router_logins'] = !empty($input['enable_router_logins']);
            $sanitized['enable_device_info'] = !empty($input['enable_device_info']);
            $sanitized['enable_useful_links'] = !empty($input['enable_useful_links']);
            $sanitized['enable_guides'] = !empty($input['enable_guides']);

            $sanitized['useful_link_1_text'] = isset($input['useful_link_1_text']) ? sanitize_text_field($input['useful_link_1_text']) : '';
            $sanitized['useful_link_1_url'] = isset($input['useful_link_1_url']) ? esc_url_raw($input['useful_link_1_url']) : '';
            $sanitized['useful_link_2_text'] = isset($input['useful_link_2_text']) ? sanitize_text_field($input['useful_link_2_text']) : '';
            $sanitized['useful_link_2_url'] = isset($input['useful_link_2_url']) ? esc_url_raw($input['useful_link_2_url']) : '';

            $sanitized['custom_quick_tip'] = isset($input['custom_quick_tip']) ? sanitize_textarea_field($input['custom_quick_tip']) : $defaults['custom_quick_tip'];

            $timeout_input = isset($input['api_timeout']) ? absint($input['api_timeout']) : (int) $defaults['api_timeout'];
            $sanitized['api_timeout'] = ($timeout_input >= 5 && $timeout_input <= 60) ? $timeout_input : $defaults['api_timeout'];

            return $sanitized;
        }

        /**
         * Render the settings page HTML.
         *
         * @return void
         */
        public function render_settings_page() {
            if (!current_user_can('manage_options')) {
                return;
            }
            ?>
            <div class="wrap unpc-admin-wrap">
                <h1><?php esc_html_e('Ultimate NAT & Port Checker – Settings', 'ultimate-nat-port-checker'); ?></h1>
                <div class="unpc-admin-banner">
                    <p class="description">
                        <?php esc_html_e('Configure your NAT and Port Checker plugin to suit your needs.', 'ultimate-nat-port-checker'); ?>
                    </p>
                    <p class="description">
                        <strong><?php esc_html_e('Available Shortcodes:', 'ultimate-nat-port-checker'); ?></strong><br>
                        <code>[nat_port_checker]</code> – <?php esc_html_e('Full tool with all features', 'ultimate-nat-port-checker'); ?><br>
                        <code>[nat_checker_only]</code> – <?php esc_html_e('NAT checker only', 'ultimate-nat-port-checker'); ?><br>
                        <code>[port_checker_only]</code> – <?php esc_html_e('Port checker only', 'ultimate-nat-port-checker'); ?>
                    </p>
                    <p class="description">
                        <strong><?php esc_html_e('Key Features:', 'ultimate-nat-port-checker'); ?></strong>
                        <?php esc_html_e('WebRTC NAT detection, port diagnostics, device info, router guides, and responsive themes.', 'ultimate-nat-port-checker'); ?>
                    </p>
                </div>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('unpc_settings_group');
                    do_settings_sections('unpc-settings');
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }

        /**
         * Render section description for general settings.
         *
         * @return void
         */
        public function render_general_section_description() {
            echo '<p>' . esc_html__('Configure the default appearance and behavior of the tool.', 'ultimate-nat-port-checker') . '</p>';
        }

        /**
         * Render section description for module toggles.
         *
         * @return void
         */
        public function render_modules_section_description() {
            echo '<p>' . esc_html__('Enable or disable specific feature modules to show only the sections you need.', 'ultimate-nat-port-checker') . '</p>';
        }

        /**
         * Render section description for custom links.
         *
         * @return void
         */
        public function render_links_section_description() {
            echo '<p>' . esc_html__('Define the two links that will appear in the "Useful Links" section.', 'ultimate-nat-port-checker') . '</p>';
        }

        /**
         * Render section description for advanced settings.
         *
         * @return void
         */
        public function render_advanced_section_description() {
            echo '<p>' . esc_html__('Advanced options for fine-tuning plugin behavior.', 'ultimate-nat-port-checker') . '</p>';
        }

        /**
         * Render theme dropdown field.
         *
         * @return void
         */
        public function render_theme_field() {
            $settings = unpc_get_settings();
            $current = isset($settings['theme']) ? $settings['theme'] : 'light';
            ?>
            <select name="unpc_settings[theme]" id="unpc_theme">
                <option value="light" <?php selected($current, 'light'); ?>><?php esc_html_e('Light', 'ultimate-nat-port-checker'); ?></option>
                <option value="dark" <?php selected($current, 'dark'); ?>><?php esc_html_e('Dark', 'ultimate-nat-port-checker'); ?></option>
            </select>
            <p class="description"><?php esc_html_e('Choose the default theme. Visitors can toggle between themes on the front end.', 'ultimate-nat-port-checker'); ?></p>
            <?php
        }

        /**
         * Render accent color field.
         *
         * @return void
         */
        public function render_accent_color_field() {
            $settings = unpc_get_settings();
            $current = isset($settings['accent_color']) ? $settings['accent_color'] : '#3a7afe';
            ?>
            <input type="color" name="unpc_settings[accent_color]" id="unpc_accent_color" value="<?php echo esc_attr($current); ?>">
            <p class="description"><?php esc_html_e('Select an accent color for buttons and highlights.', 'ultimate-nat-port-checker'); ?></p>
            <?php
        }

        /**
         * Render enable animations checkbox.
         *
         * @return void
         */
        public function render_enable_animations_field() {
            $settings = unpc_get_settings();
            $checked = isset($settings['enable_animations']) && $settings['enable_animations'];
            ?>
            <label>
                <input type="checkbox" name="unpc_settings[enable_animations]" value="1" <?php checked($checked); ?>>
                <?php esc_html_e('Enable smooth animations and transitions.', 'ultimate-nat-port-checker'); ?>
            </label>
            <?php
        }

        /**
         * Render animation speed dropdown.
         *
         * @return void
         */
        public function render_animation_speed_field() {
            $settings = unpc_get_settings();
            $current = isset($settings['animation_speed']) ? $settings['animation_speed'] : 'normal';
            ?>
            <select name="unpc_settings[animation_speed]" id="unpc_animation_speed">
                <option value="slow" <?php selected($current, 'slow'); ?>><?php esc_html_e('Slow', 'ultimate-nat-port-checker'); ?></option>
                <option value="normal" <?php selected($current, 'normal'); ?>><?php esc_html_e('Normal', 'ultimate-nat-port-checker'); ?></option>
                <option value="fast" <?php selected($current, 'fast'); ?>><?php esc_html_e('Fast', 'ultimate-nat-port-checker'); ?></option>
            </select>
            <p class="description"><?php esc_html_e('Set the speed of animations throughout the plugin.', 'ultimate-nat-port-checker'); ?></p>
            <?php
        }

        /**
         * Render container width field.
         *
         * @return void
         */
        public function render_container_width_field() {
            $settings = unpc_get_settings();
            $current = isset($settings['container_width']) ? $settings['container_width'] : 1400;
            ?>
            <input type="number" name="unpc_settings[container_width]" id="unpc_container_width" value="<?php echo esc_attr($current); ?>" min="768" max="2400" step="10">
            <p class="description"><?php esc_html_e('Set the maximum container width in pixels (768–2400).', 'ultimate-nat-port-checker'); ?></p>
            <?php
        }

        /**
         * Render a generic checkbox field.
         *
         * @param array $args Field arguments.
         * @return void
         */
        public function render_checkbox_field($args) {
            $settings = unpc_get_settings();
            $field = $args['field'];
            $checked = isset($settings[$field]) && $settings[$field];
            ?>
            <label>
                <input type="checkbox" name="unpc_settings[<?php echo esc_attr($field); ?>]" value="1" <?php checked($checked); ?>>
                <?php esc_html_e('Enable this feature module', 'ultimate-nat-port-checker'); ?>
            </label>
            <?php
        }

        /**
         * Render a generic text input field.
         *
         * @param array $args Field arguments.
         * @return void
         */
        public function render_text_field($args) {
            $settings = unpc_get_settings();
            $field = $args['field'];
            $current = isset($settings[$field]) ? $settings[$field] : '';
            ?>
            <input type="text" name="unpc_settings[<?php echo esc_attr($field); ?>]" value="<?php echo esc_attr($current); ?>" class="regular-text">
            <?php
        }

        /**
         * Render a generic textarea field.
         *
         * @param array $args Field arguments.
         * @return void
         */
        public function render_textarea_field($args) {
            $settings = unpc_get_settings();
            $field = $args['field'];
            $current = isset($settings[$field]) ? $settings[$field] : '';
            ?>
            <textarea name="unpc_settings[<?php echo esc_attr($field); ?>]" rows="4" class="large-text"><?php echo esc_textarea($current); ?></textarea>
            <p class="description"><?php esc_html_e('This message will appear in the quick tips section.', 'ultimate-nat-port-checker'); ?></p>
            <?php
        }

        /**
         * Render a generic number field.
         *
         * @param array $args Field arguments.
         * @return void
         */
        public function render_number_field($args) {
            $settings = unpc_get_settings();
            $field = $args['field'];
            $current = isset($settings[$field]) ? $settings[$field] : '';
            $min = isset($args['min']) ? $args['min'] : '';
            $max = isset($args['max']) ? $args['max'] : '';
            ?>
            <input type="number" name="unpc_settings[<?php echo esc_attr($field); ?>]" value="<?php echo esc_attr($current); ?>" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>">
            <?php
        }

        /**
         * Enqueue admin-specific assets.
         *
         * @param string $hook Current admin page hook.
         * @return void
         */
        public function enqueue_admin_assets($hook) {
            if ('settings_page_unpc-settings' !== $hook) {
                return;
            }

            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');

            $custom_css = '
                .unpc-admin-wrap { max-width: 960px; }
                .unpc-admin-wrap h1 { margin-bottom: 0.5em; }
                .unpc-admin-wrap .description { font-size: 13px; }
                .unpc-admin-banner { background: #f0f6ff; border-left: 4px solid #3a7afe; padding: 1em 1.5em; margin: 1em 0 2em 0; border-radius: 4px; }
                .unpc-admin-banner code { background: #e0e7ff; padding: 0.125em 0.375em; border-radius: 3px; font-size: 0.95em; }
            ';
            wp_add_inline_style('wp-color-picker', $custom_css);
        }
    }
}
