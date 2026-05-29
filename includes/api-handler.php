<?php
class CGE_API_Handler {
    private $rawg_api_key;

    public function __construct() {
        $options = get_option('cge_settings');
        $this->rawg_api_key = isset($options['rawg_api_key']) ? $options['rawg_api_key'] : '';
    }

    public function search_games($query, $platform_filter = '', $page = 1) {
        if (empty($this->rawg_api_key)) {
            return array('error' => 'Missing RAWG API Key. Please add it in settings.');
        }

        $url = "https://api.rawg.io/api/games?key={$this->rawg_api_key}&search=" . urlencode($query) . "&page={$page}&page_size=20";
        
        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return array('error' => 'RAWG API Request Failed');
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        $results = array();

        if (isset($data['results'])) {
            foreach ($data['results'] as $game) {
                $availability = $this->get_cloud_availability($game['name']);
                
                // Filter by platform if requested
                if ($platform_filter) {
                    $found = false;
                    foreach ($availability as $plat) {
                        if ($plat['platform'] === $platform_filter && $plat['available']) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) continue;
                }

                $results[] = array(
                    'id' => $game['id'],
                    'slug' => $game['slug'],
                    'name' => $game['name'],
                    'image' => $game['background_image'],
                    'rating' => $game['rating'],
                    'released' => $game['released'],
                    'platforms' => $game['platforms'],
                    'cloud_availability' => $availability,
                );
            }
        }

        return $results;
    }

    private function get_cloud_availability($game_name) {
        // In a production environment, this would be a lookup in a local database
        // populated by scrapers or official APIs.
        // For this implementation, I will simulate the lookup.
        // Real cloud availability is dynamic and complex.
        
        $name = strtolower($game_name);
        $platforms = array(
            'GeForce NOW' => array('url' => 'https://www.nvidia.com/en-us/geforce-now/games/', 'icon' => 'nvidia'),
            'Xbox Cloud Gaming' => array('url' => 'https://www.xbox.com/play', 'icon' => 'xbox'),
            'Boosteroid' => array('url' => 'https://boosteroid.com/go/games', 'icon' => 'boosteroid'),
            'PlayStation Plus' => array('url' => 'https://www.playstation.com/ps-plus/games/', 'icon' => 'playstation'),
            'Amazon Luna' => array('url' => 'https://luna.amazon.com/', 'icon' => 'luna'),
            'Shadow PC' => array('url' => 'https://shadow.tech/', 'icon' => 'shadow'),
            'Blacknut' => array('url' => 'https://www.blacknut.com/en/games', 'icon' => 'blacknut'),
        );

        $availability = array();
        foreach ($platforms as $p_name => $p_info) {
            $is_available = $this->is_on_platform($name, $p_name);
            $availability[] = array(
                'platform' => $p_name,
                'available' => $is_available,
                'url' => $p_info['url'],
                'icon' => $p_info['icon']
            );
        }

        return $availability;
    }

    private function is_on_platform($game_name, $platform) {
        static $mapping = null;
        if ($mapping === null) {
            $json = file_get_contents(CGE_PLUGIN_DIR . 'includes/cloud-mapping.json');
            $mapping = json_decode($json, true);
        }

        $game_name = strtolower($game_name);
        
        // Direct match
        if (isset($mapping[$game_name])) {
            return in_array($platform, $mapping[$game_name]);
        }

        // Partial match
        foreach ($mapping as $title => $plats) {
            if (strpos($game_name, $title) !== false || strpos($title, $game_name) !== false) {
                return in_array($platform, $plats);
            }
        }

        // Fallback for Shadow, Air GPU, CloudDeck (BYO Game services)
        if (in_array($platform, array('Shadow PC', 'Air GPU', 'CloudDeck'))) {
            return true;
        }

        return false;
    }
}
