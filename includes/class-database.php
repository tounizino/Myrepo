<?php
/**
 * Database Class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CGT_Database {
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $platforms_table = $wpdb->prefix . 'cgt_platforms';
        $sql_platforms   = "CREATE TABLE $platforms_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            icon_url varchar(500) DEFAULT '',
            game_included tinyint(1) DEFAULT 0,
            price varchar(50) DEFAULT '',
            currency varchar(10) DEFAULT '$',
            tier varchar(255) DEFAULT '',
            cta_text varchar(100) DEFAULT 'Play Now',
            cta_url varchar(500) DEFAULT '',
            unavailable_url varchar(500) DEFAULT '',
            display_order int(11) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $games_table = $wpdb->prefix . 'cgt_games';
        $sql_games    = "CREATE TABLE $games_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            description text DEFAULT '',
            cover_image varchar(500) DEFAULT '',
            tier varchar(100) DEFAULT '',
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";

        $game_platforms_table = $wpdb->prefix . 'cgt_game_platforms';
        $sql_game_platforms   = "CREATE TABLE $game_platforms_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            platform_id bigint(20) UNSIGNED NOT NULL,
            game_id bigint(20) UNSIGNED NOT NULL,
            is_available tinyint(1) DEFAULT 1,
            game_included_override tinyint(1) DEFAULT -1,
            custom_price varchar(50) DEFAULT '',
            custom_cta_url varchar(500) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY platform_game (platform_id, game_id),
            KEY platform_id (platform_id),
            KEY game_id (game_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql_platforms );
        dbDelta( $sql_games );
        dbDelta( $sql_game_platforms );
    }

    public static function insert_default_platforms() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cgt_platforms';
        $count      = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        if ( $count > 0 ) {
            return;
        }

        $default_platforms = array(
            array(
                'name'           => 'GeForce NOW',
                'icon_url'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/GeForce_NOW_logo.svg/200px-GeForce_NOW_logo.svg.png',
                'game_included' => 0,
                'price'         => '9.99',
                'currency'      => '$',
                'tier'          => 'Priority',
                'cta_text'      => 'Play Now',
                'cta_url'       => 'https://play.geforcenow.com',
                'display_order' => 1,
                'is_active'     => 1,
            ),
            array(
                'name'           => 'Xbox Cloud Gaming',
                'icon_url'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Xbox_one_logo.svg/200px-Xbox_one_logo.svg.png',
                'game_included' => 1,
                'price'         => '14.99',
                'currency'      => '$',
                'tier'          => 'Game Pass Ultimate',
                'cta_text'      => 'Play Now',
                'cta_url'       => 'https://www.xbox.com/play',
                'display_order' => 2,
                'is_active'     => 1,
            ),
            array(
                'name'           => 'PlayStation Plus Premium',
                'icon_url'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/PlayStation_logo.svg/200px-PlayStation_logo.svg.png',
                'game_included' => 1,
                'price'         => '13.33',
                'currency'      => '$',
                'tier'          => 'Premium',
                'cta_text'      => 'Play Now',
                'cta_url'       => 'https://www.playstation.com',
                'display_order' => 3,
                'is_active'     => 1,
            ),
            array(
                'name'           => 'Amazon Luna',
                'icon_url'       => 'https://m.media-amazon.com/images/G/01/luna/luna-logo._CB1486155661_.png',
                'game_included' => 1,
                'price'         => '9.99',
                'currency'      => '$',
                'tier'          => 'Luna+',
                'cta_text'      => 'Play Now',
                'cta_url'       => 'https://luna.amazon.com',
                'display_order' => 4,
                'is_active'     => 1,
            ),
            array(
                'name'           => 'Boosteroid',
                'icon_url'       => 'https://boosteroid.com/assets/images/logo.svg',
                'game_included' => 0,
                'price'         => '7.49',
                'currency'      => '€',
                'tier'          => 'Any tier',
                'cta_text'      => 'Play Now',
                'cta_url'       => 'https://boosteroid.com',
                'display_order' => 5,
                'is_active'     => 1,
            ),
            array(
                'name'           => 'Shadow PC',
                'icon_url'       => 'https://shadow.tech/assets/images/logo.svg',
                'game_included' => 0,
                'price'         => '29.99',
                'currency'      => '$',
                'tier'          => 'Basic',
                'cta_text'      => 'Play Now',
                'cta_url'       => 'https://shadow.tech',
                'display_order' => 6,
                'is_active'     => 1,
            ),
            array(
                'name'           => 'Air GPU',
                'icon_url'       => 'https://airgpu.com/assets/logo.svg',
                'game_included' => 0,
                'price'         => '19.99',
                'currency'      => '$',
                'tier'          => 'Standard',
                'cta_text'      => 'Play Now',
                'cta_url'       => 'https://airgpu.com',
                'display_order' => 7,
                'is_active'     => 1,
            ),
            array(
                'name'           => 'Blacknut',
                'icon_url'       => 'https://www.blacknut.com/assets/logo.svg',
                'game_included' => 1,
                'price'         => '14.99',
                'currency'      => '$',
                'tier'          => 'Standard',
                'cta_text'      => 'Play Now',
                'cta_url'       => 'https://www.blacknut.com',
                'display_order' => 8,
                'is_active'     => 1,
            ),
            array(
                'name'           => 'CloudDeck',
                'icon_url'       => 'https://clouddeck.gg/assets/logo.svg',
                'game_included' => 0,
                'price'         => '24.99',
                'currency'      => '$',
                'tier'          => 'Pro',
                'cta_text'      => 'Play Now',
                'cta_url'       => 'https://clouddeck.gg',
                'display_order' => 9,
                'is_active'     => 1,
            ),
        );

        foreach ( $default_platforms as $platform ) {
            $wpdb->insert( $table_name, $platform );
        }
    }

    public static function get_platforms( $args = array() ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cgt_platforms';
        $defaults   = array( 'active_only' => true, 'order_by' => 'display_order', 'order' => 'ASC' );
        $args       = wp_parse_args( $args, $defaults );
        $where      = $args['active_only'] ? 'WHERE is_active = 1' : '';
        $sql        = "SELECT * FROM $table_name $where ORDER BY {$args['order_by']} {$args['order']}";
        $results    = $wpdb->get_results( $sql, ARRAY_A );
        return $results ? $results : array();
    }

    public static function get_platform( $platform_id ) {
        global $wpdb;
        $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cgt_platforms WHERE id = %d", $platform_id ), ARRAY_A );
        return $result ? $result : array();
    }

    public static function get_game( $game_id ) {
        global $wpdb;
        $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cgt_games WHERE id = %d", $game_id ), ARRAY_A );
        return $result ? $result : array();
    }

    public static function get_games( $args = array() ) {
        global $wpdb;
        $defaults   = array( 'active_only' => true, 'order_by' => 'name', 'order' => 'ASC' );
        $args       = wp_parse_args( $args, $defaults );
        $where      = $args['active_only'] ? 'WHERE is_active = 1' : '';
        $results    = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}cgt_games $where ORDER BY {$args['order_by']} {$args['order']}", ARRAY_A );
        return $results ? $results : array();
    }

    public static function get_game_availability( $game_id, $platform_id = null ) {
        global $wpdb;
        if ( $platform_id ) {
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cgt_game_platforms WHERE game_id = %d AND platform_id = %d", $game_id, $platform_id ), ARRAY_A );
            return $result ? $result : null;
        }
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}cgt_game_platforms WHERE game_id = %d", $game_id ), ARRAY_A );
        return $results ? $results : array();
    }

    public static function get_platforms_for_game( $game_id, $show_unavailable = true ) {
        global $wpdb;
        $platforms_table      = $wpdb->prefix . 'cgt_platforms';
        $game_platforms_table = $wpdb->prefix . 'cgt_game_platforms';

        $platforms = $wpdb->get_results( "SELECT * FROM $platforms_table WHERE is_active = 1 ORDER BY display_order ASC", ARRAY_A );
        if ( ! $platforms ) {
            $platforms = array();
        }

        $availability_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $game_platforms_table WHERE game_id = %d", $game_id ), ARRAY_A );
        if ( ! $availability_data ) {
            $availability_data = array();
        }

        $availability_map = array();
        foreach ( $availability_data as $avail ) {
            $availability_map[ $avail['platform_id'] ] = $avail;
        }

        foreach ( $platforms as &$platform ) {
            if ( isset( $availability_map[ $platform['id'] ] ) ) {
                $platform['is_available']            = $availability_map[ $platform['id'] ]['is_available'];
                $platform['game_included_override'] = $availability_map[ $platform['id'] ]['game_included_override'];
                if ( ! empty( $availability_map[ $platform['id'] ]['custom_price'] ) ) {
                    $platform['custom_price'] = $availability_map[ $platform['id'] ]['custom_price'];
                }
                if ( ! empty( $availability_map[ $platform['id'] ]['custom_cta_url'] ) ) {
                    $platform['custom_cta_url'] = $availability_map[ $platform['id'] ]['custom_cta_url'];
                }
            } else {
                $platform['is_available']            = 1;
                $platform['game_included_override'] = -1;
            }
        }

        if ( ! $show_unavailable ) {
            $platforms = array_filter( $platforms, function( $p ) { return ! empty( $p['is_available'] ); } );
        }

        return $platforms;
    }

    public static function update_platform( $platform_id, $data ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cgt_platforms';
        return $wpdb->update( $table_name, $data, array( 'id' => $platform_id ) );
    }

    public static function add_platform( $data ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cgt_platforms';
        $wpdb->insert( $table_name, $data );
        return $wpdb->insert_id;
    }

    public static function save_game_availability( $game_id, $platform_id, $is_available, $game_included_override = -1 ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cgt_game_platforms';

        $existing = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE game_id = %d AND platform_id = %d", $game_id, $platform_id ) );

        if ( $existing ) {
            return $wpdb->update(
                $table_name,
                array(
                    'is_available'            => $is_available,
                    'game_included_override' => $game_included_override,
                ),
                array( 'id' => $existing->id )
            );
        } else {
            return $wpdb->insert(
                $table_name,
                array(
                    'game_id'                => $game_id,
                    'platform_id'            => $platform_id,
                    'is_available'           => $is_available,
                    'game_included_override' => $game_included_override,
                )
            );
        }
    }
}