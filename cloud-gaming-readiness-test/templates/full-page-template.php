<?php
/**
 * Full page template for Cloud Gaming Readiness Test.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Enqueue assets explicitly for full-page mode.
wp_enqueue_style( 'cloud-gaming-readiness-test-public' );
wp_enqueue_style( 'cloud-gaming-readiness-test-' . get_option( 'cgrt_default_mode', 'dark' ) );
wp_enqueue_script( 'cloud-gaming-readiness-test-app' );
wp_enqueue_script( 'cloud-gaming-readiness-test-network-test' );
wp_enqueue_script( 'cloud-gaming-readiness-test-speed-test' );
wp_enqueue_script( 'cloud-gaming-readiness-test-ui-animations' );

get_header();
?>

<main class="site-main cgrt-full-page">
    <div class="cgrt-full-page-container">
        <?php echo do_shortcode( '[cloud_gaming_test]' ); ?>
    </div>
</main>

<?php
get_footer();
