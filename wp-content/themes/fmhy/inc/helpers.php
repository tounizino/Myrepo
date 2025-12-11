<?php
/**
 * Utility helpers for FMHY theme.
 *
 * @package fmhy
 */

/**
 * Store user language choice in a cookie.
 */
function fmhy_store_language_preference( string $lang ): void {
    $allowed = array( 'en', 'ar' );
    if ( ! in_array( $lang, $allowed, true ) ) {
        return;
    }

    fmhy_store_cookie( 'fmhy_lang', $lang );
}


/**
 * Generic cookie setter with sane defaults.
 */
function fmhy_store_cookie( string $key, string $value ): void {
    $path   = defined( 'COOKIEPATH' ) ? COOKIEPATH : '/';
    $domain = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';
    setcookie( $key, $value, time() + YEAR_IN_SECONDS, $path, $domain, is_ssl(), true );
}

/**
 * Return the currently active language code.
 */
function fmhy_get_current_language(): string {
    if ( isset( $_COOKIE['fmhy_lang'] ) ) {
        $cookie = sanitize_text_field( wp_unslash( $_COOKIE['fmhy_lang'] ) );
        if ( in_array( $cookie, array( 'en', 'ar' ), true ) ) {
            return $cookie;
        }
    }

    $locale = determine_locale();
    return str_starts_with( $locale, 'ar' ) ? 'ar' : 'en';
}

/**
 * Determine theme mode preference.
 */
function fmhy_get_theme_mode(): string {
    if ( isset( $_COOKIE['fmhy_theme'] ) ) {
        $mode = sanitize_text_field( wp_unslash( $_COOKIE['fmhy_theme'] ) );
        if ( in_array( $mode, array( 'light', 'dark' ), true ) ) {
            return $mode;
        }
    }

    return 'light';
}

/**
 * Build a URL that switches the language.
 */
function fmhy_switch_lang_url( string $lang ): string {
    $current = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
    return add_query_arg( 'lang', $lang, $current );
}

/**
 * Get the current relative path (without home path prefix).
 */
function fmhy_current_path(): string {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
    $request_uri = strtok( $request_uri, '?' );
    $request_uri = trim( $request_uri, '/' );

    $home_path = trim( parse_url( home_url( '/' ), PHP_URL_PATH ) ?? '', '/' );
    if ( $home_path && str_starts_with( $request_uri, $home_path ) ) {
        $request_uri = trim( substr( $request_uri, strlen( $home_path ) ), '/' );
    }

    return $request_uri;
}

/**
 * Convert a string into a slug suitable for IDs.
 */
function fmhy_slugify( string $text ): string {
    $text = strtolower( wp_strip_all_tags( $text ) );
    $text = preg_replace( '/[^\p{L}\p{N}\s-]/u', '', $text );
    $text = preg_replace( '/[\s_-]+/u', '-', $text );
    return trim( $text, '-' ) ?: 'section';
}

/**
 * Allowed tags for doc content rendering.
 */
function fmhy_allowed_doc_tags(): array {
    $allowed = wp_kses_allowed_html( 'post' );

    $custom_tags = array( 'details', 'summary', 'table', 'thead', 'tbody', 'tr', 'th', 'td' );
    foreach ( $custom_tags as $tag ) {
        $allowed[ $tag ] = array(
            'class' => true,
            'id'    => true,
            'open'  => true,
        );
    }

    $allowed['code'] = array(
        'class' => true,
        'id'    => true,
    );
    $allowed['pre'] = array(
        'class' => true,
    );
    $allowed['span'] = array(
        'class' => true,
        'id'    => true,
    );
    $allowed['img'] = array(
        'src'    => true,
        'alt'    => true,
        'width'  => true,
        'height' => true,
        'class'  => true,
    );
    $allowed['a']['target'] = true;
    $allowed['a']['rel']    = true;

    return $allowed;
}

/**
 * Normalize Markdown links so they point to the WordPress site.
 */
function fmhy_normalize_markdown_links( string $markdown ): string {
    return preg_replace_callback(
        '/\]\(([^)]+)\.md(#[^)]+)?\)/i',
        function ( $matches ) {
            $path = $matches[1];
            $hash = $matches[2] ?? '';
            $path = preg_replace( '#^\./#', '', $path );
            $path = str_replace( '../', '', $path );
            $url  = home_url( '/' . ltrim( $path, '/' ) . '/' );
            return sprintf( '](%s%s)', $url, $hash );
        },
        $markdown
    );
}

if ( ! function_exists( 'str_starts_with' ) ) {
    function str_starts_with( string $haystack, string $needle ): bool { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
        return 0 === strpos( $haystack, $needle );
    }
}

if ( ! function_exists( 'str_ends_with' ) ) {
    function str_ends_with( string $haystack, string $needle ): bool { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
        if ( '' === $needle ) {
            return true;
        }
        return substr( $haystack, -strlen( $needle ) ) === $needle;
    }
}

if ( ! function_exists( 'str_contains' ) ) {
    function str_contains( string $haystack, string $needle ): bool { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
        return false !== strpos( $haystack, $needle );
    }
}
