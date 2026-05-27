<?php
namespace CloudLoadout\Scrapers;

class Boosteroid extends BaseScraper {
    public function __construct() {
        parent::__construct('boosteroid');
    }

    public function scrape() {
        // Boosteroid often uses a library API or public page.
        // For production, this would use a more robust scraping method or API if available.
        $url = 'https://cloud.boosteroid.com/api/v1/games/list';
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        if (!$data || !isset($data->games)) {
            return false;
        }

        $count = 0;
        foreach ($data->games as $game) {
            if ($this->process_game($game->title)) {
                $count++;
            }
        }

        return $count;
    }
}
