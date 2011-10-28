<?php

/**
 * Wrapper class around the Feedzilla Search API for PHP
 *
 * From http://code.google.com/p/feedzilla-api/wiki/RestApi
 *
 * @package DatumDroid_API
 * @subpackage Services
 */

// Exit if accessed directly
if ( !defined( 'DD_DIR' ) ) exit;

class DD_Service_Feedzilla extends DD_Search_Service {

	/**
	 * API Version that we're requesting
	 */
	var $api_ver = 1;

	/**
	 * The maximum number of results that this service can return
	 * @var int
	 */
	var $max = '100';

	/**
	 * Get the required number of results
	 *
	 * Feedzilla, at max, can only return 100 results for a search query and it
	 * also doesn't support any page parameters so we've to work accordingly
	 *
	 * You might still not be able to get the correct number of results
	 *
	 * @param bool $reset_query Reset query?
	 * @return array Array of results
	 */
	function search( $reset_query = false ) {
		if ( $this->rpp * $this->page > $this->max )
			return array( "no results found" );

		$this->pre_trim = ( $this->page - 1 ) * $this->rpp;
		$this->rpp      = $this->rpp * $this->page;
		$this->page     = 1;

		return parent::search();
	}

	/**
	 * Build and perform the query, return the results.
	 * @param $reset_query boolean optional.
	 * @return object
	 */
	function results( $reset_query = true ) {
		$request  = 'http://api.feedzilla.com/v' . $this->api_ver . '/articles/search.' . $this->type;
		$request .= '?q=' . urlencode( $this->query );
		$request .= '&order=date&title_only=1&client_source=' . DD_NAME . DD_VER;

		if ( isset( $this->rpp ) ) {
			$request .= '&count=' . $this->rpp;
		}

		/*
		if ( isset( $this->page ) ) {
			$request .= '&page=' . $this->page;
		}
		*/

		if ( isset( $this->lang ) ) {
			//$request .= '&culture_code=' . $this->lang . '_us';
			$request .= '&culture_code=en_us';
		}

		if ( isset( $this->since ) ) {
			$request .= '&from-date=' . date( 'Y-m-d', strtotime( $this->since ) );
		}

		if ( $reset_query ) {
			$this->query = '';
		}

		$results = $this->objectify( $this->process( $request ) );

		return !empty( $results->articles ) ? $results->articles : false;
	}

}

?>
