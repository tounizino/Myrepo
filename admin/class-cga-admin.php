<?php
/**
 * Admin functionality
 *
 * @package CloudGamingAvailability
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CGA_Admin class
 */
class CGA_Admin {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post_cloud_games', array( $this, 'save_metabox' ) );
		add_filter( 'manage_cloud_games_posts_columns', array( $this, 'add_admin_columns' ) );
		add_action( 'manage_cloud_games_posts_custom_column', array( $this, 'display_admin_columns' ), 10, 2 );
	}

	/**
	 * Add metabox for platform availability
	 */
	public function add_metabox() {
		add_meta_box(
			'cga_platform_availability',
			__( 'Platform Availability', 'cloud-gaming-availability' ),
			array( $this, 'render_metabox' ),
			'cloud_games',
			'normal',
			'high'
		);
	}

	/**
	 * Render platform availability metabox
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_metabox( $post ) {
		wp_nonce_field( 'cga_metabox_nonce', 'cga_metabox_nonce' );

		$platforms = CGA_Loader::get_platforms();
		$available = get_post_meta( $post->ID, 'cga_platform_availability', true );

		if ( ! is_array( $available ) ) {
			$available = array();
		}

		?>
		<div class="cga-metabox-content">
			<p><?php esc_html_e( 'Select which platforms this game is available on:', 'cloud-gaming-availability' ); ?></p>
			<div class="cga-platform-checkboxes">
				<?php
				foreach ( $platforms as $platform_id => $platform ) {
					$checked = in_array( $platform_id, $available, true ) ? 'checked' : '';
					?>
					<label class="cga-checkbox-label">
						<input type="checkbox" name="cga_platform_availability[]" value="<?php echo esc_attr( $platform_id ); ?>" <?php echo esc_attr( $checked ); ?>>
						<span><?php echo esc_html( $platform['name'] ); ?></span>
					</label>
					<?php
				}
				?>
			</div>
			<p class="description"><?php esc_html_e( 'Platforms marked as available will show a Play button on the frontend.', 'cloud-gaming-availability' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Save metabox data
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_metabox( $post_id ) {
		// Verify nonce
		if ( ! isset( $_POST['cga_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['cga_metabox_nonce'], 'cga_metabox_nonce' ) ) {
			return;
		}

		// Check if user can edit post
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Prevent autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Get and sanitize platform availability
		$availability = isset( $_POST['cga_platform_availability'] ) ? $_POST['cga_platform_availability'] : array();
		$availability = array_map( 'sanitize_text_field', $availability );

		// Save to post meta
		if ( ! empty( $availability ) ) {
			update_post_meta( $post_id, 'cga_platform_availability', $availability );
		} else {
			delete_post_meta( $post_id, 'cga_platform_availability' );
		}
	}

	/**
	 * Add custom columns to cloud games list
	 *
	 * @param array $columns The columns.
	 * @return array
	 */
	public function add_admin_columns( $columns ) {
		$columns['platforms'] = __( 'Available Platforms', 'cloud-gaming-availability' );
		return $columns;
	}

	/**
	 * Display custom admin columns
	 *
	 * @param string $column The column name.
	 * @param int    $post_id The post ID.
	 */
	public function display_admin_columns( $column, $post_id ) {
		if ( 'platforms' === $column ) {
			$availability = get_post_meta( $post_id, 'cga_platform_availability', true );

			if ( empty( $availability ) ) {
				echo '<em>' . esc_html__( 'No platforms selected', 'cloud-gaming-availability' ) . '</em>';
				return;
			}

			$platforms = CGA_Loader::get_platforms();
			$platform_names = array();

			foreach ( $availability as $platform_id ) {
				if ( isset( $platforms[ $platform_id ] ) ) {
					$platform_names[] = $platforms[ $platform_id ]['name'];
				}
			}

			echo esc_html( implode( ', ', $platform_names ) );
		}
	}
}
