<?php
namespace CloudLoadout;

class Ingestion {
    private $api_key;
    private $base_url = 'https://api.rawg.io/api/';

    public function __construct($api_key = null) {
        $this->api_key = $api_key ?: get_option('cl_rawg_api_key');
    }

    public function fetch_game_details($slug) {
        $url = "{$this->base_url}games/{$slug}?key={$this->api_key}";
        $response = wp_remote_get($url);

        if (is_wp_error($response)) return null;

        $data = json_decode(wp_remote_retrieve_body($response), true);
        return $data;
    }

    public function import_game($rawg_data) {
        global $wpdb;
        
        $game_data = [
            'external_id' => $rawg_data['id'],
            'name' => $rawg_data['name'],
            'slug' => $rawg_data['slug'],
            'description' => $rawg_data['description_raw'] ?? $rawg_data['description'],
            'release_date' => $rawg_data['released'],
            'rating' => $rawg_data['rating'],
            'cover_url' => $rawg_data['background_image'],
            'background_url' => $rawg_data['background_image_additional'] ?? $rawg_data['background_image'],
            'developer' => isset($rawg_data['developers'][0]) ? $rawg_data['developers'][0]['name'] : '',
            'publisher' => isset($rawg_data['publishers'][0]) ? $rawg_data['publishers'][0]['name'] : '',
            'genres' => implode(',', array_column($rawg_data['genres'], 'name')),
        ];

        $wpdb->replace($wpdb->prefix . 'cl_games', $game_data);
        $game_id = $wpdb->insert_id;

        // Import screenshots
        if (isset($rawg_data['short_screenshots'])) {
            foreach ($rawg_data['short_screenshots'] as $ss) {
                $wpdb->insert($wpdb->prefix . 'cl_screenshots', [
                    'game_id' => $game_id,
                    'image_url' => $ss['image']
                ]);
            }
        }

        return $game_id;
    }
}
