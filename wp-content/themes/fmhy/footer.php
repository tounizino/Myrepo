<?php
/**
 * Footer template.
 *
 * @package fmhy
 */
?>
</main>
<footer class="fmhy-foot">
	<div class="fmhy-shell">
		<p>
			<?php
			printf(
				/* translators: %s is an HTML link pointing to the feedback page. */
				esc_html__( 'Made with ❤ by the FMHY community — %s', 'fmhy' ),
				'<a class="feedback-footer" href="' . esc_url( home_url( '/feedback/' ) ) . '">' . esc_html__( 'share feedback', 'fmhy' ) . '</a>'
			);
			?>
		</p>
		<p>
			<?php
			printf(
				/* translators: 1: current year, 2: founding year */
				esc_html__( '© %1$s · Estd %2$s. This site does not host any files.', 'fmhy' ),
				gmdate( 'Y' ),
				'2018'
			);
			?>
		</p>
		<div class="fmhy-social">
			<?php foreach ( fmhy_social_links() as $social ) : ?>
				<a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener">
					<span class="visually-hidden"><?php echo esc_html( $social['label'] ); ?></span>
					<?php echo esc_html( mb_substr( $social['label'], 0, 1 ) ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
