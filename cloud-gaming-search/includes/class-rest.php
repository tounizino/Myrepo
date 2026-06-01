<?php
/**
 * CGS REST API Endpoints
 *
 * Registers and handles all REST API routes for the Cloud Gaming Search Engine.
 *
 * Endpoints:
 *   /search         - RAWG-powered game search
 *   /game           - Full game metadata + cloud availability
 *   /availability   - Cloud platform data only
 *   /top100         - Curated Top 100 cloud games
 *   /platforms      - Supported cloud platforms list
 */

defined('ABSPATH') || exit;

class CGS_REST {

    private $rawg;
    private $cloud_engine;
    private $top100;
    private $cache;
    private $namespace = 'cloud-gaming/v1';

    public function __construct($rawg, $cloud_engine, $top100, $cache) {
        $this->rawg = $rawg;
        $this->cloud_engine = $cloud_engine;
        $this->top100 = $top100;
        $this->cache = $cache;
    }

    /**
     * Register all REST routes.
     */
    public function register_routes() {
        // Search games
        register_rest_route($this->namespace, '/search', [
            'methods' => 'GET',
            'callback' => [$this, 'handle_search'],
            'permission_callback' => '__return_true',
            'args' => [
                'q' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'page' => [
                    'default' => 1,
                    'sanitize_callback' => 'absint',
                ],
                'per_page' => [
                    'default' => 20,
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);

        // Get single game details
        register_rest_route($this->namespace, '/game', [
            'methods' => 'GET',
            'callback' => [$this, 'handle_game'],
            'permission_callback' => '__return_true',
            'args' => [
                'id' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);

        // Get cloud availability for a game
        register_rest_route($this->namespace, '/availability', [
            'methods' => 'GET',
            'callback' => [$this, 'handle_availability'],
            'permission_callback' => '__return_true',
            'args' => [
                'title' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);

        // Get Top 100 cloud games
        register_rest_route($this->namespace, '/top100', [
            'methods' => 'GET',
            'callback' => [$this, 'handle_top100'],
            'permission_callback' => '__return_true',
            'args' => [
                'page' => [
                    'default' => 1,
                    'sanitize_callback' => 'absint',
                ],
                'per_page' => [
                    'default' => 20,
                    'sanitize_callback' => 'absint',
                ],
            ],
        ]);

        // Get supported platforms
        register_rest_route($this->namespace, '/platforms', [
            'methods' => 'GET',
            'callback' => [$this, 'handle_platforms'],
            'permission_callback' => '__return_true',
        ]);

        // Health check
        register_rest_route($this->namespace, '/health', [
            'methods' => 'GET',
            'callback' => [$this, 'handle_health'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Handle search request.
     */
    public function handle_search($request) {
        $query = $request->get_param('q');
        $page = $request->get_param('page');
        $per_page = min(50, $request->get_param('per_page'));

        if (empty(trim($query))) {
            return new WP_REST_Error('empty_query', 'Search query is required.', ['status' => 400]);
        }

        $results = $this->rawg->search($query, $page, $per_page);

        return new WP_REST_Response([
            'success' => true,
            'data' => $results,
            'query' => $query,
        ], 200);
    }

    /**
     * Handle single game request.
     */
    public function handle_game($request) {
        $game_id = $request->get_param('id');

        $game = $this->rawg->get_game($game_id);
        if (null === $game) {
            return new WP_REST_Error('game_not_found', 'Game not found.', ['status' => 404]);
        }

        // Enrich with cloud availability
        $availability = $this->cloud_engine->get_availability($game['title'], $game['platforms']);
        $game['cloud'] = $availability['platforms'];
        $game['cloud_confidence'] = $availability['confidence'];

        return new WP_REST_Response([
            'success' => true,
            'data' => $game,
        ], 200);
    }

    /**
     * Handle availability request.
     */
    public function handle_availability($request) {
        $title = $request->get_param('title');

        if (empty(trim($title))) {
            return new WP_REST_Error('empty_title', 'Game title is required.', ['status' => 400]);
        }

        $availability = $this->cloud_engine->get_availability($title);

        return new WP_REST_Response([
            'success' => true,
            'data' => $availability,
        ], 200);
    }

    /**
     * Handle Top 100 request.
     */
    public function handle_top100($request) {
        $page = $request->get_param('page');
        $per_page = min(50, $request->get_param('per_page'));

        $top100 = $this->top100->get_top100($page, $per_page);

        return new WP_REST_Response([
            'success' => true,
            'data' => $top100,
        ], 200);
    }

    /**
     * Handle platforms list request.
     */
    public function handle_platforms($request) {
        $platforms = $this->cloud_engine->get_supported_platforms();

        return new WP_REST_Response([
            'success' => true,
            'data' => $platforms,
        ], 200);
    }

    /**
     * Health check endpoint.
     */
    public function handle_health($request) {
        return new WP_REST_Response([
            'success' => true,
            'data' => [
                'status' => 'ok',
                'version' => CGS_VERSION,
                'rawg_configured' => $this->rawg->has_api_key(),
                'cache_engine' => 'transients',
                'time' => current_time('mysql'),
            ],
        ], 200);
    }
}