<?php
/**
 * Helper Functions
 *
 * @package CloudGamingAvailability
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all cloud games
 *
 * @param int $per_page Items per page.
 * @param int $offset Offset.
 * @return WP_Query
 */
function cga_get_games( $per_page = 10, $offset = 0 ) {
	return new WP_Query(
		array(
			'post_type'      => 'cloud_games',
			'posts_per_page' => $per_page,
			'offset'         => $offset,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
}

/**
 * Get game availability for a specific game
 *
 * @param int $game_id Game post ID.
 * @return array
 */
function cga_get_game_availability( $game_id ) {
	$availability = get_post_meta( $game_id, 'cga_platform_availability', true );
	return is_array( $availability ) ? $availability : array();
}

/**
 * Check if a game is available on a specific platform
 *
 * @param int    $game_id Game post ID.
 * @param string $platform_id Platform ID.
 * @return bool
 */
function cga_is_available_on_platform( $game_id, $platform_id ) {
	$availability = cga_get_game_availability( $game_id );
	return in_array( $platform_id, $availability, true );
}

/**
 * Get platform information
 *
 * @param string $platform_id Platform ID.
 * @return array|null
 */
function cga_get_platform( $platform_id ) {
	return CGA_Loader::get_platform( $platform_id );
}

/**
 * Get all platforms
 *
 * @return array
 */
function cga_get_platforms() {
	return CGA_Loader::get_platforms();
}

/**
 * Get platform logo URL
 *
 * @param string $platform_id Platform ID.
 * @return string
 */
function cga_get_platform_logo( $platform_id ) {
	$logos = get_option( 'cga_platform_logos', array() );
	return isset( $logos[ $platform_id ] ) ? $logos[ $platform_id ] : '';
}

/**
 * Get plugin settings
 *
 * @return array
 */
function cga_get_settings() {
	return get_option( 'cga_settings', array() );
}

/**
 * Get a specific setting
 *
 * @param string $key Setting key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function cga_get_setting( $key, $default = '' ) {
	$settings = cga_get_settings();
	return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

/**
 * Render game shortcode
 *
 * @param int    $game_id Game post ID.
 * @param string $theme Theme.
 * @param int    $columns Grid columns.
 * @param bool   $show_description Show description.
 * @return string
 */
function cga_render_game( $game_id, $theme = 'auto', $columns = 3, $show_description = true ) {
	$shortcode = new CGA_Shortcode();
	return $shortcode->render_shortcode(
		array(
			'game_id'         => $game_id,
			'theme'           => $theme,
			'columns'         => $columns,
			'show_description' => $show_description ? 'true' : 'false',
		)
	);
}

/**
 * Count games available on a platform
 *
 * @param string $platform_id Platform ID.
 * @return int
 */
function cga_count_games_on_platform( $platform_id ) {
	$args = array(
		'post_type'      => 'cloud_games',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	);

	$games = get_posts( $args );
	$count  = 0;

	foreach ( $games as $game_id ) {
		if ( cga_is_available_on_platform( $game_id, $platform_id ) ) {
			$count++;
		}
	}

	return $count;
}

/**
 * Get games available on a specific platform
 *
 * @param string $platform_id Platform ID.
 * @param int    $per_page Items per page.
 * @return WP_Query
 */
function cga_get_games_by_platform( $platform_id, $per_page = -1 ) {
	$args = array(
		'post_type'      => 'cloud_games',
		'posts_per_page' => $per_page,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'fields'         => 'ids',
	);

	$all_games = get_posts( $args );
	$filtered_games = array();

	foreach ( $all_games as $game_id ) {
		if ( cga_is_available_on_platform( $game_id, $platform_id ) ) {
			$filtered_games[] = $game_id;
		}
	}

	$args['post__in'] = $filtered_games;
	unset( $args['fields'] );

	return new WP_Query( $args );
}

/**
 * Get statistics about platform availability
 *
 * @return array
 */
function cga_get_platform_stats() {
	$platforms = cga_get_platforms();
	$stats     = array();

	foreach ( $platforms as $platform_id => $platform ) {
		$stats[ $platform_id ] = array(
			'name'  => $platform['name'],
			'count' => cga_count_games_on_platform( $platform_id ),
		);
	}

	return $stats;
}
