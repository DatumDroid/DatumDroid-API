<?php

/**
 * @package DatumDroid_API
 * @subpackage Core Functions
 */

// Exit if accessed directly
if ( !defined( 'DD_DIR' ) ) exit;

/** _get functions ************************************************************/

/**
 * Returns the search query
 *
 * @global string $dd_query Search Query
 * @return string Search Query
 */
function dd_get_query() {
	global $dd_query;

	return $dd_query;
}

/**
 * Returns the per page option
 *
 * @global int $dd_per_page Per page option
 * @return int Per page Option
 */
function dd_get_per_page() {
	global $dd_per_page;

	return $dd_per_page;
}

/**
 * Returns the current page number
 *
 * @global int $dd_page Current page number
 * @return int Current page number
 */
function dd_get_page() {
	global $dd_page;

	return $dd_page;
}

/**
 * Returns the language (like en)
 *
 * @global string $dd_lang Current language
 * @return string Language
 */
function dd_get_lang() {
	global $dd_lang;

	return $dd_lang;
}

/** Loaders *******************************************************************/

function dd_load_service( $service = '' ) {
	require_once( DD_DIR_INC . 'service.' .  $service . '.php' );
}

/** Formatting/misc ***********************************************************/

/**
 * Returns the url query as associative array
 *
 * @param string $url URL
 * @return array Associative array of query
 */
function dd_convert_url_query( $url = '' ) {
	$query      = parse_url( $url, PHP_URL_QUERY );
	$query      = html_entity_decode( $query );
	$queryParts = explode( '&', $query );
	$params     = array();

	foreach ( $queryParts as $param ) {
		$item             = explode( '=', $param );
		$params[$item[0]] = isset( $item[1] ) ? $item[1] : null;
	}

	return $params;
}

/**
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout WordPress to allow for both string or array
 * to be merged into another array.
 *
 * @param string|array $args Value to merge with $defaults
 * @param array $defaults Array that serves as the defaults.
 * @return array Merged user defined values with defaults.
 */
function dd_parse_args( $args, $defaults = '' ) {
	if ( is_object( $args ) ) {
		$r = get_object_vars( $args );
	} elseif ( is_array( $args ) ) {
		$r =& $args;
	} else {
		//wp_parse_str( $args, $r );
		// Parses a string into variables to be stored in an array.
		parse_str( $args, $r );
		if ( get_magic_quotes_gpc() )
			$r = stripslashes_deep( $r );
	}

	if ( is_array( $defaults ) )
		return array_merge( $defaults, $r );

	return $r;
}

/** Output ********************************************************************/

/**
 * Output the results.
 *
 * If DD_DEBUG is true, output is just a print_r of the array.
 * Otherwise it is outputted after being json_encoded and proper headers being
 * sent.
 *
 * @param array $results The results to be outputted
 */
function dd_output( $results = array() ) {
	// If none has been requested, add an error message
	if ( empty( $results ) )
		$results = array( 'responseDetails' => 'no service requested', 'responseStatus' => 400 );

	if ( true == DD_DEBUG ) {
		// If we're in debug mode, print in human-readable form
		echo '<pre>'; print_r( $results ); echo '</pre>';
	} else {
		// Set the correct MIME type for JSON.
		header( 'Content-type: application/json' );

		echo json_encode( $results );
	}

	exit;
}

?>
