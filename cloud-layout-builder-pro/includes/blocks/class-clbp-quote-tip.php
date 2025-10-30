<?php
/**
 * Cloud Layout Builder Pro - Quote / Tip Block
 *
 * @package CloudLayoutBuilderPro\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CLBP_Quote_Tip_Block extends CLBP_Block_Base {

    public function __construct() {
        $this->name  = 'quote-tip';
        $this->title = __( 'Quote / Tip', 'cloud-layout-builder-pro' );
        $this->icon  = 'format-quote';
        $this->attributes = array(
            'quote'       => array( 'type' => 'string', 'default' => __( 'Streaming games isn’t just about speed—it’s about stability and smart optimization.', 'cloud-layout-builder-pro' ) ),
            'author'      => array( 'type' => 'string', 'default' => __( 'Cloud Gaming Pro', 'cloud-layout-builder-pro' ) ),
            'role'        => array( 'type' => 'string', 'default' => __( 'Performance Strategist', 'cloud-layout-builder-pro' ) ),
            'accentColor' => array( 'type' => 'string', 'default' => '' ),
            'style'       => array( 'type' => 'string', 'default' => 'highlight' ),
        );
    }

    public function render( $attributes ) {
        $settings = wp_parse_args( $attributes, array( 'accentColor' => CLBP_Settings::get( 'primary_color', '#1E88E5' ) ) );

        ob_start();
        ?>
        <section <?php echo $this->get_wrapper_attributes( $attributes ); ?>>
            <blockquote class="clbp-quote clbp-quote--<?php echo esc_attr( $settings['style'] ); ?>" style="border-color: <?php echo esc_attr( $settings['accentColor'] ); ?>;">
                <span class="clbp-quote__icon" aria-hidden="true">“</span>
                <p class="clbp-quote__text"><?php echo esc_html( $settings['quote'] ); ?></p>
                <footer class="clbp-quote__footer">
                    <span class="clbp-quote__author"><?php echo esc_html( $settings['author'] ); ?></span>
                    <?php if ( ! empty( $settings['role'] ) ) : ?>
                        <span class="clbp-quote__role"><?php echo esc_html( $settings['role'] ); ?></span>
                    <?php endif; ?>
                </footer>
            </blockquote>
        </section>
        <?php
        return ob_get_clean();
    }
}
