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
 * Min - 1
 * Max - {@see DD_MAX_PER_PAGE}
 *
 * @param string $service The service requested. If empty, the all are returned
 * @global int $dd_per_page Per page option
 * @return int Per page Option
 */
function dd_get_per_page( $service = '' ) {
	global $dd_per_page;

	$service = dd_get_service( $service );

	$per_page = ( empty( $_REQUEST[$service] ) || ( $_REQUEST[$service] == 1 && isset( $dd_per_page ) ) ) ? $dd_per_page : $_REQUEST[$service];

	if ( $per_page < 1 )
		return 1;
	elseif ( $per_page > DD_MAX_PER_PAGE )
		return DD_MAX_PER_PAGE;
	else
		return $per_page;
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

/**
 * Returns the api key for the requested service
 *
 * @param string $service The service requested. If empty, the all are returned
 * @global string $dd_api_keys API Keys
 * @return array|string API Key(s) (for the requested service)
 */
function dd_get_api_key( $service = '' ) {
	global $dd_api_keys;

	if ( empty( $service ) )
		return $dd_api_keys;

	return !empty( $dd_api_keys[$service] ) ? $dd_api_keys[$service] : '';
}

/** Loaders & setters *********************************************************/

/**
 * Gets the current service
 *
 * @param string $service Service name
 * @global string $dd_current_service Current service
 * @return string Current service
 */
function dd_get_service( $service = '' ) {
	global $dd_current_service, $dd_services;

	if ( array_key_exists( $service, $dd_services ) )
		return $service;
	else
		return $dd_current_service;
}

/**
 * Set the current service global to the sent parameter
 *
 * @param string $service Service name
 * @global string $dd_current_service Current service
 * @global array $dd_services Registered services
 */
function dd_set_service( $service = '' ) {
	global $dd_current_service, $dd_services;

	if ( array_key_exists( $service, $dd_services ) )
		$dd_current_service = $service;
	else
		dd_reset_service();
}

/**
 * Reset the current service global to empty string
 *
 * @global string $dd_current_service Current service
 */
function dd_reset_service() {
	global $dd_current_service;

	$dd_current_service = '';
}

/**
 * Load the class file for a service
 *
 * @param string $service Service name
 */
function dd_load_service( $service = '' ) {
	require_once( DD_DIR_INC . 'service.' .  dd_get_service( $service ) . '.php' );
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

/** Search & Output ***********************************************************/

/**
 * Perform the search
 *
 * @return array An array of results, keys are services, values are results
 */
function dd_search() {
	global $dd_services;

	// Results array, which would be outputted as json encoded later
	$dd_results = array();

	// Get the services in the $dd_services array and check if they are required
	// If yes, add their results to the results array
	foreach ( (array) $dd_services as $service => $service_name ) {
		if ( ( !empty( $_REQUEST['all'] ) && $_REQUEST['all'] == 1 ) || isset( $_REQUEST[$service] ) ) {
			// Require the search results fetcher file
			dd_set_service( $service );
			dd_load_service();

			$service_class = 'DD_Service_' . $service_name;

			$search = new $service_class();

			$dd_results[dd_get_service()] = $search->search();
		}
	}

	return $dd_results;
}

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
