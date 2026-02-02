<div id="cgrt-test-container" class="cgrt-container">
    <div class="cgrt-header">
        <h2><?php _e( 'Cloud Gaming Readiness Test', 'cloud-gaming-test' ); ?></h2>
        <p><?php _e( 'Analyze your connection for high-performance cloud gaming.', 'cloud-gaming-test' ); ?></p>
    </div>

    <div id="cgrt-setup" class="cgrt-section">
        <div class="cgrt-form-group">
            <label for="cgrt-platform-select"><?php _e( 'Select Gaming Platform', 'cloud-gaming-test' ); ?></label>
            <select id="cgrt-platform-select">
                <?php 
                if ( is_array( $platforms ) ) :
                    foreach ( $platforms as $platform ) : 
                        if ( ! isset($platform['enabled']) || ! $platform['enabled'] ) continue; 
                        ?>
                        <option value="<?php echo esc_attr( $platform['id'] ); ?>"><?php echo esc_html( $platform['name'] ); ?></option>
                        <?php 
                    endforeach; 
                endif;
                ?>
            </select>
        </div>
        <div class="cgrt-form-group">
            <label for="cgrt-server-select"><?php _e( 'Select Target Region', 'cloud-gaming-test' ); ?></label>
            <select id="cgrt-server-select">
                <!-- Populated by JS -->
            </select>
        </div>
        <button id="cgrt-start-btn" class="cgrt-btn-primary"><?php _e( 'Start Diagnostic Test', 'cloud-gaming-test' ); ?></button>
    </div>

    <div id="cgrt-testing" class="cgrt-section" style="display: none;">
        <div class="cgrt-progress-container">
            <div id="cgrt-progress-bar" class="cgrt-progress-bar"></div>
        </div>
        <div class="cgrt-live-stats">
            <div class="cgrt-stat">
                <span class="label"><?php _e( 'Latency', 'cloud-gaming-test' ); ?></span>
                <span id="cgrt-live-latency" class="value">--</span><span class="unit">ms</span>
            </div>
            <div class="cgrt-stat">
                <span class="label"><?php _e( 'Jitter', 'cloud-gaming-test' ); ?></span>
                <span id="cgrt-live-jitter" class="value">--</span><span class="unit">ms</span>
            </div>
            <div class="cgrt-stat">
                <span class="label"><?php _e( 'Packet Loss', 'cloud-gaming-test' ); ?></span>
                <span id="cgrt-live-packet-loss" class="value">--</span><span class="unit">%</span>
            </div>
        </div>
        <div id="cgrt-live-graph" class="cgrt-graph-container">
            <canvas id="cgrt-latency-chart"></canvas>
        </div>
        <div class="cgrt-status-text" id="cgrt-status-text">
            <?php _e( 'Initializing secure connection...', 'cloud-gaming-test' ); ?>
        </div>
    </div>

    <div id="cgrt-results" class="cgrt-section" style="display: none;">
        <div class="cgrt-score-circle">
            <svg viewBox="0 0 36 36" class="circular-chart">
                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <path id="cgrt-score-path" class="circle" stroke-dasharray="0, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <text x="18" y="20.35" class="percentage" id="cgrt-score-value">0</text>
            </svg>
        </div>
        <div class="cgrt-verdict">
            <h3 id="cgrt-verdict-title">--</h3>
            <p id="cgrt-verdict-desc">--</p>
        </div>

        <div class="cgrt-results-grid">
            <div class="cgrt-result-card">
                <span class="label"><?php _e( 'Avg Latency', 'cloud-gaming-test' ); ?></span>
                <span id="cgrt-final-latency" class="value">--</span>
            </div>
            <div class="cgrt-result-card">
                <span class="label"><?php _e( 'Max Jitter', 'cloud-gaming-test' ); ?></span>
                <span id="cgrt-final-jitter" class="value">--</span>
            </div>
            <div class="cgrt-result-card">
                <span class="label"><?php _e( 'Packet Loss', 'cloud-gaming-test' ); ?></span>
                <span id="cgrt-final-packet-loss" class="value">--</span>
            </div>
            <div class="cgrt-result-card">
                <span class="label"><?php _e( 'Stability', 'cloud-gaming-test' ); ?></span>
                <span id="cgrt-final-stability" class="value">--</span>
            </div>
        </div>

        <div class="cgrt-recommendations" id="cgrt-recommendations">
            <!-- Recommendations based on results -->
        </div>

        <button id="cgrt-retry-btn" class="cgrt-btn-secondary"><?php _e( 'Run Test Again', 'cloud-gaming-test' ); ?></button>
    </div>
</div>
