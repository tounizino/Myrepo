<?php
/**
 * Admin settings page template for Cloud Gaming Latest Posts Grid.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap cglp-admin-page">
    <h1><?php esc_html_e('Cloud Gaming Latest Posts', CGLP_TEXTDOMAIN); ?></h1>

    <form action="options.php" method="post" class="cglp-settings-form">
        <?php
        settings_fields('cglp_settings_group');
        do_settings_sections('cloud-gaming-posts');
        submit_button();
        ?>
    </form>

    <div class="cglp-usage">
        <h2><?php esc_html_e('Usage', CGLP_TEXTDOMAIN); ?></h2>
        <p><?php esc_html_e('Add the shortcode [cloud_gaming_posts] to any page, post, or widget area to render the latest posts grid.', CGLP_TEXTDOMAIN); ?></p>
        <p><?php esc_html_e('Example:', CGLP_TEXTDOMAIN); ?> <code>[cloud_gaming_posts posts_per_page="9"]</code></p>
        <ul>
            <li><?php esc_html_e('Dark Theme: Toggle in the settings to flip the grid into a sleek dark appearance.', CGLP_TEXTDOMAIN); ?></li>
            <li><?php esc_html_e('Container Max Width: Control how wide the grid can grow.', CGLP_TEXTDOMAIN); ?></li>
            <li><?php esc_html_e('Container Margin & Padding: Adjust spacing around the whole section to fit your layout.', CGLP_TEXTDOMAIN); ?></li>
        </ul>
    </div>
</div>
