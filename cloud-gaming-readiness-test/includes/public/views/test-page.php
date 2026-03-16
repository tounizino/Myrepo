<?php
/**
 * Cloud Gaming Test page template.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get settings.
$mode = isset( $atts['mode'] ) ? $atts['mode'] : get_option( 'cgrt_default_mode', 'dark' );
$theme_color = isset( $atts['theme'] ) ? $atts['theme'] : get_option( 'cgrt_theme_color', '#6366f1' );
?>

<div id="cgrt-app" class="cgrt-container cgrt-mode-<?php echo esc_attr( $mode ); ?>" data-theme-color="<?php echo esc_attr( $theme_color ); ?>">
    <!-- Intro Screen -->
    <div class="cgrt-intro-screen" id="cgrt-intro">
        <div class="cgrt-intro-content">
            <div class="cgrt-icon-wrapper">
                <svg class="cgrt-main-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <h1 class="cgrt-title"><?php esc_html_e( 'Cloud Gaming Readiness Test', 'cloud-gaming-readiness-test' ); ?></h1>
            <p class="cgrt-subtitle"><?php esc_html_e( 'Check if your connection is ready for cloud gaming', 'cloud-gaming-readiness-test' ); ?></p>
            <button class="cgrt-start-button" id="cgrt-start-btn">
                <span><?php esc_html_e( 'Start Test', 'cloud-gaming-readiness-test' ); ?></span>
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </button>
            <div class="cgrt-info-badges">
                <span class="cgrt-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                    </svg>
                    <?php esc_html_e( 'Fast', 'cloud-gaming-readiness-test' ); ?>
                </span>
                <span class="cgrt-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <?php esc_html_e( 'Secure', 'cloud-gaming-readiness-test' ); ?>
                </span>
                <span class="cgrt-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                    <?php esc_html_e( 'Real-time', 'cloud-gaming-readiness-test' ); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Testing Screen -->
    <div class="cgrt-test-screen" id="cgrt-test" style="display: none;">
        <div class="cgrt-test-progress">
            <div class="cgrt-progress-bar">
                <div class="cgrt-progress-fill" id="cgrt-progress-fill"></div>
            </div>
            <div class="cgrt-progress-text" id="cgrt-progress-text">
                <?php esc_html_e( 'Preparing...', 'cloud-gaming-readiness-test' ); ?>
            </div>
        </div>

        <div class="cgrt-test-metrics">
            <div class="cgrt-metric-card" id="cgrt-latency-card">
                <div class="cgrt-metric-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                </div>
                <div class="cgrt-metric-value" id="cgrt-latency-value">--</div>
                <div class="cgrt-metric-label"><?php esc_html_e( 'Latency', 'cloud-gaming-readiness-test' ); ?></div>
                <div class="cgrt-metric-unit">ms</div>
            </div>

            <div class="cgrt-metric-card" id="cgrt-jitter-card">
                <div class="cgrt-metric-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="cgrt-metric-value" id="cgrt-jitter-value">--</div>
                <div class="cgrt-metric-label"><?php esc_html_e( 'Jitter', 'cloud-gaming-readiness-test' ); ?></div>
                <div class="cgrt-metric-unit">ms</div>
            </div>

            <div class="cgrt-metric-card" id="cgrt-packet-loss-card">
                <div class="cgrt-metric-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <div class="cgrt-metric-value" id="cgrt-packet-loss-value">--</div>
                <div class="cgrt-metric-label"><?php esc_html_e( 'Packet Loss', 'cloud-gaming-readiness-test' ); ?></div>
                <div class="cgrt-metric-unit">%</div>
            </div>

            <div class="cgrt-metric-card" id="cgrt-download-card">
                <div class="cgrt-metric-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                </div>
                <div class="cgrt-metric-value" id="cgrt-download-value">--</div>
                <div class="cgrt-metric-label"><?php esc_html_e( 'Download', 'cloud-gaming-readiness-test' ); ?></div>
                <div class="cgrt-metric-unit">Mbps</div>
            </div>

            <div class="cgrt-metric-card" id="cgrt-upload-card">
                <div class="cgrt-metric-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17,8 12,3 7,8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                </div>
                <div class="cgrt-metric-value" id="cgrt-upload-value">--</div>
                <div class="cgrt-metric-label"><?php esc_html_e( 'Upload', 'cloud-gaming-readiness-test' ); ?></div>
                <div class="cgrt-metric-unit">Mbps</div>
            </div>
        </div>
    </div>

    <!-- Results Screen -->
    <div class="cgrt-results-screen" id="cgrt-results" style="display: none;">
        <div class="cgrt-results-header">
            <div class="cgrt-overall-score" id="cgrt-overall-score">
                <div class="cgrt-score-circle">
                    <svg viewBox="0 0 100 100">
                        <circle class="cgrt-score-bg" cx="50" cy="50" r="45"/>
                        <circle class="cgrt-score-progress" cx="50" cy="50" r="45"/>
                    </svg>
                    <div class="cgrt-score-value" id="cgrt-score-value">0</div>
                </div>
                <div class="cgrt-score-label" id="cgrt-score-label">
                    <?php esc_html_e( 'Testing...', 'cloud-gaming-readiness-test' ); ?>
                </div>
            </div>
        </div>

        <div class="cgrt-detailed-results">
            <h3><?php esc_html_e( 'Detailed Results', 'cloud-gaming-readiness-test' ); ?></h3>

            <div class="cgrt-result-item">
                <div class="cgrt-result-header">
                    <span class="cgrt-result-label"><?php esc_html_e( 'Latency', 'cloud-gaming-readiness-test' ); ?></span>
                    <span class="cgrt-result-value" id="cgrt-result-latency">-- ms</span>
                </div>
                <div class="cgrt-result-bar">
                    <div class="cgrt-result-fill" id="cgrt-result-latency-bar"></div>
                </div>
                <div class="cgrt-result-status" id="cgrt-result-latency-status"></div>
            </div>

            <div class="cgrt-result-item">
                <div class="cgrt-result-header">
                    <span class="cgrt-result-label"><?php esc_html_e( 'Jitter', 'cloud-gaming-readiness-test' ); ?></span>
                    <span class="cgrt-result-value" id="cgrt-result-jitter">-- ms</span>
                </div>
                <div class="cgrt-result-bar">
                    <div class="cgrt-result-fill" id="cgrt-result-jitter-bar"></div>
                </div>
                <div class="cgrt-result-status" id="cgrt-result-jitter-status"></div>
            </div>

            <div class="cgrt-result-item">
                <div class="cgrt-result-header">
                    <span class="cgrt-result-label"><?php esc_html_e( 'Packet Loss', 'cloud-gaming-readiness-test' ); ?></span>
                    <span class="cgrt-result-value" id="cgrt-result-packet-loss">-- %</span>
                </div>
                <div class="cgrt-result-bar">
                    <div class="cgrt-result-fill" id="cgrt-result-packet-loss-bar"></div>
                </div>
                <div class="cgrt-result-status" id="cgrt-result-packet-loss-status"></div>
            </div>

            <div class="cgrt-result-item">
                <div class="cgrt-result-header">
                    <span class="cgrt-result-label"><?php esc_html_e( 'Download Speed', 'cloud-gaming-readiness-test' ); ?></span>
                    <span class="cgrt-result-value" id="cgrt-result-download">-- Mbps</span>
                </div>
                <div class="cgrt-result-bar">
                    <div class="cgrt-result-fill" id="cgrt-result-download-bar"></div>
                </div>
                <div class="cgrt-result-status" id="cgrt-result-download-status"></div>
            </div>

            <div class="cgrt-result-item">
                <div class="cgrt-result-header">
                    <span class="cgrt-result-label"><?php esc_html_e( 'Upload Speed', 'cloud-gaming-readiness-test' ); ?></span>
                    <span class="cgrt-result-value" id="cgrt-result-upload">-- Mbps</span>
                </div>
                <div class="cgrt-result-bar">
                    <div class="cgrt-result-fill" id="cgrt-result-upload-bar"></div>
                </div>
                <div class="cgrt-result-status" id="cgrt-result-upload-status"></div>
            </div>
        </div>

        <div class="cgrt-recommendations">
            <h3><?php esc_html_e( 'Cloud Gaming Recommendations', 'cloud-gaming-readiness-test' ); ?></h3>
            <div class="cgrt-recommendation-list" id="cgrt-recommendation-list">
                <!-- Recommendations will be injected here -->
            </div>
        </div>

        <div class="cgrt-results-actions">
            <button class="cgrt-button cgrt-button-secondary" id="cgrt-retest-btn">
                <?php esc_html_e( 'Test Again', 'cloud-gaming-readiness-test' ); ?>
            </button>
            <button class="cgrt-button cgrt-button-primary" id="cgrt-share-btn">
                <?php esc_html_e( 'Share Results', 'cloud-gaming-readiness-test' ); ?>
            </button>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="cgrt-modal" id="cgrt-error-modal" style="display: none;">
        <div class="cgrt-modal-content">
            <div class="cgrt-modal-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <h3><?php esc_html_e( 'Test Error', 'cloud-gaming-readiness-test' ); ?></h3>
            <p id="cgrt-error-message"><?php esc_html_e( 'An error occurred during the test.', 'cloud-gaming-readiness-test' ); ?></p>
            <button class="cgrt-button cgrt-button-primary" id="cgrt-error-close-btn">
                <?php esc_html_e( 'Close', 'cloud-gaming-readiness-test' ); ?>
            </button>
        </div>
    </div>

    <!-- Theme Toggle -->
    <button class="cgrt-theme-toggle" id="cgrt-theme-toggle" aria-label="<?php esc_attr_e( 'Toggle theme', 'cloud-gaming-readiness-test' ); ?>">
        <svg class="cgrt-sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="5"/>
            <line x1="12" y1="1" x2="12" y2="3"/>
            <line x1="12" y1="21" x2="12" y2="23"/>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
            <line x1="1" y1="12" x2="3" y2="12"/>
            <line x1="21" y1="12" x2="23" y2="12"/>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
        <svg class="cgrt-moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
    </button>
</div>
