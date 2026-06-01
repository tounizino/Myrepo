<?php
/**
 * CGS Cloud Availability Intelligence Engine
 *
 * Combines official platform data, scraping adapters, and fallback logic
 * to determine cloud gaming availability for any title.
 *
 * OUTPUT FORMAT (strict):
 * {
 *   "game": "string",
 *   "platforms": {
 *     "geforce_now": "available|not_available|unknown",
 *     "xbox_cloud": "available|not_available|unknown",
 *     "ps_plus_cloud": "available|not_available|unknown",
 *     "amazon_luna": "available|not_available|unknown",
 *     "boosteroid": "available|not_available|unknown",
 *     "airgpu": "always available",
 *     "shadow_pc": "always available"
 *   },
 *   "confidence": 0-100
 * }
 */

defined('ABSPATH') || exit;

class CGS_Cloud_Engine {

    private $cache;
    private $adapters;
    private $enabled_platforms;

    public function __construct($cache) {
        $this->cache = $cache;
        $this->adapters = new CGS_Scraper_Adapters($cache);
        $this->enabled_platforms = get_option('cgs_enabled_platforms', [
            'geforce_now' => true,
            'xbox_cloud' => true,
            'ps_plus_cloud' => true,
            'amazon_luna' => true,
            'boosteroid' => true,
            'shadow_pc' => true,
        ]);
    }

    /**
     * Get cloud platform availability for a game.
     */
    public function get_availability($game_title, $game_platforms = []) {
        $cache_key = $this->cache->make_key(['availability', $game_title]);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $platforms = [];

        // Check each enabled platform
        if ($this->enabled_platforms['geforce_now']) {
            $platforms['geforce_now'] = $this->adapters->check_geforce_now($game_title);
        } else {
            $platforms['geforce_now'] = 'unknown';
        }

        if ($this->enabled_platforms['xbox_cloud']) {
            $platforms['xbox_cloud'] = $this->adapters->check_xbox_cloud($game_title);
        } else {
            $platforms['xbox_cloud'] = 'unknown';
        }

        if ($this->enabled_platforms['ps_plus_cloud']) {
            $platforms['ps_plus_cloud'] = $this->adapters->check_ps_plus_cloud($game_title);
        } else {
            $platforms['ps_plus_cloud'] = 'unknown';
        }

        if ($this->enabled_platforms['amazon_luna']) {
            $platforms['amazon_luna'] = $this->adapters->check_amazon_luna($game_title);
        } else {
            $platforms['amazon_luna'] = 'unknown';
        }

        if ($this->enabled_platforms['boosteroid']) {
            $platforms['boosteroid'] = $this->adapters->check_boosteroid($game_title);
        } else {
            $platforms['boosteroid'] = 'unknown';
        }

        // AirGPU and Shadow PC are always available (virtual Windows machines)
        $platforms['airgpu'] = 'always available';
        if ($this->enabled_platforms['shadow_pc']) {
            $platforms['shadow_pc'] = 'always available';
        } else {
            $platforms['shadow_pc'] = 'unknown';
        }

        // Calculate confidence based on how many platforms returned definitive answers
        $confidence = $this->calculate_confidence($platforms);

        $result = [
            'game' => $game_title,
            'platforms' => $platforms,
            'confidence' => $confidence,
        ];

        // Cache for 6 hours (platform data doesn't change often)
        $this->cache->set($cache_key, $result, 6 * HOUR_IN_SECONDS);

        return $result;
    }

    /**
     * Get availability for multiple games (batch).
     */
    public function get_bulk_availability($games) {
        $results = [];
        foreach ($games as $game) {
            $title = is_string($game) ? $game : ($game['title'] ?? '');
            $platforms = is_array($game) ? ($game['platforms'] ?? []) : [];
            if (!empty($title)) {
                $results[$title] = $this->get_availability($title, $platforms);
            }
        }
        return $results;
    }

    /**
     * Calculate confidence score.
     */
    private function calculate_confidence($platforms) {
        $known = 0;
        $total = 0;

        foreach ($platforms as $key => $status) {
            if (in_array($key, ['airgpu', 'shadow_pc'])) {
                continue; // Always available, no confidence impact
            }
            $total++;
            if ('available' === $status || 'not_available' === $status) {
                $known++;
            }
        }

        if (0 === $total) {
            return 0;
        }

        return (int) round(($known / $total) * 100);
    }

    /**
     * Get list of supported platforms.
     */
    public function get_supported_platforms() {
        return [
            'geforce_now' => [
                'label' => 'GeForce NOW',
                'icon' => 'geforce-now',
                'url' => 'https://play.geforcenow.com/',
                'enabled' => $this->enabled_platforms['geforce_now'],
            ],
            'xbox_cloud' => [
                'label' => 'Xbox Cloud Gaming',
                'icon' => 'xbox-cloud',
                'url' => 'https://xbox.com/cloud-gaming',
                'enabled' => $this->enabled_platforms['xbox_cloud'],
            ],
            'ps_plus_cloud' => [
                'label' => 'PlayStation Plus',
                'icon' => 'ps-plus',
                'url' => 'https://playstation.com/ps-plus',
                'enabled' => $this->enabled_platforms['ps_plus_cloud'],
            ],
            'amazon_luna' => [
                'label' => 'Amazon Luna',
                'icon' => 'amazon-luna',
                'url' => 'https://luna.amazon.com/',
                'enabled' => $this->enabled_platforms['amazon_luna'],
            ],
            'boosteroid' => [
                'label' => 'Boosteroid',
                'icon' => 'boosteroid',
                'url' => 'https://boosteroid.com/',
                'enabled' => $this->enabled_platforms['boosteroid'],
            ],
            'airgpu' => [
                'label' => 'AirGPU',
                'icon' => 'airgpu',
                'url' => 'https://airgpu.com/',
                'enabled' => true,
            ],
            'shadow_pc' => [
                'label' => 'Shadow PC',
                'icon' => 'shadow-pc',
                'url' => 'https://shadow.tech/',
                'enabled' => $this->enabled_platforms['shadow_pc'],
            ],
        ];
    }
}