<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['save_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'cloudloadout_settings')) {
    $options = get_option('cloudloadout_latency_options', array());
    
    // Update basic settings
    $options['test_timeout'] = intval($_POST['test_timeout']);
    $options['test_count'] = intval($_POST['test_count']);
    $options['enable_detailed_logs'] = isset($_POST['enable_detailed_logs']);
    $options['admin_theme'] = sanitize_text_field($_POST['admin_theme']);
    $options['show_results_chart'] = isset($_POST['show_results_chart']);
    $options['enable_analytics'] = isset($_POST['enable_analytics']);
    
    // Update platform settings
    $platforms = array();
    foreach ($options['supported_platforms'] as $key => $platform) {
        $platforms[$key] = array(
            'name' => $platform['name'],
            'enabled' => isset($_POST['platform_' . $key]),
            'servers' => isset($_POST['platform_' . $key . '_servers']) ? 
                        sanitize_textarea_field($_POST['platform_' . $key . '_servers']) : ''
        );
    }
    $options['supported_platforms'] = $platforms;
    
    update_option('cloudloadout_latency_options', $options);
    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'cloud-loadout-latency-tester') . '</p></div>';
}

$options = get_option('cloudloadout_latency_options', array());

// Helper function to get default servers
function cloudloadout_get_default_servers($platform_key) {
    $default_servers = array(
        'xbox_cloud' => "https://test.xbox.com\nhttps://xbox.com\nhttps://www.xbox.com/en-US/xbox-game-pass/cloud-gaming",
        'amazon_luna' => "https://luna.amazon.com\nhttps://www.amazon.com/luna",
        'shadow' => "https://shadow.tech\nhttps://www.shadow.tech",
        'boosteroid' => "https://boosteroid.com\nhttps://www.boosteroid.com",
        'playstation_cloud' => "https://playstation.com\nhttps://store.playstation.com",
        'nvidia_geforce' => "https://play.geforcenow.com\nhttps://www.nvidia.com/en-us/geforce-now",
        'microsoft_cloud' => "https://windows.microsoft.com\nhttps://docs.microsoft.com/en-us/azure/windows-365"
    );
    
    return $default_servers[$platform_key] ?? '';
}
?>
<div class="wrap cloudloadout-admin settings <?php echo esc_attr($options['admin_theme'] ?? 'light'); ?>-theme">
    <div class="cloudloadout-header">
        <h1><?php _e('Settings', 'cloud-loadout-latency-tester'); ?></h1>
        <p class="subtitle"><?php _e('Configure your Cloud Loadout Latency Tester plugin', 'cloud-loadout-latency-tester'); ?></p>
    </div>
    
    <div class="cloudloadout-settings-container">
        <div class="cloudloadout-settings-grid">
            <!-- Main Settings -->
            <div class="cloudloadout-card">
                <h3><?php _e('General Settings', 'cloud-loadout-latency-tester'); ?></h3>
                <form method="post" action="">
                    <?php wp_nonce_field('cloudloadout_settings'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Test Timeout', 'cloud-loadout-latency-tester'); ?></th>
                            <td>
                                <input type="number" name="test_timeout" value="<?php echo esc_attr($options['test_timeout'] ?? 5000); ?>" min="1000" max="30000" step="1000" />
                                <p class="description"><?php _e('Timeout for each latency test in milliseconds (1000-30000)', 'cloud-loadout-latency-tester'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Test Count', 'cloud-loadout-latency-tester'); ?></th>
                            <td>
                                <input type="number" name="test_count" value="<?php echo esc_attr($options['test_count'] ?? 5); ?>" min="1" max="20" />
                                <p class="description"><?php _e('Number of latency tests to perform (1-20)', 'cloud-loadout-latency-tester'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Admin Theme', 'cloud-loadout-latency-tester'); ?></th>
                            <td>
                                <select name="admin_theme">
                                    <option value="light" <?php selected($options['admin_theme'] ?? 'light', 'light'); ?>><?php _e('Light Theme', 'cloud-loadout-latency-tester'); ?></option>
                                    <option value="dark" <?php selected($options['admin_theme'] ?? 'light', 'dark'); ?>><?php _e('Dark Theme', 'cloud-loadout-latency-tester'); ?></option>
                                </select>
                                <p class="description"><?php _e('Choose your preferred admin interface theme', 'cloud-loadout-latency-tester'); ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <h4><?php _e('Feature Options', 'cloud-loadout-latency-tester'); ?></h4>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Detailed Logs', 'cloud-loadout-latency-tester'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="enable_detailed_logs" <?php checked($options['enable_detailed_logs'] ?? true); ?> />
                                    <?php _e('Enable detailed test logs', 'cloud-loadout-latency-tester'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Results Chart', 'cloud-loadout-latency-tester'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="show_results_chart" <?php checked($options['show_results_chart'] ?? true); ?> />
                                    <?php _e('Show results in chart format', 'cloud-loadout-latency-tester'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Analytics', 'cloud-loadout-latency-tester'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="enable_analytics" <?php checked($options['enable_analytics'] ?? true); ?> />
                                    <?php _e('Enable test analytics and reporting', 'cloud-loadout-latency-tester'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="submit-section">
                        <input type="submit" name="save_settings" class="button button-primary" value="<?php _e('Save Settings', 'cloud-loadout-latency-tester'); ?>" />
                    </div>
                </form>
            </div>
            
            <!-- Platform Settings -->
            <div class="cloudloadout-card">
                <h3><?php _e('Cloud Gaming Platforms', 'cloud-loadout-latency-tester'); ?></h3>
                <p class="description"><?php _e('Configure which cloud gaming platforms to test and their server endpoints', 'cloud-loadout-latency-tester'); ?></p>
                
                <?php foreach ($options['supported_platforms'] ?? array() as $key => $platform): ?>
                <div class="platform-settings-item">
                    <div class="platform-header">
                        <label class="platform-toggle">
                            <input type="checkbox" name="platform_<?php echo esc_attr($key); ?>" <?php checked($platform['enabled'] ?? true); ?> />
                            <span class="platform-name"><?php echo esc_html($platform['name']); ?></span>
                        </label>
                    </div>
                    
                    <div class="platform-details">
                        <label for="platform_<?php echo esc_attr($key); ?>_servers">
                            <?php _e('Server Endpoints (one per line):', 'cloud-loadout-latency-tester'); ?>
                        </label>
                        <textarea name="platform_<?php echo esc_attr($key); ?>_servers" rows="3" class="large-text"><?php 
                            echo esc_textarea($platform['servers'] ?? cloudloadout_get_default_servers($key)); 
                        ?></textarea>
                        <p class="description">
                            <?php _e('Test server URLs for this platform. Leave blank to use default servers.', 'cloud-loadout-latency-tester'); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="platform-help">
                    <h4><?php _e('Popular Cloud Gaming Platforms', 'cloud-loadout-latency-tester'); ?></h4>
                    <ul>
                        <li><strong>Xbox Cloud Gaming:</strong> test.xbox.com, xbox.com</li>
                        <li><strong>Amazon Luna:</strong> luna.amazon.com, www.amazon.com/luna</li>
                        <li><strong>Shadow:</strong> shadow.tech, www.shadow.tech</li>
                        <li><strong>Boosteroid:</strong> boosteroid.com</li>
                        <li><strong>PlayStation Cloud:</strong> playstation.com, store.playstation.com</li>
                        <li><strong>NVIDIA GeForce NOW:</strong> nvidianow.nvidia.com</li>
                        <li><strong>Microsoft Cloud PC:</strong> windows.microsoft.com</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="cloudloadout-sidebar">
            <!-- Quick Actions -->
            <div class="cloudloadout-card">
                <h3><?php _e('Quick Actions', 'cloud-loadout-latency-tester'); ?></h3>
                <div class="sidebar-actions">
                    <button type="button" id="test-all-platforms" class="button button-primary">
                        <?php _e('Test All Platforms', 'cloud-loadout-latency-tester'); ?>
                    </button>
                    <button type="button" id="export-settings" class="button">
                        <?php _e('Export Settings', 'cloud-loadout-latency-tester'); ?>
                    </button>
                    <button type="button" id="reset-settings" class="button button-secondary">
                        <?php _e('Reset to Defaults', 'cloud-loadout-latency-tester'); ?>
                    </button>
                </div>
            </div>
            
            <!-- Usage Information -->
            <div class="cloudloadout-card">
                <h3><?php _e('Usage Information', 'cloud-loadout-latency-tester'); ?></h3>
                <div class="usage-info">
                    <div class="info-section">
                        <h4><?php _e('Shortcode Usage', 'cloud-loadout-latency-tester'); ?></h4>
                        <code>[cloudloadout_latency_test]</code>
                        <p><?php _e('Add the latency tester to any page or post', 'cloud-loadout-latency-tester'); ?></p>
                    </div>
                    
                    <div class="info-section">
                        <h4><?php _e('Latency Guidelines', 'cloud-loadout-latency-tester'); ?></h4>
                        <ul>
                            <li><span class="latency-excellent">0-50ms</span> <?php _e('Excellent for cloud gaming', 'cloud-loadout-latency-tester'); ?></li>
                            <li><span class="latency-good">51-100ms</span> <?php _e('Good performance', 'cloud-loadout-latency-tester'); ?></li>
                            <li><span class="latency-fair">101-150ms</span> <?php _e('Fair, may notice some delay', 'cloud-loadout-latency-tester'); ?></li>
                            <li><span class="latency-poor">150ms+</span> <?php _e('Poor, gaming may be difficult', 'cloud-loadout-latency-tester'); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Troubleshooting -->
            <div class="cloudloadout-card">
                <h3><?php _e('Troubleshooting', 'cloud-loadout-latency-tester'); ?></h3>
                <div class="troubleshooting">
                    <div class="trouble-item">
                        <strong><?php _e('High Latency?', 'cloud-loadout-latency-tester'); ?></strong>
                        <p><?php _e('Check your internet connection and try wired ethernet instead of WiFi.', 'cloud-loadout-latency-tester'); ?></p>
                    </div>
                    <div class="trouble-item">
                        <strong><?php _e('Tests Failing?', 'cloud-loadout-latency-tester'); ?></strong>
                        <p><?php _e('Verify server endpoints are accessible and firewall settings allow HTTP requests.', 'cloud-loadout-latency-tester'); ?></p>
                    </div>
                    <div class="trouble-item">
                        <strong><?php _e('Inconsistent Results?', 'cloud-loadout-latency-tester'); ?></strong>
                        <p><?php _e('Run multiple tests and check for background applications using bandwidth.', 'cloud-loadout-latency-tester'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cloudloadout-settings-container {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    margin-top: 20px;
}

.cloudloadout-settings-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.platform-settings-item {
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 15px;
    padding: 15px;
    background: #f9f9f9;
}

.dark-theme .platform-settings-item {
    background: #34495e;
    border-color: #4a6741;
}

.platform-header {
    margin-bottom: 10px;
}

.platform-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.platform-name {
    font-weight: 600;
    font-size: 1.1em;
}

.platform-details {
    margin-left: 30px;
}

.platform-help {
    margin-top: 20px;
    padding: 15px;
    background: #f0f8ff;
    border-radius: 6px;
    border-left: 4px solid #667eea;
}

.dark-theme .platform-help {
    background: #2c3e50;
    border-left-color: #667eea;
}

.platform-help ul {
    margin: 10px 0;
    padding-left: 20px;
}

.platform-help li {
    margin-bottom: 5px;
}

.submit-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}

.sidebar-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.usage-info .info-section {
    margin-bottom: 20px;
}

.usage-info h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.dark-theme .usage-info h4 {
    color: #ecf0f1;
}

.usage-info code {
    display: block;
    padding: 10px;
    background: #f1f1f1;
    border-radius: 4px;
    margin: 10px 0;
}

.dark-theme .usage-info code {
    background: #34495e;
    color: #ecf0f1;
}

.latency-excellent { color: #27ae60; font-weight: bold; }
.latency-good { color: #f39c12; font-weight: bold; }
.latency-fair { color: #e67e22; font-weight: bold; }
.latency-poor { color: #e74c3c; font-weight: bold; }

.troubleshooting .trouble-item {
    margin-bottom: 15px;
    padding: 10px;
    background: #fff8dc;
    border-radius: 4px;
    border-left: 3px solid #f39c12;
}

.dark-theme .troubleshooting .trouble-item {
    background: #34495e;
    border-left-color: #f39c12;
}

@media (max-width: 768px) {
    .cloudloadout-settings-container {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Test all platforms
    $('#test-all-platforms').on('click', function() {
        if (confirm('<?php _e('Run latency tests for all enabled platforms? This may take a few minutes.', 'cloud-loadout-latency-tester'); ?>')) {
            // Implementation for testing all platforms
            alert('<?php _e('Starting comprehensive latency test...', 'cloud-loadout-latency-tester'); ?>');
        }
    });
    
    // Export settings
    $('#export-settings').on('click', function() {
        var settings = <?php echo json_encode($options); ?>;
        var dataStr = JSON.stringify(settings, null, 2);
        var dataBlob = new Blob([dataStr], {type: 'application/json'});
        var url = URL.createObjectURL(dataBlob);
        var link = document.createElement('a');
        link.href = url;
        link.download = 'cloudloadout-latency-settings.json';
        link.click();
    });
    
    // Reset settings
    $('#reset-settings').on('click', function() {
        if (confirm('<?php _e('Are you sure you want to reset all settings to defaults? This cannot be undone.', 'cloud-loadout-latency-tester'); ?>')) {
            window.location.href = '<?php echo add_query_arg('reset_settings', '1'); ?>';
        }
    });
});
</script>