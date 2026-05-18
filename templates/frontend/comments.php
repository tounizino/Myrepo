<?php
/**
 * Custom Comments Template for Cloud Gamers Discuss
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="cgd-comments" class="cgd-container" data-post-id="<?php echo get_the_ID(); ?>">
	<div class="cgd-glass-panel">
		<div class="cgd-header">
			<h3><span class="cgd-count"><?php echo get_comments_number(); ?></span> <?php _e( 'DISCUSSIONS', 'cloud-gamers-discuss' ); ?></h3>
			<div class="cgd-sorting">
				<button class="cgd-sort-btn active" data-sort="newest"><?php _e( 'Newest', 'cloud-gamers-discuss' ); ?></button>
				<button class="cgd-sort-btn" data-sort="top"><?php _e( 'Top', 'cloud-gamers-discuss' ); ?></button>
				<button class="cgd-sort-btn" data-sort="trending"><?php _e( 'Trending', 'cloud-gamers-discuss' ); ?></button>
			</div>
		</div>

		<!-- Comment Form -->
		<div class="cgd-form-container">
			<form id="cgd-comment-form" class="cgd-comment-form">
				<div class="cgd-form-header">
					<img src="<?php echo get_avatar_url( 0 ); ?>" class="cgd-avatar" alt="Guest">
					<div class="cgd-input-group">
						<input type="text" name="author" placeholder="<?php _e( 'Name', 'cloud-gamers-discuss' ); ?>" required>
						<input type="email" name="email" placeholder="<?php _e( 'Email (Optional)', 'cloud-gamers-discuss' ); ?>">
					</div>
				</div>
				<div class="cgd-editor">
					<textarea name="comment" placeholder="<?php _e( 'Join the discussion...', 'cloud-gamers-discuss' ); ?>" required></textarea>
					<div class="cgd-editor-toolbar">
						<button type="button" class="cgd-tool-btn" title="Bold">B</button>
						<button type="button" class="cgd-tool-btn" title="Italic">I</button>
						<button type="button" class="cgd-tool-btn" title="Spoiler">!</button>
						<button type="button" class="cgd-tool-btn" title="GIF">GIF</button>
					</div>
				</div>
				<div class="cgd-form-footer">
					<div class="cgd-optin">
						<label>
							<input type="checkbox" name="subscribe"> <?php _e( 'Get the latest cloud gaming news', 'cloud-gamers-discuss' ); ?>
						</label>
					</div>
					<button type="submit" class="cgd-btn">
						<?php _e( 'POST COMMENT', 'cloud-gamers-discuss' ); ?>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
					</button>
				</div>
				<input type="hidden" name="comment_post_ID" value="<?php echo get_the_ID(); ?>">
				<input type="hidden" name="comment_parent" id="cgd-parent-id" value="0">
			</form>
		</div>

		<!-- Comments List -->
		<div id="cgd-comments-list" class="cgd-comments-list">
			<!-- AJAX loaded comments go here -->
			<div class="cgd-loading-skeleton">
				<div class="cgd-skeleton-item"></div>
				<div class="cgd-skeleton-item"></div>
				<div class="cgd-skeleton-item"></div>
			</div>
		</div>

		<div class="cgd-load-more-container">
			<button id="cgd-load-more" class="cgd-btn cgd-btn-secondary" style="display: none;">
				<?php _e( 'LOAD MORE', 'cloud-gamers-discuss' ); ?>
			</button>
		</div>
	</div>
</div>
