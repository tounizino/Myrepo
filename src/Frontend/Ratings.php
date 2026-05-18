<?php

namespace CloudGamersDiscuss\Frontend;

class Ratings {
	public function __construct() {
		add_filter( 'the_content', [ $this, 'addRatingBox' ], 20 );
	}

	public function addRatingBox( $content ) {
		if ( ! is_singular( 'post' ) ) {
			return $content;
		}

		$rating_html = $this->renderRatingBox();
		return $content . $rating_html;
	}

	public function renderRatingBox() {
		$post_id = get_the_ID();
		$avg_rating = $this->getAverageRating( $post_id );
		
		return "
			<div class='cgd-rating-box cgd-glass-panel'>
				<div class='cgd-rating-stars' data-post-id='{$post_id}'>
					" . $this->renderStars( $avg_rating ) . "
				</div>
				<span class='cgd-rating-text'>" . sprintf( __( 'Average Rating: %s', 'cloud-gamers-discuss' ), $avg_rating ) . "</span>
			</div>
		";
	}

	private function getAverageRating( $post_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'cgd_ratings';
		$avg = $wpdb->get_var( $wpdb->prepare(
			"SELECT AVG(rating) FROM $table WHERE post_id = %d",
			$post_id
		) );
		return round( $avg ?: 0, 1 );
	}

	private function renderStars( $rating ) {
		$html = '';
		for ( $i = 1; $i <= 5; $i++ ) {
			$active = ( $i <= round( $rating ) ) ? 'active' : '';
			$html .= "<span class='cgd-star {$active}' data-value='{$i}'>★</span>";
		}
		return $html;
	}
}
