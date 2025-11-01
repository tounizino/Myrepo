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
                --cgnpc-primary: <?php echo esc_attr($settings['primary_color']); ?>;
                --cgnpc-secondary: <?php echo esc_attr($settings['secondary_color']); ?>;
                --cgnpc-bg-dark: <?php echo esc_attr($settings['bg_color_dark']); ?>;
                --cgnpc-bg-light: <?php echo esc_attr($settings['bg_color_light']); ?>;
                --cgnpc-text-dark: <?php echo esc_attr($settings['text_color_dark']); ?>;
                --cgnpc-text-light: <?php echo esc_attr($settings['text_color_light']); ?>;
                --cgnpc-font-small: <?php echo esc_attr($settings['font_size_small']); ?>;
                --cgnpc-font-medium: <?php echo esc_attr($settings['font_size_medium']); ?>;
                --cgnpc-font-large: <?php echo esc_attr($settings['font_size_large']); ?>;
                --cgnpc-container-width: <?php echo esc_attr($settings['container_width']); ?>;
                --cgnpc-container-padding: <?php echo esc_attr($settings['container_padding']); ?>;
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
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><text x="2" y="18" font-size="14" font-weight="bold">A</text><text x="11" y="18" font-size="10">a</text></svg>
                    </button>
                    <button class="cgnpc-theme-toggle" aria-label="<?php esc_attr_e('Toggle Theme', 'cloud-nat-port-checker'); ?>" title="<?php esc_attr_e('Toggle Light/Dark Mode', 'cloud-nat-port-checker'); ?>">
                        <svg class="cgnpc-sun-icon" width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                        <svg class="cgnpc-moon-icon" width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
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
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1-2.73 2.71-2.73 7.08 0 9.79 2.73 2.71 7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29-3.51 3.48-9.21 3.48-12.72 0-3.5-3.47-3.53-9.11-.02-12.58 3.51-3.47 9.14-3.47 12.65 0L21 3v7.12zM12.5 8v4.25l3.5 2.08-.72 1.21L11 13V8h1.5z"/></svg>
                        <?php esc_html_e('Check NAT Type', 'cloud-nat-port-checker'); ?>
                    </button>
                </div>
            </header>
            <div class="cgnpc-nat-result" style="display:none;">
                <div class="cgnpc-nat-status-card">
                    <div class="cgnpc-nat-indicator"></div>
                    <div class="cgnpc-nat-info">
                        <h3 class="cgnpc-nat-label"></h3>
                        <p class="cgnpc-nat-description"></p>
                    </div>
                </div>
                <div class="cgnpc-geo-grid">
                    <div class="cgnpc-info-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
                        <div><strong><?php esc_html_e('Public IP:', 'cloud-nat-port-checker'); ?></strong> <span class="cgnpc-ip"></span> <button class="cgnpc-copy-btn" data-target="ip" aria-label="<?php esc_attr_e('Copy IP', 'cloud-nat-port-checker'); ?>">📋</button></div>
                    </div>
                    <div class="cgnpc-info-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        <div><strong><?php esc_html_e('Location:', 'cloud-nat-port-checker'); ?></strong> <span class="cgnpc-location"></span></div>
                    </div>
                    <div class="cgnpc-info-item">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h18V4H4c-1.1 0-2 .9-2 2v11H0v3h14v-3H4V6zm19 2h-6c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V9c0-.55-.45-1-1-1zm-1 9h-4v-7h4v7z"/></svg>
                        <div><strong><?php esc_html_e('ISP:', 'cloud-nat-port-checker'); ?></strong> <span class="cgnpc-isp"></span></div>
                    </div>
                </div>
                <details class="cgnpc-advanced-details">
                    <summary><?php esc_html_e('Show Advanced Technical Details', 'cloud-nat-port-checker'); ?></summary>
                    <div class="cgnpc-advanced-content"></div>
                </details>
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
            <div class="cgnpc-port-results"></div>
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
        ?>
        <section class="cgnpc-section cgnpc-router-section">
            <header class="cgnpc-section-header">
                <h2><?php esc_html_e('Router Configuration Tips', 'cloud-nat-port-checker'); ?></h2>
            </header>
            <div class="cgnpc-router-content">
                <div class="cgnpc-router-logins">
                    <h3><?php esc_html_e('Common Router Admin IPs', 'cloud-nat-port-checker'); ?></h3>
                    <ul class="cgnpc-router-list">
                        <li><strong>192.168.1.1</strong> – Most common (Linksys, Netgear, D-Link)</li>
                        <li><strong>192.168.0.1</strong> – Alternative common address</li>
                        <li><strong>192.168.2.1</strong> – Belkin, SMC</li>
                        <li><strong>10.0.0.1</strong> – Comcast Xfinity, Cox</li>
                        <li><strong>192.168.100.1</strong> – Thomson</li>
                    </ul>
                    <p class="cgnpc-tip"><?php esc_html_e('💡 Default username/password is often: admin/admin, admin/password, or printed on router label.', 'cloud-nat-port-checker'); ?></p>
                </div>
                <div class="cgnpc-router-tips-list">
                    <h3><?php esc_html_e('Quick NAT/QoS Setup Tips', 'cloud-nat-port-checker'); ?></h3>
                    <ul class="cgnpc-tips-list">
                        <li><?php esc_html_e('✅ Enable UPnP (Universal Plug and Play) for automatic port forwarding.', 'cloud-nat-port-checker'); ?></li>
                        <li><?php esc_html_e('✅ Set up DMZ (Demilitarized Zone) for your gaming device if issues persist.', 'cloud-nat-port-checker'); ?></li>
                        <li><?php esc_html_e('✅ Enable QoS and prioritize gaming traffic for reduced lag.', 'cloud-nat-port-checker'); ?></li>
                        <li><?php esc_html_e('✅ Disable SIP ALG in router settings (can interfere with gaming protocols).', 'cloud-nat-port-checker'); ?></li>
                        <li><?php esc_html_e('✅ Use wired Ethernet instead of Wi-Fi for best latency and stability.', 'cloud-nat-port-checker'); ?></li>
                    </ul>
                </div>
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
