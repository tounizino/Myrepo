<?php
/**
 * Cloud Layout Builder Pro - Helper Functions
 *
 * @package CloudLayoutBuilderPro\Utils
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Helpers
 */
class CLBP_Helpers {

    /**
     * Sanitize array recursively
     *
     * @param array $array Input array.
     *
     * @return array
     */
    public static function sanitize_array( $array ) {
        foreach ( $array as $key => $value ) {
            if ( is_array( $value ) ) {
                $array[ $key ] = self::sanitize_array( $value );
            } else {
                $array[ $key ] = sanitize_text_field( $value );
            }
        }
        return $array;
    }

    /**
     * Get post thumbnail with fallback
     *
     * @param int    $post_id Post ID.
     * @param string $size Image size.
     * @param bool   $lazy Enable lazy loading.
     *
     * @return string
     */
    public static function get_post_thumbnail( $post_id, $size = 'large', $lazy = true ) {
        if ( has_post_thumbnail( $post_id ) ) {
            $attr = $lazy ? array( 'loading' => 'lazy' ) : array();
            return get_the_post_thumbnail( $post_id, $size, $attr );
        }

        return self::get_placeholder_image( $size );
    }

    /**
     * Get placeholder image
     *
     * @param string $size Image size.
     *
     * @return string
     */
    public static function get_placeholder_image( $size = 'large' ) {
        $dimensions = self::get_image_dimensions( $size );
        $svg        = '<svg width="' . $dimensions['width'] . '" height="' . $dimensions['height'] . '" xmlns="http://www.w3.org/2000/svg">';
        $svg       .= '<rect width="100%" height="100%" fill="#E5E7EB"/>';
        $svg       .= '<text x="50%" y="50%" font-family="Arial" font-size="16" fill="#9CA3AF" dominant-baseline="middle" text-anchor="middle">No Image</text>';
        $svg       .= '</svg>';

        return '<img src="data:image/svg+xml;base64,' . base64_encode( $svg ) . '" alt="Placeholder" class="clbp-placeholder">';
    }

    /**
     * Get image dimensions for size
     *
     * @param string $size Image size.
     *
     * @return array
     */
    public static function get_image_dimensions( $size ) {
        $sizes = array(
            'thumbnail' => array( 'width' => 150, 'height' => 150 ),
            'medium'    => array( 'width' => 300, 'height' => 225 ),
            'large'     => array( 'width' => 800, 'height' => 600 ),
            'full'      => array( 'width' => 1200, 'height' => 900 ),
        );

        return isset( $sizes[ $size ] ) ? $sizes[ $size ] : $sizes['large'];
    }

    /**
     * Truncate text
     *
     * @param string $text Text to truncate.
     * @param int    $length Maximum length.
     * @param string $suffix Suffix to append.
     *
     * @return string
     */
    public static function truncate( $text, $length = 150, $suffix = '...' ) {
        if ( strlen( $text ) <= $length ) {
            return $text;
        }

        return substr( $text, 0, $length ) . $suffix;
    }

    /**
     * Get reading time estimate
     *
     * @param string $content Post content.
     *
     * @return int
     */
    public static function get_reading_time( $content ) {
        $word_count = str_word_count( wp_strip_all_tags( $content ) );
        $minutes    = ceil( $word_count / 200 );
        return max( 1, $minutes );
    }

    /**
     * Format number for display
     *
     * @param int $number Number to format.
     *
     * @return string
     */
    public static function format_number( $number ) {
        if ( $number >= 1000000 ) {
            return round( $number / 1000000, 1 ) . 'M';
        } elseif ( $number >= 1000 ) {
            return round( $number / 1000, 1 ) . 'K';
        }
        return (string) $number;
    }

    /**
     * Get category badge HTML
     *
     * @param int    $post_id Post ID.
     * @param string $class Additional CSS class.
     *
     * @return string
     */
    public static function get_category_badge( $post_id, $class = '' ) {
        $categories = get_the_category( $post_id );

        if ( empty( $categories ) ) {
            return '';
        }

        $category = $categories[0];
        $color    = get_term_meta( $category->term_id, 'clbp_category_color', true );

        if ( ! $color ) {
            $color = CLBP_Settings::get( 'primary_color', '#1E88E5' );
        }

        $output = '<span class="clbp-category-badge ' . esc_attr( $class ) . '" style="background-color: ' . esc_attr( $color ) . ';">';
        $output .= esc_html( $category->name );
        $output .= '</span>';

        return $output;
    }

    /**
     * Get post meta with fallback
     *
     * @param int    $post_id Post ID.
     * @param string $classes Additional classes.
     *
     * @return string
     */
    public static function get_post_meta_html( $post_id, $classes = '' ) {
        $output = '<div class="clbp-post-meta ' . esc_attr( $classes ) . '">';

        $output .= '<span class="clbp-meta-author">';
        $output .= get_avatar( get_the_author_meta( 'ID' ), 24 );
        $output .= '<span>' . get_the_author() . '</span>';
        $output .= '</span>';

        $output .= '<span class="clbp-meta-date">';
        $output .= '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>';
        $output .= '<span>' . get_the_date() . '</span>';
        $output .= '</span>';

        $reading_time = self::get_reading_time( get_post_field( 'post_content', $post_id ) );
        $output      .= '<span class="clbp-meta-reading-time">';
        $output      .= '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/></svg>';
        $output      .= '<span>' . sprintf( _n( '%d min read', '%d mins read', $reading_time, 'cloud-layout-builder-pro' ), $reading_time ) . '</span>';
        $output      .= '</span>';

        $output .= '</div>';

        return $output;
    }

    /**
     * Check if device visibility is enabled
     *
     * @param array  $settings Block settings.
     * @param string $device Device type.
     *
     * @return bool
     */
    public static function is_device_visible( $settings, $device ) {
        if ( ! isset( $settings['device_visibility'] ) ) {
            return true;
        }

        return ! empty( $settings['device_visibility'][ $device ] );
    }

    /**
     * Get visibility classes
     *
     * @param array $settings Block settings.
     *
     * @return string
     */
    public static function get_visibility_classes( $settings ) {
        $classes = array();

        if ( ! self::is_device_visible( $settings, 'desktop' ) ) {
            $classes[] = 'clbp-hide-desktop';
        }

        if ( ! self::is_device_visible( $settings, 'tablet' ) ) {
            $classes[] = 'clbp-hide-tablet';
        }

        if ( ! self::is_device_visible( $settings, 'mobile' ) ) {
            $classes[] = 'clbp-hide-mobile';
        }

        return implode( ' ', $classes );
    }

    /**
     * Generate unique block ID
     *
     * @return string
     */
    public static function generate_block_id() {
        return 'block_' . uniqid() . '_' . wp_rand( 1000, 9999 );
    }

    /**
     * Verify nonce
     *
     * @param string $nonce Nonce value.
     * @param string $action Nonce action.
     *
     * @return bool
     */
    public static function verify_nonce( $nonce, $action = 'clbp_nonce' ) {
        return wp_verify_nonce( $nonce, $action );
    }

    /**
     * Render post card HTML
     *
     * @param WP_Post $post Post object.
     * @param array   $args Optional arguments.
     *
     * @return string
     */
    public static function render_post_card( $post, $args = array() ) {
        if ( ! $post instanceof WP_Post ) {
            return '';
        }

        $defaults = array(
            'show_image'       => true,
            'show_category'    => true,
            'show_meta'        => true,
            'show_excerpt'     => true,
            'excerpt_length'   => 120,
            'image_size'       => 'large',
            'layout'           => 'vertical',
        );

        $args = wp_parse_args( $args, $defaults );

        ob_start();
        ?>
        <article class="clbp-post-card clbp-post-card--<?php echo esc_attr( $args['layout'] ); ?>">
            <?php if ( $args['show_image'] ) : ?>
                <div class="clbp-post-card__thumbnail">
                    <a href="<?php echo esc_url( get_permalink( $post ) ); ?>">
                        <?php echo self::get_post_thumbnail( $post->ID, $args['image_size'] ); ?>
                    </a>
                    <?php if ( $args['show_category'] ) : ?>
                        <?php echo self::get_category_badge( $post->ID ); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="clbp-post-card__content">
                <h3 class="clbp-post-card__title">
                    <a href="<?php echo esc_url( get_permalink( $post ) ); ?>">
                        <?php echo esc_html( get_the_title( $post ) ); ?>
                    </a>
                </h3>

                <?php if ( $args['show_meta'] ) : ?>
                    <?php echo self::get_post_meta_html( $post->ID, 'clbp-post-card__meta' ); ?>
                <?php endif; ?>

                <?php if ( $args['show_excerpt'] ) : ?>
                    <div class="clbp-post-card__excerpt">
                        <?php echo esc_html( self::truncate( get_the_excerpt( $post ), $args['excerpt_length'] ) ); ?>
                    </div>
                <?php endif; ?>

                <a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="clbp-post-card__link">
                    <?php esc_html_e( 'Read More', 'cloud-layout-builder-pro' ); ?>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </a>
            </div>
        </article>
        <?php
        return ob_get_clean();
    }

    /**
     * Check if user can manage plugin
     *
     * @return bool
     */
    public static function current_user_can_manage() {
        return current_user_can( 'manage_options' );
    }
}
