<?php
/**
 * AJAX request handlers for Cloud Gaming Speed Test plugin.
 *
 * @package CloudGamingSpeedTest
 */

if (!defined('ABSPATH')) {
    exit;
}

class CGST_AJAX_Handlers {

    public function __construct() {
        add_action('wp_ajax_cgst_save_result', array($this, 'save_result'));
        add_action('wp_ajax_nopriv_cgst_save_result', array($this, 'save_result'));

        add_action('wp_ajax_cgst_save_server', array($this, 'save_server'));
        add_action('wp_ajax_cgst_delete_server', array($this, 'delete_server'));

        add_action('wp_ajax_cgst_save_article', array($this, 'save_article'));
        add_action('wp_ajax_cgst_delete_article', array($this, 'delete_article'));

        add_action('wp_ajax_cgst_export_csv', array($this, 'export_csv'));

        add_action('wp_ajax_cgst_get_articles', array($this, 'get_articles'));
        add_action('wp_ajax_nopriv_cgst_get_articles', array($this, 'get_articles'));
    }

    public function save_result() {
        check_ajax_referer('cgst_nonce', 'nonce');

        $settings = array();
        if (isset($_POST['settings']) && is_array($_POST['settings'])) {
            foreach ($_POST['settings'] as $key => $value) {
                $settings[sanitize_key($key)] = sanitize_text_field($value);
            }
        }

        $data = array(
            'server_id'        => isset($_POST['server_id']) ? sanitize_key($_POST['server_id']) : '',
            'server_name'      => isset($_POST['server_name']) ? sanitize_text_field($_POST['server_name']) : __('Unknown Server', 'cloud-gaming-speed-test'),
            'download_mbps'    => isset($_POST['download_mbps']) ? floatval($_POST['download_mbps']) : 0,
            'upload_mbps'      => isset($_POST['upload_mbps']) ? floatval($_POST['upload_mbps']) : 0,
            'ping_ms'          => isset($_POST['ping_ms']) ? floatval($_POST['ping_ms']) : 0,
            'jitter_ms'        => isset($_POST['jitter_ms']) ? floatval($_POST['jitter_ms']) : 0,
            'packet_loss'      => isset($_POST['packet_loss']) ? max(0, min(1, floatval($_POST['packet_loss']))) : 0,
            'settings'         => $settings,
        );

        $ratingData = CGST_Database::calculate_rating(
            $data['download_mbps'],
            $data['upload_mbps'],
            $data['ping_ms'],
            $data['jitter_ms'],
            $data['packet_loss']
        );

        $data['rating'] = $ratingData['rating'];
        $data['recommendation'] = $ratingData['recommendation'];
        $data['settings']['icon'] = $ratingData['icon'];

        $result_id = CGST_Database::insert_result($data);

        if ($result_id) {
            wp_send_json_success(array(
                'message' => __('Test result saved successfully.', 'cloud-gaming-speed-test'),
                'id'      => $result_id,
                'rating'  => $ratingData,
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to save result.', 'cloud-gaming-speed-test'),
            ));
        }
    }

    public function save_server() {
        check_ajax_referer('cgst_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'cloud-gaming-speed-test')));
        }

        $server = array(
            'id'            => isset($_POST['id']) ? sanitize_key($_POST['id']) : '',
            'name'          => isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '',
            'location'      => isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '',
            'backend'       => isset($_POST['backend']) ? esc_url_raw($_POST['backend']) : '',
            'download_path' => isset($_POST['download_path']) ? sanitize_text_field($_POST['download_path']) : 'download.php',
            'upload_path'   => isset($_POST['upload_path']) ? sanitize_text_field($_POST['upload_path']) : 'upload.php',
            'ping_path'     => isset($_POST['ping_path']) ? sanitize_text_field($_POST['ping_path']) : 'ping.php',
            'icon'          => isset($_POST['icon']) ? sanitize_html_class($_POST['icon']) : 'dashicons-performance',
            'weight'        => isset($_POST['weight']) ? intval($_POST['weight']) : 0,
            'geo_lat'       => isset($_POST['geo_lat']) ? floatval($_POST['geo_lat']) : '',
            'geo_lng'       => isset($_POST['geo_lng']) ? floatval($_POST['geo_lng']) : '',
            'notes'         => isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '',
        );

        CGST_Database::save_server($server);

        wp_send_json_success(array(
            'message' => __('Server saved successfully.', 'cloud-gaming-speed-test'),
            'servers' => CGST_Database::get_servers(),
        ));
    }

    public function delete_server() {
        check_ajax_referer('cgst_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'cloud-gaming-speed-test')));
        }

        $id = isset($_POST['id']) ? sanitize_key($_POST['id']) : '';

        if (empty($id)) {
            wp_send_json_error(array('message' => __('Invalid server ID.', 'cloud-gaming-speed-test')));
        }

        CGST_Database::delete_server($id);

        wp_send_json_success(array(
            'message' => __('Server deleted successfully.', 'cloud-gaming-speed-test'),
            'servers' => CGST_Database::get_servers(),
        ));
    }

    public function save_article() {
        check_ajax_referer('cgst_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'cloud-gaming-speed-test')));
        }

        $article = array(
            'id'          => isset($_POST['id']) ? sanitize_key($_POST['id']) : '',
            'title'       => isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '',
            'url'         => isset($_POST['url']) ? esc_url_raw($_POST['url']) : '',
            'description' => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
            'category'    => isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '',
        );

        CGST_Database::save_article($article);

        wp_send_json_success(array(
            'message'  => __('Article saved successfully.', 'cloud-gaming-speed-test'),
            'articles' => CGST_Database::get_articles(),
        ));
    }

    public function delete_article() {
        check_ajax_referer('cgst_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized.', 'cloud-gaming-speed-test')));
        }

        $id = isset($_POST['id']) ? sanitize_key($_POST['id']) : '';

        if (empty($id)) {
            wp_send_json_error(array('message' => __('Invalid article ID.', 'cloud-gaming-speed-test')));
        }

        CGST_Database::delete_article($id);

        wp_send_json_success(array(
            'message'  => __('Article deleted successfully.', 'cloud-gaming-speed-test'),
            'articles' => CGST_Database::get_articles(),
        ));
    }

    public function export_csv() {
        check_ajax_referer('cgst_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized.', 'cloud-gaming-speed-test'));
        }

        CGST_Database::stream_results_csv();
    }

    public function get_articles() {
        check_ajax_referer('cgst_nonce', 'nonce');

        $articles = CGST_Database::get_articles();

        wp_send_json_success(array(
            'articles' => $articles,
        ));
    }
}
