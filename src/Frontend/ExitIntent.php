<?php

namespace CloudGamersDiscuss\Frontend;

class ExitIntent {
	public function __construct() {
		add_action( 'wp_footer', [ $this, 'renderPopup' ] );
	}

	public function renderPopup() {
		if ( ! is_singular() ) {
			return;
		}
		?>
		<div id="cgd-exit-popup" class="cgd-popup">
			<div class="cgd-popup-content cgd-glass-panel">
				<button class="cgd-close-popup">&times;</button>
				<h2>Wait! Don't Miss Out</h2>
				<p>Get the latest cloud gaming news and exclusive deals delivered to your inbox.</p>
				<form id="cgd-exit-form">
					<input type="email" name="email" placeholder="Your email address" required>
					<button type="submit" class="cgd-btn">Subscribe Now</button>
				</form>
			</div>
		</div>
		<style>
			#cgd-exit-popup {
				position: fixed;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: rgba(0, 0, 0, 0.8);
				display: none;
				justify-content: center;
				align-items: center;
				z-index: 10000;
				backdrop-filter: blur(10px);
			}
			.cgd-popup-content {
				max-width: 500px;
				width: 90%;
				text-align: center;
				position: relative;
			}
			.cgd-close-popup {
				position: absolute;
				top: 10px;
				right: 15px;
				background: none;
				border: none;
				color: #fff;
				font-size: 2rem;
				cursor: pointer;
			}
		</style>
		<?php
	}
}
