<?php
/**
 * Speed Test Front-end Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$home_url = apply_filters('cgst_home_url', home_url('/'));
$component_id = uniqid('cgst-', false);
?>
<div class="cgst-component cgst-theme-<?php echo esc_attr($theme); ?>" id="<?php echo esc_attr($component_id); ?>" data-cgst-component data-theme="<?php echo esc_attr($theme); ?>">
    <div class="cgst-header">
        <a class="cgst-return-home" href="<?php echo esc_url($home_url); ?>">&larr; <?php esc_html_e('Return Home', 'cloud-gaming-speedtest'); ?></a>
        <div class="cgst-headline">
            <div class="cgst-title-group">
                <h2><?php esc_html_e('Cloud Gaming Speed Test', 'cloud-gaming-speedtest'); ?></h2>
                <p class="cgst-intro-text"><?php echo wp_kses_post($intro); ?></p>
            </div>
            <div class="cgst-info-strip">
                <span class="cgst-pill" data-cgst-status-pill><?php esc_html_e('Ready to Analyze', 'cloud-gaming-speedtest'); ?></span>
                <span class="cgst-pill cgst-pill-secondary" data-cgst-session-time><?php esc_html_e('Session: waiting to start', 'cloud-gaming-speedtest'); ?></span>
            </div>
        </div>
    </div>

    <div class="cgst-body">
        <div class="cgst-test-panel">
            <div class="cgst-gauge">
                <div class="cgst-gauge-ring">
                    <div class="cgst-gauge-fill" data-cgst-gauge-fill></div>
                    <div class="cgst-gauge-pointer" data-cgst-gauge-pointer></div>
                </div>
                <div class="cgst-gauge-readout">
                    <span class="cgst-gauge-value" data-cgst-speed-value>0</span>
                    <span class="cgst-gauge-unit">Mbps</span>
                    <span class="cgst-gauge-label" data-cgst-gauge-label><?php esc_html_e('Download', 'cloud-gaming-speedtest'); ?></span>
                </div>
            </div>

            <div class="cgst-progress">
                <div class="cgst-progress-bar" data-cgst-progress>
                    <span class="cgst-progress-fill" data-cgst-progress-fill></span>
                </div>
                <div class="cgst-progress-steps">
                    <span data-step="latency" class="is-active"><?php esc_html_e('Latency', 'cloud-gaming-speedtest'); ?></span>
                    <span data-step="download"><?php esc_html_e('Download', 'cloud-gaming-speedtest'); ?></span>
                    <span data-step="upload"><?php esc_html_e('Upload', 'cloud-gaming-speedtest'); ?></span>
                    <span data-step="analysis"><?php esc_html_e('Analysis', 'cloud-gaming-speedtest'); ?></span>
                </div>
            </div>

            <button type="button" class="cgst-start-button" data-cgst-action="start-test">
                <span class="cgst-start-glow"></span>
                <span class="cgst-start-text"><?php esc_html_e('Start Full Cloud Gaming Test', 'cloud-gaming-speedtest'); ?></span>
            </button>
        </div>

        <div class="cgst-metrics-panel">
            <div class="cgst-metric" data-metric="download">
                <span class="cgst-metric-label"><?php esc_html_e('Download', 'cloud-gaming-speedtest'); ?></span>
                <span class="cgst-metric-value" data-cgst-download>--</span>
                <span class="cgst-metric-unit">Mbps</span>
                <p class="cgst-metric-note"><?php esc_html_e('Sustained bandwidth for streaming frames', 'cloud-gaming-speedtest'); ?></p>
            </div>
            <div class="cgst-metric" data-metric="upload">
                <span class="cgst-metric-label"><?php esc_html_e('Upload', 'cloud-gaming-speedtest'); ?></span>
                <span class="cgst-metric-value" data-cgst-upload>--</span>
                <span class="cgst-metric-unit">Mbps</span>
                <p class="cgst-metric-note"><?php esc_html_e('Upstream capacity for inputs & multiplayer voice', 'cloud-gaming-speedtest'); ?></p>
            </div>
            <div class="cgst-metric" data-metric="latency">
                <span class="cgst-metric-label"><?php esc_html_e('Latency', 'cloud-gaming-speedtest'); ?></span>
                <span class="cgst-metric-value" data-cgst-latency>--</span>
                <span class="cgst-metric-unit">ms</span>
                <p class="cgst-metric-note"><?php esc_html_e('Time it takes for your inputs to reach the cloud', 'cloud-gaming-speedtest'); ?></p>
            </div>
            <div class="cgst-metric" data-metric="jitter">
                <span class="cgst-metric-label"><?php esc_html_e('Jitter', 'cloud-gaming-speedtest'); ?></span>
                <span class="cgst-metric-value" data-cgst-jitter>--</span>
                <span class="cgst-metric-unit">ms</span>
                <p class="cgst-metric-note"><?php esc_html_e('Variation in latency that causes stutter', 'cloud-gaming-speedtest'); ?></p>
            </div>
        </div>
    </div>

    <div class="cgst-results" data-cgst-results hidden>
        <div class="cgst-status-banner" data-cgst-status-banner>
            <div class="cgst-status-icon" data-cgst-status-icon>🎮</div>
            <div>
                <h3 data-cgst-status-headline><?php esc_html_e('Run the test to get your personalized breakdown.', 'cloud-gaming-speedtest'); ?></h3>
                <p data-cgst-status-description><?php esc_html_e('We will compare your performance against top-tier cloud gaming tiers and share upgrade tips.', 'cloud-gaming-speedtest'); ?></p>
            </div>
        </div>

        <div class="cgst-results-grid">
            <div class="cgst-results-card">
                <h4><?php esc_html_e('Top Recommendations', 'cloud-gaming-speedtest'); ?></h4>
                <ul class="cgst-recommendations" data-cgst-recommendations></ul>
            </div>
            <div class="cgst-results-card">
                <h4><?php esc_html_e('Quality Profiles', 'cloud-gaming-speedtest'); ?></h4>
                <div class="cgst-quality-list">
                    <?php foreach ($quality_levels as $level) : ?>
                        <div class="cgst-quality-item">
                            <div class="cgst-quality-label"><?php echo esc_html($level['quality']); ?></div>
                            <div class="cgst-quality-meta">
                                <span><?php printf(__('Down: %s', 'cloud-gaming-speedtest'), esc_html($level['download'])); ?></span>
                                <span><?php printf(__('Up: %s', 'cloud-gaming-speedtest'), esc_html($level['upload'])); ?></span>
                                <span><?php printf(__('Latency: %s', 'cloud-gaming-speedtest'), esc_html($level['latency'])); ?></span>
                            </div>
                            <p class="cgst-quality-note"><?php echo esc_html($level['notes']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="cgst-resources">
            <h4><?php esc_html_e('Helpful Resources', 'cloud-gaming-speedtest'); ?></h4>
            <ul>
                <?php foreach ($resources as $resource) :
                    if (empty($resource['title']) || empty($resource['url'])) {
                        continue;
                    }
                    ?>
                    <li>
                        <a href="<?php echo esc_url($resource['url']); ?>" target="_blank" rel="noopener noreferrer">
                            <span class="cgst-resource-text"><?php echo esc_html($resource['title']); ?></span>
                            <span class="cgst-resource-arrow">&rarr;</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if (!empty($footer)) : ?>
                <p class="cgst-footer-note"><?php echo wp_kses_post($footer); ?></p>
            <?php endif; ?>
        </div>

        <div class="cgst-network-summary">
            <h4><?php esc_html_e('Connection Summary', 'cloud-gaming-speedtest'); ?></h4>
            <div class="cgst-summary-grid">
                <div>
                    <span class="cgst-summary-label"><?php esc_html_e('IP Address', 'cloud-gaming-speedtest'); ?></span>
                    <span class="cgst-summary-value" data-cgst-ip>--</span>
                </div>
                <div>
                    <span class="cgst-summary-label"><?php esc_html_e('Location', 'cloud-gaming-speedtest'); ?></span>
                    <span class="cgst-summary-value" data-cgst-location>--</span>
                </div>
                <div>
                    <span class="cgst-summary-label"><?php esc_html_e('ISP', 'cloud-gaming-speedtest'); ?></span>
                    <span class="cgst-summary-value" data-cgst-isp>--</span>
                </div>
            </div>
        </div>
    </div>
</div>
