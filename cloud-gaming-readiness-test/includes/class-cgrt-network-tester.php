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

        $method     = isset( $endpoint['method'] ) ? strtoupper( (string) $endpoint['method'] ) : 'GET';
        $timeout_ms = isset( $endpoint['timeout_ms'] ) ? (int) $endpoint['timeout_ms'] : 6500;
        $timeout_ms = max( 5000, min( 7000, $timeout_ms ) );

        if ( ! in_array( $method, array( 'GET', 'HEAD' ), true ) ) {
            $method = 'GET';
        }

        clg_debug_log( 'Starting endpoint test batch', array(
            'url'        => $url,
            'method'     => $method,
            'samples'    => $samples,
            'timeout_ms' => $timeout_ms,
        ) );

        // Intentionally do not sleep between samples: the frontend already paces the display.
        // This keeps the UX duration stable while still capturing repeated DNS/TCP/HTTP timings.
        $results         = array();
        $success_count   = 0;
        $failure_count   = 0;

        for ( $i = 0; $i < $samples; $i++ ) {
            $sample = self::measure_single( $url, $method, $timeout_ms );
            $results[] = $sample;

            if ( ! empty( $sample['ok'] ) ) {
                $success_count++;
            } else {
                $failure_count++;
            }
        }

        clg_debug_log( 'Endpoint test batch complete', array(
            'url'      => $url,
            'total'    => count( $results ),
            'success'  => $success_count,
            'failures' => $failure_count,
        ) );

        return array(
            'ok'      => true,
            'samples' => $results,
        );
    }

    /**
     * Measure single request timing (DNS + TCP + HTTP).
     *
     * @param string $url        URL to test.
     * @param string $method     HTTP method (GET or HEAD).
     * @param int    $timeout_ms Timeout in milliseconds.
     * @return array
     */
    private static function measure_single( $url, $method = 'GET', $timeout_ms = 6500 ) {
        $parsed = wp_parse_url( $url );
        if ( ! $parsed || ! isset( $parsed['host'] ) ) {
            self::log_debug( 'Invalid URL', array( 'url' => $url ) );
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

        self::log_debug( 'DNS lookup', array( 'host' => $host, 'ip' => $ip, 'dns_ms' => round( $dns_ms, 2 ) ) );

        if ( $ip === $host ) {
            self::log_debug( 'DNS resolution failed', array( 'host' => $host ) );
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

        self::log_debug( 'TCP handshake', array( 'ip' => $ip, 'port' => $port, 'tcp_ms' => round( $tcp_ms, 2 ), 'success' => (bool) $socket ) );

        if ( ! $socket ) {
            self::log_debug( 'TCP connection failed', array( 'errno' => $errno, 'errstr' => $errstr ) );
            return array(
                'ok'     => false,
                'rttMs'  => ( microtime( true ) - $t_start ) * 1000,
                'error'  => 'TCP connection failed: ' . $errstr,
                'dnsMs'  => $dns_ms,
                'tcpMs'  => $tcp_ms,
                'httpMs' => 0,
            );
        }

        fclose( $socket );

        // HTTP request.
        $http_start = microtime( true );
        $args       = array(
            'timeout'     => $timeout_ms / 1000,
            'redirection' => 5,
            'httpversion' => '1.1',
            'user-agent'  => 'WordPress Cloud Gaming Readiness Test/1.0',
            'sslverify'   => true,
            'headers'     => array(
                'Accept' => '*/*',
            ),
        );

        if ( 'HEAD' === $method ) {
            $response = wp_remote_head( $url, $args );
        } else {
            $response = wp_remote_get( $url, $args );
        }

        $http_end   = microtime( true );
        $http_ms    = ( $http_end - $http_start ) * 1000;
        $is_error   = is_wp_error( $response );
        $t_end      = microtime( true );
        $total_ms   = ( $t_end - $t_start ) * 1000;
        $status     = ! $is_error ? wp_remote_retrieve_response_code( $response ) : 0;
        $body_len   = ! $is_error ? strlen( wp_remote_retrieve_body( $response ) ) : 0;

        self::log_debug(
            'HTTP request',
            array(
                'method'   => $method,
                'http_ms'  => round( $http_ms, 2 ),
                'status'   => $status,
                'body_len' => $body_len,
                'is_error' => $is_error,
            )
        );

        if ( $is_error ) {
            $error_msg = $response->get_error_message();
            self::log_debug( 'HTTP request error', array( 'error' => $error_msg ) );
            return array(
                'ok'     => false,
                'rttMs'  => $total_ms,
                'error'  => $error_msg,
                'dnsMs'  => $dns_ms,
                'tcpMs'  => $tcp_ms,
                'httpMs' => $http_ms,
            );
        }

        // Success: Accept any 2xx or 3xx status as "ok" since redirects were followed.
        $success = $status >= 200 && $status < 400;

        return array(
            'ok'     => $success,
            'rttMs'  => $total_ms,
            'status' => $status,
            'dnsMs'  => $dns_ms,
            'tcpMs'  => $tcp_ms,
            'httpMs' => $http_ms,
        );
    }

    /**
     * Debug logging.
     *
     * @param string $message Message.
     * @param array  $context Context data.
     * @return void
     */
    private static function log_debug( $message, $context = array() ) {
        if ( function_exists( 'clg_debug_log' ) ) {
            clg_debug_log( $message, $context );
            return;
        }

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            $line = '[CloudLoadout Test] ' . $message;
            if ( ! empty( $context ) ) {
                $line .= ' | ' . wp_json_encode( $context );
            }
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            error_log( $line );
        }
    }

    /**
     * Test status endpoint (diagnostic only).
     *
     * @param array $endpoint Endpoint data.
     * @return array Status info (never blocks frontend results).
     */
    public static function test_status_endpoint( $endpoint ) {
        $url = isset( $endpoint['url'] ) ? $endpoint['url'] : '';
        if ( empty( $url ) ) {
            return array(
                'reachable' => false,
                'message'   => 'No URL provided',
            );
        }

        $method     = isset( $endpoint['method'] ) ? strtoupper( (string) $endpoint['method'] ) : 'GET';
        $timeout_ms = 5000;

        self::log_debug( 'Status endpoint check (diagnostic only)', array( 'url' => $url ) );

        $result = self::measure_single( $url, $method, $timeout_ms );

        if ( $result['ok'] ) {
            return array(
                'reachable' => true,
                'message'   => 'Service reachable',
                'rtt_ms'    => $result['rttMs'],
            );
        }

        return array(
            'reachable' => false,
            'message'   => 'Service may be degraded: ' . ( $result['error'] ?? 'Unknown' ),
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
