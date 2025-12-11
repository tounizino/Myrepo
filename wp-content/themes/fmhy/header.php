<?php
/**
 * Header template.
 *
 * @package fmhy
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-theme="<?php echo esc_attr( fmhy_get_theme_mode() ); ?>">
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>
<a class="visually-hidden" href="#fmhy-main"><?php esc_html_e( 'Skip to content', 'fmhy' ); ?></a>
<header class="fmhy-nav">
	<div class="fmhy-nav__inner fmhy-shell">
		<a class="fmhy-nav__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="fmhy-nav__brand-badge" aria-hidden="true">FM</span>
			<span>FMHY</span>
		</a>
		<nav class="fmhy-nav__links" id="fmhy-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'fmhy' ); ?>">
			<?php foreach ( fmhy_nav_links() as $entry ) : ?>
				<?php if ( isset( $entry['type'] ) && 'group' === $entry['type'] ) : ?>
					<details class="fmhy-nav__group">
						<summary><?php echo esc_html( $entry['label'] ); ?></summary>
						<div class="fmhy-nav__dropdown">
							<?php foreach ( $entry['items'] as $item ) : ?>
								<a class="fmhy-nav__link" <?php echo $item['external'] ?? false ? 'target="_blank" rel="noopener"' : ''; ?> href="<?php echo esc_url( $item['url'] ); ?>">
									<?php echo esc_html( $item['label'] ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</details>
				<?php else : ?>
					<a class="fmhy-nav__link" <?php echo $entry['external'] ?? false ? 'target="_blank" rel="noopener"' : ''; ?> href="<?php echo esc_url( $entry['url'] ); ?>">
						<?php echo esc_html( $entry['label'] ); ?>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>
		<button class="fmhy-nav__toggle" type="button" aria-expanded="false" aria-controls="fmhy-nav" data-nav-toggle>
			<span class="visually-hidden"><?php esc_html_e( 'Toggle navigation', 'fmhy' ); ?></span>
			☰
		</button>
		<div class="fmhy-nav__actions">
			<div class="fmhy-lang-switch" aria-label="<?php esc_attr_e( 'Language switcher', 'fmhy' ); ?>">
				<?php $lang = fmhy_get_current_language(); ?>
				<a class="<?php echo 'en' === $lang ? 'is-active' : ''; ?>" href="<?php echo esc_url( fmhy_switch_lang_url( 'en' ) ); ?>">EN</a>
				<a class="<?php echo 'ar' === $lang ? 'is-active' : ''; ?>" href="<?php echo esc_url( fmhy_switch_lang_url( 'ar' ) ); ?>">AR</a>
			</div>
			<button type="button" class="fmhy-pill-button fmhy-pill-button--primary" data-theme-toggle>
				<span class="icon" aria-hidden="true">🌓</span>
				<span class="label"><?php esc_html_e( 'Theme', 'fmhy' ); ?></span>
			</button>
		</div>
	</div>
</header>
<main id="fmhy-main">
