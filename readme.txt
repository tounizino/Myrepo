=== Cloud Games Availability v2 ===
Contributors: cloudgames
Tags: cloud gaming, gaming, availability, comparison, cards, platforms
Requires at least: 5.0
Requires PHP: 7.4
Tested up to: 6.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display cloud gaming platform availability with modern card-based UI. Supports GeForce NOW, Xbox Cloud Gaming, PlayStation Cloud, Luna, Boosteroid, Shadow PC, and more.

== Description ==

Cloud Games Availability v2 is a modern WordPress plugin designed for cloud gaming content websites. It displays platform availability using a beautiful card-based UI inspired by modern SaaS/affiliate comparison designs.

**Key Features:**

* **Modern Card-Based UI** - Premium glassmorphism design with smooth animations
* **Multiple Platform Support** - Includes GeForce NOW, Xbox Cloud Gaming, PlayStation Cloud, Amazon Luna, Boosteroid, Shadow PC, and more
* **Per-Game Availability** - Toggle availability for each game on each platform
* **Fully Customizable** - Control colors, spacing, button styles, and more
* **Light/Dark Theme** - Automatic theme detection or manual selection
* **Responsive Design** - Mobile-first design that works on all devices
* **Shortcode Support** - Easy embedding with shortcodes
* **Gutenberg Block** - Native WordPress block editor integration
* **SEO Friendly** - Clean markup and fast loading
* **No Heavy Dependencies** - Lightweight and fast

== Installation ==

1. Upload the `cloud-games-availability-v2` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Cloud Games → Settings to configure the plugin
4. Add platforms and games through the admin panel
5. Embed using the shortcode or Gutenberg block

== Usage ==

**Shortcode:**
```
[cloud_games_availability game_id="1"]
```

**Optional Parameters:**
* `game_id` - Game ID (required)
* `group_by_availability` - "yes" or "no" (default: no)
* `columns` - Number of columns: 1, 2, 3, 4, or 6 (default: 3)

**Gutenberg Block:**
Search for "Cloud Games Availability" in the block editor and insert it. Configure the game ID and display options in the block settings.

== Screenshots ==

1. Admin Dashboard with game and platform overview
2. Platform management interface
3. Game availability management
4. Settings panel with customization options
5. Frontend card display in light mode
6. Frontend card display in dark mode

== Changelog ==

= 2.0.0 =
* Initial release with card-based UI
* Support for multiple cloud gaming platforms
* Per-game availability management
* Light/Dark theme support
* Glassmorphism effects
* Gutenberg block integration
* Responsive design
* Full admin panel

== Frequently Asked Questions ==

= Can I add custom platforms? =
Yes! You can add unlimited custom platforms through the admin panel. Each platform can have its own icon, pricing, CTA text, and link.

= Does this work with the WordPress Block Editor? =
Yes! The plugin includes a Gutenberg block that makes it easy to add availability cards to any page or post.

= Can I customize the card design? =
Absolutely. You can customize colors, border radius, spacing, button styles, and more through the settings panel. You can also add custom CSS for advanced customization.

= Is the plugin mobile-friendly? =
Yes! The card-based design is fully responsive and works beautifully on mobile devices, tablets, and desktops.

= Can I display availability for multiple games on one page? =
Yes! You can use multiple shortcodes or blocks with different game IDs to display availability for multiple games on the same page.

== Upgrade Notice ==

= 2.0.0 =
First public release of Cloud Games Availability v2 with modern card-based UI design.
