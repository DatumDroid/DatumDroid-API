<?php

/**
 * Wrapper class around the Guardian Search API for PHP
 *
 * http://explorer.content.guardianapis.com/
 *
 * @package DatumDroid_API
 * @subpackage Services
 */

// Exit if accessed directly
if ( !defined( 'DD_DIR' ) ) exit;

class DD_Service_Guardian extends DD_Search_Service {

	/**
	 * API Version that we're requesting
	 * @var int
	 */
	var $api_ver = 1;

	/**
	 * API Key for the service
	 * @var string
	 */
	var $api_key = 'cufazf7k84v6a2cazkujwcpc';

	/**
	 * Build and perform the query, return the results.
	 * @param $reset_query boolean optional.
	 * @return object
	 */
	function results( $reset_query = true ) {
		$request  = 'http://content.guardianapis.com/search?format=' . $this->type . '&api_key=' . $this->api_key;
		$request .= '&q=' . urlencode( $this->query );
		$request .= '&order-by=newest';

		if ( isset( $this->rpp ) ) {
			$request .= '&page-size=' . $this->rpp;
		}

		if ( isset( $this->page ) ) {
			$request .= '&page=' . $this->page;
		}

		if ( isset( $this->since ) ) {
			$request .= '&from-date=' . date( 'Y-m-d', strtotime( $this->since ) );
		}

		if ( $reset_query ) {
			$this->query = '';
		}

		return $this->objectify( $this->process( $request ) )->response->results;
	}

}

?>
