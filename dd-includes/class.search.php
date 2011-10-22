<?php

/**
 * @package DatumDroid_API
 * @subpackage Search
 */

// Exit if accessed directly
if ( !defined( 'DD_DIR' ) ) exit;

class DD_Search {

	/**
	 * Perform a Google Image Search
	 */
	function gimages() {
		// Require the search results fetcher file
		dd_load_service( 'gimages' );

		$gi = new DD_Service_Google_Images();

		//if ( true == DD_DEBUG )
		//	return $gi->search_experimental();

		return $gi->results();
	}

	/**
	 * Perform a Google Image Search
	 */
	function gweather() {
		// Require the search results fetcher file
		dd_load_service( 'gweather' );

		$gw = new DD_Service_Google_Weather();

		return $gw->results();
	}

	/**
	 * Perform a Guardian Search
	 */
	function gstocks() {
		// Load the Twitter Class
		dd_load_service( 'gstocks' );

		$gs = new DD_Service_Google_Stocks();

		return $gs->results();
	}

	/**
	 * Perform a YouTube Search
	 */
	function youtube() {
		// Load the YouTube class
		dd_load_service( 'youtube' );

		$yt = new DD_Service_YouTube();

		return $yt->results();
	}

	/**
	 * Perform a Twitter Search
	 */
	function twitter() {
		// Load the Twitter Class
		dd_load_service( 'twitter' );

		$tw = new DD_Service_Twitter();

		//$tw->contains( 'news' );

		return $tw->results();
	}

	/**
	 * Perform a Feedzilla Search
	 */
	function feedzilla() {
		// Load the Twitter Class
		dd_load_service( 'feedzilla' );

		$fz = new DD_Service_Feedzilla();

		return $fz->results();
	}

	/**
	 * Perform a Guardian Search
	 */
	function guardian() {
		// Load the Twitter Class
		dd_load_service( 'guardian' );

		$gd = new DD_Service_Guardian();

		return $gd->results();
	}

}

?>
