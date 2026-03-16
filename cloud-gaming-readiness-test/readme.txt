=== Cloud Gaming Readiness Test ===
Contributors: cloudgamingtools
Tags: cloud gaming, network test, speed test, gaming, diagnostics, latency, jitter, bandwidth, packet loss
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Professional network diagnostics with real-time testing and modern full-page gaming UI for cloud gaming readiness assessment.

== Description ==

Cloud Gaming Readiness Test is a professional WordPress plugin that helps users determine if their internet connection is ready for cloud gaming services like GeForce NOW, Xbox Cloud Gaming, PlayStation Plus Premium, and more.

**Key Features:**

*   **Real-Time Network Diagnostics** - Live testing with sub-millisecond accuracy for latency, jitter, packet loss, download/upload speeds
*   **Enhanced Accuracy** - Multiple endpoint testing, outlier filtering, and concurrent connections for precise measurements
*   **Modern Gaming UI** - Immersive full-page dark gaming theme with animated backgrounds, glassmorphism effects, and smooth transitions
*   **Detailed Metrics Dashboard** - Real-time progress tracking with min/max values, peak speeds, and consistency analysis
*   **Connection Quality Grade** - Overall A+ to F grading based on comprehensive performance analysis
*   **Admin Customization Panel** - Fully configurable settings for test parameters and result thresholds
*   **Shortcode & Template Support** - Use `[cloud_gaming_test]` shortcode or dedicated full-page template
*   **Cloud Gaming Recommendations** - Personalized advice based on your specific test results for GeForce NOW, Xbox Cloud Gaming, and more
*   **Mobile Responsive** - Perfect experience on all devices with touch-optimized interactions
*   **Accessibility Ready** - WCAG 2.1 AA compliant with keyboard navigation and screen reader support

== Installation ==

1.  Upload `cloud-gaming-readiness-test` folder to `/wp-content/plugins/` directory
2.  Activate the plugin through 'Plugins' menu in WordPress
3.  Configure settings under Settings > Cloud Gaming Test (optional)
4.  Use shortcode `[cloud_gaming_test]` in any page or post, OR
5.  Create a new page and select "Cloud Gaming Test Page" from Page Attributes template dropdown

== Frequently Asked Questions ==

= How do I display the test on my site? =

You have two options:
1.  Use shortcode `[cloud_gaming_test]` in any page or post
2.  Create a new page and select "Cloud Gaming Test Page" from Page Attributes template dropdown for full immersive experience

= What internet speed do I need for cloud gaming? =

*   Minimum: 10 Mbps download (720p streaming)
*   Recommended: 25 Mbps download (1080p streaming)  
*   Optimal: 50+ Mbps download (4K streaming)

For competitive gaming, we recommend latency under 50ms and jitter under 10ms.

= How accurate are the tests? =

The plugin uses enhanced browser-based testing with:
*   Multiple test endpoints for accuracy
*   Outlier filtering for reliable results
*   Concurrent download/upload connections
*   Sub-millisecond latency precision
*   Consistency and stability analysis

The test runs for approximately 15 seconds with real-time progress updates.

= What metrics are tested? =

*   **Latency** - Response time in milliseconds (min, max, average, deviation)
*   **Jitter** - Network consistency variation (average, peak)
*   **Packet Loss** - Percentage of lost data packets (sent, lost, retransmits)
*   **Download Speed** - Maximum and average download rate with consistency score
*   **Upload Speed** - Maximum and average upload rate with latency impact
*   **Connection Quality** - Overall grade (A+ to F) and stability percentage

= Can I customize the test parameters? =

Yes! Go to Settings > Cloud Gaming Test to configure:
*   Ping count (number of latency tests)
*   Test timeout duration
*   Result thresholds for latency, jitter, and packet loss
*   Cloudflare API integration (optional)

= Does this work with all cloud gaming services? =

Yes! The test evaluates your connection's general readiness for cloud gaming. Compatible services include:
*   GeForce NOW
*   Xbox Cloud Gaming  
*   PlayStation Plus Premium
*   Amazon Luna
*   And more!

= Why is there no theme toggle? =

The plugin uses a unified dark gaming theme optimized for cloud gaming aesthetics. This provides:
*   Better visual focus during testing
*   Reduced eye strain during extended testing sessions
*   Consistent professional gaming experience
*   Optimized for color contrast and readability

== Screenshots ==

1.  Hero section with start button and feature cards
2.  Real-time testing dashboard with progress and metrics
3.  Detailed results with grade circle and analysis
4.  Personalized cloud gaming recommendations

== Changelog ==

= 1.0.0 =
*   Initial release
*   Real-time network diagnostics
*   Full-page gaming UI
*   Enhanced accuracy with multi-endpoint testing
*   Detailed metrics dashboard
*   Connection quality grading
*   Cloud gaming recommendations

== Upgrade Notice ==

= 1.0.0 =
Initial release of Cloud Gaming Readiness Test plugin with real-time testing and modern full-page gaming UI.
