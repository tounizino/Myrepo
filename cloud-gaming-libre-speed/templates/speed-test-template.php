<?php
/**
 * Frontend speed test template.
 *
 * @package CloudGamingSpeedTest
 */

if (!defined('ABSPATH')) {
    exit;
}

$articles = CGST_Database::get_articles();
?>

<div class="cgst-wrapper">
    <div class="cgst-container">
        <div class="cgst-header">
            <h2 class="cgst-title">
                <span class="cgst-icon-gaming"></span>
                <?php esc_html_e('Cloud Gaming Speed Test', 'cloud-gaming-speed-test'); ?>
            </h2>
            <p class="cgst-subtitle">
                <?php esc_html_e('Test your internet speed and discover if your connection is optimized for cloud gaming.', 'cloud-gaming-speed-test'); ?>
            </p>
        </div>

        <div class="cgst-server-selector">
            <label class="cgst-label">
                <span class="cgst-icon-server"></span>
                <?php esc_html_e('Select Server:', 'cloud-gaming-speed-test'); ?>
            </label>
            <div class="cgst-selector-group">
                <button type="button" class="cgst-btn cgst-btn-auto active" data-mode="auto">
                    <span class="cgst-icon-auto"></span>
                    <?php esc_html_e('Auto (Fastest)', 'cloud-gaming-speed-test'); ?>
                </button>
                <button type="button" class="cgst-btn cgst-btn-manual" data-mode="manual">
                    <span class="cgst-icon-manual"></span>
                    <?php esc_html_e('Manual Selection', 'cloud-gaming-speed-test'); ?>
                </button>
            </div>

            <div class="cgst-server-dropdown" style="display: none;">
                <select id="cgst-server-select" class="cgst-select">
                    <option value=""><?php esc_html_e('Select a server...', 'cloud-gaming-speed-test'); ?></option>
                </select>
            </div>
        </div>

        <div class="cgst-test-section">
            <button type="button" id="cgst-start-test" class="cgst-btn-start">
                <span class="cgst-icon-play"></span>
                <span class="cgst-btn-text"><?php esc_html_e('Start Test', 'cloud-gaming-speed-test'); ?></span>
            </button>

            <div id="cgst-progress-container" class="cgst-progress-container" style="display: none;">
                <div class="cgst-stage">
                    <div class="cgst-stage-title">
                        <span class="cgst-stage-icon cgst-icon-ping"></span>
                        <span class="cgst-stage-label"><?php esc_html_e('Testing Latency', 'cloud-gaming-speed-test'); ?></span>
                    </div>
                    <div class="cgst-progress-bar">
                        <div class="cgst-progress-fill" id="cgst-progress-ping"></div>
                    </div>
                </div>

                <div class="cgst-stage">
                    <div class="cgst-stage-title">
                        <span class="cgst-stage-icon cgst-icon-download"></span>
                        <span class="cgst-stage-label"><?php esc_html_e('Testing Download', 'cloud-gaming-speed-test'); ?></span>
                    </div>
                    <div class="cgst-progress-bar">
                        <div class="cgst-progress-fill" id="cgst-progress-download"></div>
                    </div>
                </div>

                <div class="cgst-stage">
                    <div class="cgst-stage-title">
                        <span class="cgst-stage-icon cgst-icon-upload"></span>
                        <span class="cgst-stage-label"><?php esc_html_e('Testing Upload', 'cloud-gaming-speed-test'); ?></span>
                    </div>
                    <div class="cgst-progress-bar">
                        <div class="cgst-progress-fill" id="cgst-progress-upload"></div>
                    </div>
                </div>

                <div class="cgst-status-message" id="cgst-status-message">
                    <?php esc_html_e('Initializing test...', 'cloud-gaming-speed-test'); ?>
                </div>
            </div>

            <div id="cgst-results-container" class="cgst-results-container" style="display: none;">
                <div class="cgst-tested-server" id="cgst-tested-server" style="display: none;">
                    <span class="cgst-tested-label"><?php esc_html_e('Tested against:', 'cloud-gaming-speed-test'); ?></span>
                    <span class="cgst-tested-value" id="cgst-tested-server-name"></span>
                </div>

                <div class="cgst-results-grid">
                    <div class="cgst-result-card cgst-card-download">
                        <div class="cgst-result-icon cgst-icon-download-result"></div>
                        <div class="cgst-result-value" id="cgst-result-download">--</div>
                        <div class="cgst-result-label"><?php esc_html_e('Download', 'cloud-gaming-speed-test'); ?></div>
                        <div class="cgst-result-unit"><?php esc_html_e('Mbps', 'cloud-gaming-speed-test'); ?></div>
                    </div>

                    <div class="cgst-result-card cgst-card-upload">
                        <div class="cgst-result-icon cgst-icon-upload-result"></div>
                        <div class="cgst-result-value" id="cgst-result-upload">--</div>
                        <div class="cgst-result-label"><?php esc_html_e('Upload', 'cloud-gaming-speed-test'); ?></div>
                        <div class="cgst-result-unit"><?php esc_html_e('Mbps', 'cloud-gaming-speed-test'); ?></div>
                    </div>

                    <div class="cgst-result-card cgst-card-ping">
                        <div class="cgst-result-icon cgst-icon-ping-result"></div>
                        <div class="cgst-result-value" id="cgst-result-ping">--</div>
                        <div class="cgst-result-label"><?php esc_html_e('Ping', 'cloud-gaming-speed-test'); ?></div>
                        <div class="cgst-result-unit"><?php esc_html_e('ms', 'cloud-gaming-speed-test'); ?></div>
                    </div>

                    <div class="cgst-result-card cgst-card-jitter">
                        <div class="cgst-result-icon cgst-icon-jitter-result"></div>
                        <div class="cgst-result-value" id="cgst-result-jitter">--</div>
                        <div class="cgst-result-label"><?php esc_html_e('Jitter', 'cloud-gaming-speed-test'); ?></div>
                        <div class="cgst-result-unit"><?php esc_html_e('ms', 'cloud-gaming-speed-test'); ?></div>
                    </div>

                    <div class="cgst-result-card cgst-card-packet">
                        <div class="cgst-result-icon cgst-icon-packet-result"></div>
                        <div class="cgst-result-value" id="cgst-result-packet">--</div>
                        <div class="cgst-result-label"><?php esc_html_e('Packet Loss', 'cloud-gaming-speed-test'); ?></div>
                        <div class="cgst-result-unit"><?php esc_html_e('%', 'cloud-gaming-speed-test'); ?></div>
                    </div>
                </div>

                <div class="cgst-rating-section">
                    <div class="cgst-rating-badge" id="cgst-rating-badge">
                        <div class="cgst-rating-icon" id="cgst-rating-icon"></div>
                        <div class="cgst-rating-text" id="cgst-rating-text">--</div>
                    </div>
                    <div class="cgst-recommendation" id="cgst-recommendation">
                        <?php esc_html_e('Run a test to get your cloud gaming assessment...', 'cloud-gaming-speed-test'); ?>
                    </div>
                </div>

                <div class="cgst-tips-wrapper" id="cgst-tips-wrapper" style="display: none;">
                    <h4 class="cgst-tips-title">
                        <span class="cgst-icon-tips"></span>
                        <?php esc_html_e('Pro Tips to Level Up', 'cloud-gaming-speed-test'); ?>
                    </h4>
                    <ul class="cgst-tips-list" id="cgst-tips-list"></ul>
                </div>

                <div class="cgst-actions">
                    <button type="button" id="cgst-retest" class="cgst-btn cgst-btn-secondary">
                        <span class="cgst-icon-refresh"></span>
                        <?php esc_html_e('Run Another Test', 'cloud-gaming-speed-test'); ?>
                    </button>
                </div>
            </div>
        </div>

        <?php if (!empty($articles)) : ?>
        <div class="cgst-resources-section">
            <h3 class="cgst-resources-title">
                <span class="cgst-icon-resources"></span>
                <?php esc_html_e('Optimization Resources', 'cloud-gaming-speed-test'); ?>
            </h3>
            <div class="cgst-resources-grid">
                <?php foreach ($articles as $article) : ?>
                <div class="cgst-resource-card">
                    <div class="cgst-resource-category"><?php echo esc_html($article['category']); ?></div>
                    <h4 class="cgst-resource-title">
                        <a href="<?php echo esc_url($article['url']); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($article['title']); ?>
                        </a>
                    </h4>
                    <p class="cgst-resource-description"><?php echo esc_html($article['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
