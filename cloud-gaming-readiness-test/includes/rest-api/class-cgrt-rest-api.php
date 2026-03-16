<?php
/**
 * REST API handler.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * REST API class.
 *
 * @since 1.0.0
 */
class CGRT_REST_API {

    /**
     * The plugin name.
     *
     * @since 1.0.0
     * @var string
     */
    private $plugin_name;

    /**
     * The plugin version.
     *
     * @since 1.0.0
     * @var string
     */
    private $version;

    /**
     * Initialize the class.
     *
     * @since 1.0.0
     * @param string $plugin_name The plugin name.
     * @param string $version The plugin version.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register REST API routes.
     *
     * @since 1.0.0
     */
    public function register_routes() {
        $namespace = 'cloud-gaming-test/v1';

        // Test endpoint - run speed test via server.
        register_rest_route(
            $namespace,
            '/speed-test',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'handle_speed_test' ),
                'permission_callback' => array( $this, 'check_permissions' ),
                'args'                => array(
                    'test_type' => array(
                        'required'          => false,
                        'default'           => 'all',
                        'sanitize_callback' => 'sanitize_text_field',
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                ),
            )
        );

        // Cloudflare API endpoint.
        register_rest_route(
            $namespace,
            '/cloudflare-test',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'handle_cloudflare_test' ),
                'permission_callback' => array( $this, 'check_permissions' ),
            )
        );

        // Settings endpoint - get public settings.
        register_rest_route(
            $namespace,
            '/settings',
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_settings' ),
                'permission_callback' => '__return_true', // Public endpoint.
            )
        );

        // Results endpoint - save test results.
        register_rest_route(
            $namespace,
            '/save-results',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'save_results' ),
                'permission_callback' => '__return_true', // Public endpoint.
                'args'                => array(
                    'results' => array(
                        'required'          => true,
                        'sanitize_callback' => array( $this, 'sanitize_results' ),
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                ),
            )
        );
    }

    /**
     * Check if the user has permission to access the API.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The request object.
     * @return bool True if authorized, false otherwise.
     */
    public function check_permissions( $request ) {
        // Verify nonce.
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'cgrt_public_nonce' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Handle speed test request.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response Response object.
     */
    public function handle_speed_test( $request ) {
        $test_type = $request->get_param( 'test_type' );

        // Check if Cloudflare is enabled.
        $cloudflare_enabled = get_option( 'cgrt_cloudflare_enabled', false );

        if ( $cloudflare_enabled ) {
            $cloudflare_api = new CGRT_Cloudflare_API();
            $result = $cloudflare_api->run_speed_test();
        } else {
            // Return server-side diagnostics.
            $result = $this->run_server_diagnostics();
        }

        return new WP_REST_Response(
            array(
                'success' => true,
                'data'    => $result,
            ),
            200
        );
    }

    /**
     * Handle Cloudflare test request.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response Response object.
     */
    public function handle_cloudflare_test( $request ) {
        $cloudflare_api = new CGRT_Cloudflare_API();
        $result = $cloudflare_api->run_speed_test();

        if ( is_wp_error( $result ) ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'error'   => $result->get_error_message(),
                ),
                400
            );
        }

        return new WP_REST_Response(
            array(
                'success' => true,
                'data'    => $result,
            ),
            200
        );
    }

    /**
     * Get public settings.
     *
     * @since 1.0.0
     * @return WP_REST_Response Response object.
     */
    public function get_settings() {
        $settings = array(
            'themeColor'     => get_option( 'cgrt_theme_color', '#6366f1' ),
            'defaultMode'    => get_option( 'cgrt_default_mode', 'dark' ),
            'cloudflareEnabled' => get_option( 'cgrt_cloudflare_enabled', false ),
            'enableAdvanced' => get_option( 'cgrt_enable_advanced', false ),
            'pingCount'      => get_option( 'cgrt_ping_count', 10 ),
            'testTimeout'    => get_option( 'cgrt_test_timeout', 30 ),
            'thresholds'     => array(
                'latency' => array(
                    'excellent' => get_option( 'cgrt_latency_excellent', 20 ),
                    'good'      => get_option( 'cgrt_latency_good', 50 ),
                    'fair'      => get_option( 'cgrt_latency_fair', 100 ),
                ),
                'jitter' => array(
                    'excellent' => get_option( 'cgrt_jitter_excellent', 5 ),
                    'good'      => get_option( 'cgrt_jitter_good', 10 ),
                    'fair'      => get_option( 'cgrt_jitter_fair', 20 ),
                ),
                'packetLoss' => array(
                    'good' => get_option( 'cgrt_packet_loss_good', 1 ),
                    'fair' => get_option( 'cgrt_packet_loss_fair', 3 ),
                ),
            ),
        );

        return new WP_REST_Response(
            array(
                'success'  => true,
                'settings' => $settings,
            ),
            200
        );
    }

    /**
     * Save test results.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response Response object.
     */
    public function save_results( $request ) {
        $results = $request->get_param( 'results' );

        // Store results in transient for analytics (24 hours).
        $transient_key = 'cgrt_results_' . md5( wp_json_encode( $results ) );
        set_transient( $transient_key, $results, DAY_IN_SECONDS );

        return new WP_REST_Response(
            array(
                'success' => true,
                'message' => __( 'Results saved successfully.', 'cloud-gaming-readiness-test' ),
            ),
            200
        );
    }

    /**
     * Sanitize results data.
     *
     * @since 1.0.0
     * @param array $results Raw results data.
     * @return array Sanitized results.
     */
    public function sanitize_results( $results ) {
        $sanitized = array();

        if ( isset( $results['latency'] ) ) {
            $sanitized['latency'] = floatval( $results['latency'] );
        }
        if ( isset( $results['jitter'] ) ) {
            $sanitized['jitter'] = floatval( $results['jitter'] );
        }
        if ( isset( $results['packetLoss'] ) ) {
            $sanitized['packetLoss'] = floatval( $results['packetLoss'] );
        }
        if ( isset( $results['download'] ) ) {
            $sanitized['download'] = floatval( $results['download'] );
        }
        if ( isset( $results['upload'] ) ) {
            $sanitized['upload'] = floatval( $results['upload'] );
        }

        return $sanitized;
    }

    /**
     * Run server-side diagnostics.
     *
     * @since 1.0.0
     * @return array Diagnostic results.
     */
    private function run_server_diagnostics() {
        // Simulate server-side test.
        return array(
            'server'          => get_option( 'cgrt_home_url', home_url() ),
            'timestamp'       => current_time( 'mysql' ),
            'php_version'     => PHP_VERSION,
            'server_info'     => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( $_SERVER['SERVER_SOFTWARE'] ) : '',
        );
    }
}
