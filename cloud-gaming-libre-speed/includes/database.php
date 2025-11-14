<?php
/**
 * Database and persistence helper for Cloud Gaming Speed Test
 *
 * Handles plugin options, default seeds, and historic test storage.
 * All data is stored using the WordPress options API except the
 * recorded test history which lives in a dedicated custom table.
 *
 * @package CloudGamingSpeedTest
 */

if (!defined('ABSPATH')) {
    exit;
}

class CGST_Database {

    const RESULTS_TABLE     = 'cgst_results';
    const SERVERS_OPTION    = 'cgst_servers';
    const ARTICLES_OPTION   = 'cgst_articles';
    const SETTINGS_OPTION   = 'cgst_settings';

    /**
     * Create the historic results table.
     * Called on plugin activation.
     */
    public static function create_tables() {
        global $wpdb;

        $table_name      = $wpdb->prefix . self::RESULTS_TABLE;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            server_id varchar(191) NOT NULL,
            server_name varchar(191) NOT NULL,
            download_mbps float NOT NULL DEFAULT 0,
            upload_mbps float NOT NULL DEFAULT 0,
            ping_ms float NOT NULL DEFAULT 0,
            jitter_ms float NOT NULL DEFAULT 0,
            packet_loss float NOT NULL DEFAULT 0,
            rating varchar(20) NOT NULL,
            recommendation varchar(200) NOT NULL,
            settings_json longtext NULL,
            PRIMARY KEY  (id),
            KEY server_id (server_id)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Seed default server presets when none exist.
     */
    public static function insert_default_servers() {
        $servers = get_option(self::SERVERS_OPTION);
        if (!empty($servers) && is_array($servers)) {
            return;
        }

        $defaults = array(
            array(
                'id'            => 'us-east',
                'name'          => __('US East - Ashburn', 'cloud-gaming-speed-test'),
                'location'      => __('Ashburn, VA, USA', 'cloud-gaming-speed-test'),
                'backend'       => 'https://librespeed.example.com/us-east/',
                'download_path' => 'download.php',
                'upload_path'   => 'upload.php',
                'ping_path'     => 'ping.php',
                'icon'          => 'dashicons-location-alt',
                'weight'        => 1,
                'geo'           => array(
                    'lat' => 39.0438,
                    'lng' => -77.4874
                ),
                'notes'         => __('Ideal for gamers on the US East Coast.', 'cloud-gaming-speed-test'),
            ),
            array(
                'id'            => 'us-west',
                'name'          => __('US West - Silicon Valley', 'cloud-gaming-speed-test'),
                'location'      => __('San Jose, CA, USA', 'cloud-gaming-speed-test'),
                'backend'       => 'https://librespeed.example.com/us-west/',
                'download_path' => 'download.php',
                'upload_path'   => 'upload.php',
                'ping_path'     => 'ping.php',
                'icon'          => 'dashicons-admin-site',
                'weight'        => 2,
                'geo'           => array(
                    'lat' => 37.3382,
                    'lng' => -121.8863
                ),
                'notes'         => __('Great option for West Coast and Pacific gamers.', 'cloud-gaming-speed-test'),
            ),
            array(
                'id'            => 'eu-central',
                'name'          => __('EU Central - Frankfurt', 'cloud-gaming-speed-test'),
                'location'      => __('Frankfurt, Germany', 'cloud-gaming-speed-test'),
                'backend'       => 'https://librespeed.example.com/eu-central/',
                'download_path' => 'download.php',
                'upload_path'   => 'upload.php',
                'ping_path'     => 'ping.php',
                'icon'          => 'dashicons-admin-site-alt3',
                'weight'        => 3,
                'geo'           => array(
                    'lat' => 50.1109,
                    'lng' => 8.6821
                ),
                'notes'         => __('Best latency for Central EU players.', 'cloud-gaming-speed-test'),
            ),
            array(
                'id'            => 'asia-sg',
                'name'          => __('Asia Pacific - Singapore', 'cloud-gaming-speed-test'),
                'location'      => __('Singapore', 'cloud-gaming-speed-test'),
                'backend'       => 'https://librespeed.example.com/asia-sg/',
                'download_path' => 'download.php',
                'upload_path'   => 'upload.php',
                'ping_path'     => 'ping.php',
                'icon'          => 'dashicons-admin-network',
                'weight'        => 4,
                'geo'           => array(
                    'lat' => 1.3521,
                    'lng' => 103.8198
                ),
                'notes'         => __('Optimised for South East Asia.', 'cloud-gaming-speed-test'),
            ),
        );

        update_option(self::SERVERS_OPTION, $defaults, false);
    }

    /**
     * Seed default recommended articles and tips.
     */
    public static function insert_default_articles() {
        $articles = get_option(self::ARTICLES_OPTION);
        if (!empty($articles) && is_array($articles)) {
            return;
        }

        $defaults = array(
            array(
                'id'          => 'optimize-home-network',
                'title'       => __('8 Pro Tips to Reduce Cloud Gaming Latency', 'cloud-gaming-speed-test'),
                'url'         => 'https://example.com/blog/reduce-cloud-gaming-latency',
                'description' => __('Actionable checklist to stabilise your ping with QoS, wired networking, and router tuning.', 'cloud-gaming-speed-test'),
                'category'    => __('Optimization', 'cloud-gaming-speed-test'),
            ),
            array(
                'id'          => 'best-vpns',
                'title'       => __('Best Gaming VPNs for Streaming Services', 'cloud-gaming-speed-test'),
                'url'         => 'https://example.com/blog/best-gaming-vpns',
                'description' => __('Latency-focused VPN recommendations that keep you below 40ms when geo-routing is required.', 'cloud-gaming-speed-test'),
                'category'    => __('Networking', 'cloud-gaming-speed-test'),
            ),
            array(
                'id'          => 'choose-server',
                'title'       => __('How to Choose the Right Cloud Gaming Region', 'cloud-gaming-speed-test'),
                'url'         => 'https://example.com/guides/cloud-gaming-region',
                'description' => __('Step-by-step guide for matching your ISP routes with Stadia, GeForce NOW, and Xbox Cloud Gaming.', 'cloud-gaming-speed-test'),
                'category'    => __('Guides', 'cloud-gaming-speed-test'),
            ),
        );

        update_option(self::ARTICLES_OPTION, $defaults, false);
    }

    /**
     * Retrieve all stored server presets ordered by weight.
     *
     * @return array
     */
    public static function get_servers() {
        $servers = get_option(self::SERVERS_OPTION, array());
        if (!is_array($servers)) {
            $servers = array();
        }

        usort($servers, function ($a, $b) {
            $a_weight = isset($a['weight']) ? (int) $a['weight'] : 0;
            $b_weight = isset($b['weight']) ? (int) $b['weight'] : 0;
            if ($a_weight === $b_weight) {
                return strcmp($a['name'], $b['name']);
            }
            return $a_weight - $b_weight;
        });

        return $servers;
    }

    /**
     * Retrieve server presets that can be used on the front-end.
     * Formats the data to include the computed URLs.
     *
     * @return array
     */
    public static function get_active_servers() {
        $servers = self::get_servers();

        return array_map(function ($server) {
            $backend = trailingslashit($server['backend']);
            return array(
                'id'        => $server['id'],
                'name'      => $server['name'],
                'location'  => isset($server['location']) ? $server['location'] : '',
                'pingUrl'   => $backend . ltrim($server['ping_path'], '/'),
                'downloadUrl' => $backend . ltrim($server['download_path'], '/'),
                'uploadUrl' => $backend . ltrim($server['upload_path'], '/'),
                'icon'      => isset($server['icon']) ? $server['icon'] : 'dashicons-performance',
                'weight'    => isset($server['weight']) ? (int) $server['weight'] : 0,
                'geo'       => isset($server['geo']) ? $server['geo'] : array(),
                'notes'     => isset($server['notes']) ? $server['notes'] : '',
            );
        }, $servers);
    }

    /**
     * Store or update a server preset.
     *
     * @param array $server
     * @return array Updated server list
     */
    public static function save_server($server) {
        $servers = self::get_servers();

        $server['id']            = !empty($server['id']) ? sanitize_key($server['id']) : sanitize_key(uniqid('cgst_srv_'));
        $server['name']          = sanitize_text_field($server['name']);
        $server['location']      = sanitize_text_field($server['location']);
        $server['backend']       = esc_url_raw(trailingslashit($server['backend']));
        $server['download_path'] = sanitize_text_field($server['download_path']);
        $server['upload_path']   = sanitize_text_field($server['upload_path']);
        $server['ping_path']     = sanitize_text_field($server['ping_path']);
        $server['icon']          = sanitize_html_class($server['icon']);
        $server['weight']        = isset($server['weight']) ? (int) $server['weight'] : 0;
        $server['notes']         = sanitize_textarea_field($server['notes']);
        $server['geo']           = array(
            'lat' => isset($server['geo_lat']) ? floatval($server['geo_lat']) : '',
            'lng' => isset($server['geo_lng']) ? floatval($server['geo_lng']) : '',
        );

        $exists = false;
        foreach ($servers as $index => $item) {
            if ($item['id'] === $server['id']) {
                $servers[$index] = array_merge($item, $server);
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $servers[] = $server;
        }

        update_option(self::SERVERS_OPTION, $servers, false);

        return $servers;
    }

    /**
     * Delete a server by ID.
     *
     * @param string $id
     * @return void
     */
    public static function delete_server($id) {
        $servers = self::get_servers();

        $servers = array_filter($servers, function ($server) use ($id) {
            return $server['id'] !== $id;
        });

        update_option(self::SERVERS_OPTION, array_values($servers), false);
    }

    /**
     * Retrieve all recommended articles.
     *
     * @return array
     */
    public static function get_articles() {
        $articles = get_option(self::ARTICLES_OPTION, array());
        if (!is_array($articles)) {
            $articles = array();
        }

        usort($articles, function ($a, $b) {
            return strcmp($a['title'], $b['title']);
        });

        return $articles;
    }

    /**
     * Store or update an article.
     *
     * @param array $article
     * @return array
     */
    public static function save_article($article) {
        $articles = self::get_articles();

        $article['id']          = !empty($article['id']) ? sanitize_key($article['id']) : sanitize_key(uniqid('cgst_art_'));
        $article['title']       = sanitize_text_field($article['title']);
        $article['url']         = esc_url_raw($article['url']);
        $article['description'] = sanitize_textarea_field($article['description']);
        $article['category']    = sanitize_text_field($article['category']);

        $exists = false;
        foreach ($articles as $index => $item) {
            if ($item['id'] === $article['id']) {
                $articles[$index] = array_merge($item, $article);
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $articles[] = $article;
        }

        update_option(self::ARTICLES_OPTION, $articles, false);

        return $articles;
    }

    /**
     * Delete an article by ID.
     *
     * @param string $id
     * @return void
     */
    public static function delete_article($id) {
        $articles = self::get_articles();

        $articles = array_filter($articles, function ($article) use ($id) {
            return $article['id'] !== $id;
        });

        update_option(self::ARTICLES_OPTION, array_values($articles), false);
    }

    /**
     * Insert a new speed test result into the custom table.
     *
     * @param array $data
     * @return int|false Insert ID or false
     */
    public static function insert_result($data) {
        global $wpdb;

        $table = $wpdb->prefix . self::RESULTS_TABLE;

        $payload = array(
            'server_id'        => sanitize_key($data['server_id']),
            'server_name'      => sanitize_text_field($data['server_name']),
            'download_mbps'    => floatval($data['download_mbps']),
            'upload_mbps'      => floatval($data['upload_mbps']),
            'ping_ms'          => floatval($data['ping_ms']),
            'jitter_ms'        => floatval($data['jitter_ms']),
            'packet_loss'      => floatval($data['packet_loss']),
            'rating'           => sanitize_text_field($data['rating']),
            'recommendation'   => sanitize_text_field($data['recommendation']),
            'settings_json'    => wp_json_encode($data['settings']),
        );

        $wpdb->insert($table, $payload);

        return $wpdb->insert_id;
    }

    /**
     * Fetch a collection of stored results.
     *
     * @param int $limit
     * @return array
     */
    public static function get_results($limit = 100) {
        global $wpdb;

        $table = $wpdb->prefix . self::RESULTS_TABLE;

        $sql = $wpdb->prepare(
            "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d",
            absint($limit)
        );

        $results = $wpdb->get_results($sql, ARRAY_A);

        return array_map(function ($row) {
            $row['settings'] = !empty($row['settings_json']) ? json_decode($row['settings_json'], true) : array();
            return $row;
        }, $results);
    }

    /**
     * Export all results to CSV output.
     */
    public static function stream_results_csv() {
        $filename = 'cloud-gaming-speed-test-results-' . gmdate('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Timestamp',
            'Server ID',
            'Server Name',
            'Download (Mbps)',
            'Upload (Mbps)',
            'Ping (ms)',
            'Jitter (ms)',
            'Packet Loss (%)',
            'Rating',
            'Recommendation',
        ));

        $results = self::get_results(5000);

        foreach ($results as $result) {
            fputcsv($output, array(
                $result['created_at'],
                $result['server_id'],
                $result['server_name'],
                $result['download_mbps'],
                $result['upload_mbps'],
                $result['ping_ms'],
                $result['jitter_ms'],
                $result['packet_loss'],
                $result['rating'],
                $result['recommendation'],
            ));
        }

        fclose($output);
        exit;
    }

    /**
     * Get rating thresholds for frontend use.
     *
     * @return array
     */
    public static function get_rating_thresholds() {
        return array(
            'excellent' => array(
                'download'    => 150,
                'upload'      => 25,
                'ping'        => 20,
                'jitter'      => 5,
                'packet_loss' => 0.1,
            ),
            'good' => array(
                'download'    => 90,
                'upload'      => 15,
                'ping'        => 35,
                'jitter'      => 8,
                'packet_loss' => 0.3,
            ),
            'fair' => array(
                'download'    => 45,
                'upload'      => 8,
                'ping'        => 55,
                'jitter'      => 12,
                'packet_loss' => 0.8,
            ),
        );
    }

    /**
     * Calculate the cloud gaming rating and recommended settings.
     *
     * @param float $download
     * @param float $upload
     * @param float $ping
     * @param float $jitter
     * @param float $packet_loss
     * @return array
     */
    public static function calculate_rating($download, $upload, $ping, $jitter, $packet_loss) {
        $download   = floatval($download);
        $upload     = floatval($upload);
        $ping       = floatval($ping);
        $jitter     = floatval($jitter);
        $packetLoss = floatval($packet_loss);

        $thresholds = self::get_rating_thresholds();

        $bands = array(
            'excellent' => array(
                'label'         => __('Excellent', 'cloud-gaming-speed-test'),
                'recommendation'=> __('4K / 120fps – perfect for GeForce NOW Ultimate & Xbox Cloud Gaming Performance preset.', 'cloud-gaming-speed-test'),
                'icon'          => 'cgst-icon-excellent',
            ),
            'good' => array(
                'label'         => __('Good', 'cloud-gaming-speed-test'),
                'recommendation'=> __('1440p / 60fps – enable VRR and keep bitrate under 45 Mbps.', 'cloud-gaming-speed-test'),
                'icon'          => 'cgst-icon-good',
            ),
            'fair' => array(
                'label'         => __('Fair', 'cloud-gaming-speed-test'),
                'recommendation'=> __('1080p / 60fps – reduce graphics streaming quality to balanced.', 'cloud-gaming-speed-test'),
                'icon'          => 'cgst-icon-fair',
            ),
            'poor' => array(
                'label'         => __('Poor', 'cloud-gaming-speed-test'),
                'recommendation'=> __('720p @ 60fps – lower bitrate and enable performance mode.', 'cloud-gaming-speed-test'),
                'icon'          => 'cgst-icon-poor',
            ),
        );

        $ratingKey = 'poor';

        if (
            $download >= $thresholds['excellent']['download'] &&
            $upload >= $thresholds['excellent']['upload'] &&
            $ping <= $thresholds['excellent']['ping'] &&
            $jitter <= $thresholds['excellent']['jitter'] &&
            $packetLoss <= $thresholds['excellent']['packet_loss']
        ) {
            $ratingKey = 'excellent';
        } elseif (
            $download >= $thresholds['good']['download'] &&
            $upload >= $thresholds['good']['upload'] &&
            $ping <= $thresholds['good']['ping'] &&
            $jitter <= $thresholds['good']['jitter'] &&
            $packetLoss <= $thresholds['good']['packet_loss']
        ) {
            $ratingKey = 'good';
        } elseif (
            $download >= $thresholds['fair']['download'] &&
            $upload >= $thresholds['fair']['upload'] &&
            $ping <= $thresholds['fair']['ping'] &&
            $jitter <= $thresholds['fair']['jitter'] &&
            $packetLoss <= $thresholds['fair']['packet_loss']
        ) {
            $ratingKey = 'fair';
        }

        return array(
            'rating'         => $bands[$ratingKey]['label'],
            'recommendation' => $bands[$ratingKey]['recommendation'],
            'icon'           => $bands[$ratingKey]['icon'],
        );
    }

}
