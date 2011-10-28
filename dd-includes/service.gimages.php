<?php

/**
 * Wrapper class around the Google Image API for PHP
 *
 * http://code.google.com/apis/imagesearch/v1/jsondevguide.html
 * http://w-shadow.com/blog/2008/02/28/get-google-image-search-results-with-php/
 *
 * @package DatumDroid_API
 * @subpackage Services
 */

// Exit if accessed directly
if ( !defined( 'DD_DIR' ) ) exit;

class DD_Service_Google_Images extends DD_Search_Service {

	/**
	 * Possible values:
	 *  * large - l
	 *  * medium - m
	 *  * icon - i
	 *  * any - '' (null) (default)
	 *
	 * @var string
	 */
	var $image_size = '';

	/**
	 * Possible values: face|photo|clipart|lineart|(null)
	 *
	 * For any, send null or '' (default)
	 *
	 * @var string
	 */
	var $image_type = '';

	/**
	 * The maximum number of results that this service can return
	 * @var int
	 */
	var $max = '21';

	/**
	 * Construct the class
	 *
	 * Also unset the user agent set by the parent class
	 *
	 * @param mixed $args Request arguments
	 */
	function DD_Service_Google_Images( $args = array() ) {
		parent::DD_Search_Service( $args );

		// Do not send any user agent with the cURL call, otherwise different types of results are returned which we can't match
		$this->user_agent = false;
	}

	/**
	 * @param int $size required
	 * @return object
	 */
	function image_size( $size ) {
		$this->image_size = in_array( $size, array( 'l', 'm', 'i', '' ) ) ? $size : $this->image_size;
		return $this;
	}

	/**
	 * @param int $type required
	 * @return object
	 */
	function image_type( $type ) {
		$this->image_type = in_array( $type, array( 'face', 'photo', 'clipart', 'lineart', '' ) ) ? $type : $this->image_type;
		return $this;
	}

	/**
	 * Build and perform the query, return the results.
	 * @param $reset_query boolean optional.
	 * @return object
	 */
	function results( $reset_query = true ) {
		$request  = 'http://www.google.com/images?hl=' . $this->lang;
		$request .= '&q=' . urlencode( $this->query );

		if ( isset( $this->page ) ) {
			$request .= '&start='   . ( $this->page - 1 ) * ( $this->rpp > $this->max ? $this->max : $this->rpp );
		}

		if ( isset( $this->image_type ) ) {
			$request .= '&imgtype=' . $this->image_type;
		}

		if ( isset( $this->image_size ) ) {
			$request .= '&imgsz='   . $this->image_size;
		}

		if ( isset( $this->geocode ) ) {
			$request .= '&geocode=' . $this->geocode;
		}

		if ( $reset_query ) {
			$this->query = '';
		}

		// Make the call and extract out results
		return $this->refine( $this->process( $request ) );
	}

	/**
	 * Function to prepare data for return to client
	 * @access private
	 * @param string $data
	 */
	function refine( $data = '' ) {
		$retVal = array();

		// Extract the image information. This is found inside of a javascript call to setResults
		preg_match( '/dyn.setResults\(\[(.*?)\]\);/is', $data, $match );

		if ( !isset( $match[1] ) )
			return $retVal;

		// Grab all the arrays
		preg_match_all( '/\[(.*?)\"\]/', $match[1], $m );

		foreach ( $m[1] as $item ) {
			// Explode on each paramter (comma delimeter)
			$item = urldecode( str_replace( '\x', '%', $item ) );
			preg_match_all( '/\"(.*?)\"/', $item, $params );

			// Check for more than one paramter. Not sure why, but there seem to be empty array sets between actual results
			if ( count( $params[1] ) > 0 ) {

				$params = $params[1];

				// Important array indices
				// 0  - Link to Google image result. This is the page that displays when you click a result through normal image search
				// 3  - URL of source image
				// 4  - Width of the thumbnail image
				// 5  - Height of the thumbnail image
				// 6  - Title of the image
				// 9  - Width, height and size of the image (In the format of 'width &times; height - size')
				// 10 - Image type
				// 11 - Originating domain
				// 18 - URL of google's thumbnail

				// Match correct values
				preg_match( '/([\d]+) &times; ([\d]+) - ([\d]+)/', $params[9], $dimensions );
				$query = dd_convert_url_query( 'http://www.google.com' . $params[0] );

				// Make the array
				$t = null;
				$t->imgurl        = $query['imgurl'];
				$t->source        = $query['imgrefurl'];
				$t->title         = strip_tags( $params[6] );
				$t->width         = $dimensions[1];
				$t->height        = $dimensions[2];
				$t->size          = $dimensions[3];
				$t->type          = $params[10];
				$t->domain        = $params[11];
				$t->thumb         = null;
				$t->thumb->src    = $params[18];
				$t->thumb->width  = $params[4];
				$t->thumb->height = $params[5];
				$retVal[]         = $t;
			}

			if ( count( $retVal ) == $this->rpp )
				break;
		}

		return $retVal;
	}

}

?>
