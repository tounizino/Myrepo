<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CGRT_Database {

    /**
     * @var CGRT_Database
     */
    private static $instance;

    /**
     * @return CGRT_Database
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    /**
     * Table names.
     *
     * @return array
     */
    public static function tables() {
        global $wpdb;

        return array(
            'platforms' => $wpdb->prefix . 'cgrt_platforms',
            'endpoints' => $wpdb->prefix . 'cgrt_endpoints',
            'results'   => $wpdb->prefix . 'cgrt_results',
        );
    }

    /**
     * Create plugin tables.
     */
    public static function create_tables() {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $tables          = self::tables();

        $sql_platforms = "CREATE TABLE {$tables['platforms']} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(191) NOT NULL,
            slug varchar(191) NOT NULL,
            enabled tinyint(1) NOT NULL DEFAULT 1,
            weights_json longtext NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";

        $sql_endpoints = "CREATE TABLE {$tables['endpoints']} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            platform_id bigint(20) unsigned NOT NULL,
            region varchar(191) NOT NULL,
            endpoint_url text NOT NULL,
            method varchar(16) NOT NULL DEFAULT 'HEAD',
            enabled tinyint(1) NOT NULL DEFAULT 1,
            timeout_ms int(11) NOT NULL DEFAULT 2500,
            notes text NULL,
            is_default tinyint(1) NOT NULL DEFAULT 0,
            endpoint_type varchar(32) NOT NULL DEFAULT 'service_entry',
            has_disclaimer tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY platform_id (platform_id)
        ) $charset_collate;";

        $sql_results = "CREATE TABLE {$tables['results']} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL,
            platform_id bigint(20) unsigned NULL,
            endpoint_id bigint(20) unsigned NULL,
            score int(11) NOT NULL,
            tier varchar(32) NOT NULL,
            metrics_json longtext NOT NULL,
            ua_hash char(64) NULL,
            ip_hash char(64) NULL,
            PRIMARY KEY  (id),
            KEY created_at (created_at),
            KEY score (score)
        ) $charset_collate;";

        dbDelta( $sql_platforms );
        dbDelta( $sql_endpoints );
        dbDelta( $sql_results );

        // Default settings.
        if ( false === get_option( 'cgrt_settings', false ) ) {
            add_option( 'cgrt_settings', self::default_settings(), '', false );
        }
    }

    /**
     * Default plugin settings.
     *
     * @return array
     */
    public static function default_settings() {
        return array(
            'test_duration_seconds' => 18,
            'sample_interval_ms'   => 350,
            'endpoint_pick_pings'  => 5,
            'spike_ms'             => 35,
            'store_results'        => false,
            'tiers'                => array(
                'excellent' => 85,
                'good'      => 70,
                'fair'      => 55,
                'poor'      => 0,
            ),
            'default_weights'      => array(
                'latency'   => 0.40,
                'jitter'    => 0.25,
                'loss'      => 0.25,
                'stability' => 0.10,
            ),
        );
    }

    /**
     * Insert curated default platforms/endpoints.
     */
    public static function insert_default_data() {
        self::ensure_curated_defaults();
    }

    /**
     * Curated default platforms and endpoints.
     *
     * @return array
     */
    private static function curated_seed_data() {
        $default_weights = self::default_settings()['default_weights'];

        $limited_weights = array(
            'latency'   => 0.30,
            'jitter'    => 0.20,
            'loss'      => 0.30,
            'stability' => 0.20,
        );

        return array(
            array(
                'name'    => 'GeForce NOW',
                'slug'    => 'geforce-now',
                'enabled' => 1,
                'weights' => $default_weights,
                'endpoints' => array(
                    array(
                        'region'        => 'Service Entry',
                        'endpoint_url'  => 'https://play.geforcenow.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'service_entry',
                    ),
                    array(
                        'region'        => 'Status',
                        'endpoint_url'  => 'https://status.nvidia.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'status',
                    ),
                ),
            ),
            array(
                'name'    => 'Xbox Cloud Gaming',
                'slug'    => 'xbox-cloud-gaming',
                'enabled' => 1,
                'weights' => $default_weights,
                'endpoints' => array(
                    array(
                        'region'        => 'Service Entry',
                        'endpoint_url'  => 'https://www.xbox.com/play',
                        'method'        => 'GET',
                        'endpoint_type' => 'service_entry',
                    ),
                    array(
                        'region'        => 'Status',
                        'endpoint_url'  => 'https://xcloud.xboxlive.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'status',
                    ),
                ),
            ),
            array(
                'name'    => 'Amazon Luna',
                'slug'    => 'amazon-luna',
                'enabled' => 1,
                'weights' => $default_weights,
                'endpoints' => array(
                    array(
                        'region'        => 'Service Entry',
                        'endpoint_url'  => 'https://www.amazon.com/luna/landing-page',
                        'method'        => 'GET',
                        'endpoint_type' => 'service_entry',
                    ),
                    array(
                        'region'        => 'Status',
                        'endpoint_url'  => 'https://ec2.us-east-1.amazonaws.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'status',
                    ),
                ),
            ),
            array(
                'name'    => 'Boosteroid',
                'slug'    => 'boosteroid',
                'enabled' => 1,
                'weights' => $default_weights,
                'endpoints' => array(
                    array(
                        'region'        => 'Service Entry',
                        'endpoint_url'  => 'https://cloud.boosteroid.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'service_entry',
                    ),
                    array(
                        'region'        => 'Status',
                        'endpoint_url'  => 'https://boosteroid.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'status',
                    ),
                ),
            ),
            array(
                'name'    => 'Shadow PC',
                'slug'    => 'shadow-pc',
                'enabled' => 1,
                'weights' => $default_weights,
                'endpoints' => array(
                    array(
                        'region'        => 'Service Entry',
                        'endpoint_url'  => 'https://shadow.tech/',
                        'method'        => 'GET',
                        'endpoint_type' => 'service_entry',
                    ),
                    array(
                        'region'        => 'Status',
                        'endpoint_url'  => 'https://status.shadow.tech/',
                        'method'        => 'GET',
                        'endpoint_type' => 'status',
                    ),
                ),
            ),
            array(
                'name'    => 'PlayStation Plus Premium',
                'slug'    => 'playstation-plus-premium',
                'enabled' => 1,
                'weights' => $limited_weights,
                'limited' => 1,
                'endpoints' => array(
                    array(
                        'region'        => 'Service Entry',
                        'endpoint_url'  => 'https://www.playstation.com/ps-plus',
                        'method'        => 'GET',
                        'endpoint_type' => 'service_entry',
                        'has_disclaimer'=> 1,
                    ),
                    array(
                        'region'        => 'Status',
                        'endpoint_url'  => 'https://status.playstation.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'status',
                        'has_disclaimer'=> 1,
                    ),
                ),
            ),
            array(
                'name'    => 'AirGPU',
                'slug'    => 'airgpu',
                'enabled' => 1,
                'weights' => $limited_weights,
                'limited' => 1,
                'endpoints' => array(
                    array(
                        'region'        => 'Service Entry',
                        'endpoint_url'  => 'https://airgpu.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'service_entry',
                        'has_disclaimer'=> 1,
                    ),
                    array(
                        'region'        => 'Status',
                        'endpoint_url'  => 'https://api.airgpu.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'status',
                        'has_disclaimer'=> 1,
                    ),
                ),
            ),
            array(
                'name'    => 'Blacknut',
                'slug'    => 'blacknut',
                'enabled' => 1,
                'weights' => $limited_weights,
                'limited' => 1,
                'endpoints' => array(
                    array(
                        'region'        => 'Service Entry',
                        'endpoint_url'  => 'https://www.blacknut.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'service_entry',
                        'has_disclaimer'=> 1,
                    ),
                    array(
                        'region'        => 'Status',
                        'endpoint_url'  => 'https://status.blacknut.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'status',
                        'has_disclaimer'=> 1,
                    ),
                ),
            ),
            array(
                'name'    => 'CloudDeck',
                'slug'    => 'clouddeck',
                'enabled' => 1,
                'weights' => $limited_weights,
                'limited' => 1,
                'endpoints' => array(
                    array(
                        'region'        => 'Service Entry',
                        'endpoint_url'  => 'https://clouddeck.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'service_entry',
                        'has_disclaimer'=> 1,
                    ),
                    array(
                        'region'        => 'Status',
                        'endpoint_url'  => 'https://status.clouddeck.com/',
                        'method'        => 'GET',
                        'endpoint_type' => 'status',
                        'has_disclaimer'=> 1,
                    ),
                ),
            ),
        );
    }

    /**
     * Ensure curated defaults exist (platforms + endpoints). Re-creates missing curated defaults.
     *
     * @return void
     */
    public static function ensure_curated_defaults() {
        global $wpdb;

        $tables   = self::tables();
        $seed     = self::curated_seed_data();
        $now      = current_time( 'mysql' );
        $settings = self::get_settings();

        foreach ( $seed as $platform_seed ) {
            $slug = sanitize_title( $platform_seed['slug'] );

            $platform_id = (int) $wpdb->get_var(
                $wpdb->prepare( "SELECT id FROM {$tables['platforms']} WHERE slug = %s", $slug )
            );

            if ( $platform_id <= 0 ) {
                $wpdb->insert(
                    $tables['platforms'],
                    array(
                        'name'         => $platform_seed['name'],
                        'slug'         => $slug,
                        'enabled'      => (int) (bool) $platform_seed['enabled'],
                        'weights_json' => wp_json_encode( $platform_seed['weights'] ),
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ),
                    array( '%s', '%s', '%d', '%s', '%s', '%s' )
                );

                $platform_id = (int) $wpdb->insert_id;
            }

            if ( $platform_id <= 0 ) {
                continue;
            }

            $platform_weights = isset( $platform_seed['weights'] ) && is_array( $platform_seed['weights'] ) ? $platform_seed['weights'] : $settings['default_weights'];

            // Ensure weight data remains present for curated platforms.
            $wpdb->update(
                $tables['platforms'],
                array(
                    'weights_json' => wp_json_encode( $platform_weights ),
                    'updated_at'   => $now,
                ),
                array( 'id' => $platform_id ),
                array( '%s', '%s' ),
                array( '%d' )
            );

            foreach ( $platform_seed['endpoints'] as $endpoint_seed ) {
                $url = esc_url_raw( $endpoint_seed['endpoint_url'] );
                if ( empty( $url ) ) {
                    continue;
                }

                $exists = (int) $wpdb->get_var(
                    $wpdb->prepare( "SELECT id FROM {$tables['endpoints']} WHERE platform_id = %d AND endpoint_url = %s", $platform_id, $url )
                );

                if ( $exists > 0 ) {
                    continue;
                }

                $wpdb->insert(
                    $tables['endpoints'],
                    array(
                        'platform_id'    => $platform_id,
                        'region'         => $endpoint_seed['region'],
                        'endpoint_url'   => $url,
                        'method'         => isset( $endpoint_seed['method'] ) ? $endpoint_seed['method'] : 'GET',
                        'enabled'        => 1,
                        'timeout_ms'     => 6500,
                        'notes'          => 'Curated default endpoint.',
                        'is_default'     => 1,
                        'endpoint_type'  => isset( $endpoint_seed['endpoint_type'] ) ? $endpoint_seed['endpoint_type'] : 'service_entry',
                        'has_disclaimer' => ! empty( $endpoint_seed['has_disclaimer'] ) ? 1 : 0,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ),
                    array( '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%d', '%s', '%d', '%s', '%s' )
                );
            }
        }
    }


    /**
     * Get settings.
     *
     * @return array
     */
    public static function get_settings() {
        $settings = get_option( 'cgrt_settings', array() );
        $defaults = self::default_settings();

        $settings = wp_parse_args( $settings, $defaults );

        if ( empty( $settings['tiers'] ) || ! is_array( $settings['tiers'] ) ) {
            $settings['tiers'] = $defaults['tiers'];
        }

        if ( empty( $settings['default_weights'] ) || ! is_array( $settings['default_weights'] ) ) {
            $settings['default_weights'] = $defaults['default_weights'];
        }

        return $settings;
    }

    /**
     * Update settings.
     *
     * @param array $settings Settings.
     * @return bool
     */
    public static function update_settings( $settings ) {
        $existing = self::get_settings();
        $merged   = wp_parse_args( $settings, $existing );

        return update_option( 'cgrt_settings', $merged, false );
    }

    /**
     * Get enabled platforms with endpoints.
     *
     * @return array
     */
    public static function get_platforms_with_endpoints() {
        global $wpdb;

        $tables    = self::tables();
        $settings  = self::get_settings();
        $platforms = $wpdb->get_results( "SELECT * FROM {$tables['platforms']} ORDER BY name ASC", ARRAY_A );

        $out = array();
        foreach ( $platforms as $p ) {
            $p['id']      = (int) $p['id'];
            $p['enabled'] = (int) $p['enabled'];

            $weights = array();
            if ( ! empty( $p['weights_json'] ) ) {
                $decoded = json_decode( $p['weights_json'], true );
                if ( is_array( $decoded ) ) {
                    $weights = $decoded;
                }
            }
            $p['weights'] = wp_parse_args( $weights, $settings['default_weights'] );

            $endpoints = $wpdb->get_results(
                $wpdb->prepare( "SELECT * FROM {$tables['endpoints']} WHERE platform_id = %d ORDER BY enabled DESC, region ASC", $p['id'] ),
                ARRAY_A
            );

            foreach ( $endpoints as &$e ) {
                $e['id']          = (int) $e['id'];
                $e['platform_id'] = (int) $e['platform_id'];
                $e['enabled']     = (int) $e['enabled'];
                $e['timeout_ms']  = (int) $e['timeout_ms'];
                $e['is_default']  = isset( $e['is_default'] ) ? (int) $e['is_default'] : 0;
                $e['has_disclaimer'] = isset( $e['has_disclaimer'] ) ? (int) $e['has_disclaimer'] : 0;
                $e['endpoint_type']  = isset( $e['endpoint_type'] ) ? sanitize_text_field( $e['endpoint_type'] ) : 'service_entry';
            }

            $p['endpoints'] = $endpoints;
            $out[]          = $p;
        }

        return $out;
    }

    /**
     * Get a platform.
     *
     * @param int $platform_id Platform ID.
     * @return array|null
     */
    public static function get_platform( $platform_id ) {
        global $wpdb;

        $platform_id = (int) $platform_id;
        if ( $platform_id <= 0 ) {
            return null;
        }

        $tables = self::tables();
        $row    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$tables['platforms']} WHERE id = %d", $platform_id ), ARRAY_A );
        if ( ! $row ) {
            return null;
        }

        $row['id']      = (int) $row['id'];
        $row['enabled'] = (int) $row['enabled'];

        return $row;
    }

    /**
     * List endpoints for a platform.
     *
     * @param int $platform_id Platform ID.
     * @return array
     */
    public static function get_endpoints_for_platform( $platform_id ) {
        global $wpdb;

        $tables = self::tables();

        $rows = $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM {$tables['endpoints']} WHERE platform_id = %d ORDER BY enabled DESC, region ASC", (int) $platform_id ),
            ARRAY_A
        );

        foreach ( $rows as &$r ) {
            $r['id']          = (int) $r['id'];
            $r['platform_id'] = (int) $r['platform_id'];
            $r['enabled']     = (int) $r['enabled'];
            $r['timeout_ms']  = (int) $r['timeout_ms'];
        }

        return $rows;
    }

    /**
     * Save a platform.
     *
     * @param array $data Data.
     * @return int Platform ID.
     */
    public static function save_platform( $data ) {
        global $wpdb;

        $tables = self::tables();
        $now    = current_time( 'mysql' );

        $id      = isset( $data['id'] ) ? (int) $data['id'] : 0;
        $name    = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
        $slug    = isset( $data['slug'] ) ? sanitize_title( $data['slug'] ) : '';
        $enabled = isset( $data['enabled'] ) ? (int) (bool) $data['enabled'] : 0;

        $weights = isset( $data['weights'] ) && is_array( $data['weights'] ) ? $data['weights'] : array();
        $weights = array(
            'latency'   => isset( $weights['latency'] ) ? (float) $weights['latency'] : null,
            'jitter'    => isset( $weights['jitter'] ) ? (float) $weights['jitter'] : null,
            'loss'      => isset( $weights['loss'] ) ? (float) $weights['loss'] : null,
            'stability' => isset( $weights['stability'] ) ? (float) $weights['stability'] : null,
        );
        $weights = array_filter( $weights, static function ( $v ) {
            return null !== $v && $v >= 0;
        } );

        $weights_json = ! empty( $weights ) ? wp_json_encode( $weights ) : null;

        if ( $id > 0 ) {
            $wpdb->update(
                $tables['platforms'],
                array(
                    'name'         => $name,
                    'slug'         => $slug,
                    'enabled'      => $enabled,
                    'weights_json' => $weights_json,
                    'updated_at'   => $now,
                ),
                array( 'id' => $id ),
                array( '%s', '%s', '%d', '%s', '%s' ),
                array( '%d' )
            );
            return $id;
        }

        $wpdb->insert(
            $tables['platforms'],
            array(
                'name'         => $name,
                'slug'         => $slug,
                'enabled'      => $enabled,
                'weights_json' => $weights_json,
                'created_at'   => $now,
                'updated_at'   => $now,
            ),
            array( '%s', '%s', '%d', '%s', '%s', '%s' )
        );

        return (int) $wpdb->insert_id;
    }

    /**
     * Delete platform and its endpoints.
     *
     * @param int $platform_id Platform ID.
     * @return void
     */
    public static function delete_platform( $platform_id ) {
        global $wpdb;

        $platform_id = (int) $platform_id;
        if ( $platform_id <= 0 ) {
            return;
        }

        $tables = self::tables();

        $wpdb->delete( $tables['endpoints'], array( 'platform_id' => $platform_id ), array( '%d' ) );
        $wpdb->delete( $tables['platforms'], array( 'id' => $platform_id ), array( '%d' ) );
    }

    /**
     * Save an endpoint.
     *
     * @param array $data Data.
     * @return int Endpoint ID.
     */
    public static function save_endpoint( $data ) {
        global $wpdb;

        $tables = self::tables();
        $now    = current_time( 'mysql' );

        $id          = isset( $data['id'] ) ? (int) $data['id'] : 0;
        $platform_id = isset( $data['platform_id'] ) ? (int) $data['platform_id'] : 0;
        $region      = isset( $data['region'] ) ? sanitize_text_field( $data['region'] ) : '';
        $url         = isset( $data['endpoint_url'] ) ? esc_url_raw( $data['endpoint_url'] ) : '';
        $method      = isset( $data['method'] ) ? strtoupper( sanitize_text_field( $data['method'] ) ) : 'HEAD';
        $enabled     = isset( $data['enabled'] ) ? (int) (bool) $data['enabled'] : 0;
        $timeout_ms  = isset( $data['timeout_ms'] ) ? (int) $data['timeout_ms'] : 2500;
        $notes       = isset( $data['notes'] ) ? sanitize_textarea_field( $data['notes'] ) : '';

        if ( ! in_array( $method, array( 'HEAD', 'GET' ), true ) ) {
            $method = 'HEAD';
        }

        $timeout_ms = max( 500, min( 10000, $timeout_ms ) );

        if ( $id > 0 ) {
            $wpdb->update(
                $tables['endpoints'],
                array(
                    'region'       => $region,
                    'endpoint_url' => $url,
                    'method'       => $method,
                    'enabled'      => $enabled,
                    'timeout_ms'   => $timeout_ms,
                    'notes'        => $notes,
                    'updated_at'   => $now,
                ),
                array( 'id' => $id ),
                array( '%s', '%s', '%s', '%d', '%d', '%s', '%s' ),
                array( '%d' )
            );
            return $id;
        }

        $wpdb->insert(
            $tables['endpoints'],
            array(
                'platform_id'  => $platform_id,
                'region'       => $region,
                'endpoint_url' => $url,
                'method'       => $method,
                'enabled'      => $enabled,
                'timeout_ms'   => $timeout_ms,
                'notes'        => $notes,
                'created_at'   => $now,
                'updated_at'   => $now,
            ),
            array( '%d', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s' )
        );

        return (int) $wpdb->insert_id;
    }

    /**
     * Delete endpoint.
     *
     * @param int $endpoint_id Endpoint ID.
     * @return void
     */
    public static function delete_endpoint( $endpoint_id ) {
        global $wpdb;

        $endpoint_id = (int) $endpoint_id;
        if ( $endpoint_id <= 0 ) {
            return;
        }

        $tables = self::tables();
        $wpdb->delete( $tables['endpoints'], array( 'id' => $endpoint_id ), array( '%d' ) );
    }

    /**
     * Store a test result if enabled.
     *
     * @param array $payload Payload.
     * @return int Result ID.
     */
    public static function insert_result( $payload ) {
        $settings = self::get_settings();
        if ( empty( $settings['store_results'] ) ) {
            return 0;
        }

        global $wpdb;

        $tables = self::tables();
        $now    = current_time( 'mysql' );

        $platform_id = isset( $payload['platform_id'] ) ? (int) $payload['platform_id'] : null;
        $endpoint_id = isset( $payload['endpoint_id'] ) ? (int) $payload['endpoint_id'] : null;
        $score       = isset( $payload['score'] ) ? (int) $payload['score'] : 0;
        $tier        = isset( $payload['tier'] ) ? sanitize_text_field( $payload['tier'] ) : 'unknown';
        $metrics     = isset( $payload['metrics'] ) && is_array( $payload['metrics'] ) ? $payload['metrics'] : array();

        $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
        $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

        $ua_hash = $ua ? hash_hmac( 'sha256', $ua, wp_salt( 'auth' ) ) : null;
        $ip_hash = $ip ? hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) ) : null;

        $wpdb->insert(
            $tables['results'],
            array(
                'created_at'   => $now,
                'platform_id'  => $platform_id,
                'endpoint_id'  => $endpoint_id,
                'score'        => max( 0, min( 100, $score ) ),
                'tier'         => $tier,
                'metrics_json' => wp_json_encode( $metrics ),
                'ua_hash'      => $ua_hash,
                'ip_hash'      => $ip_hash,
            ),
            array( '%s', '%d', '%d', '%d', '%s', '%s', '%s', '%s' )
        );

        return (int) $wpdb->insert_id;
    }

    /**
     * Get basic aggregated stats.
     *
     * @return array
     */
    public static function get_stats() {
        global $wpdb;
        $tables   = self::tables();
        $settings = self::get_settings();

        if ( empty( $settings['store_results'] ) ) {
            return array(
                'enabled' => false,
            );
        }

        $total       = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$tables['results']}" );
        $avg_score   = (float) $wpdb->get_var( "SELECT AVG(score) FROM {$tables['results']}" );
        $last_24h    = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$tables['results']} WHERE created_at >= %s", gmdate( 'Y-m-d H:i:s', time() - DAY_IN_SECONDS ) ) );
        $avg_last_24 = (float) $wpdb->get_var( $wpdb->prepare( "SELECT AVG(score) FROM {$tables['results']} WHERE created_at >= %s", gmdate( 'Y-m-d H:i:s', time() - DAY_IN_SECONDS ) ) );

        return array(
            'enabled'          => true,
            'total_tests'      => $total,
            'avg_score'        => round( $avg_score, 1 ),
            'tests_last_24h'   => $last_24h,
            'avg_score_24h'    => round( $avg_last_24, 1 ),
        );
    }
}
