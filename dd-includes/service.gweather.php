<?php

/**
 * Wrapper class around the Google Weather API for PHP
 *
 * From http://blog.programmableweb.com/2010/02/08/googles-secret-weather-api/
 *
 * @package DatumDroid_API
 * @subpackage Services
 */

// Exit if accessed directly
if ( !defined( 'DD_DIR' ) ) exit;

class DD_Service_Google_Weather extends DD_Search_Service {

	/**
	 * API Type
	 */
	var $type = 'xml';

	/**
	 * The maximum number of results that this service can return
	 *
	 * 2 for this service so that it fails the received results >= max results => more results|next page test
	 *
	 * @var int
	 */
	var $max = '2';

	/**
	 * Function to prepare data for return to client
	 * @access private
	 * @param string $data
	 */
	function objectify( $data ) {
		$obj = simplexml_load_string( $data );

		if ( ! $obj )
			return array();
		else
			$obj = $obj->weather->current_conditions;

		$conditions = array();

		foreach ( (array) $obj as $condition => $val ) {
			if ( !isset( $val['data'] ) ) continue;
			$conditions[$condition] = (array) $val['data'];
			$conditions[$condition] = $conditions[$condition][0];
			$conditions[$condition] = 'icon' == $condition ? 'http://google.com' . $conditions[$condition] : $conditions[$condition];
		}

		return array( (object) $conditions );
	}

	/**
	 * Build and perform the query, return the results.
	 * @param $reset_query boolean optional.
	 * @return object
	 */
	function results( $reset_query = true ) {
		$request  = 'http://www.google.com/ig/api';
		$request .= '?weather=' . urlencode( $this->query );

		if ( $reset_query ) {
			$this->query = '';
		}

		return $this->objectify( $this->process( $request ) );
	}

}

?>
