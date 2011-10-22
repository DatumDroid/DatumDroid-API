<?php

/**
 * Wrapper class around the Google Stocks API for PHP
 *
 * From http://blog.programmableweb.com/2010/02/08/googles-secret-weather-api/
 * http://www.google.com/ig/api?stock=YHOO
 *
 * @package DatumDroid_API
 * @subpackage Services
 */

// Exit if accessed directly
if ( !defined( 'DD_DIR' ) ) exit;

class DD_Service_Google_Stocks extends DD_Search_Service {

	/**
	 * API Type
	 */
	var $type = 'xml';

	/**
	 * Function to prepare data for return to client
	 * @access private
	 * @param string $data
	 */
	function objectify( $data ) {
		$obj = simplexml_load_string( $data )->finance;

		$conditions = array();

		foreach ( (array) $obj as $condition => $val ) {
			if ( !isset( $val['data'] ) ) continue;
			$conditions[$condition] = (array) $val['data'];
			$conditions[$condition] = $conditions[$condition][0];
			$conditions[$condition] = in_array( $condition, array( 'symbol_lookup_url', 'symbol_url', 'chart_url', 'disclaimer_url' ) ) ? 'http://google.com' . $conditions[$condition] : $conditions[$condition];
		}

		return (object) $conditions;
	}

	/**
	 * Build and perform the query, return the results.
	 * @param $reset_query boolean optional.
	 * @return object
	 */
	function results( $reset_query = true ) {
		$request  = 'http://www.google.com/ig/api';
		$request .= '?stock=' . urlencode( $this->query );

		if ( $reset_query ) {
			$this->query = '';
		}

		return $this->objectify( $this->process( $request ) );
	}

}

?>
