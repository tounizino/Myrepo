<?php
namespace CloudLoadout;

if (!defined('ABSPATH')) {
    exit;
}

class Database {
    public static function install() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $tables = [
            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cl_games (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                external_id varchar(100) DEFAULT NULL,
                name varchar(255) NOT NULL,
                slug varchar(255) NOT NULL,
                description text,
                release_date date DEFAULT NULL,
                rating decimal(3,2) DEFAULT NULL,
                cover_url varchar(255) DEFAULT NULL,
                background_url varchar(255) DEFAULT NULL,
                developer varchar(255) DEFAULT NULL,
                publisher varchar(255) DEFAULT NULL,
                genres varchar(255) DEFAULT NULL,
                supported_devices text,
                controller_support varchar(100) DEFAULT NULL,
                cross_platform tinyint(1) DEFAULT 0,
                last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY slug (slug),
                KEY external_id (external_id)
            ) $charset_collate;",

            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cl_providers (
                id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(100) NOT NULL,
                slug varchar(100) NOT NULL,
                website_url varchar(255) DEFAULT NULL,
                logo_url varchar(255) DEFAULT NULL,
                PRIMARY KEY  (id),
                UNIQUE KEY slug (slug)
            ) $charset_collate;",

            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cl_game_provider (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                game_id bigint(20) NOT NULL,
                provider_id int(11) NOT NULL,
                status varchar(50) DEFAULT 'supported',
                last_checked datetime DEFAULT CURRENT_TIMESTAMP,
                confidence_score int(3) DEFAULT 100,
                source_url varchar(255) DEFAULT NULL,
                cloud_notes text,
                notes text,
                PRIMARY KEY  (id),
                UNIQUE KEY game_provider (game_id, provider_id)
            ) $charset_collate;",

            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cl_aliases (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                game_id bigint(20) NOT NULL,
                alias_name varchar(255) NOT NULL,
                PRIMARY KEY  (id),
                KEY game_id (game_id),
                KEY alias_name (alias_name)
            ) $charset_collate;",

            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cl_screenshots (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                game_id bigint(20) NOT NULL,
                image_url varchar(255) NOT NULL,
                PRIMARY KEY  (id),
                KEY game_id (game_id)
            ) $charset_collate;",

            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cl_sync_logs (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                provider_id int(11) DEFAULT NULL,
                sync_type varchar(50) DEFAULT 'scraper',
                status varchar(50) DEFAULT 'pending',
                message text,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id)
            ) $charset_collate;"
        ];

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach ($tables as $sql) {
            dbDelta($sql);
        }

        self::seed_providers();
    }

    private static function seed_providers() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cl_providers';

        $providers = [
            ['name' => 'GeForce NOW', 'slug' => 'geforce-now'],
            ['name' => 'Boosteroid', 'slug' => 'boosteroid'],
            ['name' => 'Xbox Cloud Gaming', 'slug' => 'xbox-cloud'],
            ['name' => 'Amazon Luna', 'slug' => 'amazon-luna'],
            ['name' => 'Blacknut', 'slug' => 'blacknut'],
            ['name' => 'Shadow PC', 'slug' => 'shadow-pc'],
            ['name' => 'AirGPU', 'slug' => 'airgpu'],
            ['name' => 'Playstation Cloud', 'slug' => 'playstation-cloud'],
        ];

        foreach ($providers as $provider) {
            $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE slug = %s", $provider['slug']));
            if (!$exists) {
                $wpdb->insert($table_name, $provider);
            }
        }
    }
}
