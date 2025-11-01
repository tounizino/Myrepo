<?php
/**
 * Full Tool Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$theme_class = 'theme-' . $settings['theme'];
$font_size_class = 'font-size-' . $settings['fontSize'];
$container_width = $settings['containerWidth'] . 'px';
?>

<div class="unpc-wrapper <?php echo esc_attr($theme_class . ' ' . $font_size_class); ?>" data-animations="<?php echo esc_attr($settings['enableAnimations']); ?>">
    <style>
        .unpc-wrapper {
            --container-width: <?php echo esc_attr($container_width); ?>;
            --color-primary: <?php echo esc_attr($settings['colorPrimary']); ?>;
            --color-success: <?php echo esc_attr($settings['colorSuccess']); ?>;
            --color-warning: <?php echo esc_attr($settings['colorWarning']); ?>;
            --color-error: <?php echo esc_attr($settings['colorError']); ?>;
            --color-info: <?php echo esc_attr($settings['colorInfo']); ?>;
        }
        <?php if (!empty($settings['customCSS'])) : ?>
        <?php echo wp_kses_post($settings['customCSS']); ?>
        <?php endif; ?>
    </style>

    <!-- Header -->
    <div class="unpc-header">
        <button class="unpc-theme-toggle" data-theme="<?php echo esc_attr($settings['theme']); ?>">
            <i class="fas fa-<?php echo $settings['theme'] === 'dark' ? 'moon' : 'sun'; ?>"></i>
            <span><?php echo $settings['theme'] === 'dark' ? __('Dark Mode', 'ultimate-nat-port-checker') : __('Light Mode', 'ultimate-nat-port-checker'); ?></span>
        </button>
    </div>

    <!-- Main Container -->
    <div class="unpc-main-container">
        <h1 class="unpc-tool-title"><?php esc_html_e('Ultimate NAT & Port Checker for Cloud Gaming', 'ultimate-nat-port-checker'); ?></h1>
        
        <?php if ($settings['showSecurityNote'] === '1') : ?>
        <div class="unpc-security-note">
            <i class="fas fa-shield-alt"></i> 
            <?php esc_html_e('This tool doesn\'t store any of your data. All checks are performed securely in your browser.', 'ultimate-nat-port-checker'); ?>
        </div>
        <?php endif; ?>

        <!-- NAT Checker Section -->
        <div class="unpc-tool-section">
            <h2 class="unpc-section-title">
                <i class="fas fa-network-wired"></i>
                <?php esc_html_e('NAT Type Checker', 'ultimate-nat-port-checker'); ?>
            </h2>
            <button class="unpc-check-nat-btn">
                <i class="fas fa-search"></i>
                <?php esc_html_e('Check NAT Type', 'ultimate-nat-port-checker'); ?>
            </button>
            <div class="unpc-nat-result" style="display:none;"></div>
            <?php if ($settings['showTechnicalLog'] === '1') : ?>
            <div class="unpc-technical-log-wrapper" style="display:none;">
                <button class="unpc-toggle-log">
                    <i class="fas fa-chevron-down"></i>
                    <span><?php esc_html_e('Show Technical Details', 'ultimate-nat-port-checker'); ?></span>
                </button>
                <div class="unpc-technical-log" style="display:none;"></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Port Checker Section -->
        <div class="unpc-tool-section">
            <h2 class="unpc-section-title">
                <i class="fas fa-plug"></i>
                <?php esc_html_e('Multi-Port Checker', 'ultimate-nat-port-checker'); ?>
            </h2>
            <div class="unpc-port-presets">
                <button class="unpc-preset-btn" data-ports="3074"><?php esc_html_e('Xbox Live', 'ultimate-nat-port-checker'); ?></button>
                <button class="unpc-preset-btn" data-ports="3478,3479,3480"><?php esc_html_e('PlayStation Network', 'ultimate-nat-port-checker'); ?></button>
                <button class="unpc-preset-btn" data-ports="1935,3478,3479"><?php esc_html_e('Steam', 'ultimate-nat-port-checker'); ?></button>
                <button class="unpc-preset-btn" data-ports="6000-6100"><?php esc_html_e('GeForce NOW', 'ultimate-nat-port-checker'); ?></button>
                <button class="unpc-preset-btn" data-ports="80,443,1935"><?php esc_html_e('Google Stadia', 'ultimate-nat-port-checker'); ?></button>
                <button class="unpc-preset-btn" data-ports="27015-27030"><?php esc_html_e('Steam Gaming', 'ultimate-nat-port-checker'); ?></button>
                <button class="unpc-preset-btn" data-ports="3074,3544,4500"><?php esc_html_e('Call of Duty', 'ultimate-nat-port-checker'); ?></button>
                <button class="unpc-preset-btn" data-ports="5000-5500"><?php esc_html_e('Shadow PC', 'ultimate-nat-port-checker'); ?></button>
                <?php
                $custom_presets = get_option('unpc_custom_presets', '');
                if (!empty($custom_presets)) {
                    $presets = explode("\n", $custom_presets);
                    foreach ($presets as $preset) {
                        $parts = explode(':', $preset);
                        if (count($parts) === 2) {
                            $label = trim($parts[0]);
                            $ports = trim($parts[1]);
                            echo '<button class="unpc-preset-btn" data-ports="' . esc_attr($ports) . '">' . esc_html($label) . '</button>';
                        }
                    }
                }
                ?>
                <button class="unpc-preset-btn" data-ports="custom"><?php esc_html_e('Custom Ports', 'ultimate-nat-port-checker'); ?></button>
            </div>
            <div class="unpc-port-input">
                <input type="text" class="unpc-port-input-field" placeholder="<?php esc_attr_e('Enter port(s) - e.g., 80,443 or 3000-3100', 'ultimate-nat-port-checker'); ?>" value="3074">
                <button class="unpc-check-port-btn"><?php esc_html_e('Check Ports', 'ultimate-nat-port-checker'); ?></button>
            </div>
            <div class="unpc-port-result" style="display:none;"></div>
            <div class="unpc-port-details" style="display:none;"></div>
        </div>

        <?php if ($settings['showDeviceInfo'] === '1') : ?>
        <!-- Device Details Section -->
        <div class="unpc-tool-section">
            <h2 class="unpc-section-title">
                <i class="fas fa-laptop"></i>
                <?php esc_html_e('Your Device Details', 'ultimate-nat-port-checker'); ?>
            </h2>
            <div class="unpc-device-info-grid">
                <div class="unpc-device-info-item">
                    <div class="unpc-device-info-label"><?php esc_html_e('IP Address', 'ultimate-nat-port-checker'); ?></div>
                    <div class="unpc-device-info-value" data-info="ip"><?php esc_html_e('Loading...', 'ultimate-nat-port-checker'); ?></div>
                </div>
                <div class="unpc-device-info-item">
                    <div class="unpc-device-info-label"><?php esc_html_e('Country', 'ultimate-nat-port-checker'); ?></div>
                    <div class="unpc-device-info-value" data-info="country"><?php esc_html_e('Loading...', 'ultimate-nat-port-checker'); ?></div>
                </div>
                <div class="unpc-device-info-item">
                    <div class="unpc-device-info-label"><?php esc_html_e('ISP', 'ultimate-nat-port-checker'); ?></div>
                    <div class="unpc-device-info-value" data-info="isp"><?php esc_html_e('Loading...', 'ultimate-nat-port-checker'); ?></div>
                </div>
                <div class="unpc-device-info-item">
                    <div class="unpc-device-info-label"><?php esc_html_e('Browser', 'ultimate-nat-port-checker'); ?></div>
                    <div class="unpc-device-info-value" data-info="browser"><?php esc_html_e('Loading...', 'ultimate-nat-port-checker'); ?></div>
                </div>
                <div class="unpc-device-info-item">
                    <div class="unpc-device-info-label"><?php esc_html_e('OS', 'ultimate-nat-port-checker'); ?></div>
                    <div class="unpc-device-info-value" data-info="os"><?php esc_html_e('Loading...', 'ultimate-nat-port-checker'); ?></div>
                </div>
                <div class="unpc-device-info-item">
                    <div class="unpc-device-info-label"><?php esc_html_e('Screen Resolution', 'ultimate-nat-port-checker'); ?></div>
                    <div class="unpc-device-info-value" data-info="screen"><?php esc_html_e('Loading...', 'ultimate-nat-port-checker'); ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($settings['showRouterLogins'] === '1') : ?>
        <!-- Router Login Section -->
        <div class="unpc-tool-section">
            <h2 class="unpc-section-title">
                <i class="fas fa-wifi"></i>
                <?php esc_html_e('Router Login & Setup', 'ultimate-nat-port-checker'); ?>
            </h2>
            <div class="unpc-router-login-grid">
                <?php
                $routers = array(
                    array('brand' => 'Netgear', 'login' => 'admin', 'password' => 'password', 'ip' => '192.168.1.1'),
                    array('brand' => 'TP-Link', 'login' => 'admin', 'password' => 'admin', 'ip' => '192.168.0.1'),
                    array('brand' => 'ASUS', 'login' => 'admin', 'password' => 'admin', 'ip' => '192.168.1.1'),
                    array('brand' => 'Linksys', 'login' => 'admin', 'password' => 'admin', 'ip' => '192.168.1.1'),
                    array('brand' => 'D-Link', 'login' => 'admin', 'password' => '(blank)', 'ip' => '192.168.0.1'),
                    array('brand' => 'Belkin', 'login' => '(blank)', 'password' => '(blank)', 'ip' => '192.168.2.1'),
                    array('brand' => 'Huawei', 'login' => 'admin', 'password' => 'admin', 'ip' => '192.168.3.1'),
                    array('brand' => 'Zyxel', 'login' => 'admin', 'password' => '1234', 'ip' => '192.168.1.1'),
                    array('brand' => 'Arris', 'login' => 'admin', 'password' => 'password', 'ip' => '192.168.100.1'),
                );
                foreach ($routers as $router) :
                ?>
                <div class="unpc-router-item">
                    <div class="unpc-router-brand"><?php echo esc_html($router['brand']); ?></div>
                    <div class="unpc-router-credentials">
                        <?php echo sprintf(__('Login: %s<br>Password: %s', 'ultimate-nat-port-checker'), esc_html($router['login']), esc_html($router['password'])); ?>
                    </div>
                    <a href="http://<?php echo esc_attr($router['ip']); ?>" class="unpc-router-link" target="_blank" rel="noopener">
                        <?php esc_html_e('Login to Router', 'ultimate-nat-port-checker'); ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="unpc-router-tips">
                <p><strong><?php esc_html_e('Quick NAT Setup:', 'ultimate-nat-port-checker'); ?></strong> <?php esc_html_e('Enable UPnP in router settings for automatic port forwarding.', 'ultimate-nat-port-checker'); ?></p>
                <p><strong><?php esc_html_e('QoS Setup:', 'ultimate-nat-port-checker'); ?></strong> <?php esc_html_e('Prioritize gaming traffic by setting QoS rules for your gaming device\'s IP.', 'ultimate-nat-port-checker'); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($settings['showGuides'] === '1') : ?>
        <!-- Guides Section -->
        <div class="unpc-tool-section">
            <h2 class="unpc-section-title">
                <i class="fas fa-book"></i>
                <?php esc_html_e('Helpful Guides', 'ultimate-nat-port-checker'); ?>
            </h2>
            <div class="unpc-guide-cards">
                <div class="unpc-guide-card">
                    <div class="unpc-guide-title"><?php esc_html_e('How to Fix NAT Type Issues', 'ultimate-nat-port-checker'); ?></div>
                    <div class="unpc-guide-desc"><?php esc_html_e('Complete guide to achieving Open NAT for optimal cloud gaming performance.', 'ultimate-nat-port-checker'); ?></div>
                </div>
                <div class="unpc-guide-card">
                    <div class="unpc-guide-title"><?php esc_html_e('Router QoS Configuration', 'ultimate-nat-port-checker'); ?></div>
                    <div class="unpc-guide-desc"><?php esc_html_e('Step-by-step setup for prioritizing gaming traffic on popular routers.', 'ultimate-nat-port-checker'); ?></div>
                </div>
                <div class="unpc-guide-card">
                    <div class="unpc-guide-title"><?php esc_html_e('Port Forwarding Master Guide', 'ultimate-nat-port-checker'); ?></div>
                    <div class="unpc-guide-desc"><?php esc_html_e('Learn when and how to manually forward ports for better connectivity.', 'ultimate-nat-port-checker'); ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($settings['showFooter'] === '1') : ?>
    <!-- Footer -->
    <div class="unpc-footer">
        <div class="unpc-footer-content">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'ultimate-nat-port-checker'); ?></p>
            <p><?php esc_html_e('This tool is provided for educational and troubleshooting purposes only.', 'ultimate-nat-port-checker'); ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>
