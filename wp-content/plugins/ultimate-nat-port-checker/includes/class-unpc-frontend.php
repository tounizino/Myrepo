<?php
/**
 * Frontend functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class UNPC_Frontend {
    
    private static $instance = null;
    private $settings = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
        add_shortcode('ultimate_nat_port_checker', array($this, 'render_full_tool'));
        add_shortcode('nat_checker_only', array($this, 'render_nat_only'));
        add_shortcode('port_checker_only', array($this, 'render_port_only'));
    }
    
    /**
     * Register styles and scripts (enqueued on demand)
     */
    public function register_assets() {
        wp_register_style(
            'unpc-frontend-style',
            UNPC_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            UNPC_VERSION
        );
        
        wp_register_script(
            'unpc-frontend-script',
            UNPC_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            UNPC_VERSION,
            true
        );
    }
    
    /**
     * Enqueue assets and pass localized data
     */
    private function enqueue_assets($context = 'full') {
        if (!wp_style_is('unpc-frontend-style', 'enqueued')) {
            wp_enqueue_style('unpc-frontend-style');
        }
        
        if (!wp_script_is('unpc-frontend-script', 'enqueued')) {
            wp_enqueue_script('unpc-frontend-script');
        }
        
        $settings = $this->get_settings();
        $port_presets = array_map(function($preset) {
            return array(
                'id' => isset($preset['id']) ? (int) $preset['id'] : (int) $preset['ID'],
                'platform_name' => $preset['platform_name'],
                'ports' => $preset['ports'],
                'protocol' => strtoupper($preset['protocol'])
            );
        }, $this->get_port_presets());
        
        $localized = array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('unpc_nonce'),
            'settings' => array(
                'primary_color' => $settings['primary_color'],
                'secondary_color' => $settings['secondary_color'],
                'nat_type1_color' => $settings['nat_type1_color'],
                'nat_type2_color' => $settings['nat_type2_color'],
                'nat_type3_color' => $settings['nat_type3_color'],
                'port_open_color' => $settings['port_open_color'],
                'port_closed_color' => $settings['port_closed_color'],
                'container_width' => (int) $settings['container_width'],
                'font_size_preset' => $this->get_font_size_preset($settings)
            ),
            'portPresets' => $port_presets,
            'context' => $context
        );
        
        wp_localize_script('unpc-frontend-script', 'unpcData', $localized);
    }
    
    /**
     * Retrieve plugin settings with defaults
     */
    private function get_settings() {
        if (null === $this->settings) {
            $this->settings = UNPC_API::get_settings();
        }
        return $this->settings;
    }
    
    /**
     * Get font size preset with fallback
     */
    private function get_font_size_preset($settings) {
        $allowed = array('small', 'medium', 'large');
        $preset = isset($settings['font_size_preset']) ? sanitize_text_field($settings['font_size_preset']) : 'small';
        return in_array($preset, $allowed, true) ? $preset : 'small';
    }
    
    /**
     * Inline CSS variables for dynamic styling
     */
    private function build_inline_styles($settings) {
        $styles = array();
        
        if (!empty($settings['container_width'])) {
            $styles[] = '--unpc-container-width:' . intval($settings['container_width']) . 'px';
        }
        if (!empty($settings['primary_color'])) {
            $styles[] = '--unpc-primary:' . sanitize_hex_color($settings['primary_color']);
        }
        if (!empty($settings['secondary_color'])) {
            $styles[] = '--unpc-secondary:' . sanitize_hex_color($settings['secondary_color']);
        }
        if (!empty($settings['nat_type1_color'])) {
            $styles[] = '--unpc-nat-type1:' . sanitize_hex_color($settings['nat_type1_color']);
        }
        if (!empty($settings['nat_type2_color'])) {
            $styles[] = '--unpc-nat-type2:' . sanitize_hex_color($settings['nat_type2_color']);
        }
        if (!empty($settings['nat_type3_color'])) {
            $styles[] = '--unpc-nat-type3:' . sanitize_hex_color($settings['nat_type3_color']);
        }
        if (!empty($settings['port_open_color'])) {
            $styles[] = '--unpc-port-open:' . sanitize_hex_color($settings['port_open_color']);
        }
        if (!empty($settings['port_closed_color'])) {
            $styles[] = '--unpc-port-closed:' . sanitize_hex_color($settings['port_closed_color']);
        }
        
        return implode(';', array_filter($styles));
    }
    
    /**
     * Retrieve guides from settings
     */
    private function get_guides() {
        return UNPC_API::get_guides();
    }
    
    /**
     * Retrieve port presets
     */
    private function get_port_presets() {
        return UNPC_API::get_port_presets();
    }
    
    /**
     * Render option tags for port presets
     */
    private function render_port_preset_options() {
        $options = '';
        $presets = $this->get_port_presets();
        foreach ($presets as $preset) {
            $options .= sprintf(
                '<option value="%1$d" data-ports="%2$s" data-protocol="%3$s">%4$s</option>',
                isset($preset['id']) ? intval($preset['id']) : intval($preset['ID']),
                esc_attr($preset['ports']),
                esc_attr(strtoupper($preset['protocol'])),
                esc_html($preset['platform_name'])
            );
        }
        return $options;
    }
    
    /**
     * Render preset buttons for port checker
     */
    private function render_port_preset_buttons() {
        $buttons = '';
        $presets = $this->get_port_presets();
        foreach ($presets as $preset) {
            $buttons .= sprintf(
                '<button type="button" class="unpc-preset-btn" data-ports="%1$s" data-protocol="%2$s">%3$s</button>',
                esc_attr($preset['ports']),
                esc_attr(strtoupper($preset['protocol'])),
                esc_html($preset['platform_name'])
            );
        }
        $buttons .= '<button type="button" class="unpc-preset-btn" data-ports="custom">Custom Ports</button>';
        return $buttons;
    }
    
    /**
     * Render full tool shortcode
     */
    public function render_full_tool($atts) {
        $this->enqueue_assets('full');
        $settings = $this->get_settings();
        $guides = $this->get_guides();
        $style_attr = $this->build_inline_styles($settings);
        $font_preset = $this->get_font_size_preset($settings);
        
        ob_start();
        ?>
        <div class="unpc-wrapper" data-theme="dark" data-font-size="<?php echo esc_attr($font_preset); ?>"<?php echo $style_attr ? ' style="' . esc_attr($style_attr) . '"' : ''; ?>>
            <header class="unpc-header">
                <a href="<?php echo esc_url($settings['home_url']); ?>" class="unpc-home-btn" aria-label="Go to home">
                    <span class="dashicons dashicons-admin-home" aria-hidden="true"></span>
                </a>
                <button class="unpc-theme-toggle" type="button" aria-label="Toggle light or dark theme">
                    <span class="theme-icon theme-icon-dark" aria-hidden="true">🌙</span>
                    <span class="theme-icon theme-icon-light" aria-hidden="true">☀️</span>
                    <span class="theme-label">Dark Mode</span>
                </button>
            </header>
            
            <div class="unpc-container">
                <h1 class="unpc-title"><?php echo esc_html($settings['header_text']); ?></h1>
                <p class="unpc-subtitle">Advanced live diagnostics engineered for next-generation cloud gaming</p>
                
                <div class="unpc-privacy-notice">
                    <span class="dashicons dashicons-shield" aria-hidden="true"></span>
                    <span><?php echo esc_html($settings['privacy_note']); ?></span>
                </div>
                
                <section class="unpc-section unpc-nat-section">
                    <div class="unpc-section-header">
                        <h2>NAT Type Intelligence</h2>
                        <p>Instantly identify your network profile, public IP, and routing quality for seamless gameplay</p>
                    </div>
                    <div class="unpc-section-content">
                        <div class="unpc-action-row">
                            <button class="unpc-btn unpc-btn-primary" id="unpc-check-nat" type="button">
                                <span class="btn-text">Run NAT Assessment</span>
                                <span class="btn-loader" aria-hidden="true"></span>
                            </button>
                            <span class="unpc-hint">Uses secure WebRTC diagnostics &amp; real-time GEO lookup</span>
                        </div>
                        
                        <div id="unpc-nat-results" class="unpc-results" style="display:none;">
                            <div class="unpc-result-card">
                                <div class="unpc-result-main" id="unpc-result-main" role="status" data-nat-type="2">
                                    <div class="unpc-result-label">Detected NAT Profile</div>
                                    <div class="unpc-result-value" id="nat-type-value">-</div>
                                    <div class="unpc-result-status" id="nat-status-badge">-</div>
                                    <div class="unpc-result-description" id="nat-insights">Detailed insights will appear after analysis.</div>
                                </div>
                                <div class="unpc-result-grid" aria-live="polite">
                                    <div class="unpc-result-item"><span class="label">Public IP</span><span class="value" id="nat-public-ip">-</span></div>
                                    <div class="unpc-result-item"><span class="label">ISP</span><span class="value" id="nat-isp">-</span></div>
                                    <div class="unpc-result-item"><span class="label">Country</span><span class="value" id="nat-country">-</span></div>
                                    <div class="unpc-result-item"><span class="label">Region</span><span class="value" id="nat-region">-</span></div>
                                    <div class="unpc-result-item"><span class="label">City</span><span class="value" id="nat-city">-</span></div>
                                    <div class="unpc-result-item"><span class="label">Timezone</span><span class="value" id="nat-timezone">-</span></div>
                                </div>
                                <details class="unpc-details">
                                    <summary>Technical Diagnostics</summary>
                                    <div class="unpc-details-content">
                                        <div class="detail-item"><span class="label">Organization</span><span class="value" id="nat-org">-</span></div>
                                        <div class="detail-item"><span class="label">AS Number</span><span class="value" id="nat-asn">-</span></div>
                                        <div class="detail-item"><span class="label">Connection Type</span><span class="value" id="nat-connection-type">-</span></div>
                                        <div class="detail-item"><span class="label">Latitude</span><span class="value" id="nat-lat">-</span></div>
                                        <div class="detail-item"><span class="label">Longitude</span><span class="value" id="nat-lon">-</span></div>
                                        <div class="detail-item"><span class="label">Postal</span><span class="value" id="nat-postal">-</span></div>
                                        <div class="detail-item"><span class="label">STUN Server</span><span class="value" id="nat-stun">stun:stun.l.google.com:19302</span></div>
                                        <div class="detail-item"><span class="label">Symmetric NAT Check</span><span class="value" id="nat-symmetric">Analysing…</span></div>
                                        <div class="detail-item detail-multi">
                                            <span class="label">ICE Candidates</span>
                                            <div class="detail-list" id="nat-ice-details">Gathering peer-to-peer routing signatures…</div>
                                        </div>
                                    </div>
                                </details>
                            </div>
                        </div>
                        <div class="unpc-technical-log" id="unpc-technical-log" style="display:none;"></div>
                    </div>
                </section>
                
                <section class="unpc-section unpc-port-section">
                    <div class="unpc-section-header">
                        <h2>Port Accessibility Scanner</h2>
                        <p>Validate gaming platform presets or custom ranges for direct-to-device reachability</p>
                    </div>
                    <div class="unpc-section-content">
                        <input type="hidden" id="port-host" value="" />
                        <input type="hidden" id="port-protocol" value="TCP" />
                        <div class="unpc-port-presets" id="unpc-port-presets">
                            <?php echo $this->render_port_preset_buttons(); ?>
                        </div>
                        <div class="unpc-port-controls">
                            <div class="unpc-form-group">
                                <label for="custom-port">Port / Range</label>
                                <input type="text" id="custom-port" class="unpc-input" placeholder="Enter ports e.g. 3074 or 27015-27030" />
                                <span class="unpc-input-hint">Supports multiple ports, ranges, and mixed entries. Default protocol: TCP.</span>
                            </div>
                            <div class="unpc-form-group unpc-form-group-action">
                                <button class="unpc-btn unpc-btn-primary" id="unpc-check-port" type="button">
                                    <span class="btn-text">Run Port Scan</span>
                                    <span class="btn-loader" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        <div id="unpc-port-results" class="unpc-results" style="display:none;">
                            <div class="unpc-result-card">
                                <div class="port-results-meta" id="port-results-meta"></div>
                                <div id="port-results-container"></div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <section class="unpc-section unpc-device-section">
                    <div class="unpc-section-header">
                        <h2>Live Device Telemetry</h2>
                        <p>We gather client-side capabilities to help you fine tune streaming performance</p>
                    </div>
                    <div class="unpc-section-content">
                        <div class="unpc-device-grid">
                            <div class="unpc-device-card">
                                <div class="device-icon" aria-hidden="true">💻</div>
                                <h3>System Profile</h3>
                                <div class="device-details">
                                    <div class="detail-row"><span class="label">Operating System</span><span class="value" id="device-os">-</span></div>
                                    <div class="detail-row"><span class="label">Platform</span><span class="value" id="device-platform">-</span></div>
                                    <div class="detail-row"><span class="label">Architecture</span><span class="value" id="device-arch">-</span></div>
                                </div>
                            </div>
                            <div class="unpc-device-card">
                                <div class="device-icon" aria-hidden="true">🌐</div>
                                <h3>Browser Engine</h3>
                                <div class="device-details">
                                    <div class="detail-row"><span class="label">Browser</span><span class="value" id="device-browser">-</span></div>
                                    <div class="detail-row"><span class="label">Version</span><span class="value" id="device-browser-version">-</span></div>
                                    <div class="detail-row"><span class="label">Rendering Engine</span><span class="value" id="device-engine">-</span></div>
                                </div>
                            </div>
                            <div class="unpc-device-card">
                                <div class="device-icon" aria-hidden="true">📱</div>
                                <h3>Display Metrics</h3>
                                <div class="device-details">
                                    <div class="detail-row"><span class="label">Screen</span><span class="value" id="device-screen">-</span></div>
                                    <div class="detail-row"><span class="label">Viewport</span><span class="value" id="device-viewport">-</span></div>
                                    <div class="detail-row"><span class="label">Color Depth</span><span class="value" id="device-color">-</span></div>
                                </div>
                            </div>
                        </div>
                        <details class="unpc-details">
                            <summary>Advanced Device Breakdown</summary>
                            <div class="unpc-details-content unpc-device-advanced">
                                <div class="detail-item"><span class="label">User Agent</span><span class="value" id="device-ua">-</span></div>
                                <div class="detail-item"><span class="label">Language</span><span class="value" id="device-lang">-</span></div>
                                <div class="detail-item"><span class="label">Timezone</span><span class="value" id="device-tz">-</span></div>
                                <div class="detail-item"><span class="label">CPU Cores</span><span class="value" id="device-cores">-</span></div>
                                <div class="detail-item"><span class="label">RAM (approx)</span><span class="value" id="device-memory">-</span></div>
                                <div class="detail-item"><span class="label">Touch Support</span><span class="value" id="device-touch">-</span></div>
                                <div class="detail-item"><span class="label">WebGL</span><span class="value" id="device-webgl">-</span></div>
                                <div class="detail-item"><span class="label">Cookies</span><span class="value" id="device-cookies">-</span></div>
                            </div>
                        </details>
                    </div>
                </section>
                
                <section class="unpc-section unpc-router-section">
                    <div class="unpc-section-header">
                        <h2>Router Quick Access Library</h2>
                        <p>Popular gateways and firmware credentials so you can toggle UPnP, QoS, or port forwarding instantly</p>
                    </div>
                    <div class="unpc-section-content">
                        <div class="unpc-router-search">
                            <input type="text" id="router-search" class="unpc-input" placeholder="Search router brand or model" />
                        </div>
                        <div class="unpc-router-list" id="router-list">
                            <?php echo $this->get_router_list_html(); ?>
                        </div>
                        <div class="unpc-info-banner">
                            <p><span class="accent accent-success">Quick NAT Setup:</span> Enable UPnP or NAT-PMP in your router to allow automatic port forwarding for consoles and cloud gaming rigs.</p>
                            <p><span class="accent accent-info">QoS Setup:</span> Prioritize your gaming device by MAC or IP address to guarantee low-latency traffic during peak usage.</p>
                        </div>
                    </div>
                </section>
                
                <?php if (!empty($guides)) : ?>
                <section class="unpc-section unpc-guides-section">
                    <div class="unpc-section-header">
                        <h2>Expert Network Guides</h2>
                        <p>Premium tutorials curated from your cloud gaming hub</p>
                    </div>
                    <div class="unpc-section-content">
                        <div class="unpc-guides-grid">
                            <?php foreach ($guides as $guide) : ?>
                                <a class="unpc-guide-card" href="<?php echo esc_url($guide['url']); ?>" target="_blank" rel="noopener">
                                    <div class="guide-icon" aria-hidden="true">📖</div>
                                    <h3><?php echo esc_html($guide['title']); ?></h3>
                                    <p><?php echo esc_html($guide['description']); ?></p>
                                    <span class="guide-link">Dive into the guide →</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?>
            </div>
            
            <footer class="unpc-footer" role="contentinfo">
                <p><?php echo esc_html($settings['footer_text']); ?></p>
            </footer>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render NAT checker only shortcode
     */
    public function render_nat_only($atts) {
        $this->enqueue_assets('nat');
        $settings = $this->get_settings();
        $style_attr = $this->build_inline_styles($settings);
        $font_preset = $this->get_font_size_preset($settings);
        
        ob_start();
        ?>
        <div class="unpc-standalone unpc-nat-standalone" data-theme="dark" data-font-size="<?php echo esc_attr($font_preset); ?>"<?php echo $style_attr ? ' style="' . esc_attr($style_attr) . '"' : ''; ?>>
            <div class="unpc-section-content">
                <div class="unpc-action-row">
                    <button class="unpc-btn unpc-btn-primary" id="unpc-check-nat-standalone" type="button">
                        <span class="btn-text">Run NAT Assessment</span>
                        <span class="btn-loader" aria-hidden="true"></span>
                    </button>
                    <span class="unpc-hint">Secure scan – no data stored or shared</span>
                </div>
                <div id="unpc-nat-results-standalone" class="unpc-results" style="display:none;">
                    <div class="unpc-result-card">
                        <div class="unpc-result-main" id="unpc-result-main-standalone" role="status" data-nat-type="2">
                            <div class="unpc-result-label">Detected NAT Profile</div>
                            <div class="unpc-result-value" id="nat-type-value-standalone">-</div>
                            <div class="unpc-result-status" id="nat-status-badge-standalone">-</div>
                            <div class="unpc-result-description" id="nat-insights-standalone">Insights will load after diagnostics.</div>
                        </div>
                        <div class="unpc-result-grid">
                            <div class="unpc-result-item"><span class="label">Public IP</span><span class="value" id="nat-public-ip-standalone">-</span></div>
                            <div class="unpc-result-item"><span class="label">ISP</span><span class="value" id="nat-isp-standalone">-</span></div>
                            <div class="unpc-result-item"><span class="label">Country</span><span class="value" id="nat-country-standalone">-</span></div>
                            <div class="unpc-result-item"><span class="label">Region</span><span class="value" id="nat-region-standalone">-</span></div>
                        </div>
                        <details class="unpc-details">
                            <summary>Technical Diagnostics</summary>
                            <div class="unpc-details-content">
                                <div class="detail-item"><span class="label">Organization</span><span class="value" id="nat-org-standalone">-</span></div>
                                <div class="detail-item"><span class="label">AS Number</span><span class="value" id="nat-asn-standalone">-</span></div>
                                <div class="detail-item"><span class="label">Connection Type</span><span class="value" id="nat-connection-type-standalone">-</span></div>
                                <div class="detail-item detail-multi"><span class="label">ICE Candidates</span><div class="detail-list" id="nat-ice-details-standalone">Collecting details…</div></div>
                                <div class="detail-item"><span class="label">Symmetric NAT</span><span class="value" id="nat-symmetric-standalone">Analysing…</span></div>
                            </div>
                        </details>
                    </div>
                </div>
                <div class="unpc-technical-log" id="unpc-technical-log-standalone" style="display:none;"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render port checker only shortcode
     */
    public function render_port_only($atts) {
        $this->enqueue_assets('port');
        $settings = $this->get_settings();
        $style_attr = $this->build_inline_styles($settings);
        $font_preset = $this->get_font_size_preset($settings);
        
        ob_start();
        ?>
        <div class="unpc-standalone unpc-port-standalone" data-theme="dark" data-font-size="<?php echo esc_attr($font_preset); ?>"<?php echo $style_attr ? ' style="' . esc_attr($style_attr) . '"' : ''; ?>>
            <div class="unpc-section-content">
                <input type="hidden" id="port-host-standalone" value="" />
                <input type="hidden" id="port-protocol-standalone" value="TCP" />
                <div class="unpc-port-presets" id="unpc-port-presets-standalone">
                    <?php echo $this->render_port_preset_buttons(); ?>
                </div>
                <div class="unpc-port-controls">
                    <div class="unpc-form-group">
                        <label for="custom-port-standalone">Port / Range</label>
                        <input type="text" id="custom-port-standalone" class="unpc-input" placeholder="Enter ports e.g. 3074 or 27015-27030" />
                        <span class="unpc-input-hint">Supports multiple ports &amp; ranges</span>
                    </div>
                    <div class="unpc-form-group unpc-form-group-action">
                        <button class="unpc-btn unpc-btn-primary" id="unpc-check-port-standalone" type="button">
                            <span class="btn-text">Run Port Scan</span>
                            <span class="btn-loader" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div id="unpc-port-results-standalone" class="unpc-results" style="display:none;">
                    <div class="unpc-result-card">
                        <div class="port-results-meta" id="port-results-meta-standalone"></div>
                        <div id="port-results-container-standalone"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Router reference cards
     */
    private function get_router_list_html() {
        $routers = array(
            array('brand' => 'TP-Link Archer', 'ip' => '192.168.0.1', 'username' => 'admin', 'password' => 'admin'),
            array('brand' => 'Netgear Nighthawk', 'ip' => '192.168.1.1', 'username' => 'admin', 'password' => 'password'),
            array('brand' => 'Asus RT Series', 'ip' => '192.168.1.1', 'username' => 'admin', 'password' => 'admin'),
            array('brand' => 'Linksys Smart Wi-Fi', 'ip' => '192.168.1.1', 'username' => 'admin', 'password' => 'admin'),
            array('brand' => 'D-Link DIR Series', 'ip' => '192.168.0.1', 'username' => 'admin', 'password' => 'password'),
            array('brand' => 'Belkin Routers', 'ip' => '192.168.2.1', 'username' => 'admin', 'password' => '(blank)'),
            array('brand' => 'Cisco Small Business', 'ip' => '192.168.1.1', 'username' => 'cisco', 'password' => 'cisco'),
            array('brand' => 'Motorola Surfboard', 'ip' => '192.168.0.1', 'username' => 'admin', 'password' => 'motorola'),
            array('brand' => 'Ubiquiti UniFi', 'ip' => '192.168.1.1', 'username' => 'ubnt', 'password' => 'ubnt'),
            array('brand' => 'Google Nest Wi-Fi', 'ip' => 'Google Home App', 'username' => 'App Login', 'password' => 'App Login'),
            array('brand' => 'Arris TG Series', 'ip' => '192.168.0.1', 'username' => 'admin', 'password' => 'password'),
            array('brand' => 'Huawei Fiber Routers', 'ip' => '192.168.100.1', 'username' => 'admin', 'password' => 'admin'),
            array('brand' => 'ZTE Home Gateways', 'ip' => '192.168.1.1', 'username' => 'admin', 'password' => 'admin')
        );
        $html = '';
        foreach ($routers as $router) {
            $html .= '<div class="unpc-router-item" data-brand="' . esc_attr(strtolower($router['brand'])) . '">';
            $html .= '<div class="router-brand">' . esc_html($router['brand']) . '</div>';
            $html .= '<div class="router-details">';
            $html .= '<div class="router-detail"><span class="label">Gateway</span><span class="value">' . esc_html($router['ip']) . '</span></div>';
            $html .= '<div class="router-detail"><span class="label">Username</span><span class="value">' . esc_html($router['username']) . '</span></div>';
            $html .= '<div class="router-detail"><span class="label">Password</span><span class="value">' . esc_html($router['password']) . '</span></div>';
            $html .= '</div></div>';
        }
        return $html;
    }
}
