(function( $ ) {
	$.fn.where_map_field = function() {
		return this.each(function() {
			var field             = $( this );
			var canvas_wrapper    = field.find( '.map-canvas-wrapper' );
			var canvas            = field.find( '.map-canvas' )[0];
			var geocoder          = null;
			var map               = null;
			var marker            = null;
			var map_field_handler = {
				map_options: {
					streetViewControl: false
				},
				init: function() {
					var center = new google.maps.LatLng( 39, -50 );
					var zoom   = 2;

					this.map_options.center = center;
					this.map_options.zoom   = zoom;

					map                     = new google.maps.Map( canvas, this.map_options );
					marker                  = new google.maps.Marker({
						map: map,
						draggable: true,
						animation: google.maps.Animation.DROP,
						title: 'Location'
					});
					geocoder = new google.maps.Geocoder();

					google.maps.event.addListener( map, 'click', function( event ) {
						map_field_handler.reverse_geocode( event.latLng, true );
					});
					google.maps.event.addListener( marker, "dragend", function( event ) {
						map_field_handler.reverse_geocode( event.latLng, true );
					});

					field.on( 'input', '.input-text', this.location_field_input );
					field.on( 'change', '.input-text', this.location_field_change );
					field.change();
				},
				hide_map: function() {
					$( canvas_wrapper ).hide();
					field.find( '.description' ).show();
				},
				show_map: function() {
					$( canvas_wrapper ).show();
					field.find( '.description' ).hide();
					google.maps.event.trigger( map, 'resize' );
				},
				location_field_change: function( event ) {
					var $this = $(this);
					clearTimeout( $this.data('timer') );
    				map_field_handler.geocode( $this.val() );
				},
				location_field_input: function( event ) {
					var $this = $(this);
					clearTimeout( $this.data('timer') );
    				$this.data('timer', setTimeout(function(){
       					 map_field_handler.geocode( $this.val() );
    				}, 1000 ) );
				},
				place_marker: function( location ) {
					marker.setPosition( location );
					map.setCenter(location );
				},
				geocode: function( address ) {
					if ( ! address ) {
						map_field_handler.hide_map();
						$( canvas_wrapper ).find( ':input' ).val( '' );
					}
					geocoder.geocode({ "address": address }, function( results, status ) {
						if ( status == google.maps.GeocoderStatus.OK ) {
							map_field_handler.place_marker( results[0].geometry.location );
							map_field_handler.reverse_geocode( results[0].geometry.location, false );
							map_field_handler.show_map();
						} else {
							$( canvas_wrapper ).find( ':input' ).val( '' );
						}
					});
				},
				reverse_geocode: function( location, update_location_field ) {
					geocoder.geocode({ "latLng": location }, function( results, status ) {
						if ( status == google.maps.GeocoderStatus.OK ) {
							map_field_handler.place_marker( location );

							var street,
								city,
								state_short,
								state_long,
								postcode,
								country_short,
								country_long;

							var address_components = results[0].address_components;
							var components         = {};

							$.each( address_components, function( k,v1 ) {
								$.each( v1.types, function( k2, v2 ) {
									components[ v2 ] = v1.long_name;
									if ( v1.short_name ) {
										components[ v2 + '_short' ] = v1.short_name;
									}
								});
							});

							var formatted_address = results[0].formatted_address;

							$.each( components, function( key, value ) {
								switch( key ) {
									case "street_number" :
										street = value;
										break;
									case "route" :
										if ( street ) {
											street = street + ' ' + value;
										} else {
											street = value;
										}
										break;
									case "sublocality_level_1" :
									case "locality" :
									case "postal_town" :
										city = value;
										break;
									case "administrative_area_level_1_short" :
									case "administrative_area_level_2_short" :
										state_short = value;
										break;
									case "administrative_area_level_1" :
									case "administrative_area_level_2" :
										state_long = value;
										break;
									case "postal_code" :
										postcode = value;
										break;
									case "country_short" :
										country_short = value;
										break;
									case "country" :
										country_long = value;
										break;
								}
							});

							field.find('.where_location_formatted_address').val( formatted_address );
							field.find('.where_location_lat').val( location.lat() );
							field.find('.where_location_long').val( location.lng() );
							field.find('.where_location_street').val( street );
							field.find('.where_location_city').val( city );
							field.find('.where_location_state_short').val( state_short );
							field.find('.where_location_state_long').val( state_long );
							field.find('.where_location_postcode').val( postcode );
							field.find('.where_location_country_short').val( country_short );
							field.find('.where_location_country_long').val( country_long );

							if ( update_location_field ) {
								var location_array = [ city, state_long, country_short == 'GB' ? 'UK' : country_short ];
								var nice_location = location_array.filter(function(v){ return v });
								field.find( '.input-text' ).val( nice_location.join( ', ' ) );
							}

							return true;
						}
					});
				}
			};
			map_field_handler.init();
			return this;
		});
	};

	$( '.where-google-map-field' ).where_map_field();
}( jQuery ));