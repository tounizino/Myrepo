<?php

namespace CloudGamersDiscuss\Frontend;

class Assets {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueueScripts' ] );
	}

	public function enqueueScripts() {
		if ( ! is_singular() ) {
			return;
		}

		wp_enqueue_style( 
			'cgd-frontend', 
			CGD_PLUGIN_URL . 'assets/css/frontend.css', 
			[], 
			CGD_VERSION 
		);

		wp_enqueue_script( 
			'cgd-frontend', 
			CGD_PLUGIN_URL . 'assets/js/frontend.js', 
			[], 
			CGD_VERSION, 
			true 
		);

		wp_localize_script( 'cgd-frontend', 'cgdData', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'restUrl' => esc_url_raw( rest_url( 'cgd/v1' ) ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'postId'  => get_the_ID(),
			'i18n'    => [
				'posting' => __( 'Posting...', 'cloud-gamers-discuss' ),
				'error'   => __( 'Something went wrong.', 'cloud-gamers-discuss' ),
			]
		] );
	}
}
