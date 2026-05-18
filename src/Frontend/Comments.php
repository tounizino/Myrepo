<?php

namespace CloudGamersDiscuss\Frontend;

class Comments {
	public function __construct() {
		add_filter( 'comments_template', [ $this, 'loadTemplate' ] );
	}

	public function loadTemplate( $template ) {
		if ( ! is_singular() ) {
			return $template;
		}

		$custom_template = CGD_PLUGIN_DIR . 'templates/frontend/comments.php';
		if ( file_exists( $custom_template ) ) {
			return $custom_template;
		}

		return $template;
	}
}
