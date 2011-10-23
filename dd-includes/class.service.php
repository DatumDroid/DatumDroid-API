<?php

/**
 * Search wrapper class
 *
 * @package DatumDroid_API
 * @subpackage Services
 */

// Exit if accessed directly
if ( !defined( 'DD_DIR' ) ) exit;

class DD_Search_Service {

	/**
	 * Can be set to JSON (requires PHP 5.2 or the json pecl module) or XML or atom
	 * @var string
	 */
	var $type = 'json';

	/**
	 * @var string
	 */
	var $headers = array();

	/**
	 * @var string
	 */
	var $user_agent = '';

	/**
	 * @var string
	 */
	var $query = '';

	/**
	 * @var array
	 */
	var $responseInfo = array();

	/**
	 * Use an ISO language code. en, de...
	 * @var string
	 */
	var $lang;

	/**
	 * The number of results to return per page
	 * @var int
	 */
	var $rpp;

	/**
	 * The maximum number of results that this service can return
	 * @var int
	 */
	var $max = '10';

	/**
	 * The page number to return, up to a max of roughly 1500 results
	 * @var int
	 */
	var $page;

	/**
	 * Return tweets with a status id greater than the since value
	 * @var int
	 */
	var $since;

	/**
	 * Returns tweets by users located within a given radius of the given latitude/longitude, where the user's location is taken from their Twitter profile. The parameter value is specified by "latitide,longitude,radius", where radius units must be specified as either "mi" (miles) or "km" (kilometers)
	 * @var string
	 */
	var $geocode;

	/**
	 * Construct the class
	 *
	 * @param mixed $args Request arguments. Here is the list of support args:
	 *  - rpp: Results per page. Default the one sent in $_GET or 10
	 *  - page: Current page. Default the one sent in $_GET or 1
	 *  - lang: Language. Default the one sent in $_GET or en
	 *  - query: Search query. Default the one sent in $_GET
	 */
	function DD_Search_Service( $args = array() ) {
		$defaults = array(
			'page'     => dd_get_page    (),
			'lang'     => dd_get_lang    (),
			'query'    => dd_get_query   (),
			'per_page' => dd_get_per_page()
		);
		$r = dd_parse_args( $args, $defaults );
		extract ( $r );

		$this->rpp        = $per_page;
		$this->page       = $page;
		$this->lang       = $lang;
		$this->query      = $query;
		$this->user_agent = DD_USER_AGENT;
	}

	/**
	 * Find tweets containing a word
	 * @param string $user required
	 * @return object
	 */
	function contains( $word ) {
		$this->query .= ' ' . $word;
		return $this;
	}

	/**
	 * @param int $since_id required
	 * @return object
	 */
	function since( $since_id ) {
		$this->since = $since_id;
		return $this;
	}

	/**
	 * @param int $language required
	 * @return object
	 */
	function lang( $language ) {
		$this->lang = $language;
		return $this;
	}

	/**
	 * @param int $n required
	 * @return object
	 */
	function rpp( $n ) {
		$this->rpp = $n;
		return $this;
	}

	/**
	 * @param int $n required
	 * @return object
	 */
	function page( $n ) {
		$this->page = $n;
		return $this;
	}

	/**
	 * @param float $lat required. lattitude
	 * @param float $long required. longitude
	 * @param int $radius required.
	 * @param string optional. mi|km
	 * @return object
	 */
	function geocode( $lat, $long, $radius, $units = 'mi' ) {
		$this->geocode = $lat . ',' . $long . ',' . $radius . $units;
		return $this;
	}

	/**
	 * Get the required number of results
	 * @param string $url Required. API URL to request
	 * @param string $postargs Optional. Urlencoded query string to append to the $url
	 */
	function search( $reset_query = false ) {
		// For the first page
		$results = $this->results( $reset_query );

		// No results
		if ( empty( $results ) )
			return array( "no results found" );

		// No more results or no more requested
		//if ( count( $results ) <= min( $this->max, $this->rpp ) )
		//	return $results;

		return $results;
	}

	/**
	 * Internal function where all the juicy curl fun takes place
	 * this should not be called by anything external unless you are
	 * doing something else completely then knock youself out.
	 * @access private
	 * @param string $url Required. API URL to request
	 * @param string $postargs Optional. Urlencoded query string to append to the $url
	 */
	function process( $url, $postargs = false ) {
		$ch = curl_init( $url );
		if ( $postargs !== false ) {
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postargs );
		}

		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
		//curl_setopt( $ch, CURLOPT_NOBODY,  0 );
		//curl_setopt( $ch, CURLOPT_HEADER,  0 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		//curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->headers );

		//if ( $this->user_agent != false )
		//	curl_setopt( $ch, CURLOPT_USERAGENT, $this->user_agent );

		$response = curl_exec( $ch );

		$this->responseInfo = curl_getinfo( $ch );
		curl_close( $ch );

		if ( intval( $this->responseInfo['http_code'] ) == 200 )
			return $response;
		else
			return false;
	}

	/**
	 * Function to prepare data for return to client
	 * @access private
	 * @param string $data
	 */
	function objectify( $data ) {
		if ( $this->type == 'json' )
			return (object) json_decode( $data );

		// Only for twitter, note statuses var @todo Change
		else if ( $this->type == 'xml' ) {

			if ( function_exists( 'simplexml_load_string' ) ) {
				$obj = simplexml_load_string( $data );

				$statuses = array( );
				foreach ( $obj->status as $status ) {
					$statuses[] = $status;
				}
				return (object) $statuses;
			} else {
				return $out;
			}
		}
		else
			return false;
	}

	/**
	 * A function meant to be overriden
	 *
	 * @param bool $reset_query Reset the query?
	 * @return array Empty array
	 */
	function results( $reset_query = true ) {
		if ( $reset_query ) {
			$this->query = '';
		}

		return array();
	}
}

?>
