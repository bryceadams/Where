<?php
/**
 * Plugin Name: Where?
 * Plugin URI: http://bryce.se/
 * Description: Show off where you are and keep a log of past locations
 * Author: Bryce Adams
 * Author URI: http://bryce.se/
 * Version: 1.0.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Where {

    function __construct() {
 
 		// Define constants
		define( 'WHERE_VERSION', '1.0.0' );
		define( 'WHERE_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'WHERE_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		define( 'WHERE_TZDB_API_KEY', 'YIKTTVO8KDYA' );

		// Includes
		include( 'includes/class-where-types.php' );
		include( 'includes/class-where-meta.php' );
		include( 'includes/class-where-shortcode.php' );

		// Actions
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

    }

    /**
	 * Localisation
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'where' );

		load_textdomain( 'where', WP_LANG_DIR . "/where/where-$locale.mo" );
		load_plugin_textdomain( 'where', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

    /**
	 * Load functions
	 */
	public function include_template_functions() {
		include( 'where-functions.php' );
		include( 'where-template.php' );
	}

	/**
	 * Frontend Assets
	 */
	public function frontend_assets() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'where-frontend', WHERE_PLUGIN_URL . '/assets/css/frontend.css' );
	}

	/**
	 * Admin Assets
	 */
	public function admin_assets() {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'google-maps-api', 'https://maps.googleapis.com/maps/api/js?v=3.exp', array( 'jquery' ), '3', true );
		wp_register_script( 'where-google-map-field', WHERE_PLUGIN_URL . '/assets/js/admin-map.js', array( 'google-maps-api', 'jquery' ), WHERE_VERSION, true );
		wp_enqueue_style( 'where-google-map-field-admin', WHERE_PLUGIN_URL . '/assets/css/admin.css' );
	}

}

$GLOBALS['where'] = new Where();