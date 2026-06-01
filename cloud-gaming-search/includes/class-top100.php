<?php
/**
 * CGS Top 100 Cloud Games System
 *
 * Curates the Top 100 cloud-compatible games using RAWG popularity data
 * cross-referenced with cloud platform availability.
 */

defined('ABSPATH') || exit;

class CGS_Top100 {

    private $rawg;
    private $cloud_engine;
    private $cache;

    public function __construct($rawg, $cloud_engine, $cache) {
        $this->rawg = $rawg;
        $this->cloud_engine = $cloud_engine;
        $this->cache = $cache;
    }

    /**
     * Get curated Top 100 cloud games.
     */
    public function get_top100($page = 1, $per_page = 20) {
        $cache_key = $this->cache->make_key(['top100_full']);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $this->paginate($cached, $page, $per_page);
        }

        $top100 = $this->build_top100();

        // Cache for a full day
        $this->cache->set($cache_key, $top100, DAY_IN_SECONDS);

        return $this->paginate($top100, $page, $per_page);
    }

    /**
     * Build Top 100 list from RAWG popularity + cloud availability.
     */
    private function build_top100() {
        $top100 = [];

        // Fetch popular games from RAWG (multiple pages)
        $popular_games = [];
        for ($page = 1; $page <= 3; $page++) {
            $result = $this->rawg->get_popular_games($page, 40);
            if (!empty($result['games'])) {
                $popular_games = array_merge($popular_games, $result['games']);
            }
        }

        if (empty($popular_games)) {
            // Fallback: return curated list
            return $this->get_fallback_top100();
        }

        // Deduplicate by ID
        $seen = [];
        $unique_games = [];
        foreach ($popular_games as $game) {
            if (!isset($seen[$game['id']])) {
                $seen[$game['id']] = true;
                $unique_games[] = $game;
            }
        }

        // Enrich with cloud availability
        foreach ($unique_games as $game) {
            $availability = $this->cloud_engine->get_availability($game['title'], $game['platforms']);

            $top100[] = [
                'id' => $game['id'],
                'slug' => $game['slug'],
                'title' => $game['title'],
                'cover' => $game['cover'],
                'rating' => $game['rating'],
                'release_year' => $game['release_year'],
                'genres' => $game['genres'],
                'platforms' => $game['platforms'],
                'cloud' => $availability['platforms'],
                'cloud_confidence' => $availability['confidence'],
            ];

            if (count($top100) >= 100) {
                break;
            }
        }

        // Sort by cloud availability count (games on more platforms first)
        usort($top100, function($a, $b) {
            $score_a = $this->count_available($a['cloud']);
            $score_b = $this->count_available($b['cloud']);
            if ($score_a === $score_b) {
                return ($b['rating'] ?? 0) <=> ($a['rating'] ?? 0);
            }
            return $score_b <=> $score_a;
        });

        return array_slice($top100, 0, 100);
    }

    /**
     * Count available platforms.
     */
    private function count_available($cloud) {
        $count = 0;
        foreach ($cloud as $key => $status) {
            if ('available' === $status || 'always available' === $status) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Paginate an array.
     */
    private function paginate($items, $page, $per_page) {
        $page = max(1, (int) $page);
        $per_page = max(1, min(50, (int) $per_page));
        $total = count($items);
        $total_pages = ceil($total / $per_page);
        $offset = ($page - 1) * $per_page;
        $items = array_slice($items, $offset, $per_page);

        return [
            'games' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => $total_pages,
        ];
    }

    /**
     * Fallback curated Top 100 list when API is unavailable.
     */
    private function get_fallback_top100() {
        $curated = [
            ['title' => 'Fortnite', 'cover' => '', 'rating' => 4.0, 'release_year' => 2017, 'genres' => ['Action', 'Battle Royale'], 'platforms' => ['PC', 'PlayStation', 'Xbox', 'Nintendo']],
            ['title' => 'Cyberpunk 2077', 'cover' => '', 'rating' => 4.3, 'release_year' => 2020, 'genres' => ['RPG', 'Action'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'Baldur\'s Gate 3', 'cover' => '', 'rating' => 4.7, 'release_year' => 2023, 'genres' => ['RPG', 'Adventure'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'The Witcher 3: Wild Hunt', 'cover' => '', 'rating' => 4.7, 'release_year' => 2015, 'genres' => ['RPG', 'Action'], 'platforms' => ['PC', 'PlayStation', 'Xbox', 'Nintendo']],
            ['title' => 'Elden Ring', 'cover' => '', 'rating' => 4.6, 'release_year' => 2022, 'genres' => ['RPG', 'Action'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'Starfield', 'cover' => '', 'rating' => 4.2, 'release_year' => 2023, 'genres' => ['RPG', 'Sci-Fi'], 'platforms' => ['PC', 'Xbox']],
            ['title' => 'Red Dead Redemption 2', 'cover' => '', 'rating' => 4.7, 'release_year' => 2018, 'genres' => ['Action', 'Adventure'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'Grand Theft Auto V', 'cover' => '', 'rating' => 4.5, 'release_year' => 2013, 'genres' => ['Action', 'Adventure'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'God of War', 'cover' => '', 'rating' => 4.6, 'release_year' => 2018, 'genres' => ['Action', 'Adventure'], 'platforms' => ['PC', 'PlayStation']],
            ['title' => 'Horizon Forbidden West', 'cover' => '', 'rating' => 4.5, 'release_year' => 2022, 'genres' => ['RPG', 'Action'], 'platforms' => ['PC', 'PlayStation']],
            ['title' => 'Apex Legends', 'cover' => '', 'rating' => 4.1, 'release_year' => 2019, 'genres' => ['Battle Royale', 'Action'], 'platforms' => ['PC', 'PlayStation', 'Xbox', 'Nintendo']],
            ['title' => 'Destiny 2', 'cover' => '', 'rating' => 4.0, 'release_year' => 2017, 'genres' => ['FPS', 'Action'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'Death Stranding', 'cover' => '', 'rating' => 4.3, 'release_year' => 2019, 'genres' => ['Adventure', 'Action'], 'platforms' => ['PC', 'PlayStation']],
            ['title' => 'Resident Evil 4', 'cover' => '', 'rating' => 4.6, 'release_year' => 2023, 'genres' => ['Horror', 'Action'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'Diablo IV', 'cover' => '', 'rating' => 4.2, 'release_year' => 2023, 'genres' => ['RPG', 'Action'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'Halo Infinite', 'cover' => '', 'rating' => 4.0, 'release_year' => 2021, 'genres' => ['FPS', 'Action'], 'platforms' => ['PC', 'Xbox']],
            ['title' => 'Forza Horizon 5', 'cover' => '', 'rating' => 4.5, 'release_year' => 2021, 'genres' => ['Racing', 'Open World'], 'platforms' => ['PC', 'Xbox']],
            ['title' => 'Call of Duty: Warzone', 'cover' => '', 'rating' => 3.9, 'release_year' => 2020, 'genres' => ['Battle Royale', 'FPS'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'Overwatch 2', 'cover' => '', 'rating' => 3.8, 'release_year' => 2022, 'genres' => ['FPS', 'Action'], 'platforms' => ['PC', 'PlayStation', 'Xbox', 'Nintendo']],
            ['title' => 'Valorant', 'cover' => '', 'rating' => 4.0, 'release_year' => 2020, 'genres' => ['FPS', 'Tactical'], 'platforms' => ['PC']],
            ['title' => 'Counter-Strike 2', 'cover' => '', 'rating' => 4.2, 'release_year' => 2023, 'genres' => ['FPS', 'Tactical'], 'platforms' => ['PC']],
            ['title' => 'League of Legends', 'cover' => '', 'rating' => 4.0, 'release_year' => 2009, 'genres' => ['MOBA', 'Strategy'], 'platforms' => ['PC']],
            ['title' => 'Dota 2', 'cover' => '', 'rating' => 4.1, 'release_year' => 2013, 'genres' => ['MOBA', 'Strategy'], 'platforms' => ['PC']],
            ['title' => 'Genshin Impact', 'cover' => '', 'rating' => 4.3, 'release_year' => 2020, 'genres' => ['RPG', 'Action'], 'platforms' => ['PC', 'PlayStation', 'Mobile']],
            ['title' => 'Palworld', 'cover' => '', 'rating' => 3.9, 'release_year' => 2024, 'genres' => ['Survival', 'Action'], 'platforms' => ['PC', 'Xbox']],
            ['title' => 'Helldivers 2', 'cover' => '', 'rating' => 4.4, 'release_year' => 2024, 'genres' => ['Action', 'Co-op'], 'platforms' => ['PC', 'PlayStation']],
            ['title' => 'Lies of P', 'cover' => '', 'rating' => 4.4, 'release_year' => 2023, 'genres' => ['Action', 'RPG'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'Atomic Heart', 'cover' => '', 'rating' => 4.0, 'release_year' => 2023, 'genres' => ['Action', 'FPS'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'Hogwarts Legacy', 'cover' => '', 'rating' => 4.3, 'release_year' => 2023, 'genres' => ['RPG', 'Action'], 'platforms' => ['PC', 'PlayStation', 'Xbox']],
            ['title' => 'Returnal', 'cover' => '', 'rating' => 4.3, 'release_year' => 2021, 'genres' => ['Action', 'Roguelike'], 'platforms' => ['PC', 'PlayStation']],
        ];

        $top100 = [];
        foreach ($curated as $game) {
            $availability = $this->cloud_engine->get_availability($game['title'], $game['platforms']);
            $top100[] = [
                'id' => 0,
                'slug' => sanitize_title($game['title']),
                'title' => $game['title'],
                'cover' => $game['cover'],
                'rating' => $game['rating'],
                'release_year' => $game['release_year'],
                'genres' => $game['genres'],
                'platforms' => $game['platforms'],
                'cloud' => $availability['platforms'],
                'cloud_confidence' => $availability['confidence'],
            ];
        }

        return $top100;
    }
}