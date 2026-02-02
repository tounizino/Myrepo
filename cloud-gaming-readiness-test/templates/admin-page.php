<div class="wrap cgrt-admin">
    <h1><?php _e( 'Cloud Gaming Readiness Test Settings', 'cloud-gaming-test' ); ?></h1>

    <div class="cgrt-card" style="background: #e7f3ff; border-left: 4px solid #007bff;">
        <h2><?php _e( 'Overview', 'cloud-gaming-test' ); ?></h2>
        <p><strong><?php _e( 'Total Tests Conducted:', 'cloud-gaming-test' ); ?></strong> <?php echo intval( $stats['total_tests'] ); ?></p>
    </div>

    <form method="post" action="">
        <?php wp_nonce_field( 'cgrt_settings_nonce' ); ?>
        
        <div class="cgrt-card">
            <h2><?php _e( 'Test Configuration', 'cloud-gaming-test' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="test_duration"><?php _e( 'Test Duration (seconds)', 'cloud-gaming-test' ); ?></label></th>
                    <td><input name="test_duration" type="number" id="test_duration" value="<?php echo esc_attr( $settings['test_duration'] ); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="test_intensity"><?php _e( 'Test Intensity (requests/sec)', 'cloud-gaming-test' ); ?></label></th>
                    <td><input name="test_intensity" type="number" id="test_intensity" value="<?php echo esc_attr( $settings['test_intensity'] ); ?>" class="small-text"></td>
                </tr>
            </table>
        </div>

        <div class="cgrt-card">
            <h2><?php _e( 'Scoring Weights (%)', 'cloud-gaming-test' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="weight_latency"><?php _e( 'Latency Weight', 'cloud-gaming-test' ); ?></label></th>
                    <td><input name="weight_latency" type="number" id="weight_latency" value="<?php echo esc_attr( $settings['weights']['latency'] ); ?>" class="small-text"> %</td>
                </tr>
                <tr>
                    <th scope="row"><label for="weight_jitter"><?php _e( 'Jitter Weight', 'cloud-gaming-test' ); ?></label></th>
                    <td><input name="weight_jitter" type="number" id="weight_jitter" value="<?php echo esc_attr( $settings['weights']['jitter'] ); ?>" class="small-text"> %</td>
                </tr>
                <tr>
                    <th scope="row"><label for="weight_packet_loss"><?php _e( 'Packet Loss Weight', 'cloud-gaming-test' ); ?></label></th>
                    <td><input name="weight_packet_loss" type="number" id="weight_packet_loss" value="<?php echo esc_attr( $settings['weights']['packet_loss'] ); ?>" class="small-text"> %</td>
                </tr>
                <tr>
                    <th scope="row"><label for="weight_stability"><?php _e( 'Stability Weight', 'cloud-gaming-test' ); ?></label></th>
                    <td><input name="weight_stability" type="number" id="weight_stability" value="<?php echo esc_attr( $settings['weights']['stability'] ); ?>" class="small-text"> %</td>
                </tr>
            </table>
        </div>

        <div class="cgrt-card">
            <h2><?php _e( 'Readiness Thresholds (Total Score 0-100)', 'cloud-gaming-test' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="threshold_excellent"><?php _e( 'Excellent Threshold', 'cloud-gaming-test' ); ?></label></th>
                    <td><input name="threshold_excellent" type="number" id="threshold_excellent" value="<?php echo esc_attr( $settings['thresholds']['excellent'] ); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="threshold_good"><?php _e( 'Good Threshold', 'cloud-gaming-test' ); ?></label></th>
                    <td><input name="threshold_good" type="number" id="threshold_good" value="<?php echo esc_attr( $settings['thresholds']['good'] ); ?>" class="small-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="threshold_fair"><?php _e( 'Fair Threshold', 'cloud-gaming-test' ); ?></label></th>
                    <td><input name="threshold_fair" type="number" id="threshold_fair" value="<?php echo esc_attr( $settings['thresholds']['fair'] ); ?>" class="small-text"></td>
                </tr>
            </table>
        </div>

        <p class="submit">
            <input type="submit" name="cgrt_save_settings" id="submit" class="button button-primary" value="<?php _e( 'Save All Settings', 'cloud-gaming-test' ); ?>">
        </p>
    </form>

    <div class="cgrt-card">
        <h2><?php _e( 'Cloud Gaming Platforms', 'cloud-gaming-test' ); ?></h2>
        <p><?php _e( 'Manage the platforms and endpoints used for testing.', 'cloud-gaming-test' ); ?></p>
        <div id="cgrt-platforms-manager">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e( 'Platform Name', 'cloud-gaming-test' ); ?></th>
                        <th><?php _e( 'Endpoints', 'cloud-gaming-test' ); ?></th>
                        <th><?php _e( 'Status', 'cloud-gaming-test' ); ?></th>
                        <th><?php _e( 'Actions', 'cloud-gaming-test' ); ?></th>
                    </tr>
                </thead>
                <tbody id="cgrt-platforms-list">
                    <?php 
                    if ( is_array( $platforms ) ) :
                        foreach ( $platforms as $platform ) : 
                            if ( ! is_array( $platform ) ) continue;
                            ?>
                            <tr data-platform='<?php echo esc_attr( json_encode($platform) ); ?>'>
                                <td><strong><?php echo esc_html( isset($platform['name']) ? $platform['name'] : 'Unnamed' ); ?></strong></td>
                                <td><?php echo isset($platform['servers']) && is_array($platform['servers']) ? count( $platform['servers'] ) : 0; ?> <?php _e( 'server(s)', 'cloud-gaming-test' ); ?></td>
                                <td><?php echo isset($platform['enabled']) && $platform['enabled'] ? __( 'Enabled', 'cloud-gaming-test' ) : __( 'Disabled', 'cloud-gaming-test' ); ?></td>
                                <td>
                                    <button class="button edit-platform-btn"><?php _e( 'Edit', 'cloud-gaming-test' ); ?></button>
                                    <button class="button delete-platform-btn" style="color:red"><?php _e( 'Delete', 'cloud-gaming-test' ); ?></button>
                                </td>
                            </tr>
                            <?php 
                        endforeach; 
                    endif;
                    ?>
                </tbody>
            </table>
            <p><button class="button button-secondary" id="add-platform-btn"><?php _e( 'Add New Platform', 'cloud-gaming-test' ); ?></button></p>
        </div>
    </div>

    <!-- Platform Edit Modal (Simplified) -->
    <div id="cgrt-platform-modal" style="display:none; position:fixed; top:10%; left:25%; width:50%; background:#fff; border:1px solid #ccc; padding:20px; z-index:10000; box-shadow: 0 0 20px rgba(0,0,0,0.2);">
        <h2 id="modal-title">Edit Platform</h2>
        <input type="hidden" id="edit-platform-id">
        <p>
            <label>Platform Name:<br>
            <input type="text" id="edit-platform-name" class="regular-text"></label>
        </p>
        <p>
            <label><input type="checkbox" id="edit-platform-enabled"> Enabled</label>
        </p>
        <h3>Endpoints</h3>
        <div id="edit-servers-container"></div>
        <button class="button" id="add-server-btn">Add Endpoint</button>
        <hr>
        <button class="button button-primary" id="save-platform-btn">Save Platform</button>
        <button class="button" id="close-modal-btn">Cancel</button>
    </div>
    <div id="cgrt-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;"></div>
</div>
