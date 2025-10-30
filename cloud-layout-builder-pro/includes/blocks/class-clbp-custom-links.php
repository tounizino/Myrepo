<?php
/**
 * Cloud Layout Builder Pro - Custom Links Block
 *
 * @package CloudLayoutBuilderPro\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CLBP_Custom_Links_Block extends CLBP_Block_Base {

    public function __construct() {
        $this->name  = 'custom-links';
        $this->title = __( 'Resource Links', 'cloud-layout-builder-pro' );
        $this->icon  = 'admin-links';
        $this->attributes = array(
            'title'    => array( 'type' => 'string', 'default' => __( 'Quick Access', 'cloud-layout-builder-pro' ) ),
            'links'    => array( 'type' => 'array', 'default' => array() ),
            'columns'  => array( 'type' => 'number', 'default' => 3 ),
            'style'    => array( 'type' => 'string', 'default' => 'cards' ),
        );
    }

    public function render( $attributes ) {
        $attributes = wp_parse_args( $attributes, array( 'links' => array(), 'columns' => 3, 'style' => 'cards' ) );

        if ( empty( $attributes['links'] ) ) {
            return '';
        }

        ob_start();
        ?>
        <section <?php echo $this->get_wrapper_attributes( $attributes ); ?>>
            <?php echo $this->get_block_heading( $attributes['title'] ?? '' ); ?>
            <div class="clbp-links clbp-links--<?php echo esc_attr( $attributes['style'] ); ?> clbp-columns-<?php echo esc_attr( $attributes['columns'] ); ?>">
                <?php foreach ( $attributes['links'] as $link ) : ?>
                    <a class="clbp-link" href="<?php echo esc_url( $link['url'] ?? '#' ); ?>" target="<?php echo ! empty( $link['newTab'] ) ? '_blank' : '_self'; ?>" rel="noopener">
                        <?php if ( ! empty( $link['icon'] ) ) : ?>
                            <span class="clbp-link__icon" aria-hidden="true">
                                <i class="<?php echo esc_attr( $link['icon'] ); ?>"></i>
                            </span>
                        <?php endif; ?>
                        <span class="clbp-link__content">
                            <span class="clbp-link__label"><?php echo esc_html( $link['label'] ?? '' ); ?></span>
                            <?php if ( ! empty( $link['description'] ) ) : ?>
                                <span class="clbp-link__description"><?php echo esc_html( $link['description'] ); ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="clbp-link__chevron" aria-hidden="true">&#10132;</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
