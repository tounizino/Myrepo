<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$action_url = admin_url( 'admin-post.php?action=cgrt_save_platform' );
$back_url   = admin_url( 'admin.php?page=cgrt-platforms' );

$id      = isset( $platform['id'] ) ? (int) $platform['id'] : 0;
$name    = isset( $platform['name'] ) ? $platform['name'] : '';
$slug    = isset( $platform['slug'] ) ? $platform['slug'] : '';
$enabled = isset( $platform['enabled'] ) ? (int) $platform['enabled'] : 1;

$settings = CGRT_Database::get_settings();
$defaults = $settings['default_weights'];

$weights = array();
if ( ! empty( $platform['weights_json'] ) ) {
	$decoded = json_decode( $platform['weights_json'], true );
	if ( is_array( $decoded ) ) {
		$weights = $decoded;
	}
}

$latency_w   = isset( $weights['latency'] ) ? (float) $weights['latency'] : null;
$jitter_w    = isset( $weights['jitter'] ) ? (float) $weights['jitter'] : null;
$loss_w      = isset( $weights['loss'] ) ? (float) $weights['loss'] : null;
$stability_w = isset( $weights['stability'] ) ? (float) $weights['stability'] : null;

?>

<div class="wrap cgrt-admin">
	<h1><?php echo $id > 0 ? esc_html__( 'Edit Platform', 'cloud-gaming-readiness-test' ) : esc_html__( 'New Platform', 'cloud-gaming-readiness-test' ); ?></h1>
	<a href="<?php echo esc_url( $back_url ); ?>" class="page-title-action"><?php echo esc_html__( '← Back to platforms', 'cloud-gaming-readiness-test' ); ?></a>
	<hr class="wp-header-end" />

	<form method="post" action="<?php echo esc_url( $action_url ); ?>">
		<?php wp_nonce_field( 'cgrt_save_platform' ); ?>
		<input type="hidden" name="id" value="<?php echo esc_attr( (int) $id ); ?>" />

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<label for="name"><?php echo esc_html__( 'Platform name', 'cloud-gaming-readiness-test' ); ?></label>
					</th>
					<td>
						<input type="text" name="name" id="name" value="<?php echo esc_attr( $name ); ?>" class="regular-text" required />
						<p class="description"><?php echo esc_html__( 'Friendly name visible to site visitors.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="slug"><?php echo esc_html__( 'Slug', 'cloud-gaming-readiness-test' ); ?></label>
					</th>
					<td>
						<input type="text" name="slug" id="slug" value="<?php echo esc_attr( $slug ); ?>" class="regular-text" required pattern="[a-z0-9\-]+" />
						<p class="description"><?php echo esc_html__( 'Lowercase, alphanumeric slug. Used in shortcode: [cgrt_test platform="slug"]', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php echo esc_html__( 'Enabled', 'cloud-gaming-readiness-test' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="enabled" value="1" <?php checked( $enabled, 1 ); ?> />
							<?php echo esc_html__( 'Make this platform available in front-end tests', 'cloud-gaming-readiness-test' ); ?>
						</label>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php echo esc_html__( 'Custom weights (optional)', 'cloud-gaming-readiness-test' ); ?></th>
					<td>
						<p class="description"><?php echo esc_html__( 'Leave blank to use global defaults. When set, weights will be auto-normalized.', 'cloud-gaming-readiness-test' ); ?></p>
						<table class="widefat striped cgrt-admin__table-sm">
							<thead>
								<tr>
									<th><?php echo esc_html__( 'Metric', 'cloud-gaming-readiness-test' ); ?></th>
									<th><?php echo esc_html__( 'Weight', 'cloud-gaming-readiness-test' ); ?></th>
									<th><?php echo esc_html__( 'Global default', 'cloud-gaming-readiness-test' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><?php echo esc_html__( 'Latency', 'cloud-gaming-readiness-test' ); ?></td>
									<td><input type="number" name="weight_latency" step="0.01" min="0" max="1" value="<?php echo null !== $latency_w ? esc_attr( $latency_w ) : ''; ?>" /></td>
									<td><?php echo esc_html( $defaults['latency'] ); ?></td>
								</tr>
								<tr>
									<td><?php echo esc_html__( 'Jitter', 'cloud-gaming-readiness-test' ); ?></td>
									<td><input type="number" name="weight_jitter" step="0.01" min="0" max="1" value="<?php echo null !== $jitter_w ? esc_attr( $jitter_w ) : ''; ?>" /></td>
									<td><?php echo esc_html( $defaults['jitter'] ); ?></td>
								</tr>
								<tr>
									<td><?php echo esc_html__( 'Loss', 'cloud-gaming-readiness-test' ); ?></td>
									<td><input type="number" name="weight_loss" step="0.01" min="0" max="1" value="<?php echo null !== $loss_w ? esc_attr( $loss_w ) : ''; ?>" /></td>
									<td><?php echo esc_html( $defaults['loss'] ); ?></td>
								</tr>
								<tr>
									<td><?php echo esc_html__( 'Stability', 'cloud-gaming-readiness-test' ); ?></td>
									<td><input type="number" name="weight_stability" step="0.01" min="0" max="1" value="<?php echo null !== $stability_w ? esc_attr( $stability_w ) : ''; ?>" /></td>
									<td><?php echo esc_html( $defaults['stability'] ); ?></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__( 'Save Platform', 'cloud-gaming-readiness-test' ); ?>" />
		</p>
	</form>
</div>
