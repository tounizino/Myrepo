<?php
/**
 * Theme level configuration arrays (navigation, hero, etc.).
 *
 * @package fmhy
 */

function fmhy_hero_data(): array {
	return array(
		'badge'      => array(
			'label' => __( 'Dec 2025 Updates ❄️', 'fmhy' ),
			'url'   => home_url( '/posts/dec-2025/' ),
		),
		'name'       => __( 'freemediaheckyeah', 'fmhy' ),
		'tagline'    => __( 'The largest collection of free stuff on the internet!', 'fmhy' ),
		'actions'    => array(
			array(
				'label' => __( 'See Beginners Guide', 'fmhy' ),
				'url'   => home_url( '/beginners-guide/' ),
				'type'  => 'primary',
			),
			array(
				'label' => __( 'Posts', 'fmhy' ),
				'url'   => home_url( '/posts/' ),
				'type'  => 'ghost',
			),
			array(
				'label' => __( 'Contribute', 'fmhy' ),
				'url'   => home_url( '/other/contributing/' ),
				'type'  => 'ghost',
			),
			array(
				'label'   => __( 'Discord', 'fmhy' ),
				'url'     => 'https://github.com/fmhy/FMHY/wiki/FMHY-Discord',
				'type'    => 'ghost',
				'external'=> true,
			),
		),
		'lede'       => __( 'Or browse these pages ✨', 'fmhy' ),
		'hero_image' => get_template_directory_uri() . '/assets/hero-placeholder.svg',
	);
}

function fmhy_nav_links(): array {
	return array(
		array(
			'label'    => __( '📑 Changelog', 'fmhy' ),
			'url'      => 'https://changes.fmhy.bid/',
			'external' => true,
		),
		array(
			'label'    => __( '📖 Glossary', 'fmhy' ),
			'url'      => 'https://rentry.org/The-Piracy-Glossary',
			'external' => true,
		),
		array(
			'label' => __( '💾 Backups', 'fmhy' ),
			'url'   => home_url( '/other/backups/' ),
		),
		array(
			'label' => __( '🌱 Ecosystem', 'fmhy' ),
			'type'  => 'group',
			'items' => fmhy_ecosystem_links(),
		),
	);
}

function fmhy_ecosystem_links(): array {
	return array(
		array(
			'label' => __( '🌐 Search', 'fmhy' ),
			'url'   => home_url( '/posts/search/' ),
		),
		array(
			'label' => __( '❓ FAQs', 'fmhy' ),
			'url'   => home_url( '/other/FAQ/' ),
		),
		array(
			'label'    => __( '🔖 Bookmarks', 'fmhy' ),
			'url'      => 'https://github.com/fmhy/bookmarks',
			'external' => true,
		),
		array(
			'label'    => __( '✅ SafeGuard', 'fmhy' ),
			'url'      => 'https://github.com/fmhy/FMHY-SafeGuard',
			'external' => true,
		),
		array(
			'label'    => __( '🚀 Startpage', 'fmhy' ),
			'url'      => 'https://fmhy.net/startpage',
			'external' => true,
		),
		array(
			'label'    => __( '📋 snowbin', 'fmhy' ),
			'url'      => 'https://pastes.fmhy.net',
			'external' => true,
		),
		array(
			'label'    => __( '🔎 SearXNG', 'fmhy' ),
			'url'      => 'https://searx.fmhy.net/',
			'external' => true,
		),
		array(
			'label'    => __( '💡 Site Hunting', 'fmhy' ),
			'url'      => 'https://www.reddit.com/r/FREEMEDIAHECKYEAH/wiki/find-new-sites/',
			'external' => true,
		),
		array(
			'label'    => __( '😇 SFW FMHY', 'fmhy' ),
			'url'      => 'https://rentry.org/piracy',
			'external' => true,
		),
		array(
			'label' => __( '🏠 Selfhosting', 'fmhy' ),
			'url'   => home_url( '/other/selfhosting/' ),
		),
		array(
			'label' => __( '🏞 Wallpapers', 'fmhy' ),
			'url'   => home_url( '/other/wallpapers/' ),
		),
		array(
			'label' => __( '💙 Feedback', 'fmhy' ),
			'url'   => home_url( '/feedback/' ),
		),
	);
}

function fmhy_feature_cards(): array {
	return array(
		array(
			'emoji'       => '🛡️',
			'title'       => __( 'Adblocking / Privacy', 'fmhy' ),
			'description' => __( 'Learn how to block ads, trackers and telemetry across all your devices.', 'fmhy' ),
			'url'         => home_url( '/privacy/' ),
			'color'       => '#D05A6E',
		),
		array(
			'emoji'       => '🤖',
			'title'       => __( 'Artificial Intelligence', 'fmhy' ),
			'description' => __( 'Prompting, tooling and datasets to explore the AI universe.', 'fmhy' ),
			'url'         => home_url( '/ai/' ),
			'color'       => '#91989F',
		),
		array(
			'emoji'       => '📺',
			'title'       => __( 'Streaming', 'fmhy' ),
			'description' => __( 'Stream, download or binge your favourite shows, movies and anime.', 'fmhy' ),
			'url'         => home_url( '/video/' ),
			'color'       => '#7AA2F7',
		),
		array(
			'emoji'       => '🥁',
			'title'       => __( 'Listening', 'fmhy' ),
			'description' => __( 'Music, podcasts and radio without paywalls.', 'fmhy' ),
			'url'         => home_url( '/audio/' ),
			'color'       => '#7C82FE',
		),
		array(
			'emoji'       => '🕹️',
			'title'       => __( 'Gaming', 'fmhy' ),
			'description' => __( 'PC, console, emulation and modding resources galore.', 'fmhy' ),
			'url'         => home_url( '/gaming/' ),
			'color'       => '#49D3E9',
		),
		array(
			'emoji'       => '📚',
			'title'       => __( 'Reading', 'fmhy' ),
			'description' => __( 'Books, comics, manga and more for every bibliophile.', 'fmhy' ),
			'url'         => home_url( '/reading/' ),
			'color'       => '#3CCD93',
		),
		array(
			'emoji'       => '💾',
			'title'       => __( 'Downloading', 'fmhy' ),
			'description' => __( 'Windows, macOS, Android & iOS software, media and more.', 'fmhy' ),
			'url'         => home_url( '/downloading/' ),
			'color'       => '#BEC23F',
		),
		array(
			'emoji'       => '🌀',
			'title'       => __( 'Torrenting', 'fmhy' ),
			'description' => __( 'BitTorrent trackers, magnet hubs and automation scripts.', 'fmhy' ),
			'url'         => home_url( '/torrenting/' ),
			'color'       => '#8A6BBE',
		),
		array(
			'emoji'       => '🧠',
			'title'       => __( 'Educational', 'fmhy' ),
			'description' => __( 'STEM, humanities, certifications and language learning.', 'fmhy' ),
			'url'         => home_url( '/educational/' ),
			'color'       => '#A8D8B9',
		),
		array(
			'emoji'       => '📱',
			'title'       => __( 'Android / iOS', 'fmhy' ),
			'description' => __( 'Mobile-first resources, APK mirrors and tweaks.', 'fmhy' ),
			'url'         => home_url( '/mobile/' ),
			'color'       => '#DAC9A6',
		),
		array(
			'emoji'       => '🐧',
			'title'       => __( 'Linux / macOS', 'fmhy' ),
			'description' => __( 'Dotfiles, package mirrors and productivity stacks.', 'fmhy' ),
			'url'         => home_url( '/linux-macos/' ),
			'color'       => '#F17C67',
		),
		array(
			'emoji'       => '🌍',
			'title'       => __( 'Non-English', 'fmhy' ),
			'description' => __( 'Localized libraries curated by our community.', 'fmhy' ),
			'url'         => home_url( '/non-english/' ),
			'color'       => '#FB9966',
		),
		array(
			'emoji'       => '🗂️',
			'title'       => __( 'Miscellaneous', 'fmhy' ),
			'description' => __( 'Wallpaper packs, shopping, news, food, travel and beyond.', 'fmhy' ),
			'url'         => home_url( '/misc/' ),
			'color'       => '#DDD23B',
		),
	);
}

function fmhy_social_links(): array {
	return array(
		array(
			'label' => __( 'GitHub', 'fmhy' ),
			'url'   => 'https://github.com/fmhy/edit',
		),
		array(
			'label' => __( 'Discord', 'fmhy' ),
			'url'   => 'https://github.com/fmhy/FMHY/wiki/FMHY-Discord',
		),
		array(
			'label' => __( 'Reddit', 'fmhy' ),
			'url'   => 'https://reddit.com/r/FREEMEDIAHECKYEAH',
		),
	);
}
