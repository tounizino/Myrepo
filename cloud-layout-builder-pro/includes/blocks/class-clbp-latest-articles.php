<?php
/**
 * Cloud Layout Builder Pro - Latest Articles Block
 *
 * @package CloudLayoutBuilderPro\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Latest_Articles_Block
 */
class CLBP_Latest_Articles_Block extends CLBP_Block_Base {

    /**
     * Block constructor
     */
    public function __construct() {
        $this->name  = 'latest-articles';
        $this->title = __( 'Latest Articles Grid', 'cloud-layout-builder-pro' );
        $this->icon  = 'grid-view';
        $this->attributes = array(
            'title'            => array(
                'type'    => 'string',
                'default' => __( 'Latest Insights', 'cloud-layout-builder-pro' ),
            ),
            'subtitle'         => array(
                'type'    => 'string',
                'default' => __( 'Fresh stories from the cloud gaming universe', 'cloud-layout-builder-pro' ),
            ),
            'postsToShow'      => array(
                'type'    => 'number',
                'default' => 9,
            ),
            'columns'          => array(
                'type'    => 'number',
                'default' => 3,
            ),
            'layout'           => array(
                'type'    => 'string',
                'default' => 'grid',
            ),
            'pagination'       => array(
                'type'    => 'string',
                'default' => 'numbers',
            ),
            'source'           => array(
                'type'    => 'string',
                'default' => 'latest',
            ),
            'categories'       => array(
                'type'    => 'array',
                'items'   => array(
                    'type' => 'number',
                ),
                'default' => array(),
            ),
            'tags'             => array(
                'type'    => 'array',
                'items'   => array(
                    'type' => 'number',
                ),
                'default' => array(),
            ),
            'authors'          => array(
                'type'    => 'array',
                'items'   => array(
                    'type' => 'number',
                ),
                'default' => array(),
            ),
            'dateRange'        => array(
                'type'    => 'string',
                'default' => '',
            ),
            'showExcerpt'      => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            'excerptLength'    => array(
                'type'    => 'number',
                'default' => 120,
            ),
            'showMeta'         => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            'showCategory'     => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            'masonryGap'       => array(
                'type'    => 'number',
                'default' => 24,
            ),
            'enableInfinite'   => array(
                'type'    => 'boolean',
                'default' => false,
            ),
            'order'            => array(
                'type'    => 'string',
                'default' => 'date',
            ),
            'orderDirection'   => array(
                'type'    => 'string',
                'default' => 'DESC',
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
                'title'          => __( 'Latest Insights', 'cloud-layout-builder-pro' ),
                'subtitle'       => __( 'Fresh stories from the cloud gaming universe', 'cloud-layout-builder-pro' ),
                'postsToShow'    => 9,
                'columns'        => 3,
                'layout'         => 'grid',
                'pagination'     => 'numbers',
                'source'         => 'latest',
                'categories'     => array(),
                'tags'           => array(),
                'authors'        => array(),
                'dateRange'      => '',
                'showExcerpt'    => true,
                'excerptLength'  => 120,
                'showMeta'       => true,
                'showCategory'   => true,
                'masonryGap'     => 24,
                'enableInfinite' => false,
                'order'          => 'date',
                'orderDirection' => 'DESC',
            )
        );

        $args  = CLBP_Query::build_args_from_settings( $attributes );
        $paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
        $args['paged'] = $paged;

        $query = CLBP_Query::get_posts( $args );

        if ( ! $query->have_posts() ) {
            return '';
        }

        $columns = max( 1, min( 4, absint( $attributes['columns'] ) ) );
        $layout  = $attributes['layout'] === 'masonry' ? 'masonry' : 'grid';

        ob_start();
        ?>
        <section <?php echo $this->get_wrapper_attributes( $attributes ); ?> data-pagination-type="<?php echo esc_attr( $attributes['pagination'] ); ?>" data-page="<?php echo esc_attr( $paged ); ?>" data-enable-infinite="<?php echo esc_attr( $attributes['enableInfinite'] ? 'true' : 'false' ); ?>">
            <?php echo $this->get_block_heading( $attributes['title'], $attributes['subtitle'] ); ?>

            <div class="clbp-articles-grid clbp-articles-grid--<?php echo esc_attr( $layout ); ?> clbp-columns-<?php echo esc_attr( $columns ); ?>" data-gap="<?php echo esc_attr( $attributes['masonryGap'] ); ?>">
                <?php
                while ( $query->have_posts() ) :
                    $query->the_post();
                    echo $this->get_post_card(
                        get_post(),
                        array(
                            'show_category'  => $attributes['showCategory'],
                            'show_meta'      => $attributes['showMeta'],
                            'show_excerpt'   => $attributes['showExcerpt'],
                            'excerpt_length' => $attributes['excerptLength'],
                            'layout'         => $layout === 'masonry' ? 'masonry' : 'vertical',
                        )
                    );
                endwhile;
                ?>
            </div>

            <?php if ( 'numbers' === $attributes['pagination'] ) : ?>
                <div class="clbp-pagination">
                    <?php
                    echo paginate_links(
                        array(
                            'total'   => $query->max_num_pages,
                            'current' => $paged,
                        )
                    );
                    ?>
                </div>
            <?php elseif ( $attributes['enableInfinite'] ) : ?>
                <button class="clbp-load-more" data-block="latest-articles" data-query="<?php echo esc_attr( wp_json_encode( $args ) ); ?>">
                    <?php esc_html_e( 'Load more articles', 'cloud-layout-builder-pro' ); ?>
                </button>
            <?php endif; ?>
        </section>
        <?php
        wp_reset_postdata();

        do_action( 'clbp_track_block_view', 'latest-articles' );

        return ob_get_clean();
    }
}
