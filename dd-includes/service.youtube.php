<?php

/**
 *
 * http://framework.zend.com/manual/en/zend.gdata.youtube.html
 * http://code.google.com/apis/youtube/2.0/developers_guide_php.html
 * http://www.ibm.com/developerworks/xml/library/x-youtubeapi/
 *
 * @package DatumDroid_API
 * @subpackage Services
 */

// Exit if accessed directly
if ( !defined( 'DD_DIR' ) ) exit;

/**
 * Wrapper class around the YouTube Search API for PHP
 */
class DD_Service_YouTube extends DD_Search_Service {

	/**
	 * API Version that we're requesting
	 */
	var $api_ver = 2;

	/**
	 * The maximum number of results that this service can return
	 * @var int
	 */
	var $max = '50';

	/**
	 * Build and perform the query, return the results.
	 * @param $reset_query boolean optional.
	 * @return object
	 */
	function results( $reset_query = true ) {
		$request  = 'http://gdata.youtube.com/feeds/api/videos/?v=' . $this->api_ver . '&alt=' . $this->type;
		$request .= '&q=' . urlencode( $this->query );
		$request .= '&order=published&format=1,6'; //format for mobile vids code.google.com/apis/youtube/2.0/reference.html#formatsp

		if ( isset( $this->rpp ) ) {
			$request .= '&max-results=' . min( $this->rpp, $this->max );
		}

		if ( isset( $this->page ) ) {
			$offset = ( $this->page - 1 ) * min( $this->rpp, $this->max );
			$request .= '&start-index=' . ( $offset < 1 ) ? 1 : $offset;
		}

		if ( isset( $this->lang ) ) {
			$request .= '&lr=' . $this->lang;
		}

		if ( isset( $this->geocode ) ) {
			$request .= '&location=' . $this->geocode;
		}

		if ( $reset_query ) {
			$this->query = '';
		}

		//print_r($request);

		return $this->refine( $this->objectify( $this->process( $request ) )->feed->entry );
	}

	/**
	 * Function to prepare data for return to client
	 * @access private
	 * @param array $results
	 */
	function refine( $results = array() ) {
		$new_results = array();
		$t           = '$t';
		$media       = 'media$';

		foreach ( $results as $result ) {
			$video                   = null;
			$video->title            = $result->title->$t;
			$video->watch_page       = $result->link[0]->href;
			$video->thumbnail        = $result->{$media . 'group'}->{$media . 'thumbnail'}[0]->url;
			$video->mobile_rtsp_link = array();

			// We use the mediaGroup object directly to retrieve its 'Mobile RSTP
			// link' child
			foreach ( $result->{$media . 'group'}->{$media . 'content'} as $content ) {
				switch ( $content->type ) {
					case 'video/3gpp' :
						$video->mobile_rtsp_link[] = $content->url;
						break;

					case 'application/x-shockwave-flash' :
						$video->flash_player_url = $content->url;
						break;
				}
			}

			$new_results[] = $video;
		}

		return $new_results;
	}

}

?>
