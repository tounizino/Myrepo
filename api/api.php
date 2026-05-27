<?php
namespace CloudLoadout;

if (!defined('ABSPATH')) {
    exit;
}

class API {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('cl/v1', '/search', [
            'methods' => 'GET',
            'callback' => [$this, 'search_games'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('cl/v1', '/games/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'get_game'],
            'permission_callback' => '__return_true'
        ]);
    }

    public function search_games(\WP_REST_Request $request) {
        global $wpdb;
        $query = $request->get_param('q');
        if (empty($query)) {
            return new \WP_REST_Response([], 200);
        }

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT id, name, slug, cover_url FROM {$wpdb->prefix}cl_games 
             WHERE name LIKE %s OR slug LIKE %s 
             LIMIT 10",
            '%' . $wpdb->esc_like($query) . '%',
            '%' . $wpdb->esc_like($query) . '%'
        ));

        return new \WP_REST_Response($results, 200);
    }

    public function get_game(\WP_REST_Request $request) {
        global $wpdb;
        $id = $request->get_param('id');
        
        $game = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cl_games WHERE id = %d",
            $id
        ));

        if (!$game) {
            return new \WP_Error('no_game', 'Game not found', ['status' => 404]);
        }

        // Get providers
        $providers = $wpdb->get_results($wpdb->prepare(
            "SELECT p.name, p.slug, gp.status, gp.last_checked, gp.confidence_score 
             FROM {$wpdb->prefix}cl_game_provider gp
             JOIN {$wpdb->prefix}cl_providers p ON gp.provider_id = p.id
             WHERE gp.game_id = %d",
            $id
        ));

        $game->providers = $providers;

        return new \WP_REST_Response($game, 200);
    }
}
