<?php
/**
 * Post Queries Helper Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UBCG_Post_Queries {
    
    public static function get_filtered_posts($args = array()) {
        $defaults = array(
            'posts_per_page' => 9,
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => 1
        );
        
        $query_args = wp_parse_args($args, $defaults);
        
        return new WP_Query($query_args);
    }
    
    public static function get_related_posts($post_id, $limit = 3) {
        $post = get_post($post_id);
        
        if (!$post) {
            return array();
        }
        
        $categories = wp_get_post_categories($post_id);
        
        if (empty($categories)) {
            return array();
        }
        
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'post__not_in' => array($post_id),
            'category__in' => $categories,
            'orderby' => 'rand'
        );
        
        $query = new WP_Query($args);
        
        return $query->posts;
    }
    
    public static function get_popular_posts($limit = 5) {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'orderby' => 'comment_count',
            'order' => 'DESC'
        );
        
        $query = new WP_Query($args);
        
        return $query->posts;
    }
    
    public static function get_posts_by_category($category_id, $limit = 9) {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'category__in' => array($category_id),
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        $query = new WP_Query($args);
        
        return $query->posts;
    }
    
    public static function get_posts_by_tag($tag_id, $limit = 9) {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'tag__in' => array($tag_id),
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        $query = new WP_Query($args);
        
        return $query->posts;
    }
}
