<?php

namespace CloudGamersDiscuss\Frontend;

class Reactions {
	public function __construct() {
		add_filter( 'the_content', [ $this, 'addReactionBar' ] );
	}

	public function addReactionBar( $content ) {
		if ( ! is_singular( 'post' ) ) {
			return $content;
		}

		$reactions_html = $this->renderReactionBar();
		return $content . $reactions_html;
	}

	public function renderReactionBar() {
		$post_id = get_the_ID();
		return "
			<div class='cgd-article-reactions cgd-glass-panel'>
				<h4>" . __( 'HOW DO YOU FEEL ABOUT THIS?', 'cloud-gamers-discuss' ) . "</h4>
				<div class='cgd-reactions-flex'>
					" . $this->renderReactionItem( $post_id, 'fire', '🔥' ) . "
					" . $this->renderReactionItem( $post_id, 'gaming', '🎮' ) . "
					" . $this->renderReactionItem( $post_id, 'mindblown', '🤯' ) . "
					" . $this->renderReactionItem( $post_id, 'love', '❤️' ) . "
					" . $this->renderReactionItem( $post_id, 'like', '👍' ) . "
					" . $this->renderReactionItem( $post_id, 'dislike', '👎' ) . "
				</div>
			</div>
		";
	}

	private function renderReactionItem( $post_id, $type, $emoji ) {
		global $wpdb;
		$table = $wpdb->prefix . 'cgd_reactions';
		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM $table WHERE post_id = %d AND type = %s AND comment_id = 0",
			$post_id, $type
		) );

		return "
			<button class='cgd-reaction-btn' data-type='{$type}' data-post-id='{$post_id}'>
				<span class='emoji'>{$emoji}</span>
				<span class='count'>{$count}</span>
			</button>
		";
	}
}
