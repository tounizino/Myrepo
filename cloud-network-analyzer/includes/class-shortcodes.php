<?php

if (!defined('ABSPATH')) {
    exit;
}

class CNA_Shortcodes {
    
    public static function init() {
        add_shortcode('cloud_network_analyzer', array(__CLASS__, 'full_analyzer'));
        add_shortcode('cloud_nat_checker', array(__CLASS__, 'nat_checker'));
        add_shortcode('cloud_port_checker', array(__CLASS__, 'port_checker'));
        add_shortcode('cloud_device_info', array(__CLASS__, 'device_info'));
    }
    
    public static function full_analyzer($atts) {
        $atts = shortcode_atts(array(
            'theme' => '',
        ), $atts);
        
        $settings = get_option('cna_settings', array());
        
        ob_start();
        ?>
        <div class="cna-wrapper <?php echo self::get_theme_class($atts['theme']); ?>" data-palette="<?php echo esc_attr(isset($settings['color_palette']) ? $settings['color_palette'] : 'blue-gray'); ?>">
            <div class="cna-container">
                <div class="cna-header">
                    <h2 class="cna-title">
                        <svg class="cna-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="2"></circle>
                            <path d="M12 2v2"></path>
                            <path d="M12 20v2"></path>
                            <path d="m4.93 4.93 1.41 1.41"></path>
                            <path d="m17.66 17.66 1.41 1.41"></path>
                            <path d="M2 12h2"></path>
                            <path d="M20 12h2"></path>
                            <path d="m6.34 17.66-1.41 1.41"></path>
                            <path d="m19.07 4.93-1.41 1.41"></path>
                        </svg>
                        <?php _e('Cloud Network Analyzer', 'cloud-network-analyzer'); ?>
                    </h2>
                    <p class="cna-subtitle"><?php _e('Professional network diagnostics for cloud gaming', 'cloud-network-analyzer'); ?></p>
                </div>
                
                <?php if (isset($settings['enable_nat_checker']) && $settings['enable_nat_checker']): ?>
                    <?php echo self::nat_checker(); ?>
                <?php endif; ?>
                
                <?php if (isset($settings['enable_port_checker']) && $settings['enable_port_checker']): ?>
                    <?php echo self::port_checker(); ?>
                <?php endif; ?>
                
                <?php if (isset($settings['enable_device_info']) && $settings['enable_device_info']): ?>
                    <?php echo self::device_info(); ?>
                <?php endif; ?>
                
                <?php if (isset($settings['enable_guides']) && $settings['enable_guides']): ?>
                    <?php echo self::educational_guides(); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public static function nat_checker($atts = array()) {
        $settings = get_option('cna_settings', array());
        
        ob_start();
        ?>
        <div class="cna-section cna-nat-checker">
            <div class="cna-section-header">
                <h3 class="cna-section-title">
                    <svg class="cna-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                    <?php _e('NAT Type Checker', 'cloud-network-analyzer'); ?>
                    <span class="cna-tooltip" data-tooltip="<?php esc_attr_e('Network Address Translation determines how easily you can connect to other players', 'cloud-network-analyzer'); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </span>
                </h3>
                <button class="cna-btn cna-btn-primary cna-check-nat">
                    <svg class="cna-icon cna-spin-on-load" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <polyline points="1 20 1 14 7 14"></polyline>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                    </svg>
                    <?php _e('Check NAT Type', 'cloud-network-analyzer'); ?>
                </button>
            </div>
            
            <div class="cna-nat-result" style="display: none;">
                <div class="cna-nat-status">
                    <div class="cna-nat-indicator">
                        <div class="cna-nat-circle"></div>
                        <div class="cna-nat-type-label"></div>
                    </div>
                    <div class="cna-nat-description"></div>
                </div>
                
                <div class="cna-nat-details">
                    <div class="cna-detail-row">
                        <span class="cna-detail-label"><?php _e('Public IP:', 'cloud-network-analyzer'); ?></span>
                        <span class="cna-detail-value" id="cna-public-ip">-</span>
                    </div>
                    <div class="cna-detail-row">
                        <span class="cna-detail-label"><?php _e('Private IP:', 'cloud-network-analyzer'); ?></span>
                        <span class="cna-detail-value" id="cna-private-ip">-</span>
                    </div>
                    <div class="cna-detail-row">
                        <span class="cna-detail-label"><?php _e('ISP:', 'cloud-network-analyzer'); ?></span>
                        <span class="cna-detail-value" id="cna-isp">-</span>
                    </div>
                    <div class="cna-detail-row">
                        <span class="cna-detail-label"><?php _e('Location:', 'cloud-network-analyzer'); ?></span>
                        <span class="cna-detail-value" id="cna-location">-</span>
                    </div>
                    <div class="cna-detail-row">
                        <span class="cna-detail-label"><?php _e('IP Version:', 'cloud-network-analyzer'); ?></span>
                        <span class="cna-detail-value" id="cna-ip-version">-</span>
                    </div>
                </div>
                
                <?php if (isset($settings['enable_advanced_details']) && $settings['enable_advanced_details']): ?>
                <div class="cna-advanced-toggle">
                    <button class="cna-btn cna-btn-secondary cna-toggle-advanced">
                        <svg class="cna-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                        <?php _e('Advanced Details', 'cloud-network-analyzer'); ?>
                    </button>
                </div>
                
                <div class="cna-advanced-details" style="display: none;">
                    <div class="cna-detail-row">
                        <span class="cna-detail-label"><?php _e('Gateway IP:', 'cloud-network-analyzer'); ?></span>
                        <span class="cna-detail-value" id="cna-gateway-ip">-</span>
                    </div>
                    <div class="cna-detail-row">
                        <span class="cna-detail-label"><?php _e('Subnet Mask:', 'cloud-network-analyzer'); ?></span>
                        <span class="cna-detail-value" id="cna-subnet-mask">-</span>
                    </div>
                    <div class="cna-detail-row">
                        <span class="cna-detail-label"><?php _e('DNS Servers:', 'cloud-network-analyzer'); ?></span>
                        <span class="cna-detail-value" id="cna-dns-servers">-</span>
                    </div>
                    <div class="cna-detail-row">
                        <span class="cna-detail-label"><?php _e('UPnP Status:', 'cloud-network-analyzer'); ?></span>
                        <span class="cna-detail-value" id="cna-upnp-status">-</span>
                    </div>
                    <div class="cna-detail-row">
                        <span class="cna-detail-label"><?php _e('NAT Mapping:', 'cloud-network-analyzer'); ?></span>
                        <span class="cna-detail-value" id="cna-nat-mapping">-</span>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="cna-nat-explanation">
                    <h4><?php _e('What does this mean?', 'cloud-network-analyzer'); ?></h4>
                    <p class="cna-nat-explanation-text"></p>
                </div>
            </div>
            
            <div class="cna-loading" style="display: none;">
                <div class="cna-spinner"></div>
                <p><?php _e('Analyzing your network...', 'cloud-network-analyzer'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public static function port_checker($atts = array()) {
        $settings = get_option('cna_settings', array());
        
        ob_start();
        ?>
        <div class="cna-section cna-port-checker">
            <div class="cna-section-header">
                <h3 class="cna-section-title">
                    <svg class="cna-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <?php _e('Port Checker', 'cloud-network-analyzer'); ?>
                    <span class="cna-tooltip" data-tooltip="<?php esc_attr_e('Check if gaming ports are open for optimal connectivity', 'cloud-network-analyzer'); ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </span>
                </h3>
            </div>
            
            <div class="cna-port-input-section">
                <div class="cna-port-presets">
                    <label><?php _e('Quick Test:', 'cloud-network-analyzer'); ?></label>
                    <div class="cna-preset-buttons">
                        <button class="cna-btn cna-btn-preset" data-service="geforce-now"><?php _e('GeForce NOW', 'cloud-network-analyzer'); ?></button>
                        <button class="cna-btn cna-btn-preset" data-service="xbox-cloud"><?php _e('Xbox Cloud', 'cloud-network-analyzer'); ?></button>
                        <button class="cna-btn cna-btn-preset" data-service="playstation"><?php _e('PlayStation', 'cloud-network-analyzer'); ?></button>
                        <button class="cna-btn cna-btn-preset" data-service="boosteroid"><?php _e('Boosteroid', 'cloud-network-analyzer'); ?></button>
                        <button class="cna-btn cna-btn-preset" data-service="shadow"><?php _e('Shadow PC', 'cloud-network-analyzer'); ?></button>
                    </div>
                </div>
                
                <div class="cna-manual-port">
                    <label for="cna-port-input"><?php _e('Manual Port Test:', 'cloud-network-analyzer'); ?></label>
                    <div class="cna-port-input-group">
                        <input type="text" id="cna-port-input" class="cna-input" placeholder="<?php esc_attr_e('Enter ports (e.g., 3074, 3075-3077)', 'cloud-network-analyzer'); ?>">
                        <select id="cna-protocol-select" class="cna-select">
                            <option value="tcp">TCP</option>
                            <option value="udp">UDP</option>
                            <option value="both"><?php _e('Both', 'cloud-network-analyzer'); ?></option>
                        </select>
                        <button class="cna-btn cna-btn-primary cna-check-port">
                            <?php _e('Check Ports', 'cloud-network-analyzer'); ?>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="cna-port-results" style="display: none;">
                <table class="cna-port-table">
                    <thead>
                        <tr>
                            <th><?php _e('Port', 'cloud-network-analyzer'); ?></th>
                            <th><?php _e('Protocol', 'cloud-network-analyzer'); ?></th>
                            <th><?php _e('Status', 'cloud-network-analyzer'); ?></th>
                            <th><?php _e('Notes', 'cloud-network-analyzer'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="cna-port-results-body">
                    </tbody>
                </table>
            </div>
            
            <div class="cna-loading" style="display: none;">
                <div class="cna-spinner"></div>
                <p><?php _e('Testing ports...', 'cloud-network-analyzer'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public static function device_info($atts = array()) {
        ob_start();
        ?>
        <div class="cna-section cna-device-info">
            <div class="cna-section-header">
                <h3 class="cna-section-title">
                    <svg class="cna-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                    <?php _e('Device & Connection Info', 'cloud-network-analyzer'); ?>
                </h3>
                <button class="cna-btn cna-btn-primary cna-get-device-info">
                    <svg class="cna-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                    <?php _e('Detect Device', 'cloud-network-analyzer'); ?>
                </button>
            </div>
            
            <div class="cna-device-result" style="display: none;">
                <div class="cna-device-grid">
                    <div class="cna-device-card">
                        <svg class="cna-device-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                        <h4><?php _e('Device', 'cloud-network-analyzer'); ?></h4>
                        <p id="cna-device-type">-</p>
                    </div>
                    <div class="cna-device-card">
                        <svg class="cna-device-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        <h4><?php _e('Browser', 'cloud-network-analyzer'); ?></h4>
                        <p id="cna-browser">-</p>
                    </div>
                    <div class="cna-device-card">
                        <svg class="cna-device-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                            <line x1="12" y1="18" x2="12.01" y2="18"></line>
                        </svg>
                        <h4><?php _e('OS', 'cloud-network-analyzer'); ?></h4>
                        <p id="cna-os">-</p>
                    </div>
                    <div class="cna-device-card">
                        <svg class="cna-device-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                            <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                            <line x1="12" y1="20" x2="12.01" y2="20"></line>
                        </svg>
                        <h4><?php _e('Connection', 'cloud-network-analyzer'); ?></h4>
                        <p id="cna-connection-type">-</p>
                    </div>
                </div>
                
                <div class="cna-network-test">
                    <h4><?php _e('Network Performance', 'cloud-network-analyzer'); ?></h4>
                    <button class="cna-btn cna-btn-secondary cna-run-network-test">
                        <?php _e('Run Network Test', 'cloud-network-analyzer'); ?>
                    </button>
                    <div class="cna-network-results" style="display: none;">
                        <div class="cna-network-metric">
                            <span class="cna-metric-label"><?php _e('Ping:', 'cloud-network-analyzer'); ?></span>
                            <span class="cna-metric-value" id="cna-ping">-</span>
                        </div>
                        <div class="cna-network-metric">
                            <span class="cna-metric-label"><?php _e('Jitter:', 'cloud-network-analyzer'); ?></span>
                            <span class="cna-metric-value" id="cna-jitter">-</span>
                        </div>
                        <div class="cna-network-metric">
                            <span class="cna-metric-label"><?php _e('Packet Loss:', 'cloud-network-analyzer'); ?></span>
                            <span class="cna-metric-value" id="cna-packet-loss">-</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="cna-loading" style="display: none;">
                <div class="cna-spinner"></div>
                <p><?php _e('Detecting device...', 'cloud-network-analyzer'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public static function educational_guides() {
        $settings = get_option('cna_settings', array());
        
        ob_start();
        ?>
        <div class="cna-section cna-guides">
            <div class="cna-section-header">
                <h3 class="cna-section-title">
                    <svg class="cna-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                    <?php _e('Gaming Network Guides', 'cloud-network-analyzer'); ?>
                </h3>
            </div>
            
            <div class="cna-accordion">
                <div class="cna-accordion-item">
                    <button class="cna-accordion-header">
                        <svg class="cna-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                        <?php _e('How to Fix Strict NAT Types', 'cloud-network-analyzer'); ?>
                    </button>
                    <div class="cna-accordion-content">
                        <h4><?php _e('Method 1: Enable UPnP', 'cloud-network-analyzer'); ?></h4>
                        <ol>
                            <li><?php _e('Log into your router admin panel', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Navigate to UPnP settings (usually under Advanced or Security)', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Enable UPnP and save changes', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Restart your router and gaming device', 'cloud-network-analyzer'); ?></li>
                        </ol>
                        
                        <h4><?php _e('Method 2: Port Forwarding', 'cloud-network-analyzer'); ?></h4>
                        <ol>
                            <li><?php _e('Find your device\'s local IP address', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Access your router settings', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Navigate to Port Forwarding section', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Add port forwarding rules for your gaming service', 'cloud-network-analyzer'); ?></li>
                        </ol>
                        
                        <h4><?php _e('Method 3: DMZ (Use with caution)', 'cloud-network-analyzer'); ?></h4>
                        <ol>
                            <li><?php _e('Access router settings', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Find DMZ settings (usually under Advanced)', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Enter your gaming device\'s IP address', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Enable DMZ and save changes', 'cloud-network-analyzer'); ?></li>
                        </ol>
                        <p class="cna-warning">⚠️ <?php _e('DMZ exposes your device to the internet. Only use if other methods fail.', 'cloud-network-analyzer'); ?></p>
                    </div>
                </div>
                
                <?php if (isset($settings['enable_router_links']) && $settings['enable_router_links']): ?>
                <div class="cna-accordion-item">
                    <button class="cna-accordion-header">
                        <svg class="cna-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                        <?php _e('Router Login Database', 'cloud-network-analyzer'); ?>
                    </button>
                    <div class="cna-accordion-content">
                        <p><?php _e('Common router default login credentials:', 'cloud-network-analyzer'); ?></p>
                        <table class="cna-router-table">
                            <thead>
                                <tr>
                                    <th><?php _e('Brand', 'cloud-network-analyzer'); ?></th>
                                    <th><?php _e('Default IP', 'cloud-network-analyzer'); ?></th>
                                    <th><?php _e('Username', 'cloud-network-analyzer'); ?></th>
                                    <th><?php _e('Password', 'cloud-network-analyzer'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Netgear</td>
                                    <td><code>192.168.1.1</code></td>
                                    <td>admin</td>
                                    <td>password</td>
                                </tr>
                                <tr>
                                    <td>TP-Link</td>
                                    <td><code>192.168.0.1</code></td>
                                    <td>admin</td>
                                    <td>admin</td>
                                </tr>
                                <tr>
                                    <td>Linksys</td>
                                    <td><code>192.168.1.1</code></td>
                                    <td>admin</td>
                                    <td>admin</td>
                                </tr>
                                <tr>
                                    <td>Asus</td>
                                    <td><code>192.168.1.1</code></td>
                                    <td>admin</td>
                                    <td>admin</td>
                                </tr>
                                <tr>
                                    <td>D-Link</td>
                                    <td><code>192.168.0.1</code></td>
                                    <td>admin</td>
                                    <td>(blank)</td>
                                </tr>
                                <tr>
                                    <td>Belkin</td>
                                    <td><code>192.168.2.1</code></td>
                                    <td>(blank)</td>
                                    <td>(blank)</td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="cna-note">📝 <?php _e('Note: Change default passwords immediately after first login for security.', 'cloud-network-analyzer'); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="cna-accordion-item">
                    <button class="cna-accordion-header">
                        <svg class="cna-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                        <?php _e('Port Forwarding Guide', 'cloud-network-analyzer'); ?>
                    </button>
                    <div class="cna-accordion-content">
                        <h4><?php _e('Step-by-Step Port Forwarding', 'cloud-network-analyzer'); ?></h4>
                        
                        <h5><?php _e('1. Find Your Local IP Address', 'cloud-network-analyzer'); ?></h5>
                        <ul>
                            <li><strong>Windows:</strong> <?php _e('Open CMD and type', 'cloud-network-analyzer'); ?> <code>ipconfig</code></li>
                            <li><strong>Mac:</strong> <?php _e('System Preferences → Network', 'cloud-network-analyzer'); ?></li>
                            <li><strong>Console:</strong> <?php _e('Settings → Network → View Connection Status', 'cloud-network-analyzer'); ?></li>
                        </ul>
                        
                        <h5><?php _e('2. Access Your Router', 'cloud-network-analyzer'); ?></h5>
                        <p><?php _e('Enter your router\'s IP address in a web browser (usually 192.168.1.1 or 192.168.0.1)', 'cloud-network-analyzer'); ?></p>
                        
                        <h5><?php _e('3. Configure Port Forwarding', 'cloud-network-analyzer'); ?></h5>
                        <ul>
                            <li><?php _e('Look for "Port Forwarding", "Virtual Server", or "Applications" in settings', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Create a new rule with these details:', 'cloud-network-analyzer'); ?>
                                <ul>
                                    <li><?php _e('Service Name: Custom name (e.g., "Xbox Gaming")', 'cloud-network-analyzer'); ?></li>
                                    <li><?php _e('Port Range: Gaming service ports', 'cloud-network-analyzer'); ?></li>
                                    <li><?php _e('Local IP: Your device\'s IP address', 'cloud-network-analyzer'); ?></li>
                                    <li><?php _e('Protocol: TCP, UDP, or Both', 'cloud-network-analyzer'); ?></li>
                                </ul>
                            </li>
                            <li><?php _e('Save and apply settings', 'cloud-network-analyzer'); ?></li>
                        </ul>
                        
                        <h5><?php _e('Common Gaming Ports', 'cloud-network-analyzer'); ?></h5>
                        <ul>
                            <li><strong>GeForce NOW:</strong> TCP: 47989-47990, UDP: 47998-48010</li>
                            <li><strong>Xbox:</strong> TCP: 3074, UDP: 88, 500, 3074, 3544, 4500</li>
                            <li><strong>PlayStation:</strong> TCP: 80, 443, 3478-3480, UDP: 3478-3479</li>
                            <li><strong>Steam:</strong> TCP: 27015-27030, UDP: 27000-27100</li>
                        </ul>
                    </div>
                </div>
                
                <div class="cna-accordion-item">
                    <button class="cna-accordion-header">
                        <svg class="cna-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                        <?php _e('QoS & DSCP Tagging', 'cloud-network-analyzer'); ?>
                    </button>
                    <div class="cna-accordion-content">
                        <h4><?php _e('Quality of Service (QoS) Setup', 'cloud-network-analyzer'); ?></h4>
                        <p><?php _e('QoS prioritizes gaming traffic over other network activities for smoother gameplay.', 'cloud-network-analyzer'); ?></p>
                        
                        <h5><?php _e('Enabling QoS:', 'cloud-network-analyzer'); ?></h5>
                        <ol>
                            <li><?php _e('Access your router settings', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Find QoS settings (Advanced → QoS or Traffic Control)', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Enable QoS', 'cloud-network-analyzer'); ?></li>
                            <li><?php _e('Set priority rules:', 'cloud-network-analyzer'); ?>
                                <ul>
                                    <li><?php _e('Highest: Gaming devices/applications', 'cloud-network-analyzer'); ?></li>
                                    <li><?php _e('Medium: Streaming services', 'cloud-network-analyzer'); ?></li>
                                    <li><?php _e('Low: Downloads/file sharing', 'cloud-network-analyzer'); ?></li>
                                </ul>
                            </li>
                        </ol>
                        
                        <h5><?php _e('DSCP Tagging', 'cloud-network-analyzer'); ?></h5>
                        <p><?php _e('For advanced users: DSCP tags packets for network priority. Common values:', 'cloud-network-analyzer'); ?></p>
                        <ul>
                            <li><strong>CS5 (40):</strong> <?php _e('Gaming traffic', 'cloud-network-analyzer'); ?></li>
                            <li><strong>EF (46):</strong> <?php _e('Voice/real-time gaming', 'cloud-network-analyzer'); ?></li>
                            <li><strong>AF41 (34):</strong> <?php _e('Streaming video', 'cloud-network-analyzer'); ?></li>
                        </ul>
                    </div>
                </div>
                
                <div class="cna-accordion-item">
                    <button class="cna-accordion-header">
                        <svg class="cna-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                        <?php _e('Cloud Gaming Optimization Tips', 'cloud-network-analyzer'); ?>
                    </button>
                    <div class="cna-accordion-content">
                        <h4><?php _e('Reduce Latency', 'cloud-network-analyzer'); ?></h4>
                        <ul>
                            <li>✅ <?php _e('Use wired Ethernet connection instead of Wi-Fi', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Close bandwidth-heavy applications (downloads, streaming)', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Connect to nearest server/data center', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Use 5GHz Wi-Fi band if wired isn\'t possible', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Update router firmware regularly', 'cloud-network-analyzer'); ?></li>
                        </ul>
                        
                        <h4><?php _e('Minimize Packet Loss', 'cloud-network-analyzer'); ?></h4>
                        <ul>
                            <li>✅ <?php _e('Check and replace damaged Ethernet cables', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Reduce Wi-Fi interference (move away from microwaves, cordless phones)', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Use a gaming VPN with optimized routes (if ISP throttles)', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Contact ISP if persistent issues occur', 'cloud-network-analyzer'); ?></li>
                        </ul>
                        
                        <h4><?php _e('Improve Stability', 'cloud-network-analyzer'); ?></h4>
                        <ul>
                            <li>✅ <?php _e('Restart router before gaming sessions', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Limit connected devices during gaming', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Enable QoS for gaming traffic priority', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Consider upgrading internet plan if speeds are insufficient', 'cloud-network-analyzer'); ?></li>
                            <li>✅ <?php _e('Use a dedicated gaming router with advanced features', 'cloud-network-analyzer'); ?></li>
                        </ul>
                        
                        <h4><?php _e('Recommended Minimum Requirements', 'cloud-network-analyzer'); ?></h4>
                        <ul>
                            <li><strong><?php _e('Download Speed:', 'cloud-network-analyzer'); ?></strong> 25+ Mbps (50+ Mbps for 4K)</li>
                            <li><strong><?php _e('Upload Speed:', 'cloud-network-analyzer'); ?></strong> 5+ Mbps</li>
                            <li><strong><?php _e('Ping:', 'cloud-network-analyzer'); ?></strong> &lt;60ms (ideal: &lt;30ms)</li>
                            <li><strong><?php _e('Jitter:', 'cloud-network-analyzer'); ?></strong> &lt;10ms</li>
                            <li><strong><?php _e('Packet Loss:', 'cloud-network-analyzer'); ?></strong> &lt;1%</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private static function get_theme_class($theme) {
        $settings = get_option('cna_settings', array());
        $theme_mode = !empty($theme) ? $theme : (isset($settings['theme_mode']) ? $settings['theme_mode'] : 'light');
        
        return 'cna-theme-' . $theme_mode;
    }
}
