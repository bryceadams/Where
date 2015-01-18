<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Custom meta

class Where_Meta {

	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );

	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {

        $post_types = array( 'where_location' );     //limit meta box to certain post types

        // Add main number metabox
        if ( in_array( $post_type, $post_types )) {
			add_meta_box(
				'where_location_meta',
				__( 'Location Info', 'where' ),
				array( $this, 'render_location_content' ),
				$post_type,
				'advanced',
				'high'
			);
        }

	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['where_inner_custom_box_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['where_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'where_inner_custom_box' ) ) {
			return $post_id;
		}

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		if ( ! empty( $_POST['where_location_formatted_address'] ) ) {
			update_post_meta( $post_id, 'geolocated', 1 );
			update_post_meta( $post_id, 'geolocated_client_side', 1 );
			update_post_meta( $post_id, 'geolocation_formatted_address', sanitize_text_field( $_POST['where_location_formatted_address'] ) );
			update_post_meta( $post_id, 'geolocation_lat', sanitize_text_field( $_POST['where_location_lat'] ) );
			update_post_meta( $post_id, 'geolocation_long', sanitize_text_field( $_POST['where_location_long'] ) );
			update_post_meta( $post_id, 'geolocation_street', sanitize_text_field( $_POST['where_location_street'] ) );
			update_post_meta( $post_id, 'geolocation_city', sanitize_text_field( $_POST['where_location_city'] ) );
			update_post_meta( $post_id, 'geolocation_state_short', sanitize_text_field( $_POST['where_location_state_short'] ) );
			update_post_meta( $post_id, 'geolocation_state_long', sanitize_text_field( $_POST['where_location_state_long'] ) );
			update_post_meta( $post_id, 'geolocation_postcode', sanitize_text_field( $_POST['where_location_postcode'] ) );
			update_post_meta( $post_id, 'geolocation_country_short', sanitize_text_field( $_POST['where_location_country_short'] ) );
			update_post_meta( $post_id, 'geolocation_country_long', sanitize_text_field( $_POST['where_location_country_long'] ) );
		}

	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_location_content( $post ) {
	
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'where_inner_custom_box', 'where_inner_custom_box_nonce' );

		include( 'views/location-meta.php' );

	}

}

// Bootstrap
function call_Where_Meta() {
	new Where_Meta();
}

if ( is_admin() ) {
    add_action( 'load-post.php', 'call_Where_Meta' );
    add_action( 'load-post-new.php', 'call_Where_Meta' );
}