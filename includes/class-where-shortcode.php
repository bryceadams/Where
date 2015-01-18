<?php
/**
 * ClassDescription
 *
 * @package  PackageName
 * @author   Bryce Adams <bryce@bryce.se>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Where_Shortcode {

    function __construct() {
 
 		add_shortcode( 'where_now', array( $this, 'where_now' ) );
 		add_shortcode( 'where_all', array( $this, 'where_all' ) );

    }

    /**
     * Shortcode for 'where now' (current location)
     */
    public function where_now() {

    	$args = array(
    		'post_type'			=> 'where_location',
    		'posts_per_page'	=> 1,
    	);

    	$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {
			
			while ( $the_query->have_posts() ) : $the_query->the_post();

		    	$post_id 	= get_the_ID();

		    	$lat 		= get_post_meta( $post_id, 'geolocation_lat' );
		    	$long 		= get_post_meta( $post_id, 'geolocation_long' );
		    	$country	= get_post_meta( $post_id, 'geolocation_country_long' );
		    	$iso		= get_post_meta( $post_id, 'geolocation_country_short' ); ?>

		    	<div class="where-currently">
		    		<h3>I'm Currently In</h3>
		    		<h2><?php echo get_the_title(); ?></h2>
		    	</div>

		    	<div class="where-time">
					<?php
					$timezone 		= where_get_nearest_timezone( $lat[0], $lon[0], $iso[0] );
					$time_get		= wp_remote_get( 'http://api.timezonedb.com/?zone=' . $timezone . '&format=json&key=' . WHERE_TZDB_API_KEY );
					$time_body 		= json_decode( wp_remote_retrieve_body( $time_get ) );
					$time_os_raw	= $time_body->gmtOffset / 60 / 60; // (hours)
					$time_offset	= ( $time_os_raw > 0 ) ? '+' . $time_os_raw : $time_os_raw;
					$time 			= $time_body->timestamp;
					?>
					<h4><?php echo date( get_option( 'time_format' ), $time ); ?> <span class="offset">(UTC <?php echo $time_offset; ?>)</span></h4>
				</div>

		    	<div id="map_canvas"></div>

				<script type="text/javascript">
				function initialize() {
					var myLatlng = new google.maps.LatLng(<?php echo $lat[0]; ?>, <?php echo $long[0]; ?>)
				  var mapOptions = {
				    zoom: 8,
				    center: myLatlng,
				    mapTypeId: google.maps.MapTypeId.ROADMAP,
				    styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#a2daf2"}]},{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"color":"#f7f1df"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#d0e3b4"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#bde6ab"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"geometry","stylers":[{"color":"#fbd3da"}]},{"featureType":"poi.business","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffe15f"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#efd151"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"black"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"color":"#cfb2db"}]}]
				  }
				  var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
				  var marker = new google.maps.Marker({
				      position: myLatlng,
				      map: map,
				      title: 'Hello World!'
				  });
				}

				 function loadScript() {
				  var script = document.createElement("script");
				  script.type = "text/javascript";
				  script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize";
				  document.body.appendChild(script);
				}

				window.onload = function(){loadScript();};

				</script>

			<?php endwhile;

		} else {
			// no posts found
		}

		/* Restore original Post Data */
		wp_reset_postdata();

    }

    /**
     * Shortcode for 'where all' (all past locations)
     */
    public function where_all() {

    	if ( ! where_all_can_see() ) {
    		return;
    	}

    	$args = array(
    		'post_type'			=> 'where_location',
    		'posts_per_page'	=> -1,
    	);

    	$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) { ?>

			<div class="where-been">
				<h2>Where I've Been</h2>
			</div>

			<table class="where-locations">
				<tr>
					<th>From</th>
					<th>Until</th>
					<th>Place</th>
				</tr>
			
			<?php while ( $the_query->have_posts() ) : $the_query->the_post();

		    	$post_id 	= get_the_ID();

		    	$lat 		= get_post_meta( $post_id, 'geolocation_lat' );
		    	$long 		= get_post_meta( $post_id, 'geolocation_long' );
		    	$country	= get_post_meta( $post_id, 'geolocation_country_long' );
		    	$place 		= get_post_meta( $post_id, 'geolocation_formatted_address' );

		    	$previous 	= ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
				$next     	= get_adjacent_post( false, '', false );
				$next_date	= $next ? date( get_option( 'date_format' ), strtotime( $next->post_date ) ) : 'Who Knows?'; ?>

		    	<tr>
		    		<td><?php echo get_the_date( get_option( 'date_format' ), $post_id ); ?></td>
		    		<td><?php echo $next_date; ?></td>
		    		<td class="location"><?php echo get_the_title(); ?> <a href="https://www.google.com.au/maps/search/<?php echo esc_attr( $place[0] ); ?>/@<?php echo $lat[0] . ',' . $long[0]; ?>,4z" target="_blank"><span class="dashicons dashicons-location"></span></a></td>
		    	</tr>		    	

			<?php endwhile; ?>

			</table>

		<?php } else {

			_e( 'I haven\'t been anywhere yet!', 'where' );

		}

		/* Restore original Post Data */
		wp_reset_postdata();

    }

}

new Where_Shortcode;