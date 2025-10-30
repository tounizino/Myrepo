<?php
/**
 * Block Renderer Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UBCG_Block_Renderer {
    
    public static function render_latest_posts_grid($attributes) {
        $defaults = array(
            'postsPerPage' => 9,
            'columns' => 3,
            'showImage' => true,
            'showDate' => true,
            'showExcerpt' => true,
            'showAuthor' => false,
            'showCategory' => true,
            'orderBy' => 'date',
            'order' => 'DESC',
            'categories' => array(),
            'tags' => array(),
            'pagination' => true,
            'imageSize' => 'medium_large',
            'excerptLength' => 20
        );
        
        $atts = wp_parse_args($attributes, $defaults);
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $atts['postsPerPage'],
            'orderby' => $atts['orderBy'],
            'order' => $atts['order'],
            'paged' => $paged,
            'post_status' => 'publish'
        );
        
        if (!empty($atts['categories'])) {
            $args['category__in'] = $atts['categories'];
        }
        
        if (!empty($atts['tags'])) {
            $args['tag__in'] = $atts['tags'];
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            echo '<div class="ubcg-latest-posts-grid ubcg-columns-' . esc_attr($atts['columns']) . '">';
            
            while ($query->have_posts()) {
                $query->the_post();
                self::render_post_card($atts);
            }
            
            echo '</div>';
            
            if ($atts['pagination'] && $query->max_num_pages > 1) {
                echo '<div class="ubcg-pagination">';
                echo paginate_links(array(
                    'total' => $query->max_num_pages,
                    'current' => $paged,
                    'prev_text' => __('&laquo; Previous', 'ubcg'),
                    'next_text' => __('Next &raquo;', 'ubcg'),
                    'type' => 'list'
                ));
                echo '</div>';
            }
        } else {
            echo '<p class="ubcg-no-posts">' . __('No posts found.', 'ubcg') . '</p>';
        }
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    public static function render_featured_posts($attributes) {
        $defaults = array(
            'postsPerPage' => 3,
            'layout' => 'horizontal',
            'showImage' => true,
            'showDate' => true,
            'showExcerpt' => true,
            'categories' => array()
        );
        
        $atts = wp_parse_args($attributes, $defaults);
        
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $atts['postsPerPage'],
            'meta_key' => '_ubcg_featured',
            'meta_value' => '1',
            'post_status' => 'publish'
        );
        
        if (!empty($atts['categories'])) {
            $args['category__in'] = $atts['categories'];
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            echo '<div class="ubcg-featured-posts ubcg-layout-' . esc_attr($atts['layout']) . '">';
            
            while ($query->have_posts()) {
                $query->the_post();
                echo '<article class="ubcg-featured-post-item">';
                
                if ($atts['showImage'] && has_post_thumbnail()) {
                    echo '<div class="ubcg-post-thumbnail">';
                    echo '<a href="' . esc_url(get_permalink()) . '">';
                    the_post_thumbnail('large', array('loading' => 'lazy'));
                    echo '</a>';
                    echo '</div>';
                }
                
                echo '<div class="ubcg-post-content">';
                echo '<h3 class="ubcg-post-title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h3>';
                
                if ($atts['showDate']) {
                    echo '<time class="ubcg-post-date" datetime="' . esc_attr(get_the_date('c')) . '">' . get_the_date() . '</time>';
                }
                
                if ($atts['showExcerpt']) {
                    echo '<div class="ubcg-post-excerpt">' . wp_trim_words(get_the_excerpt(), 30) . '</div>';
                }
                
                echo '</div>';
                echo '</article>';
            }
            
            echo '</div>';
        }
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    public static function render_category_showcase($attributes) {
        $defaults = array(
            'categories' => array(),
            'showCount' => true,
            'showDescription' => true,
            'columns' => 3
        );
        
        $atts = wp_parse_args($attributes, $defaults);
        
        $cat_args = array(
            'taxonomy' => 'category',
            'hide_empty' => true
        );
        
        if (!empty($atts['categories'])) {
            $cat_args['include'] = $atts['categories'];
        }
        
        $categories = get_terms($cat_args);
        
        ob_start();
        
        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<div class="ubcg-category-showcase ubcg-columns-' . esc_attr($atts['columns']) . '">';
            
            foreach ($categories as $category) {
                echo '<div class="ubcg-category-item">';
                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="ubcg-category-link">';
                echo '<h3 class="ubcg-category-name">' . esc_html($category->name) . '</h3>';
                
                if ($atts['showCount']) {
                    echo '<span class="ubcg-category-count">' . sprintf(_n('%s article', '%s articles', $category->count, 'ubcg'), number_format_i18n($category->count)) . '</span>';
                }
                
                if ($atts['showDescription'] && $category->description) {
                    echo '<p class="ubcg-category-description">' . esc_html($category->description) . '</p>';
                }
                
                echo '</a>';
                echo '</div>';
            }
            
            echo '</div>';
        }
        
        return ob_get_clean();
    }
    
    public static function render_tag_cloud_gaming($attributes) {
        $defaults = array(
            'number' => 30,
            'orderby' => 'count',
            'order' => 'DESC'
        );
        
        $atts = wp_parse_args($attributes, $defaults);
        
        $tags = get_tags(array(
            'number' => $atts['number'],
            'orderby' => $atts['orderby'],
            'order' => $atts['order']
        ));
        
        ob_start();
        
        if (!empty($tags) && !is_wp_error($tags)) {
            echo '<div class="ubcg-tag-cloud">';
            
            foreach ($tags as $tag) {
                $font_size = 12 + ($tag->count * 0.5);
                $font_size = min($font_size, 24);
                
                echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="ubcg-tag-item" style="font-size: ' . $font_size . 'px;">';
                echo esc_html($tag->name);
                echo '<span class="ubcg-tag-count">' . $tag->count . '</span>';
                echo '</a>';
            }
            
            echo '</div>';
        }
        
        return ob_get_clean();
    }
    
    public static function render_gaming_hero($attributes) {
        $defaults = array(
            'title' => 'Cloud Gaming News & Reviews',
            'subtitle' => 'Your ultimate source for cloud gaming content',
            'backgroundImage' => '',
            'showButton' => true,
            'buttonText' => 'Explore Now',
            'buttonLink' => '#'
        );
        
        $atts = wp_parse_args($attributes, $defaults);
        
        ob_start();
        
        $style = '';
        if (!empty($atts['backgroundImage'])) {
            $style = 'style="background-image: url(' . esc_url($atts['backgroundImage']) . ');"';
        }
        
        echo '<div class="ubcg-gaming-hero" ' . $style . '>';
        echo '<div class="ubcg-hero-content">';
        echo '<h1 class="ubcg-hero-title">' . esc_html($atts['title']) . '</h1>';
        echo '<p class="ubcg-hero-subtitle">' . esc_html($atts['subtitle']) . '</p>';
        
        if ($atts['showButton']) {
            echo '<a href="' . esc_url($atts['buttonLink']) . '" class="ubcg-hero-button">' . esc_html($atts['buttonText']) . '</a>';
        }
        
        echo '</div>';
        echo '</div>';
        
        return ob_get_clean();
    }
    
    public static function render_review_card($attributes) {
        $defaults = array(
            'postId' => 0,
            'rating' => 0,
            'showRating' => true
        );
        
        $atts = wp_parse_args($attributes, $defaults);
        
        if (!$atts['postId']) {
            return '';
        }
        
        $post = get_post($atts['postId']);
        
        if (!$post) {
            return '';
        }
        
        ob_start();
        
        echo '<div class="ubcg-review-card">';
        
        if (has_post_thumbnail($post->ID)) {
            echo '<div class="ubcg-review-thumbnail">';
            echo get_the_post_thumbnail($post->ID, 'medium_large', array('loading' => 'lazy'));
            echo '</div>';
        }
        
        echo '<div class="ubcg-review-content">';
        echo '<h3 class="ubcg-review-title">' . esc_html($post->post_title) . '</h3>';
        
        if ($atts['showRating'] && $atts['rating'] > 0) {
            echo '<div class="ubcg-rating">';
            for ($i = 1; $i <= 5; $i++) {
                $class = $i <= $atts['rating'] ? 'ubcg-star-filled' : 'ubcg-star-empty';
                echo '<span class="ubcg-star ' . $class . '">★</span>';
            }
            echo '<span class="ubcg-rating-text">' . $atts['rating'] . '/5</span>';
            echo '</div>';
        }
        
        echo '<div class="ubcg-review-excerpt">' . wp_trim_words($post->post_excerpt, 25) . '</div>';
        echo '<a href="' . esc_url(get_permalink($post->ID)) . '" class="ubcg-review-link">' . __('Read Full Review', 'ubcg') . '</a>';
        echo '</div>';
        echo '</div>';
        
        return ob_get_clean();
    }
    
    public static function render_game_specs($attributes) {
        $defaults = array(
            'gameName' => '',
            'specs' => array()
        );
        
        $atts = wp_parse_args($attributes, $defaults);
        
        ob_start();
        
        echo '<div class="ubcg-game-specs">';
        
        if (!empty($atts['gameName'])) {
            echo '<h3 class="ubcg-specs-title">' . esc_html($atts['gameName']) . ' - ' . __('Specifications', 'ubcg') . '</h3>';
        }
        
        if (!empty($atts['specs'])) {
            echo '<dl class="ubcg-specs-list">';
            foreach ($atts['specs'] as $spec) {
                echo '<dt class="ubcg-spec-label">' . esc_html($spec['label']) . '</dt>';
                echo '<dd class="ubcg-spec-value">' . esc_html($spec['value']) . '</dd>';
            }
            echo '</dl>';
        }
        
        echo '</div>';
        
        return ob_get_clean();
    }
    
    public static function render_streaming_platforms($attributes) {
        $defaults = array(
            'platforms' => array()
        );
        
        $atts = wp_parse_args($attributes, $defaults);
        
        $default_platforms = array(
            array('name' => 'GeForce NOW', 'url' => 'https://www.nvidia.com/en-us/geforce-now/', 'logo' => ''),
            array('name' => 'Xbox Cloud Gaming', 'url' => 'https://www.xbox.com/play', 'logo' => ''),
            array('name' => 'PlayStation Plus', 'url' => 'https://www.playstation.com/ps-plus/', 'logo' => ''),
            array('name' => 'Amazon Luna', 'url' => 'https://www.amazon.com/luna/', 'logo' => ''),
            array('name' => 'Google Stadia', 'url' => 'https://stadia.google.com/', 'logo' => '')
        );
        
        $platforms = !empty($atts['platforms']) ? $atts['platforms'] : $default_platforms;
        
        ob_start();
        
        echo '<div class="ubcg-streaming-platforms">';
        echo '<h3 class="ubcg-platforms-title">' . __('Available on Cloud Gaming Platforms', 'ubcg') . '</h3>';
        echo '<div class="ubcg-platforms-grid">';
        
        foreach ($platforms as $platform) {
            echo '<a href="' . esc_url($platform['url']) . '" class="ubcg-platform-item" target="_blank" rel="noopener">';
            
            if (!empty($platform['logo'])) {
                echo '<img src="' . esc_url($platform['logo']) . '" alt="' . esc_attr($platform['name']) . '" loading="lazy">';
            } else {
                echo '<span class="ubcg-platform-name">' . esc_html($platform['name']) . '</span>';
            }
            
            echo '</a>';
        }
        
        echo '</div>';
        echo '</div>';
        
        return ob_get_clean();
    }
    
    public static function render_performance_stats($attributes) {
        $defaults = array(
            'stats' => array()
        );
        
        $atts = wp_parse_args($attributes, $defaults);
        
        ob_start();
        
        echo '<div class="ubcg-performance-stats">';
        
        if (!empty($atts['stats'])) {
            echo '<div class="ubcg-stats-grid">';
            
            foreach ($atts['stats'] as $stat) {
                echo '<div class="ubcg-stat-item">';
                echo '<div class="ubcg-stat-value">' . esc_html($stat['value']) . '</div>';
                echo '<div class="ubcg-stat-label">' . esc_html($stat['label']) . '</div>';
                echo '</div>';
            }
            
            echo '</div>';
        }
        
        echo '</div>';
        
        return ob_get_clean();
    }
    
    public static function render_newsletter_signup($attributes) {
        $defaults = array(
            'title' => 'Subscribe to Our Newsletter',
            'description' => 'Get the latest cloud gaming news delivered to your inbox',
            'buttonText' => 'Subscribe'
        );
        
        $atts = wp_parse_args($attributes, $defaults);
        
        ob_start();
        
        echo '<div class="ubcg-newsletter-signup">';
        echo '<h3 class="ubcg-newsletter-title">' . esc_html($atts['title']) . '</h3>';
        echo '<p class="ubcg-newsletter-description">' . esc_html($atts['description']) . '</p>';
        
        echo '<form class="ubcg-newsletter-form" method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="ubcg_newsletter_signup">';
        echo wp_nonce_field('ubcg_newsletter_nonce', '_ubcg_newsletter_nonce', true, false);
        
        echo '<div class="ubcg-form-group">';
        echo '<input type="email" name="email" class="ubcg-email-input" placeholder="' . esc_attr__('Enter your email', 'ubcg') . '" required>';
        echo '<button type="submit" class="ubcg-submit-button">' . esc_html($atts['buttonText']) . '</button>';
        echo '</div>';
        
        echo '</form>';
        echo '</div>';
        
        return ob_get_clean();
    }
    
    private static function render_post_card($atts) {
        echo '<article class="ubcg-post-card">';
        
        if ($atts['showImage'] && has_post_thumbnail()) {
            echo '<div class="ubcg-post-thumbnail">';
            echo '<a href="' . esc_url(get_permalink()) . '" aria-label="' . esc_attr(get_the_title()) . '">';
            the_post_thumbnail($atts['imageSize'], array('loading' => 'lazy'));
            echo '</a>';
            echo '</div>';
        }
        
        echo '<div class="ubcg-post-content">';
        
        if ($atts['showCategory']) {
            $categories = get_the_category();
            if (!empty($categories)) {
                echo '<span class="ubcg-post-category">' . esc_html($categories[0]->name) . '</span>';
            }
        }
        
        echo '<h3 class="ubcg-post-title"><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h3>';
        
        echo '<div class="ubcg-post-meta">';
        
        if ($atts['showDate']) {
            echo '<time class="ubcg-post-date" datetime="' . esc_attr(get_the_date('c')) . '">' . get_the_date() . '</time>';
        }
        
        if ($atts['showAuthor']) {
            echo '<span class="ubcg-post-author">' . __('by', 'ubcg') . ' ' . get_the_author() . '</span>';
        }
        
        echo '</div>';
        
        if ($atts['showExcerpt']) {
            echo '<div class="ubcg-post-excerpt">' . wp_trim_words(get_the_excerpt(), $atts['excerptLength']) . '</div>';
        }
        
        echo '<a href="' . esc_url(get_permalink()) . '" class="ubcg-read-more">' . __('Read More', 'ubcg') . '</a>';
        
        echo '</div>';
        echo '</article>';
    }
}
