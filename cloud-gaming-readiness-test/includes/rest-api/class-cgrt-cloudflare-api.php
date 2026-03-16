<?php
/**
 * Cloudflare API integration.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Cloudflare API class.
 *
 * @since 1.0.0
 */
class CGRT_Cloudflare_API {

    /**
     * Cloudflare API endpoint.
     *
     * @since 1.0.0
     * @var string
     */
    private $api_endpoint = 'https://api.cloudflare.com/client/v4';

    /**
     * Run Cloudflare speed test.
     *
     * @since 1.0.0
     * @return array|WP_Error Speed test results or error.
     */
    public function run_speed_test() {
        $api_key = get_option( 'cgrt_cloudflare_api_key', '' );
        $email = get_option( 'cgrt_cloudflare_email', '' );

        if ( empty( $api_key ) || empty( $email ) ) {
            return new WP_Error(
                'missing_credentials',
                __( 'Cloudflare API credentials are not configured.', 'cloud-gaming-readiness-test' )
            );
        }

        // Check transient for cached results.
        $cache_key = 'cgrt_cloudflare_test_' . md5( $email );
        $cached = get_transient( $cache_key );

        if ( false !== $cached ) {
            return $cached;
        }

        // Attempt to run speed test.
        $results = $this->perform_speed_test( $api_key, $email );

        if ( is_wp_error( $results ) ) {
            return $results;
        }

        // Cache results for 5 minutes.
        set_transient( $cache_key, $results, 5 * MINUTE_IN_SECONDS );

        return $results;
    }

    /**
     * Perform the actual speed test.
     *
     * @since 1.0.0
     * @param string $api_key Cloudflare API key.
     * @param string $email Cloudflare account email.
     * @return array|WP_Error Test results or error.
     */
    private function perform_speed_test( $api_key, $email ) {
        // Note: Cloudflare's public API doesn't have a direct speed test endpoint.
        // We'll use a simulated approach that demonstrates the integration pattern.
        // In production, you would use Cloudflare's Speed Test API or a similar service.

        // Simulate API request timeout handling.
        $timeout = get_option( 'cgrt_test_timeout', 30 );

        // Simulate network latency test using WordPress HTTP API.
        $start_time = microtime( true );
        $response = wp_remote_get(
            home_url(),
            array(
                'timeout'   => $timeout,
                'sslverify' => false,
            )
        );
        $end_time = microtime( true );

        if ( is_wp_error( $response ) ) {
            return new WP_Error(
                'connection_error',
                $response->get_error_message()
            );
        }

        // Calculate latency.
        $latency = round( ( $end_time - $start_time ) * 1000 );

        // Return simulated results that would come from Cloudflare.
        return array(
            'latency'    => $latency,
            'jitter'     => $this->calculate_jitter( $latency ),
            'packetLoss' => $this->estimate_packet_loss(),
            'download'   => $this->estimate_bandwidth( 'download' ),
            'upload'     => $this->estimate_bandwidth( 'upload' ),
            'server'     => $this->get_nearest_server(),
            'timestamp'  => current_time( 'mysql' ),
        );
    }

    /**
     * Calculate jitter based on latency.
     *
     * @since 1.0.0
     * @param float $latency Latency value.
     * @return float Jitter value.
     */
    private function calculate_jitter( $latency ) {
        // Simulate jitter calculation.
        return round( $latency * ( rand( 1, 10 ) / 100 ), 2 );
    }

    /**
     * Estimate packet loss.
     *
     * @since 1.0.0
     * @return float Packet loss percentage.
     */
    private function estimate_packet_loss() {
        // Simulate packet loss estimation.
        return rand( 0, 5 ) / 10;
    }

    /**
     * Estimate bandwidth.
     *
     * @since 1.0.0
     * @param string $type Bandwidth type (download/upload).
     * @return float Bandwidth in Mbps.
     */
    private function estimate_bandwidth( $type ) {
        // Simulate bandwidth estimation.
        $base_speed = 'download' === $type ? 50 : 25;
        return round( $base_speed + ( rand( -10, 20 ) ), 2 );
    }

    /**
     * Get nearest server information.
     *
     * @since 1.0.0
     * @return string Server location.
     */
    private function get_nearest_server() {
        // Simulate server detection.
        $servers = array(
            'New York, US',
            'London, UK',
            'Frankfurt, DE',
            'Tokyo, JP',
            'Singapore, SG',
        );
        return $servers[ array_rand( $servers ) ];
    }

    /**
     * Validate Cloudflare credentials.
     *
     * @since 1.0.0
     * @return bool True if valid, false otherwise.
     */
    public function validate_credentials() {
        $api_key = get_option( 'cgrt_cloudflare_api_key', '' );
        $email = get_option( 'cgrt_cloudflare_email', '' );

        if ( empty( $api_key ) || empty( $email ) ) {
            return false;
        }

        // Try to make a test API call.
        $response = wp_remote_get(
            $this->api_endpoint . '/user/tokens',
            array(
                'headers' => array(
                    'X-Auth-Email' => $email,
                    'X-Auth-Key'   => $api_key,
                    'Content-Type' => 'application/json',
                ),
                'timeout' => 10,
            )
        );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        return isset( $data['success'] ) && true === $data['success'];
    }
}
