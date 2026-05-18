<?php

namespace CloudGamersDiscuss\Admin;

class Settings {
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'addMenu' ] );
		add_action( 'admin_init', [ $this, 'registerSettings' ] );
	}

	public function addMenu() {
		add_menu_page(
			'Cloud Gamers Discuss',
			'CG Discuss',
			'manage_options',
			'cgd-settings',
			[ $this, 'renderPage' ],
			'dashicons-format-chat',
			30
		);
	}

	public function registerSettings() {
		register_setting( 'cgd_settings_group', 'cgd_mailchimp_api_key' );
		register_setting( 'cgd_settings_group', 'cgd_mailchimp_list_id' );
		register_setting( 'cgd_settings_group', 'cgd_primary_color' );
		register_setting( 'cgd_settings_group', 'cgd_accent_color' );
	}

	public function renderPage() {
		?>
		<div class="wrap cgd-admin-wrap">
			<h1>Cloud Gamers Discuss - Admin Dashboard</h1>
			
			<div class="cgd-admin-grid">
				<div class="cgd-admin-card glass">
					<h2>Mailchimp Integration</h2>
					<form method="post" action="options.php">
						<?php settings_fields( 'cgd_settings_group' ); ?>
						<table class="form-table">
							<tr>
								<th>API Key</th>
								<td><input type="text" name="cgd_mailchimp_api_key" value="<?php echo esc_attr( get_option('cgd_mailchimp_api_key') ); ?>" class="regular-text"></td>
							</tr>
							<tr>
								<th>List ID</th>
								<td><input type="text" name="cgd_mailchimp_list_id" value="<?php echo esc_attr( get_option('cgd_mailchimp_list_id') ); ?>" class="regular-text"></td>
							</tr>
						</table>
						<?php submit_button(); ?>
					</form>
				</div>

				<div class="cgd-admin-card glass">
					<h2>Visual Customization</h2>
					<form method="post" action="options.php">
						<?php settings_fields( 'cgd_settings_group' ); ?>
						<table class="form-table">
							<tr>
								<th>Primary Color</th>
								<td><input type="color" name="cgd_primary_color" value="<?php echo esc_attr( get_option('cgd_primary_color', '#00f2ff') ); ?>"></td>
							</tr>
							<tr>
								<th>Accent Color</th>
								<td><input type="color" name="cgd_accent_color" value="<?php echo esc_attr( get_option('cgd_accent_color', '#7000ff') ); ?>"></td>
							</tr>
						</table>
						<?php submit_button(); ?>
					</form>
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
			.cgd-admin-grid {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
				gap: 20px;
				margin-top: 20px;
			}
			.cgd-admin-card.glass {
				background: rgba(255, 255, 255, 0.05);
				backdrop-filter: blur(10px);
				border: 1px solid rgba(255, 255, 255, 0.1);
				padding: 20px;
				border-radius: 12px;
			}
			.cgd-admin-card h2 {
				color: #00f2ff;
				margin-top: 0;
			}
			.form-table th {
				color: #fff;
			}
			.wp-core-ui .button-primary {
				background: #7000ff;
				border-color: #7000ff;
			}
		</style>
		<?php
	}
}
