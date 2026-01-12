<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CGRT_Frontend {

	/**
	 * @var CGRT_Frontend
	 */
	private static $instance;

	/**
	 * @return CGRT_Frontend
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'cloud_gaming_readiness_test', array( $this, 'shortcode' ) );
		add_shortcode( 'cgrt_test', array( $this, 'shortcode' ) );
	}

	/**
	 * Shortcode renderer.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'platform' => 'auto',
			),
			$atts,
			'cloud_gaming_readiness_test'
		);

		$this->enqueue_assets();

		$platform = sanitize_text_field( $atts['platform'] );

		$container_id = 'cgrt-' . wp_generate_uuid4();

		ob_start();
		?>
		<div class="cgrt" id="<?php echo esc_attr( $container_id ); ?>" data-platform="<?php echo esc_attr( $platform ); ?>">
			<div class="cgrt__shell" aria-live="polite">
				<div class="cgrt__header">
					<div class="cgrt__brand">
						<div class="cgrt__title"><?php echo esc_html__( 'Cloud Gaming Readiness Test', 'cloud-gaming-readiness-test' ); ?></div>
						<div class="cgrt__subtitle"><?php echo esc_html__( 'Latency • Jitter • Packet Loss • Stability', 'cloud-gaming-readiness-test' ); ?></div>
					</div>
					<div class="cgrt__status" data-cgrt-status></div>
				</div>
				<div class="cgrt__body" data-cgrt-body>
					<div class="cgrt__loading">
						<div class="cgrt__spinner" aria-hidden="true"></div>
						<div>
							<div class="cgrt__loadingTitle"><?php echo esc_html__( 'Preparing test…', 'cloud-gaming-readiness-test' ); ?></div>
							<div class="cgrt__loadingHint"><?php echo esc_html__( 'Fetching platform endpoints and calibrating timing.', 'cloud-gaming-readiness-test' ); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Enqueue frontend assets.
	 */
	private function enqueue_assets() {
		wp_register_style(
			'cgrt-frontend',
			CGRT_PLUGIN_URL . 'assets/frontend/frontend.css',
			array(),
			CGRT_VERSION
		);

		wp_register_script(
			'cgrt-frontend',
			CGRT_PLUGIN_URL . 'assets/frontend/frontend.js',
			array(),
			CGRT_VERSION,
			true
		);

		wp_enqueue_style( 'cgrt-frontend' );
		wp_enqueue_script( 'cgrt-frontend' );

		$settings = CGRT_Database::get_settings();

		wp_add_inline_script(
			'cgrt-frontend',
			'window.CGRT = window.CGRT || {}; window.CGRT_BOOT = ' . wp_json_encode(
				array(
					'restUrl'   => esc_url_raw( rest_url( 'cgrt/v1' ) ),
					'siteUrl'   => esc_url_raw( home_url( '/' ) ),
					'settings'  => $settings,
					'version'   => CGRT_VERSION,
					'i18n'      => array(
						'start'               => __( 'Start test', 'cloud-gaming-readiness-test' ),
						'retry'               => __( 'Retry', 'cloud-gaming-readiness-test' ),
						'choosePlatform'      => __( 'Choose platform', 'cloud-gaming-readiness-test' ),
						'chooseRegion'        => __( 'Choose region', 'cloud-gaming-readiness-test' ),
						'runTest'             => __( 'Run readiness test', 'cloud-gaming-readiness-test' ),
						'running'             => __( 'Running diagnostic…', 'cloud-gaming-readiness-test' ),
						'configError'         => __( 'Unable to load test configuration. Please try again later.', 'cloud-gaming-readiness-test' ),
						'noEndpoints'         => __( 'No test endpoints are available. Ask the site owner to configure at least one enabled endpoint with CORS support.', 'cloud-gaming-readiness-test' ),
						'copy'                => __( 'Copy results', 'cloud-gaming-readiness-test' ),
						'copied'              => __( 'Copied', 'cloud-gaming-readiness-test' ),
						'advancedDetails'     => __( 'Advanced details', 'cloud-gaming-readiness-test' ),
						'whatThisMeans'       => __( 'What this means for cloud gaming', 'cloud-gaming-readiness-test' ),
						'recommendations'     => __( 'Recommendations', 'cloud-gaming-readiness-test' ),
						'disclaimerTitle'     => __( 'Accuracy notes', 'cloud-gaming-readiness-test' ),
						'disclaimerBody'      => __( 'This test measures real HTTP timing from your device to selected endpoints. For best accuracy, endpoints should be hosted close to the cloud gaming edge and must allow cross-origin requests (CORS).', 'cloud-gaming-readiness-test' ),
					),
				)
			),
			'before'
		);
	}
}
