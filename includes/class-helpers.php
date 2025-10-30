<?php
/**
 * Helper Functions and Utilities
 */

if (!defined('ABSPATH')) {
    exit;
}

class UBCG_Helpers {
    
    /**
     * Get formatted date
     */
    public static function get_formatted_date($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        return sprintf(
            '<time datetime="%s">%s</time>',
            esc_attr(get_the_date('c', $post_id)),
            esc_html(get_the_date('', $post_id))
        );
    }
    
    /**
     * Get reading time estimate
     */
    public static function get_reading_time($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        $content = get_post_field('post_content', $post_id);
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200); // Average reading speed
        
        return sprintf(
            _n('%s min read', '%s min read', $reading_time, 'ubcg'),
            $reading_time
        );
    }
    
    /**
     * Check if post is featured
     */
    public static function is_featured($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        return get_post_meta($post_id, '_ubcg_featured', true) === '1';
    }
    
    /**
     * Set post as featured
     */
    public static function set_featured($post_id, $featured = true) {
        return update_post_meta($post_id, '_ubcg_featured', $featured ? '1' : '0');
    }
    
    /**
     * Get post views count
     */
    public static function get_views($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        $views = get_post_meta($post_id, '_ubcg_views', true);
        return $views ? absint($views) : 0;
    }
    
    /**
     * Increment post views
     */
    public static function increment_views($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        $views = self::get_views($post_id);
        return update_post_meta($post_id, '_ubcg_views', $views + 1);
    }
    
    /**
     * Get responsive image sizes
     */
    public static function get_responsive_image_sizes() {
        return array(
            'thumbnail' => array(150, 150, true),
            'medium' => array(300, 300, true),
            'medium_large' => array(768, 0, false),
            'large' => array(1024, 1024, false),
            'ubcg-featured' => array(1200, 675, true), // 16:9 ratio
            'ubcg-card' => array(400, 300, true),
            'ubcg-hero' => array(1920, 600, true)
        );
    }
    
    /**
     * Sanitize hex color
     */
    public static function sanitize_hex_color($color) {
        if ('' === $color) {
            return '';
        }
        
        if (preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color)) {
            return $color;
        }
        
        return '';
    }
    
    /**
     * Get color contrast
     */
    public static function get_color_contrast($hex_color) {
        $hex_color = str_replace('#', '', $hex_color);
        
        $r = hexdec(substr($hex_color, 0, 2));
        $g = hexdec(substr($hex_color, 2, 2));
        $b = hexdec(substr($hex_color, 4, 2));
        
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }
    
    /**
     * Get social share links
     */
    public static function get_social_share_links($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        $url = urlencode(get_permalink($post_id));
        $title = urlencode(get_the_title($post_id));
        
        return array(
            'twitter' => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
            'linkedin' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title,
            'reddit' => 'https://reddit.com/submit?url=' . $url . '&title=' . $title
        );
    }
    
    /**
     * Check if user can edit post
     */
    public static function current_user_can_edit($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        return current_user_can('edit_post', $post_id);
    }
    
    /**
     * Get plugin settings
     */
    public static function get_settings() {
        return array(
            'primary_color' => get_option('ubcg_primary_color', '#2563eb'),
            'secondary_color' => get_option('ubcg_secondary_color', '#1e40af'),
            'accent_color' => get_option('ubcg_accent_color', '#60a5fa'),
            'text_color' => get_option('ubcg_text_color', '#1e293b'),
            'posts_per_page' => get_option('ubcg_posts_per_page', 9),
            'enable_seo' => get_option('ubcg_enable_seo', true)
        );
    }
    
    /**
     * Generate Schema.org markup
     */
    public static function generate_schema($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        $post = get_post($post_id);
        
        if (!$post || !get_option('ubcg_enable_seo', true)) {
            return '';
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title($post_id),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name', $post->post_author)
            )
        );
        
        if (has_post_thumbnail($post_id)) {
            $schema['image'] = get_the_post_thumbnail_url($post_id, 'full');
        }
        
        return '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
    }
    
    /**
     * Format number for display
     */
    public static function format_number($number) {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return $number;
    }
    
    /**
     * Get related posts
     */
    public static function get_related_posts($post_id = null, $limit = 3) {
        return UBCG_Post_Queries::get_related_posts($post_id, $limit);
    }
}
