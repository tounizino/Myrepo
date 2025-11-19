<?php
/**
 * Single post card template for Cloud Gaming Latest Posts Grid.
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();
$post_link = get_permalink($post_id);
$post_title = get_the_title($post_id);
$post_excerpt = get_the_excerpt($post_id);
$post_date = get_the_date('M d, Y', $post_id);
$categories = get_the_category($post_id);
$category_name = !empty($categories) ? esc_html($categories[0]->name) : __('Uncategorized', CGLP_TEXTDOMAIN);

$featured_image_url = get_the_post_thumbnail_url($post_id, 'medium_large');
if (!$featured_image_url) {
    $featured_image_url = CGLP_PLUGIN_URL . 'assets/images/placeholder.svg';
}

$badge = Cloud_Gaming_Latest_Posts::get_post_badge($post_id);
$read_time = Cloud_Gaming_Latest_Posts::calculate_read_time($post_id);
$excerpt_fallback = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $post_id)), 24, '…');
$post_excerpt = $post_excerpt ? wp_trim_words(wp_strip_all_tags($post_excerpt), 24, '…') : $excerpt_fallback;
?>

<article class="latest-card">
    <div class="card-image-wrap">
        <img src="<?php echo esc_url($featured_image_url); ?>" alt="<?php echo esc_attr($post_title); ?>">
        <?php if ($badge) : ?>
            <span class="card-badge <?php echo esc_attr($badge['type']); ?>"><?php echo esc_html($badge['label']); ?></span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <span class="card-category"><?php echo esc_html($category_name); ?></span>
        <h3 class="card-title">
            <a href="<?php echo esc_url($post_link); ?>"><?php echo esc_html($post_title); ?></a>
        </h3>
        <p class="card-excerpt"><?php echo esc_html($post_excerpt); ?></p>
        <div class="card-meta">
            <span class="date"><?php echo esc_html($post_date); ?></span>
            <span class="separator">·</span>
            <span class="read-time"><?php echo esc_html(sprintf(__('%s min read', CGLP_TEXTDOMAIN), $read_time)); ?></span>
        </div>
    </div>
</article>
