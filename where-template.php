<?php
/**
 * Template Functions
 *
 * Template functions specifically created for Where?
 *
 * @author 		Bryce Adams (Mike Jolley)
 * @category 	Core
 * @package 	Where?
 * @version     1.0.0
 */

/**
 * Get and include template files.
 *
 * @param mixed $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function get_where_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array($args) )
		extract( $args );

	include( locate_where_template( $template_name, $template_path, $default_path ) );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @param mixed $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function locate_where_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path )
		$template_path = 'where';
	if ( ! $default_path )
		$default_path = WHERE_PLUGIN_DIR . '/templates/';

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template )
		$template = $default_path . $template_name;

	// Return what we found
	return apply_filters( 'where_locate_template', $template, $template_name, $template_path );
}

/**
 * Get template part (for templates in loops).
 *
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function get_where_template_part( $slug, $name = '', $template_path = '', $default_path = '' ) {
	if ( ! $template_path )
		$template_path = 'where';
	if ( ! $default_path )
		$default_path = WHERE_PLUGIN_DIR . '/templates/';

	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/where/slug-name.php
	if ( $name )
		$template = locate_template( array ( "{$slug}-{$name}.php", "{$template_path}/{$slug}-{$name}.php" ) );

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( $default_path . "{$slug}-{$name}.php" ) )
		$template = $default_path . "{$slug}-{$name}.php";

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/where/slug.php
	if ( ! $template )
		$template = locate_template( array( "{$slug}.php", "{$template_path}/{$slug}.php" ) );

	if ( $template )
		load_template( $template, false );
}
