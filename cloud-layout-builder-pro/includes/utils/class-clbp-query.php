<?php
/**
 * Cloud Layout Builder Pro - Query Helpers
 *
 * @package CloudLayoutBuilderPro\Utils
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Query
 */
class CLBP_Query {

    /**
     * Get posts based on block attributes
     *
     * @param array $args Arguments.
     *
     * @return WP_Query
     */
    public static function get_posts( $args = array() ) {
        $defaults = array(
            'post_type'           => 'post',
            'posts_per_page'      => 6,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => false,
        );

        $args = wp_parse_args( $args, $defaults );

        return new WP_Query( $args );
    }

    /**
     * Build query args from block settings
     *
     * @param array $settings Block settings.
     *
     * @return array
     */
    public static function build_args_from_settings( $settings ) {
        $args = array();

        $args['posts_per_page'] = isset( $settings['postsToShow'] ) ? absint( $settings['postsToShow'] ) : 6;
        $args['orderby']        = isset( $settings['order'] ) ? sanitize_text_field( $settings['order'] ) : 'date';
        $args['order']          = isset( $settings['orderDirection'] ) ? sanitize_text_field( $settings['orderDirection'] ) : 'DESC';

        if ( ! empty( $settings['source'] ) ) {
            switch ( $settings['source'] ) {
                case 'category':
                    if ( ! empty( $settings['categories'] ) ) {
                        $args['category__in'] = array_map( 'absint', (array) $settings['categories'] );
                    }
                    break;
                case 'tag':
                    if ( ! empty( $settings['tags'] ) ) {
                        $args['tag__in'] = array_map( 'absint', (array) $settings['tags'] );
                    }
                    break;
                case 'author':
                    if ( ! empty( $settings['authors'] ) ) {
                        $args['author__in'] = array_map( 'absint', (array) $settings['authors'] );
                    }
                    break;
                case 'ids':
                    if ( ! empty( $settings['postIds'] ) ) {
                        $args['post__in'] = array_map( 'absint', (array) $settings['postIds'] );
                    }
                    break;
            }
        }

        if ( ! empty( $settings['offset'] ) ) {
            $args['offset'] = absint( $settings['offset'] );
        }

        if ( ! empty( $settings['dateRange'] ) ) {
            $date_range = sanitize_text_field( $settings['dateRange'] );
            $args['date_query'] = array();

            switch ( $date_range ) {
                case '7days':
                    $args['date_query'][] = array( 'after' => gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) ) );
                    break;
                case '30days':
                    $args['date_query'][] = array( 'after' => gmdate( 'Y-m-d H:i:s', strtotime( '-30 days' ) ) );
                    break;
                case '90days':
                    $args['date_query'][] = array( 'after' => gmdate( 'Y-m-d H:i:s', strtotime( '-90 days' ) ) );
                    break;
                case 'year':
                    $args['date_query'][] = array( 'after' => gmdate( 'Y-m-d H:i:s', strtotime( '-1 year' ) ) );
                    break;
            }
        }

        return $args;
    }
}
