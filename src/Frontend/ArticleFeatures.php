<?php

namespace CloudGamersDiscuss\Frontend;

class ArticleFeatures {
	public function __construct() {
		add_filter( 'the_content', [ $this, 'addShareBar' ], 5 );
	}

	public function addShareBar( $content ) {
		if ( ! is_singular( 'post' ) ) {
			return $content;
		}

		$share_html = "
			<div class='cgd-share-bar'>
				<button class='cgd-share-btn' data-platform='x' title='Share on X'>X</button>
				<button class='cgd-share-btn' data-platform='facebook' title='Share on Facebook'>F</button>
				<button class='cgd-share-btn' data-platform='reddit' title='Share on Reddit'>R</button>
				<button class='cgd-share-btn' data-platform='whatsapp' title='Share on WhatsApp'>W</button>
				<button class='cgd-share-btn' data-platform='copy' title='Copy Link'>🔗</button>
			</div>
		";

		return $share_html . $content;
	}
}
