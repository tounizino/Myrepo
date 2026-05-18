<?php

namespace CloudGamersDiscuss\API;

class ReactionController {
	public function registerRoutes() {
		register_rest_route( 'cgd/v1', '/reactions', [
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'toggleReaction' ],
				'permission_callback' => '__return_true',
			],
		] );
	}

	public function toggleReaction( $request ) {
		global $wpdb;
		$params = $request->get_json_params();
		$comment_id = intval( $params['comment_id'] );
		$type       = sanitize_text_field( $params['type'] );
		$ip         = $_SERVER['REMOTE_ADDR'];
		$table      = $wpdb->prefix . 'cgd_reactions';

		// Check if already reacted
		$existing = $wpdb->get_var( $wpdb->prepare(
			"SELECT id FROM $table WHERE comment_id = %d AND type = %s AND ip_address = %s",
			$comment_id, $type, $ip
		) );

		if ( $existing ) {
			$wpdb->delete( $table, [ 'id' => $existing ] );
		} else {
			$wpdb->insert( $table, [
				'comment_id' => $comment_id,
				'post_id'    => get_comment( $comment_id )->comment_post_ID,
				'type'       => $type,
				'ip_address' => $ip,
				'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
			] );
		}

		$new_count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM $table WHERE comment_id = %d AND type = %s",
			$comment_id, $type
		) );

		return [
			'new_count' => (int) $new_count,
			'active'    => ! $existing,
		];
	}
}
