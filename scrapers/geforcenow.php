<?php
namespace CloudLoadout\Scrapers;

class GeForceNow extends BaseScraper {
    public function __construct() {
        parent::__construct('geforce-now');
    }

    public function scrape() {
        $url = 'https://static.nvidiagrid.net/supported-public-game-list/gfnpc.json?JSON';
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $games = json_decode($body);

        if (!$games) {
            return false;
        }

        $count = 0;
        foreach ($games as $game) {
            if ($this->process_game($game->title)) {
                $count++;
            }
        }

        return $count;
    }
}
