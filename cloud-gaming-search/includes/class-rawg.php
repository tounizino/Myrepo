<?php
/**
 * CGS RAWG API Integration
 * 
 * Handles all communication with the RAWG Video Games Database API.
 * Includes caching, rate-limit protection, and response normalization.
 */

defined('ABSPATH') || exit;

class CGS_RAWG {

    const BASE_URL = 'https://api.rawg.io/api/';
    const RATE_LIMIT = 1; // seconds between requests

    private $api_key;
    private $cache;
    private $last_request_time = 0;

    public function __construct($cache) {
        $this->cache = $cache;
        $this->api_key = get_option('cgs_rawg_api_key', '');
    }

    /**
     * Search games by query string.
     */
    public function search($query, $page = 1, $page_size = 20) {
        if (empty(trim($query))) {
            return [];
        }

        $cache_key = $this->cache->make_key(['search', $query, $page, $page_size]);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $params = [
            'key' => $this->api_key,
            'search' => $query,
            'page' => (int) $page,
            'page_size' => (int) $page_size,
        ];

        $data = $this->request('games', $params);
        if (is_wp_error($data) || !isset($data['results'])) {
            return [];
        }

        $results = $this->normalize_games($data['results'], $data);
        $this->cache->set($cache_key, $results);

        return $results;
    }

    /**
     * Get detailed game information.
     */
    public function get_game($game_id) {
        $cache_key = $this->cache->make_key(['game', $game_id]);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $data = $this->request("games/{$game_id}", ['key' => $this->api_key]);
        if (is_wp_error($data) || empty($data)) {
            return null;
        }

        $normalized = $this->normalize_single_game($data);
        $this->cache->set($cache_key, $normalized);

        return $normalized;
    }

    /**
     * Get popular games for Top 100.
     */
    public function get_popular_games($page = 1, $page_size = 40) {
        $cache_key = $this->cache->make_key(['popular', $page, $page_size]);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $params = [
            'key' => $this->api_key,
            'ordering' => '-added',
            'page' => (int) $page,
            'page_size' => (int) $page_size,
            'metacritic' => '50,100',
        ];

        $data = $this->request('games', $params);
        if (is_wp_error($data) || !isset($data['results'])) {
            return [];
        }

        $results = $this->normalize_games($data['results'], $data);
        $this->cache->set($cache_key, $results);

        return $results;
    }

    /**
     * Perform API request with rate limiting.
     */
    private function request($endpoint, $params = []) {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'RAWG API key not configured.');
        }

        // Rate limiting protection
        $now = microtime(true);
        $elapsed = $now - $this->last_request_time;
        if ($elapsed < self::RATE_LIMIT) {
            usleep((self::RATE_LIMIT - $elapsed) * 1000000);
        }
        $this->last_request_time = microtime(true);

        $url = add_query_arg($params, self::BASE_URL . $endpoint);

        $response = wp_remote_get($url, [
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'CloudGamingSearch/1.0',
                'Accept' => 'application/json',
            ],
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if (429 === $code) {
            // Rate limited - wait and retry once
            sleep(2);
            $response = wp_remote_get($url, ['timeout' => 15]);
            if (is_wp_error($response)) {
                return $response;
            }
            $body = wp_remote_retrieve_body($response);
        }

        if (200 !== $code) {
            return new WP_Error('api_error', "RAWG API returned {$code}: " . substr($body, 0, 200));
        }

        return json_decode($body, true) ?: [];
    }

    /**
     * Normalize game list data.
     */
    private function normalize_games($games, $meta = []) {
        $normalized = [];
        foreach ($games as $game) {
            $normalized[] = [
                'id' => $game['id'] ?? 0,
                'slug' => $game['slug'] ?? '',
                'title' => $game['name'] ?? 'Unknown',
                'release_year' => $this->extract_year($game['released'] ?? ''),
                'cover' => $this->get_cover($game),
                'rating' => $game['rating'] ?? 0,
                'genres' => array_map(function($g) { return $g['name']; }, $game['genres'] ?? []),
                'platforms' => array_map(function($p) { return $p['platform']['name'] ?? ''; }, $game['platforms'] ?? []),
                'metacritic' => $game['metacritic'] ?? null,
            ];
        }

        return [
            'games' => $normalized,
            'total' => $meta['count'] ?? count($normalized),
            'page' => $meta['page'] ?? 1,
            'pages' => ceil(($meta['count'] ?? count($normalized)) / ($meta['page_size'] ?? 20)),
        ];
    }

    /**
     * Normalize single game detail.
     */
    private function normalize_single_game($game) {
        return [
            'id' => $game['id'] ?? 0,
            'slug' => $game['slug'] ?? '',
            'title' => $game['name_original'] ?? $game['name'] ?? 'Unknown',
            'description' => $game['description_raw'] ?? '',
            'release_year' => $this->extract_year($game['released'] ?? ''),
            'release_date' => $game['released'] ?? '',
            'cover' => $this->get_cover($game),
            'background' => $game['background_image'] ?? '',
            'background_additional' => $game['background_image_additional'] ?? '',
            'rating' => $game['rating'] ?? 0,
            'rating_top' => $game['rating_top'] ?? 5,
            'ratings_count' => $game['ratings_count'] ?? 0,
            'metacritic' => $game['metacritic'] ?? null,
            'genres' => array_map(function($g) { return $g['name']; }, $game['genres'] ?? []),
            'platforms' => array_map(function($p) { return $p['platform']['name'] ?? ''; }, $game['platforms'] ?? []),
            'developers' => array_map(function($d) { return $d['name']; }, $game['developers'] ?? []),
            'publishers' => array_map(function($p) { return $p['name']; }, $game['publishers'] ?? []),
            'tags' => array_map(function($t) { return $t['name']; }, $game['tags'] ?? []),
            'esrb_rating' => $game['esrb_rating']['name'] ?? null,
            'clip' => $game['clip']['clip'] ?? '',
            'stores' => array_map(function($s) { 
                return [
                    'name' => $s['store']['name'] ?? '',
                    'url' => $s['url'] ?? '',
                ];
            }, $game['stores'] ?? []),
            'screenshots' => array_map(function($s) { return $s['image']; }, $game['screenshots'] ?? []),
            'website' => $game['website'] ?? '',
        ];
    }

    /**
     * Extract year from date string.
     */
    private function extract_year($date) {
        if (empty($date)) {
            return null;
        }
        $parts = explode('-', $date);
        return (int) ($parts[0] ?? null);
    }

    /**
     * Get best available cover image.
     */
    private function get_cover($game) {
        $candidates = [
            $game['background_image'] ?? '',
        ];

        // Try to get smaller optimized versions
        foreach ($candidates as $url) {
            if (!empty($url)) {
                return $url;
            }
        }

        return '';
    }

    /**
     * Check if API key is configured.
     */
    public function has_api_key() {
        return !empty($this->api_key);
    }
}