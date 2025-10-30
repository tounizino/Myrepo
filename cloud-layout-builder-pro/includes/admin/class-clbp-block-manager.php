<?php
/**
 * Cloud Layout Builder Pro - Block Manager
 *
 * @package CloudLayoutBuilderPro\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Block_Manager
 */
class CLBP_Block_Manager {

    /**
     * Instance
     *
     * @var CLBP_Block_Manager
     */
    private static $instance = null;

    /**
     * Registered blocks
     *
     * @var array
     */
    private $blocks = array();

    /**
     * Get instance
     *
     * @return CLBP_Block_Manager
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
        add_action( 'init', array( $this, 'register_assets' ) );
        add_action( 'init', array( $this, 'register_blocks' ), 20 );
        add_filter( 'block_categories_all', array( $this, 'register_block_category' ), 10, 2 );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
    }

    /**
     * Register block category
     *
     * @param array               $categories Categories.
     * @param WP_Post_Type|string $post Post.
     *
     * @return array
     */
    public function register_block_category( $categories, $post ) {
        $category = array(
            'slug'  => 'cloud-layout-builder',
            'title' => __( 'Cloud Layout Builder', 'cloud-layout-builder-pro' ),
            'icon'  => 'layout',
        );

        array_unshift( $categories, $category );

        return $categories;
    }

    /**
     * Register block assets
     */
    public function register_assets() {
        wp_register_script(
            'clbp-blocks',
            CLBP_PLUGIN_URL . 'assets/js/blocks.js',
            array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-data', 'wp-compose', 'wp-i18n', 'wp-server-side-render' ),
            CLBP_VERSION,
            true
        );

        wp_register_style(
            'clbp-blocks-editor',
            CLBP_PLUGIN_URL . 'assets/css/editor.css',
            array( 'wp-edit-blocks' ),
            CLBP_VERSION
        );
    }

    /**
     * Register blocks
     */
    public function register_blocks() {
        $this->blocks = array(
            new CLBP_Latest_Articles_Block(),
            new CLBP_Featured_Hero_Block(),
            new CLBP_Category_Highlight_Block(),
            new CLBP_Tag_Topic_Block(),
            new CLBP_Carousel_Block(),
            new CLBP_Mixed_Content_Block(),
            new CLBP_Custom_Links_Block(),
            new CLBP_Newsletter_Block(),
            new CLBP_Quote_Tip_Block(),
        );

        foreach ( $this->blocks as $block ) {
            $block->register();
        }
    }

    /**
     * Enqueue editor assets
     */
    public function enqueue_editor_assets() {
        wp_enqueue_script( 'clbp-blocks' );
        wp_enqueue_style( 'clbp-blocks-editor' );
    }
}
