<?php
/**
 * REST API endpoints for the Ultimate NAT & Port Checker plugin.
 *
 * @package UltimateNATPortChecker
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('UNPC_Ajax')) {
    /**
     * Registers and handles REST requests.
     */
    class UNPC_Ajax {
        /**
         * Maximum number of ports that can be checked in a single request.
         */
        const MAX_PORTS = 40;

        /**
         * Constructor.
         */
        public function __construct() {
            add_action('rest_api_init', array($this, 'register_rest_routes'));
        }

        /**
         * Register REST API routes.
         *
         * @return void
         */
        public function register_rest_routes() {
            register_rest_route(
                'unpc/v1',
                '/port-check',
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'handle_port_check'),
                    'permission_callback' => array($this, 'verify_nonce'),
                    'args' => array(
                        'host' => array(
                            'required' => false,
                            'sanitize_callback' => array($this, 'sanitize_host'),
                        ),
                        'ports' => array(
                            'required' => true,
                            'validate_callback' => array($this, 'validate_ports_argument'),
                        ),
                        'timeout' => array(
                            'required' => false,
                            'default' => 5,
                            'validate_callback' => function ($value) {
                                $value = absint($value);
                                return ($value >= 1 && $value <= 30);
                            },
                        ),
                    ),
                )
            );
        }

        /**
         * Verify REST request nonce.
         *
         * @param WP_REST_Request $request Request data.
         * @return bool
         */
        public function verify_nonce($request) {
            $nonce = $request->get_header('X-WP-Nonce');

            if (!$nonce) {
                return false;
            }

            return (bool) wp_verify_nonce($nonce, 'wp_rest');
        }

        /**
         * Validate the ports argument structure.
         *
         * @param mixed            $value   Ports parameter value.
         * @param WP_REST_Request  $request Request object.
         * @param string           $param   Parameter name.
         * @return bool
         */
        public function validate_ports_argument($value, $request, $param) {
            if (!is_array($value) || empty($value)) {
                return false;
            }

            if (count($value) > self::MAX_PORTS) {
                return false;
            }

            foreach ($value as $entry) {
                if (!isset($entry['port'])) {
                    return false;
                }

                $port = absint($entry['port']);
                if ($port < 1 || $port > 65535) {
                    return false;
                }

                if (isset($entry['protocol']) && !in_array(strtolower($entry['protocol']), array('tcp', 'udp'), true)) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Sanitize host input.
         *
         * @param string $value Raw host value.
         * @return string
         */
        public function sanitize_host($value) {
            $value = sanitize_text_field($value);

            if (empty($value) || 'auto' === strtolower($value)) {
                return ''; // Will default to client IP.
            }

            return $value;
        }

        /**
         * Handle port check requests.
         *
         * @param WP_REST_Request $request Request instance.
         * @return WP_REST_Response|WP_Error
         */
        public function handle_port_check(WP_REST_Request $request) {
            $settings = unpc_get_settings();

            $timeout = (int) $request->get_param('timeout');
            $timeout = ($timeout >= 1 && $timeout <= 30) ? $timeout : (int) $settings['api_timeout'];

            $host = $request->get_param('host');
            if (empty($host)) {
                $host = $this->get_client_ip();
            }

            $resolved = $this->resolve_host($host);
            if (empty($resolved)) {
                return new WP_Error(
                    'unpc_invalid_host',
                    __('We could not resolve the host you provided. Please double-check the IP or hostname.', 'ultimate-nat-port-checker'),
                    array('status' => 400)
                );
            }

            $ports_input = $request->get_param('ports');
            $results = array();
            $open_count = 0;

            foreach ($ports_input as $entry) {
                $port = absint($entry['port']);
                $protocol = isset($entry['protocol']) ? strtolower(sanitize_text_field($entry['protocol'])) : 'tcp';

                $result = array(
                    'port' => $port,
                    'protocol' => strtoupper($protocol),
                    'service' => $this->get_common_service($port),
                    'status' => 'unknown',
                    'response_time' => null,
                    'message' => '',
                );

                if ('tcp' === $protocol) {
                    $check = $this->check_tcp_port($resolved, $port, $timeout);
                    $result['status'] = $check['open'] ? 'open' : 'closed';
                    $result['response_time'] = $check['response_time'];
                    $result['message'] = $check['message'];

                    if ($check['open']) {
                        $open_count++;
                    }
                } elseif ('udp' === $protocol) {
                    $check = $this->check_udp_port($resolved, $port, $timeout);
                    $result['status'] = $check['status'];
                    $result['message'] = $check['message'];
                }

                $results[] = $result;
            }

            $payload = array(
                'success' => true,
                'host' => $host,
                'resolved_host' => $resolved,
                'results' => $results,
                'summary' => array(
                    'checked' => count($results),
                    'open' => $open_count,
                    'closed' => count($results) - $open_count,
                ),
                'timestamp' => current_time('mysql'),
            );

            return new WP_REST_Response($payload, 200);
        }

        /**
         * Attempt to resolve a host to an IP address.
         *
         * @param string $host Hostname or IP.
         * @return string|false
         */
        private function resolve_host($host) {
            $host = trim($host);

            if (filter_var($host, FILTER_VALIDATE_IP)) {
                return $host;
            }

            $resolved = @gethostbyname($host);
            if ($resolved === $host) {
                return false;
            }

            return $resolved;
        }

        /**
         * Retrieve the client IP address with best-effort checks.
         *
         * @return string
         */
        private function get_client_ip() {
            $keys = array(
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'HTTP_CF_CONNECTING_IP',
                'REMOTE_ADDR',
            );

            foreach ($keys as $key) {
                if (!empty($_SERVER[$key])) {
                    $ip_list = explode(',', $_SERVER[$key]);
                    foreach ($ip_list as $ip) {
                        $ip = trim($ip);
                        if (filter_var($ip, FILTER_VALIDATE_IP)) {
                            return $ip;
                        }
                    }
                }
            }

            return '0.0.0.0';
        }

        /**
         * Perform a TCP port check.
         *
         * @param string $host Host address.
         * @param int    $port Port number.
         * @param int    $timeout Timeout in seconds.
         * @return array
         */
        private function check_tcp_port($host, $port, $timeout = 5) {
            $result = array(
                'open' => false,
                'response_time' => null,
                'message' => '',
            );

            $start = microtime(true);
            $errno = 0;
            $errstr = '';

            $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
            $duration = round((microtime(true) - $start) * 1000, 2);

            if (is_resource($connection)) {
                fclose($connection);
                $result['open'] = true;
                $result['response_time'] = $duration;
                $result['message'] = __('Connection established successfully.', 'ultimate-nat-port-checker');
            } else {
                $result['open'] = false;
                $result['response_time'] = null;
                $result['message'] = !empty($errstr) ? $errstr : __('Unable to establish connection.', 'ultimate-nat-port-checker');
            }

            return $result;
        }

        /**
         * Provide informational status for UDP ports.
         *
         * @param string $host Host address.
         * @param int    $port Port number.
         * @param int    $timeout Timeout value.
         * @return array
         */
        private function check_udp_port($host, $port, $timeout) {
            $result = array(
                'status' => 'info',
                'message' => __('UDP availability is best confirmed from the client side. Consider using in-game diagnostics or router tools for verification.', 'ultimate-nat-port-checker'),
            );

            $socket = @stream_socket_client('udp://' . $host . ':' . $port, $errno, $errstr, $timeout);

            if ($socket) {
                stream_set_timeout($socket, $timeout);
                fwrite($socket, chr(0));
                $meta = stream_get_meta_data($socket);
                fclose($socket);

                if (!empty($meta['timed_out'])) {
                    $result['status'] = 'indeterminate';
                    $result['message'] = __('No response received – UDP services may still be operating normally.', 'ultimate-nat-port-checker');
                } else {
                    $result['status'] = 'indeterminate';
                    $result['message'] = __('A UDP probe was sent but confirmation is not guaranteed.', 'ultimate-nat-port-checker');
                }
            }

            return $result;
        }

        /**
         * Retrieve the friendly service name for a given port.
         *
         * @param int $port Port number.
         * @return string
         */
        private function get_common_service($port) {
            $services = array(
                20 => 'FTP Data',
                21 => 'FTP Control',
                22 => 'SSH',
                23 => 'Telnet',
                25 => 'SMTP',
                53 => 'DNS',
                67 => 'DHCP',
                68 => 'DHCP',
                80 => 'HTTP',
                110 => 'POP3',
                123 => 'NTP',
                143 => 'IMAP',
                161 => 'SNMP',
                1935 => 'RTMP',
                3074 => 'Xbox Live',
                3478 => 'STUN/TURN',
                3479 => 'PlayStation Network',
                3480 => 'PlayStation Network',
                3659 => 'EA Online',
                4500 => 'IPSec NAT-T',
                49000 => 'UPnP Control',
                5432 => 'PostgreSQL',
                8080 => 'HTTP Alternate',
                8443 => 'HTTPS Alternate',
                9295 => 'PS Remote Play',
            );

            return isset($services[$port]) ? $services[$port] : __('Custom', 'ultimate-nat-port-checker');
        }
    }
}
