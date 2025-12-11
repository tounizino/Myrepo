<?php
/**
 * Custom Game Manager Page
 *
 * @package PlatformsForCloudGames
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CGA_Game_Manager class
 */
class CGA_Game_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_game_manager_page' ) );
		add_action( 'admin_post_cga_save_game', array( $this, 'save_game' ) );
		add_action( 'admin_post_cga_delete_game', array( $this, 'delete_game' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add game manager page to menu
	 */
	public function add_game_manager_page() {
		add_submenu_page(
			'edit.php?post_type=cloud_games',
			__( 'Manage Games', 'platforms-cloud-games' ),
			__( 'Manage Games', 'platforms-cloud-games' ),
			'manage_options',
			'cga-game-manager',
			array( $this, 'render_game_manager_page' )
		);
	}

	/**
	 * Enqueue assets for game manager
	 */
	public function enqueue_assets() {
		$screen = get_current_screen();
		if ( ! isset( $screen ) || 'cloud_games_page_cga-game-manager' !== $screen->id ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}

	/**
	 * Render game manager page
	 */
	public function render_game_manager_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage games.', 'platforms-cloud-games' ) );
		}

		$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		$game_id = isset( $_GET['game_id'] ) ? absint( $_GET['game_id'] ) : 0;

		if ( 'edit' === $action && $game_id ) {
			$this->render_edit_game_form( $game_id );
		} else {
			$this->render_games_list();
		}
	}

	/**
	 * Render games list
	 */
	private function render_games_list() {
		$games = get_posts( array(
			'post_type'      => 'cloud_games',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		?>
		<div class="wrap cga-game-manager">
			<h1><?php esc_html_e( 'Manage Games', 'platforms-cloud-games' ); ?></h1>

			<div class="cga-manager-header">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=cga-game-manager&action=add' ) ); ?>" class="button button-primary">
					<?php esc_html_e( '+ Add New Game', 'platforms-cloud-games' ); ?>
				</a>
			</div>

			<?php
			if ( ! empty( $games ) ) {
				?>
				<table class="widefat striped cga-games-table">
					<thead>
						<tr>
							<th width="30%"><?php esc_html_e( 'Game Title', 'platforms-cloud-games' ); ?></th>
							<th width="40%"><?php esc_html_e( 'Available Platforms', 'platforms-cloud-games' ); ?></th>
							<th width="20%"><?php esc_html_e( 'Shortcode', 'platforms-cloud-games' ); ?></th>
							<th width="10%"><?php esc_html_e( 'Actions', 'platforms-cloud-games' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $games as $game ) {
							$availability = get_post_meta( $game->ID, 'cga_platform_availability', true );
							$availability = is_array( $availability ) ? $availability : array();
							$platforms = CGA_Loader::get_platforms();
							$platform_names = array();

							foreach ( $availability as $platform_id ) {
								if ( isset( $platforms[ $platform_id ] ) ) {
									$platform_names[] = $platforms[ $platform_id ]['name'];
								}
							}

							$shortcode = '[cloud_gaming_availability game_id="' . $game->ID . '"]';
							?>
							<tr>
								<td>
									<strong><?php echo esc_html( $game->post_title ); ?></strong>
								</td>
								<td>
									<?php
									if ( ! empty( $platform_names ) ) {
										echo esc_html( implode( ', ', $platform_names ) );
									} else {
										echo '<em>' . esc_html__( 'No platforms selected', 'platforms-cloud-games' ) . '</em>';
									}
									?>
								</td>
								<td>
									<code class="cga-shortcode-copy" data-shortcode="<?php echo esc_attr( $shortcode ); ?>">
										<?php echo esc_html( $shortcode ); ?>
									</code>
									<button type="button" class="cga-copy-btn button button-small" data-shortcode="<?php echo esc_attr( $shortcode ); ?>" title="<?php esc_attr_e( 'Copy to clipboard', 'platforms-cloud-games' ); ?>">
										📋
									</button>
								</td>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=cga-game-manager&action=edit&game_id=' . $game->ID ) ); ?>" class="button button-small">
										<?php esc_html_e( 'Edit', 'platforms-cloud-games' ); ?>
									</a>
									<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=cga_delete_game&game_id=' . $game->ID ), 'cga_delete_game_' . $game->ID ) ); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'platforms-cloud-games' ); ?>');">
										<?php esc_html_e( 'Delete', 'platforms-cloud-games' ); ?>
									</a>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<?php
			} else {
				?>
				<div class="notice notice-info">
					<p><?php esc_html_e( 'No games yet. Add your first game!', 'platforms-cloud-games' ); ?></p>
				</div>
				<?php
			}
			?>
		</div>

		<script>
		jQuery(document).ready(function($) {
			$('.cga-copy-btn').on('click', function(e) {
				e.preventDefault();
				var shortcode = $(this).data('shortcode');
				var $temp = $('<textarea>');
				$('body').append($temp);
				$temp.val(shortcode).select();
				document.execCommand('copy');
				$temp.remove();

				var originalText = $(this).text();
				$(this).text('✓');
				var $btn = $(this);
				setTimeout(function() {
					$btn.text(originalText);
				}, 2000);
			});
		});
		</script>
		<?php
	}

	/**
	 * Render add/edit game form
	 *
	 * @param int $game_id Game ID for edit mode, 0 for add mode.
	 */
	private function render_edit_game_form( $game_id = 0 ) {
		$is_edit = $game_id > 0;
		$game = null;
		$title = '';
		$description = '';
		$selected_platforms = array();

		if ( $is_edit ) {
			$game = get_post( $game_id );
			if ( ! $game || 'cloud_games' !== $game->post_type ) {
				wp_die( esc_html__( 'Game not found.', 'platforms-cloud-games' ) );
			}
			$title = $game->post_title;
			$description = $game->post_content;
			$selected_platforms = get_post_meta( $game_id, 'cga_platform_availability', true );
			$selected_platforms = is_array( $selected_platforms ) ? $selected_platforms : array();
		}

		$platforms = CGA_Loader::get_platforms();
		$thumbnail_id = $is_edit ? get_post_thumbnail_id( $game_id ) : 0;
		$thumbnail_url = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : '';

		?>
		<div class="wrap cga-game-manager">
			<h1><?php echo $is_edit ? esc_html__( 'Edit Game', 'platforms-cloud-games' ) : esc_html__( 'Add New Game', 'platforms-cloud-games' ); ?></h1>

			<a href="<?php echo esc_url( admin_url( 'admin.php?page=cga-game-manager' ) ); ?>" class="button">
				<?php esc_html_e( '← Back to Games', 'platforms-cloud-games' ); ?>
			</a>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="cga-game-form">
				<?php wp_nonce_field( 'cga_save_game', 'cga_save_game_nonce' ); ?>
				<input type="hidden" name="action" value="cga_save_game">
				<input type="hidden" name="game_id" value="<?php echo esc_attr( $game_id ); ?>">
				<input type="hidden" name="thumbnail_id" id="cga_thumbnail_id" value="<?php echo esc_attr( $thumbnail_id ); ?>">

				<div class="cga-form-section">
					<h2><?php esc_html_e( 'Game Information', 'platforms-cloud-games' ); ?></h2>

					<div class="form-group">
						<label for="cga_title"><?php esc_html_e( 'Game Title', 'platforms-cloud-games' ); ?> <span class="required">*</span></label>
						<input type="text" id="cga_title" name="game_title" value="<?php echo esc_attr( $title ); ?>" required class="form-control" placeholder="<?php esc_attr_e( 'Enter game title', 'platforms-cloud-games' ); ?>">
					</div>

					<div class="form-group">
						<label for="cga_description"><?php esc_html_e( 'Game Description', 'platforms-cloud-games' ); ?></label>
						<textarea id="cga_description" name="game_description" rows="6" class="form-control" placeholder="<?php esc_attr_e( 'Enter game description', 'platforms-cloud-games' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
					</div>

					<div class="form-group">
						<label><?php esc_html_e( 'Game Cover Image', 'platforms-cloud-games' ); ?></label>
						<div class="cga-thumbnail-wrapper">
							<div id="cga_thumbnail_preview" class="cga-thumbnail-preview">
								<?php if ( $thumbnail_url ) : ?>
									<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php esc_attr_e( 'Game Cover', 'platforms-cloud-games' ); ?>">
								<?php else : ?>
									<p><?php esc_html_e( 'No image selected', 'platforms-cloud-games' ); ?></p>
								<?php endif; ?>
							</div>
							<button type="button" id="cga_upload_image_btn" class="button button-secondary">
								<?php esc_html_e( 'Upload Image', 'platforms-cloud-games' ); ?>
							</button>
							<?php if ( $thumbnail_url ) : ?>
								<button type="button" id="cga_remove_image_btn" class="button button-link-delete">
									<?php esc_html_e( 'Remove Image', 'platforms-cloud-games' ); ?>
								</button>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<div class="cga-form-section">
					<h2><?php esc_html_e( 'Platform Availability', 'platforms-cloud-games' ); ?></h2>
					<p><?php esc_html_e( 'Select which platforms this game is available on:', 'platforms-cloud-games' ); ?></p>

					<div class="cga-platforms-checkboxes">
						<?php
						foreach ( $platforms as $platform_id => $platform ) {
							$checked = in_array( $platform_id, $selected_platforms, true ) ? 'checked' : '';
							?>
							<label class="cga-platform-checkbox">
								<input type="checkbox" name="platforms[]" value="<?php echo esc_attr( $platform_id ); ?>" <?php echo esc_attr( $checked ); ?>>
								<span><?php echo esc_html( $platform['name'] ); ?></span>
							</label>
							<?php
						}
						?>
					</div>
				</div>

				<div class="cga-form-actions">
					<button type="submit" class="button button-primary button-large">
						<?php echo $is_edit ? esc_html__( 'Update Game', 'platforms-cloud-games' ) : esc_html__( 'Create Game', 'platforms-cloud-games' ); ?>
					</button>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=cga-game-manager' ) ); ?>" class="button button-large">
						<?php esc_html_e( 'Cancel', 'platforms-cloud-games' ); ?>
					</a>
				</div>
			</form>
		</div>

		<style>
			.cga-game-form {
				max-width: 800px;
				background: white;
				padding: 20px;
				margin-top: 20px;
				border-radius: 4px;
			}

			.cga-form-section {
				margin-bottom: 30px;
				padding-bottom: 30px;
				border-bottom: 1px solid #e5e5e5;
			}

			.cga-form-section:last-child {
				border-bottom: none;
				padding-bottom: 0;
				margin-bottom: 0;
			}

			.cga-form-section h2 {
				margin-top: 0;
				margin-bottom: 20px;
				font-size: 18px;
			}

			.form-group {
				margin-bottom: 20px;
			}

			.form-group label {
				display: block;
				margin-bottom: 8px;
				font-weight: 600;
				color: #333;
			}

			.form-group .required {
				color: #dc3545;
			}

			.form-control {
				width: 100%;
				padding: 10px;
				border: 1px solid #ddd;
				border-radius: 4px;
				font-size: 14px;
				font-family: inherit;
			}

			.form-control:focus {
				outline: none;
				border-color: #007cba;
				box-shadow: 0 0 0 3px rgba(0, 124, 186, 0.1);
			}

			.cga-thumbnail-wrapper {
				padding: 20px;
				background: #f9f9f9;
				border: 2px dashed #ddd;
				border-radius: 4px;
				text-align: center;
			}

			.cga-thumbnail-preview {
				margin-bottom: 15px;
				min-height: 150px;
				display: flex;
				align-items: center;
				justify-content: center;
				background: white;
				border-radius: 4px;
			}

			.cga-thumbnail-preview img {
				max-width: 100%;
				max-height: 300px;
				object-fit: contain;
			}

			.cga-platforms-checkboxes {
				display: grid;
				grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
				gap: 15px;
				padding: 15px;
				background: #f9f9f9;
				border-radius: 4px;
			}

			.cga-platform-checkbox {
				display: flex;
				align-items: center;
				padding: 10px;
				background: white;
				border: 1px solid #ddd;
				border-radius: 4px;
				cursor: pointer;
				user-select: none;
				transition: all 0.2s;
			}

			.cga-platform-checkbox:hover {
				background: #f0f0f0;
				border-color: #999;
			}

			.cga-platform-checkbox input[type="checkbox"] {
				margin-right: 10px;
				cursor: pointer;
				width: 18px;
				height: 18px;
			}

			.cga-platform-checkbox input[type="checkbox"]:checked + span {
				font-weight: 600;
				color: #007cba;
			}

			.cga-form-actions {
				margin-top: 30px;
				padding-top: 20px;
				border-top: 1px solid #e5e5e5;
			}

			.cga-form-actions button,
			.cga-form-actions a {
				margin-right: 10px;
			}

			@media (max-width: 768px) {
				.cga-platforms-checkboxes {
					grid-template-columns: 1fr;
				}

				.form-control {
					font-size: 16px;
				}
			}
		</style>

		<script>
		jQuery(document).ready(function($) {
			wp_media_loader = wp.media({
				title: '<?php esc_js_e( 'Select Game Cover Image', 'platforms-cloud-games' ); ?>',
				button: { text: '<?php esc_js_e( 'Use this image', 'platforms-cloud-games' ); ?>' },
				multiple: false,
				library: { type: 'image' }
			});

			$('#cga_upload_image_btn').on('click', function(e) {
				e.preventDefault();
				wp_media_loader.open();
			});

			wp_media_loader.on('select', function() {
				var attachment = wp_media_loader.state().get('selection').first().toJSON();
				$('#cga_thumbnail_id').val(attachment.id);
				$('#cga_thumbnail_preview').html('<img src="' + attachment.url + '" alt="Game Cover">');
				
				if ($('#cga_remove_image_btn').length === 0) {
					$('#cga_upload_image_btn').after('<button type="button" id="cga_remove_image_btn" class="button button-link-delete"><?php esc_js_e( 'Remove Image', 'platforms-cloud-games' ); ?></button>');
				}
				
				handleRemoveImageButton();
			});

			function handleRemoveImageButton() {
				$(document).off('click', '#cga_remove_image_btn').on('click', '#cga_remove_image_btn', function(e) {
					e.preventDefault();
					$('#cga_thumbnail_id').val('0');
					$('#cga_thumbnail_preview').html('<p><?php esc_js_e( 'No image selected', 'platforms-cloud-games' ); ?></p>');
					$(this).remove();
				});
			}

			handleRemoveImageButton();
		});
		</script>
		<?php
	}

	/**
	 * Save game
	 */
	public function save_game() {
		if ( ! isset( $_POST['cga_save_game_nonce'] ) || ! wp_verify_nonce( $_POST['cga_save_game_nonce'], 'cga_save_game' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'platforms-cloud-games' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to save games.', 'platforms-cloud-games' ) );
		}

		$game_id = isset( $_POST['game_id'] ) ? absint( $_POST['game_id'] ) : 0;
		$title = isset( $_POST['game_title'] ) ? sanitize_text_field( $_POST['game_title'] ) : '';
		$description = isset( $_POST['game_description'] ) ? wp_kses_post( $_POST['game_description'] ) : '';
		$platforms = isset( $_POST['platforms'] ) ? array_map( 'sanitize_text_field', $_POST['platforms'] ) : array();
		$thumbnail_id = isset( $_POST['thumbnail_id'] ) ? absint( $_POST['thumbnail_id'] ) : 0;

		if ( empty( $title ) ) {
			wp_safe_remote_post( admin_url( 'admin.php?page=cga-game-manager' ), array(
				'blocking' => false,
			) );
			wp_die( esc_html__( 'Game title is required.', 'platforms-cloud-games' ) );
		}

		if ( $game_id ) {
			// Update existing game
			wp_update_post( array(
				'ID'           => $game_id,
				'post_title'   => $title,
				'post_content' => $description,
				'post_type'    => 'cloud_games',
			) );
		} else {
			// Create new game
			$game_id = wp_insert_post( array(
				'post_type'   => 'cloud_games',
				'post_title'  => $title,
				'post_content' => $description,
				'post_status' => 'publish',
			) );
		}

		// Set featured image
		if ( $thumbnail_id > 0 ) {
			set_post_thumbnail( $game_id, $thumbnail_id );
		} else {
			delete_post_thumbnail( $game_id );
		}

		// Save platform availability
		if ( ! empty( $platforms ) ) {
			update_post_meta( $game_id, 'cga_platform_availability', $platforms );
		} else {
			delete_post_meta( $game_id, 'cga_platform_availability' );
		}

		// Redirect back to manager with success message
		wp_safe_remote_post( admin_url( 'admin.php?page=cga-game-manager&success=1' ), array(
			'blocking' => false,
		) );

		wp_safe_redirect( admin_url( 'admin.php?page=cga-game-manager' ) );
		exit;
	}

	/**
	 * Delete game
	 */
	public function delete_game() {
		$game_id = isset( $_GET['game_id'] ) ? absint( $_GET['game_id'] ) : 0;

		if ( ! $game_id ) {
			wp_safe_redirect( admin_url( 'admin.php?page=cga-game-manager' ) );
			exit;
		}

		// Verify nonce
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'cga_delete_game_' . $game_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'platforms-cloud-games' ) );
		}

		if ( ! current_user_can( 'delete_post', $game_id ) ) {
			wp_die( esc_html__( 'You do not have permission to delete games.', 'platforms-cloud-games' ) );
		}

		wp_delete_post( $game_id, true );

		wp_safe_redirect( admin_url( 'admin.php?page=cga-game-manager' ) );
		exit;
	}
}
