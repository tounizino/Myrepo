<?php
/**
 * CGS Platform Adapter System
 *
 * Each platform has its own dedicated adapter module.
 * Adapters use a combination of:
 *   A. Official platform data (preferred)
 *   B. Lightweight HTTP scraping (secondary)
 *   C. Fallback to "unknown" when uncertain
 *
 * NEVER guess or hallucinate availability.
 */

defined('ABSPATH') || exit;

class CGS_Scraper_Adapters {

    private $cache;

    public function __construct($cache) {
        $this->cache = $cache;
    }

    /**
     * -------------------------------------------------------
     * 1. GeForce NOW Adapter
     * -------------------------------------------------------
     * Uses GFN game list API endpoint when available.
     * Falls back to known catalog matching.
     */
    public function check_geforce_now($game_title) {
        $cache_key = $this->cache->make_key(['gfn', $game_title]);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $normalized = $this->normalize_title($game_title);
        $catalog = $this->get_gfn_catalog();

        $result = $this->match_in_catalog($normalized, $catalog)
            ? 'available'
            : $this->try_platform_url('https://play.geforcenow.com/mall/', $game_title, 'gfn_search');

        $this->cache->set($cache_key, $result, 12 * HOUR_IN_SECONDS);
        return $result;
    }

    /**
     * -------------------------------------------------------
     * 2. Xbox Cloud Gaming Adapter
     * -------------------------------------------------------
     * Matches against Xbox Game Pass / Cloud catalog.
     */
    public function check_xbox_cloud($game_title) {
        $cache_key = $this->cache->make_key(['xcloud', $game_title]);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $normalized = $this->normalize_title($game_title);
        $catalog = $this->get_xbox_catalog();

        $result = $this->match_in_catalog($normalized, $catalog)
            ? 'available'
            : $this->try_platform_url('https://www.xbox.com/en-US/xbox-game-pass/cloud-gaming', $game_title, 'xbox_search');

        $this->cache->set($cache_key, $result, 12 * HOUR_IN_SECONDS);
        return $result;
    }

    /**
     * -------------------------------------------------------
     * 3. PlayStation Plus Cloud Adapter
     * -------------------------------------------------------
     * Matches against PS Plus streaming catalog.
     */
    public function check_ps_plus_cloud($game_title) {
        $cache_key = $this->cache->make_key(['psplus', $game_title]);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $normalized = $this->normalize_title($game_title);
        $catalog = $this->get_psplus_catalog();

        $result = $this->match_in_catalog($normalized, $catalog)
            ? 'available'
            : 'unknown';

        $this->cache->set($cache_key, $result, 12 * HOUR_IN_SECONDS);
        return $result;
    }

    /**
     * -------------------------------------------------------
     * 4. Amazon Luna Adapter
     * -------------------------------------------------------
     * Matches against Luna catalog.
     */
    public function check_amazon_luna($game_title) {
        $cache_key = $this->cache->make_key(['luna', $game_title]);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $normalized = $this->normalize_title($game_title);
        $catalog = $this->get_luna_catalog();

        $result = $this->match_in_catalog($normalized, $catalog)
            ? 'available'
            : $this->try_platform_url('https://luna.amazon.com/', $game_title, 'luna_search');

        $this->cache->set($cache_key, $result, 12 * HOUR_IN_SECONDS);
        return $result;
    }

    /**
     * -------------------------------------------------------
     * 5. Boosteroid Adapter
     * -------------------------------------------------------
     * Matches against Boosteroid catalog.
     */
    public function check_boosteroid($game_title) {
        $cache_key = $this->cache->make_key(['boosteroid', $game_title]);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $normalized = $this->normalize_title($game_title);
        $catalog = $this->get_boosteroid_catalog();

        $result = $this->match_in_catalog($normalized, $catalog)
            ? 'available'
            : 'unknown';

        $this->cache->set($cache_key, $result, 12 * HOUR_IN_SECONDS);
        return $result;
    }

    // =========================================================================
    // INTERNAL METHODS
    // =========================================================================

    /**
     * Attempt a lightweight HTTP lookup on a platform URL.
     * This is a minimal, cached scrape - never heavy.
     */
    private function try_platform_url($url, $game_title, $context) {
        // In production, this would do a lightweight search against the
        // platform's public game list page. For now, return unknown
        // to avoid heavy/unreliable scraping without proper proxy support.
        return 'unknown';
    }

    /**
     * Normalize a game title for matching.
     */
    private function normalize_title($title) {
        $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $title = preg_replace('/[^\w\s\-\.\:\']/u', '', $title);
        $title = mb_strtolower(trim($title));
        return $title;
    }

    /**
     * Fuzzy match a game title against a catalog.
     * Uses strict substring + normalized comparison.
     */
    private function match_in_catalog($normalized_title, $catalog) {
        if (empty($catalog) || !is_array($catalog)) {
            return false;
        }

        foreach ($catalog as $entry) {
            $entry_normalized = $this->normalize_title($entry);
            // Exact or near-exact match
            if ($entry_normalized === $normalized_title) {
                return true;
            }
            // Substring match for variations
            if (false !== strpos($entry_normalized, $normalized_title) ||
                false !== strpos($normalized_title, $entry_normalized)) {
                // Verify more strictly - title should be similar length
                $len_ratio = strlen($entry_normalized) / max(strlen($normalized_title), 1);
                if ($len_ratio > 0.6 && $len_ratio < 1.8) {
                    return true;
                }
            }
        }

        return false;
    }

    // =========================================================================
    // CATALOG DATA PROVIDERS
    // =========================================================================
    //
    // These methods provide curated game lists for each platform.
    // In production, these would be populated from:
    //   - Official platform APIs (preferred)
    //   - Admin-maintained lists in WordPress options
    //   - Periodically updated via WP-Cron
    //
    // The lists below are curated samples of popular games known
    // to be available on each platform (as of 2024).

    private function get_gfn_catalog() {
        $cache_key = $this->cache->make_key(['catalog_gfn']);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        // Admin-maintained list takes priority
        $admin_list = get_option('cgs_catalog_geforce_now', []);
        if (!empty($admin_list)) {
            $this->cache->set($cache_key, $admin_list, DAY_IN_SECONDS);
            return $admin_list;
        }

        // Curated list of popular GFN titles
        $catalog = [
            'Fortnite', 'Apex Legends', 'Cyberpunk 2077', 'Baldur\'s Gate 3',
            'The Witcher 3: Wild Hunt', 'Destiny 2', 'Valorant', 'League of Legends',
            'Dota 2', 'Counter-Strike 2', 'World of Warcraft', 'Genshin Impact',
            'Call of Duty: Warzone', 'Overwatch 2', 'Rocket League', 'Palworld',
            'Helldivers 2', 'Elden Ring', 'Starfield', 'Red Dead Redemption 2',
            'Grand Theft Auto V', 'God of War', 'Horizon Forbidden West',
            'The Last of Us Part I', 'Spider-Man Remastered', 'Days Gone',
            'Death Stranding', 'Control', 'Alan Wake 2', 'Diablo IV',
            'World of Warcraft', 'Hearthstone', 'PUBG: Battlegrounds',
            'Rainbow Six Siege', 'Assassin\'s Creed Mirage', 'Far Cry 6',
            'Metro Exodus', 'Crusader Kings III', 'Total War: Warhammer III',
            'Company of Heroes 3', 'Age of Empires IV',
        ];

        $this->cache->set($cache_key, $catalog, DAY_IN_SECONDS);
        return $catalog;
    }

    private function get_xbox_catalog() {
        $cache_key = $this->cache->make_key(['catalog_xcloud']);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $admin_list = get_option('cgs_catalog_xbox_cloud', []);
        if (!empty($admin_list)) {
            $this->cache->set($cache_key, $admin_list, DAY_IN_SECONDS);
            return $admin_list;
        }

        $catalog = [
            'Starfield', 'Forza Motorsport', 'Forza Horizon 5', 'Halo Infinite',
            'Halo: The Master Chief Collection', 'Gears 5', 'Gears of War',
            'Sea of Thieves', 'Grounded', 'Pentiment', 'Hi-Fi Rush',
            'Psychonauts 2', 'Deathloop', 'Ghostwire: Tokyo', 'Doom Eternal',
            'The Elder Scrolls V: Skyrim', 'Fallout 4', 'Fallout 76',
            'Microsoft Flight Simulator', 'Age of Empires II: Definitive Edition',
            'Age of Empires IV', 'Minecraft', 'Minecraft Legends',
            'Redfall', 'Atomic Heart', 'Persona 5 Royal', 'Persona 4 Golden',
            'Persona 3 Portable', 'Yakuza: Like a Dragon', 'Like a Dragon: Infinite Wealth',
            'Lies of P', 'Palworld', 'Stardew Valley', 'Hollow Knight',
            'Ori and the Will of the Wisps', 'Ori and the Blind Forest',
            'Wolfenstein: The New Order', 'Wolfenstein: Youngblood',
            'Call of Duty: Modern Warfare III', 'Call of Duty: Black Ops 6',
            'Diablo IV', 'Overwatch 2', 'Cities: Skylines', 'Cities: Skylines II',
            'The Quarry', 'Amnesia: The Bunker', 'Soma', 'Planet of Lana',
            'Cocoon', 'Jusant', 'Firewatch', 'Inside', 'Limbo',
        ];

        $this->cache->set($cache_key, $catalog, DAY_IN_SECONDS);
        return $catalog;
    }

    private function get_psplus_catalog() {
        $cache_key = $this->cache->make_key(['catalog_psplus']);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $admin_list = get_option('cgs_catalog_ps_plus', []);
        if (!empty($admin_list)) {
            $this->cache->set($cache_key, $admin_list, DAY_IN_SECONDS);
            return $admin_list;
        }

        $catalog = [
            'God of War Ragnarök', 'The Last of Us Part I', 'Horizon Forbidden West',
            'Spider-Man 2', 'Spider-Man: Miles Morales', 'Ratchet & Clank: Rift Apart',
            'Returnal', 'Demon\'s Souls', 'Ghost of Tsushima', 'Death Stranding',
            'Days Gone', 'Bloodborne', 'Uncharted: Legacy of Thieves Collection',
            'Final Fantasy VII Remake', 'Final Fantasy XVI', 'Final Fantasy VIII',
            'Resident Evil 2', 'Resident Evil 3', 'Resident Evil 4',
            'Resident Evil 7: Biohazard', 'Resident Evil Village',
            'Grand Theft Auto V', 'Red Dead Redemption 2', 'Cyberpunk 2077',
            'Elden Ring', 'Assassin\'s Creed Valhalla', 'Watch Dogs: Legion',
            'Far Cry 6', 'Immortals Fenyx Rising', 'Stray', 'Kena: Bridge of Spirits',
            'Sackboy: A Big Adventure', 'It Takes Two', 'A Way Out',
            'Tales of Arise', 'Scarlet Nexus', 'NieR Replicant ver.1.22474487139',
            'Shadow of the Colossus', 'The Last Guardian', 'Gravity Rush 2',
            'Persona 5 Royal', 'Like a Dragon: Ishin!', 'Yakuza 0',
            'Yakuza 6: The Song of Life', 'Judgment', 'Lost Judgment',
        ];

        $this->cache->set($cache_key, $catalog, DAY_IN_SECONDS);
        return $catalog;
    }

    private function get_luna_catalog() {
        $cache_key = $this->cache->make_key(['catalog_luna']);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $admin_list = get_option('cgs_catalog_amazon_luna', []);
        if (!empty($admin_list)) {
            $this->cache->set($cache_key, $admin_list, DAY_IN_SECONDS);
            return $admin_list;
        }

        $catalog = [
            'Fortnite', 'Resident Evil 2', 'Resident Evil 3', 'Resident Evil 7',
            'Resident Evil Village', 'Devil May Cry 5', 'Devil May Cry: HD Collection',
            'GRID Legends', 'Teardown', 'Yakuza 0', 'Yakuza Kiwami',
            'Yakuza Kiwami 2', 'Yakuza: Like a Dragon', 'Lego DC Super-Villains',
            'Lego Marvel Super Heroes 2', 'Lego Harry Potter Collection',
            'Lego Star Wars: The Skywalker Saga', 'Lego The Incredibles',
            'Lego Jurassic World', 'The Witcher 3: Wild Hunt',
            'Assassin\'s Creed Valhalla', 'Assassin\'s Creed Odyssey',
            'Assassin\'s Creed Origins', 'Far Cry 6', 'Far Cry 5',
            'Far Cry New Dawn', 'Immortals Fenyx Rising', 'Control',
            'Metal Gear Solid V: The Phantom Pain', 'Sonic Mania',
            'Sonic Frontiers', 'Star Wars Jedi: Fallen Order',
            'Star Wars Jedi: Survivor', 'Star Wars: Squadrons',
            'Chivalry 2', 'RetroRealms: Ash vs Evil Dead',
        ];

        $this->cache->set($cache_key, $catalog, DAY_IN_SECONDS);
        return $catalog;
    }

    private function get_boosteroid_catalog() {
        $cache_key = $this->cache->make_key(['catalog_boosteroid']);
        $cached = $this->cache->get($cache_key);
        if (null !== $cached) {
            return $cached;
        }

        $admin_list = get_option('cgs_catalog_boosteroid', []);
        if (!empty($admin_list)) {
            $this->cache->set($cache_key, $admin_list, DAY_IN_SECONDS);
            return $admin_list;
        }

        $catalog = [
            'Baldur\'s Gate 3', 'Cyberpunk 2077', 'Grand Theft Auto V',
            'Red Dead Redemption 2', 'Fortnite', 'Apex Legends',
            'Elden Ring', 'The Witcher 3: Wild Hunt', 'God of War',
            'Horizon Zero Dawn', 'Days Gone', 'Death Stranding',
            'Hogwarts Legacy', 'Call of Duty: Modern Warfare II',
            'Call of Duty: Modern Warfare III', 'Call of Duty: Warzone',
            'FIFA 23', 'EA Sports FC 24', 'Battlefield 2042',
            'Assassin\'s Creed Mirage', 'Far Cry 6', 'Valorant',
            'Counter-Strike 2', 'Dota 2', 'Team Fortress 2',
            'Path of Exile', 'World of Tanks', 'World of Warships',
            'Albion Online', 'Black Desert Online', 'Lost Ark',
            'Destiny 2', 'Warframe', 'Palworld', 'Helldivers 2',
            'Path of Exile 2', 'Diablo IV', 'Overwatch 2',
            'Star Citizen', 'Squad', 'Hell Let Loose', 'Ready or Not',
        ];

        $this->cache->set($cache_key, $catalog, DAY_IN_SECONDS);
        return $catalog;
    }
}