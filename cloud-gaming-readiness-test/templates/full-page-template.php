<?php
/**
 * Cloud Gaming Test page template - Full-page design.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Enqueue assets explicitly for full-page mode.
wp_enqueue_style( 'cloud-gaming-readiness-test-public' );
wp_enqueue_script( 'cloud-gaming-readiness-test-app' );
wp_enqueue_script( 'cloud-gaming-readiness-test-network-test' );
wp_enqueue_script( 'cloud-gaming-readiness-test-ui-animations' );

// Add SVG gradients definition
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'cgrt-full-page' ); ?>>

<?php echo do_shortcode( '[cloud_gaming_test]' ); ?>

<!-- SVG Definitions for Gradients -->
<svg style="display: none;">
    <defs>
        <linearGradient id="scoreGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#00ff88;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#00ccff;stop-opacity:1" />
        </linearGradient>
    </defs>
</svg>

<?php wp_footer(); ?>
</body>
</html>
