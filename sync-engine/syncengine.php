<?php
namespace CloudLoadout;

if (!defined('ABSPATH')) {
    exit;
}

class SyncEngine {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('cl_daily_sync', [$this, 'run_sync']);
        if (!wp_next_scheduled('cl_daily_sync')) {
            wp_schedule_event(time(), 'daily', 'cl_daily_sync');
        }
    }

    public function run_sync() {
        $this->log_sync('global', 'started', 'Daily sync started');
        
        // In reality, this would trigger different scrapers
        // For this demo, we'll implement a structure that can be expanded
        $this->sync_providers();
        
        $this->log_sync('global', 'completed', 'Daily sync completed');
    }

    private function sync_providers() {
        // GFN
        $gfn = new \CloudLoadout\Scrapers\GeForceNow();
        $gfn->scrape();

        // Boosteroid
        $boosteroid = new \CloudLoadout\Scrapers\Boosteroid();
        $boosteroid->scrape();
    }

    public function log_sync($provider_id, $status, $message) {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'cl_sync_logs', [
            'provider_id' => $provider_id === 'global' ? NULL : $provider_id,
            'status' => $status,
            'message' => $message
        ]);
    }

    public function update_game_compatibility($game_id, $provider_id, $status, $source_url = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'cl_game_provider';
        
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE game_id = %d AND provider_id = %d",
            $game_id, $provider_id
        ));

        if ($existing) {
            $wpdb->update($table, [
                'status' => $status,
                'last_checked' => current_time('mysql'),
                'source_url' => $source_url
            ], ['id' => $existing]);
        } else {
            $wpdb->insert($table, [
                'game_id' => $game_id,
                'provider_id' => $provider_id,
                'status' => $status,
                'last_checked' => current_time('mysql'),
                'source_url' => $source_url
            ]);
        }
    }
}
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
/home/engine/.bashrc: line 1: syntax error near unexpected token `('
/home/engine/.bashrc: line 1: `. /etc/profile.d/workload-containment.shn# ~/.bashrc: executed by bash(1) for non-login shells.'
