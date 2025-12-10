<?php
/**
 * Shortcode Handler
 *
 * @package CloudGamingAvailability
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CGA_Shortcode class
 */
class CGA_Shortcode {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'cloud_gaming_availability', array( $this, 'render_shortcode' ) );
		add_action( 'wp_ajax_cga_filter_platforms', array( $this, 'handle_platform_filter' ) );
		add_action( 'wp_ajax_nopriv_cga_filter_platforms', array( $this, 'handle_platform_filter' ) );
	}

	/**
	 * Render shortcode
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'game_id'         => 0,
				'theme'           => 'auto',
				'columns'         => 3,
				'show_description' => 'true',
			),
			$atts
		);

		$game_id = absint( $atts['game_id'] );
		$theme   = sanitize_text_field( $atts['theme'] );
		$columns = absint( $atts['columns'] );
		$show_description = 'true' === $atts['show_description'];

		if ( ! $game_id ) {
			return '<div class="cga-error">' . esc_html__( 'Game ID is required.', 'cloud-gaming-availability' ) . '</div>';
		}

		$game = get_post( $game_id );

		if ( ! $game || 'cloud_games' !== $game->post_type ) {
			return '<div class="cga-error">' . esc_html__( 'Invalid game.', 'cloud-gaming-availability' ) . '</div>';
		}

		// Get settings
		$settings = get_option( 'cga_settings', array() );
		$logos    = get_option( 'cga_platform_logos', array() );

		// Determine theme
		if ( 'auto' === $theme ) {
			$theme = isset( $settings['theme'] ) ? $settings['theme'] : 'light';
		}

		// Get platform availability
		$availability = get_post_meta( $game_id, 'cga_platform_availability', true );
		if ( ! is_array( $availability ) ) {
			$availability = array();
		}

		// Build CSS variables
		$css_vars = $this->get_css_variables( $settings, $theme );

		// Start output buffering
		ob_start();
		?>
		<div class="cga-container cga-theme-<?php echo esc_attr( $theme ); ?>" style="<?php echo esc_attr( $css_vars ); ?>">
			<div class="cga-game-card">
				<?php if ( has_post_thumbnail( $game_id ) ) : ?>
					<div class="cga-game-cover">
						<?php echo get_the_post_thumbnail( $game_id, 'medium', array( 'class' => 'cga-cover-image' ) ); ?>
					</div>
				<?php endif; ?>

				<div class="cga-game-content">
					<h2 class="cga-game-title"><?php echo esc_html( $game->post_title ); ?></h2>

					<?php if ( $show_description && $game->post_content ) : ?>
						<div class="cga-game-description">
							<?php echo wp_kses_post( wp_trim_words( $game->post_content, 30 ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( isset( $settings['enable_filters'] ) && '1' === $settings['enable_filters'] ) : ?>
						<div class="cga-platform-filters">
							<button class="cga-filter-btn cga-filter-all" data-filter="all">
								<?php esc_html_e( 'All', 'cloud-gaming-availability' ); ?>
							</button>
							<?php
							foreach ( CGA_Loader::get_platforms() as $platform_id => $platform ) {
								?>
								<button class="cga-filter-btn" data-filter="<?php echo esc_attr( $platform_id ); ?>">
									<?php echo esc_html( $platform['name'] ); ?>
								</button>
								<?php
							}
							?>
						</div>
					<?php endif; ?>

					<div class="cga-platforms-grid cga-columns-<?php echo esc_attr( $columns ); ?>">
						<?php
						foreach ( CGA_Loader::get_platforms() as $platform_id => $platform ) {
							$is_available = in_array( $platform_id, $availability, true );
							$logo_url     = isset( $logos[ $platform_id ] ) ? $logos[ $platform_id ] : '';
							$filter_class = $is_available ? 'cga-available' : 'cga-unavailable';

							?>
							<div class="cga-platform-item <?php echo esc_attr( $filter_class ); ?>" data-platform="<?php echo esc_attr( $platform_id ); ?>">
								<div class="cga-platform-logo-wrapper">
									<?php if ( $logo_url ) : ?>
										<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $platform['name'] ); ?>" class="cga-platform-logo">
									<?php else : ?>
										<div class="cga-platform-logo-placeholder">
											<span><?php echo esc_html( substr( $platform['name'], 0, 1 ) ); ?></span>
										</div>
									<?php endif; ?>
									<?php if ( $is_available ) : ?>
										<span class="cga-status-badge cga-available-badge"><?php esc_html_e( 'Available', 'cloud-gaming-availability' ); ?></span>
									<?php else : ?>
										<span class="cga-status-badge cga-unavailable-badge"><?php esc_html_e( 'Unavailable', 'cloud-gaming-availability' ); ?></span>
									<?php endif; ?>
								</div>

								<h3 class="cga-platform-name"><?php echo esc_html( $platform['name'] ); ?></h3>

								<?php if ( $is_available ) : ?>
									<a href="<?php echo esc_url( $platform['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="cga-play-button">
										<?php esc_html_e( 'Play Now', 'cloud-gaming-availability' ); ?>
										<span class="cga-icon-arrow">→</span>
									</a>
								<?php else : ?>
									<button class="cga-play-button cga-unavailable" disabled>
										<?php esc_html_e( 'Not Available', 'cloud-gaming-availability' ); ?>
									</button>
								<?php endif; ?>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Get CSS variables for theming
	 *
	 * @param array  $settings The settings array.
	 * @param string $theme The theme.
	 * @return string
	 */
	private function get_css_variables( $settings, $theme ) {
		$button_color     = isset( $settings['button_color'] ) ? $settings['button_color'] : '#007cba';
		$text_color       = 'dark' === $theme ? ( isset( $settings['text_color_dark'] ) ? $settings['text_color_dark'] : '#ffffff' ) : ( isset( $settings['text_color_light'] ) ? $settings['text_color_light'] : '#000000' );
		$border_radius    = isset( $settings['border_radius'] ) ? absint( $settings['border_radius'] ) : 4;
		$spacing          = isset( $settings['spacing'] ) ? absint( $settings['spacing'] ) : 12;
		$padding          = isset( $settings['padding'] ) ? absint( $settings['padding'] ) : 8;
		$logo_size        = isset( $settings['logo_size'] ) ? absint( $settings['logo_size'] ) : 48;

		return sprintf(
			'--cga-button-color: %s; --cga-text-color: %s; --cga-border-radius: %dpx; --cga-spacing: %dpx; --cga-padding: %dpx; --cga-logo-size: %dpx;',
			esc_attr( $button_color ),
			esc_attr( $text_color ),
			$border_radius,
			$spacing,
			$padding,
			$logo_size
		);
	}

	/**
	 * Handle platform filtering via AJAX
	 */
	public function handle_platform_filter() {
		check_ajax_referer( 'cga_frontend_nonce', 'nonce' );

		$filter = sanitize_text_field( $_POST['filter'] );

		if ( 'all' === $filter ) {
			wp_send_json_success( array( 'filter' => 'all' ) );
		}

		$platforms = CGA_Loader::get_platforms();

		if ( ! isset( $platforms[ $filter ] ) ) {
			wp_send_json_error( __( 'Invalid filter.', 'cloud-gaming-availability' ) );
		}

		wp_send_json_success( array( 'filter' => $filter ) );
	}
}
