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

			$out[] = array(
				'id'        => $p['id'],
				'name'      => $p['name'],
				'slug'      => $p['slug'],
				'weights'   => $p['weights'],
				'endpoints' => array_values( array_map( function( $e ) {
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
}
