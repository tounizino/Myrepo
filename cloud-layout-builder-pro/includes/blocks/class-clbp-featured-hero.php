<?php
/**
 * Cloud Layout Builder Pro - Featured Hero Block
 *
 * @package CloudLayoutBuilderPro\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Featured_Hero_Block
 */
class CLBP_Featured_Hero_Block extends CLBP_Block_Base {

    /**
     * Block constructor
     */
    public function __construct() {
        $this->name  = 'featured-hero';
        $this->title = __( 'Featured Hero Article', 'cloud-layout-builder-pro' );
        $this->icon  = 'cover-image';
        $this->attributes = array(
            'postId'           => array(
                'type'    => 'number',
                'default' => 0,
            ),
            'postSource'       => array(
                'type'    => 'string',
                'default' => 'latest',
            ),
            'showOverlay'      => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            'showCategory'     => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            'showButton'       => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            'buttonText'       => array(
                'type'    => 'string',
                'default' => __( 'Read More', 'cloud-layout-builder-pro' ),
            ),
            'overlayOpacity'   => array(
                'type'    => 'number',
                'default' => 0.5,
            ),
            'minHeight'        => array(
                'type'    => 'string',
                'default' => '500px',
            ),
        );
    }

    /**
     * Render block
     *
     * @param array $attributes Block attributes.
     *
     * @return string
     */
    public function render( $attributes ) {
        $attributes = wp_parse_args(
            $attributes,
            array(
                'postId'         => 0,
                'postSource'     => 'latest',
                'showOverlay'    => true,
                'showCategory'   => true,
                'showButton'     => true,
                'buttonText'     => __( 'Read More', 'cloud-layout-builder-pro' ),
                'overlayOpacity' => 0.5,
                'minHeight'      => '500px',
            )
        );

        $post_id = absint( $attributes['postId'] );

        if ( ! $post_id || 'latest' === $attributes['postSource'] ) {
            $posts = get_posts(
                array(
                    'posts_per_page' => 1,
                    'post_status'    => 'publish',
                    'meta_key'       => '_clbp_featured',
                    'meta_value'     => '1',
                )
            );

            if ( empty( $posts ) ) {
                $posts = get_posts(
                    array(
                        'posts_per_page' => 1,
                        'post_status'    => 'publish',
                    )
                );
            }

            if ( ! empty( $posts ) ) {
                $post = $posts[0];
            }
        } else {
            $post = get_post( $post_id );
        }

        if ( ! $post ) {
            return '';
        }

        $thumbnail_url = get_the_post_thumbnail_url( $post->ID, 'full' );

        if ( ! $thumbnail_url ) {
            $thumbnail_url = CLBP_PLUGIN_URL . 'assets/images/placeholder-hero.jpg';
        }

        ob_start();
        ?>
        <section <?php echo $this->get_wrapper_attributes( $attributes ); ?> style="min-height: <?php echo esc_attr( $attributes['minHeight'] ); ?>;">
            <div class="clbp-hero-background" style="background-image: url('<?php echo esc_url( $thumbnail_url ); ?>');">
                <?php if ( $attributes['showOverlay'] ) : ?>
                    <div class="clbp-hero-overlay" style="opacity: <?php echo esc_attr( $attributes['overlayOpacity'] ); ?>;"></div>
                <?php endif; ?>
            </div>

            <div class="clbp-hero-content">
                <?php if ( $attributes['showCategory'] ) : ?>
                    <?php echo CLBP_Helpers::get_category_badge( $post->ID, 'clbp-hero-category' ); ?>
                <?php endif; ?>

                <h1 class="clbp-hero-title">
                    <a href="<?php echo esc_url( get_permalink( $post ) ); ?>">
                        <?php echo esc_html( get_the_title( $post ) ); ?>
                    </a>
                </h1>

                <?php echo CLBP_Helpers::get_post_meta_html( $post->ID, 'clbp-hero-meta' ); ?>

                <div class="clbp-hero-excerpt">
                    <?php echo esc_html( CLBP_Helpers::truncate( get_the_excerpt( $post ), 180 ) ); ?>
                </div>

                <?php if ( $attributes['showButton'] ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="clbp-hero-button">
                        <?php echo esc_html( $attributes['buttonText'] ); ?>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </section>
        <?php
        do_action( 'clbp_track_block_view', 'featured-hero' );

        return ob_get_clean();
    }
}
