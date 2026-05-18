<?php

namespace CloudGamersDiscuss\API;

class MailchimpController {
	public function registerRoutes() {
		register_rest_route( 'cgd/v1', '/subscribe', [
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'subscribe' ],
				'permission_callback' => '__return_true',
			],
		] );
	}

	public function subscribe( $request ) {
		$params = $request->get_json_params();
		$email  = sanitize_email( $params['email'] ?? '' );
		$name   = sanitize_text_field( $params['name'] ?? '' );

		if ( ! is_email( $email ) ) {
			return new \WP_Error( 'invalid_email', 'Invalid email address.', [ 'status' => 400 ] );
		}

		$mailchimp = new \CloudGamersDiscuss\Integrations\Mailchimp();
		$success = $mailchimp->subscribe( $email, $name );

		if ( ! $success ) {
			return new \WP_Error( 'mailchimp_error', 'Subscription failed.', [ 'status' => 500 ] );
		}

		return [ 'success' => true ];
	}
}
