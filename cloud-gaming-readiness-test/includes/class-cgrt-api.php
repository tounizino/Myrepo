<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CGRT_API {

    /**
     * @var CGRT_API
     */
    private static $instance;

    /**
     * @return CGRT_API
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register REST routes.
     */
    public function register_routes() {
        register_rest_route(
            'cgrt/v1',
            '/config',
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_config' ),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'cgrt/v1',
            '/submit',
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'submit_result' ),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'cgrt/v1',
            '/test',
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'run_server_test' ),
                'permission_callback' => '__return_true',
            )
        );
    }

    /**
     * Get test configuration.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function get_config( $request ) {
        $platforms = CGRT_Database::get_platforms_with_endpoints();
        $settings  = CGRT_Database::get_settings();

        $out = array();
        foreach ( $platforms as $p ) {
            if ( ! $p['enabled'] ) {
                continue;
            }

            $enabled_endpoints = array_filter( $p['endpoints'], function( $e ) {
                return $e['enabled'];
            } );

            if ( empty( $enabled_endpoints ) ) {
                continue;
            }

            // Frontend only uses service_entry endpoints for faster test.
            $service_entry_endpoints = array_filter( $enabled_endpoints, function( $e ) {
                $type = isset( $e['endpoint_type'] ) ? $e['endpoint_type'] : 'service_entry';
                return 'service_entry' === $type;
            } );

            // If no service entry found, fallback to first endpoint.
            if ( empty( $service_entry_endpoints ) ) {
                $service_entry_endpoints = array_slice( $enabled_endpoints, 0, 1 );
            }

            $has_disclaimer = false;
            foreach ( $enabled_endpoints as $e ) {
                if ( ! empty( $e['has_disclaimer'] ) ) {
                    $has_disclaimer = true;
                    break;
                }
            }

            $out[] = array(
                'id'             => $p['id'],
                'name'           => $p['name'],
                'slug'           => $p['slug'],
                'weights'        => $p['weights'],
                'hasDisclaimer'  => $has_disclaimer,
                'endpoints'      => array_values( array_map( function( $e ) {
                    return array(
                        'id'         => $e['id'],
                        'region'     => $e['region'],
                        'url'        => $e['endpoint_url'],
                        'method'     => $e['method'],
                        'timeout_ms' => $e['timeout_ms'],
                    );
                }, $service_entry_endpoints ) ),
            );
        }

        return new WP_REST_Response(
            array(
                'platforms' => $out,
                'settings'  => array(
                    'testDurationSeconds' => (int) $settings['test_duration_seconds'],
                    'sampleIntervalMs'    => (int) $settings['sample_interval_ms'],
                    'endpointPickPings'   => (int) $settings['endpoint_pick_pings'],
                    'spikeMs'             => (int) $settings['spike_ms'],
                    'tiers'               => $settings['tiers'],
                ),
            ),
            200
        );
    }

    /**
     * Submit test result.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function submit_result( $request ) {
        $body = $request->get_json_params();

        if ( empty( $body ) || ! is_array( $body ) ) {
            return new WP_REST_Response(
                array( 'error' => 'Invalid payload' ),
                400
            );
        }

        $platform_id = isset( $body['platformId'] ) ? (int) $body['platformId'] : null;
        $endpoint_id = isset( $body['endpointId'] ) ? (int) $body['endpointId'] : null;
        $score       = isset( $body['score'] ) ? (int) $body['score'] : 0;
        $tier        = isset( $body['tier'] ) ? sanitize_text_field( $body['tier'] ) : '';
        $metrics     = isset( $body['metrics'] ) && is_array( $body['metrics'] ) ? $body['metrics'] : array();

        $payload = array(
            'platform_id' => $platform_id,
            'endpoint_id' => $endpoint_id,
            'score'       => $score,
            'tier'        => $tier,
            'metrics'     => $metrics,
        );

        $result_id = CGRT_Database::insert_result( $payload );

        return new WP_REST_Response(
            array(
                'success'   => true,
                'result_id' => $result_id,
            ),
            200
        );
    }

    /**
     * Run server-side network test.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function run_server_test( $request ) {
        $body = $request->get_json_params();

        if ( empty( $body ) || ! is_array( $body ) ) {
            return new WP_REST_Response(
                array( 'error' => 'Invalid payload' ),
                400
            );
        }

        $ip          = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        $endpoint_id = isset( $body['endpointId'] ) ? (int) $body['endpointId'] : 0;
        $settings    = CGRT_Database::get_settings();

        if ( $endpoint_id <= 0 ) {
            return new WP_REST_Response(
                array( 'error' => 'Endpoint ID required' ),
                400
            );
        }

        // Cache short-term results to reduce load and avoid unnecessary rate limiting.
        $cache_key = 'cgrt_test_' . md5( $ip . '|' . $endpoint_id );
        $cached    = get_transient( $cache_key );
        if ( is_array( $cached ) && isset( $cached['samples'] ) && is_array( $cached['samples'] ) ) {
            return new WP_REST_Response(
                array(
                    'success' => true,
                    'samples' => $cached['samples'],
                ),
                200
            );
        }

        if ( ! CGRT_Network_Tester::check_rate_limit( $ip ) ) {
            return new WP_REST_Response(
                array( 'error' => 'Rate limit exceeded. Please wait before retrying.' ),
                429
            );
        }

        global $wpdb;
        $tables = CGRT_Database::tables();
        $row    = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$tables['endpoints']} WHERE id = %d AND enabled = 1", $endpoint_id ),
            ARRAY_A
        );

        if ( ! $row ) {
            return new WP_REST_Response(
                array( 'error' => 'Endpoint not found or disabled' ),
                404
            );
        }

        $endpoint = array(
            'id'          => (int) $row['id'],
            'platform_id' => (int) $row['platform_id'],
            'url'         => esc_url_raw( $row['endpoint_url'] ),
            'method'      => isset( $row['method'] ) ? strtoupper( sanitize_text_field( $row['method'] ) ) : 'GET',
            // Force server-side timeout to the recommended range.
            'timeout_ms'  => 6500,
        );

        $duration_sec = isset( $settings['test_duration_seconds'] ) ? (int) $settings['test_duration_seconds'] : 18;
        $interval_ms  = isset( $settings['sample_interval_ms'] ) ? (int) $settings['sample_interval_ms'] : 350;
        $samples      = max( 10, (int) floor( $duration_sec / ( $interval_ms / 1000 ) ) );
        $interval_sec = $interval_ms / 1000;

        // Optional status endpoint used as fallback/debugging (not exposed to frontend selection).
        $status_row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$tables['endpoints']} WHERE platform_id = %d AND enabled = 1 AND endpoint_type = %s ORDER BY is_default DESC, id ASC LIMIT 1",
                (int) $row['platform_id'],
                'status'
            ),
            ARRAY_A
        );

        $status_endpoint = null;
        if ( $status_row && ! empty( $status_row['endpoint_url'] ) ) {
            $status_endpoint = array(
                'id'          => (int) $status_row['id'],
                'platform_id' => (int) $status_row['platform_id'],
                'url'         => esc_url_raw( $status_row['endpoint_url'] ),
                'method'      => isset( $status_row['method'] ) ? strtoupper( sanitize_text_field( $status_row['method'] ) ) : 'GET',
                'timeout_ms'  => 6500,
            );
        }

        $result = CGRT_Network_Tester::test_endpoint( $endpoint, $samples, $interval_sec );

        if ( ! $result['ok'] ) {
            return new WP_REST_Response(
                array( 'error' => isset( $result['error'] ) ? $result['error'] : 'Test failed' ),
                500
            );
        }

        // If service entry blocks or fails for some samples, try a limited fallback to the status endpoint.
        if ( $status_endpoint && ! empty( $result['samples'] ) ) {
            $fallback_used = 0;
            foreach ( $result['samples'] as $idx => $sample ) {
                if ( $fallback_used >= 5 ) {
                    break;
                }
                if ( ! empty( $sample['ok'] ) ) {
                    continue;
                }
                $fallback = CGRT_Network_Tester::test_endpoint( $status_endpoint, 1, $interval_sec );
                if ( ! empty( $fallback['samples'] ) && ! empty( $fallback['samples'][0]['ok'] ) ) {
                    $result['samples'][ $idx ] = $fallback['samples'][0];
                    $fallback_used++;
                }
            }
        }

        set_transient( $cache_key, array( 'samples' => $result['samples'] ), 30 );

        return new WP_REST_Response(
            array(
                'success' => true,
                'samples' => $result['samples'],
            ),
            200
        );
    }
}
