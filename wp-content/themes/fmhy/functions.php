<?php
/**
 * FMHY Atlas Theme bootstrap file.
 *
 * @package fmhy
 */

define( 'FMHY_THEME_VERSION', '1.0.0' );
define( 'FMHY_GITHUB_RAW_BASE', 'https://raw.githubusercontent.com/fmhy/edit/main/docs/' );
define( 'FMHY_GITHUB_TREE_ENDPOINT', 'https://api.github.com/repos/fmhy/edit/git/trees/main?recursive=1' );
require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/theme-data.php';
require_once get_template_directory() . '/inc/docs.php';
require_once get_template_directory() . '/inc/vendor/Parsedown.php';

/**
 * Theme setup.
 */
function fmhy_setup(): void {
    load_theme_textdomain( 'fmhy', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
    add_theme_support( 'responsive-embeds' );
    register_nav_menus(
        array(
            'primary' => __( 'Primary menu', 'fmhy' ),
            'footer'  => __( 'Footer menu', 'fmhy' ),
        )
    );
    add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', 'fmhy_setup' );

/**
 * Locale switcher that reads the lang query parameter or stored cookie.
 */
function fmhy_locale_switcher( string $locale ): string {
    if ( is_admin() ) {
        return $locale;
    }

    $supported = array(
        'en' => 'en_US',
        'ar' => 'ar',
    );

    $request_lang = null;
    if ( isset( $_GET['lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        $request_lang = sanitize_text_field( wp_unslash( $_GET['lang'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
    }

    if ( $request_lang && isset( $supported[ $request_lang ] ) ) {
        fmhy_store_language_preference( $request_lang );
        return $supported[ $request_lang ];
    }

    $cookie_lang = isset( $_COOKIE['fmhy_lang'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['fmhy_lang'] ) ) : null;
    if ( $cookie_lang && isset( $supported[ $cookie_lang ] ) ) {
        return $supported[ $cookie_lang ];
    }

    return $locale;
}
add_filter( 'locale', 'fmhy_locale_switcher' );

/**
 * Register scripts and styles.
 */
function fmhy_enqueue_assets(): void {
    $theme_uri = get_template_directory_uri();

    wp_enqueue_style(
        'fmhy-fonts',
        'https://fonts.googleapis.com/css2?family=Cairo:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap',
        array(),
        FMHY_THEME_VERSION
    );

    wp_enqueue_style(
        'fmhy-style',
        get_stylesheet_uri(),
        array( 'fmhy-fonts' ),
        filemtime( get_template_directory() . '/style.css' )
    );

    wp_enqueue_script(
        'fmhy-theme',
        $theme_uri . '/assets/js/theme.js',
        array(),
        filemtime( get_template_directory() . '/assets/js/theme.js' ),
        true
    );

    wp_localize_script(
        'fmhy-theme',
        'fmhyTheme',
        array(
            'lang'     => fmhy_get_current_language(),
            'langUrls' => array(
                'en' => fmhy_switch_lang_url( 'en' ),
                'ar' => fmhy_switch_lang_url( 'ar' ),
            ),
            'theme'    => fmhy_get_theme_mode(),
            'homeUrl'  => home_url( '/' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'fmhy_enqueue_assets' );

/**
 * Add helpful classes to the body tag.
 */
function fmhy_body_classes( array $classes ): array {
    $classes[] = 'ltr';
    if ( 'ar' === fmhy_get_current_language() ) {
        $classes[] = 'rtl';
    }
    return $classes;
}
add_filter( 'body_class', 'fmhy_body_classes' );

/**
 * Attempt to render a FMHY doc when WordPress cannot resolve a template.
 */
function fmhy_template_router(): void {
    if ( is_admin() || is_feed() || is_embed() ) {
        return;
    }

    if ( ! is_404() ) {
        return;
    }

    $path = fmhy_current_path();
    if ( '' === $path ) {
        return;
    }

    $language = fmhy_get_current_language();
    $doc      = fmhy_fetch_doc( $path, $language );

    if ( null === $doc ) {
        return;
    }

    status_header( 200 );
    $GLOBALS['fmhy_current_doc'] = $doc;
    locate_template( 'templates/doc.php', true );
    exit;
}
add_action( 'template_redirect', 'fmhy_template_router', 0 );


