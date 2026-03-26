<?php

if (!defined('ABSPATH')) {
    exit;
}

class CGA_Database {

    private $table_prefix;

    public function __construct() {
        global $wpdb;
        $this->table_prefix = $wpdb->prefix . 'cga_';
    }

    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Games table
        $games_table = $this->table_prefix . 'games';
        $sql_games = "CREATE TABLE IF NOT EXISTS $games_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            description text,
            cover_image varchar(500),
            developer varchar(255),
            publisher varchar(255),
            release_date date,
            status enum('active', 'inactive') DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql_games);

        // Platforms table
        $platforms_table = $this->table_prefix . 'platforms';
        $sql_platforms = "CREATE TABLE IF NOT EXISTS $platforms_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            icon_url varchar(500),
            description text,
            base_price decimal(10, 2),
            price_currency varchar(10) DEFAULT 'USD',
            price_period enum('month', 'year', 'one-time') DEFAULT 'month',
            cta_text varchar(100),
            cta_url varchar(500),
            tier_name varchar(100),
            notes text,
            status enum('active', 'inactive') DEFAULT 'active',
            display_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta($sql_platforms);

        // Game availability table (junction table)
        $availability_table = $this->table_prefix . 'availability';
        $sql_availability = "CREATE TABLE IF NOT EXISTS $availability_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            game_id bigint(20) UNSIGNED NOT NULL,
            platform_id bigint(20) UNSIGNED NOT NULL,
            is_available tinyint(1) DEFAULT 1,
            game_included tinyint(1) DEFAULT 1,
            custom_price decimal(10, 2),
            custom_notes text,
            availability_notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY game_platform (game_id, platform_id),
            KEY game_id (game_id),
            KEY platform_id (platform_id),
            FOREIGN KEY (game_id) REFERENCES {$this->table_prefix}games(id) ON DELETE CASCADE,
            FOREIGN KEY (platform_id) REFERENCES {$this->table_prefix}platforms(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql_availability);
    }

    public function insert_default_data() {
        global $wpdb;
        $platforms_table = $this->table_prefix . 'platforms';
        $availability_table = $this->table_prefix . 'availability';
        $games_table = $this->table_prefix . 'games';

        // Check if platforms already exist
        $existing_count = $wpdb->get_var("SELECT COUNT(*) FROM $platforms_table");
        if ($existing_count > 0) {
            return;
        }

        // Default platforms
        $default_platforms = array(
            array(
                'name' => 'GeForce NOW',
                'slug' => 'geforce-now',
                'icon_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/GeForce_NOW_logo.svg/512px-GeForce_NOW_logo.svg.png',
                'description' => 'NVIDIA\'s cloud gaming service with RTX support',
                'base_price' => 9.99,
                'price_period' => 'month',
                'cta_text' => 'Play Now',
                'cta_url' => 'https://www.geforcenow.com',
                'tier_name' => 'Priority',
                'display_order' => 1,
            ),
            array(
                'name' => 'Xbox Cloud Gaming',
                'slug' => 'xbox-cloud-gaming',
                'icon_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Xbox_one_logo.svg/512px-Xbox_one_logo.svg.png',
                'description' => 'Microsoft\'s Xbox Game Pass cloud streaming service',
                'base_price' => 14.99,
                'price_period' => 'month',
                'cta_text' => 'Start Gaming',
                'cta_url' => 'https://www.xbox.com/xbox-cloud-gaming',
                'tier_name' => 'Ultimate',
                'display_order' => 2,
            ),
            array(
                'name' => 'PlayStation Cloud',
                'slug' => 'playstation-cloud',
                'icon_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/PlayStation_4_logo.svg/512px-PlayStation_4_logo.svg.png',
                'description' => 'Sony\'s PlayStation cloud gaming service',
                'base_price' => 19.99,
                'price_period' => 'month',
                'cta_text' => 'Play Now',
                'cta_url' => 'https://www.playstation.com/ps-plus/',
                'tier_name' => 'Premium',
                'display_order' => 3,
            ),
            array(
                'name' => 'Amazon Luna',
                'slug' => 'amazon-luna',
                'icon_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Amazon_logo.svg/512px-Amazon_logo.svg.png',
                'description' => 'Amazon\'s cloud gaming service',
                'base_price' => 9.99,
                'price_period' => 'month',
                'cta_text' => 'Start Playing',
                'cta_url' => 'https://luna.amazon.com',
                'tier_name' => 'Luna+',
                'display_order' => 4,
            ),
            array(
                'name' => 'Boosteroid',
                'slug' => 'boosteroid',
                'icon_url' => 'https://boosteroid.com/favicon.ico',
                'description' => 'Cloud gaming service with PC game library',
                'base_price' => 9.99,
                'price_period' => 'month',
                'cta_text' => 'Start Gaming',
                'cta_url' => 'https://boosteroid.com',
                'tier_name' => 'Standard',
                'display_order' => 5,
            ),
            array(
                'name' => 'Shadow PC',
                'slug' => 'shadow-pc',
                'icon_url' => 'https://shadow.tech/favicon.ico',
                'description' => 'Full Windows PC in the cloud',
                'base_price' => 29.99,
                'price_period' => 'month',
                'cta_text' => 'Get Shadow',
                'cta_url' => 'https://shadow.tech',
                'tier_name' => 'Power Up',
                'display_order' => 6,
            ),
            array(
                'name' => 'Antstream Arcade',
                'slug' => 'antstream-arcade',
                'icon_url' => 'https://antstream.com/favicon.ico',
                'description' => 'Retro gaming cloud service',
                'base_price' => 4.99,
                'price_period' => 'month',
                'cta_text' => 'Play Retro',
                'cta_url' => 'https://antstream.com',
                'tier_name' => 'Premium',
                'display_order' => 7,
            ),
            array(
                'name' => 'NVIDIA GeForce NOW (Free)',
                'slug' => 'geforce-now-free',
                'icon_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/GeForce_NOW_logo.svg/512px-GeForce_NOW_logo.svg.png',
                'description' => 'Free tier with limited session time',
                'base_price' => 0,
                'price_period' => 'month',
                'cta_text' => 'Try Free',
                'cta_url' => 'https://www.geforcenow.com',
                'tier_name' => 'Free',
                'display_order' => 8,
            ),
        );

        foreach ($default_platforms as $platform) {
            $wpdb->insert($platforms_table, $platform);
        }

        // Default games
        $default_games = array(
            array(
                'name' => 'Cyberpunk 2077',
                'slug' => 'cyberpunk-2077',
                'description' => 'Open-world action RPG set in Night City',
                'developer' => 'CD Projekt Red',
                'publisher' => 'CD Projekt',
            ),
            array(
                'name' => 'Elden Ring',
                'slug' => 'elden-ring',
                'description' => 'Action RPG by FromSoftware and George R.R. Martin',
                'developer' => 'FromSoftware',
                'publisher' => 'Bandai Namco',
            ),
            array(
                'name' => 'Fortnite',
                'slug' => 'fortnite',
                'description' => 'Free-to-play battle royale game',
                'developer' => 'Epic Games',
                'publisher' => 'Epic Games',
            ),
        );

        foreach ($default_games as $game) {
            $wpdb->insert($games_table, $game);
        }

        // Create sample availability for first game
        $game_id = $wpdb->get_var("SELECT id FROM $games_table WHERE slug = 'cyberpunk-2077'");
        $platforms = $wpdb->get_col("SELECT id FROM $platforms_table");

        if ($game_id && !empty($platforms)) {
            foreach ($platforms as $platform_id) {
                // Make most platforms available except a few
                $is_available = !in_array($platform_id, array(6, 8));
                
                $wpdb->insert($availability_table, array(
                    'game_id' => $game_id,
                    'platform_id' => $platform_id,
                    'is_available' => $is_available ? 1 : 0,
                    'game_included' => 1,
                ));
            }
        }
    }

    public function get_platforms($status = 'active') {
        global $wpdb;
        $platforms_table = $this->table_prefix . 'platforms';
        
        $query = "SELECT * FROM $platforms_table";
        if ($status) {
            $query .= $wpdb->prepare(" WHERE status = %s", $status);
        }
        $query .= " ORDER BY display_order ASC, name ASC";
        
        return $wpdb->get_results($query);
    }

    public function get_games($status = 'active') {
        global $wpdb;
        $games_table = $this->table_prefix . 'games';
        
        $query = "SELECT * FROM $games_table";
        if ($status) {
            $query .= $wpdb->prepare(" WHERE status = %s", $status);
        }
        $query .= " ORDER BY name ASC";
        
        return $wpdb->get_results($query);
    }

    public function get_game_availability($game_id) {
        global $wpdb;
        $availability_table = $this->table_prefix . 'availability';
        $platforms_table = $this->table_prefix . 'platforms';
        
        $query = $wpdb->prepare("
            SELECT p.*, a.is_available, a.game_included, a.custom_price, 
                   a.custom_notes, a.availability_notes
            FROM $platforms_table p
            LEFT JOIN $availability_table a ON p.id = a.platform_id 
                AND a.game_id = %d
            WHERE p.status = 'active'
            ORDER BY p.display_order ASC, p.name ASC
        ", $game_id);
        
        return $wpdb->get_results($query);
    }

    public function get_platform($id) {
        global $wpdb;
        $platforms_table = $this->table_prefix . 'platforms';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $platforms_table WHERE id = %d",
            $id
        ));
    }

    public function get_game($id) {
        global $wpdb;
        $games_table = $this->table_prefix . 'games';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $games_table WHERE id = %d",
            $id
        ));
    }

    public function save_platform($data) {
        global $wpdb;
        $platforms_table = $this->table_prefix . 'platforms';
        
        if (empty($data['id'])) {
            // Insert new platform
            $wpdb->insert($platforms_table, $data);
            return $wpdb->insert_id;
        } else {
            // Update existing platform
            $id = $data['id'];
            unset($data['id']);
            $wpdb->update($platforms_table, $data, array('id' => $id));
            return $id;
        }
    }

    public function save_game($data) {
        global $wpdb;
        $games_table = $this->table_prefix . 'games';
        
        if (empty($data['id'])) {
            // Insert new game
            if (empty($data['slug'])) {
                $data['slug'] = sanitize_title($data['name']);
            }
            $wpdb->insert($games_table, $data);
            return $wpdb->insert_id;
        } else {
            // Update existing game
            $id = $data['id'];
            unset($data['id']);
            if (!isset($data['slug'])) {
                $data['slug'] = sanitize_title($data['name']);
            }
            $wpdb->update($games_table, $data, array('id' => $id));
            return $id;
        }
    }

    public function save_availability($game_id, $platforms_data) {
        global $wpdb;
        $availability_table = $this->table_prefix . 'availability';
        
        foreach ($platforms_data as $platform_id => $data) {
            $existing = $wpdb->get_row($wpdb->prepare(
                "SELECT id FROM $availability_table WHERE game_id = %d AND platform_id = %d",
                $game_id, $platform_id
            ));
            
            $record = array(
                'game_id' => $game_id,
                'platform_id' => $platform_id,
                'is_available' => isset($data['is_available']) ? 1 : 0,
                'game_included' => isset($data['game_included']) ? 1 : 0,
                'custom_price' => !empty($data['custom_price']) ? $data['custom_price'] : null,
                'custom_notes' => !empty($data['custom_notes']) ? $data['custom_notes'] : null,
                'availability_notes' => !empty($data['availability_notes']) ? $data['availability_notes'] : null,
            );
            
            if ($existing) {
                $wpdb->update($availability_table, $record, array('id' => $existing->id));
            } else {
                $wpdb->insert($availability_table, $record);
            }
        }
        
        return true;
    }

    public function delete_platform($id) {
        global $wpdb;
        $platforms_table = $this->table_prefix . 'platforms';
        
        return $wpdb->delete($platforms_table, array('id' => $id));
    }

    public function delete_game($id) {
        global $wpdb;
        $games_table = $this->table_prefix . 'games';
        
        return $wpdb->delete($games_table, array('id' => $id));
    }
}
