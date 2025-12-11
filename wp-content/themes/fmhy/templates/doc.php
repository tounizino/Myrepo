<?php
/**
 * Remote document template.
 *
 * @package fmhy
 */

$doc = $GLOBALS['fmhy_current_doc'] ?? null;

if ( ! $doc ) {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

get_header();
?>
<section class="fmhy-shell fmhy-doc">
	<div class="fmhy-doc__meta">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			← <?php esc_html_e( 'Back to index', 'fmhy' ); ?>
		</a>
		<?php if ( ! empty( $doc['updated'] ) ) : ?>
			<span>
				<?php
				printf(
					/* translators: %s is a formatted date. */
					esc_html__( 'Updated %s', 'fmhy' ),
					esc_html( date_i18n( get_option( 'date_format' ), $doc['updated'] ) )
				);
				?>
			</span>
		<?php endif; ?>
	</div>
	<header>
		<h1><?php echo esc_html( $doc['title'] ); ?></h1>
		<?php if ( ! empty( $doc['description'] ) ) : ?>
			<p class="fmhy-section__lede"><?php echo esc_html( $doc['description'] ); ?></p>
		<?php endif; ?>
	</header>
	<div class="fmhy-doc__layout">
		<article class="fmhy-doc__content">
			<?php echo $doc['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</article>
		<?php if ( ! empty( $doc['toc'] ) ) : ?>
			<aside class="fmhy-doc__toc" aria-label="<?php esc_attr_e( 'Table of contents', 'fmhy' ); ?>">
				<p class="fmhy-doc__toc-title"><?php esc_html_e( 'Table of contents', 'fmhy' ); ?></p>
				<ol>
					<?php foreach ( $doc['toc'] as $item ) : ?>
						<li class="level-<?php echo esc_attr( $item['level'] ); ?>">
							<a href="#<?php echo esc_attr( $item['id'] ); ?>">
								<?php echo esc_html( $item['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			</aside>
		<?php endif; ?>
	</div>
	<div class="fmhy-doc__source">
		<?php if ( $doc['source_url'] ) : ?>
			<a href="<?php echo esc_url( $doc['source_url'] ); ?>" target="_blank" rel="noopener">
				<?php esc_html_e( 'View source on GitHub', 'fmhy' ); ?>
			</a>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
