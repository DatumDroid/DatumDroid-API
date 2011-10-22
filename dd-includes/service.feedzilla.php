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
	 * Build and perform the query, return the results.
	 * @param $reset_query boolean optional.
	 * @return object
	 */
	function results( $reset_query = true ) {
		$request  = 'http://api.feedzilla.com/v' . $this->api_ver . '/articles/search.' . $this->type;
		$request .= '?q=' . urlencode( $this->query );
		$request .= '&order=date&title_only=1';

		if ( isset( $this->rpp ) ) {
			$request .= '&count=' . $this->rpp;
		}

		if ( isset( $this->page ) ) {
			$request .= '&page=' . $this->page;
		}

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

		return $this->objectify( $this->process( $request ) )->articles;
	}

}

?>
