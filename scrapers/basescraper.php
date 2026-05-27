<?php
namespace CloudLoadout\Scrapers;

abstract class BaseScraper {
    protected $provider_id;
    protected $provider_slug;

    public function __construct($slug) {
        global $wpdb;
        $this->provider_slug = $slug;
        $this->provider_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}cl_providers WHERE slug = %s",
            $slug
        ));
    }

    abstract public function scrape();

    protected function process_game($game_name, $source_url = '') {
        $match_engine = \CloudLoadout\MatchEngine::get_instance();
        $sync_engine = \CloudLoadout\SyncEngine::get_instance();

        $game_id = $match_engine->find_match($game_name);

        if ($game_id) {
            $sync_engine->update_game_compatibility($game_id, $this->provider_id, 'supported', $source_url);
            return true;
        }

        // If no match, we might want to log it for manual review or auto-create (with caution)
        return false;
    }
}
