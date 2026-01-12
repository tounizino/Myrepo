<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$add_url = admin_url( 'admin.php?page=cgrt-platforms&action=new_platform' );
?>

<div class="wrap cgrt-admin">
	<h1 class="wp-heading-inline"><?php echo esc_html__( 'Platforms & Endpoints', 'cloud-gaming-readiness-test' ); ?></h1>
	<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action"><?php echo esc_html__( 'Add new platform', 'cloud-gaming-readiness-test' ); ?></a>
	<hr class="wp-header-end" />

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html__( 'Saved.', 'cloud-gaming-readiness-test' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['deleted'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html__( 'Deleted.', 'cloud-gaming-readiness-test' ); ?></p></div>
	<?php endif; ?>

	<div class="cgrt-admin__panel">
		<p class="cgrt-admin__muted">
			<?php echo esc_html__( 'Cloud gaming tests require fast endpoints that allow CORS. Configure one or more endpoints per platform (regions). The front-end test measures real HTTP timing from the visitor’s device to these endpoints.', 'cloud-gaming-readiness-test' ); ?>
		</p>
	</div>

	<?php if ( empty( $platforms ) ) : ?>
		<p><?php echo esc_html__( 'No platforms found.', 'cloud-gaming-readiness-test' ); ?></p>
	<?php else : ?>
		<?php foreach ( $platforms as $p ) : ?>
			<div class="cgrt-admin__platform">
				<div class="cgrt-admin__platform-header">
					<div>
						<h2 class="cgrt-admin__platform-title">
							<?php echo esc_html( $p['name'] ); ?>
							<?php if ( ! $p['enabled'] ) : ?>
								<span class="cgrt-admin__pill cgrt-admin__pill--muted"><?php echo esc_html__( 'Disabled', 'cloud-gaming-readiness-test' ); ?></span>
							<?php else : ?>
								<span class="cgrt-admin__pill cgrt-admin__pill--ok"><?php echo esc_html__( 'Enabled', 'cloud-gaming-readiness-test' ); ?></span>
							<?php endif; ?>
						</h2>
						<div class="cgrt-admin__muted">
							<?php echo esc_html__( 'Slug:', 'cloud-gaming-readiness-test' ); ?>
							<code><?php echo esc_html( $p['slug'] ); ?></code>
						</div>
					</div>
					<div class="cgrt-admin__platform-actions">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=cgrt-platforms&action=edit_platform&id=' . (int) $p['id'] ) ); ?>" class="button">
							<?php echo esc_html__( 'Edit platform', 'cloud-gaming-readiness-test' ); ?>
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=cgrt-platforms&action=new_endpoint&platform_id=' . (int) $p['id'] ) ); ?>" class="button button-primary">
							<?php echo esc_html__( 'Add endpoint', 'cloud-gaming-readiness-test' ); ?>
						</a>
						<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=cgrt_delete_platform&id=' . (int) $p['id'] ), 'cgrt_delete_platform' ) ); ?>" class="button button-link-delete" onclick="return confirm('<?php echo esc_js( __( 'Delete platform and all endpoints?', 'cloud-gaming-readiness-test' ) ); ?>');">
							<?php echo esc_html__( 'Delete', 'cloud-gaming-readiness-test' ); ?>
						</a>
					</div>
				</div>

				<?php if ( empty( $p['endpoints'] ) ) : ?>
					<div class="cgrt-admin__muted"><?php echo esc_html__( 'No endpoints configured yet.', 'cloud-gaming-readiness-test' ); ?></div>
				<?php else : ?>
					<table class="widefat striped cgrt-admin__table">
						<thead>
							<tr>
								<th><?php echo esc_html__( 'Enabled', 'cloud-gaming-readiness-test' ); ?></th>
								<th><?php echo esc_html__( 'Region', 'cloud-gaming-readiness-test' ); ?></th>
								<th><?php echo esc_html__( 'Endpoint URL', 'cloud-gaming-readiness-test' ); ?></th>
								<th><?php echo esc_html__( 'Method', 'cloud-gaming-readiness-test' ); ?></th>
								<th><?php echo esc_html__( 'Timeout', 'cloud-gaming-readiness-test' ); ?></th>
								<th><?php echo esc_html__( 'Actions', 'cloud-gaming-readiness-test' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $p['endpoints'] as $e ) : ?>
								<tr>
									<td>
										<?php if ( $e['enabled'] ) : ?>
											<span class="cgrt-admin__pill cgrt-admin__pill--ok"><?php echo esc_html__( 'Yes', 'cloud-gaming-readiness-test' ); ?></span>
										<?php else : ?>
											<span class="cgrt-admin__pill cgrt-admin__pill--muted"><?php echo esc_html__( 'No', 'cloud-gaming-readiness-test' ); ?></span>
										<?php endif; ?>
									</td>
									<td><?php echo esc_html( $e['region'] ); ?></td>
									<td><code><?php echo esc_html( $e['endpoint_url'] ); ?></code></td>
									<td><code><?php echo esc_html( $e['method'] ); ?></code></td>
									<td><?php echo esc_html( (int) $e['timeout_ms'] ); ?> ms</td>
									<td>
										<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=cgrt-platforms&action=edit_endpoint&id=' . (int) $e['id'] ) ); ?>">
											<?php echo esc_html__( 'Edit', 'cloud-gaming-readiness-test' ); ?>
										</a>
										<a class="button button-link-delete" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=cgrt_delete_endpoint&id=' . (int) $e['id'] ), 'cgrt_delete_endpoint' ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Delete endpoint?', 'cloud-gaming-readiness-test' ) ); ?>');">
											<?php echo esc_html__( 'Delete', 'cloud-gaming-readiness-test' ); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
