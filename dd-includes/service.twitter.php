<?php

/**
 * Wrapper class around the Twitter Search API for PHP
 *
 * From http://ryanfaerman.com/twittersearch
 *
 * @package DatumDroid_API
 * @subpackage Services
 */

// Exit if accessed directly
if ( !defined( 'DD_DIR' ) ) exit;

class DD_Service_Twitter extends DD_Search_Service {

	/**
	 * When "true", adds "<user>:" to the beginning of the tweet. This is useful for readers that do not display Atom's author field. The default is "false"
	 * @var boolean
	 */
	var $show_user = false;

	/**
	 * The maximum number of results that this service can return
	 * @var int
	 */
	var $max = '100';

	/**
	 * Find tweets from a user
	 * @param string $user required
	 * @return object
	 */
	function from( $user ) {
		$this->query .= ' from:' . str_replace( '@', '', $user );
		return $this;
	}

	/**
	 * Find tweets to a user
	 * @param string $user required
	 * @return object
	 */
	function to( $user ) {
		$this->query .= ' to:' . str_replace( '@', '', $user );
		return $this;
	}

	/**
	 * Find tweets referencing a user
	 * @param string $user required
	 * @return object
	 */
	function about( $user ) {
		$this->query .= ' @' . str_replace( '@', '', $user );
		return $this;
	}

	/**
	 * Find tweets containing a hashtag
	 * @param string $user required
	 * @return object
	 */
	function with( $hashtag ) {
		$this->query .= ' #' . str_replace( '#', '', $hashtag );
		return $this;
	}

	/**
	 * Set show_user to true
	 * @return object
	 */
	function show_user() {
		$this->show_user = true;
		return $this;
	}

	/**
	 * Build and perform the query, return the results.
	 * @param $reset_query boolean optional.
	 * @return object
	 */
	function results( $reset_query = true ) {
		$request  = 'http://search.twitter.com/search.' . $this->type;
		$request .= '?q=' . urlencode( $this->query );

		if ( isset( $this->rpp ) ) {
			$request .= '&rpp=' . $this->rpp;
		}

		if ( isset( $this->page ) ) {
			$request .= '&page=' . $this->page;
		}

		if ( isset( $this->lang ) ) {
			$request .= '&lang=' . $this->lang;
		}

		if ( isset( $this->since ) ) {
			$request .= '&since_id=' . $this->since;
		}

		if ( $this->show_user ) {
			$request .= '&show_user=true';
		}

		if ( isset( $this->geocode ) ) {
			$request .= '&geocode=' . $this->geocode;
		}

		if ( $reset_query ) {
			$this->query = '';
		}

		return $this->objectify( $this->process( $request ) )->results;
	}

	/**
	 * Returns the top ten queries that are currently trending on Twitter.
	 * @return object
	 */
	function trends() {
		$request = 'http://search.twitter.com/trends.json';

		return $this->objectify( $this->process( $request ) );
	}

}

?>
