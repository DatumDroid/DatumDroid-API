<?php
/**
 * @package DatumDroid_API
 * @subpackage Request
 */

/**
 * This script takes the search keywords and returns appropriate results in JSON
 * format.
 *
 * @todo Refine search query
 * @todo Refine results
 * @todo Simpler YouTube
 */

/**
 * To add a new service:
 *
 *  1. Add service.[name].php in DD_DIR_INC like service.twitter.php
 *  2. Add DD_Search->[name]() in DD_DIR_INC/class.search.php like
 *      DD_Search->twitter()
 *  3. Done
 */

/** Debug *********************************************************************/

// Debug
define( 'DD_DEBUG', isset( $_REQUEST['debug'] ) ? true : false );

// Report all errors if debugging, else none
error_reporting( E_ALL );

ini_set( 'display_errors', true == DD_DEBUG ? 1 : 0 );

/** Definitions ***************************************************************/

/* Directories */

// Root Path
define( 'DD_DIR',          dirname( __FILE__ )        . '/' );

// Includes Directory
define( 'DD_DIR_INC',      DD_DIR     . 'dd-includes' . '/' );

/* Misc */

// Version
define( 'DD_VER',        '1.0'                                             );

// Name
define( 'DD_NAME',       'DatumDroid'                                      );

// User Agent
define( 'DD_USER_AGENT', DD_NAME . 'API/' . DD_VER . ':api@datumdroid.com' );

/* URIs */

// Url Path to this API
define( 'DD_URI', 'http://api.datumdroid.com/' . DD_VER . '/' );

/** Actions *******************************************************************/

// Set the default timezone to India/Delhi (+5.5)
date_default_timezone_set( 'Asia/Calcutta' );

/** Variables *****************************************************************/

// Set max results
$dd_per_page = !empty( $_REQUEST['per_page'] ) ? intval( $_REQUEST['per_page'] ) : 10;
$dd_per_page = ( $dd_per_page < 1 || $dd_per_page > 50 ) ? 10 : $dd_per_page;

// Page number
$dd_page = !empty( $_REQUEST['page'] ) ? intval( $_REQUEST['page'] ) : 1;
$dd_page = $dd_page < 1 ? 1 : $dd_page;

// Language
$dd_lang = !empty( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : 'en';

// Results array, which would be outputted as json encoded later
$results = array();

/** Include required files ****************************************************/

// Core functions
require_once( DD_DIR_INC . 'functions.core.php' );

// Service wrapper class
require_once( DD_DIR_INC . 'class.service.php'  );

// Search class
require_once( DD_DIR_INC . 'class.search.php'   );

/** Search as required & print ************************************************/

// Check for search keywords
if ( !isset( $_REQUEST['q'] ) || !$dd_query = trim( $_REQUEST['q'] ) )
	dd_output( array( 'responseDetails' => "missing query parameter 'q'", 'responseStatus' => 400 ) );

// Initiate the class
$dd_search = new DD_Search();

// Get the services in the DD_Search class and check if they are required
// If yes, add their results to the search array
foreach ( get_class_methods( 'DD_Search' ) as $service ) {
	if ( ( !empty( $_REQUEST['all'] ) && $_REQUEST['all'] == 1 ) || ( isset( $_REQUEST[$service] ) && $_REQUEST[$service] == 1 ) )
		$results[$service] = $dd_search->{$service}();
}

dd_output( $results );

// Gaut.am was here

?>
