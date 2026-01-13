<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CGRT_Block {

	/**
	 * @var CGRT_Block
	 */
	private static $instance;

	/**
	 * @return CGRT_Block
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
	}

	public function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		wp_register_script(
			'cgrt-block',
			CGRT_PLUGIN_URL . 'blocks/cgrt/block.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor' ),
			CGRT_VERSION,
			true
		);

		register_block_type(
			'cgrt/cloud-gaming-readiness-test',
			array(
				'editor_script'   => 'cgrt-block',
				'render_callback' => array( $this, 'render_block' ),
				'attributes'      => array(
					'platform' => array(
						'type'    => 'string',
						'default' => 'auto',
					),
				),
			)
		);
	}

	public function render_block( $attributes ) {
		$platform = isset( $attributes['platform'] ) ? sanitize_text_field( $attributes['platform'] ) : 'auto';
		return do_shortcode( '[cloud_gaming_readiness_test platform="' . esc_attr( $platform ) . '"]' );
	}
}
