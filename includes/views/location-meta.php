<?php
	global $post;

	$localize = array(
		'long' => get_post_meta( $post->ID, 'geolocation_long' ),
		'lat'  => get_post_meta( $post->ID, 'geolocation_lat' ),
	);
	wp_localize_script( 'where-google-map-field', 'where_map', $localize );
	wp_enqueue_script( 'where-google-map-field' );

	$value = get_post_meta( $post->ID, 'geolocation_formatted_address' );
?>

<div class="where-google-map-field">
	<input
		type="text"
		class="input-text"
		name="where_location"
		size="85"
		style="max-width: 100%"
		id="where_location"
		placeholder="Location (eg. Korea)"
		value="<?php echo $value[0]; ?>"
		/>
	<?php if ( ! empty( $field['description'] ) ) : ?><small class="description"><?php echo $field['description']; ?></small><?php endif; ?>

	<div class="map-canvas-wrapper">
		<div class="map-canvas"></div>
		<input type="hidden" name="where_location_formatted_address" class="where_location_formatted_address" />
		<input type="hidden" name="where_location_lat" class="where_location_lat" />
		<input type="hidden" name="where_location_long" class="where_location_long" />
		<input type="hidden" name="where_location_street" class="where_location_street" />
		<input type="hidden" name="where_location_city" class="where_location_city" />
		<input type="hidden" name="where_location_state_short" class="where_location_state_short" />
		<input type="hidden" name="where_location_state_long" class="where_location_state_long" />
		<input type="hidden" name="where_location_postcode" class="where_location_postcode" />
		<input type="hidden" name="where_location_country_short" class="where_location_country_short" />
		<input type="hidden" name="where_location_country_long" class="where_location_country_long" />
	</div>
</div>