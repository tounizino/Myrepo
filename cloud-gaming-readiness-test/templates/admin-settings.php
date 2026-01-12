<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$action_url = admin_url( 'admin-post.php?action=cgrt_save_settings' );
?>

<div class="wrap cgrt-admin">
	<h1><?php echo esc_html__( 'Settings', 'cloud-gaming-readiness-test' ); ?></h1>
	<hr class="wp-header-end" />

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php echo esc_html__( 'Settings saved.', 'cloud-gaming-readiness-test' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( $action_url ); ?>">
		<?php wp_nonce_field( 'cgrt_save_settings' ); ?>

		<h2 class="cgrt-admin__section-title"><?php echo esc_html__( 'Test behavior', 'cloud-gaming-readiness-test' ); ?></h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="test_duration_seconds"><?php echo esc_html__( 'Test duration (seconds)', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="test_duration_seconds" id="test_duration_seconds" value="<?php echo esc_attr( (int) $settings['test_duration_seconds'] ); ?>" min="10" max="120" />
						<p class="description"><?php echo esc_html__( 'How long the test runs. Longer tests improve accuracy but require more patience from users.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="sample_interval_ms"><?php echo esc_html__( 'Sample interval (ms)', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="sample_interval_ms" id="sample_interval_ms" value="<?php echo esc_attr( (int) $settings['sample_interval_ms'] ); ?>" min="100" max="2000" step="50" />
						<p class="description"><?php echo esc_html__( 'Delay between test requests. Lower = more samples, more accurate, more network usage.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="endpoint_pick_pings"><?php echo esc_html__( 'Endpoint selection pings', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="endpoint_pick_pings" id="endpoint_pick_pings" value="<?php echo esc_attr( (int) $settings['endpoint_pick_pings'] ); ?>" min="3" max="20" />
						<p class="description"><?php echo esc_html__( 'Number of pings sent to each candidate endpoint before choosing the fastest. Avoids skewed results from random spikes.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="spike_ms"><?php echo esc_html__( 'Spike threshold (ms)', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="spike_ms" id="spike_ms" value="<?php echo esc_attr( (int) $settings['spike_ms'] ); ?>" min="10" max="200" />
						<p class="description"><?php echo esc_html__( 'Deviation (above median) considered a "latency spike" for cloud gaming analysis. Used in stability scoring.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<h2 class="cgrt-admin__section-title"><?php echo esc_html__( 'Default weighting logic', 'cloud-gaming-readiness-test' ); ?></h2>
		<p class="description">
			<?php echo esc_html__( 'Control how each metric contributes to the unified readiness score. Values will be normalized to sum to 1. You can override these per-platform.', 'cloud-gaming-readiness-test' ); ?>
		</p>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="weight_latency"><?php echo esc_html__( 'Latency', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="weight_latency" id="weight_latency" value="<?php echo esc_attr( (float) $settings['default_weights']['latency'] ); ?>" step="0.01" min="0" max="1" />
						<p class="description"><?php echo esc_html__( 'Average latency (ping).', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="weight_jitter"><?php echo esc_html__( 'Jitter', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="weight_jitter" id="weight_jitter" value="<?php echo esc_attr( (float) $settings['default_weights']['jitter'] ); ?>" step="0.01" min="0" max="1" />
						<p class="description"><?php echo esc_html__( 'Latency variation. High jitter causes "stuttery" feel.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="weight_loss"><?php echo esc_html__( 'Packet loss', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="weight_loss" id="weight_loss" value="<?php echo esc_attr( (float) $settings['default_weights']['loss'] ); ?>" step="0.01" min="0" max="1" />
						<p class="description"><?php echo esc_html__( 'Failed/timed-out requests.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="weight_stability"><?php echo esc_html__( 'Stability', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="weight_stability" id="weight_stability" value="<?php echo esc_attr( (float) $settings['default_weights']['stability'] ); ?>" step="0.01" min="0" max="1" />
						<p class="description"><?php echo esc_html__( 'Spike frequency and volatility over time.', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<h2 class="cgrt-admin__section-title"><?php echo esc_html__( 'Readiness tiers', 'cloud-gaming-readiness-test' ); ?></h2>
		<p class="description">
			<?php echo esc_html__( 'Score thresholds for tier labels. Scores are from 0 to 100.', 'cloud-gaming-readiness-test' ); ?>
		</p>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="tier_excellent"><?php echo esc_html__( 'Excellent', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="tier_excellent" id="tier_excellent" value="<?php echo esc_attr( (int) $settings['tiers']['excellent'] ); ?>" min="0" max="100" />
						<p class="description"><?php echo esc_html__( 'Minimum score for "Excellent" tier (default: 85).', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="tier_good"><?php echo esc_html__( 'Good', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="tier_good" id="tier_good" value="<?php echo esc_attr( (int) $settings['tiers']['good'] ); ?>" min="0" max="100" />
						<p class="description"><?php echo esc_html__( 'Minimum score for "Good" tier (default: 70).', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="tier_fair"><?php echo esc_html__( 'Fair', 'cloud-gaming-readiness-test' ); ?></label></th>
					<td>
						<input type="number" name="tier_fair" id="tier_fair" value="<?php echo esc_attr( (int) $settings['tiers']['fair'] ); ?>" min="0" max="100" />
						<p class="description"><?php echo esc_html__( 'Minimum score for "Fair" tier (default: 55).', 'cloud-gaming-readiness-test' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>

		<h2 class="cgrt-admin__section-title"><?php echo esc_html__( 'Data & privacy', 'cloud-gaming-readiness-test' ); ?></h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Store test results', 'cloud-gaming-readiness-test' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="store_results" value="1" <?php checked( ! empty( $settings['store_results'] ), true ); ?> />
							<?php echo esc_html__( 'Save anonymized results for statistics (optional)', 'cloud-gaming-readiness-test' ); ?>
						</label>
						<p class="description">
							<?php echo esc_html__( 'When enabled, test results are stored with anonymized hashes (IP and user-agent). No personally identifiable information (PII) is stored. This allows aggregated statistics in the dashboard.', 'cloud-gaming-readiness-test' ); ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__( 'Save settings', 'cloud-gaming-readiness-test' ); ?>" />
		</p>
	</form>
</div>
