<?php
/**
 * Front page template.
 *
 * @package fmhy
 */

get_header();

$hero      = fmhy_hero_data();
$features  = fmhy_feature_cards();
$ecosystem = fmhy_ecosystem_links();
$social    = fmhy_social_links();
?>
<section class="fmhy-hero">
    <div class="fmhy-shell">
        <div class="fmhy-hero__grid">
            <div>
                <?php if ( ! empty( $hero['badge']['label'] ) ) : ?>
                    <a class="fmhy-hero__badge" href="<?php echo esc_url( $hero['badge']['url'] ); ?>">
                        <?php echo esc_html( $hero['badge']['label'] ); ?>
                    </a>
                <?php endif; ?>
                <h1 class="fmhy-hero__title"><?php echo esc_html( $hero['name'] ); ?></h1>
                <p class="fmhy-hero__tagline"><?php echo esc_html( $hero['tagline'] ); ?></p>
                <div class="fmhy-hero__actions">
                    <?php foreach ( $hero['actions'] as $action ) : ?>
                        <a class="fmhy-btn <?php echo 'primary' === $action['type'] ? 'fmhy-btn--brand' : 'fmhy-btn--ghost'; ?>" <?php echo $action['external'] ?? false ? 'target="_blank" rel="noopener"' : ''; ?> href="<?php echo esc_url( $action['url'] ); ?>">
                            <?php echo esc_html( $action['label'] ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="fmhy-hero__visual">
                <span class="fmhy-hero__planet"></span>
                <span class="fmhy-hero__spark"></span>
                <span class="fmhy-hero__spark"></span>
                <?php if ( ! empty( $hero['hero_image'] ) ) : ?>
                    <img src="<?php echo esc_url( $hero['hero_image'] ); ?>" alt="<?php esc_attr_e( 'FMHY abstract art', 'fmhy' ); ?>" class="visually-hidden" />
                <?php endif; ?>
            </div>
        </div>
        <p class="fmhy-section__lede"><?php echo esc_html( $hero['lede'] ); ?></p>
    </div>
</section>

<section class="fmhy-shell">
    <div class="fmhy-feature-grid">
        <?php foreach ( $features as $feature ) : ?>
            <article class="fmhy-feature-card">
                <div class="fmhy-feature-card__icon" style="background: <?php echo esc_attr( $feature['color'] ); ?>1f; color: <?php echo esc_attr( $feature['color'] ); ?>;">
                    <span aria-hidden="true"><?php echo esc_html( $feature['emoji'] ); ?></span>
                </div>
                <h3 class="fmhy-feature-card__title"><?php echo esc_html( $feature['title'] ); ?></h3>
                <p class="fmhy-feature-card__desc"><?php echo esc_html( $feature['description'] ); ?></p>
                <a class="fmhy-feature-card__cta" href="<?php echo esc_url( $feature['url'] ); ?>">
                    <span><?php esc_html_e( 'Explore', 'fmhy' ); ?></span>
                    <span class="icon" aria-hidden="true">→</span>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="fmhy-shell" aria-labelledby="ecosystem-heading">
    <div class="fmhy-card">
        <div class="fmhy-section__title" id="ecosystem-heading"><?php esc_html_e( 'Ecosystem & tools', 'fmhy' ); ?></div>
        <p class="fmhy-section__lede"><?php esc_html_e( 'Community services, mirrors and tools that power FMHY every day.', 'fmhy' ); ?></p>
        <div class="fmhy-chip-list">
            <?php foreach ( $ecosystem as $chip ) : ?>
                <a class="fmhy-chip" <?php echo $chip['external'] ?? false ? 'target="_blank" rel="noopener"' : ''; ?> href="<?php echo esc_url( $chip['url'] ); ?>">
                    <?php echo esc_html( $chip['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="fmhy-shell" aria-labelledby="community-heading">
    <div class="fmhy-card">
        <div class="fmhy-section__title" id="community-heading"><?php esc_html_e( 'Community & socials', 'fmhy' ); ?></div>
        <p class="fmhy-section__lede"><?php esc_html_e( 'Join the moderators, maintainers and curators keeping the library alive.', 'fmhy' ); ?></p>
        <div class="fmhy-chip-list">
            <?php foreach ( $social as $profile ) : ?>
                <a class="fmhy-chip fmhy-chip--brand" href="<?php echo esc_url( $profile['url'] ); ?>" target="_blank" rel="noopener">
                    <?php echo esc_html( $profile['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php
get_footer();
