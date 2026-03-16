=== Cloud Gaming Readiness Test ===
Contributors: cloudgamingtools
Tags: cloud gaming, network test, speed test, gaming, diagnostics, latency, jitter, bandwidth, packet loss
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Professional network diagnostics and Cloudflare speed test for cloud gaming readiness assessment with modern 2026 UI/UX.

== Description ==

Cloud Gaming Readiness Test is a professional WordPress plugin that helps users determine if their internet connection is ready for cloud gaming services like GeForce NOW, Xbox Cloud Gaming, PlayStation Now, and more.

**Key Features:**

*   **Comprehensive Network Diagnostics** - Test latency, jitter, packet loss, download/upload speeds
*   **Cloudflare Speed Test Integration** - Optional integration with Cloudflare API for accurate speed testing
*   **Modern 2026 UI/UX** - Beautiful, responsive interface with dark/light themes and smooth animations
*   **Admin Customization Panel** - Fully customizable settings for appearance, test parameters, and thresholds
*   **Shortcode Support** - Easy integration with `[cloud_gaming_test]` shortcode
*   **Full-Page Mode** - Dedicated page template for immersive testing experience
*   **Cloud Gaming Recommendations** - Personalized recommendations based on your test results
*   **Mobile Responsive** - Works perfectly on all devices
*   **Accessibility Ready** - WCAG 2.1 AA compliant

== Installation ==

1.  Upload the `cloud-gaming-readiness-test` folder to the `/wp-content/plugins/` directory
2.  Activate the plugin through the 'Plugins' menu in WordPress
3.  Configure settings under Settings > Cloud Gaming Test
4.  Use the shortcode `[cloud_gaming_test]` in any page or post, or use the full-page template

== Frequently Asked Questions ==

= How do I display the test on my site? =

You have two options:
1.  Use the shortcode `[cloud_gaming_test]` in any page or post
2.  Create a new page and select "Cloud Gaming Test Page" from the Page Attributes template dropdown

= What internet speed do I need for cloud gaming? =

*   Minimum: 10 Mbps download (720p streaming)
*   Recommended: 25 Mbps download (1080p streaming)
*   Optimal: 50+ Mbps download (4K streaming)

For competitive gaming, we recommend latency under 50ms and jitter under 10ms.

= How accurate are the tests? =

The plugin uses browser-based tests for accuracy. For the most precise results, enable Cloudflare integration in the settings (requires Cloudflare API credentials).

= Can I customize the appearance? =

Yes! Go to Settings > Cloud Gaming Test to customize:
*   Theme color
*   Dark/light mode
*   Custom CSS
*   Result thresholds
*   Test parameters

= Does this work with all cloud gaming services? =

Yes! The test evaluates your connection's general readiness for cloud gaming. Compatible services include:
*   GeForce NOW
*   Xbox Cloud Gaming
*   PlayStation Now (now PlayStation Plus Premium)
*   Amazon Luna
*   And more!

== Screenshots ==

1.  Intro screen with start button
2.  Testing in progress with real-time metrics
3.  Results dashboard with score and recommendations

== Changelog ==

= 1.0.0 =
*   Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of Cloud Gaming Readiness Test plugin.
