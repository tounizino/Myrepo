<?php

namespace CloudGamersDiscuss\API;

class RatingController {
	public function registerRoutes() {
		register_rest_route( 'cgd/v1', '/ratings', [
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'submitRating' ],
				'permission_callback' => '__return_true',
			],
		] );
	}

	public function submitRating( $request ) {
		global $wpdb;
		$params  = $request->get_json_params();
		$post_id = intval( $params['post_id'] );
		$rating  = intval( $params['rating'] );
		$ip      = $_SERVER['REMOTE_ADDR'];
		$table   = $wpdb->prefix . 'cgd_ratings';

		// Clamp rating
		$rating = max( 1, min( 5, $rating ) );

		// Check if already rated (limit to 1 per IP per post)
		$existing = $wpdb->get_var( $wpdb->prepare(
			"SELECT id FROM $table WHERE post_id = %d AND ip_address = %s",
			$post_id, $ip
		) );

		if ( $existing ) {
			$wpdb->update( $table, [ 'rating' => $rating ], [ 'id' => $existing ] );
		} else {
			$wpdb->insert( $table, [
				'post_id'    => $post_id,
				'rating'     => $rating,
				'ip_address' => $ip,
				'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
			] );
		}

		$new_avg = $wpdb->get_var( $wpdb->prepare(
			"SELECT AVG(rating) FROM $table WHERE post_id = %d",
			$post_id
		) );

		return [
			'new_avg' => round( $new_avg, 1 ),
		];
	}
}
