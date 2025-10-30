<?php
/**
 * Cloud Layout Builder Pro - Shortcodes
 *
 * @package CloudLayoutBuilderPro\Frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Shortcodes
 */
class CLBP_Shortcodes {

    /**
     * Instance
     *
     * @var CLBP_Shortcodes
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return CLBP_Shortcodes
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode( 'clbp_homepage', array( $this, 'render_homepage' ) );
        add_shortcode( 'clbp_latest_articles', array( $this, 'render_latest_articles' ) );
        add_shortcode( 'clbp_featured_hero', array( $this, 'render_featured_hero' ) );
        add_shortcode( 'clbp_category_highlight', array( $this, 'render_category_highlight' ) );
        add_shortcode( 'clbp_tag_topic', array( $this, 'render_tag_topic' ) );
        add_shortcode( 'clbp_carousel', array( $this, 'render_carousel' ) );
        add_shortcode( 'clbp_mixed_content', array( $this, 'render_mixed_content' ) );
        add_shortcode( 'clbp_custom_links', array( $this, 'render_custom_links' ) );
        add_shortcode( 'clbp_newsletter', array( $this, 'render_newsletter' ) );
        add_shortcode( 'clbp_quote_tip', array( $this, 'render_quote_tip' ) );
    }

    /**
     * Render homepage shortcode
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_homepage( $atts ) {
        $blocks = get_option( 'clbp_homepage_blocks', array() );

        if ( empty( $blocks ) ) {
            return '';
        }

        usort( $blocks, function( $a, $b ) {
            return ( $a['order'] ?? 0 ) <=> ( $b['order'] ?? 0 );
        });

        ob_start();

        echo '<div class="clbp-homepage-wrapper">';

        foreach ( $blocks as $block ) {
            if ( empty( $block['enabled'] ) ) {
                continue;
            }

            $block_type = $block['type'] ?? '';
            $settings   = $block['settings'] ?? array();

            $this->render_block( $block_type, $settings );
        }

        echo '</div>';

        return ob_get_clean();
    }

    /**
     * Render a block
     *
     * @param string $type Block type.
     * @param array  $settings Block settings.
     */
    private function render_block( $type, $settings ) {
        $class_map = array(
            'latest_articles'    => 'CLBP_Latest_Articles_Block',
            'featured_hero'      => 'CLBP_Featured_Hero_Block',
            'category_highlight' => 'CLBP_Category_Highlight_Block',
            'tag_topic'          => 'CLBP_Tag_Topic_Block',
            'carousel'           => 'CLBP_Carousel_Block',
            'mixed_content'      => 'CLBP_Mixed_Content_Block',
            'custom_links'       => 'CLBP_Custom_Links_Block',
            'newsletter'         => 'CLBP_Newsletter_Block',
            'quote_tip'          => 'CLBP_Quote_Tip_Block',
        );

        if ( ! isset( $class_map[ $type ] ) || ! class_exists( $class_map[ $type ] ) ) {
            return;
        }

        $block = new $class_map[ $type ]();
        echo $block->render( $settings );
    }

    /**
     * Render latest articles shortcode
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_latest_articles( $atts ) {
        $block = new CLBP_Latest_Articles_Block();
        return $block->render( $atts );
    }

    /**
     * Render featured hero shortcode
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_featured_hero( $atts ) {
        $block = new CLBP_Featured_Hero_Block();
        return $block->render( $atts );
    }

    /**
     * Render category highlight shortcode
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_category_highlight( $atts ) {
        $block = new CLBP_Category_Highlight_Block();
        return $block->render( $atts );
    }

    /**
     * Render tag topic shortcode
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_tag_topic( $atts ) {
        $block = new CLBP_Tag_Topic_Block();
        return $block->render( $atts );
    }

    /**
     * Render carousel shortcode
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_carousel( $atts ) {
        $block = new CLBP_Carousel_Block();
        return $block->render( $atts );
    }

    /**
     * Render mixed content shortcode
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_mixed_content( $atts ) {
        $block = new CLBP_Mixed_Content_Block();
        return $block->render( $atts );
    }

    /**
     * Render custom links shortcode
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_custom_links( $atts ) {
        $block = new CLBP_Custom_Links_Block();
        return $block->render( $atts );
    }

    /**
     * Render newsletter shortcode
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_newsletter( $atts ) {
        $block = new CLBP_Newsletter_Block();
        return $block->render( $atts );
    }

    /**
     * Render quote tip shortcode
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public function render_quote_tip( $atts ) {
        $block = new CLBP_Quote_Tip_Block();
        return $block->render( $atts );
    }
}
