<?php
/**
 * Posts grid template for Cloud Gaming Latest Posts Grid.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!isset($settings) || !is_array($settings)) {
    $settings = Cloud_Gaming_Latest_Posts::get_default_settings();
}

$theme_class = !empty($settings['dark_mode']) ? 'dark-theme' : 'light-theme';
$grid_id = $container_id . '-grid';
$pagination_id = $container_id . '-pagination';

$style_parts = [
    '--cglp-container-max-width:' . esc_attr($settings['container_max_width']),
    '--cglp-container-margin:' . esc_attr($settings['container_margin']),
    '--cglp-container-padding:' . esc_attr($settings['container_padding']),
];

$trimmed_padding = trim((string) $settings['container_padding']);
if ($trimmed_padding !== '' && $trimmed_padding !== '0') {
    $style_parts[] = '--cglp-container-padding-mobile:' . esc_attr($settings['container_padding']);
}

$style_attr = implode(';', $style_parts);
if (!empty($style_attr)) {
    $style_attr .= ';';
}
?>

<div class="gaming-latest-container v27 cglp-latest-posts <?php echo esc_attr($theme_class); ?>"
     id="<?php echo esc_attr($container_id); ?>"
     data-container-id="<?php echo esc_attr($container_id); ?>"
     data-current-page="1"
     data-total-pages="<?php echo esc_attr($total_pages); ?>"
     data-posts-per-page="<?php echo esc_attr($posts_per_page); ?>"
     style="<?php echo esc_attr($style_attr); ?>">
    <h2 class="section-title">
        <span class="title-square blue"></span> <?php esc_html_e('Latest Posts', CGLP_TEXTDOMAIN); ?>
    </h2>

    <div class="latest-grid" id="<?php echo esc_attr($grid_id); ?>">
        <?php
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                include CGLP_PLUGIN_DIR . 'includes/post-card.php';
            }
        } else {
            echo '<p class="latest-empty-message">' . esc_html__('No posts found.', CGLP_TEXTDOMAIN) . '</p>';
        }
        ?>
    </div>

    <div class="latest-pagination" id="<?php echo esc_attr($pagination_id); ?>">
        <?php if ($total_pages > 1) : ?>
            <button type="button" class="prev" <?php disabled(true); ?>><?php esc_html_e('← Previous', CGLP_TEXTDOMAIN); ?></button>
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <button type="button" data-page="<?php echo esc_attr($i); ?>"<?php echo $i === 1 ? ' class="active"' : ''; ?>><?php echo esc_html($i); ?></button>
            <?php endfor; ?>
            <button type="button" class="next"<?php echo $total_pages <= 1 ? ' disabled' : ''; ?>><?php esc_html_e('Next →', CGLP_TEXTDOMAIN); ?></button>
        <?php endif; ?>
    </div>
</div>
