<?php

/**
 * Configuration File
 *
 * Add your own definitions and variables (api keys etc) and rename this file
 * to `dd-config.php`
 *
 * @package DatumDroid_API
 * @subpackage Config
 */

/**
 * API Keys
 */
$dd_api_keys = array(
	'guardian' => ''
);

/**
 * Supported services
 */
$dd_services = array(
	// Plain name => Class name
	'feedzilla' => 'Feedzilla',
	'gimages'   => 'Google_Images',
	'gstocks'   => 'Google_Stocks',
	'gweather'  => 'Google_Weather',
	'guardian'  => 'Guardian',
	'twitter'   => 'Twitter',
	'youtube'   => 'YouTube'
);

?>
