<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$action_url = admin_url( 'admin-post.php?action=cgrt_save_endpoint' );
$back_url   = admin_url( 'admin.php?page=cgrt-platforms' );

$id          = isset( $endpoint['id'] ) ? (int) $endpoint['id'] : 0;
$platform_id = isset( $endpoint['platform_id'] ) ? (int) $endpoint['platform_id'] : 0;
$region      = isset( $endpoint['region'] ) ? $endpoint['region'] : '';
$url         = isset( $endpoint['endpoint_url'] ) ? $endpoint['endpoint_url'] : '';
$method      = isset( $endpoint['method'] ) ? $endpoint['method'] : 'HEAD';
$enabled     = isset( $endpoint['enabled'] ) ? (int) $endpoint['enabled'] : 1;
$timeout_ms  = isset( $endpoint['timeout_ms'] ) ? (int) $endpoint['timeout_ms'] : 2500;
$notes       = isset( $endpoint['notes'] ) ? $endpoint['notes'] : '';

$platform = CGRT_Database::get_platform( $platform_id );
?>

<div class="wrap cgrt-admin">
	<h1><?php echo $id > 0 ? esc_html__( 'Edit Endpoint', 'cloud-gaming-readiness-test' ) : esc_html__( 'New Endpoint', 'cloud-gaming-readiness-test' ); ?></h1>
	<a href="<?php echo esc_url( $back_url ); ?>" class="page-title-action"><?php echo esc_html__( '← Back to platforms', 'cloud-gaming-readiness-test' ); ?></a>
	<hr class="wp-header-end" />

	<?php if ( $platform ) : ?>
		<div class="notice notice-info">
			<p>
				<?php echo esc_html__( 'Platform:', 'cloud-gaming-readiness-test' ); ?>
				<strong><?php echo esc_html( $platform['name'] ); ?></strong>
			</p>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( $action_url ); ?>">
		<?php wp_nonce_field( 'cgrt_save_endpoint' ); ?>
		<input type="hidden" name="id" value="<?php echo esc_attr( (int) $id ); ?>" />
		<input type="hidden" name="platform_id" value="<?php echo esc_attr( (int) $platform_id ); ?>" />

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="region"><?php echo esc_html__( 'Region label', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="text" name="region" id="region" class="regular-text" value="<?php echo esc_attr( $region ); ?>" placeholder="e.g. US-East, EU-West" required />
						<p class="description"><?php echo esc_html__( 'Shown to users. Use a short, recognizable region name.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="endpoint_url"><?php echo esc_html__( 'Endpoint URL', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="url" name="endpoint_url" id="endpoint_url" class="large-text" value="<?php echo esc_attr( $url ); ?>" placeholder="https://edge.example.com/ping" required />
						<p class="description">
							<?php echo esc_html__( 'Must respond quickly and allow cross-origin requests (CORS). Recommended response: 204 or small JSON.', 'cloud-gaming-readiness-test' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="method"><?php echo esc_html__( 'Protocol / method', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<select name="method" id="method">
							<option value="HEAD" <?php selected( $method, 'HEAD' ); ?>>HEAD</option>
							<option value="GET" <?php selected( $method, 'GET' ); ?>>GET</option>
						</select>
						<p class="description"><?php echo esc_html__( 'HEAD is ideal for minimal payload. Use GET if your endpoint does not support HEAD.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="timeout_ms"><?php echo esc_html__( 'Timeout (ms)', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="timeout_ms" id="timeout_ms" value="<?php echo esc_attr( $timeout_ms ); ?>" min="500" max="10000" step="50" />
						<p class="description"><?php echo esc_html__( 'Request timeout for each sample. Lower values fail fast but may flag slow networks as loss.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php echo esc_html__( 'Enabled', 'cloud-gaming-readiness-test' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="enabled" value="1" <?php checked( $enabled, 1 ); ?> />
							<?php echo esc_html__( 'Use this endpoint in front-end tests', 'cloud-gaming-readiness-test' ); ?>
						</label>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="notes"><?php echo esc_html__( 'Notes (optional)', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<textarea name="notes" id="notes" class="large-text" rows="3"><?php echo esc_textarea( $notes ); ?></textarea>
						<p class="description"><?php echo esc_html__( 'Internal notes for admins (not shown to users).', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__( 'Save Endpoint', 'cloud-gaming-readiness-test' ); ?>" />
		</p>
	</form>

	<div class="cgrt-admin__panel">
		<h2><?php echo esc_html__( 'Endpoint requirements (cloud gaming accuracy)', 'cloud-gaming-readiness-test' ); ?></h2>
		<ul class="cgrt-admin__bullets">
			<li><?php echo esc_html__( 'Must be reachable over HTTPS from browsers.', 'cloud-gaming-readiness-test' ); ?></li>
			<li><?php echo esc_html__( 'Should return a fast response (204 or minimal JSON).', 'cloud-gaming-readiness-test' ); ?></li>
			<li><?php echo esc_html__( 'Must allow CORS: Access-Control-Allow-Origin: * (or your site domain).', 'cloud-gaming-readiness-test' ); ?></li>
			<li><?php echo esc_html__( 'Should be hosted close to (or within) your cloud gaming infrastructure edge region.', 'cloud-gaming-readiness-test' ); ?></li>
		</ul>
	</div>
</div>
