<?php
/**
 * Front-end template for the Ultimate NAT & Port Checker.
 *
 * @package UltimateNATPortChecker
 */

if (!defined('ABSPATH')) {
    exit;
}

$settings = unpc_get_settings();
$theme = isset($settings['theme']) ? $settings['theme'] : 'light';
$animations_enabled = !empty($settings['enable_animations']);

$router_logins = array(
    array(
        'brand' => 'Asus (Gaming Series)',
        'url' => 'http://192.168.1.1',
        'username' => 'admin',
        'password' => 'admin',
        'notes' => __('Look for “Adaptive QoS” to prioritise gaming traffic.', 'ultimate-nat-port-checker'),
    ),
    array(
        'brand' => 'Netgear Nighthawk',
        'url' => 'http://192.168.0.1',
        'username' => 'admin',
        'password' => 'password',
        'notes' => __('Enable UPnP and check “Traffic Meter” for congestion stats.', 'ultimate-nat-port-checker'),
    ),
    array(
        'brand' => 'TP-Link Archer',
        'url' => 'http://tplinkwifi.net',
        'username' => 'admin',
        'password' => 'admin',
        'notes' => __('Use the “NAT Boost” and “Game Accelerator” toggles if available.', 'ultimate-nat-port-checker'),
    ),
    array(
        'brand' => 'Linksys Velop',
        'url' => 'http://192.168.1.1',
        'username' => 'admin',
        'password' => 'admin',
        'notes' => __('Check the app for “Device Prioritisation” to favour your gaming device.', 'ultimate-nat-port-checker'),
    ),
);
?>
<div class="unpc-wrapper" data-default-theme="<?php echo esc_attr($theme); ?>" data-animations="<?php echo $animations_enabled ? 'enabled' : 'disabled'; ?>">
    <div class="unpc-container" data-theme="<?php echo esc_attr($theme); ?>" data-default-theme="<?php echo esc_attr($theme); ?>">
        <header class="unpc-header">
            <div class="unpc-header__titles">
                <h2><?php esc_html_e('Ultimate NAT & Port Checker', 'ultimate-nat-port-checker'); ?></h2>
                <p>
                    <?php esc_html_e('Next-generation diagnostics crafted for cloud gaming visitors. Analyse NAT type, validate ports, and fine-tune your network in minutes.', 'ultimate-nat-port-checker'); ?>
                </p>
            </div>
            <div class="unpc-header__toggles">
                <div class="unpc-theme-toggle" role="group" aria-label="<?php esc_attr_e('Theme toggle', 'ultimate-nat-port-checker'); ?>">
                    <button type="button" class="unpc-pill" data-theme-target="light" aria-pressed="<?php echo 'light' === $theme ? 'true' : 'false'; ?>"><?php esc_html_e('Light', 'ultimate-nat-port-checker'); ?></button>
                    <button type="button" class="unpc-pill" data-theme-target="dark" aria-pressed="<?php echo 'dark' === $theme ? 'true' : 'false'; ?>"><?php esc_html_e('Dark', 'ultimate-nat-port-checker'); ?></button>
                </div>
                <span class="unpc-badge" data-field="quick-tip"><?php echo esc_html($settings['custom_quick_tip']); ?></span>
            </div>
        </header>

        <?php if (!empty($settings['enable_nat_checker'])) : ?>
            <section class="unpc-card unpc-card--nat" id="unpc-nat" aria-labelledby="unpc-nat-title">
                <div class="unpc-card__header">
                    <div>
                        <h3 id="unpc-nat-title"><?php esc_html_e('NAT Intelligence', 'ultimate-nat-port-checker'); ?></h3>
                        <p><?php esc_html_e('Determine your NAT type with live WebRTC diagnostics and real-world connection insights.', 'ultimate-nat-port-checker'); ?></p>
                    </div>
                    <button type="button" class="unpc-button" data-action="run-nat">
                        <span class="unpc-button__label" data-label-idle="<?php esc_attr_e('Run NAT Analysis', 'ultimate-nat-port-checker'); ?>" data-label-busy="<?php esc_attr_e('Analysing…', 'ultimate-nat-port-checker'); ?>">
                            <?php esc_html_e('Run NAT Analysis', 'ultimate-nat-port-checker'); ?>
                        </span>
                    </button>
                </div>

                <div class="unpc-nat__body">
                    <div class="unpc-nat__status" data-nat-state="idle" data-nat-tier="idle">
                        <span class="unpc-nat__type">--</span>
                        <span class="unpc-nat__label"><?php esc_html_e('Awaiting analysis', 'ultimate-nat-port-checker'); ?></span>
                        <span class="unpc-nat__description">&nbsp;</span>
                    </div>
                    <div class="unpc-nat__metrics">
                        <div class="unpc-metric">
                            <span class="unpc-metric__label"><?php esc_html_e('Public IP', 'ultimate-nat-port-checker'); ?></span>
                            <span class="unpc-metric__value" data-field="ip-address">—</span>
                        </div>
                        <div class="unpc-metric">
                            <span class="unpc-metric__label"><?php esc_html_e('Region', 'ultimate-nat-port-checker'); ?></span>
                            <span class="unpc-metric__value" data-field="location">—</span>
                        </div>
                        <div class="unpc-metric">
                            <span class="unpc-metric__label"><?php esc_html_e('ISP', 'ultimate-nat-port-checker'); ?></span>
                            <span class="unpc-metric__value" data-field="isp">—</span>
                        </div>
                        <div class="unpc-metric">
                            <span class="unpc-metric__label"><?php esc_html_e('STUN server latency', 'ultimate-nat-port-checker'); ?></span>
                            <span class="unpc-metric__value" data-field="stun-latency">—</span>
                        </div>
                    </div>
                    <div class="unpc-status" data-status="nat">&nbsp;</div>
                </div>

                <details class="unpc-details" data-detail="nat-tech">
                    <summary><?php esc_html_e('Technical breakdown', 'ultimate-nat-port-checker'); ?></summary>
                    <div class="unpc-details__grid">
                        <div>
                            <h4><?php esc_html_e('ICE Candidates & Mapping', 'ultimate-nat-port-checker'); ?></h4>
                            <ul class="unpc-list" data-field="ice-candidates">
                                <li><?php esc_html_e('Run the analysis to reveal NAT traversal details.', 'ultimate-nat-port-checker'); ?></li>
                            </ul>
                        </div>
                        <div>
                            <h4><?php esc_html_e('NAT Summary', 'ultimate-nat-port-checker'); ?></h4>
                            <ul class="unpc-list" data-field="nat-summary">
                                <li><?php esc_html_e('The tool evaluates port preservation, address mapping, and relay fallbacks.', 'ultimate-nat-port-checker'); ?></li>
                            </ul>
                        </div>
                        <div>
                            <h4><?php esc_html_e('Recommendations', 'ultimate-nat-port-checker'); ?></h4>
                            <ul class="unpc-list" data-field="nat-recommendations">
                                <li><?php esc_html_e('Initiate a scan to receive tailored configuration advice.', 'ultimate-nat-port-checker'); ?></li>
                            </ul>
                        </div>
                    </div>
                </details>
            </section>
        <?php endif; ?>

        <?php if (!empty($settings['enable_port_checker'])) : ?>
            <section class="unpc-card unpc-card--ports" id="unpc-ports" aria-labelledby="unpc-port-title">
                <div class="unpc-card__header">
                    <div>
                        <h3 id="unpc-port-title"><?php esc_html_e('Port Availability Suite', 'ultimate-nat-port-checker'); ?></h3>
                        <p><?php esc_html_e('Batch-test essential cloud gaming ports, apply curated presets, and confirm forwarding rules instantly.', 'ultimate-nat-port-checker'); ?></p>
                    </div>
                    <button type="button" class="unpc-button unpc-button--ghost" data-action="reset-ports"><?php esc_html_e('Reset', 'ultimate-nat-port-checker'); ?></button>
                </div>

                <div class="unpc-ports__grid">
                    <div class="unpc-input-group">
                        <label for="unpc-port-host"><?php esc_html_e('Host or IP', 'ultimate-nat-port-checker'); ?></label>
                        <div class="unpc-input-with-action">
                            <input type="text" id="unpc-port-host" name="unpc-port-host" data-field="port-host" placeholder="<?php esc_attr_e('Auto-detect public IP', 'ultimate-nat-port-checker'); ?>">
                            <button type="button" class="unpc-button unpc-button--subtle" data-action="use-my-ip"><?php esc_html_e('Use my IP', 'ultimate-nat-port-checker'); ?></button>
                        </div>
                        <p class="unpc-help"><?php esc_html_e('Leave blank to auto-detect. You can also enter a hostname or LAN IP for targeted checks.', 'ultimate-nat-port-checker'); ?></p>
                    </div>
                    <div class="unpc-input-group">
                        <label for="unpc-port-list"><?php esc_html_e('Ports to check', 'ultimate-nat-port-checker'); ?></label>
                        <textarea id="unpc-port-list" data-field="port-list" rows="3" placeholder="80,443,3478-3480/udp"></textarea>
                        <p class="unpc-help"><?php esc_html_e('Separate ports with commas. Use ranges like 27015-27030. Append /udp for UDP ports.', 'ultimate-nat-port-checker'); ?></p>
                    </div>
                </div>

                <div class="unpc-presets" aria-label="<?php esc_attr_e('Cloud gaming port presets', 'ultimate-nat-port-checker'); ?>">
                    <span class="unpc-presets__title"><?php esc_html_e('Cloud gaming presets', 'ultimate-nat-port-checker'); ?></span>
                    <div class="unpc-presets__list" data-field="port-presets"></div>
                </div>

                <div class="unpc-actions">
                    <button type="button" class="unpc-button unpc-button--primary" data-action="run-port-scan">
                        <span class="unpc-button__label" data-label-idle="<?php esc_attr_e('Check Selected Ports', 'ultimate-nat-port-checker'); ?>" data-label-busy="<?php esc_attr_e('Scanning…', 'ultimate-nat-port-checker'); ?>">
                            <?php esc_html_e('Check Selected Ports', 'ultimate-nat-port-checker'); ?>
                        </span>
                    </button>
                    <span class="unpc-status" data-status="port">&nbsp;</span>
                </div>

                <div class="unpc-port-results" role="region" aria-live="polite">
                    <div class="unpc-port-summary" data-field="port-summary"></div>
                    <table class="unpc-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Port', 'ultimate-nat-port-checker'); ?></th>
                                <th><?php esc_html_e('Protocol', 'ultimate-nat-port-checker'); ?></th>
                                <th><?php esc_html_e('Service', 'ultimate-nat-port-checker'); ?></th>
                                <th><?php esc_html_e('Status', 'ultimate-nat-port-checker'); ?></th>
                                <th><?php esc_html_e('Response', 'ultimate-nat-port-checker'); ?></th>
                                <th><?php esc_html_e('Notes', 'ultimate-nat-port-checker'); ?></th>
                            </tr>
                        </thead>
                        <tbody data-field="port-results">
                            <tr class="unpc-table__empty">
                                <td colspan="6"><?php esc_html_e('Add ports and launch the scan to see results.', 'ultimate-nat-port-checker'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($settings['enable_router_logins'])) : ?>
            <section class="unpc-card unpc-card--router" id="unpc-router" aria-labelledby="unpc-router-title">
                <div class="unpc-card__header">
                    <div>
                        <h3 id="unpc-router-title"><?php esc_html_e('Popular Router Login Shortcuts', 'ultimate-nat-port-checker'); ?></h3>
                        <p><?php esc_html_e('Quickly access your router dashboard to adjust NAT, port forwarding, and QoS rules.', 'ultimate-nat-port-checker'); ?></p>
                    </div>
                </div>
                <div class="unpc-router__list">
                    <?php foreach ($router_logins as $router) : ?>
                        <article class="unpc-router__item">
                            <header>
                                <h4><?php echo esc_html($router['brand']); ?></h4>
                                <a class="unpc-link" href="<?php echo esc_url($router['url']); ?>" target="_blank" rel="noreferrer noopener"><?php echo esc_html($router['url']); ?></a>
                            </header>
                            <dl>
                                <div>
                                    <dt><?php esc_html_e('Username', 'ultimate-nat-port-checker'); ?></dt>
                                    <dd><?php echo esc_html($router['username']); ?></dd>
                                </div>
                                <div>
                                    <dt><?php esc_html_e('Password', 'ultimate-nat-port-checker'); ?></dt>
                                    <dd><?php echo esc_html($router['password']); ?></dd>
                                </div>
                            </dl>
                            <p class="unpc-router__note"><?php echo esc_html($router['notes']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($settings['enable_device_info'])) : ?>
            <section class="unpc-card unpc-card--device" id="unpc-device" aria-labelledby="unpc-device-title">
                <div class="unpc-card__header">
                    <div>
                        <h3 id="unpc-device-title"><?php esc_html_e('Device Snapshot', 'ultimate-nat-port-checker'); ?></h3>
                        <p><?php esc_html_e('Review the hardware and connection attributes detected from your current session.', 'ultimate-nat-port-checker'); ?></p>
                    </div>
                </div>
                <div class="unpc-device__grid">
                    <div class="unpc-device__metric">
                        <span class="unpc-device__label"><?php esc_html_e('Platform', 'ultimate-nat-port-checker'); ?></span>
                        <span class="unpc-device__value" data-device="platform">—</span>
                    </div>
                    <div class="unpc-device__metric">
                        <span class="unpc-device__label"><?php esc_html_e('Browser & Version', 'ultimate-nat-port-checker'); ?></span>
                        <span class="unpc-device__value" data-device="browser">—</span>
                    </div>
                    <div class="unpc-device__metric">
                        <span class="unpc-device__label"><?php esc_html_e('Connection Type', 'ultimate-nat-port-checker'); ?></span>
                        <span class="unpc-device__value" data-device="connection">—</span>
                    </div>
                    <div class="unpc-device__metric">
                        <span class="unpc-device__label"><?php esc_html_e('Approx. Bandwidth', 'ultimate-nat-port-checker'); ?></span>
                        <span class="unpc-device__value" data-device="bandwidth">—</span>
                    </div>
                    <div class="unpc-device__metric">
                        <span class="unpc-device__label"><?php esc_html_e('Screen Resolution', 'ultimate-nat-port-checker'); ?></span>
                        <span class="unpc-device__value" data-device="resolution">—</span>
                    </div>
                    <div class="unpc-device__metric">
                        <span class="unpc-device__label"><?php esc_html_e('Hardware Threads', 'ultimate-nat-port-checker'); ?></span>
                        <span class="unpc-device__value" data-device="cores">—</span>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($settings['enable_useful_links'])) : ?>
            <section class="unpc-card unpc-card--links" id="unpc-links" aria-labelledby="unpc-links-title">
                <div class="unpc-card__header">
                    <div>
                        <h3 id="unpc-links-title"><?php esc_html_e('Useful Links', 'ultimate-nat-port-checker'); ?></h3>
                        <p><?php esc_html_e('Explore curated guides from our cloud gaming knowledge base.', 'ultimate-nat-port-checker'); ?></p>
                    </div>
                </div>
                <ul class="unpc-links-list">
                    <li>
                        <a class="unpc-link" href="<?php echo esc_url($settings['useful_link_1_url']); ?>" target="_blank" rel="noreferrer noopener">
                            <?php echo esc_html($settings['useful_link_1_text']); ?>
                        </a>
                    </li>
                    <li>
                        <a class="unpc-link" href="<?php echo esc_url($settings['useful_link_2_url']); ?>" target="_blank" rel="noreferrer noopener">
                            <?php echo esc_html($settings['useful_link_2_text']); ?>
                        </a>
                    </li>
                </ul>
            </section>
        <?php endif; ?>

        <?php if (!empty($settings['enable_guides'])) : ?>
            <section class="unpc-card unpc-card--guides" id="unpc-guides" aria-labelledby="unpc-guides-title">
                <div class="unpc-card__header">
                    <div>
                        <h3 id="unpc-guides-title"><?php esc_html_e('Advanced Router Config – Quick Guides', 'ultimate-nat-port-checker'); ?></h3>
                        <p><?php esc_html_e('Compact explainers to help you unlock Open NAT, master port forwarding, and prioritise gaming packets.', 'ultimate-nat-port-checker'); ?></p>
                    </div>
                </div>
                <div class="unpc-guides__list">
                    <details>
                        <summary><?php esc_html_e('Open NAT Playbook', 'ultimate-nat-port-checker'); ?></summary>
                        <ul class="unpc-list">
                            <li><?php esc_html_e('Assign a static IP to your console or gaming PC before applying port rules.', 'ultimate-nat-port-checker'); ?></li>
                            <li><?php esc_html_e('Enable UPnP for automatic port negotiation when available.', 'ultimate-nat-port-checker'); ?></li>
                            <li><?php esc_html_e('If your router supports it, activate “Full Cone NAT” or “NAT Acceleration”.', 'ultimate-nat-port-checker'); ?></li>
                            <li><?php esc_html_e('Avoid double NAT by using bridge mode on secondary routers.', 'ultimate-nat-port-checker'); ?></li>
                        </ul>
                    </details>
                    <details>
                        <summary><?php esc_html_e('Port Forwarding Blueprint', 'ultimate-nat-port-checker'); ?></summary>
                        <ul class="unpc-list">
                            <li><?php esc_html_e('Forward both the TCP and UDP ranges provided in the presets to the device IP.', 'ultimate-nat-port-checker'); ?></li>
                            <li><?php esc_html_e('Use consecutive port ranges when possible to simplify firewall rules.', 'ultimate-nat-port-checker'); ?></li>
                            <li><?php esc_html_e('After saving rules, reboot the router to clear stale NAT tables.', 'ultimate-nat-port-checker'); ?></li>
                            <li><?php esc_html_e('Verify the open ports using this tool and in-game network tests.', 'ultimate-nat-port-checker'); ?></li>
                        </ul>
                    </details>
                    <details>
                        <summary><?php esc_html_e('DSCP Tagging & QoS Enhancements', 'ultimate-nat-port-checker'); ?></summary>
                        <ul class="unpc-list">
                            <li><?php esc_html_e('Mark gaming traffic with DSCP EF (46) or CS6 priority where supported.', 'ultimate-nat-port-checker'); ?></li>
                            <li><?php esc_html_e('Pair DSCP tagging with Smart Queue Management (SQM) to tame bufferbloat.', 'ultimate-nat-port-checker'); ?></li>
                            <li><?php esc_html_e('Allocate at least 30% reserved bandwidth to gaming devices during heavy use.', 'ultimate-nat-port-checker'); ?></li>
                            <li><?php esc_html_e('Regularly update router firmware for the latest QoS and security improvements.', 'ultimate-nat-port-checker'); ?></li>
                        </ul>
                    </details>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>
