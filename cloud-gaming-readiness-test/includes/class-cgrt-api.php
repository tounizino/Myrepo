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
                }, $enabled_endpoints ) ),
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

        $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

        if ( ! CGRT_Network_Tester::check_rate_limit( $ip ) ) {
            return new WP_REST_Response(
                array( 'error' => 'Rate limit exceeded. Please wait before retrying.' ),
                429
            );
        }

        $endpoint_id = isset( $body['endpointId'] ) ? (int) $body['endpointId'] : 0;
        $settings    = CGRT_Database::get_settings();

        if ( $endpoint_id <= 0 ) {
            return new WP_REST_Response(
                array( 'error' => 'Endpoint ID required' ),
                400
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
            'id'         => (int) $row['id'],
            'platform_id'=> (int) $row['platform_id'],
            'url'        => esc_url_raw( $row['endpoint_url'] ),
            'method'     => isset( $row['method'] ) ? strtoupper( sanitize_text_field( $row['method'] ) ) : 'GET',
            'timeout_ms' => isset( $row['timeout_ms'] ) ? (int) $row['timeout_ms'] : 2500,
            'has_disclaimer' => ! empty( $row['has_disclaimer'] ) ? 1 : 0,
        );

        $duration_sec = isset( $settings['test_duration_seconds'] ) ? (int) $settings['test_duration_seconds'] : 18;
        $interval_ms  = isset( $settings['sample_interval_ms'] ) ? (int) $settings['sample_interval_ms'] : 350;
        $samples      = max( 10, (int) floor( $duration_sec / ( $interval_ms / 1000 ) ) );
        $interval_sec = $interval_ms / 1000;

        $result = CGRT_Network_Tester::test_endpoint( $endpoint, $samples, $interval_sec );

        if ( ! $result['ok'] ) {
            return new WP_REST_Response(
                array( 'error' => isset( $result['error'] ) ? $result['error'] : 'Test failed' ),
                500
            );
        }

        return new WP_REST_Response(
            array(
                'success' => true,
                'samples' => $result['samples'],
            ),
            200
        );
    }
}
