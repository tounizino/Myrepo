# Ultimate NAT & Port Checker WordPress Plugin

**Version:** 1.1.1  
**Author:** Your Name  
**License:** GPL v2 or later

## Overview

The **Ultimate NAT & Port Checker** is a cutting-edge WordPress plugin designed specifically for cloud gaming websites and tech blogs. It provides visitors with a comprehensive toolkit to diagnose and optimize their network configuration for online gaming, streaming, and peer-to-peer services.

## Key Features

### 🎮 Advanced NAT Intelligence
- **WebRTC-based NAT Detection**: Utilizes STUN servers and ICE candidate analysis to determine NAT type (Type 1/Open, Type 2/Moderate, Type 3/Strict)
- **Real-time IP Geolocation**: Displays public IP, city, country, and ISP information
- **Technical Breakdown**: Reveals ICE candidates, mapping behavior, filtering behavior, and port preservation details
- **Smart Recommendations**: Provides tailored configuration advice based on detected NAT type

### 🔌 Comprehensive Port Diagnostics
- **Batch Port Scanning**: Check up to 40 ports in a single request
- **Protocol Support**: Tests both TCP and UDP connections
- **Cloud Gaming Presets**: One-click port configurations for:
  - PlayStation Network
  - Xbox Network & Game Pass
  - NVIDIA GeForce NOW
  - PlayStation Remote Play
  - Generic Cloud Gaming Services
- **Port Range Support**: Accepts formats like `3478-3480`, `80,443`, `9295/udp`
- **Response Time Metrics**: Measures and displays connection latency

### 🛠️ Router Configuration Resources
- **Popular Router Login Shortcuts**: Pre-configured login details for:
  - Asus Gaming Series
  - Netgear Nighthawk
  - TP-Link Archer
  - Linksys Velop
- **Quick Guides**: Collapsible sections covering:
  - Open NAT Playbook
  - Port Forwarding Blueprint
  - DSCP Tagging & QoS Enhancements

### 📱 Device Information Snapshot
- Browser and version detection
- Operating system identification
- Connection type (4G, 5G, WiFi)
- Estimated bandwidth
- Screen resolution
- Hardware thread count

### 🎨 Modern 2026 Design
- **Dual Theme Support**: Light and dark modes with persistent localStorage
- **Responsive Layout**: Fully optimized for desktop, tablet, and mobile
- **Transparent Background**: Blends seamlessly with any website theme
- **Smooth Animations**: Configurable animation speed (slow, normal, fast)
- **Compact Typography**: Space-efficient font sizing for dense information display
- **Accessible**: Proper ARIA labels, semantic HTML, keyboard navigation support

### ⚙️ Powerful Admin Panel
Located at **Settings → NAT & Port Checker**, the admin panel allows you to:
- Toggle between light and dark default themes
- Customize the accent color
- Edit the tool title and subtitle directly from the dashboard
- Choose the global font scale preset (Compact, Standard, Comfort)
- Set container width (768px–2400px)
- Customize light and dark theme background colors
- Enable/disable individual feature modules
- Configure custom useful links (2 links, stacked vertically)
- Add a custom quick tip message
- Adjust API timeout duration

## Installation

1. Download or clone this repository into your WordPress `wp-content/plugins/` directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/yourusername/ultimate-nat-port-checker.git
   ```

2. Activate the plugin through the **Plugins** menu in WordPress.

3. Navigate to **Settings → NAT & Port Checker** to configure plugin options.

4. Add the shortcode `[nat_port_checker]` to any page or post where you want the tool to appear.

## Shortcode Usage

```php
[nat_port_checker]
[nat_checker_only]
[port_checker_only]
```

- `[nat_port_checker]` renders the complete toolkit with every module enabled according to your settings.
- `[nat_checker_only]` renders only the NAT Intelligence module (ideal for compact diagnostics pages).
- `[port_checker_only]` renders only the Port Availability Suite (perfect for quick port verification pages).

All shortcodes respect the global settings configured in the admin panel.

## Technical Stack

- **Frontend**: Vanilla JavaScript (ES6+), modern CSS (CSS Grid, Flexbox, CSS Custom Properties)
- **Backend**: PHP 7.4+, WordPress REST API
- **NAT Detection**: WebRTC (RTCPeerConnection, STUN servers)
- **Port Scanning**: PHP `fsockopen()` for TCP, `stream_socket_client()` for UDP
- **Geolocation**: Integration with ipapi.co and ip-api.com
- **Browser Compatibility**: Modern browsers with WebRTC support (Chrome, Firefox, Edge, Safari)

## Architecture

```
ultimate-nat-port-checker/
├── assets/
│   ├── css/
│   │   └── ultimate-nat-port-checker.css
│   └── js/
│       └── ultimate-nat-port-checker.js
├── includes/
│   ├── class-unpc-admin.php
│   ├── class-unpc-ajax.php
│   ├── class-unpc-core.php
│   └── template-checker.php
├── ultimate-nat-port-checker.php
└── README.md
```

### File Descriptions

- **ultimate-nat-port-checker.php**: Main plugin file, registers activation hooks and shortcode
- **class-unpc-core.php**: Core functionality, asset enqueuing, and port presets
- **class-unpc-admin.php**: Admin settings page and options management
- **class-unpc-ajax.php**: REST API endpoints for port checking
- **template-checker.php**: Front-end HTML template
- **ultimate-nat-port-checker.css**: Comprehensive styling with theme support
- **ultimate-nat-port-checker.js**: Frontend logic, WebRTC NAT detection, and interactive UI

## Configuration

### Default Settings

The plugin ships with sensible defaults:

- **Theme**: Light
- **Accent Color**: `#3a7afe`
- **Animation Speed**: Normal
- **Container Width**: 1400px
- **API Timeout**: 10 seconds
- **All Modules**: Enabled

### Customizing Port Presets

Developers can add custom port presets using the `unpc_port_presets` filter:

```php
add_filter('unpc_port_presets', function($presets) {
    $presets[] = array(
        'id' => 'custom-service',
        'label' => __('Custom Gaming Service', 'your-text-domain'),
        'description' => __('Description of the service', 'your-text-domain'),
        'entries' => array(
            array('ports' => '8000-8010', 'protocol' => 'tcp'),
            array('ports' => '9000', 'protocol' => 'udp'),
        ),
    );
    return $presets;
});
```

### Forcing Asset Enqueue

If you need to load plugin assets on all pages (not just those with the shortcode):

```php
add_filter('unpc_force_enqueue_assets', '__return_true');
```

## Browser Support

| Browser | Minimum Version |
|---------|----------------|
| Chrome  | 90+            |
| Firefox | 88+            |
| Edge    | 90+            |
| Safari  | 14+            |
| Opera   | 76+            |

**Note**: WebRTC support is required for NAT detection. Port checking works in all browsers with JavaScript enabled.

## Security

- All REST API endpoints are nonce-verified
- User inputs are sanitized and validated
- No database writes from frontend
- Follows WordPress coding standards
- CSRF protection via `wp_verify_nonce()`

## Performance

- Assets are conditionally loaded only on pages with the shortcode
- Geo-IP data is cached with transients (1 hour)
- Minimal JavaScript footprint (~20KB minified)
- CSS uses modern techniques for optimal rendering
- No external dependencies (jQuery-free)

## Accessibility

- Semantic HTML5 structure
- ARIA labels and roles
- Keyboard-navigable
- Focus indicators
- Reduced motion support via `prefers-reduced-motion`

## Troubleshooting

### NAT Check Fails

- **Issue**: "No ICE candidates discovered"
  - **Solution**: User's firewall or VPN may be blocking STUN requests. Try disabling VPN or adjusting firewall rules.

- **Issue**: WebRTC not supported
  - **Solution**: Upgrade to a modern browser (Chrome, Firefox, Edge, Safari 14+).

### Port Check Fails

- **Issue**: All ports show as closed
  - **Solution**: Ensure the target host/IP is correct and that ports are actually open. Server-side port scanning may be blocked by hosting provider.

- **Issue**: Port check times out
  - **Solution**: Increase the API timeout in admin settings (Settings → NAT & Port Checker → API Timeout).

### Styling Issues

- **Issue**: Container too wide on mobile
  - **Solution**: The plugin is fully responsive. Check for theme CSS conflicts. Try adjusting container width in settings.

- **Issue**: Dark mode not working
  - **Solution**: Clear browser cache and localStorage. Ensure JavaScript is enabled.

## Changelog

### Version 1.0.0 (2024)
- Initial release
- WebRTC NAT detection with STUN servers
- Comprehensive port checking (TCP & UDP)
- Cloud gaming port presets
- Router login shortcuts
- Device information detection
- Light/dark theme support
- Fully responsive and mobile-friendly
- Admin settings panel
- WordPress REST API integration

## Roadmap

- [ ] IPv6 support
- [ ] Upnp/NAT-PMP detection
- [ ] Network speed test integration
- [ ] Packet loss and jitter measurements
- [ ] Export results as PDF/JSON
- [ ] Multi-language support (WPML/Polylang)
- [ ] Gutenberg block variant

## Support

For bug reports, feature requests, or general support:
- GitHub Issues: https://github.com/yourusername/ultimate-nat-port-checker/issues
- Email: support@example.com

## Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This plugin is licensed under the GNU General Public License v2 or later.

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## Credits

- **STUN Servers**: Google Public STUN
- **Geo-IP Data**: ipapi.co and ip-api.com
- **Icon/Design**: Custom 2026 design language

---

**Made with ❤️ for cloud gaming enthusiasts**
