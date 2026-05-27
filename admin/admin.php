<?php
namespace CloudLoadout;

if (!defined('ABSPATH')) {
    exit;
}

class Admin {
    public static function render_dashboard() {
        ?>
        <div class="wrap cl-admin">
            <h1>CloudLoadout Dashboard</h1>
            <div class="cl-stats-grid">
                <div class="cl-stat-card">
                    <h3>Total Games</h3>
                    <p><?php echo self::get_count('cl_games'); ?></p>
                </div>
                <div class="cl-stat-card">
                    <h3>Total Providers</h3>
                    <p><?php echo self::get_count('cl_providers'); ?></p>
                </div>
                <div class="cl-stat-card">
                    <h3>Sync Status</h3>
                    <p>Healthy</p>
                </div>
            </div>
            
            <h2>Quick Actions</h2>
            <button class="button button-primary">Trigger Manual Sync</button>
            <button class="button">Clear Cache</button>
        </div>
        <?php
    }

    public static function render_games() {
        global $wpdb;
        
        // Handle manual override
        if (isset($_POST['cl_override_nonce']) && wp_verify_nonce($_POST['cl_override_nonce'], 'cl_override')) {
            $game_id = intval($_POST['game_id']);
            
            if (isset($_POST['action']) && $_POST['action'] === 'update_meta') {
                $wpdb->update($wpdb->prefix . 'cl_games', [
                    'supported_devices' => sanitize_text_field($_POST['devices']),
                    'controller_support' => sanitize_text_field($_POST['controller'])
                ], ['id' => $game_id]);
                echo '<div class="updated"><p>Meta updated!</p></div>';
            } else {
                $provider_id = intval($_POST['provider_id']);
                $status = sanitize_text_field($_POST['status']);
                
                $sync_engine = \CloudLoadout\SyncEngine::get_instance();
                $sync_engine->update_game_compatibility($game_id, $provider_id, $status, 'manual-override');
                echo '<div class="updated"><p>Compatibility updated successfully!</p></div>';
            }
        }

        $games = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cl_games LIMIT 50");
        $providers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cl_providers");
        ?>
        <div class="wrap">
            <h1>Games Management</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Providers Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($games as $game): ?>
                        <tr>
                            <td><?php echo $game->id; ?></td>
                            <td><strong><?php echo esc_html($game->name); ?></strong></td>
                            <td>
                                <form method="post" style="display:flex; gap:5px; align-items:center;">
                                    <?php wp_nonce_field('cl_override', 'cl_override_nonce'); ?>
                                    <input type="hidden" name="game_id" value="<?php echo $game->id; ?>">
                                    <select name="provider_id" style="font-size:11px;">
                                        <?php foreach ($providers as $p): ?>
                                            <option value="<?php echo $p->id; ?>"><?php echo esc_html($p->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="status" style="font-size:11px;">
                                        <option value="supported">Supported</option>
                                        <option value="unsupported">Unsupported</option>
                                        <option value="playable">Playable</option>
                                    </select>
                                    <button type="submit" class="button button-small">Update</button>
                                </form>
                                <div style="margin-top:10px;">
                                    <form method="post">
                                        <?php wp_nonce_field('cl_override', 'cl_override_nonce'); ?>
                                        <input type="hidden" name="game_id" value="<?php echo $game->id; ?>">
                                        <input type="hidden" name="action" value="update_meta">
                                        <input type="text" name="devices" placeholder="Devices" value="<?php echo esc_attr($game->supported_devices); ?>" style="font-size:10px; width:80px;">
                                        <input type="text" name="controller" placeholder="Controller" value="<?php echo esc_attr($game->controller_support); ?>" style="font-size:10px; width:80px;">
                                        <button type="submit" class="button button-small">Save Meta</button>
                                    </form>
                                </div>
                            </td>
                            <td>
                                <a href="<?php echo home_url('/g/' . $game->slug); ?>" target="_blank">View</a> | 
                                <a href="#" style="color:red">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function render_providers() {
        global $wpdb;
        $providers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cl_providers");
        ?>
        <div class="wrap">
            <h1>Cloud Providers</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($providers as $p): ?>
                        <tr>
                            <td><?php echo esc_html($p->name); ?></td>
                            <td><?php echo esc_html($p->slug); ?></td>
                            <td>Active</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function render_logs() {
        global $wpdb;
        $logs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cl_sync_logs ORDER BY created_at DESC LIMIT 50");
        ?>
        <div class="wrap">
            <h1>Sync Logs</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo $log->created_at; ?></td>
                            <td><?php echo esc_html($log->status); ?></td>
                            <td><?php echo esc_html($log->message); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function render_settings() {
        if (isset($_POST['cl_settings_nonce']) && wp_verify_nonce($_POST['cl_settings_nonce'], 'cl_settings')) {
            update_option('cl_rawg_api_key', sanitize_text_field($_POST['rawg_api_key']));
            echo '<div class="updated"><p>Settings saved!</p></div>';
        }

        $api_key = get_option('cl_rawg_api_key', '');
        ?>
        <div class="wrap">
            <h1>CloudLoadout Settings</h1>
            <form method="post">
                <?php wp_nonce_field('cl_settings', 'cl_settings_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="rawg_api_key">RAWG API Key</label></th>
                        <td>
                            <input name="rawg_api_key" type="text" id="rawg_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text">
                            <p class="description">Get your API key at <a href="https://rawg.io/apidocs" target="_blank">rawg.io/apidocs</a></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    private static function get_count($table) {
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}$table");
    }
}
