<?php
/**
 * Settings Management
 *
 * @package CloudGamingAvailability
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CGA_Settings class
 */
class CGA_Settings {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_ajax_cga_upload_logo', array( $this, 'handle_logo_upload' ) );
		add_action( 'wp_ajax_cga_delete_logo', array( $this, 'handle_logo_delete' ) );
	}

	/**
	 * Add settings page to admin menu
	 */
	public function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=cloud_games',
			__( 'Cloud Gaming Settings', 'cloud-gaming-availability' ),
			__( 'Settings', 'cloud-gaming-availability' ),
			'manage_options',
			'cga-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting(
			'cga_settings_group',
			'cga_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'show_in_rest'      => true,
			)
		);

		register_setting(
			'cga_settings_group',
			'cga_platform_logos',
			array(
				'type'         => 'array',
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Sanitize settings input
	 *
	 * @param array $input Settings array.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		if ( isset( $input['theme'] ) ) {
			$sanitized['theme'] = sanitize_text_field( $input['theme'] );
		}

		if ( isset( $input['button_color'] ) ) {
			$sanitized['button_color'] = sanitize_hex_color( $input['button_color'] );
		}

		if ( isset( $input['text_color_light'] ) ) {
			$sanitized['text_color_light'] = sanitize_hex_color( $input['text_color_light'] );
		}

		if ( isset( $input['text_color_dark'] ) ) {
			$sanitized['text_color_dark'] = sanitize_hex_color( $input['text_color_dark'] );
		}

		if ( isset( $input['border_radius'] ) ) {
			$sanitized['border_radius'] = absint( $input['border_radius'] );
		}

		if ( isset( $input['spacing'] ) ) {
			$sanitized['spacing'] = absint( $input['spacing'] );
		}

		if ( isset( $input['padding'] ) ) {
			$sanitized['padding'] = absint( $input['padding'] );
		}

		if ( isset( $input['logo_size'] ) ) {
			$sanitized['logo_size'] = absint( $input['logo_size'] );
		}

		if ( isset( $input['enable_filters'] ) ) {
			$sanitized['enable_filters'] = $input['enable_filters'] ? '1' : '0';
		}

		return $sanitized;
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = get_option( 'cga_settings', array() );
		$logos    = get_option( 'cga_platform_logos', array() );
		$platforms = CGA_Loader::get_platforms();
		?>
		<div class="wrap cga-settings-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p><?php esc_html_e( 'Customize your cloud gaming availability display', 'cloud-gaming-availability' ); ?></p>

			<form method="post" action="options.php" class="cga-settings-form">
				<?php
				settings_fields( 'cga_settings_group' );
				wp_nonce_field( 'cga_settings_nonce', 'cga_settings_nonce' );
				?>

				<!-- Theme Section -->
				<div class="cga-settings-section">
					<h2><?php esc_html_e( 'Theme Settings', 'cloud-gaming-availability' ); ?></h2>
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="cga_theme"><?php esc_html_e( 'Theme', 'cloud-gaming-availability' ); ?></label>
							</th>
							<td>
								<select name="cga_settings[theme]" id="cga_theme">
									<option value="light" <?php selected( isset( $settings['theme'] ) ? $settings['theme'] : 'light', 'light' ); ?>>
										<?php esc_html_e( 'Light', 'cloud-gaming-availability' ); ?>
									</option>
									<option value="dark" <?php selected( isset( $settings['theme'] ) ? $settings['theme'] : '', 'dark' ); ?>>
										<?php esc_html_e( 'Dark', 'cloud-gaming-availability' ); ?>
									</option>
									<option value="auto" <?php selected( isset( $settings['theme'] ) ? $settings['theme'] : '', 'auto' ); ?>>
										<?php esc_html_e( 'Auto (System)', 'cloud-gaming-availability' ); ?>
									</option>
								</select>
								<p class="description"><?php esc_html_e( 'Select the default theme for the cloud gaming availability display.', 'cloud-gaming-availability' ); ?></p>
							</td>
						</tr>
					</table>
				</div>

				<!-- Color Section -->
				<div class="cga-settings-section">
					<h2><?php esc_html_e( 'Color Settings', 'cloud-gaming-availability' ); ?></h2>
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="cga_button_color"><?php esc_html_e( 'Button Color', 'cloud-gaming-availability' ); ?></label>
							</th>
							<td>
								<input type="color" name="cga_settings[button_color]" id="cga_button_color" value="<?php echo isset( $settings['button_color'] ) ? esc_attr( $settings['button_color'] ) : '#007cba'; ?>">
								<p class="description"><?php esc_html_e( 'Color for the Play buttons on available platforms.', 'cloud-gaming-availability' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="cga_text_color_light"><?php esc_html_e( 'Text Color (Light Theme)', 'cloud-gaming-availability' ); ?></label>
							</th>
							<td>
								<input type="color" name="cga_settings[text_color_light]" id="cga_text_color_light" value="<?php echo isset( $settings['text_color_light'] ) ? esc_attr( $settings['text_color_light'] ) : '#000000'; ?>">
								<p class="description"><?php esc_html_e( 'Text color for light theme.', 'cloud-gaming-availability' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="cga_text_color_dark"><?php esc_html_e( 'Text Color (Dark Theme)', 'cloud-gaming-availability' ); ?></label>
							</th>
							<td>
								<input type="color" name="cga_settings[text_color_dark]" id="cga_text_color_dark" value="<?php echo isset( $settings['text_color_dark'] ) ? esc_attr( $settings['text_color_dark'] ) : '#ffffff'; ?>">
								<p class="description"><?php esc_html_e( 'Text color for dark theme.', 'cloud-gaming-availability' ); ?></p>
							</td>
						</tr>
					</table>
				</div>

				<!-- Design Section -->
				<div class="cga-settings-section">
					<h2><?php esc_html_e( 'Design Settings', 'cloud-gaming-availability' ); ?></h2>
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="cga_border_radius"><?php esc_html_e( 'Border Radius (px)', 'cloud-gaming-availability' ); ?></label>
							</th>
							<td>
								<input type="number" name="cga_settings[border_radius]" id="cga_border_radius" min="0" max="50" value="<?php echo isset( $settings['border_radius'] ) ? esc_attr( $settings['border_radius'] ) : '4'; ?>">
								<p class="description"><?php esc_html_e( 'Border radius for buttons and containers.', 'cloud-gaming-availability' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="cga_spacing"><?php esc_html_e( 'Spacing (px)', 'cloud-gaming-availability' ); ?></label>
							</th>
							<td>
								<input type="number" name="cga_settings[spacing]" id="cga_spacing" min="0" max="50" value="<?php echo isset( $settings['spacing'] ) ? esc_attr( $settings['spacing'] ) : '12'; ?>">
								<p class="description"><?php esc_html_e( 'Space between platform items.', 'cloud-gaming-availability' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="cga_padding"><?php esc_html_e( 'Padding (px)', 'cloud-gaming-availability' ); ?></label>
							</th>
							<td>
								<input type="number" name="cga_settings[padding]" id="cga_padding" min="0" max="50" value="<?php echo isset( $settings['padding'] ) ? esc_attr( $settings['padding'] ) : '8'; ?>">
								<p class="description"><?php esc_html_e( 'Padding inside buttons and containers.', 'cloud-gaming-availability' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="cga_logo_size"><?php esc_html_e( 'Logo Size (px)', 'cloud-gaming-availability' ); ?></label>
							</th>
							<td>
								<input type="number" name="cga_settings[logo_size]" id="cga_logo_size" min="24" max="128" value="<?php echo isset( $settings['logo_size'] ) ? esc_attr( $settings['logo_size'] ) : '48'; ?>">
								<p class="description"><?php esc_html_e( 'Size of platform logos.', 'cloud-gaming-availability' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="cga_enable_filters"><?php esc_html_e( 'Enable Platform Filters', 'cloud-gaming-availability' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="cga_settings[enable_filters]" id="cga_enable_filters" value="1" <?php checked( isset( $settings['enable_filters'] ) ? $settings['enable_filters'] : '1', '1' ); ?>>
								<p class="description"><?php esc_html_e( 'Allow filtering by platform on the frontend.', 'cloud-gaming-availability' ); ?></p>
							</td>
						</tr>
					</table>
				</div>

				<!-- Platform Logos Section -->
				<div class="cga-settings-section">
					<h2><?php esc_html_e( 'Platform Logos', 'cloud-gaming-availability' ); ?></h2>
					<p><?php esc_html_e( 'Upload custom logos for each platform. If no logo is provided, a placeholder will be used.', 'cloud-gaming-availability' ); ?></p>
					<div class="cga-logos-grid">
						<?php
						foreach ( $platforms as $platform_id => $platform ) {
							$logo_url = isset( $logos[ $platform_id ] ) ? $logos[ $platform_id ] : '';
							?>
							<div class="cga-logo-item">
								<h3><?php echo esc_html( $platform['name'] ); ?></h3>
								<div class="cga-logo-preview">
									<?php if ( $logo_url ) : ?>
										<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $platform['name'] ); ?>">
									<?php else : ?>
										<div class="cga-logo-placeholder"></div>
									<?php endif; ?>
								</div>
								<button type="button" class="button cga-upload-logo-btn" data-platform="<?php echo esc_attr( $platform_id ); ?>">
									<?php esc_html_e( 'Upload Logo', 'cloud-gaming-availability' ); ?>
								</button>
								<?php if ( $logo_url ) : ?>
									<button type="button" class="button button-danger cga-delete-logo-btn" data-platform="<?php echo esc_attr( $platform_id ); ?>">
										<?php esc_html_e( 'Delete', 'cloud-gaming-availability' ); ?>
									</button>
								<?php endif; ?>
								<input type="hidden" class="cga-logo-input" name="cga_platform_logos[<?php echo esc_attr( $platform_id ); ?>]" value="<?php echo esc_attr( $logo_url ); ?>">
							</div>
							<?php
						}
						?>
					</div>
				</div>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Handle logo upload via AJAX
	 */
	public function handle_logo_upload() {
		check_ajax_referer( 'cga_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You do not have permission to upload logos.', 'cloud-gaming-availability' ) );
		}

		if ( ! isset( $_FILES['logo'] ) ) {
			wp_send_json_error( __( 'No file provided.', 'cloud-gaming-availability' ) );
		}

		$platform = isset( $_POST['platform'] ) ? sanitize_text_field( $_POST['platform'] ) : '';

		if ( ! $platform ) {
			wp_send_json_error( __( 'Invalid platform.', 'cloud-gaming-availability' ) );
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$attachment_id = media_handle_upload( 'logo', 0 );

		if ( is_wp_error( $attachment_id ) ) {
			wp_send_json_error( $attachment_id->get_error_message() );
		}

		$logo_url = wp_get_attachment_url( $attachment_id );

		$logos            = get_option( 'cga_platform_logos', array() );
		$logos[ $platform ] = $logo_url;
		update_option( 'cga_platform_logos', $logos );

		wp_send_json_success(
			array(
				'url'            => $logo_url,
				'attachment_id'  => $attachment_id,
			)
		);
	}

	/**
	 * Handle logo deletion via AJAX
	 */
	public function handle_logo_delete() {
		check_ajax_referer( 'cga_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You do not have permission to delete logos.', 'cloud-gaming-availability' ) );
		}

		$platform = isset( $_POST['platform'] ) ? sanitize_text_field( $_POST['platform'] ) : '';

		if ( ! $platform ) {
			wp_send_json_error( __( 'Invalid platform.', 'cloud-gaming-availability' ) );
		}

		$logos = get_option( 'cga_platform_logos', array() );
		unset( $logos[ $platform ] );
		update_option( 'cga_platform_logos', $logos );

		wp_send_json_success();
	}
}
