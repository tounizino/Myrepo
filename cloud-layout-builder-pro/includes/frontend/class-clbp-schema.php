<?php
/**
 * Cloud Layout Builder Pro - Schema Markup
 *
 * @package CloudLayoutBuilderPro\Frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Schema
 */
class CLBP_Schema {

    /**
     * Instance
     *
     * @var CLBP_Schema
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return CLBP_Schema
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_footer', array( $this, 'output_schema' ), 90 );
    }

    /**
     * Output schema markup
     */
    public function output_schema() {
        if ( ! CLBP_Settings::get( 'enable_schema', true ) ) {
            return;
        }

        if ( ! is_home() && ! is_front_page() ) {
            return;
        }

        $posts = get_posts(
            array(
                'post_type'           => 'post',
                'posts_per_page'      => 8,
                'post_status'         => 'publish',
                'ignore_sticky_posts' => true,
            )
        );

        if ( empty( $posts ) ) {
            return;
        }

        $items = array();

        foreach ( $posts as $post ) {
            $items[] = array(
                '@type'            => 'BlogPosting',
                'headline'         => get_the_title( $post ),
                'datePublished'    => get_the_date( DATE_ISO8601, $post ),
                'dateModified'     => get_the_modified_date( DATE_ISO8601, $post ),
                'author'           => array(
                    '@type' => 'Person',
                    'name'  => get_the_author_meta( 'display_name', $post->post_author ),
                ),
                'publisher'        => array(
                    '@type' => 'Organization',
                    'name'  => get_bloginfo( 'name' ),
                    'logo'  => array(
                        '@type' => 'ImageObject',
                        'url'   => get_site_icon_url(),
                    ),
                ),
                'mainEntityOfPage' => get_permalink( $post ),
                'image'            => get_the_post_thumbnail_url( $post, 'large' ) ?: get_site_icon_url(),
                'description'      => wp_trim_words( wp_strip_all_tags( $post->post_content ), 24 ),
            );
        }

        $schema = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'Blog',
            '@id'             => home_url( '#website' ),
            'url'             => home_url(),
            'name'            => get_bloginfo( 'name' ),
            'description'     => get_bloginfo( 'description' ),
            'blogPost'        => $items,
            'inLanguage'      => get_bloginfo( 'language' ),
            'publisher'       => array(
                '@type' => 'Organization',
                'name'  => get_bloginfo( 'name' ),
                'logo'  => array(
                    '@type' => 'ImageObject',
                    'url'   => get_site_icon_url(),
                ),
            ),
        );

        echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
    }
}
