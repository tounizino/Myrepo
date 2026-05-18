<?php

namespace CloudGamersDiscuss\Integrations;

class Mailchimp {
	private $api_key;
	private $list_id;

	public function __construct() {
		$this->api_key = get_option( 'cgd_mailchimp_api_key' );
		$this->list_id = get_option( 'cgd_mailchimp_list_id' );
	}

	public function subscribe( $email, $name = '' ) {
		if ( ! $this->api_key || ! $this->list_id ) {
			return false;
		}

		$dc = substr( $this->api_key, strpos( $this->api_key, '-' ) + 1 );
		$url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$this->list_id}/members/";

		$data = [
			'email_address' => $email,
			'status'        => 'subscribed',
		];

		if ( $name ) {
			$names = explode( ' ', $name, 2 );
			$data['merge_fields'] = [
				'FNAME' => $names[0],
				'LNAME' => $names[1] ?? '',
			];
		}

		$response = wp_remote_post( $url, [
			'headers' => [
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $this->api_key ),
				'Content-Type'  => 'application/json',
			],
			'body' => json_encode( $data ),
		] );

		return ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) < 300;
	}
}
