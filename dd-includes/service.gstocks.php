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
		$obj = simplexml_load_string( '<?xml version="1.0"?><xml_api_reply version="1"><finance module_id="0" tab_id="0" mobile_row="0" mobile_zipped="1" row="0" section="0" ><symbol data="GOOG"/><pretty_symbol data="GOOG"/><symbol_lookup_url data="/finance?client=ig&amp;q=GOOG"/><company data="Google Inc."/><exchange data="Nasdaq"/><exchange_timezone data="ET"/><exchange_utc_offset data="+05:00"/><exchange_closing data="960"/><divisor data="2"/><currency data="USD"/><last data="590.49"/><high data="592.75"/><low data="586.70"/><volume data="3394233"/><avg_volume data="3408"/><market_cap data="190661.99"/><open data="589.51"/><y_close data="583.67"/><change data="+6.82"/><perc_change data="1.17"/><delay data="0"/><trade_timestamp data="21 Oct 2011"/><trade_date_utc data="20111021"/><trade_time_utc data="200008"/><current_date_utc data="20111023"/><current_time_utc data="103305"/><symbol_url data="/finance?client=ig&amp;q=GOOG"/><chart_url data="/finance/chart?q=NASDAQ:GOOG&amp;tlf=12"/><disclaimer_url data="/help/stock_disclaimer.html"/><ecn_url data=""/><isld_last data=""/><isld_trade_date_utc data=""/><isld_trade_time_utc data=""/><brut_last data=""/><brut_trade_date_utc data=""/><brut_trade_time_utc data=""/><daylight_savings data="true"/></finance></xml_api_reply>' );

		if ( ! $obj || ! $obj->finance->company['data'] )
			return array();
		else
			$obj = $obj->finance;

		$conditions = array();

		foreach ( (array) $obj as $condition => $val ) {
			if ( !isset( $val['data'] ) ) continue;
			$conditions[$condition] = (array) $val['data'];
			$conditions[$condition] = $conditions[$condition][0];
			$conditions[$condition] = in_array( $condition, array( 'symbol_lookup_url', 'symbol_url', 'chart_url', 'disclaimer_url' ) ) ? 'http://google.com' . $conditions[$condition] : $conditions[$condition];
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
		$request .= '?stock=' . urlencode( $this->query );

		if ( $reset_query ) {
			$this->query = '';
		}

		return $this->objectify( $this->process( $request ) );
	}

}

?>
