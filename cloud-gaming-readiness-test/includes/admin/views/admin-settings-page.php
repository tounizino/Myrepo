<?php
/**
 * Admin settings page template.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get current settings.
$settings = array(
    'home_url'           => get_option( 'cgrt_home_url', home_url() ),
    'show_shortcode'     => get_option( 'cgrt_show_shortcode', true ),
    'enable_page_mode'   => get_option( 'cgrt_enable_page_mode', true ),
    'theme_color'        => get_option( 'cgrt_theme_color', '#6366f1' ),
    'default_mode'       => get_option( 'cgrt_default_mode', 'dark' ),
    'custom_css'         => get_option( 'cgrt_custom_css', '' ),
    'cloudflare_enabled' => get_option( 'cgrt_cloudflare_enabled', false ),
);

?>

<div class="wrap cgrt-admin-wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <?php settings_errors( 'cgrt_settings' ); ?>

    <div class="cgrt-admin-container">
        <div class="cgrt-main-content">
            <form method="post" action="options.php">
                <?php
                settings_fields( 'cgrt_settings' );
                do_settings_sections( 'cgrt_settings' );
                submit_button( __( 'Save Settings', 'cloud-gaming-readiness-test' ) );
                ?>
            </form>
        </div>

        <div class="cgrt-sidebar">
            <div class="cgrt-sidebar-card">
                <h3><?php esc_html_e( 'Shortcode', 'cloud-gaming-readiness-test' ); ?></h3>
                <p><?php esc_html_e( 'Use this shortcode to display the test anywhere on your site:', 'cloud-gaming-readiness-test' ); ?></p>
                <code class="cgrt-shortcode">[cloud_gaming_test]</code>

                <h4><?php esc_html_e( 'Full Page Mode', 'cloud-gaming-readiness-test' ); ?></h4>
                <p><?php esc_html_e( 'To use the full-page template:', 'cloud-gaming-readiness-test' ); ?></p>
                <ol>
                    <li><?php esc_html_e( 'Create a new page', 'cloud-gaming-readiness-test' ); ?></li>
                    <li><?php esc_html_e( 'Select "Cloud Gaming Test Page" from the Page Attributes template', 'cloud-gaming-readiness-test' ); ?></li>
                    <li><?php esc_html_e( 'Publish the page', 'cloud-gaming-readiness-test' ); ?></li>
                </ol>
            </div>

            <div class="cgrt-sidebar-card">
                <h3><?php esc_html_e( 'Support', 'cloud-gaming-readiness-test' ); ?></h3>
                <p><?php esc_html_e( 'Need help? Check our documentation or contact support.', 'cloud-gaming-readiness-test' ); ?></p>
                <a href="#" class="button button-secondary"><?php esc_html_e( 'Documentation', 'cloud-gaming-readiness-test' ); ?></a>
            </div>
        </div>
    </div>
</div>
