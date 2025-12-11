<?php
/**
 * Custom Post Type Registration
 *
 * @package CloudGamingAvailability
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CGA_CPT class
 */
class CGA_CPT {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Register custom post type and taxonomy
	 */
	public function register() {
		// Register Cloud Games CPT
		$labels = array(
			'name'                  => _x( 'Cloud Games', 'Post Type General Name', 'cloud-gaming-availability' ),
			'singular_name'         => _x( 'Cloud Game', 'Post Type Singular Name', 'cloud-gaming-availability' ),
			'menu_name'             => __( 'Cloud Games', 'cloud-gaming-availability' ),
			'name_admin_bar'        => __( 'Cloud Game', 'cloud-gaming-availability' ),
			'archives'              => __( 'Cloud Game Archives', 'cloud-gaming-availability' ),
			'attributes'            => __( 'Cloud Game Attributes', 'cloud-gaming-availability' ),
			'parent_item_colon'     => __( 'Parent Cloud Game:', 'cloud-gaming-availability' ),
			'all_items'             => __( 'All Cloud Games', 'cloud-gaming-availability' ),
			'add_new_item'          => __( 'Add New Cloud Game', 'cloud-gaming-availability' ),
			'add_new'               => __( 'Add New', 'cloud-gaming-availability' ),
			'new_item'              => __( 'New Cloud Game', 'cloud-gaming-availability' ),
			'edit_item'             => __( 'Edit Cloud Game', 'cloud-gaming-availability' ),
			'update_item'           => __( 'Update Cloud Game', 'cloud-gaming-availability' ),
			'view_item'             => __( 'View Cloud Game', 'cloud-gaming-availability' ),
			'view_items'            => __( 'View Cloud Games', 'cloud-gaming-availability' ),
			'search_items'          => __( 'Search Cloud Game', 'cloud-gaming-availability' ),
			'not_found'             => __( 'Not found', 'cloud-gaming-availability' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'cloud-gaming-availability' ),
			'featured_image'        => __( 'Game Cover Image', 'cloud-gaming-availability' ),
			'set_featured_image'    => __( 'Set game cover image', 'cloud-gaming-availability' ),
			'remove_featured_image' => __( 'Remove game cover image', 'cloud-gaming-availability' ),
			'use_featured_image'    => __( 'Use as game cover image', 'cloud-gaming-availability' ),
			'insert_into_item'      => __( 'Insert into cloud game', 'cloud-gaming-availability' ),
			'uploaded_to_this_item' => __( 'Uploaded to this cloud game', 'cloud-gaming-availability' ),
			'items_list'            => __( 'Cloud Games list', 'cloud-gaming-availability' ),
			'items_list_navigation' => __( 'Cloud Games list navigation', 'cloud-gaming-availability' ),
			'filter_items_list'     => __( 'Filter cloud games list', 'cloud-gaming-availability' ),
		);

		$args = array(
			'label'               => __( 'Cloud Game', 'cloud-gaming-availability' ),
			'description'         => __( 'Cloud Gaming Availability Post Type', 'cloud-gaming-availability' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'taxonomies'          => array( 'cloud_platform' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest'        => true,
			'rest_base'           => 'cloud-games',
			'menu_icon'           => 'dashicons-games',
		);

		register_post_type( 'cloud_games', $args );

		// Register Cloud Platform Taxonomy
		$tax_labels = array(
			'name'                       => _x( 'Cloud Platforms', 'Taxonomy General Name', 'cloud-gaming-availability' ),
			'singular_name'              => _x( 'Cloud Platform', 'Taxonomy Singular Name', 'cloud-gaming-availability' ),
			'menu_name'                  => __( 'Platforms', 'cloud-gaming-availability' ),
			'all_items'                  => __( 'All Platforms', 'cloud-gaming-availability' ),
			'parent_item'                => __( 'Parent Platform', 'cloud-gaming-availability' ),
			'parent_item_colon'          => __( 'Parent Platform:', 'cloud-gaming-availability' ),
			'new_item_name'              => __( 'New Platform Name', 'cloud-gaming-availability' ),
			'add_new_item'               => __( 'Add New Platform', 'cloud-gaming-availability' ),
			'edit_item'                  => __( 'Edit Platform', 'cloud-gaming-availability' ),
			'update_item'                => __( 'Update Platform', 'cloud-gaming-availability' ),
			'view_item'                  => __( 'View Platform', 'cloud-gaming-availability' ),
			'separate_items_with_commas' => __( 'Separate platforms with commas', 'cloud-gaming-availability' ),
			'add_or_remove_items'        => __( 'Add or remove platforms', 'cloud-gaming-availability' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'cloud-gaming-availability' ),
			'popular_items'              => __( 'Popular Platforms', 'cloud-gaming-availability' ),
			'search_items'               => __( 'Search Platforms', 'cloud-gaming-availability' ),
			'not_found'                  => __( 'Not Found', 'cloud-gaming-availability' ),
			'no_terms'                   => __( 'No platforms', 'cloud-gaming-availability' ),
			'items_list'                 => __( 'Platforms list', 'cloud-gaming-availability' ),
			'items_list_navigation'      => __( 'Platforms list navigation', 'cloud-gaming-availability' ),
		);

		$tax_args = array(
			'labels'            => $tax_labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rest_base'         => 'cloud-platforms',
		);

		register_taxonomy( 'cloud_platform', array( 'cloud_games' ), $tax_args );
	}
}
