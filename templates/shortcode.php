<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('cloudloadout_latency_options', array());
$platform = $atts['platform'] ?? 'all';
$theme = $atts['theme'] ?? 'light';
?>

<div class="cloudloadout-tester-container <?php echo esc_attr($theme); ?>-theme">
    <!-- Header -->
    <div class="cloudloadout-header">
        <h2><?php _e('Cloud Loadout Latency Tester', 'cloud-loadout-latency-tester'); ?></h2>
        <p><?php _e('Test your connection to popular cloud gaming platforms', 'cloud-loadout-latency-tester'); ?></p>
    </div>
    
    <!-- Test Form -->
    <form class="cloudloadout-test-form" method="post">
        <!-- Platform Selection -->
        <div class="cloudloadout-form-group">
            <label for="platform-selector"><?php _e('Select Gaming Platform:', 'cloud-loadout-latency-tester'); ?></label>
            <select id="platform-selector" class="cloudloadout-platform-selector">
                <option value=""><?php _e('Choose a platform...', 'cloud-loadout-latency-tester'); ?></option>
                <?php if ($platform === 'all'): ?>
                    <?php foreach ($options['supported_platforms'] as $key => $platform_info): ?>
                        <?php if ($platform_info['enabled']): ?>
                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($platform_info['name']); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php 
                    $single_platform = $options['supported_platforms'][$platform] ?? null;
                    if ($single_platform && $single_platform['enabled']): ?>
                        <option value="<?php echo esc_attr($platform); ?>"><?php echo esc_html($single_platform['name']); ?></option>
                    <?php endif; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <!-- Server Selection (will be populated dynamically) -->
        <div class="cloudloadout-form-group" id="server-selection-group" style="display: none;">
            <label><?php _e('Select Server:', 'cloud-loadout-latency-tester'); ?></label>
            <div class="cloudloadout-servers-list"></div>
        </div>
        
        <!-- Test Type Selection -->
        <div class="cloudloadout-form-group">
            <label for="test-type"><?php _e('Test Type:', 'cloud-loadout-latency-tester'); ?></label>
            <select id="test-type" class="cloudloadout-test-type">
                <option value="single"><?php _e('Single Platform Test', 'cloud-loadout-latency-tester'); ?></option>
                <option value="comprehensive"><?php _e('Comprehensive Test (All Enabled Platforms)', 'cloud-loadout-latency-tester'); ?></option>
            </select>
        </div>
        
        <!-- Advanced Settings Toggle -->
        <div class="cloudloadout-form-group">
            <a href="#" class="cloudloadout-advanced-toggle"><?php _e('Advanced Settings', 'cloud-loadout-latency-tester'); ?></a>
            <div class="cloudloadout-advanced-settings">
                <div class="cloudloadout-form-group">
                    <label for="test-count"><?php _e('Number of Tests:', 'cloud-loadout-latency-tester'); ?></label>
                    <input type="number" id="test-count" class="cloudloadout-test-count" value="<?php echo esc_attr($options['test_count'] ?? 5); ?>" min="1" max="20" />
                    <small><?php _e('Higher numbers give more accurate averages but take longer', 'cloud-loadout-latency-tester'); ?></small>
                </div>
                
                <div class="cloudloadout-form-group">
                    <label for="test-timeout"><?php _e('Test Timeout (ms):', 'cloud-loadout-latency-tester'); ?></label>
                    <input type="number" id="test-timeout" class="cloudloadout-test-timeout" value="<?php echo esc_attr($options['test_timeout'] ?? 5000); ?>" min="1000" max="30000" step="1000" />
                    <small><?php _e('How long to wait for each test response', 'cloud-loadout-latency-tester'); ?></small>
                </div>
            </div>
        </div>
        
        <!-- Start Test Button -->
        <div class="cloudloadout-form-group">
            <button type="submit" class="cloudloadout-start-test">
                <?php _e('Start Latency Test', 'cloud-loadout-latency-tester'); ?>
            </button>
        </div>
        
        <!-- Test Status -->
        <div class="cloudloadout-status cloudloadout-test-status"></div>
    </form>
    
    <!-- Results Container -->
    <div class="cloudloadout-results-container"></div>
    
    <!-- Actions -->
    <div class="cloudloadout-actions" style="display: none;">
        <button type="button" class="cloudloadout-retry-test"><?php _e('Run Test Again', 'cloud-loadout-latency-tester'); ?></button>
        <button type="button" class="cloudloadout-export-results"><?php _e('Export Results', 'cloud-loadout-latency-tester'); ?></button>
        <button type="button" class="cloudloadout-clear-results"><?php _e('Clear Results', 'cloud-loadout-latency-tester'); ?></button>
        <button type="button" class="cloudloadout-show-help"><?php _e('Help & Guidelines', 'cloud-loadout-latency-tester'); ?></button>
    </div>
    
    <!-- Information Panel -->
    <div class="cloudloadout-info-panel">
        <h3><?php _e('About Cloud Gaming Latency', 'cloud-loadout-latency-tester'); ?></h3>
        <div class="info-grid">
            <div class="info-item">
                <h4><?php _e('What is Latency?', 'cloud-loadout-latency-tester'); ?></h4>
                <p><?php _e('Latency (ping) measures the time it takes for data to travel from your device to the gaming server and back. Lower latency means more responsive gameplay.', 'cloud-loadout-latency-tester'); ?></p>
            </div>
            
            <div class="info-item">
                <h4><?php _e('Latency Guidelines', 'cloud-loadout-latency-tester'); ?></h4>
                <div class="latency-guide">
                    <div class="guide-item excellent">
                        <span class="latency-range">0-50ms</span>
                        <span class="quality"><?php _e('Excellent', 'cloud-loadout-latency-tester'); ?></span>
                        <span class="description"><?php _e('Perfect for all cloud gaming', 'cloud-loadout-latency-tester'); ?></span>
                    </div>
                    <div class="guide-item good">
                        <span class="latency-range">51-100ms</span>
                        <span class="quality"><?php _e('Good', 'cloud-loadout-latency-tester'); ?></span>
                        <span class="description"><?php _e('Great for most games', 'cloud-loadout-latency-tester'); ?></span>
                    </div>
                    <div class="guide-item fair">
                        <span class="latency-range">101-150ms</span>
                        <span class="quality"><?php _e('Fair', 'cloud-loadout-latency-tester'); ?></span>
                        <span class="description"><?php _e('Some input lag expected', 'cloud-loadout-latency-tester'); ?></span>
                    </div>
                    <div class="guide-item poor">
                        <span class="latency-range">150ms+</span>
                        <span class="quality"><?php _e('Poor', 'cloud-loadout-latency-tester'); ?></span>
                        <span class="description"><?php _e('Gaming may be difficult', 'cloud-loadout-latency-tester'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="info-item">
                <h4><?php _e('Tips for Better Performance', 'cloud-loadout-latency-tester'); ?></h4>
                <ul>
                    <li><?php _e('Use a wired Ethernet connection when possible', 'cloud-loadout-latency-tester'); ?></li>
                    <li><?php _e('Close bandwidth-heavy applications', 'cloud-loadout-latency-tester'); ?></li>
                    <li><?php _e('Test at different times of day', 'cloud-loadout-latency-tester'); ?></li>
                    <li><?php _e('Choose the server location closest to you', 'cloud-loadout-latency-tester'); ?></li>
                    <li><?php _e('Consider upgrading your internet plan', 'cloud-loadout-latency-tester'); ?></li>
                </ul>
            </div>
            
            <div class="info-item">
                <h4><?php _e('Popular Cloud Gaming Platforms', 'cloud-loadout-latency-tester'); ?></h4>
                <div class="platforms-list">
                    <?php foreach ($options['supported_platforms'] as $key => $platform_info): ?>
                        <?php if ($platform_info['enabled']): ?>
                            <div class="platform-item">
                                <span class="platform-name"><?php echo esc_html($platform_info['name']); ?></span>
                                <?php
                                // Add platform-specific information
                                $platform_descriptions = array(
                                    'xbox_cloud' => __('Game Pass Ultimate subscription required', 'cloud-loadout-latency-tester'),
                                    'amazon_luna' => __('Separate Luna channels subscription', 'cloud-loadout-latency-tester'),
                                    'shadow' => __('High-end cloud PC experience', 'cloud-loadout-latency-tester'),
                                    'boosteroid' => __('Growing cloud gaming service', 'cloud-loadout-latency-tester'),
                                    'playstation_cloud' => __('PlayStation Plus Premium required', 'cloud-loadout-latency-tester'),
                                    'nvidia_geforce' => __('Free tier available, paid for premium', 'cloud-loadout-latency-tester'),
                                    'microsoft_cloud' => __('Windows 365 subscription service', 'cloud-loadout-latency-tester')
                                );
                                $description = $platform_descriptions[$key] ?? '';
                                if ($description): ?>
                                    <span class="platform-note"><?php echo esc_html($description); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notifications Container -->
<div class="cloudloadout-notifications"></div>