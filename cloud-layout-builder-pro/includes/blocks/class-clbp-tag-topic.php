<?php
/**
 * Cloud Layout Builder Pro - Tag/Topic Block
 *
 * @package CloudLayoutBuilderPro\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CLBP_Tag_Topic_Block extends CLBP_Block_Base {

    public function __construct() {
        $this->name  = 'tag-topic';
        $this->title = __( 'Tag/Topic Block', 'cloud-layout-builder-pro' );
        $this->icon  = 'tag';
        $this->attributes = array(
            'title'       => array( 'type' => 'string', 'default' => __( 'Tagged Articles', 'cloud-layout-builder-pro' ) ),
            'subtitle'    => array( 'type' => 'string', 'default' => '' ),
            'tag'         => array( 'type' => 'number', 'default' => 0 ),
            'items'       => array( 'type' => 'number', 'default' => 6 ),
            'columns'     => array( 'type' => 'number', 'default' => 3 ),
            'showMeta'    => array( 'type' => 'boolean', 'default' => true ),
        );
    }

    public function render( $attributes ) {
        $attributes = wp_parse_args( $attributes, array( 'tag' => 0, 'items' => 6, 'columns' => 3, 'showMeta' => true ) );
        
        if ( ! $attributes['tag'] ) {
            return '';
        }

        $query = CLBP_Query::get_posts( array( 'tag__in' => array( absint( $attributes['tag'] ) ), 'posts_per_page' => absint( $attributes['items'] ) ) );

        if ( ! $query->have_posts() ) {
            return '';
        }

        ob_start();
        ?>
        <section <?php echo $this->get_wrapper_attributes( $attributes ); ?>>
            <?php echo $this->get_block_heading( $attributes['title'] ?? '', $attributes['subtitle'] ?? '' ); ?>
            <div class="clbp-tag-grid clbp-columns-<?php echo esc_attr( $attributes['columns'] ); ?>">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <?php echo $this->get_post_card( get_post(), array( 'show_meta' => $attributes['showMeta'] ) ); ?>
                <?php endwhile; ?>
            </div>
        </section>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}
