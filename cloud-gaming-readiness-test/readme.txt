=== Cloud Gaming Readiness Test ===
Contributors: cloudgamingreadiness
Tags: cloud gaming, network test, latency, jitter, performance
Requires at least: 5.8
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional cloud gaming network diagnostic system. Measures latency, jitter, packet loss, and connection stability for cloud gaming readiness.

== Description ==

**Cloud Gaming Readiness Test** is a professional-grade diagnostic plugin designed to assess real-world network performance for cloud gaming. Unlike generic speed tests, this plugin focuses exclusively on the factors that matter most for cloud gaming:

* **Latency (Ping):** Average round-trip time to cloud gaming endpoints
* **Jitter:** Latency variation that causes "stuttery" gameplay
* **Packet Loss:** Failed requests that result in freezes or disconnects
* **Connection Stability:** Spike detection and volatility analysis over time
* **Sustained vs. Burst Performance:** Detects degradation during longer sessions

### Features

* **Shortcode and Gutenberg block** for easy embedding
* **Customizable admin dashboard** to manage platforms and endpoints
* **Real-time progress visualization** with live metrics and graphs
* **Cloud-gaming-tuned scoring** with Excellent/Good/Fair/Poor tiers
* **Plain-English verdicts** and actionable recommendations
* **Platform-specific weighting logic** (customize per cloud gaming service)
* **Optional result storage** with anonymized data (no PII)
* **Modern, responsive UI** designed for clarity and trust

### How It Works

Since browsers cannot perform true ICMP ping, this plugin uses real HTTP timing from the user's device to your configured endpoints. For accurate results, endpoints must:

* Be reachable over HTTPS
* Respond quickly (204 or minimal JSON)
* Allow CORS (Access-Control-Allow-Origin)
* Be hosted close to your cloud gaming infrastructure

### Use Cases

* **Cloud gaming platforms:** Provide users with a readiness check before onboarding
* **Tech review sites:** Embed a diagnostic tool for readers
* **Performance brands:** Build trust with a transparent, authoritative test
* **Support teams:** Help users troubleshoot network issues

== Installation ==

1. Upload the `cloud-gaming-readiness-test` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **CG Readiness → Platforms** to configure at least one endpoint
4. Embed with shortcode `[cloud_gaming_readiness_test]` or use the Gutenberg block

== Frequently Asked Questions ==

= Does this plugin require special server infrastructure? =

Yes. You must configure at least one **CORS-enabled HTTPS endpoint** that responds quickly. The plugin measures real HTTP timing from the user's browser to your endpoint.

= Why not just measure download speed? =

Cloud gaming performance is driven by **latency, jitter, and stability**, not bandwidth. A 1 Gbps connection with 120ms latency will feel laggy. This plugin focuses on what actually matters.

= Does this store user data? =

Only if you enable "Store results" in settings. When enabled, results are stored with anonymized hashes (IP + User-Agent). No personally identifiable information (PII) is stored.

= Can I customize the scoring logic? =

Yes. You can adjust:
* Weight of each metric (latency, jitter, loss, stability)
* Score thresholds for tier labels
* Test duration and intensity
* Per-platform custom weights

= What endpoints should I use? =

Ideally, endpoints hosted in your cloud gaming edge regions. For generic testing, use globally distributed CDN endpoints or public cloud regions with CORS enabled.

== Screenshots ==

1. Front-end test interface with real-time metrics
2. Admin dashboard with usage instructions
3. Platform and endpoint management
4. Settings page for fine-tuning
5. Test results with verdict and recommendations

== Changelog ==

= 1.0.0 =
* Initial release
* Complete cloud gaming readiness diagnostic system
* Admin dashboard for platform and endpoint management
* Real-time test visualization with graphs
* Cloud-gaming-tuned scoring algorithm
* Plain-English verdicts and recommendations
* Shortcode and Gutenberg block support
* Optional anonymized result storage

== Upgrade Notice ==

= 1.0.0 =
Initial release of the professional Cloud Gaming Readiness Test plugin.
