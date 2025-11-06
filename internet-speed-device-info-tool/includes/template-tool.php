<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="isdit-wrapper theme-<?php echo esc_attr($theme); ?>">
    <div class="isdit-header">
        <h2><?php _e('Internet Speed & Device Information', 'isdit'); ?></h2>
    </div>
    
    <div class="isdit-content" data-show-info="<?php echo $full_version ? '1' : '0'; ?>">
        <?php if ($enable_speed): ?>
        <div class="isdit-speed-test">
            <div class="isdit-speed-results">
                <div class="speed-result">
                    <span class="label"><?php _e('Download', 'isdit'); ?></span>
                    <span class="value" data-download="--">--</span>
                    <span class="unit">Mbps</span>
                </div>
                <div class="speed-result">
                    <span class="label"><?php _e('Upload', 'isdit'); ?></span>
                    <span class="value" data-upload="--">--</span>
                    <span class="unit">Mbps</span>
                </div>
                <div class="speed-result">
                    <span class="label"><?php _e('Latency', 'isdit'); ?></span>
                    <span class="value" data-latency="--">--</span>
                    <span class="unit">ms</span>
                </div>
                <div class="speed-result">
                    <span class="label"><?php _e('Jitter', 'isdit'); ?></span>
                    <span class="value" data-jitter="--">--</span>
                    <span class="unit">ms</span>
                </div>
            </div>
            <button class="isdit-button" data-speed-test-start><?php _e('Start Speed Test', 'isdit'); ?></button>
            <div class="isdit-progress">
                <div class="progress-bar" data-progress-bar></div>
            </div>
            <div class="isdit-status"></div>
        </div>
        <?php endif; ?>
        
        <?php if ($full_version && ($enable_ip || $enable_device || $enable_map)): ?>
        <div class="isdit-info-grid">
            <?php if ($enable_ip): ?>
            <div class="isdit-card ip-info">
                <h3><?php _e('IP Lookup', 'isdit'); ?></h3>
                <div class="card-content">
                    <ul>
                        <li><strong><?php _e('IP Address', 'isdit'); ?>:</strong> <span data-ip-address>--</span></li>
                        <li><strong><?php _e('Country', 'isdit'); ?>:</strong> <span data-country>--</span></li>
                        <li><strong><?php _e('Region', 'isdit'); ?>:</strong> <span data-region>--</span></li>
                        <li><strong><?php _e('City', 'isdit'); ?>:</strong> <span data-city>--</span></li>
                        <li><strong><?php _e('ISP', 'isdit'); ?>:</strong> <span data-isp>--</span></li>
                        <li><strong><?php _e('Organization', 'isdit'); ?>:</strong> <span data-organization>--</span></li>
                        <li><strong><?php _e('Timezone', 'isdit'); ?>:</strong> <span data-timezone>--</span></li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($enable_device): ?>
            <div class="isdit-card device-info">
                <h3><?php _e('Device Information', 'isdit'); ?></h3>
                <div class="card-content">
                    <ul>
                        <li><strong><?php _e('Device Type', 'isdit'); ?>:</strong> <span data-device-type>--</span></li>
                        <li><strong><?php _e('Operating System', 'isdit'); ?>:</strong> <span data-os>--</span></li>
                        <li><strong><?php _e('Browser', 'isdit'); ?>:</strong> <span data-browser>--</span></li>
                        <li><strong><?php _e('Screen Resolution', 'isdit'); ?>:</strong> <span data-screen>--</span></li>
                        <li><strong><?php _e('Viewport Size', 'isdit'); ?>:</strong> <span data-viewport>--</span></li>
                        <li><strong><?php _e('CPU Threads', 'isdit'); ?>:</strong> <span data-cpu>--</span></li>
                        <li><strong><?php _e('Memory', 'isdit'); ?>:</strong> <span data-memory>--</span></li>
                        <li><strong><?php _e('Connection Type', 'isdit'); ?>:</strong> <span data-connection>--</span></li>
                        <li><strong><?php _e('User Agent', 'isdit'); ?>:</strong> <span data-useragent>--</span></li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($enable_map): ?>
            <div class="isdit-card location-map">
                <h3><?php _e('Location Map', 'isdit'); ?></h3>
                <div class="card-content">
                    <div class="isdit-map" data-map id="isdit-map-<?php echo uniqid(); ?>"></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
