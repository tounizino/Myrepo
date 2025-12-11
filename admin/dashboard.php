<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
$options = get_option('cloudloadout_latency_options', array());
$theme = isset($options['admin_theme']) ? $options['admin_theme'] : 'light';
?>
<div class="wrap cloudloadout-admin dashboard <?php echo esc_attr($theme); ?>-theme">
    <div class="cloudloadout-header">
        <h1><?php _e('Cloud Loadout Latency Tester', 'cloud-loadout-latency-tester'); ?></h1>
        <p class="subtitle"><?php _e('Monitor and manage latency tests for cloud gaming platforms', 'cloud-loadout-latency-tester'); ?></p>
    </div>
    
    <div class="cloudloadout-dashboard-grid">
        <!-- Quick Stats -->
        <div class="cloudloadout-card">
            <h3><?php _e('Quick Statistics', 'cloud-loadout-latency-tester'); ?></h3>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'cloudloadout_latency_tests';
            
            $total_tests = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $avg_latency = $wpdb->get_var("SELECT AVG(average_latency) FROM $table_name WHERE average_latency > 0");
            $tests_today = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE DATE(test_date) = %s",
                current_time('Y-m-d')
            ));
            ?>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number"><?php echo intval($total_tests); ?></span>
                    <span class="stat-label"><?php _e('Total Tests', 'cloud-loadout-latency-tester'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo $avg_latency ? round($avg_latency, 1) . 'ms' : 'N/A'; ?></span>
                    <span class="stat-label"><?php _e('Average Latency', 'cloud-loadout-latency-tester'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo intval($tests_today); ?></span>
                    <span class="stat-label"><?php _e('Tests Today', 'cloud-loadout-latency-tester'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Platform Status -->
        <div class="cloudloadout-card">
            <h3><?php _e('Platform Status', 'cloud-loadout-latency-tester'); ?></h3>
            <div class="platform-list">
                <?php foreach ($options['supported_platforms'] as $key => $platform): ?>
                <div class="platform-item <?php echo $platform['enabled'] ? 'enabled' : 'disabled'; ?>">
                    <div class="platform-info">
                        <span class="platform-name"><?php echo esc_html($platform['name']); ?></span>
                        <span class="platform-status">
                            <?php echo $platform['enabled'] ? __('Active', 'cloud-loadout-latency-tester') : __('Disabled', 'cloud-loadout-latency-tester'); ?>
                        </span>
                    </div>
                    <div class="platform-actions">
                        <a href="<?php echo admin_url('admin.php?page=cloudloadout-latency-tester-settings'); ?>" class="button button-small">
                            <?php _e('Configure', 'cloud-loadout-latency-tester'); ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Recent Tests -->
        <div class="cloudloadout-card">
            <h3><?php _e('Recent Test Results', 'cloud-loadout-latency-tester'); ?></h3>
            <div class="recent-tests">
                <?php
                $recent_tests = $wpdb->get_results(
                    "SELECT * FROM $table_name ORDER BY test_date DESC LIMIT 5",
                    ARRAY_A
                );
                
                if ($recent_tests): ?>
                    <div class="test-results-table">
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php _e('Date', 'cloud-loadout-latency-tester'); ?></th>
                                    <th><?php _e('Platform', 'cloud-loadout-latency-tester'); ?></th>
                                    <th><?php _e('Latency', 'cloud-loadout-latency-tester'); ?></th>
                                    <th><?php _e('Location', 'cloud-loadout-latency-tester'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_tests as $test): ?>
                                <tr>
                                    <td><?php echo date('M j, Y H:i', strtotime($test['test_date'])); ?></td>
                                    <td><?php echo esc_html($test['platform']); ?></td>
                                    <td><?php echo $test['average_latency'] ? round($test['average_latency'], 1) . 'ms' : 'N/A'; ?></td>
                                    <td><?php echo esc_html($test['server_location']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p><?php _e('No test results yet. Run some tests to see data here.', 'cloud-loadout-latency-tester'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="cloudloadout-card">
            <h3><?php _e('Quick Actions', 'cloud-loadout-latency-tester'); ?></h3>
            <div class="quick-actions">
                <a href="<?php echo admin_url('admin.php?page=cloudloadout-latency-tester-settings'); ?>" class="button button-primary">
                    <?php _e('Configure Settings', 'cloud-loadout-latency-tester'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=cloudloadout-latency-tester-results'); ?>" class="button">
                    <?php _e('View All Results', 'cloud-loadout-latency-tester'); ?>
                </a>
                <a href="#" id="run-sample-test" class="button button-secondary">
                    <?php _e('Run Sample Test', 'cloud-loadout-latency-tester'); ?>
                </a>
            </div>
        </div>
        
        <!-- System Information -->
        <div class="cloudloadout-card">
            <h3><?php _e('System Information', 'cloud-loadout-latency-tester'); ?></h3>
            <div class="system-info">
                <div class="info-item">
                    <strong><?php _e('Plugin Version:', 'cloud-loadout-latency-tester'); ?></strong>
                    <span><?php echo CLOUDLOADOUT_LATENCY_VERSION; ?></span>
                </div>
                <div class="info-item">
                    <strong><?php _e('WordPress Version:', 'cloud-loadout-latency-tester'); ?></strong>
                    <span><?php echo get_bloginfo('version'); ?></span>
                </div>
                <div class="info-item">
                    <strong><?php _e('PHP Version:', 'cloud-loadout-latency-tester'); ?></strong>
                    <span><?php echo phpversion(); ?></span>
                </div>
                <div class="info-item">
                    <strong><?php _e('Server IP:', 'cloud-loadout-latency-tester'); ?></strong>
                    <span><?php echo $_SERVER['SERVER_ADDR'] ?? 'Unknown'; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cloudloadout-admin {
    max-width: 1200px;
    margin: 20px 0;
}

.cloudloadout-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
}

.cloudloadout-header h1 {
    margin: 0 0 10px 0;
    font-size: 2.5em;
}

.cloudloadout-header .subtitle {
    margin: 0;
    font-size: 1.1em;
    opacity: 0.9;
}

.cloudloadout-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.cloudloadout-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.dark-theme .cloudloadout-card {
    background: #2c3e50;
    border-color: #34495e;
    color: #ecf0f1;
}

.cloudloadout-card h3 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}

.dark-theme .cloudloadout-card h3 {
    color: #ecf0f1;
    border-bottom-color: #667eea;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.dark-theme .stat-item {
    background: #34495e;
}

.stat-number {
    display: block;
    font-size: 1.8em;
    font-weight: bold;
    color: #667eea;
}

.stat-label {
    display: block;
    font-size: 0.9em;
    color: #666;
    margin-top: 5px;
}

.dark-theme .stat-label {
    color: #bdc3c7;
}

.platform-list {
    space-y: 10px;
}

.platform-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 8px;
}

.platform-item.enabled {
    border-left: 4px solid #27ae60;
}

.platform-item.disabled {
    border-left: 4px solid #e74c3c;
}

.platform-info {
    flex: 1;
}

.platform-name {
    display: block;
    font-weight: 600;
}

.platform-status {
    font-size: 0.85em;
    color: #666;
}

.platform-item.enabled .platform-status {
    color: #27ae60;
}

.platform-item.disabled .platform-status {
    color: #e74c3c;
}

.recent-tests table {
    margin-top: 15px;
}

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.system-info {
    space-y: 10px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.info-item:last-child {
    border-bottom: none;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Run sample test
    $('#run-sample-test').on('click', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var originalText = $button.text();
        $button.text('<?php _e('Running Test...', 'cloud-loadout-latency-tester'); ?>');
        
        $.post(cloudloadout_admin.ajax_url, {
            action: 'cloudloadout_run_test',
            nonce: cloudloadout_admin.nonce,
            platform: 'sample_test',
            server_url: 'https://www.google.com',
            test_count: 3,
            timeout: 5000
        }, function(response) {
            if (response.success) {
                alert('<?php _e('Sample test completed successfully!', 'cloud-loadout-latency-tester'); ?>');
                location.reload();
            } else {
                alert('<?php _e('Test failed. Please check your settings.', 'cloud-loadout-latency-tester'); ?>');
            }
        }).always(function() {
            $button.text(originalText);
        });
    });
});
</script>