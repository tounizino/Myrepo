<?php
/**
 * Query helper for fetching posts per section configuration.
 */

if (!defined('ABSPATH')) {
    exit;
}

class SAP_Post_Query {

    /**
     * Fetch formatted posts for a section.
     *
     * @param string $section_key
     * @param array  $section_settings
     * @param array  $options Additional options (count, exclude, excerpt_length, image_size, badge_context)
     * @return array
     */
    public static function fetch($section_key, $section_settings, $options = array()) {
        $defaults = array(
            'count' => 5,
            'exclude' => array(),
            'offset' => 0,
            'excerpt_length' => isset($section_settings['excerpt_length']) ? $section_settings['excerpt_length'] : 26,
            'image_size' => 'large',
            'badge_context' => 'default',
            'additional_args' => array(),
        );

        $options = wp_parse_args($options, $defaults);

        $query_args = self::build_query_args($section_settings, $options);
        $query = new WP_Query($query_args);
        $posts = array();

        if ($query->have_posts()) {
            foreach ($query->posts as $post) {
                $posts[] = self::format_post($post, $section_settings, $options);
            }
        }

        wp_reset_postdata();
        return $posts;
    }

    /**
     * Build base WP_Query arguments from settings.
     */
    private static function build_query_args($section_settings, $options) {
        $count = isset($options['count']) ? (int) $options['count'] : 5;

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $count,
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
            'offset' => isset($options['offset']) ? (int) $options['offset'] : 0,
        );

        // Exclude posts
        if (!empty($options['exclude'])) {
            $args['post__not_in'] = array_map('intval', (array) $options['exclude']);
        }

        $data_source = isset($section_settings['data_source']) ? $section_settings['data_source'] : 'latest';

        switch ($data_source) {
            case 'updated':
                $args['orderby'] = 'modified';
                $args['order'] = 'DESC';
                break;

            case 'category':
                if (!empty($section_settings['categories'])) {
                    $args['category__in'] = array_map('intval', (array) $section_settings['categories']);
                }
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;

            case 'tag':
                if (!empty($section_settings['tags'])) {
                    $args['tag__in'] = array_map('intval', (array) $section_settings['tags']);
                }
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;

            case 'custom':
                $ids = SAP_Settings::instance()->parse_id_list(isset($section_settings['custom_post_ids']) ? $section_settings['custom_post_ids'] : '');
                if (!empty($ids)) {
                    $args['post__in'] = $ids;
                    $args['orderby'] = 'post__in';
                } else {
                    $args['orderby'] = 'date';
                    $args['order'] = 'DESC';
                }
                break;

            case 'popular':
                $args['orderby'] = 'comment_count';
                $args['order'] = 'DESC';
                break;

            case 'latest':
            default:
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;
        }

        // Override with explicit settings
        if (!empty($section_settings['order_by'])) {
            $args['orderby'] = sanitize_key($section_settings['order_by']);
        }
        if (!empty($section_settings['order_direction'])) {
            $direction = strtoupper($section_settings['order_direction']);
            if (in_array($direction, array('ASC', 'DESC'), true)) {
                $args['order'] = $direction;
            }
        }

        if (!empty($options['additional_args']) && is_array($options['additional_args'])) {
            $args = array_merge($args, $options['additional_args']);
        }

        return apply_filters('sap/query_args', $args, $section_settings, $options);
    }

    /**
     * Format post data for templates.
     */
    private static function format_post($post, $section_settings, $options) {
        $post_id = $post instanceof WP_Post ? $post->ID : (int) $post;

        $category = SAP_Utils::get_primary_category($post_id);
        $image = SAP_Utils::get_post_image($post_id, $options['image_size']);
        $excerpt_length = isset($options['excerpt_length']) ? $options['excerpt_length'] : (isset($section_settings['excerpt_length']) ? $section_settings['excerpt_length'] : 26);

        return array(
            'ID' => $post_id,
            'title' => get_the_title($post_id),
            'permalink' => get_permalink($post_id),
            'excerpt' => SAP_Utils::get_excerpt($post, $excerpt_length),
            'image' => $image,
            'category' => $category ? array(
                'name' => $category->name,
                'slug' => $category->slug,
                'link' => get_category_link($category),
            ) : null,
            'date' => array(
                'display' => get_the_date('', $post_id),
                'iso' => get_the_date('c', $post_id),
                'month' => get_the_date('M', $post_id),
                'month_full' => get_the_date('F', $post_id),
                'day' => get_the_date('d', $post_id),
            ),
            'modified' => array(
                'display' => get_the_modified_date('', $post_id),
                'iso' => get_the_modified_date('c', $post_id),
            ),
            'author' => get_the_author_meta('display_name', $post->post_author),
            'reading_time' => SAP_Utils::get_reading_time($post),
            'comment_count' => (int) get_comments_number($post_id),
            'tags' => SAP_Utils::get_terms_list($post_id, 'post_tag'),
            'metrics' => SAP_Utils::get_post_metrics($post),
            'badge' => SAP_Utils::determine_badge($post, $section_settings, $options),
        );
    }
}
