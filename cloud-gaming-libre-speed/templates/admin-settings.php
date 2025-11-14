<?php
/**
 * Admin settings page.
 *
 * @package CloudGamingSpeedTest
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap cgst-admin">
    <h1><?php esc_html_e('Plugin Settings', 'cloud-gaming-speed-test'); ?></h1>

    <div class="cgst-admin-card">
        <h2><?php esc_html_e('Frontend Usage', 'cloud-gaming-speed-test'); ?></h2>
        <p><?php esc_html_e('Embed the speed test widget anywhere using the shortcode:', 'cloud-gaming-speed-test'); ?></p>
        <p><code>[cloudspeedtest]</code></p>

        <h3><?php esc_html_e('Customization', 'cloud-gaming-speed-test'); ?></h3>
        <p><?php esc_html_e('You can customize the appearance and behavior by editing the template and CSS files located in:', 'cloud-gaming-speed-test'); ?></p>
        <ul class="cgst-list">
            <li><code><?php echo esc_html(CGST_PLUGIN_DIR); ?>templates/speed-test-template.php</code></li>
            <li><code><?php echo esc_html(CGST_PLUGIN_DIR); ?>assets/css/style.css</code></li>
            <li><code><?php echo esc_html(CGST_PLUGIN_DIR); ?>assets/js/speed-test.js</code></li>
        </ul>
    </div>

    <div class="cgst-admin-card">
        <h2><?php esc_html_e('LibreSpeed Configuration', 'cloud-gaming-speed-test'); ?></h2>
        <p><?php esc_html_e('This plugin integrates with LibreSpeed backends. To get started:', 'cloud-gaming-speed-test'); ?></p>
        <ol>
            <li><?php esc_html_e('Deploy LibreSpeed on your servers or use existing LibreSpeed instances.', 'cloud-gaming-speed-test'); ?></li>
            <li><?php esc_html_e('Add your backend URLs in the Server Presets tab.', 'cloud-gaming-speed-test'); ?></li>
            <li><?php esc_html_e('Ensure CORS is properly configured on your LibreSpeed servers.', 'cloud-gaming-speed-test'); ?></li>
        </ol>
        <p>
            <a href="https://github.com/librespeed/speedtest" target="_blank" rel="noopener noreferrer" class="button">
                <?php esc_html_e('LibreSpeed GitHub', 'cloud-gaming-speed-test'); ?>
            </a>
        </p>
    </div>

    <div class="cgst-admin-card">
        <h2><?php esc_html_e('Cloud Gaming Assessment Thresholds', 'cloud-gaming-speed-test'); ?></h2>
        <p><?php esc_html_e('The plugin uses the following criteria to rate connections:', 'cloud-gaming-speed-test'); ?></p>
        
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php esc_html_e('Rating', 'cloud-gaming-speed-test'); ?></th>
                    <th><?php esc_html_e('Download', 'cloud-gaming-speed-test'); ?></th>
                    <th><?php esc_html_e('Upload', 'cloud-gaming-speed-test'); ?></th>
                    <th><?php esc_html_e('Ping', 'cloud-gaming-speed-test'); ?></th>
                    <th><?php esc_html_e('Jitter', 'cloud-gaming-speed-test'); ?></th>
                    <th><?php esc_html_e('Packet Loss', 'cloud-gaming-speed-test'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr style="background: #d4edda;">
                    <td><strong><?php esc_html_e('Excellent', 'cloud-gaming-speed-test'); ?></strong></td>
                    <td>≥ 150 Mbps</td>
                    <td>≥ 25 Mbps</td>
                    <td>≤ 20 ms</td>
                    <td>≤ 5 ms</td>
                    <td>≤ 0.1%</td>
                </tr>
                <tr style="background: #d1ecf1;">
                    <td><strong><?php esc_html_e('Good', 'cloud-gaming-speed-test'); ?></strong></td>
                    <td>≥ 90 Mbps</td>
                    <td>≥ 15 Mbps</td>
                    <td>≤ 35 ms</td>
                    <td>≤ 8 ms</td>
                    <td>≤ 0.3%</td>
                </tr>
                <tr style="background: #fff3cd;">
                    <td><strong><?php esc_html_e('Fair', 'cloud-gaming-speed-test'); ?></strong></td>
                    <td>≥ 45 Mbps</td>
                    <td>≥ 8 Mbps</td>
                    <td>≤ 55 ms</td>
                    <td>≤ 12 ms</td>
                    <td>≤ 0.8%</td>
                </tr>
                <tr style="background: #f8d7da;">
                    <td><strong><?php esc_html_e('Poor', 'cloud-gaming-speed-test'); ?></strong></td>
                    <td colspan="5"><?php esc_html_e('Below Fair thresholds', 'cloud-gaming-speed-test'); ?></td>
                </tr>
            </tbody>
        </table>

        <p class="description">
            <?php esc_html_e('You can customize these thresholds by modifying the calculate_rating() method in includes/database.php (backend) and calculateRating() in assets/js/speed-test.js (frontend).', 'cloud-gaming-speed-test'); ?>
        </p>
    </div>

    <div class="cgst-admin-card">
        <h2><?php esc_html_e('About Cloud Gaming Speed Test', 'cloud-gaming-speed-test'); ?></h2>
        <p>
            <strong><?php esc_html_e('Version:', 'cloud-gaming-speed-test'); ?></strong> <?php echo esc_html(CGST_VERSION); ?><br />
            <strong><?php esc_html_e('Author:', 'cloud-gaming-speed-test'); ?></strong> Your Name<br />
        </p>
        <p><?php esc_html_e('This plugin provides a comprehensive speed test interface tailored specifically for cloud gaming enthusiasts. It leverages LibreSpeed for accurate measurements and offers instant assessments of network suitability.', 'cloud-gaming-speed-test'); ?></p>
    </div>
</div>
