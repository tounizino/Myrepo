<?php

namespace CloudGamersDiscuss\Admin;

class Dashboard {
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'addDashboardMenu' ] );
	}

	public function addDashboardMenu() {
		add_submenu_page(
			'cgd-settings',
			'Analytics',
			'Analytics',
			'manage_options',
			'cgd-analytics',
			[ $this, 'renderAnalytics' ]
		);
	}

	public function renderAnalytics() {
		global $wpdb;
		$reactions_table = $wpdb->prefix . 'cgd_reactions';
		$ratings_table   = $wpdb->prefix . 'cgd_ratings';

		$total_comments  = wp_count_comments()->approved;
		$total_reactions = $wpdb->get_var( "SELECT COUNT(*) FROM $reactions_table" );
		$total_ratings   = $wpdb->get_var( "SELECT COUNT(*) FROM $ratings_table" );

		?>
		<div class="wrap cgd-admin-wrap">
			<h1>Cloud Gamers Discuss - Engagement Analytics</h1>
			
			<div class="cgd-stats-grid">
				<div class="cgd-stat-card glass">
					<span class="label">Total Comments</span>
					<span class="value"><?php echo number_format( $total_comments ); ?></span>
				</div>
				<div class="cgd-stat-card glass">
					<span class="label">Total Reactions</span>
					<span class="value"><?php echo number_format( $total_reactions ); ?></span>
				</div>
				<div class="cgd-stat-card glass">
					<span class="label">Total Ratings</span>
					<span class="value"><?php echo number_format( $total_ratings ); ?></span>
				</div>
			</div>

			<div class="cgd-admin-grid">
				<div class="cgd-admin-card glass">
					<h2>Recent Reactions</h2>
					<?php
					$recent_reactions = $wpdb->get_results( "SELECT * FROM $reactions_table ORDER BY created_at DESC LIMIT 10" );
					if ( $recent_reactions ) {
						echo '<ul class="cgd-admin-list">';
						foreach ( $recent_reactions as $reaction ) {
							echo "<li>{$reaction->type} on Post #{$reaction->post_id} at {$reaction->created_at}</li>";
						}
						echo '</ul>';
					} else {
						echo '<p>No reactions yet.</p>';
					}
					?>
				</div>
			</div>
		</div>

		<style>
			.cgd-admin-wrap {
				padding: 20px;
				background: #0a0b1e;
				color: #fff;
				min-height: 100vh;
			}
			.glass {
				background: rgba(255, 255, 255, 0.05);
				backdrop-filter: blur(10px);
				border: 1px solid rgba(255, 255, 255, 0.1);
				padding: 20px;
				border-radius: 12px;
			}
			.cgd-stats-grid {
				display: grid;
				grid-template-columns: repeat(3, 1fr);
				gap: 20px;
				margin-bottom: 30px;
			}
			.cgd-stat-card {
				text-align: center;
			}
			.cgd-stat-card .label {
				display: block;
				font-size: 0.9rem;
				opacity: 0.7;
				margin-bottom: 10px;
			}
			.cgd-stat-card .value {
				font-size: 2.5rem;
				font-weight: 700;
				color: #00f2ff;
			}
			.cgd-admin-list {
				list-style: none;
				padding: 0;
			}
			.cgd-admin-list li {
				padding: 10px 0;
				border-bottom: 1px solid rgba(255, 255, 255, 0.1);
			}
		</style>
		<?php
	}
}
