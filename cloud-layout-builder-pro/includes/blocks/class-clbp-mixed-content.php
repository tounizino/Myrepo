<?php
/**
 * Cloud Layout Builder Pro - Mixed Content Block
 *
 * @package CloudLayoutBuilderPro\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CLBP_Mixed_Content_Block extends CLBP_Block_Base {

    public function __construct() {
        $this->name  = 'mixed-content';
        $this->title = __( 'Mixed Content', 'cloud-layout-builder-pro' );
        $this->icon  = 'screenoptions';
        $this->attributes = array(
            'title'        => array( 'type' => 'string', 'default' => __( 'Featured Resources', 'cloud-layout-builder-pro' ) ),
            'articles'     => array( 'type' => 'array', 'items' => array( 'type' => 'number' ), 'default' => array() ),
            'banners'      => array( 'type' => 'array', 'default' => array() ),
            'videos'       => array( 'type' => 'array', 'default' => array() ),
            'layout'       => array( 'type' => 'string', 'default' => 'alternating' ),
        );
    }

    public function render( $attributes ) {
        $attributes = wp_parse_args( $attributes, array( 'articles' => array(), 'banners' => array(), 'videos' => array() ) );
        
        if ( empty( $attributes['articles'] ) && empty( $attributes['banners'] ) && empty( $attributes['videos'] ) ) {
            $attributes['articles'] = wp_list_pluck( get_posts( array( 'posts_per_page' => 3, 'post_status' => 'publish' ) ), 'ID' );
        }

        ob_start();
        ?>
        <section <?php echo $this->get_wrapper_attributes( $attributes ); ?>>
            <?php echo $this->get_block_heading( $attributes['title'] ?? '' ); ?>
            <div class="clbp-mixed-content clbp-mixed-content--<?php echo esc_attr( $attributes['layout'] ?? 'alternating' ); ?>">
                <?php if ( ! empty( $attributes['articles'] ) ) : ?>
                    <?php foreach ( $attributes['articles'] as $post_id ) : ?>
                        <?php $post = get_post( $post_id ); if ( ! $post ) continue; ?>
                        <div class="clbp-mixed-item clbp-mixed-item--article">
                            <?php echo $this->get_post_card( $post, array( 'layout' => 'horizontal' ) ); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ( ! empty( $attributes['banners'] ) ) : ?>
                    <?php foreach ( $attributes['banners'] as $banner ) : ?>
                        <div class="clbp-mixed-item clbp-mixed-item--banner">
                            <?php if ( ! empty( $banner['link'] ) ) : ?>
                                <a href="<?php echo esc_url( $banner['link'] ); ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo esc_url( $banner['image'] ); ?>" alt="<?php echo esc_attr( $banner['alt'] ?? '' ); ?>" loading="lazy">
                                </a>
                            <?php else : ?>
                                <img src="<?php echo esc_url( $banner['image'] ); ?>" alt="<?php echo esc_attr( $banner['alt'] ?? '' ); ?>" loading="lazy">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ( ! empty( $attributes['videos'] ) ) : ?>
                    <?php foreach ( $attributes['videos'] as $video ) : ?>
                        <div class="clbp-mixed-item clbp-mixed-item--video">
                            <div class="clbp-video-embed">
                                <iframe src="<?php echo esc_url( $video['url'] ); ?>" frameborder="0" allowfullscreen loading="lazy"></iframe>
                            </div>
                            <?php if ( ! empty( $video['caption'] ) ) : ?>
                                <p class="clbp-video-caption"><?php echo esc_html( $video['caption'] ); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
