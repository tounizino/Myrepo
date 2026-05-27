<?php
namespace CloudLoadout;

if (!defined('ABSPATH')) {
    exit;
}

class SEO {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('pre_get_document_title', [$this, 'dynamic_title'], 20);
        add_action('wp_head', [$this, 'render_meta_tags'], 5);
        add_action('wp_head', [$this, 'render_schema'], 20);
    }

    public function dynamic_title($title) {
        $game_slug = get_query_var('cl_game');
        if ($game_slug) {
            $game = $this->get_game_by_slug($game_slug);
            if ($game) {
                return "Can you play {$game->name} on Cloud Gaming? | CloudLoadout";
            }
        }
        return $title;
    }

    public function render_meta_tags() {
        $game_slug = get_query_var('cl_game');
        if (!$game_slug) return;

        $game = $this->get_game_by_slug($game_slug);
        if (!$game) return;

        $description = "Check compatibility for {$game->name} on GeForce NOW, Boosteroid, Xbox Cloud Gaming, and more. Find where to play {$game->name} in the cloud.";
        
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($game->name) . ' Cloud Gaming Compatibility">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        if ($game->cover_url) {
            echo '<meta property="og:image" content="' . esc_url($game->cover_url) . '">' . "\n";
        }
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    }

    public function render_schema() {
        $game_slug = get_query_var('cl_game');
        if (!$game_slug) return;

        $game = $this->get_game_by_slug($game_slug);
        if (!$game) return;

        $schema = [
            "@context" => "https://schema.org",
            "@type" => "VideoGame",
            "name" => $game->name,
            "description" => $game->description,
            "image" => $game->cover_url,
            "datePublished" => $game->release_date,
            "genre" => explode(',', $game->genres),
            "publisher" => $game->publisher,
            "author" => ["@type" => "Organization", "name" => $game->developer]
        ];

        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
    }

    private function get_game_by_slug($slug) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cl_games WHERE slug = %s",
            $slug
        ));
    }
}
