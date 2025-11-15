<?php
/**
 * AJAX and API handler for Internet Speed & Device Info Tool
 */

if (!defined('ABSPATH')) {
    exit;
}

class ISDIT_API_Handler {

    /** @var Internet_Speed_Device_Info_Tool */
    protected $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;

        add_action('wp_ajax_isdit_get_ip_info', array($this, 'get_ip_info'));
        add_action('wp_ajax_nopriv_isdit_get_ip_info', array($this, 'get_ip_info'));

        add_action('wp_ajax_isdit_speed_test_download', array($this, 'speed_test_download'));
        add_action('wp_ajax_nopriv_isdit_speed_test_download', array($this, 'speed_test_download'));

        add_action('wp_ajax_isdit_speed_test_upload', array($this, 'speed_test_upload'));
        add_action('wp_ajax_nopriv_isdit_speed_test_upload', array($this, 'speed_test_upload'));
    }

    /**
     * Return IP information and geolocation details.
     */
    public function get_ip_info() {
        check_ajax_referer($this->plugin->get_nonce_action(), 'nonce');

        $client_ip = $this->get_client_ip();
        $ip_data = array(
            'ip'       => $client_ip,
            'location' => array(
                'country'      => '',
                'country_code' => '',
                'region'       => '',
                'city'         => '',
                'latitude'     => 0,
                'longitude'    => 0,
                'timezone'     => '',
                'isp'          => '',
                'organization' => '',
                'postal'       => '',
            ),
        );

        $endpoint = $this->plugin->get_setting('ip_lookup_endpoint', 'https://ipapi.co/json/');
        $endpoint = $this->prepare_endpoint($endpoint, $client_ip);

        $response = wp_remote_get($endpoint, array('timeout' => 10));

        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (is_array($data)) {
                $ip_data['ip'] = isset($data['ip']) ? sanitize_text_field($data['ip']) : $client_ip;

                $ip_data['location'] = array(
                    'country'      => sanitize_text_field($this->extract_value($data, array('country_name', 'country'))),
                    'country_code' => sanitize_text_field($this->extract_value($data, array('country_code', 'countryCode'))),
                    'region'       => sanitize_text_field($this->extract_value($data, array('region', 'regionName', 'state'))),
                    'city'         => sanitize_text_field($this->extract_value($data, array('city'))),
                    'latitude'     => floatval($this->extract_value($data, array('latitude', 'lat'))),
                    'longitude'    => floatval($this->extract_value($data, array('longitude', 'lon'))),
                    'timezone'     => sanitize_text_field($this->extract_value($data, array('timezone'))),
                    'isp'          => sanitize_text_field($this->extract_value($data, array('org', 'asn', 'isp'))),
                    'organization' => sanitize_text_field($this->extract_value($data, array('org', 'asn', 'as', 'company'))),
                    'postal'       => sanitize_text_field($this->extract_value($data, array('postal', 'zip'))),
                );
            }
        }

        wp_send_json_success($ip_data);
    }

    /**
     * Stream pseudo-random bytes for download speed measurement.
     */
    public function speed_test_download() {
        check_ajax_referer($this->plugin->get_nonce_action(), 'nonce');

        $configured_size = (float) $this->plugin->get_setting('download_size_mb', 5);
        $max_size = max(0.5, min(30, $configured_size * 2));

        $requested = isset($_GET['size']) ? floatval($_GET['size']) : $configured_size;
        $requested = max(0.5, min($requested, $max_size));

        $bytes = (int) round($requested * 1024 * 1024);
        $chunk = 8192;

        nocache_headers();
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . $bytes);

        $buffer = str_repeat('0', $chunk);
        $sent = 0;

        while ($sent < $bytes) {
            $remaining = $bytes - $sent;
            $length = $remaining > $chunk ? $chunk : $remaining;
            echo substr($buffer, 0, $length);
            $sent += $length;

            if ($sent % (1024 * 256) === 0) {
                @flush();
            }

            if (connection_aborted()) {
                break;
            }
        }

        wp_die();
    }

    /**
     * Consume uploaded payload for upload speed measurement.
     */
    public function speed_test_upload() {
        check_ajax_referer($this->plugin->get_nonce_action(), 'nonce');

        $configured_size = (float) $this->plugin->get_setting('upload_size_mb', 3);
        $max_size = max(0.25, min(20, $configured_size * 2)) * 1024 * 1024; // bytes

        $input = fopen('php://input', 'rb');
        $received = 0;

        if ($input) {
            while (!feof($input)) {
                $chunk = fread($input, 1048576); // 1MB
                if ($chunk === false) {
                    break;
                }
                $received += strlen($chunk);

                if ($received > $max_size) {
                    break;
                }
            }
            fclose($input);
        }

        wp_send_json_success(array(
            'received'  => $received,
            'limit'     => (int) $max_size,
            'timestamp' => microtime(true),
        ));
    }

    /**
     * Determine the most likely client IP address.
     */
    protected function get_client_ip() {
        $keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        );

        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                foreach ($ips as $ip) {
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
     * Prepare the IP lookup endpoint, replacing placeholders when necessary.
     */
    protected function prepare_endpoint($endpoint, $ip) {
        $endpoint = trim($endpoint);
        if (empty($endpoint)) {
            return 'https://ipapi.co/json/';
        }

        if (strpos($endpoint, '{ip}') !== false) {
            return str_replace('{ip}', rawurlencode($ip), $endpoint);
        }

        // ipapi requires trailing /json/ if not provided.
        if (strpos($endpoint, 'ipapi.co') !== false && strpos($endpoint, 'json') === false) {
            $endpoint = trailingslashit($endpoint) . 'json/';
        }

        return $endpoint;
    }

    /**
     * Extract the first existing value for the provided keys from the data array.
     */
    protected function extract_value($data, $keys) {
        foreach ((array) $keys as $key) {
            if (isset($data[$key]) && '' !== $data[$key]) {
                return $data[$key];
            }
        }
        return '';
    }
}
