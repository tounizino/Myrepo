<?php
/**
 * Cloud Layout Builder Pro - Carousel Block
 *
 * @package CloudLayoutBuilderPro\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CLBP_Carousel_Block extends CLBP_Block_Base {

    public function __construct() {
        $this->name  = 'carousel';
        $this->title = __( 'Content Carousel', 'cloud-layout-builder-pro' );
        $this->icon  = 'slides';
        $this->attributes = array(
            'title'          => array( 'type' => 'string', 'default' => __( 'Trending Now', 'cloud-layout-builder-pro' ) ),
            'subtitle'       => array( 'type' => 'string', 'default' => '' ),
            'source'         => array( 'type' => 'string', 'default' => 'latest' ),
            'categories'     => array( 'type' => 'array', 'items' => array( 'type' => 'number' ), 'default' => array() ),
            'tags'           => array( 'type' => 'array', 'items' => array( 'type' => 'number' ), 'default' => array() ),
            'authors'        => array( 'type' => 'array', 'items' => array( 'type' => 'number' ), 'default' => array() ),
            'itemCount'      => array( 'type' => 'number', 'default' => 6 ),
            'autoplay'       => array( 'type' => 'boolean', 'default' => true ),
            'autoplaySpeed'  => array( 'type' => 'number', 'default' => 5000 ),
            'showDots'       => array( 'type' => 'boolean', 'default' => true ),
            'showArrows'     => array( 'type' => 'boolean', 'default' => true ),
            'itemsPerView'   => array( 'type' => 'number', 'default' => 3 ),
        );
    }

    public function render( $attributes ) {
        $attributes = wp_parse_args( $attributes, array( 'source' => 'latest', 'itemCount' => 6, 'itemsPerView' => 3 ) );

        $args = CLBP_Query::build_args_from_settings( array_merge( $attributes, array( 'postsToShow' => $attributes['itemCount'] ) ) );
        $args['posts_per_page'] = $attributes['itemCount'];

        $query = CLBP_Query::get_posts( $args );

        if ( ! $query->have_posts() ) {
            return '';
        }

        ob_start();
        ?>
        <section <?php echo $this->get_wrapper_attributes( $attributes ); ?> data-carousel="true" data-autoplay="<?php echo esc_attr( $attributes['autoplay'] ? 'true' : 'false' ); ?>" data-speed="<?php echo esc_attr( $attributes['autoplaySpeed'] ); ?>" data-items="<?php echo esc_attr( $attributes['itemsPerView'] ); ?>">
            <?php echo $this->get_block_heading( $attributes['title'] ?? '', $attributes['subtitle'] ?? '' ); ?>
            <div class="clbp-carousel">
                <div class="clbp-carousel__track">
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <div class="clbp-carousel__slide">
                            <?php echo $this->get_post_card( get_post(), array( 'layout' => 'vertical' ) ); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php if ( $attributes['showArrows'] ) : ?>
                    <button class="clbp-carousel__nav clbp-carousel__nav--prev" aria-label="<?php esc_attr_e( 'Previous slide', 'cloud-layout-builder-pro' ); ?>">&#10094;</button>
                    <button class="clbp-carousel__nav clbp-carousel__nav--next" aria-label="<?php esc_attr_e( 'Next slide', 'cloud-layout-builder-pro' ); ?>">&#10095;</button>
                <?php endif; ?>
                <?php if ( $attributes['showDots'] ) : ?>
                    <div class="clbp-carousel__dots" aria-hidden="true"></div>
                <?php endif; ?>
            </div>
        </section>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}
