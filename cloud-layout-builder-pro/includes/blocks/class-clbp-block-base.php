<?php
/**
 * Cloud Layout Builder Pro - Base Block Class
 *
 * @package CloudLayoutBuilderPro\Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Abstract Class CLBP_Block_Base
 */
abstract class CLBP_Block_Base {

    /**
     * Block name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Block title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Block icon
     *
     * @var string
     */
    protected $icon = 'block-default';

    /**
     * Block category
     *
     * @var string
     */
    protected $category = 'cloud-layout-builder';

    /**
     * Block supports
     *
     * @var array
     */
    protected $supports = array(
        'html'       => false,
        'anchor'     => true,
        'className'  => true,
        'customClassName' => true,
    );

    /**
     * Block attributes
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * Register block
     */
    public function register() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        register_block_type(
            'cloud-layout-builder/' . $this->name,
            array(
                'title'           => $this->title,
                'icon'            => $this->icon,
                'category'        => $this->category,
                'attributes'      => $this->get_attributes(),
                'supports'        => $this->supports,
                'render_callback' => array( $this, 'render_callback' ),
            )
        );
    }

    /**
     * Get block attributes with defaults
     *
     * @return array
     */
    protected function get_attributes() {
        $base_attributes = array(
            'align'             => array(
                'type'    => 'string',
                'default' => '',
            ),
            'className'         => array(
                'type'    => 'string',
                'default' => '',
            ),
            'blockId'           => array(
                'type'    => 'string',
                'default' => '',
            ),
            'deviceVisibility'  => array(
                'type'    => 'object',
                'default' => array(
                    'desktop' => true,
                    'tablet'  => true,
                    'mobile'  => true,
                ),
            ),
        );

        return array_merge( $base_attributes, $this->attributes );
    }

    /**
     * Render callback for server-side rendering
     *
     * @param array  $attributes Block attributes.
     * @param string $content Block content.
     *
     * @return string
     */
    public function render_callback( $attributes, $content = '' ) {
        return $this->render( $attributes );
    }

    /**
     * Render the block
     *
     * @param array $attributes Block attributes.
     *
     * @return string
     */
    abstract public function render( $attributes );

    /**
     * Get wrapper attributes
     *
     * @param array $attributes Block attributes.
     *
     * @return string
     */
    protected function get_wrapper_attributes( $attributes ) {
        $classes = array( 'clbp-block', 'clbp-block-' . $this->name );

        if ( ! empty( $attributes['className'] ) ) {
            $classes[] = esc_attr( $attributes['className'] );
        }

        if ( ! empty( $attributes['align'] ) ) {
            $classes[] = 'align' . esc_attr( $attributes['align'] );
        }

        $visibility_classes = CLBP_Helpers::get_visibility_classes( $attributes );
        if ( $visibility_classes ) {
            $classes[] = $visibility_classes;
        }

        $attrs = 'class="' . implode( ' ', $classes ) . '"';

        if ( ! empty( $attributes['blockId'] ) ) {
            $attrs .= ' id="' . esc_attr( $attributes['blockId'] ) . '"';
        }

        return $attrs;
    }

    /**
     * Get block heading HTML
     *
     * @param string $title Block title.
     * @param string $subtitle Block subtitle.
     * @param string $class Additional classes.
     *
     * @return string
     */
    protected function get_block_heading( $title = '', $subtitle = '', $class = '' ) {
        if ( empty( $title ) ) {
            return '';
        }

        $output = '<div class="clbp-block-heading ' . esc_attr( $class ) . '">';

        if ( ! empty( $title ) ) {
            $output .= '<h2 class="clbp-block-title">' . esc_html( $title ) . '</h2>';
        }

        if ( ! empty( $subtitle ) ) {
            $output .= '<p class="clbp-block-subtitle">' . esc_html( $subtitle ) . '</p>';
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Get post card HTML
     *
     * @param WP_Post $post Post object.
     * @param array   $args Card args.
     *
     * @return string
     */
    protected function get_post_card( $post, $args = array() ) {
        return CLBP_Helpers::render_post_card( $post, $args );
    }
}
