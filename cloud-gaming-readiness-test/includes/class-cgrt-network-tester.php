<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CGRT_Network_Tester {

	/**
	 * Run server-side network test for endpoint.
	 *
	 * @param array $endpoint Endpoint data.
	 * @param int   $samples  Number of samples.
	 * @param float $interval_sec Interval between samples.
	 * @return array
	 */
	public static function test_endpoint( $endpoint, $samples = 15, $interval_sec = 0.35 ) {
		$url = isset( $endpoint['url'] ) ? $endpoint['url'] : '';
		if ( empty( $url ) ) {
			return array(
				'ok'      => false,
				'error'   => 'No URL provided',
				'samples' => array(),
			);
		}

		$results = array();

		for ( $i = 0; $i < $samples; $i++ ) {
			$sample = self::measure_single( $url, 2500 );
			$results[] = $sample;

			if ( $i < $samples - 1 ) {
				// Sleep between samples.
				usleep( (int) ( $interval_sec * 1000000 ) );
			}
		}

		return array(
			'ok'      => true,
			'samples' => $results,
		);
	}

	/**
	 * Measure single request timing (DNS + TCP + HTTP).
	 *
	 * @param string $url        URL to test.
	 * @param int    $timeout_ms Timeout in milliseconds.
	 * @return array
	 */
	private static function measure_single( $url, $timeout_ms = 2500 ) {
		$parsed = wp_parse_url( $url );
		if ( ! $parsed || ! isset( $parsed['host'] ) ) {
			return array(
				'ok'     => false,
				'rttMs'  => 0,
				'error'  => 'Invalid URL',
				'dnsMs'  => 0,
				'tcpMs'  => 0,
				'httpMs' => 0,
			);
		}

		$host   = $parsed['host'];
		$scheme = isset( $parsed['scheme'] ) ? $parsed['scheme'] : 'https';
		$port   = isset( $parsed['port'] ) ? $parsed['port'] : ( $scheme === 'https' ? 443 : 80 );

		$t_start = microtime( true );

		// DNS resolution.
		$dns_start = microtime( true );
		$ip        = gethostbyname( $host );
		$dns_end   = microtime( true );
		$dns_ms    = ( $dns_end - $dns_start ) * 1000;

		if ( $ip === $host ) {
			return array(
				'ok'     => false,
				'rttMs'  => 0,
				'error'  => 'DNS resolution failed',
				'dnsMs'  => $dns_ms,
				'tcpMs'  => 0,
				'httpMs' => 0,
			);
		}

		// TCP handshake.
		$tcp_start = microtime( true );
		$socket    = @fsockopen( $ip, $port, $errno, $errstr, $timeout_ms / 1000 );
		$tcp_end   = microtime( true );
		$tcp_ms    = ( $tcp_end - $tcp_start ) * 1000;

		if ( ! $socket ) {
			return array(
				'ok'     => false,
				'rttMs'  => ( microtime( true ) - $t_start ) * 1000,
				'error'  => 'TCP connection failed',
				'dnsMs'  => $dns_ms,
				'tcpMs'  => $tcp_ms,
				'httpMs' => 0,
			);
		}

		fclose( $socket );

		// HTTP request.
		$http_start = microtime( true );
		$response   = wp_remote_get(
			$url,
			array(
				'timeout'     => $timeout_ms / 1000,
				'redirection' => 3,
				'httpversion' => '1.1',
				'user-agent'  => 'WordPress Cloud Gaming Readiness Test',
				'sslverify'   => true,
			)
		);
		$http_end   = microtime( true );
		$http_ms    = ( $http_end - $http_start ) * 1000;

		$is_error = is_wp_error( $response );
		$t_end    = microtime( true );
		$total_ms = ( $t_end - $t_start ) * 1000;

		if ( $is_error ) {
			return array(
				'ok'     => false,
				'rttMs'  => $total_ms,
				'error'  => $response->get_error_message(),
				'dnsMs'  => $dns_ms,
				'tcpMs'  => $tcp_ms,
				'httpMs' => $http_ms,
			);
		}

		return array(
			'ok'     => true,
			'rttMs'  => $total_ms,
			'status' => wp_remote_retrieve_response_code( $response ),
			'dnsMs'  => $dns_ms,
			'tcpMs'  => $tcp_ms,
			'httpMs' => $http_ms,
		);
	}

	/**
	 * Rate limit check (transient-based).
	 *
	 * @param string $ip IP address.
	 * @return bool True if allowed, false if rate limited.
	 */
	public static function check_rate_limit( $ip ) {
		$key   = 'cgrt_rate_' . md5( $ip );
		$count = (int) get_transient( $key );

		if ( $count >= 3 ) {
			return false;
		}

		set_transient( $key, $count + 1, 60 );
		return true;
	}
}
