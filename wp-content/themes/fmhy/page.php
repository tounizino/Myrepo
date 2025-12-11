<?php
/**
 * Generic page template.
 *
 * @package fmhy
 */

get_header();
?>
<section class="fmhy-shell fmhy-doc">
    <div class="fmhy-card">
        <?php while ( have_posts() ) : the_post(); ?>
            <article <?php post_class(); ?>>
                <h1><?php the_title(); ?></h1>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</section>
<?php
get_footer();
