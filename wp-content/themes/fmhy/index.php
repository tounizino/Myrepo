<?php
/**
 * Default catch-all template.
 *
 * @package fmhy
 */

get_header();
?>
<section class="fmhy-shell fmhy-doc">
    <div class="fmhy-card">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article <?php post_class(); ?>>
                    <h1><?php the_title(); ?></h1>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <h1><?php esc_html_e( 'Nothing to see here… yet.', 'fmhy' ); ?></h1>
            <p><?php esc_html_e( 'This install relies on the remote FMHY docs. Try visiting one of the sections from the homepage.', 'fmhy' ); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();
