<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Types / Taxonomies
class Where_Types {

	protected static $instance = null;

    function __construct() {
 
 		// Hook into the 'init' action
		add_action( 'init', array( $this, 'post_type' ), 6 );

    }

	/**
	 * Start the Class when called
	 */

	public static function get_instance() {
	  // If the single instance hasn't been set, set it now.
	  if ( null == self::$instance ) {
		self::$instance = new self;
	  }
	  return self::$instance;
	}

    // Register Custom Post Type
	public function post_type() {

		$labels = array(
			'name'                => _x( 'Locations', 'Post Type General Name', 'where' ),
			'singular_name'       => _x( 'Location', 'Post Type Singular Name', 'where' ),
			'menu_name'           => __( 'Locations', 'where' ),
			'parent_item_colon'   => __( 'Parent Location:', 'where' ),
			'all_items'           => __( 'All Locations', 'where' ),
			'view_item'           => __( 'View Location', 'where' ),
			'add_new_item'        => __( 'Add New Location', 'where' ),
			'add_new'             => __( 'Add New', 'where' ),
			'edit_item'           => __( 'Edit Location', 'where' ),
			'update_item'         => __( 'Update Location', 'where' ),
			'search_items'        => __( 'Search Location', 'where' ),
			'not_found'           => __( 'Not found', 'where' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'where' ),
		);
		$args = array(
			'label'               => __( 'Location', 'where' ),
			'description'         => __( 'Where I been... Where I going?', 'where' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'thumbnail' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'			  => 'dashicons-location-alt',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
		register_post_type( 'where_location', $args );

	}

}

new Where_Types();