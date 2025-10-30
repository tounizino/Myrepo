<?php
/**
 * Cloud Layout Builder Pro - Category Highlight Block
 *
 * @package CloudLayoutBuilderPro\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Category_Highlight_Block
 */
class CLBP_Category_Highlight_Block extends CLBP_Block_Base {

    /**
     * Constructor
     */
    public function __construct() {
        $this->name  = 'category-highlight';
        $this->title = __( 'Category Highlight', 'cloud-layout-builder-pro' );
        $this->icon  = 'category';
        $this->attributes = array(
            'title'          => array(
                'type'    => 'string',
                'default' => __( 'Category Spotlight', 'cloud-layout-builder-pro' ),
            ),
            'subtitle'       => array(
                'type'    => 'string',
                'default' => __( 'Curated picks from your chosen category', 'cloud-layout-builder-pro' ),
            ),
            'category'       => array(
                'type'    => 'number',
                'default' => 0,
            ),
            'layout'         => array(
                'type'    => 'string',
                'default' => 'horizontal',
            ),
            'items'          => array(
                'type'    => 'number',
                'default' => 6,
            ),
            'showExcerpt'    => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            'showMeta'       => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            'showCategory'   => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            'ctaText'        => array(
                'type'    => 'string',
                'default' => __( 'Explore Category', 'cloud-layout-builder-pro' ),
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
                'title'        => __( 'Category Spotlight', 'cloud-layout-builder-pro' ),
                'subtitle'     => __( 'Curated picks from your chosen category', 'cloud-layout-builder-pro' ),
                'category'     => 0,
                'layout'       => 'horizontal',
                'items'        => 6,
                'showExcerpt'  => true,
                'showMeta'     => true,
                'showCategory' => true,
                'ctaText'      => __( 'Explore Category', 'cloud-layout-builder-pro' ),
            )
        );

        $category_id = absint( $attributes['category'] );

        if ( ! $category_id ) {
            return '';
        }

        $args = array(
            'posts_per_page' => absint( $attributes['items'] ),
            'category__in'   => array( $category_id ),
        );

        $query = CLBP_Query::get_posts( $args );

        if ( ! $query->have_posts() ) {
            return '';
        }

        $term = get_term( $category_id );

        ob_start();
        ?>
        <section <?php echo $this->get_wrapper_attributes( $attributes ); ?>>
            <?php echo $this->get_block_heading( $attributes['title'], $attributes['subtitle'], 'clbp-category-heading' ); ?>

            <div class="clbp-category-highlight clbp-category-highlight--<?php echo esc_attr( $attributes['layout'] ); ?>">
                <?php
                while ( $query->have_posts() ) :
                    $query->the_post();

                    echo $this->get_post_card(
                        get_post(),
                        array(
                            'show_category'  => $attributes['showCategory'],
                            'show_meta'      => $attributes['showMeta'],
                            'show_excerpt'   => $attributes['showExcerpt'],
                            'layout'         => $attributes['layout'] === 'tiles' ? 'vertical' : 'horizontal',
                            'excerpt_length' => $attributes['layout'] === 'horizontal' ? 140 : 90,
                        )
                    );
                endwhile;
                ?>
            </div>

            <div class="clbp-category-cta">
                <a href="<?php echo esc_url( get_category_link( $category_id ) ); ?>" class="clbp-cta-link">
                    <?php echo esc_html( $attributes['ctaText'] ); ?>
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M10.293 15.95a1 1 0 0 0 1.414 0l6.364-6.364a1 1 0 0 0 0-1.414L11.707 1.807a1 1 0 1 0-1.414 1.414L15.536 8.47H3a1 1 0 0 0 0 2h12.536l-5.243 5.243a1 1 0 0 0 0 1.414z"/></svg>
                </a>
                <span class="clbp-category-count">
                    <?php printf( esc_html__( '%s articles in %s', 'cloud-layout-builder-pro' ), esc_html( CLBP_Helpers::format_number( $term->count ) ), esc_html( $term->name ) ); ?>
                </span>
            </div>
        </section>
        <?php
        wp_reset_postdata();

        do_action( 'clbp_track_block_view', 'category-highlight' );

        return ob_get_clean();
    }
}
