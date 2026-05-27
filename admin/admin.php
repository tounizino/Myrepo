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
        $games = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cl_games LIMIT 50");
        ?>
        <div class="wrap">
            <h1>Games Management</h1>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Release Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($games as $game): ?>
                        <tr>
                            <td><?php echo $game->id; ?></td>
                            <td><?php echo esc_html($game->name); ?></td>
                            <td><?php echo esc_html($game->slug); ?></td>
                            <td><?php echo $game->release_date; ?></td>
                            <td>
                                <a href="#">Edit</a> | <a href="#" style="color:red">Delete</a>
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

    private static function get_count($table) {
        global $wpdb;
        return $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}$table");
    }
}
