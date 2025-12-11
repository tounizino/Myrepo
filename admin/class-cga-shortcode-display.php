<?php
/**
 * Shortcode Display Box
 *
 * @package PlatformsForCloudGames
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CGA_Shortcode_Display class
 */
class CGA_Shortcode_Display {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_shortcode_box' ) );
	}

	/**
	 * Add shortcode display box
	 */
	public function add_shortcode_box() {
		add_meta_box(
			'cga_shortcode_display',
			__( 'Game Shortcode', 'platforms-cloud-games' ),
			array( $this, 'render_shortcode_box' ),
			'cloud_games',
			'side',
			'high'
		);
	}

	/**
	 * Render shortcode box
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_shortcode_box( $post ) {
		if ( 'publish' !== $post->post_status ) {
			?>
			<p><?php esc_html_e( 'Publish this game to see its shortcode.', 'platforms-cloud-games' ); ?></p>
			<?php
			return;
		}

		$shortcode = '[cloud_gaming_availability game_id="' . $post->ID . '"]';
		$shortcode_with_params = '[cloud_gaming_availability game_id="' . $post->ID . '" theme="light" columns="3" show_description="true"]';

		?>
		<div class="cga-shortcode-display">
			<p><?php esc_html_e( 'Use these shortcodes to display this game on your site:', 'platforms-cloud-games' ); ?></p>

			<h4><?php esc_html_e( 'Basic Shortcode:', 'platforms-cloud-games' ); ?></h4>
			<div class="cga-shortcode-wrapper">
				<code class="cga-shortcode-code"><?php echo esc_html( $shortcode ); ?></code>
				<button type="button" class="button button-small cga-copy-shortcode-btn" data-shortcode="<?php echo esc_attr( $shortcode ); ?>" title="<?php esc_attr_e( 'Copy to clipboard', 'platforms-cloud-games' ); ?>">
					<?php esc_html_e( 'Copy', 'platforms-cloud-games' ); ?>
				</button>
			</div>

			<h4><?php esc_html_e( 'With Custom Parameters:', 'platforms-cloud-games' ); ?></h4>
			<div class="cga-shortcode-wrapper">
				<code class="cga-shortcode-code"><?php echo esc_html( $shortcode_with_params ); ?></code>
				<button type="button" class="button button-small cga-copy-shortcode-btn" data-shortcode="<?php echo esc_attr( $shortcode_with_params ); ?>" title="<?php esc_attr_e( 'Copy to clipboard', 'platforms-cloud-games' ); ?>">
					<?php esc_html_e( 'Copy', 'platforms-cloud-games' ); ?>
				</button>
			</div>

			<hr>

			<h4><?php esc_html_e( 'Parameters:', 'platforms-cloud-games' ); ?></h4>
			<ul style="margin: 10px 0; padding-left: 20px;">
				<li><code>theme</code>: light, dark, or auto</li>
				<li><code>columns</code>: 2, 3, or 4</li>
				<li><code>show_description</code>: true or false</li>
			</ul>

			<style>
				.cga-shortcode-display {
					padding: 10px;
				}

				.cga-shortcode-wrapper {
					background: #f5f5f5;
					padding: 10px;
					border: 1px solid #ddd;
					border-radius: 4px;
					margin-bottom: 15px;
					display: flex;
					align-items: center;
					gap: 8px;
				}

				.cga-shortcode-code {
					flex: 1;
					word-break: break-all;
					background: white;
					padding: 8px;
					border-radius: 3px;
					border: 1px solid #ddd;
					display: block;
					font-family: 'Courier New', monospace;
					font-size: 12px;
				}

				.cga-copy-shortcode-btn {
					white-space: nowrap;
					flex-shrink: 0;
				}

				.cga-shortcode-display h4 {
					margin: 15px 0 10px 0;
					font-size: 13px;
				}

				.cga-shortcode-display p {
					margin: 0 0 15px 0;
				}

				.cga-shortcode-display ul {
					font-size: 13px;
				}

				.cga-shortcode-display code {
					background: #f5f5f5;
					padding: 2px 4px;
					border-radius: 3px;
				}
			</style>

			<script>
			jQuery(document).ready(function($) {
				$('.cga-copy-shortcode-btn').on('click', function(e) {
					e.preventDefault();
					var shortcode = $(this).data('shortcode');
					var $temp = $('<textarea>');
					$('body').append($temp);
					$temp.val(shortcode).select();
					document.execCommand('copy');
					$temp.remove();

					var originalText = $(this).text();
					$(this).text('<?php esc_js_e( 'Copied!', 'platforms-cloud-games' ); ?>');
					var $btn = $(this);
					setTimeout(function() {
						$btn.text(originalText);
					}, 2000);
				});
			});
			</script>
		</div>
		<?php
	}
}
