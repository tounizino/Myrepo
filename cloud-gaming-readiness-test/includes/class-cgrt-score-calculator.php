<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CGRT_Score_Calculator {

	/**
	 * Normalize weights to sum to 1.
	 *
	 * @param array $weights Weights.
	 * @return array
	 */
	public static function normalize_weights( $weights ) {
		$weights = wp_parse_args(
			$weights,
			array(
				'latency'   => 0.40,
				'jitter'    => 0.25,
				'loss'      => 0.25,
				'stability' => 0.10,
			)
		);

		$sum = 0.0;
		foreach ( $weights as $k => $v ) {
			$weights[ $k ] = max( 0.0, (float) $v );
			$sum          += $weights[ $k ];
		}

		if ( $sum <= 0.0001 ) {
			return array(
				'latency'   => 0.40,
				'jitter'    => 0.25,
				'loss'      => 0.25,
				'stability' => 0.10,
			);
		}

		foreach ( $weights as $k => $v ) {
			$weights[ $k ] = $v / $sum;
		}

		return $weights;
	}

	/**
	 * Convert a metric into a 0-100 subscore using cloud-gaming-tuned cutoffs.
	 *
	 * @param float $latency_ms Latency (avg or median) in ms.
	 * @return int
	 */
	public static function latency_score( $latency_ms ) {
		$latency_ms = max( 0.0, (float) $latency_ms );

		// Cloud gaming is interactive; latency cliffs matter.
		$points = array(
			20  => 100,
			30  => 90,
			50  => 75,
			80  => 55,
			120 => 30,
			180 => 10,
			999 => 0,
		);

		return self::piecewise_score( $latency_ms, $points, true );
	}

	/**
	 * Jitter score.
	 *
	 * @param float $jitter_ms Jitter (stddev or p95-p50) in ms.
	 * @return int
	 */
	public static function jitter_score( $jitter_ms ) {
		$jitter_ms = max( 0.0, (float) $jitter_ms );

		$points = array(
			2   => 100,
			5   => 88,
			10  => 70,
			20  => 45,
			30  => 28,
			50  => 10,
			999 => 0,
		);

		return self::piecewise_score( $jitter_ms, $points, true );
	}

	/**
	 * Packet loss score.
	 *
	 * @param float $loss_pct Loss percentage.
	 * @return int
	 */
	public static function loss_score( $loss_pct ) {
		$loss_pct = max( 0.0, (float) $loss_pct );

		$points = array(
			0.0 => 100,
			0.5 => 86,
			1.0 => 72,
			2.0 => 55,
			5.0 => 25,
			10.0 => 5,
			100.0 => 0,
		);

		return self::piecewise_score( $loss_pct, $points, true );
	}

	/**
	 * Stability score.
	 *
	 * @param float $volatility_index 0..1 where 0 is stable.
	 * @return int
	 */
	public static function stability_score( $volatility_index ) {
		$volatility_index = max( 0.0, min( 1.0, (float) $volatility_index ) );
		// 0 = perfect (100), 1 = chaotic (0).
		return (int) round( ( 1.0 - $volatility_index ) * 100 );
	}

	/**
	 * Calculate unified score.
	 *
	 * @param array $metrics Metrics.
	 * @param array $weights Weights.
	 * @return array
	 */
	public static function calculate( $metrics, $weights ) {
		$weights = self::normalize_weights( $weights );

		$lat = isset( $metrics['latency_ms'] ) ? (float) $metrics['latency_ms'] : 0.0;
		$jit = isset( $metrics['jitter_ms'] ) ? (float) $metrics['jitter_ms'] : 0.0;
		$los = isset( $metrics['loss_pct'] ) ? (float) $metrics['loss_pct'] : 0.0;
		$vol = isset( $metrics['volatility'] ) ? (float) $metrics['volatility'] : 0.0;

		$subs = array(
			'latency'   => self::latency_score( $lat ),
			'jitter'    => self::jitter_score( $jit ),
			'loss'      => self::loss_score( $los ),
			'stability' => self::stability_score( $vol ),
		);

		$score = 0.0;
		foreach ( $subs as $k => $v ) {
			$score += (float) $v * (float) $weights[ $k ];
		}

		return array(
			'score'    => (int) round( max( 0.0, min( 100.0, $score ) ) ),
			'subscores' => $subs,
			'weights'  => $weights,
		);
	}

	/**
	 * Tier label.
	 *
	 * @param int   $score Score.
	 * @param array $tiers Tier thresholds.
	 * @return string
	 */
	public static function tier_for_score( $score, $tiers ) {
		$score = (int) $score;
		$tiers = wp_parse_args(
			$tiers,
			array(
				'excellent' => 85,
				'good'      => 70,
				'fair'      => 55,
				'poor'      => 0,
			)
		);

		if ( $score >= (int) $tiers['excellent'] ) {
			return 'Excellent';
		}
		if ( $score >= (int) $tiers['good'] ) {
			return 'Good';
		}
		if ( $score >= (int) $tiers['fair'] ) {
			return 'Fair';
		}
		return 'Poor';
	}

	private static function piecewise_score( $x, $points, $descending_thresholds ) {
		// $points is threshold=>score.
		$thresholds = array_keys( $points );
		sort( $thresholds, SORT_NUMERIC );

		foreach ( $thresholds as $t ) {
			if ( $x <= $t ) {
				return (int) $points[ $t ];
			}
		}

		$last = end( $thresholds );
		return (int) $points[ $last ];
	}
}
