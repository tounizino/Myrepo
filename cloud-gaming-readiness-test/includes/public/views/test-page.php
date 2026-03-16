<?php
/**
 * Cloud Gaming Test page template - Full-page immersive design.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get settings.
$theme_color = isset( $atts['theme'] ) ? $atts['theme'] : get_option( 'cgrt_theme_color', '#00ff88' );
?>

<div id="cgrt-app" class="cgrt-container" data-theme-color="<?php echo esc_attr( $theme_color ); ?>">
    <div class="cgrt-content-wrapper">
        <!-- Header -->
        <header class="cgrt-header">
            <div class="cgrt-logo">
                <div class="cgrt-logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <span class="cgrt-logo-text">CloudGamingTest</span>
            </div>
            <nav class="cgrt-nav">
                <a href="#" class="cgrt-nav-link active">Test</a>
                <a href="#" class="cgrt-nav-link">Guide</a>
                <a href="#" class="cgrt-nav-link">FAQ</a>
            </nav>
        </header>

        <!-- Intro Screen -->
        <section class="cgrt-intro-screen" id="cgrt-intro">
            <div class="cgrt-hero">
                <div class="cgrt-hero-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                    <span>Real-time Network Analysis</span>
                </div>
                
                <h1 class="cgrt-hero-title">
                    Test Your Network for<br>
                    <span class="gradient">Cloud Gaming</span>
                </h1>
                
                <p class="cgrt-hero-subtitle">
                    Comprehensive diagnostics including latency, jitter, packet loss, and speed analysis to determine if your connection is ready for GeForce NOW, Xbox Cloud Gaming, PlayStation Plus, and more.
                </p>
                
                <button class="cgrt-start-button" id="cgrt-start-btn">
                    <span>Start Full Test</span>
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </button>
                
                <!-- Features Grid -->
                <div class="cgrt-features">
                    <div class="cgrt-feature-card">
                        <div class="cgrt-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                            </svg>
                        </div>
                        <h3 class="cgrt-feature-title">Ultra-Low Latency Test</h3>
                        <p class="cgrt-feature-desc">Precise ping measurements to gaming servers with sub-millisecond accuracy detection.</p>
                    </div>
                    
                    <div class="cgrt-feature-card">
                        <div class="cgrt-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="cgrt-feature-title">Jitter & Stability</h3>
                        <p class="cgrt-feature-desc">Analyze connection consistency to predict and prevent in-game stutter and lag.</p>
                    </div>
                    
                    <div class="cgrt-feature-card">
                        <div class="cgrt-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                        <h3 class="cgrt-feature-title">Speed Analysis</h3>
                        <p class="cgrt-feature-desc">Download and upload speed testing with multi-server accuracy verification.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testing Screen -->
        <section class="cgrt-test-screen" id="cgrt-test" style="display: none;">
            <div class="cgrt-test-header">
                <h2 class="cgrt-test-title">Running Diagnostics</h2>
                <p class="cgrt-test-subtitle">Analyzing your connection for cloud gaming readiness</p>
            </div>

            <!-- Progress Section -->
            <div class="cgrt-progress-section">
                <div class="cgrt-progress-bar-container">
                    <div class="cgrt-progress-bar">
                        <div class="cgrt-progress-fill" id="cgrt-progress-fill"></div>
                    </div>
                    <div class="cgrt-progress-percent" id="cgrt-progress-percent">0%</div>
                </div>
                <div class="cgrt-progress-status">
                    <span class="cgrt-progress-text" id="cgrt-progress-text">Initializing...</span>
                    <span class="cgrt-progress-eta" id="cgrt-progress-eta">Estimated: ~15s</span>
                </div>
            </div>

            <!-- Metrics Dashboard -->
            <div class="cgrt-metrics-dashboard">
                <!-- Latency -->
                <div class="cgrt-metric-card" id="cgrt-latency-card">
                    <div class="cgrt-metric-header">
                        <div class="cgrt-metric-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                            </svg>
                        </div>
                        <span class="cgrt-metric-status" id="cgrt-latency-status">Waiting</span>
                    </div>
                    <div class="cgrt-metric-value" id="cgrt-latency-value">--</div>
                    <div class="cgrt-metric-unit">ms</div>
                    <div class="cgrt-metric-label">Latency</div>
                    <div class="cgrt-metric-detail">
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Min</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-latency-min">--</div>
                        </div>
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Max</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-latency-max">--</div>
                        </div>
                    </div>
                </div>

                <!-- Jitter -->
                <div class="cgrt-metric-card" id="cgrt-jitter-card">
                    <div class="cgrt-metric-header">
                        <div class="cgrt-metric-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="cgrt-metric-status" id="cgrt-jitter-status">Waiting</span>
                    </div>
                    <div class="cgrt-metric-value" id="cgrt-jitter-value">--</div>
                    <div class="cgrt-metric-unit">ms</div>
                    <div class="cgrt-metric-label">Jitter</div>
                    <div class="cgrt-metric-detail">
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Avg</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-jitter-avg">--</div>
                        </div>
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Peak</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-jitter-peak">--</div>
                        </div>
                    </div>
                </div>

                <!-- Packet Loss -->
                <div class="cgrt-metric-card" id="cgrt-packet-loss-card">
                    <div class="cgrt-metric-header">
                        <div class="cgrt-metric-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                        <span class="cgrt-metric-status" id="cgrt-packet-loss-status">Waiting</span>
                    </div>
                    <div class="cgrt-metric-value" id="cgrt-packet-loss-value">--</div>
                    <div class="cgrt-metric-unit">%</div>
                    <div class="cgrt-metric-label">Packet Loss</div>
                    <div class="cgrt-metric-detail">
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Sent</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-packet-sent">--</div>
                        </div>
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Lost</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-packet-lost">--</div>
                        </div>
                    </div>
                </div>

                <!-- Download Speed -->
                <div class="cgrt-metric-card" id="cgrt-download-card">
                    <div class="cgrt-metric-header">
                        <div class="cgrt-metric-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7,10 12,15 17,10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                        </div>
                        <span class="cgrt-metric-status" id="cgrt-download-status">Waiting</span>
                    </div>
                    <div class="cgrt-metric-value" id="cgrt-download-value">--</div>
                    <div class="cgrt-metric-unit">Mbps</div>
                    <div class="cgrt-metric-label">Download Speed</div>
                    <div class="cgrt-metric-detail">
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Peak</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-download-peak">--</div>
                        </div>
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Avg</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-download-avg">--</div>
                        </div>
                    </div>
                </div>

                <!-- Upload Speed -->
                <div class="cgrt-metric-card" id="cgrt-upload-card">
                    <div class="cgrt-metric-header">
                        <div class="cgrt-metric-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17,8 12,3 7,8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                        </div>
                        <span class="cgrt-metric-status" id="cgrt-upload-status">Waiting</span>
                    </div>
                    <div class="cgrt-metric-value" id="cgrt-upload-value">--</div>
                    <div class="cgrt-metric-unit">Mbps</div>
                    <div class="cgrt-metric-label">Upload Speed</div>
                    <div class="cgrt-metric-detail">
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Peak</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-upload-peak">--</div>
                        </div>
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Avg</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-upload-avg">--</div>
                        </div>
                    </div>
                </div>

                <!-- Connection Quality -->
                <div class="cgrt-metric-card" id="cgrt-quality-card">
                    <div class="cgrt-metric-header">
                        <div class="cgrt-metric-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <span class="cgrt-metric-status" id="cgrt-quality-status">Waiting</span>
                    </div>
                    <div class="cgrt-metric-value" id="cgrt-quality-value">--</div>
                    <div class="cgrt-metric-unit">Grade</div>
                    <div class="cgrt-metric-label">Connection Quality</div>
                    <div class="cgrt-metric-detail">
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Stability</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-stability">--</div>
                        </div>
                        <div class="cgrt-metric-detail-item">
                            <div class="cgrt-metric-detail-label">Server</div>
                            <div class="cgrt-metric-detail-value" id="cgrt-server">--</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Results Screen -->
        <section class="cgrt-results-screen" id="cgrt-results" style="display: none;">
            <div class="cgrt-results-header">
                <h2 class="cgrt-test-title">Your Results</h2>
                <p class="cgrt-test-subtitle">Comprehensive analysis of your cloud gaming readiness</p>
            </div>

            <!-- Overall Score -->
            <div class="cgrt-score-section">
                <div class="cgrt-score-card">
                    <div class="cgrt-score-circle-container">
                        <svg class="cgrt-score-circle" viewBox="0 0 200 200">
                            <circle class="cgrt-score-bg" cx="100" cy="100" r="90"/>
                            <circle class="cgrt-score-progress" cx="100" cy="100" r="90"/>
                        </svg>
                        <div class="cgrt-score-value" id="cgrt-score-value">0</div>
                    </div>
                    <div class="cgrt-score-label" id="cgrt-score-label">
                        Testing...
                    </div>
                </div>
            </div>

            <!-- Detailed Results -->
            <div class="cgrt-detailed-results">
                <h3 class="cgrt-section-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 19v-6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2zm0 0V9a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10m10-10v6a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2zm0 0V5a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v14"/>
                    </svg>
                    Performance Metrics
                </h3>
                
                <div class="cgrt-results-grid">
                    <!-- Latency Result -->
                    <div class="cgrt-result-item" id="cgrt-result-latency-item">
                        <div class="cgrt-result-header">
                            <div class="cgrt-result-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                                </svg>
                                <span class="cgrt-result-label">Latency</span>
                            </div>
                            <div class="cgrt-result-value-group">
                                <span class="cgrt-result-value" id="cgrt-result-latency">--</span>
                                <span class="cgrt-result-unit">ms</span>
                            </div>
                        </div>
                        <div class="cgrt-result-bar">
                            <div class="cgrt-result-fill" id="cgrt-result-latency-bar"></div>
                        </div>
                        <div class="cgrt-result-details">
                            <div class="cgrt-result-detail">
                                <span>Min: <strong id="cgrt-result-latency-min">--</strong> ms</span>
                            </div>
                            <div class="cgrt-result-detail">
                                <span>Max: <strong id="cgrt-result-latency-max">--</strong> ms</span>
                            </div>
                            <div class="cgrt-result-detail">
                                <span>Deviation: <strong id="cgrt-result-latency-dev">--</strong> ms</span>
                            </div>
                        </div>
                    </div>

                    <!-- Jitter Result -->
                    <div class="cgrt-result-item" id="cgrt-result-jitter-item">
                        <div class="cgrt-result-header">
                            <div class="cgrt-result-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="cgrt-result-label">Jitter</span>
                            </div>
                            <div class="cgrt-result-value-group">
                                <span class="cgrt-result-value" id="cgrt-result-jitter">--</span>
                                <span class="cgrt-result-unit">ms</span>
                            </div>
                        </div>
                        <div class="cgrt-result-bar">
                            <div class="cgrt-result-fill" id="cgrt-result-jitter-bar"></div>
                        </div>
                        <div class="cgrt-result-details">
                            <div class="cgrt-result-detail">
                                <span>Avg: <strong id="cgrt-result-jitter-avg">--</strong> ms</span>
                            </div>
                            <div class="cgrt-result-detail">
                                <span>Peak: <strong id="cgrt-result-jitter-peak">--</strong> ms</span>
                            </div>
                            <div class="cgrt-result-detail">
                                <span>Stability: <strong id="cgrt-result-jitter-stability">--</strong>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Packet Loss Result -->
                    <div class="cgrt-result-item" id="cgrt-result-packet-loss-item">
                        <div class="cgrt-result-header">
                            <div class="cgrt-result-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                </svg>
                                <span class="cgrt-result-label">Packet Loss</span>
                            </div>
                            <div class="cgrt-result-value-group">
                                <span class="cgrt-result-value" id="cgrt-result-packet-loss">--</span>
                                <span class="cgrt-result-unit">%</span>
                            </div>
                        </div>
                        <div class="cgrt-result-bar">
                            <div class="cgrt-result-fill" id="cgrt-result-packet-loss-bar"></div>
                        </div>
                        <div class="cgrt-result-details">
                            <div class="cgrt-result-detail">
                                <span>Sent: <strong id="cgrt-result-packet-sent">--</strong></span>
                            </div>
                            <div class="cgrt-result-detail">
                                <span>Lost: <strong id="cgrt-result-packet-lost">--</strong></span>
                            </div>
                            <div class="cgrt-result-detail">
                                <span>Retransmits: <strong id="cgrt-result-retransmits">--</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Download Result -->
                    <div class="cgrt-result-item" id="cgrt-result-download-item">
                        <div class="cgrt-result-header">
                            <div class="cgrt-result-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7,10 12,15 17,10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                <span class="cgrt-result-label">Download Speed</span>
                            </div>
                            <div class="cgrt-result-value-group">
                                <span class="cgrt-result-value" id="cgrt-result-download">--</span>
                                <span class="cgrt-result-unit">Mbps</span>
                            </div>
                        </div>
                        <div class="cgrt-result-bar">
                            <div class="cgrt-result-fill" id="cgrt-result-download-bar"></div>
                        </div>
                        <div class="cgrt-result-details">
                            <div class="cgrt-result-detail">
                                <span>Peak: <strong id="cgrt-result-download-peak">--</strong> Mbps</span>
                            </div>
                            <div class="cgrt-result-detail">
                                <span>Avg: <strong id="cgrt-result-download-avg">--</strong> Mbps</span>
                            </div>
                            <div class="cgrt-result-detail">
                                <span>Consistency: <strong id="cgrt-result-download-consistency">--</strong>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Result -->
                    <div class="cgrt-result-item" id="cgrt-result-upload-item">
                        <div class="cgrt-result-header">
                            <div class="cgrt-result-title">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17,8 12,3 7,8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                <span class="cgrt-result-label">Upload Speed</span>
                            </div>
                            <div class="cgrt-result-value-group">
                                <span class="cgrt-result-value" id="cgrt-result-upload">--</span>
                                <span class="cgrt-result-unit">Mbps</span>
                            </div>
                        </div>
                        <div class="cgrt-result-bar">
                            <div class="cgrt-result-fill" id="cgrt-result-upload-bar"></div>
                        </div>
                        <div class="cgrt-result-details">
                            <div class="cgrt-result-detail">
                                <span>Peak: <strong id="cgrt-result-upload-peak">--</strong> Mbps</span>
                            </div>
                            <div class="cgrt-result-detail">
                                <span>Avg: <strong id="cgrt-result-upload-avg">--</strong> Mbps</span>
                            </div>
                            <div class="cgrt-result-detail">
                                <span>Latency Impact: <strong id="cgrt-result-upload-latency">--</strong> ms</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommendations -->
            <div class="cgrt-recommendations">
                <h3 class="cgrt-section-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Cloud Gaming Recommendations
                </h3>
                <div class="cgrt-recommendation-list" id="cgrt-recommendation-list">
                    <!-- Recommendations will be injected here -->
                </div>
            </div>

            <!-- Actions -->
            <div class="cgrt-actions">
                <button class="cgrt-button cgrt-button-secondary" id="cgrt-retest-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <path d="M23 4v6h-6"/>
                        <path d="M1 20v-6h6"/>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                    </svg>
                    Run Test Again
                </button>
                <button class="cgrt-button cgrt-button-primary" id="cgrt-share-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <circle cx="18" cy="5" r="3"/>
                        <circle cx="6" cy="12" r="3"/>
                        <circle cx="18" cy="19" r="3"/>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                    </svg>
                    Share Results
                </button>
            </div>
        </section>
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
            <h3>Test Error</h3>
            <p id="cgrt-error-message">An error occurred during the test.</p>
            <button class="cgrt-button cgrt-button-primary" id="cgrt-error-close-btn">Close</button>
        </div>
    </div>
</div>
