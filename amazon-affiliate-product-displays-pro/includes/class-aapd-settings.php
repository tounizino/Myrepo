<?php

if (!defined('ABSPATH')) {
    exit;
}

class AAPD_Settings {

    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Amazon Affiliate Product Displays Pro', 'amazon-affiliate-displays'),
            __('Amazon Displays', 'amazon-affiliate-displays'),
            'manage_options',
            'aapd-settings',
            array($this, 'render_settings_page'),
            'dashicons-store',
            26
        );
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'aapd-settings') === false) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        wp_enqueue_style(
            'aapd-admin',
            AAPDP_PLUGIN_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            AAPDP_VERSION
        );

        wp_enqueue_style(
            'aapd-admin-preview',
            AAPDP_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            AAPDP_VERSION
        );

        wp_enqueue_script(
            'aapd-admin',
            AAPDP_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            AAPDP_VERSION,
            true
        );

        wp_localize_script(
            'aapd-admin',
            'AAPDThemes',
            array(
                'amazon_classic' => array(
                    'button_gradient_start' => '#ff9900',
                    'button_gradient_end' => '#ffcc00',
                    'accent_color' => '#146eb4',
                    'hover_color' => '#f37300',
                    'dark_mode' => 0,
                ),
                'minimal_white' => array(
                    'button_gradient_start' => '#ffffff',
                    'button_gradient_end' => '#f3f4f6',
                    'accent_color' => '#111827',
                    'hover_color' => '#2563eb',
                    'dark_mode' => 0,
                ),
                'dark_neon' => array(
                    'button_gradient_start' => '#7f5af0',
                    'button_gradient_end' => '#2cb1bc',
                    'accent_color' => '#2cb1bc',
                    'hover_color' => '#ff6f61',
                    'dark_mode' => 1,
                ),
                'glassmorphism' => array(
                    'button_gradient_start' => '#7f5af0',
                    'button_gradient_end' => '#9cecfb',
                    'accent_color' => '#ffffff',
                    'hover_color' => '#00c6fb',
                    'dark_mode' => 1,
                ),
            )
        );
    }

    public function register_settings() {
        register_setting('aapd_settings_group', 'aapd_settings', array($this, 'sanitize_settings'));

        add_settings_section(
            'aapd_credentials_section',
            __('Amazon API Credentials', 'amazon-affiliate-displays'),
            array($this, 'render_credentials_section'),
            'aapd-settings'
        );

        add_settings_section(
            'aapd_styling_section',
            __('Styling & Appearance', 'amazon-affiliate-displays'),
            array($this, 'render_styling_section'),
            'aapd-settings'
        );

        add_settings_section(
            'aapd_features_section',
            __('Feature Toggles', 'amazon-affiliate-displays'),
            array($this, 'render_features_section'),
            'aapd-settings'
        );

        add_settings_section(
            'aapd_advanced_section',
            __('Advanced Options', 'amazon-affiliate-displays'),
            array($this, 'render_advanced_section'),
            'aapd-settings'
        );
    }

    public function render_credentials_section() {
        echo '<p>' . esc_html__('Enter your Amazon Product Advertising API 5.0 credentials. These values are stored securely in the WordPress database and never exposed on the frontend.', 'amazon-affiliate-displays') . '</p>';
    }

    public function render_styling_section() {
        echo '<p>' . esc_html__('Customize the appearance of your product displays with gradients, accent colors, typography, and presets.', 'amazon-affiliate-displays') . '</p>';
    }

    public function render_features_section() {
        echo '<p>' . esc_html__('Toggle optional UI elements and animations for your layouts.', 'amazon-affiliate-displays') . '</p>';
    }

    public function render_advanced_section() {
        echo '<p>' . esc_html__('Fine-tune caching and add custom CSS overrides.', 'amazon-affiliate-displays') . '</p>';
    }

    public function sanitize_settings($input) {
        $defaults = self::get_default_settings();
        $sanitized = array();

        $sanitized['access_key'] = isset($input['access_key']) ? sanitize_text_field($input['access_key']) : $defaults['access_key'];
        $sanitized['secret_key'] = isset($input['secret_key']) ? sanitize_text_field($input['secret_key']) : $defaults['secret_key'];
        $sanitized['associate_tag'] = isset($input['associate_tag']) ? sanitize_text_field($input['associate_tag']) : $defaults['associate_tag'];
        $sanitized['region'] = isset($input['region']) ? sanitize_text_field($input['region']) : $defaults['region'];
        $sanitized['marketplace'] = isset($input['marketplace']) ? sanitize_text_field($input['marketplace']) : $defaults['marketplace'];

        $sanitized['font_choice'] = isset($input['font_choice']) && in_array($input['font_choice'], array('system', 'google'), true) ? $input['font_choice'] : $defaults['font_choice'];
        $sanitized['google_font_family'] = isset($input['google_font_family']) ? sanitize_text_field($input['google_font_family']) : $defaults['google_font_family'];

        $sanitized['button_gradient_start'] = isset($input['button_gradient_start']) ? sanitize_hex_color($input['button_gradient_start']) : $defaults['button_gradient_start'];
        $sanitized['button_gradient_end'] = isset($input['button_gradient_end']) ? sanitize_hex_color($input['button_gradient_end']) : $defaults['button_gradient_end'];
        $sanitized['accent_color'] = isset($input['accent_color']) ? sanitize_hex_color($input['accent_color']) : $defaults['accent_color'];
        $sanitized['hover_color'] = isset($input['hover_color']) ? sanitize_hex_color($input['hover_color']) : $defaults['hover_color'];

        $sanitized['show_prime_badge'] = !empty($input['show_prime_badge']) ? 1 : 0;
        $sanitized['show_review_count'] = !empty($input['show_review_count']) ? 1 : 0;
        $sanitized['show_discount_badge'] = !empty($input['show_discount_badge']) ? 1 : 0;
        $sanitized['show_price'] = !empty($input['show_price']) ? 1 : 0;
        $sanitized['enable_animations'] = !empty($input['enable_animations']) ? 1 : 0;
        $sanitized['dark_mode'] = !empty($input['dark_mode']) ? 1 : 0;

        $sanitized['theme_preset'] = isset($input['theme_preset']) ? sanitize_text_field($input['theme_preset']) : $defaults['theme_preset'];
        $sanitized['custom_css'] = isset($input['custom_css']) ? wp_strip_all_tags($input['custom_css']) : $defaults['custom_css'];
        $sanitized['cache_duration'] = isset($input['cache_duration']) ? absint($input['cache_duration']) : $defaults['cache_duration'];

        return $sanitized;
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings = $this->get_settings();
        ?>
        <div class="wrap aapd-settings-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields('aapd_settings_group');
                ?>

                <div class="aapd-settings-grid">
                    <div class="aapd-settings-main">
                        <div class="aapd-settings-card">
                            <h2><?php esc_html_e('Amazon API Credentials', 'amazon-affiliate-displays'); ?></h2>
                            <table class="form-table" role="presentation">
                                <tbody>
                                <tr>
                                    <th scope="row"><label for="access_key"><?php esc_html_e('Access Key', 'amazon-affiliate-displays'); ?></label></th>
                                    <td>
                                        <input type="text" id="access_key" name="aapd_settings[access_key]" value="<?php echo esc_attr($settings['access_key']); ?>" class="regular-text" autocomplete="off" />
                                        <p class="description"><?php esc_html_e('Your Amazon PA-API 5.0 Access Key.', 'amazon-affiliate-displays'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="secret_key"><?php esc_html_e('Secret Key', 'amazon-affiliate-displays'); ?></label></th>
                                    <td>
                                        <input type="password" id="secret_key" name="aapd_settings[secret_key]" value="<?php echo esc_attr($settings['secret_key']); ?>" class="regular-text" autocomplete="new-password" />
                                        <p class="description"><?php esc_html_e('Your Amazon PA-API 5.0 Secret Key.', 'amazon-affiliate-displays'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="associate_tag"><?php esc_html_e('Associate Tag', 'amazon-affiliate-displays'); ?></label></th>
                                    <td>
                                        <input type="text" id="associate_tag" name="aapd_settings[associate_tag]" value="<?php echo esc_attr($settings['associate_tag']); ?>" class="regular-text" />
                                        <p class="description"><?php esc_html_e('Your Amazon Associates tracking ID.', 'amazon-affiliate-displays'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="region"><?php esc_html_e('PA-API Region', 'amazon-affiliate-displays'); ?></label></th>
                                    <td>
                                        <select id="region" name="aapd_settings[region]">
                                            <option value="us-east-1" <?php selected($settings['region'], 'us-east-1'); ?>><?php esc_html_e('North America (us-east-1)', 'amazon-affiliate-displays'); ?></option>
                                            <option value="eu-west-1" <?php selected($settings['region'], 'eu-west-1'); ?>><?php esc_html_e('Europe (eu-west-1)', 'amazon-affiliate-displays'); ?></option>
                                            <option value="us-west-2" <?php selected($settings['region'], 'us-west-2'); ?>><?php esc_html_e('Far East (us-west-2)', 'amazon-affiliate-displays'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="marketplace"><?php esc_html_e('Default Marketplace', 'amazon-affiliate-displays'); ?></label></th>
                                    <td>
                                        <select id="marketplace" name="aapd_settings[marketplace]">
                                            <option value="www.amazon.com" <?php selected($settings['marketplace'], 'www.amazon.com'); ?>><?php esc_html_e('United States', 'amazon-affiliate-displays'); ?></option>
                                            <option value="www.amazon.co.uk" <?php selected($settings['marketplace'], 'www.amazon.co.uk'); ?>><?php esc_html_e('United Kingdom', 'amazon-affiliate-displays'); ?></option>
                                            <option value="www.amazon.de" <?php selected($settings['marketplace'], 'www.amazon.de'); ?>><?php esc_html_e('Germany', 'amazon-affiliate-displays'); ?></option>
                                            <option value="www.amazon.fr" <?php selected($settings['marketplace'], 'www.amazon.fr'); ?>><?php esc_html_e('France', 'amazon-affiliate-displays'); ?></option>
                                            <option value="www.amazon.it" <?php selected($settings['marketplace'], 'www.amazon.it'); ?>><?php esc_html_e('Italy', 'amazon-affiliate-displays'); ?></option>
                                            <option value="www.amazon.es" <?php selected($settings['marketplace'], 'www.amazon.es'); ?>><?php esc_html_e('Spain', 'amazon-affiliate-displays'); ?></option>
                                            <option value="www.amazon.ca" <?php selected($settings['marketplace'], 'www.amazon.ca'); ?>><?php esc_html_e('Canada', 'amazon-affiliate-displays'); ?></option>
                                            <option value="www.amazon.co.jp" <?php selected($settings['marketplace'], 'www.amazon.co.jp'); ?>><?php esc_html_e('Japan', 'amazon-affiliate-displays'); ?></option>
                                            <option value="www.amazon.in" <?php selected($settings['marketplace'], 'www.amazon.in'); ?>><?php esc_html_e('India', 'amazon-affiliate-displays'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="aapd-settings-card">
                            <h2><?php esc_html_e('Styling & Appearance', 'amazon-affiliate-displays'); ?></h2>
                            <table class="form-table" role="presentation">
                                <tbody>
                                <tr>
                                    <th scope="row"><label for="theme_preset"><?php esc_html_e('Theme Preset', 'amazon-affiliate-displays'); ?></label></th>
                                    <td>
                                        <select id="theme_preset" name="aapd_settings[theme_preset]" class="aapd-theme-preset">
                                            <option value="amazon_classic" <?php selected($settings['theme_preset'], 'amazon_classic'); ?>><?php esc_html_e('Amazon Classic', 'amazon-affiliate-displays'); ?></option>
                                            <option value="minimal_white" <?php selected($settings['theme_preset'], 'minimal_white'); ?>><?php esc_html_e('Minimal White', 'amazon-affiliate-displays'); ?></option>
                                            <option value="dark_neon" <?php selected($settings['theme_preset'], 'dark_neon'); ?>><?php esc_html_e('Dark Neon', 'amazon-affiliate-displays'); ?></option>
                                            <option value="glassmorphism" <?php selected($settings['theme_preset'], 'glassmorphism'); ?>><?php esc_html_e('Glassmorphism', 'amazon-affiliate-displays'); ?></option>
                                        </select>
                                        <p class="description"><?php esc_html_e('Select a preset to quickly load curated colors. You can fine-tune afterwards.', 'amazon-affiliate-displays'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="font_choice"><?php esc_html_e('Typography', 'amazon-affiliate-displays'); ?></label></th>
                                    <td>
                                        <select id="font_choice" name="aapd_settings[font_choice]" class="aapd-font-choice">
                                            <option value="system" <?php selected($settings['font_choice'], 'system'); ?>><?php esc_html_e('System Font Stack', 'amazon-affiliate-displays'); ?></option>
                                            <option value="google" <?php selected($settings['font_choice'], 'google'); ?>><?php esc_html_e('Google Font', 'amazon-affiliate-displays'); ?></option>
                                        </select>
                                        <div class="aapd-google-font-field" <?php if ($settings['font_choice'] !== 'google') echo 'style="display:none;"'; ?>>
                                            <label for="google_font_family"><?php esc_html_e('Google Font Family', 'amazon-affiliate-displays'); ?></label>
                                            <input type="text" id="google_font_family" name="aapd_settings[google_font_family]" value="<?php echo esc_attr($settings['google_font_family']); ?>" class="regular-text" />
                                            <p class="description"><?php esc_html_e('Example: Inter:wght@400;500;700', 'amazon-affiliate-displays'); ?></p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="button_gradient_start"><?php esc_html_e('Button Gradient Start', 'amazon-affiliate-displays'); ?></label></th>
                                    <td><input type="text" id="button_gradient_start" name="aapd_settings[button_gradient_start]" value="<?php echo esc_attr($settings['button_gradient_start']); ?>" class="aapd-color-picker" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="button_gradient_end"><?php esc_html_e('Button Gradient End', 'amazon-affiliate-displays'); ?></label></th>
                                    <td><input type="text" id="button_gradient_end" name="aapd_settings[button_gradient_end]" value="<?php echo esc_attr($settings['button_gradient_end']); ?>" class="aapd-color-picker" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="accent_color"><?php esc_html_e('Accent Color', 'amazon-affiliate-displays'); ?></label></th>
                                    <td><input type="text" id="accent_color" name="aapd_settings[accent_color]" value="<?php echo esc_attr($settings['accent_color']); ?>" class="aapd-color-picker" /></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="hover_color"><?php esc_html_e('Hover Color', 'amazon-affiliate-displays'); ?></label></th>
                                    <td><input type="text" id="hover_color" name="aapd_settings[hover_color]" value="<?php echo esc_attr($settings['hover_color']); ?>" class="aapd-color-picker" /></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="aapd-settings-card">
                            <h2><?php esc_html_e('Feature Toggles', 'amazon-affiliate-displays'); ?></h2>
                            <table class="form-table" role="presentation">
                                <tbody>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Display Elements', 'amazon-affiliate-displays'); ?></th>
                                    <td>
                                        <fieldset>
                                            <label><input type="checkbox" name="aapd_settings[show_price]" value="1" <?php checked($settings['show_price'], 1); ?> /> <?php esc_html_e('Show product price', 'amazon-affiliate-displays'); ?></label><br />
                                            <label><input type="checkbox" name="aapd_settings[show_prime_badge]" value="1" <?php checked($settings['show_prime_badge'], 1); ?> /> <?php esc_html_e('Show Prime badge', 'amazon-affiliate-displays'); ?></label><br />
                                            <label><input type="checkbox" name="aapd_settings[show_review_count]" value="1" <?php checked($settings['show_review_count'], 1); ?> /> <?php esc_html_e('Show star ratings & reviews', 'amazon-affiliate-displays'); ?></label><br />
                                            <label><input type="checkbox" name="aapd_settings[show_discount_badge]" value="1" <?php checked($settings['show_discount_badge'], 1); ?> /> <?php esc_html_e('Show discount badge when available', 'amazon-affiliate-displays'); ?></label><br />
                                            <label><input type="checkbox" name="aapd_settings[enable_animations]" value="1" <?php checked($settings['enable_animations'], 1); ?> /> <?php esc_html_e('Enable hover & entrance animations', 'amazon-affiliate-displays'); ?></label><br />
                                            <label><input type="checkbox" name="aapd_settings[dark_mode]" value="1" <?php checked($settings['dark_mode'], 1); ?> /> <?php esc_html_e('Enable dark mode styling', 'amazon-affiliate-displays'); ?></label>
                                        </fieldset>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="aapd-settings-card">
                            <h2><?php esc_html_e('Advanced Options', 'amazon-affiliate-displays'); ?></h2>
                            <table class="form-table" role="presentation">
                                <tbody>
                                <tr>
                                    <th scope="row"><label for="cache_duration"><?php esc_html_e('Cache duration (seconds)', 'amazon-affiliate-displays'); ?></label></th>
                                    <td>
                                        <input type="number" id="cache_duration" name="aapd_settings[cache_duration]" value="<?php echo esc_attr($settings['cache_duration']); ?>" min="0" step="1" class="small-text" />
                                        <p class="description"><?php esc_html_e('Reduce API calls by caching responses. Set to 0 to disable caching.', 'amazon-affiliate-displays'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="custom_css"><?php esc_html_e('Custom CSS', 'amazon-affiliate-displays'); ?></label></th>
                                    <td>
                                        <textarea id="custom_css" name="aapd_settings[custom_css]" rows="8" class="large-text code" spellcheck="false"><?php echo esc_textarea($settings['custom_css']); ?></textarea>
                                        <p class="description"><?php esc_html_e('Add custom CSS to further personalize your layouts.', 'amazon-affiliate-displays'); ?></p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <?php submit_button(); ?>
                    </div>

                    <div class="aapd-settings-sidebar">
                        <div class="aapd-sidebar-card aapd-preview-card">
                            <h3><?php esc_html_e('Live Preview', 'amazon-affiliate-displays'); ?></h3>
                            <p class="description"><?php esc_html_e('Preview updates instantly as you change colors and presets.', 'amazon-affiliate-displays'); ?></p>
                            <div class="aapd-preview-wrapper">
                                <div class="aapd-wrapper aapd-layout-card aapd-preview-layout">
                                    <div class="aapd-product-card aapd-animate">
                                        <div class="aapd-product-media">
                                            <div class="aapd-product-image-placeholder"></div>
                                            <span class="aapd-prime-badge"><svg aria-hidden="true" viewBox="0 0 78 24" focusable="false"><path d="M2 12c0-5.523 5.373-10 12-10h50c6.627 0 12 4.477 12 10s-5.373 10-12 10H14C7.373 22 2 17.523 2 12z" fill="currentColor" opacity="0.18"></path><path d="M22.8 6.3l-3.5 11.5h2.6l3.5-11.5h-2.6zm8.8 0l-3.5 11.5h2.6l3.5-11.5H31.6zm8.8 0l-3.5 11.5h2.6l3.5-11.5H40.4z" fill="currentColor"></path></svg><span><?php esc_html_e('Prime', 'amazon-affiliate-displays'); ?></span></span>
                                        </div>
                                        <div class="aapd-product-content">
                                            <h3 class="aapd-product-title">Amazon Echo Studio (Preview)</h3>
                                            <p class="aapd-product-description"><?php esc_html_e('Immersive Dolby Atmos smart speaker with Alexa and spatial audio processing.', 'amazon-affiliate-displays'); ?></p>
                                            <div class="aapd-product-meta">
                                                <div class="aapd-price-wrap">
                                                    <span class="aapd-price">$199.99</span>
                                                    <span class="aapd-price-compare">$229.99</span>
                                                    <span class="aapd-badge-discount">-13%</span>
                                                </div>
                                                <div class="aapd-rating" data-rating="4.7"><span class="aapd-stars"><span style="width:94%"></span></span><span class="aapd-review-count">1,245 reviews</span></div>
                                            </div>
                                            <a href="#" class="aapd-button" onclick="return false;">Buy on Amazon</a>
                                        </div>
                                    </div>
                                    <p class="aapd-disclaimer"><?php esc_html_e('As an Amazon Associate I earn from qualifying purchases.', 'amazon-affiliate-displays'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="aapd-sidebar-card">
                            <h3><?php esc_html_e('Quick Start', 'amazon-affiliate-displays'); ?></h3>
                            <ol>
                                <li><?php esc_html_e('Enter your Amazon PA-API credentials.', 'amazon-affiliate-displays'); ?></li>
                                <li><?php esc_html_e('Choose a layout and customize styling.', 'amazon-affiliate-displays'); ?></li>
                                <li><?php esc_html_e('Use shortcodes in your posts/pages.', 'amazon-affiliate-displays'); ?></li>
                            </ol>
                        </div>

                        <div class="aapd-sidebar-card">
                            <h3><?php esc_html_e('Shortcode Examples', 'amazon-affiliate-displays'); ?></h3>
                            <div class="aapd-shortcode-example">
                                <code>[amazon_card asin="B0CX57B5F4" layout="card"]</code>
                                <p><?php esc_html_e('Modern product card', 'amazon-affiliate-displays'); ?></p>
                            </div>
                            <div class="aapd-shortcode-example">
                                <code>[amazon_products asin="B0CX57B5F4,B0F2TB1KNV" layout="grid" columns="3"]</code>
                                <p><?php esc_html_e('Responsive grid layout', 'amazon-affiliate-displays'); ?></p>
                            </div>
                            <div class="aapd-shortcode-example">
                                <code>[amazon_products keyword="gaming laptop" layout="carousel" limit="6"]</code>
                                <p><?php esc_html_e('Keyword-powered carousel', 'amazon-affiliate-displays'); ?></p>
                            </div>
                            <div class="aapd-shortcode-example">
                                <code>[amazon_products keyword="wireless earbuds" layout="badge" limit="4"]</code>
                                <p><?php esc_html_e('Minimal badge layout', 'amazon-affiliate-displays'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    public function get_settings() {
        $defaults = self::get_default_settings();
        $saved = get_option('aapd_settings', array());
        return wp_parse_args($saved, $defaults);
    }

    public static function get_default_settings() {
        return array(
            'access_key' => '',
            'secret_key' => '',
            'associate_tag' => '',
            'region' => 'us-east-1',
            'marketplace' => 'www.amazon.com',
            'font_choice' => 'system',
            'google_font_family' => 'Inter:wght@400;500;700',
            'button_gradient_start' => '#ff9900',
            'button_gradient_end' => '#ffcc00',
            'accent_color' => '#146eb4',
            'hover_color' => '#f37300',
            'show_prime_badge' => 1,
            'show_review_count' => 1,
            'show_discount_badge' => 1,
            'show_price' => 1,
            'enable_animations' => 1,
            'dark_mode' => 0,
            'theme_preset' => 'amazon_classic',
            'custom_css' => '',
            'cache_duration' => 3600,
        );
    }
}
