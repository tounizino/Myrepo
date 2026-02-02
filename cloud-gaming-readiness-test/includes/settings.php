<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get default settings
 */
function cgrt_get_default_settings() {
	return array(
		'test_duration' => 15, // seconds
		'test_intensity' => 5, // requests per second
		'weights' => array(
			'latency' => 40,
			'jitter' => 30,
			'packet_loss' => 20,
			'stability' => 10
		),
		'thresholds' => array(
			'excellent' => 90,
			'good' => 75,
			'fair' => 50,
			'poor' => 0
		),
		'latency_thresholds' => array(
			'excellent' => 30,
			'good' => 60,
			'fair' => 100
		),
		'jitter_thresholds' => array(
			'excellent' => 5,
			'good' => 15,
			'fair' => 30
		)
	);
}
