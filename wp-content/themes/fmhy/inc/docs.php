<?php
/**
 * Remote documentation ingestion helpers.
 *
 * @package fmhy
 */

/**
 * Fetch and transform a FMHY doc by slug.
 */
function fmhy_fetch_doc( string $path, string $language = 'en' ): ?array {
    $path = trim( $path, '/' );
    if ( '' === $path ) {
        $path = 'index';
    }

    $cache_key = 'fmhy_doc_' . md5( $language . '|' . $path );
    $cached    = get_transient( $cache_key );
    if ( false !== $cached ) {
        return $cached;
    }

    $content      = '';
    $doc_reference = '';

    if ( 'en' !== $language ) {
        $local_file = fmhy_localized_doc_path( $path, $language );
        if ( $local_file ) {
            $local_contents = file_get_contents( $local_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
            if ( false !== $local_contents ) {
                $content       = $local_contents;
                $doc_reference = 'local:' . $local_file;
            }
        }
    }

    if ( '' === $content ) {
        $resolved = fmhy_resolve_doc_path( $path );
        if ( ! $resolved ) {
            return null;
        }

        $remote = fmhy_fetch_remote_markdown( $resolved );
        if ( is_wp_error( $remote ) || empty( $remote ) ) {
            return null;
        }

        $content       = $remote;
        $doc_reference = $resolved;
    }

    $content = fmhy_normalize_markdown_links( $content );
    list( $front_matter, $body ) = fmhy_parse_front_matter( $content );
    $rendered = fmhy_render_markdown( $body );

    if ( ! $rendered ) {
        return null;
    }

    $title       = $front_matter['title'] ?? fmhy_guess_title( $body, $path );
    $description = $front_matter['description'] ?? '';
    if ( ! $description ) {
        $description = fmhy_extract_description( $body );
    }

    $doc = array(
        'title'       => $title,
        'description' => $description,
        'html'        => $rendered['html'],
        'toc'         => $rendered['toc'],
        'source_url'  => str_starts_with( $doc_reference, 'local:' ) ? '' : fmhy_source_url( $doc_reference ),
        'path'        => $path,
        'language'    => $language,
        'updated'     => time(),
    );

    set_transient( $cache_key, $doc, 6 * HOUR_IN_SECONDS );

    return $doc;
}

/**
 * Return an absolute path for a localized markdown file.
 */
function fmhy_localized_doc_path( string $path, string $language ): ?string {
    $base   = get_template_directory() . '/content/' . $language;
    $target = trailingslashit( $base ) . $path . '.md';
    if ( file_exists( $target ) ) {
        return $target;
    }
    return null;
}

/**
 * Resolve repo document from slug.
 */
function fmhy_resolve_doc_path( string $path ): ?string {
    $path      = trim( $path, '/' );
    $path      = '' === $path ? 'index' : $path;
    $candidate = 'docs/' . $path . '.md';

    $map = fmhy_get_doc_map();
    if ( in_array( $candidate, $map, true ) ) {
        return $candidate;
    }

    $candidate_lower = strtolower( $candidate );
    foreach ( $map as $entry ) {
        if ( strtolower( $entry ) === $candidate_lower ) {
            return $entry;
        }
    }

    return null;
}

/**
 * Fetch Markdown content from GitHub.
 */
function fmhy_fetch_remote_markdown( string $doc_path ) {
    $relative = preg_replace( '#^docs/#', '', $doc_path );
    $url      = FMHY_GITHUB_RAW_BASE . $relative;

    $response = wp_remote_get(
        $url,
        array(
            'timeout' => 20,
            'headers' => fmhy_github_headers(),
        )
    );

    if ( is_wp_error( $response ) ) {
        return $response;
    }

    if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
        return new WP_Error( 'fmhy_remote', __( 'Unable to fetch remote document.', 'fmhy' ) );
    }

    return wp_remote_retrieve_body( $response );
}

/**
 * Headers for GitHub API/Raw requests.
 */
function fmhy_github_headers(): array {
    $headers = array(
        'Accept'     => 'application/vnd.github+json',
        'User-Agent' => 'fmhy-wp-theme'
    );

    if ( defined( 'FMHY_GITHUB_TOKEN' ) && FMHY_GITHUB_TOKEN ) {
        $headers['Authorization'] = 'Bearer ' . FMHY_GITHUB_TOKEN;
    }

    return $headers;
}

/**
 * Retrieve and cache repo doc listing.
 */
function fmhy_get_doc_map(): array {
    static $map = null;

    if ( null !== $map ) {
        return $map;
    }

    $cached = get_transient( 'fmhy_doc_tree' );
    if ( false !== $cached ) {
        $map = $cached;
        return $map;
    }

    $response = wp_remote_get(
        FMHY_GITHUB_TREE_ENDPOINT,
        array(
            'timeout' => 20,
            'headers' => fmhy_github_headers(),
        )
    );

    if ( is_wp_error( $response ) ) {
        $map = array();
        return $map;
    }

    $code = wp_remote_retrieve_response_code( $response );
    if ( 200 !== $code ) {
        $map = array();
        return $map;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( empty( $body['tree'] ) ) {
        $map = array();
        return $map;
    }

    $docs = array();
    foreach ( $body['tree'] as $node ) {
        if ( 'blob' !== ( $node['type'] ?? '' ) ) {
            continue;
        }

        $path = $node['path'] ?? '';
        if ( ! $path || ! str_starts_with( $path, 'docs/' ) || ! str_ends_with( $path, '.md' ) ) {
            continue;
        }

        $docs[] = $path;
    }

    set_transient( 'fmhy_doc_tree', $docs, DAY_IN_SECONDS );
    $map = $docs;

    return $map;
}

/**
 * Parse YAML front matter.
 */
function fmhy_parse_front_matter( string $markdown ): array {
    $markdown = ltrim( $markdown );
    if ( 0 !== strpos( $markdown, "---\n" ) ) {
        return array( array(), $markdown );
    }

    $end = strpos( $markdown, "\n---", 4 );
    if ( false === $end ) {
        return array( array(), $markdown );
    }

    $front = substr( $markdown, 4, $end - 4 );
    $body  = ltrim( substr( $markdown, $end + 4 ) );

    $data = array();
    foreach ( preg_split( "/\r?\n/", trim( $front ) ) as $line ) {
        if ( ! str_contains( $line, ':' ) ) {
            continue;
        }
        list( $key, $value ) = array_map( 'trim', explode( ':', $line, 2 ) );
        $data[ $key ] = $value;
    }

    return array( $data, $body );
}

/**
 * Render Markdown and extract heading metadata.
 */
function fmhy_render_markdown( string $markdown ): ?array {
    $parser = new Parsedown();
    $parser->setBreaksEnabled( true );
    $html = $parser->text( $markdown );

    $result = fmhy_process_headings( $html );
    if ( ! $result ) {
        return null;
    }

    $sanitized = wp_kses( $result['html'], fmhy_allowed_doc_tags() );
    return array(
        'html' => $sanitized,
        'toc'  => $result['toc'],
    );
}

/**
 * Attach IDs to headings and build a TOC.
 */
function fmhy_process_headings( string $html ): ?array {
    if ( ! class_exists( 'DOMDocument' ) ) {
        return array(
            'html' => $html,
            'toc'  => array(),
        );
    }

    libxml_use_internal_errors( true );
    $dom = new DOMDocument();
    $dom->loadHTML( '<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
    libxml_clear_errors();

    $xpath = new DOMXPath( $dom );
    $toc   = array();
    $used  = array();

    foreach ( array( 'h2', 'h3', 'h4' ) as $tag ) {
        foreach ( $xpath->query( '//' . $tag ) as $node ) {
            $text = trim( $node->textContent );
            if ( '' === $text ) {
                continue;
            }

            $slug = fmhy_slugify( $text );
            $slug = fmhy_ensure_unique_slug( $slug, $used );
            $node->setAttribute( 'id', $slug );
            $toc[] = array(
                'id'    => $slug,
                'label' => $text,
                'level' => (int) substr( $tag, 1 )
            );
        }
    }

    $html = $dom->saveHTML();
    $html = preg_replace( '/^<\?xml.+?\?>/i', '', $html );

    return array(
        'html' => $html,
        'toc'  => $toc,
    );
}

/**
 * Keep heading IDs unique.
 */
function fmhy_ensure_unique_slug( string $slug, array &$used ): string {
    $base = $slug ?: 'section';
    $slug = $base;
    $idx  = 2;
    while ( in_array( $slug, $used, true ) ) {
        $slug = $base . '-' . $idx;
        $idx++;
    }
    $used[] = $slug;
    return $slug;
}

/**
 * Try to determine a readable title from markdown.
 */
function fmhy_guess_title( string $markdown, string $fallback ): string {
    if ( preg_match( '/^#\s+(.+)$/m', $markdown, $matches ) ) {
        return trim( $matches[1] );
    }

    $parts = explode( '/', $fallback );
    return ucwords( str_replace( '-', ' ', end( $parts ) ) );
}

/**
 * Build a source URL pointing to GitHub.
 */
function fmhy_source_url( string $doc_path ): string {
    $doc_path = ltrim( $doc_path, '/' );
    return 'https://github.com/fmhy/edit/blob/main/' . $doc_path;
}

/**
 * Build a short description from Markdown body.
 */
function fmhy_extract_description( string $markdown ): string {
    $lines = preg_split( "/\r?\n/", $markdown );
    if ( ! $lines ) {
        return '';
    }

    foreach ( $lines as $line ) {
        $line = trim( $line );
        if ( '' === $line ) {
            continue;
        }
        if ( str_starts_with( $line, '#' ) ) {
            continue;
        }
        $line = preg_replace( '/\*\*/', '', $line );
        return wp_strip_all_tags( $line );
    }

    return '';
}
