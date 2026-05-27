<?php
namespace CloudLoadout;

if (!defined('ABSPATH')) {
    exit;
}

class MatchEngine {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function normalize_title($title) {
        $title = strtolower($title);
        $title = preg_replace('/[^a-z0-9\s]/', '', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        return trim($title);
    }

    public function find_match($title) {
        global $wpdb;
        $normalized = $this->normalize_title($title);
        $slug = sanitize_title($title);

        // 1. Exact slug match
        $game = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}cl_games WHERE slug = %s",
            $slug
        ));
        if ($game) return $game->id;

        // 2. Alias match
        $alias = $wpdb->get_row($wpdb->prepare(
            "SELECT game_id FROM {$wpdb->prefix}cl_aliases WHERE alias_name = %s",
            $title
        ));
        if ($alias) return $alias->game_id;

        // 3. Normalized title match
        $game = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}cl_games WHERE LOWER(name) = %s",
            strtolower($title)
        ));
        if ($game) return $game->id;

        // 4. Fuzzy match (Simplified for now, can be improved with Levenshtein in PHP if dataset is small enough or via custom SQL function)
        $matches = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}cl_games");
        foreach ($matches as $m) {
            $dist = levenshtein($normalized, $this->normalize_title($m->name));
            $max_len = max(strlen($normalized), strlen($this->normalize_title($m->name)));
            if ($max_len > 0 && ($dist / $max_len) < 0.2) { // 80% similarity
                return $m->id;
            }
        }

        return null;
    }

    public function add_alias($game_id, $alias_name) {
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'cl_aliases', [
            'game_id' => $game_id,
            'alias_name' => $alias_name
        ]);
    }
}
