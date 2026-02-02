<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get default platforms
 */
function cgrt_get_default_platforms() {
	return array(
		array(
			'id' => 'geforce-now',
			'name' => 'NVIDIA GeForce NOW',
			'enabled' => true,
			'servers' => array(
				array( 'region' => 'US East', 'url' => 'https://example.com/ping', 'protocol' => 'https' ),
				array( 'region' => 'EU West', 'url' => 'https://example.com/ping-eu', 'protocol' => 'https' )
			)
		),
		array(
			'id' => 'xbox-cloud',
			'name' => 'Xbox Cloud Gaming',
			'enabled' => true,
			'servers' => array(
				array( 'region' => 'Global', 'url' => 'https://example.com/xbox-ping', 'protocol' => 'https' )
			)
		),
		array(
			'id' => 'playstation-plus',
			'name' => 'PlayStation Plus',
			'enabled' => true,
			'servers' => array(
				array( 'region' => 'Global', 'url' => 'https://example.com/ps-ping', 'protocol' => 'https' )
			)
		)
	);
}
