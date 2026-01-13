<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CGRT_Test_Engine {

	/**
	 * Generate plain-English verdict given metrics and score.
	 *
	 * @param array $metrics Metrics.
	 * @param int   $score   Final score.
	 * @param array $subs    Subscores.
	 * @return array
	 */
	public static function generate_verdict( $metrics, $score, $subs ) {
		$latency = isset( $metrics['latency_ms'] ) ? (float) $metrics['latency_ms'] : 0.0;
		$jitter  = isset( $metrics['jitter_ms'] ) ? (float) $metrics['jitter_ms'] : 0.0;
		$loss    = isset( $metrics['loss_pct'] ) ? (float) $metrics['loss_pct'] : 0.0;

		$verdict = array(
			'title'           => '',
			'summary'         => '',
			'recommendations' => array(),
			'warnings'        => array(),
		);

		if ( $score >= 85 ) {
			$verdict['title']   = __( 'Excellent for cloud gaming', 'cloud-gaming-readiness-test' );
			$verdict['summary'] = __( 'Your connection is ideal for cloud gaming. You should experience smooth gameplay with minimal latency, stable performance, and no noticeable lag or stuttering — even in fast-paced, competitive titles.', 'cloud-gaming-readiness-test' );
		} elseif ( $score >= 70 ) {
			$verdict['title']   = __( 'Good for cloud gaming', 'cloud-gaming-readiness-test' );
			$verdict['summary'] = __( 'Your connection is solid for cloud gaming. You should enjoy a smooth experience in most games, though you may notice occasional minor lag or compression artifacts during intense scenes. Competitive play is feasible but may not feel as crisp as a local console.', 'cloud-gaming-readiness-test' );
		} elseif ( $score >= 55 ) {
			$verdict['title']   = __( 'Fair — playable, but not ideal', 'cloud-gaming-readiness-test' );
			$verdict['summary'] = __( 'Your connection is adequate for slower-paced games, but fast-paced titles or competitive play may feel laggy or frustrating. You may experience input delay, occasional freezes, or compression artifacts. Consider improvements for a better experience.', 'cloud-gaming-readiness-test' );
		} else {
			$verdict['title']   = __( 'Poor — not recommended', 'cloud-gaming-readiness-test' );
			$verdict['summary'] = __( 'Cloud gaming will likely be frustrating or unplayable with your current connection. You may see significant input delay, frequent freezes, stuttering, or disconnects. Improvements to your network are strongly recommended before attempting cloud gaming.', 'cloud-gaming-readiness-test' );
		}

		if ( $latency > 80 ) {
			$verdict['warnings'][] = __( 'High latency detected. This will cause noticeable input lag in fast-paced or competitive games.', 'cloud-gaming-readiness-test' );
		}

		if ( $jitter > 15 ) {
			$verdict['warnings'][] = __( 'High jitter detected. This indicates inconsistent latency, which can make gameplay feel uneven or "stuttery" even if average latency is reasonable.', 'cloud-gaming-readiness-test' );
		}

		if ( $loss > 1.0 ) {
			$verdict['warnings'][] = __( 'Packet loss detected. This can cause momentary freezes, artifacts, or even disconnects during gaming sessions.', 'cloud-gaming-readiness-test' );
		}

		$volatility = isset( $metrics['volatility'] ) ? (float) $metrics['volatility'] : 0.0;
		if ( $volatility > 0.3 ) {
			$verdict['warnings'][] = __( 'Network volatility detected. Your connection is unstable, which may result in unpredictable lag spikes or performance drops.', 'cloud-gaming-readiness-test' );
		}

		if ( $latency > 50 && $score < 85 ) {
			$verdict['recommendations'][] = __( 'Use a wired (Ethernet) connection instead of Wi-Fi to reduce latency and jitter.', 'cloud-gaming-readiness-test' );
		}

		if ( $loss > 0.5 ) {
			$verdict['recommendations'][] = __( 'Packet loss often indicates network congestion or Wi-Fi interference. Try switching to a 5 GHz band, moving closer to your router, or using an Ethernet cable.', 'cloud-gaming-readiness-test' );
		}

		if ( $jitter > 10 ) {
			$verdict['recommendations'][] = __( 'High jitter is often caused by Wi-Fi interference, ISP congestion, or background downloads. Close other network-heavy apps, pause downloads, and prioritize gaming traffic if your router supports QoS.', 'cloud-gaming-readiness-test' );
		}

		if ( $latency > 100 || $jitter > 25 || $loss > 3 ) {
			$verdict['recommendations'][] = __( 'Contact your ISP if problems persist. Your connection may be experiencing network issues, throttling, or routing inefficiencies.', 'cloud-gaming-readiness-test' );
		}

		if ( $score >= 70 && $score < 85 ) {
			$verdict['recommendations'][] = __( 'For competitive play or high-fidelity experiences, aim for latency below 30 ms and jitter below 5 ms. Small improvements can make a big difference.', 'cloud-gaming-readiness-test' );
		}

		if ( empty( $verdict['recommendations'] ) && $score >= 85 ) {
			$verdict['recommendations'][] = __( 'Your connection is already excellent. Continue using a wired connection and avoid network-heavy activities during gaming sessions.', 'cloud-gaming-readiness-test' );
		}

		return $verdict;
	}
}
