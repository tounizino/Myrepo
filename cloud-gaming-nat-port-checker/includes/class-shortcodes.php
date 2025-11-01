<?php
if (!defined('ABSPATH')) {
    exit;
}

class CGNPC_Shortcodes {
    
    private static $instance = null;
    private $assets_enqueued = false;
    private $data_localized = false;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('cloud_nat_port_checker', array($this, 'render_full_checker'));
        add_shortcode('cloud_nat_checker', array($this, 'render_nat_only'));
        add_shortcode('cloud_port_checker', array($this, 'render_port_only'));
    }
    
    private function enqueue_assets($settings) {
        if (!$this->assets_enqueued) {
            wp_enqueue_style('cgnpc-frontend');
            wp_enqueue_script('cgnpc-frontend');
            $this->assets_enqueued = true;
        }
        if ($this->data_localized) {
            return;
        }
        $presets = CGNPC_Port_Checker::get_platform_presets();
        $preset_payload = array();
        foreach ($presets as $key => $preset) {
            $ports = array();
            foreach ($preset['ports'] as $port) {
                $ports[] = array(
                    'port' => intval($port['port']),
                    'protocol' => strtoupper($port['protocol']),
                    'label' => sanitize_text_field($port['label']),
                );
            }
            $preset_payload[$key] = array(
                'label' => sanitize_text_field($preset['label']),
                'ports' => $ports,
            );
        }
        $data = array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cgnpc_nonce'),
            'siteUrl' => home_url('/'),
            'portPresets' => $preset_payload,
            'theme' => array(
                'defaultMode' => 'dark',
                'colors' => array(
                    'primary' => $settings['primary_color'],
                    'secondary' => $settings['secondary_color'],
                    'darkBg' => $settings['bg_color_dark'],
                    'lightBg' => $settings['bg_color_light'],
                    'darkText' => $settings['text_color_dark'],
                    'lightText' => $settings['text_color_light'],
                ),
            ),
            'fonts' => array(
                'default' => $settings['font_size'],
                'sizes' => array(
                    'small' => $settings['font_size_small'],
                    'medium' => $settings['font_size_medium'],
                    'large' => $settings['font_size_large'],
                ),
            ),
            'i18n' => array(
                'checking' => __('Checking...', 'cloud-nat-port-checker'),
                'error' => __('Error occurred. Please try again.', 'cloud-nat-port-checker'),
                'copied' => __('Copied!', 'cloud-nat-port-checker'),
                'copyFailed' => __('Copy failed', 'cloud-nat-port-checker'),
                'checkAgain' => __('Check Again', 'cloud-nat-port-checker'),
                'open' => __('Open', 'cloud-nat-port-checker'),
                'closed' => __('Closed', 'cloud-nat-port-checker'),
                'unknown' => __('Unknown', 'cloud-nat-port-checker'),
                'method' => __('Detection Method', 'cloud-nat-port-checker'),
                'reason' => __('Reasoning', 'cloud-nat-port-checker'),
                'confidence' => __('Confidence', 'cloud-nat-port-checker'),
            ),
        );
        wp_localize_script('cgnpc-frontend', 'cgnpcData', $data);
        $this->data_localized = true;
    }
    
    public function render_full_checker($atts) {
        $atts = shortcode_atts(array(
            'theme' => 'dark',
        ), $atts, 'cloud_nat_port_checker');
        
        $settings = CGNPC_Admin_Settings::get_default_settings();
        $saved = get_option('cgnpc_settings', array());
        $settings = wp_parse_args($saved, $settings);
        $this->enqueue_assets($settings);
        
        ob_start();
        $this->render_css_vars($settings);
        ?>
        <div class="cgnpc-wrapper" data-theme="<?php echo esc_attr($atts['theme']); ?>" data-font-size="<?php echo esc_attr($settings['font_size']); ?>">
            <?php $this->render_header(); ?>
            <?php $this->render_privacy_notice(); ?>
            <?php $this->render_nat_checker_section(); ?>
            <?php $this->render_port_checker_section(); ?>
            <?php $this->render_device_details_section(); ?>
            <?php $this->render_router_tips_section(); ?>
            <?php $this->render_guides_section($settings); ?>
            <?php $this->render_footer(); ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function render_nat_only($atts) {
        $atts = shortcode_atts(array(), $atts, 'cloud_nat_checker');
        $settings = CGNPC_Admin_Settings::get_default_settings();
        $saved = get_option('cgnpc_settings', array());
        $settings = wp_parse_args($saved, $settings);
        $this->enqueue_assets($settings);
        ob_start();
        $this->render_css_vars($settings);
        ?>
        <div class="cgnpc-wrapper cgnpc-standalone" data-theme="dark" data-font-size="<?php echo esc_attr($settings['font_size']); ?>">
            <?php $this->render_nat_checker_section(); ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function render_port_only($atts) {
        $atts = shortcode_atts(array(), $atts, 'cloud_port_checker');
        $settings = CGNPC_Admin_Settings::get_default_settings();
        $saved = get_option('cgnpc_settings', array());
        $settings = wp_parse_args($saved, $settings);
        $this->enqueue_assets($settings);
        ob_start();
        $this->render_css_vars($settings);
        ?>
        <div class="cgnpc-wrapper cgnpc-standalone" data-theme="dark" data-font-size="<?php echo esc_attr($settings['font_size']); ?>">
            <?php $this->render_port_checker_section(); ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function render_css_vars($settings) {
        ?>
        <style>
            :root {
                --cgnpc-primary: <?php echo esc_attr($settings['primary_color']); ?> !important;
                --cgnpc-secondary: <?php echo esc_attr($settings['secondary_color']); ?> !important;
                --cgnpc-bg-dark: <?php echo esc_attr($settings['bg_color_dark']); ?> !important;
                --cgnpc-bg-light: <?php echo esc_attr($settings['bg_color_light']); ?> !important;
                --cgnpc-text-dark: <?php echo esc_attr($settings['text_color_dark']); ?> !important;
                --cgnpc-text-light: <?php echo esc_attr($settings['text_color_light']); ?> !important;
                --cgnpc-font-small: <?php echo esc_attr($settings['font_size_small']); ?> !important;
                --cgnpc-font-medium: <?php echo esc_attr($settings['font_size_medium']); ?> !important;
                --cgnpc-font-large: <?php echo esc_attr($settings['font_size_large']); ?> !important;
                --cgnpc-container-width: <?php echo esc_attr($settings['container_width']); ?> !important;
                --cgnpc-container-padding: <?php echo esc_attr($settings['container_padding']); ?> !important;
                --cgnpc-header-title-size: <?php echo esc_attr($settings['header_title_size']); ?> !important;
                --cgnpc-section-title-size: <?php echo esc_attr($settings['section_title_size']); ?> !important;
                --cgnpc-section-title-font: <?php echo esc_attr($settings['section_title_font']); ?> !important;
            }
            .cgnpc-header h1 {
                font-size: var(--cgnpc-header-title-size) !important;
                color: #ffffff !important;
            }
            .cgnpc-wrapper[data-theme="dark"] .cgnpc-header h1 {
                color: #ffffff !important;
            }
            .cgnpc-section-header h2 {
                font-size: var(--cgnpc-section-title-size) !important;
                font-family: var(--cgnpc-section-title-font) !important;
            }
        </style>
        <?php
    }
    
    private function render_header() {
        ?>
        <header class="cgnpc-header">
            <div class="cgnpc-header-content">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="cgnpc-home-btn" aria-label="<?php esc_attr_e('Home', 'cloud-nat-port-checker'); ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </a>
                <h1><?php esc_html_e('Cloud Gaming NAT & Port Checker', 'cloud-nat-port-checker'); ?></h1>
                <div class="cgnpc-controls">
                    <button class="cgnpc-font-toggle" aria-label="<?php esc_attr_e('Font Size', 'cloud-nat-port-checker'); ?>" title="<?php esc_attr_e('Cycle Font Size', 'cloud-nat-port-checker'); ?>">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><text x="3" y="19" font-size="14" font-weight="bold">A</text><text x="13" y="19" font-size="10">a</text></svg>
                    </button>
                    <button class="cgnpc-theme-toggle" aria-label="<?php esc_attr_e('Toggle Theme', 'cloud-nat-port-checker'); ?>" title="<?php esc_attr_e('Toggle Light/Dark Mode', 'cloud-nat-port-checker'); ?>">
                        <span class="cgnpc-theme-icon" aria-hidden="true">
                            <svg class="cgnpc-theme-sun" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                            <svg class="cgnpc-theme-moon" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                        </span>
                        <span class="cgnpc-theme-label"><?php esc_html_e('Theme', 'cloud-nat-port-checker'); ?></span>
                    </button>
                </div>
            </div>
        </header>
        <?php
    }
    
    private function render_privacy_notice() {
        ?>
        <div class="cgnpc-privacy-notice">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5zm0 10h7c-.53 4.12-3.28 7.79-7 8.94V12H5V7.89l7-3.78v8.89z"/></svg>
            <span><?php esc_html_e('This tool does not store any personal data. All checks are performed securely in real-time.', 'cloud-nat-port-checker'); ?></span>
        </div>
        <?php
    }
    
    private function render_nat_checker_section() {
        ?>
        <section class="cgnpc-section cgnpc-nat-section">
            <header class="cgnpc-section-header">
                <h2><?php esc_html_e('NAT Type Checker', 'cloud-nat-port-checker'); ?></h2>
                <div class="cgnpc-section-actions">
                    <label class="cgnpc-auto-refresh">
                        <input type="checkbox" class="cgnpc-auto-refresh-toggle" aria-label="<?php esc_attr_e('Enable auto refresh for NAT status', 'cloud-nat-port-checker'); ?>" />
                        <span><?php esc_html_e('Auto refresh (30s)', 'cloud-nat-port-checker'); ?></span>
                    </label>
                    <button class="cgnpc-check-nat-btn cgnpc-btn-primary" aria-live="polite">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1-2.73 2.71-2.73 7.08 0 9.79 2.73 2.71 7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29-3.51 3.48-9.21 3.48-12.72 0-3.5-3.47-3.53-9.11-.02-12.58 3.51-3.47 9.14-3.47 12.65 0L21 3v7.12zM12.5 8v4.25l3.5 2.08-.72 1.21L11 13V8h1.5z"/></svg>
                        <?php esc_html_e('Check NAT Type', 'cloud-nat-port-checker'); ?>
                    </button>
                </div>
            </header>
            <div class="cgnpc-nat-result" style="display:none;">
                <div class="cgnpc-nat-card" data-nat-tier="unknown">
                    <div class="cgnpc-nat-card__status">
                        <span class="cgnpc-nat-chip"></span>
                        <p class="cgnpc-nat-description"></p>
                    </div>
                    <div class="cgnpc-nat-card__metrics">
                        <div class="cgnpc-nat-metric">
                            <span class="cgnpc-metric-label"><?php esc_html_e('Public IP', 'cloud-nat-port-checker'); ?></span>
                            <span class="cgnpc-metric-value cgnpc-ip"></span>
                        </div>
                        <div class="cgnpc-nat-metric">
                            <span class="cgnpc-metric-label"><?php esc_html_e('Region', 'cloud-nat-port-checker'); ?></span>
                            <span class="cgnpc-metric-value cgnpc-location"></span>
                        </div>
                        <div class="cgnpc-nat-metric">
                            <span class="cgnpc-metric-label"><?php esc_html_e('ISP', 'cloud-nat-port-checker'); ?></span>
                            <span class="cgnpc-metric-value cgnpc-isp"></span>
                        </div>
                    </div>
                </div>
                <div class="cgnpc-nat-meta-grid">
                    <div class="cgnpc-nat-meta-card">
                        <span class="cgnpc-meta-label"><?php esc_html_e('Confidence', 'cloud-nat-port-checker'); ?></span>
                        <span class="cgnpc-meta-value cgnpc-nat-confidence"></span>
                    </div>
                    <div class="cgnpc-nat-meta-card">
                        <span class="cgnpc-meta-label"><?php esc_html_e('Advice', 'cloud-nat-port-checker'); ?></span>
                        <div class="cgnpc-meta-list cgnpc-nat-advice"></div>
                    </div>
                    <div class="cgnpc-nat-meta-card">
                        <span class="cgnpc-meta-label"><?php esc_html_e('Fallback Plan', 'cloud-nat-port-checker'); ?></span>
                        <p class="cgnpc-meta-value cgnpc-nat-fallback"></p>
                    </div>
                </div>
                <div class="cgnpc-advanced-panel">
                    <button type="button" class="cgnpc-advanced-toggle" aria-expanded="false">
                        <span class="cgnpc-advanced-toggle__label"><?php esc_html_e('Show Advanced Insights', 'cloud-nat-port-checker'); ?></span>
                        <span class="cgnpc-advanced-toggle__icon" aria-hidden="true"></span>
                    </button>
                    <div class="cgnpc-advanced-body"></div>
                </div>
            </div>
            <div class="cgnpc-nat-loading" style="display:none;">
                <div class="cgnpc-spinner"></div>
                <p><?php esc_html_e('Analyzing your network configuration...', 'cloud-nat-port-checker'); ?></p>
            </div>
        </section>
        <?php
    }
    
    private function render_port_checker_section() {
        $presets = CGNPC_Port_Checker::get_platform_presets();
        ?>
        <section class="cgnpc-section cgnpc-port-section">
            <header class="cgnpc-section-header">
                <h2><?php esc_html_e('Port Checker', 'cloud-nat-port-checker'); ?></h2>
            </header>
            <div class="cgnpc-port-controls">
                <div class="cgnpc-port-options">
                    <div class="cgnpc-target-ip-wrapper">
                        <label class="cgnpc-custom-ip-label">
                            <input type="checkbox" class="cgnpc-custom-ip-toggle" aria-label="<?php esc_attr_e('Use custom IP address', 'cloud-nat-port-checker'); ?>" />
                            <span><?php esc_html_e('Custom IP address', 'cloud-nat-port-checker'); ?></span>
                        </label>
                        <input type="text" class="cgnpc-custom-ip-input" placeholder="<?php esc_attr_e('e.g., 8.8.8.8 or example.com', 'cloud-nat-port-checker'); ?>" style="display:none;" />
                    </div>
                </div>
                <div class="cgnpc-presets-grid">
                    <?php foreach ($presets as $key => $preset) : ?>
                        <button class="cgnpc-preset-btn" data-preset="<?php echo esc_attr($key); ?>">
                            <?php echo esc_html($preset['label']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <div class="cgnpc-custom-port-form">
                    <input type="number" class="cgnpc-custom-port-input" min="1" max="65535" placeholder="<?php esc_attr_e('Enter custom port (1-65535)', 'cloud-nat-port-checker'); ?>" />
                    <select class="cgnpc-protocol-select">
                        <option value="tcp">TCP</option>
                        <option value="udp">UDP</option>
                    </select>
                    <button class="cgnpc-check-custom-port-btn cgnpc-btn-primary"><?php esc_html_e('Check Port', 'cloud-nat-port-checker'); ?></button>
                </div>
            </div>
            <div class="cgnpc-port-results-wrapper">
                <div class="cgnpc-port-results"></div>
            </div>
        </section>
        <?php
    }
    
    private function render_device_details_section() {
        ?>
        <section class="cgnpc-section cgnpc-device-section">
            <header class="cgnpc-section-header">
                <h2><?php esc_html_e('Device & Connection Details', 'cloud-nat-port-checker'); ?></h2>
                <button class="cgnpc-refresh-device-btn">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.65 6.35A7.958 7.958 0 0 0 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08A5.99 5.99 0 0 1 12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>
                    <?php esc_html_e('Refresh', 'cloud-nat-port-checker'); ?>
                </button>
            </header>
            <div class="cgnpc-device-grid"></div>
        </section>
        <?php
    }
    
    private function render_router_tips_section() {
        $routers = array(
            array('name' => 'Linksys / Netgear / D-Link', 'ip' => '192.168.1.1', 'user' => 'admin', 'pass' => 'admin'),
            array('name' => 'TP-Link / ASUS', 'ip' => '192.168.0.1', 'user' => 'admin', 'pass' => 'admin'),
            array('name' => 'Belkin / SMC', 'ip' => '192.168.2.1', 'user' => 'admin', 'pass' => 'password'),
            array('name' => 'Comcast Xfinity / Cox', 'ip' => '10.0.0.1', 'user' => 'admin', 'pass' => 'password'),
            array('name' => 'AT&T', 'ip' => '192.168.1.254', 'user' => 'admin', 'pass' => 'attadmin'),
            array('name' => 'Thomson / Technicolor', 'ip' => '192.168.100.1', 'user' => 'admin', 'pass' => 'admin'),
        );
        ?>
        <section class="cgnpc-section cgnpc-router-section">
            <header class="cgnpc-section-header">
                <h2><?php esc_html_e('Router Configuration Quick Access', 'cloud-nat-port-checker'); ?></h2>
            </header>
            <div class="cgnpc-router-cards">
                <?php foreach ($routers as $router) : ?>
                    <div class="cgnpc-router-card">
                        <div class="cgnpc-router-card__header">
                            <h4><?php echo esc_html($router['name']); ?></h4>
                            <a href="http://<?php echo esc_attr($router['ip']); ?>" class="cgnpc-router-link" target="_blank" rel="noopener noreferrer">
                                <?php echo esc_html($router['ip']); ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6m4-3h6v6m-11 5L21 3"/></svg>
                            </a>
                        </div>
                        <div class="cgnpc-router-creds">
                            <div class="cgnpc-cred-line">
                                <span class="cgnpc-cred-label">admin:</span>
                                <code class="cgnpc-typewriter" data-text="<?php echo esc_attr($router['user']); ?>"></code>
                            </div>
                            <div class="cgnpc-cred-line">
                                <span class="cgnpc-cred-label">password:</span>
                                <code class="cgnpc-typewriter" data-text="<?php echo esc_attr($router['pass']); ?>"></code>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="cgnpc-router-tips">
                <h3><?php esc_html_e('Quick NAT/UPnP Optimization Tips', 'cloud-nat-port-checker'); ?></h3>
                <ul class="cgnpc-tips-list">
                    <li><span class="cgnpc-tip-icon">⚡</span> <?php esc_html_e('Enable UPnP for automatic port forwarding', 'cloud-nat-port-checker'); ?></li>
                    <li><span class="cgnpc-tip-icon">🎯</span> <?php esc_html_e('Set up DMZ for your gaming device', 'cloud-nat-port-checker'); ?></li>
                    <li><span class="cgnpc-tip-icon">⏫</span> <?php esc_html_e('Enable QoS and prioritize gaming traffic', 'cloud-nat-port-checker'); ?></li>
                    <li><span class="cgnpc-tip-icon">🔇</span> <?php esc_html_e('Disable SIP ALG (can interfere with gaming)', 'cloud-nat-port-checker'); ?></li>
                    <li><span class="cgnpc-tip-icon">🔌</span> <?php esc_html_e('Use wired Ethernet instead of Wi-Fi', 'cloud-nat-port-checker'); ?></li>
                </ul>
            </div>
        </section>
        <?php
    }
    
    private function render_guides_section($settings) {
        if (empty($settings['guides']) || !is_array($settings['guides'])) {
            return;
        }
        ?>
        <section class="cgnpc-section cgnpc-guides-section">
            <header class="cgnpc-section-header">
                <h2><?php esc_html_e('Useful Cloud Gaming Guides', 'cloud-nat-port-checker'); ?></h2>
            </header>
            <div class="cgnpc-guides-grid">
                <?php foreach ($settings['guides'] as $guide) : ?>
                    <?php if (empty($guide['title'])) {
                        continue;
                    } ?>
                    <article class="cgnpc-guide-card">
                        <?php if (!empty($guide['image'])) : ?>
                            <div class="cgnpc-guide-image" style="background-image: url('<?php echo esc_url($guide['image']); ?>');">
                                <img src="<?php echo esc_url($guide['image']); ?>" alt="<?php echo esc_attr($guide['title']); ?>" loading="lazy" />
                            </div>
                        <?php endif; ?>
                        <div class="cgnpc-guide-content">
                            <h3><?php echo esc_html($guide['title']); ?></h3>
                            <p><?php echo esc_html($guide['excerpt']); ?></p>
                            <?php if (!empty($guide['link'])) : ?>
                                <a href="<?php echo esc_url($guide['link']); ?>" class="cgnpc-guide-link"><?php esc_html_e('Read More →', 'cloud-nat-port-checker'); ?></a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
    
    private function render_footer() {
        $year = current_time('Y');
        ?>
        <footer class="cgnpc-footer">
            <p>&copy; <?php echo esc_html($year); ?> <?php echo esc_html(get_bloginfo('name')); ?>. <?php esc_html_e('All rights reserved. Cloud Gaming NAT & Port Checker Plugin.', 'cloud-nat-port-checker'); ?></p>
        </footer>
        <?php
    }
}
