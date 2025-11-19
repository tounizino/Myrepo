<?php
/**
 * Utility helpers for Sections AutoPosts
 */

if (!defined('ABSPATH')) {
    exit;
}

class SAP_Utils {

    /**
     * Build wrapper classes and styles for a section
     */
    public static function build_wrapper_context($section_key, $settings) {
        $classes = array(
            'sap-section',
            'sap-section--' . sanitize_html_class($section_key),
            'sap-wrap-' . sanitize_html_class($section_key),
            'sap-theme-' . sanitize_html_class($settings['theme'])
        );

        if (!empty($settings['custom_classes'])) {
            $custom = preg_split('/\s+/', $settings['custom_classes']);
            foreach ($custom as $class) {
                $class = sanitize_html_class($class);
                if (!empty($class)) {
                    $classes[] = $class;
                }
            }
        }

        $styles = array();
        if (!empty($settings['background_color'])) {
            $styles['background-color'] = $settings['background_color'];
        }
        if (!empty($settings['padding'])) {
            $styles['padding'] = $settings['padding'];
        }
        if (!empty($settings['margin'])) {
            $styles['margin'] = $settings['margin'];
        }

        $inner_styles = array();
        if (!empty($settings['container_width']) && 'full' !== $settings['container_width']) {
            $inner_styles['max-width'] = $settings['container_width'];
        }

        return array(
            'classes' => array_unique($classes),
            'styles' => $styles,
            'inner_styles' => $inner_styles,
        );
    }

    /**
     * Convert styles array to CSS string
     */
    public static function styles_to_string($styles) {
        if (empty($styles) || !is_array($styles)) {
            return '';
        }

        $compiled = array();
        foreach ($styles as $property => $value) {
            if ('' === trim($value)) {
                continue;
            }
            $compiled[] = esc_attr($property) . ':' . esc_attr($value);
        }

        return implode(';', $compiled);
    }

    /**
     * Get post excerpt trimmed to words
     */
    public static function get_excerpt($post, $length = 26) {
        if ($post instanceof WP_Post) {
            $content = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
        } else {
            $content = (string) $post;
        }

        $content = wp_strip_all_tags(strip_shortcodes($content));
        return wp_trim_words($content, max(1, $length), '…');
    }

    /**
     * Estimate reading time for a post (returns "X min read")
     */
    public static function get_reading_time($post, $wpm = 220) {
        if ($post instanceof WP_Post) {
            $content = $post->post_content;
        } else {
            $content = (string) $post;
        }

        $word_count = str_word_count(wp_strip_all_tags($content));
        $minutes = max(1, (int) ceil($word_count / max(100, $wpm)));

        return sprintf(__('%d min read', 'sections-autoposts'), $minutes);
    }

    /**
     * Gather metrics about a post useful for badges
     */
    public static function get_post_metrics($post) {
        $post_id = $post instanceof WP_Post ? $post->ID : (int) $post;
        $published_ts = get_post_time('U', true, $post_id);
        $modified_ts = get_post_modified_time('U', true, $post_id);
        $now = current_time('timestamp');

        $age_seconds = max(0, $now - $published_ts);
        $age_days = $age_seconds / DAY_IN_SECONDS;

        $modified_diff = max(0, $modified_ts - $published_ts);
        $modified_days = $modified_diff / DAY_IN_SECONDS;

        return array(
            'age_seconds' => $age_seconds,
            'age_days' => $age_days,
            'modified_seconds' => max(0, $now - $modified_ts),
            'modified_since_publish_days' => $modified_days,
            'comment_count' => (int) get_comments_number($post_id),
            'is_sticky' => is_sticky($post_id),
        );
    }

    /**
     * Determine badge type based on metrics and settings
     */
    public static function determine_badge($post, $settings, $context = array()) {
        if (isset($settings['widget_badges']) && 'off' === $settings['widget_badges']) {
            return null;
        }

        $metrics = self::get_post_metrics($post);
        $badge = null;

        $new_days = isset($settings['badge_new_days']) ? (int) $settings['badge_new_days'] : 7;
        $updated_days = isset($settings['badge_updated_days']) ? (int) $settings['badge_updated_days'] : 14;
        $hot_comments = isset($settings['badge_hot_comments']) ? (int) $settings['badge_hot_comments'] : 5;

        if ($updated_days > 0) {
            $published_ts = get_post_time('U', true, $post);
            $modified_ts = get_post_modified_time('U', true, $post);
            if ($modified_ts > $published_ts && ($modified_ts - $published_ts) >= DAY_IN_SECONDS && (current_time('timestamp') - $modified_ts) <= $updated_days * DAY_IN_SECONDS) {
                $badge = array(
                    'type' => 'update',
                    'label' => __('Updated', 'sections-autoposts'),
                );
            }
        }

        if (!$badge && $new_days > 0 && $metrics['age_days'] <= $new_days) {
            $badge = array(
                'type' => 'new',
                'label' => __('New', 'sections-autoposts'),
            );
        }

        if (!$badge && ($metrics['comment_count'] >= $hot_comments || $metrics['is_sticky'])) {
            $badge = array(
                'type' => 'hot',
                'label' => __('Hot', 'sections-autoposts'),
            );
        }

        return $badge;
    }

    /**
     * Primary category helper (supports Yoast if available)
     */
    public static function get_primary_category($post_id) {
        $categories = get_the_category($post_id);
        if (empty($categories)) {
            return null;
        }

        if (class_exists('WPSEO_Primary_Term')) {
            $primary_term = new WPSEO_Primary_Term('category', $post_id);
            $primary_term_id = $primary_term->get_primary_term();
            if ($primary_term_id) {
                $term = get_term($primary_term_id);
                if ($term && !is_wp_error($term)) {
                    return $term;
                }
            }
        }

        return $categories[0];
    }

    /**
     * Retrieve terms list for taxonomy
     */
    public static function get_terms_list($post_id, $taxonomy) {
        $terms = get_the_terms($post_id, $taxonomy);
        if (empty($terms) || is_wp_error($terms)) {
            return array();
        }

        $list = array();
        foreach ($terms as $term) {
            $list[] = array(
                'name' => $term->name,
                'slug' => $term->slug,
                'link' => get_term_link($term),
            );
        }
        return $list;
    }

    /**
     * Get image info with placeholder fallback
     */
    public static function get_post_image($post_id, $size = 'large') {
        if (has_post_thumbnail($post_id)) {
            $image_id = get_post_thumbnail_id($post_id);
            $src = wp_get_attachment_image_src($image_id, $size);
            $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            if ($src) {
                return array(
                    'id' => $image_id,
                    'url' => $src[0],
                    'alt' => $alt ? $alt : get_the_title($post_id),
                );
            }
        }

        return array(
            'id' => 0,
            'url' => SAP_PLUGIN_URL . 'assets/img/placeholder.svg',
            'alt' => __('Placeholder image', 'sections-autoposts'),
        );
    }

    /**
     * Generate friendly meta info string
     */
    public static function build_meta_string($post) {
        $date = get_the_date('', $post);
        $reading = self::get_reading_time($post);
        return sprintf('%s · %s', $date, $reading);
    }

    /**
     * Format rating meta (expects "9.2/10" or numeric)
     */
    public static function get_rating_meta($post_id, $meta_key) {
        if (empty($meta_key)) {
            return '';
        }
        $value = get_post_meta($post_id, $meta_key, true);
        if ('' === $value) {
            return '';
        }

        if (is_numeric($value)) {
            $value = number_format_i18n((float) $value, 1) . '/10';
        }

        return $value;
    }

    /**
     * Parse features meta (comma or newline separated)
     */
    public static function parse_features_meta($post_id, $meta_key) {
        if (empty($meta_key)) {
            return array();
        }
        $raw = get_post_meta($post_id, $meta_key, true);
        if (empty($raw)) {
            return array();
        }

        if (is_array($raw)) {
            $items = $raw;
        } else {
            $items = preg_split('/\r?\n|,/', $raw);
        }

        $features = array();
        foreach ($items as $item) {
            $item = trim($item);
            if ($item) {
                $features[] = $item;
            }
        }
        return $features;
    }

    /**
     * Determine difficulty badge
     */
    public static function get_difficulty_badge($post_id, $meta_key, $default = 'PRO') {
        if (empty($meta_key)) {
            return strtoupper($default);
        }
        $value = get_post_meta($post_id, $meta_key, true);
        if (empty($value)) {
            return strtoupper($default);
        }
        return strtoupper($value);
    }

    /**
     * Cycle icon list
     */
    public static function get_icon_for_index($index, $icons) {
        if (empty($icons)) {
            return '⚙️';
        }
        $icons = array_values($icons);
        return $icons[$index % count($icons)];
    }
}
