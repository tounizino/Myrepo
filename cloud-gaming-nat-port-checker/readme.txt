=== Cloud Gaming NAT & Port Checker ===
Contributors: Your Name
Tags: nat, port-checker, cloud-gaming, network-diagnostics, gaming
Requires at least: 5.6
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A premium NAT & Port Checker Tool for cloud gaming platforms with real-time network diagnostics, STUN/TURN detection, and router configuration tips.

== Description ==

**Cloud Gaming NAT & Port Checker** is a professional-grade WordPress plugin designed to help users test their network compatibility for cloud gaming platforms. With advanced NAT detection using STUN servers, multi-protocol port checking, and comprehensive device information display, this tool is perfect for cloud gaming blogs, tech support sites, and gaming communities.

= Core Features =

**🔍 NAT Type Checker**
* Detect NAT Type (Type 1/Open, Type 2/Moderate, Type 3/Strict)
* Color-coded indicators (green/yellow/red)
* Display public IP, ISP, country, region, and city
* Advanced STUN/TURN server diagnostics
* Symmetric vs full-cone NAT detection
* Port mapping behavior analysis
* Auto-refresh option (30 seconds)
* Confidence scoring and fallback recommendations

**🔌 Port Checker**
* Predefined presets for major platforms:
  - Xbox Cloud Gaming
  - GeForce NOW
  - PS Remote Play
  - Steam Link
  - Shadow PC
  - Amazon Luna / Stadia
* Custom port scanning (TCP/UDP)
* Latency measurement
* Packet loss detection
* Multi-attempt verification
* Real-time status updates

**🖥️ Device & Connection Details**
* Browser and OS detection
* Device type (Desktop/Mobile/Tablet)
* Screen resolution and pixel density
* GPU renderer (WebGL)
* CPU core count
* Estimated RAM
* Network connection type
* Downlink speed and RTT
* User agent and language

**📶 Router Configuration Tips**
* Common router admin IPs
* Default credentials reference
* NAT/UPnP setup guides
* QoS prioritization tips
* Port forwarding walkthroughs

**📚 Customizable Guides Section**
* Add blog post cards with images
* Fully editable from admin panel
* Responsive grid layout
* Direct links to your content

**🔐 Privacy & Security**
* No personal data stored
* Real-time checks only
* IP truncation in logs (optional)
* Secure nonce verification
* No external tracking

**🎨 Modern 2026 Design**
* Clean, flat UI without shadows
* Dark mode by default
* Light/dark theme toggle
* 3 font size presets (small/medium/large)
* Fully responsive design
* Animated transitions
* Accessible (ARIA labels, keyboard navigation)

**⚙️ Admin Panel**
* WordPress Settings API
* Customizable theme colors
* Font size controls
* Container width/padding
* Guide card management
* Optional diagnostic logging
* Easy shortcode reference

= Shortcodes =

* `[cloud_nat_port_checker]` - Full tool with all sections
* `[cloud_nat_checker]` - NAT checker only (lightweight embed)
* `[cloud_port_checker]` - Port checker only (focused testing)

All shortcodes work site-wide in posts, pages, and widgets without conflicts.

= Advanced Features =

* **Copy-to-clipboard** buttons for IP addresses
* **Auto-refresh** option for continuous NAT monitoring
* **Tooltips** for technical terms
* **Loading animations** for better UX
* **AJAX-based checks** to avoid page reloads
* **Fallback logic** for failed detections
* **Optional logging** for admins (disabled by default)
* **Transient caching** for geo-IP lookups
* **CSS variables** for easy theme customization

= Use Cases =

* Cloud gaming support blogs
* Network troubleshooting guides
* ISP compatibility testing
* Router configuration tutorials
* Gaming community forums
* Tech support documentation
* Educational networking content

== Installation ==

1. Upload the `cloud-gaming-nat-port-checker` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > NAT & Port Checker to configure
4. Use shortcode `[cloud_nat_port_checker]` in any post/page
5. Customize colors, fonts, and guides as needed

== Frequently Asked Questions ==

= Does this plugin store user data? =

No. The plugin performs all checks in real-time and does not store any personally identifiable information. Optional diagnostic logging (disabled by default) only stores anonymized, truncated IP addresses.

= What server requirements are needed? =

* PHP 7.4 or higher
* WordPress 5.6 or higher
* PHP `fsockopen()` enabled for TCP checks
* PHP `socket` extension for UDP/STUN checks
* Outbound connections to public STUN servers

= Why does NAT detection show "Unknown"? =

NAT detection relies on STUN servers and header analysis. If your server blocks outbound UDP traffic or uses complex proxy chains, detection may be incomplete. The plugin provides fallback suggestions in these cases.

= Can I customize the colors and layout? =

Yes! Go to Settings > NAT & Port Checker to adjust primary/secondary colors, background colors, text colors, font sizes, container width, and padding. All settings use CSS variables for instant updates.

= How do I add my own blog guides? =

In the admin panel, scroll to the "Useful Guides" section. You can add/edit/remove guide cards with custom titles, images, excerpts, and links to your blog posts.

= Does the plugin conflict with my theme? =

No. All plugin styles use `!important` flags and unique class prefixes (`cgnpc-`) to avoid conflicts. The plugin is tested with major WordPress themes.

= Can I use multiple shortcodes on one page? =

Yes. You can use any combination of shortcodes. Each shortcode loads assets only once to avoid duplication.

= How accurate is the NAT type detection? =

The plugin uses industry-standard STUN protocols combined with HTTP header analysis to provide medium-to-high confidence NAT classification. Results may vary based on network complexity.

== Screenshots ==

1. Full NAT & Port Checker interface with dark theme
2. NAT Type Checker with color-coded status
3. Port Checker with platform presets
4. Device & Connection Details panel
5. Router Configuration Tips section
6. Customizable Guides Cards
7. Admin settings panel
8. Theme toggle and font size controls

== Changelog ==

= 1.0.0 (2024) =
* Initial release
* NAT Type Checker with STUN detection
* Multi-platform port checking (TCP/UDP)
* Device information display
* Router tips and guides
* Dark/Light theme toggle
* 3 font size presets
* Admin settings panel
* Optional diagnostic logging
* Full accessibility support
* Responsive mobile design
* Auto-refresh NAT monitoring
* Copy-to-clipboard functionality

== Upgrade Notice ==

= 1.0.0 =
First stable release. Install and configure from Settings > NAT & Port Checker.

== Technical Details ==

**NAT Detection Method:**
* STUN Binding Tests (RFC 5389)
* Change-IP and Change-Port requests
* HTTP header chain analysis (X-Forwarded-For, etc.)
* Private IP range detection (RFC 1918)
* Carrier-grade NAT detection (100.64.0.0/10)
* Port mapping behavior analysis

**Port Checking Method:**
* TCP: `fsockopen()` with configurable timeouts
* UDP: PHP `socket` extension with STUN-like probes
* Multiple attempts (default: 3) for accuracy
* Latency measurement via microtime()
* Packet loss calculation

**Supported Platforms:**
* Xbox Cloud Gaming (Ports: 3074 UDP/TCP, 53, 80, 443)
* GeForce NOW (Ports: 47984-47989, 48010)
* PS Remote Play (Ports: 9295-9297, 987)
* Steam Link (Ports: 27031, 27036-27037)
* Shadow PC (Ports: 41182, 50036, 443)
* Amazon Luna / Stadia (Ports: 443, 3478, 44700)

**Device Detection:**
* Server-side: User-Agent parsing, HTTP headers
* Client-side: Navigator API, WebGL, Network Information API
* GPU: WebGL debug renderer
* Connection: Effective type, downlink, RTT

**Privacy & Security:**
* All AJAX endpoints protected with WordPress nonces
* Input sanitization via `sanitize_text_field()`, `esc_url_raw()`, etc.
* Output escaping via `esc_html()`, `esc_attr()`, `esc_url()`
* SQL injection prevention (no direct DB queries)
* XSS prevention (all user inputs escaped)
* IP truncation in logs (first two octets only)

**Performance:**
* Transient caching for geo-IP lookups (30 min)
* Assets loaded only when shortcodes present
* Minimal JavaScript bundle (~15KB minified)
* CSS variables for fast theme switching
* Debounced AJAX requests

**Browser Compatibility:**
* Chrome 90+
* Firefox 88+
* Safari 14+
* Edge 90+
* Mobile browsers (iOS 14+, Android 10+)

**Known Limitations:**
* UDP port checks may show "Unknown" for silent services
* STUN tests require outbound UDP (port 3478/19302) access
* Symmetric NAT may be misclassified without multiple STUN servers
* Client-side GPU/RAM detection depends on browser permissions
* IPv6-only networks may show partial results

== Support ==

For bug reports, feature requests, or customization inquiries:
* Email: support@your-cloud-gaming-blog.com
* GitHub: https://github.com/your-username/cloud-gaming-nat-port-checker
* Documentation: https://your-cloud-gaming-blog.com/docs

== Credits ==

* Developed by Your Name
* STUN protocol implementation based on RFC 5389
* Geo-IP lookups powered by ipapi.co
* Icons from Heroicons (MIT License)
* Inspired by network diagnostic tools for gamers worldwide

== License ==

This plugin is licensed under the GPLv2 or later.
https://www.gnu.org/licenses/gpl-2.0.html

Copyright (C) 2024 Your Name. All rights reserved.
