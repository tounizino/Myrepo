<?php

namespace CloudGamersDiscuss\Utils;

class SEO {
	public function __construct() {
		add_action( 'wp_head', [ $this, 'addSchema' ] );
	}

	public function addSchema() {
		if ( ! is_singular() ) {
			return;
		}

		$comments = get_comments( [
			'post_id' => get_the_ID(),
			'status'  => 'approve',
			'number'  => 50,
		] );

		if ( empty( $comments ) ) {
			return;
		}

		$schema = [
			'@context' => 'https://schema.org',
			'@type'    => 'DiscussionForumPosting',
			'headline' => get_the_title(),
			'comment'  => [],
		];

		foreach ( $comments as $comment ) {
			$schema['comment'][] = [
				'@type' => 'Comment',
				'text'  => wp_strip_all_tags( $comment->comment_content ),
				'dateCreated' => $comment->comment_date,
				'author' => [
					'@type' => 'Person',
					'name' => $comment->comment_author,
				],
			];
		}

		echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>';
	}
}
