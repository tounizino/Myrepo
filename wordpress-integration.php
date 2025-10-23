<?php
/**
 * Bandwidth Speed Estimator - WordPress Integration
 * 
 * This file provides WordPress integration for the Bandwidth Speed Estimator tool.
 * Add this code to your theme's functions.php or create a custom plugin.
 * 
 * @package BandwidthSpeedEstimator
 * @author CloudLoadout.com
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Bandwidth Speed Estimator assets
 */
function bse_enqueue_assets() {
    // Only load on pages that use the shortcode or specific page templates
    // For better performance, you can conditionally load these
    
    wp_enqueue_style(
        'bandwidth-speed-estimator',
        get_stylesheet_directory_uri() . '/css/bandwidth-speed-estimator.css',
        array(),
        '1.0.0',
        'all'
    );
    
    wp_enqueue_script(
        'bandwidth-speed-estimator',
        get_stylesheet_directory_uri() . '/js/bandwidth-speed-estimator.js',
        array(),
        '1.0.0',
        true
    );
}

/**
 * Shortcode: [bandwidth_speed_test]
 * 
 * Usage: Simply add [bandwidth_speed_test] to any page or post
 * 
 * Attributes:
 * - title: Custom title (optional)
 * - subtitle: Custom subtitle (optional)
 * 
 * Example: [bandwidth_speed_test title="Test Your Speed" subtitle="Optimized for gaming"]
 */
function bse_shortcode($atts) {
    // Enqueue assets when shortcode is used
    bse_enqueue_assets();
    
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'title' => 'Ultimate Bandwidth Speed Estimator',
        'subtitle' => 'Test your connection for optimal cloud gaming performance'
    ), $atts, 'bandwidth_speed_test');
    
    // Start output buffering
    ob_start();
    
    // Include the HTML template
    ?>
    <!-- WordPress-friendly container wrapper -->
    <div id="bandwidth-speed-estimator-container" class="bse-main-wrapper">
        
        <!-- Header Section -->
        <header class="bse-header">
            <div class="bse-header-content">
                <h1 class="bse-title">
                    <svg class="bse-icon-main" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?php echo esc_html($atts['title']); ?>
                </h1>
                <p class="bse-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
            </div>
        </header>

        <!-- Main Content -->
        <main class="bse-content">
            
            <!-- Test Control Panel -->
            <section class="bse-control-panel">
                <div class="bse-card bse-control-card">
                    <div class="bse-control-header">
                        <h2 class="bse-section-title">Speed Test Controls</h2>
                    </div>
                    
                    <div class="bse-test-options">
                        <div class="bse-option-group">
                            <label class="bse-checkbox-label">
                                <input type="checkbox" id="bse-test-download" checked>
                                <span class="bse-checkbox-custom"></span>
                                <span class="bse-checkbox-text">Download Speed</span>
                            </label>
                            <label class="bse-checkbox-label">
                                <input type="checkbox" id="bse-test-upload" checked>
                                <span class="bse-checkbox-custom"></span>
                                <span class="bse-checkbox-text">Upload Speed</span>
                            </label>
                            <label class="bse-checkbox-label">
                                <input type="checkbox" id="bse-test-latency" checked>
                                <span class="bse-checkbox-custom"></span>
                                <span class="bse-checkbox-text">Latency & Jitter</span>
                            </label>
                        </div>
                        
                        <div class="bse-test-size-selector">
                            <label for="bse-test-size" class="bse-label">Test Size:</label>
                            <select id="bse-test-size" class="bse-select">
                                <option value="small">Small (Quick - 5MB)</option>
                                <option value="medium" selected>Medium (Balanced - 15MB)</option>
                                <option value="large">Large (Accurate - 30MB)</option>
                            </select>
                        </div>
                    </div>

                    <button id="bse-start-test" class="bse-btn bse-btn-primary">
                        <svg class="bse-btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 3L19 12L5 21V3Z" fill="currentColor"/>
                        </svg>
                        <span class="bse-btn-text">Start Speed Test</span>
                    </button>

                    <div id="bse-progress-container" class="bse-progress-container bse-hidden">
                        <div class="bse-progress-info">
                            <span id="bse-current-test" class="bse-progress-label">Initializing...</span>
                            <span id="bse-progress-percent" class="bse-progress-percent">0%</span>
                        </div>
                        <div class="bse-progress-bar">
                            <div id="bse-progress-fill" class="bse-progress-fill"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Results Dashboard -->
            <section class="bse-results-section">
                <div class="bse-results-grid">
                    
                    <!-- Download Speed Card -->
                    <div class="bse-card bse-result-card">
                        <div class="bse-result-header">
                            <svg class="bse-result-icon bse-icon-download" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 3V16M12 16L7 11M12 16L17 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <h3 class="bse-result-title">Download Speed</h3>
                        </div>
                        <div class="bse-result-value" id="bse-download-result">
                            <span class="bse-speed-value">--</span>
                            <span class="bse-speed-unit">Mbps</span>
                        </div>
                        <div class="bse-result-details" id="bse-download-details">
                            <span class="bse-detail-label">Waiting for test...</span>
                        </div>
                    </div>

                    <!-- Upload Speed Card -->
                    <div class="bse-card bse-result-card">
                        <div class="bse-result-header">
                            <svg class="bse-result-icon bse-icon-upload" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21V8M12 8L7 13M12 8L17 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 4H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <h3 class="bse-result-title">Upload Speed</h3>
                        </div>
                        <div class="bse-result-value" id="bse-upload-result">
                            <span class="bse-speed-value">--</span>
                            <span class="bse-speed-unit">Mbps</span>
                        </div>
                        <div class="bse-result-details" id="bse-upload-details">
                            <span class="bse-detail-label">Waiting for test...</span>
                        </div>
                    </div>

                    <!-- Latency Card -->
                    <div class="bse-card bse-result-card">
                        <div class="bse-result-header">
                            <svg class="bse-result-icon bse-icon-latency" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <h3 class="bse-result-title">Latency (Ping)</h3>
                        </div>
                        <div class="bse-result-value" id="bse-latency-result">
                            <span class="bse-speed-value">--</span>
                            <span class="bse-speed-unit">ms</span>
                        </div>
                        <div class="bse-result-details" id="bse-latency-details">
                            <span class="bse-detail-label">Waiting for test...</span>
                        </div>
                    </div>

                    <!-- Jitter Card -->
                    <div class="bse-card bse-result-card">
                        <div class="bse-result-header">
                            <svg class="bse-result-icon bse-icon-jitter" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 12H6L9 3L15 21L18 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <h3 class="bse-result-title">Jitter</h3>
                        </div>
                        <div class="bse-result-value" id="bse-jitter-result">
                            <span class="bse-speed-value">--</span>
                            <span class="bse-speed-unit">ms</span>
                        </div>
                        <div class="bse-result-details" id="bse-jitter-details">
                            <span class="bse-detail-label">Waiting for test...</span>
                        </div>
                    </div>

                </div>
            </section>

            <!-- Real-time Chart -->
            <section class="bse-chart-section">
                <div class="bse-card bse-chart-card">
                    <h2 class="bse-section-title">Real-Time Performance</h2>
                    <canvas id="bse-speed-chart" class="bse-chart-canvas"></canvas>
                    <div id="bse-chart-placeholder" class="bse-chart-placeholder">
                        <svg class="bse-chart-placeholder-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 3V21H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7 14L11 10L15 14L21 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p>Run a test to see real-time performance metrics</p>
                    </div>
                </div>
            </section>

            <!-- Overall Score & Gaming Recommendations -->
            <section class="bse-score-section">
                <div class="bse-card bse-score-card">
                    <h2 class="bse-section-title">Network Quality Score</h2>
                    <div class="bse-score-container">
                        <div class="bse-score-circle" id="bse-score-circle">
                            <svg class="bse-score-svg" viewBox="0 0 200 200">
                                <circle class="bse-score-bg" cx="100" cy="100" r="90"></circle>
                                <circle class="bse-score-progress" id="bse-score-progress" cx="100" cy="100" r="90"></circle>
                            </svg>
                            <div class="bse-score-text">
                                <span class="bse-score-number" id="bse-score-number">--</span>
                                <span class="bse-score-label">Score</span>
                            </div>
                        </div>
                        <div class="bse-score-info">
                            <h3 class="bse-score-grade" id="bse-score-grade">Not Tested</h3>
                            <p class="bse-score-description" id="bse-score-description">
                                Run a speed test to get your network quality score for cloud gaming.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Cloud Gaming Service Compatibility -->
            <section class="bse-gaming-section">
                <div class="bse-card bse-gaming-card">
                    <h2 class="bse-section-title">
                        <svg class="bse-section-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="7" width="20" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
                            <circle cx="7" cy="12" r="1.5" fill="currentColor"/>
                            <circle cx="17" cy="12" r="1.5" fill="currentColor"/>
                            <path d="M14 10L15 9L16 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M14 14L15 15L16 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        Cloud Gaming Compatibility
                    </h2>
                    <div class="bse-gaming-grid" id="bse-gaming-compatibility"></div>
                </div>
            </section>

            <!-- Recommendations -->
            <section class="bse-recommendations-section">
                <div class="bse-card bse-recommendations-card">
                    <h2 class="bse-section-title">
                        <svg class="bse-section-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Recommendations
                    </h2>
                    <div class="bse-recommendations-list" id="bse-recommendations-list">
                        <div class="bse-recommendation-placeholder">
                            <p>Complete a speed test to receive personalized recommendations for optimal cloud gaming performance.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Test History -->
            <section class="bse-history-section">
                <div class="bse-card bse-history-card">
                    <div class="bse-history-header">
                        <h2 class="bse-section-title">
                            <svg class="bse-section-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Test History
                        </h2>
                        <button id="bse-clear-history" class="bse-btn bse-btn-secondary bse-btn-small">
                            Clear History
                        </button>
                    </div>
                    <div class="bse-history-list" id="bse-history-list">
                        <div class="bse-history-placeholder">
                            <p>No test history available. Run your first speed test to start tracking your network performance.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Share Results -->
            <section class="bse-share-section bse-hidden" id="bse-share-section">
                <div class="bse-card bse-share-card">
                    <h2 class="bse-section-title">Share Your Results</h2>
                    <div class="bse-share-buttons">
                        <button class="bse-btn bse-btn-share" id="bse-share-twitter">
                            <svg class="bse-btn-icon" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                            </svg>
                            Share on Twitter
                        </button>
                        <button class="bse-btn bse-btn-share" id="bse-copy-results">
                            <svg class="bse-btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="9" y="9" width="13" height="13" rx="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M5 15H4C2.89543 15 2 14.1046 2 13V4C2 2.89543 2.89543 2 4 2H13C14.1046 2 15 2.89543 15 4V5" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Copy Results
                        </button>
                        <button class="bse-btn bse-btn-share" id="bse-download-results">
                            <svg class="bse-btn-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 15V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Download Report
                        </button>
                    </div>
                </div>
            </section>

            <!-- Network Tips -->
            <section class="bse-tips-section">
                <div class="bse-card bse-tips-card">
                    <h2 class="bse-section-title">
                        <svg class="bse-section-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 16V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="12" cy="8" r="0.5" fill="currentColor" stroke="currentColor"/>
                        </svg>
                        Tips for Best Results
                    </h2>
                    <ul class="bse-tips-list">
                        <li class="bse-tip-item">
                            <svg class="bse-tip-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Use a wired Ethernet connection for most accurate results</span>
                        </li>
                        <li class="bse-tip-item">
                            <svg class="bse-tip-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Close other applications and browser tabs during testing</span>
                        </li>
                        <li class="bse-tip-item">
                            <svg class="bse-tip-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Stop downloads and streaming services before testing</span>
                        </li>
                        <li class="bse-tip-item">
                            <svg class="bse-tip-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Test at different times of day for comprehensive results</span>
                        </li>
                        <li class="bse-tip-item">
                            <svg class="bse-tip-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>For cloud gaming, latency under 40ms is ideal</span>
                        </li>
                        <li class="bse-tip-item">
                            <svg class="bse-tip-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>5GHz WiFi band performs better than 2.4GHz for gaming</span>
                        </li>
                    </ul>
                </div>
            </section>

        </main>

        <!-- Footer -->
        <footer class="bse-footer">
            <p class="bse-footer-text">
                <strong><?php bloginfo('name'); ?></strong> - Your ultimate cloud gaming resource
            </p>
            <p class="bse-footer-note">
                Note: Results are estimates and may vary based on network conditions.
            </p>
        </footer>

    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('bandwidth_speed_test', 'bse_shortcode');

/**
 * Add custom body class when shortcode is present
 */
function bse_body_class($classes) {
    global $post;
    
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'bandwidth_speed_test')) {
        $classes[] = 'has-bandwidth-test';
    }
    
    return $classes;
}
add_filter('body_class', 'bse_body_class');

/**
 * Add admin menu for tool documentation
 */
function bse_admin_menu() {
    add_submenu_page(
        'tools.php',
        'Bandwidth Speed Test',
        'Speed Test Tool',
        'manage_options',
        'bandwidth-speed-test',
        'bse_admin_page'
    );
}
add_action('admin_menu', 'bse_admin_menu');

/**
 * Admin page content
 */
function bse_admin_page() {
    ?>
    <div class="wrap">
        <h1>Bandwidth Speed Estimator</h1>
        
        <div class="card">
            <h2>How to Use</h2>
            <p>Add the following shortcode to any page or post:</p>
            <code>[bandwidth_speed_test]</code>
            
            <h3>With Custom Title & Subtitle:</h3>
            <code>[bandwidth_speed_test title="Custom Title" subtitle="Custom Subtitle"]</code>
        </div>
        
        <div class="card">
            <h2>File Locations</h2>
            <p>Make sure the following files are uploaded to your theme:</p>
            <ul>
                <li><strong>CSS:</strong> /wp-content/themes/your-theme/css/bandwidth-speed-estimator.css</li>
                <li><strong>JavaScript:</strong> /wp-content/themes/your-theme/js/bandwidth-speed-estimator.js</li>
            </ul>
        </div>
        
        <div class="card">
            <h2>Features</h2>
            <ul>
                <li>✅ Download Speed Test</li>
                <li>✅ Upload Speed Test</li>
                <li>✅ Latency & Jitter Measurement</li>
                <li>✅ Cloud Gaming Compatibility Check</li>
                <li>✅ Personalized Recommendations</li>
                <li>✅ Test History (Stored Locally)</li>
                <li>✅ Share Results</li>
                <li>✅ Mobile Responsive</li>
                <li>✅ SEO Optimized</li>
            </ul>
        </div>
        
        <div class="card">
            <h2>Support</h2>
            <p>For issues or questions, check the README.md file included with the tool.</p>
        </div>
    </div>
    <?php
}
?>
