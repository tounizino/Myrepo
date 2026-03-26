<?php

if (!defined('ABSPATH')) {
    exit;
}

class CGA_REST_API {

    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        register_rest_route('cloud-games-availability/v1', '/platforms', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_platforms'),
                'permission_callback' => array($this, 'check_read_permission'),
            ),
            array(
                'methods' => 'POST',
                'callback' => array($this, 'create_platform'),
                'permission_callback' => array($this, 'check_manage_permission'),
            ),
        ));

        register_rest_route('cloud-games-availability/v1', '/platforms/(?P<id>[\d]+)', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_platform'),
                'permission_callback' => array($this, 'check_read_permission'),
            ),
            array(
                'methods' => 'PUT',
                'callback' => array($this, 'update_platform'),
                'permission_callback' => array($this, 'check_manage_permission'),
            ),
            array(
                'methods' => 'DELETE',
                'callback' => array($this, 'delete_platform'),
                'permission_callback' => array($this, 'check_manage_permission'),
            ),
        ));

        register_rest_route('cloud-games-availability/v1', '/games', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_games'),
                'permission_callback' => array($this, 'check_read_permission'),
            ),
            array(
                'methods' => 'POST',
                'callback' => array($this, 'create_game'),
                'permission_callback' => array($this, 'check_manage_permission'),
            ),
        ));

        register_rest_route('cloud-games-availability/v1', '/games/(?P<id>[\d]+)', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_game'),
                'permission_callback' => array($this, 'check_read_permission'),
            ),
            array(
                'methods' => 'PUT',
                'callback' => array($this, 'update_game'),
                'permission_callback' => array($this, 'check_manage_permission'),
            ),
            array(
                'methods' => 'DELETE',
                'callback' => array($this, 'delete_game'),
                'permission_callback' => array($this, 'check_manage_permission'),
            ),
        ));

        register_rest_route('cloud-games-availability/v1', '/games/(?P<game_id>[\d]+)/availability', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_availability'),
                'permission_callback' => array($this, 'check_read_permission'),
            ),
            array(
                'methods' => 'PUT',
                'callback' => array($this, 'save_availability'),
                'permission_callback' => array($this, 'check_manage_permission'),
            ),
        ));
    }

    public function check_read_permission() {
        return current_user_can('read');
    }

    public function check_manage_permission() {
        return current_user_can('manage_options');
    }

    public function get_platforms() {
        $database = new CGA_Database();
        $platforms = $database->get_platforms();
        return rest_ensure_response($platforms);
    }

    public function get_platform($request) {
        $id = $request['id'];
        $database = new CGA_Database();
        $platform = $database->get_platform($id);
        
        if (!$platform) {
            return new WP_Error('not_found', __('Platform not found', 'cloud-games-availability-v2'), array('status' => 404));
        }
        
        return rest_ensure_response($platform);
    }

    public function create_platform($request) {
        $params = $request->get_params();
        $data = array(
            'name' => sanitize_text_field($params['name']),
            'slug' => sanitize_title($params['name']),
            'icon_url' => esc_url_raw($params['icon_url'] ?? ''),
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'base_price' => !empty($params['base_price']) ? floatval($params['base_price']) : 0,
            'price_currency' => sanitize_text_field($params['price_currency'] ?? 'USD'),
            'price_period' => sanitize_text_field($params['price_period'] ?? 'month'),
            'cta_text' => sanitize_text_field($params['cta_text'] ?? ''),
            'cta_url' => esc_url_raw($params['cta_url'] ?? ''),
            'tier_name' => sanitize_text_field($params['tier_name'] ?? ''),
            'notes' => sanitize_textarea_field($params['notes'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'active'),
            'display_order' => intval($params['display_order'] ?? 0),
        );

        $database = new CGA_Database();
        $id = $database->save_platform($data);
        
        return rest_ensure_response(array('id' => $id));
    }

    public function update_platform($request) {
        $id = $request['id'];
        $params = $request->get_params();
        $data = array(
            'id' => $id,
            'name' => sanitize_text_field($params['name']),
            'slug' => sanitize_title($params['name']),
            'icon_url' => esc_url_raw($params['icon_url'] ?? ''),
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'base_price' => !empty($params['base_price']) ? floatval($params['base_price']) : 0,
            'price_currency' => sanitize_text_field($params['price_currency'] ?? 'USD'),
            'price_period' => sanitize_text_field($params['price_period'] ?? 'month'),
            'cta_text' => sanitize_text_field($params['cta_text'] ?? ''),
            'cta_url' => esc_url_raw($params['cta_url'] ?? ''),
            'tier_name' => sanitize_text_field($params['tier_name'] ?? ''),
            'notes' => sanitize_textarea_field($params['notes'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'active'),
            'display_order' => intval($params['display_order'] ?? 0),
        );

        $database = new CGA_Database();
        $result_id = $database->save_platform($data);
        
        return rest_ensure_response(array('id' => $result_id));
    }

    public function delete_platform($request) {
        $id = $request['id'];
        $database = new CGA_Database();
        $database->delete_platform($id);
        
        return rest_ensure_response(array('success' => true));
    }

    public function get_games() {
        $database = new CGA_Database();
        $games = $database->get_games();
        return rest_ensure_response($games);
    }

    public function get_game($request) {
        $id = $request['id'];
        $database = new CGA_Database();
        $game = $database->get_game($id);
        
        if (!$game) {
            return new WP_Error('not_found', __('Game not found', 'cloud-games-availability-v2'), array('status' => 404));
        }
        
        return rest_ensure_response($game);
    }

    public function create_game($request) {
        $params = $request->get_params();
        $data = array(
            'name' => sanitize_text_field($params['name']),
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'cover_image' => esc_url_raw($params['cover_image'] ?? ''),
            'developer' => sanitize_text_field($params['developer'] ?? ''),
            'publisher' => sanitize_text_field($params['publisher'] ?? ''),
            'release_date' => !empty($params['release_date']) ? sanitize_text_field($params['release_date']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'active'),
        );

        $database = new CGA_Database();
        $id = $database->save_game($data);
        
        return rest_ensure_response(array('id' => $id));
    }

    public function update_game($request) {
        $id = $request['id'];
        $params = $request->get_params();
        $data = array(
            'id' => $id,
            'name' => sanitize_text_field($params['name']),
            'description' => sanitize_textarea_field($params['description'] ?? ''),
            'cover_image' => esc_url_raw($params['cover_image'] ?? ''),
            'developer' => sanitize_text_field($params['developer'] ?? ''),
            'publisher' => sanitize_text_field($params['publisher'] ?? ''),
            'release_date' => !empty($params['release_date']) ? sanitize_text_field($params['release_date']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'active'),
        );

        $database = new CGA_Database();
        $result_id = $database->save_game($data);
        
        return rest_ensure_response(array('id' => $result_id));
    }

    public function delete_game($request) {
        $id = $request['id'];
        $database = new CGA_Database();
        $database->delete_game($id);
        
        return rest_ensure_response(array('success' => true));
    }

    public function get_availability($request) {
        $game_id = $request['game_id'];
        $database = new CGA_Database();
        $availability = $database->get_game_availability($game_id);
        return rest_ensure_response($availability);
    }

    public function save_availability($request) {
        $game_id = $request['game_id'];
        $platforms_data = $request['platforms'] ?? array();
        
        $database = new CGA_Database();
        $database->save_availability($game_id, $platforms_data);
        
        return rest_ensure_response(array('success' => true));
    }
}
