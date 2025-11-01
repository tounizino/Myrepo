# Cloud Gaming NAT & Port Checker - Developer Notes

## Plugin Overview

A professional, premium WordPress plugin for cloud gaming blogs that provides real-time NAT type detection, port checking, and network diagnostics. Built with modern 2026 design principles, complete accessibility, and robust security practices.

## Architecture

### Main Components

1. **Main Plugin File** (`cloud-gaming-nat-port-checker.php`)
   - Plugin initialization and hooks
   - AJAX endpoint registration
   - Asset enqueuing
   - Activation/deactivation handlers

2. **Admin Settings** (`includes/class-admin-settings.php`)
   - WordPress Settings API integration
   - Color customization (6 color controls)
   - Font size presets (small/medium/large)
   - Guide card management (CRUD operations)
   - Diagnostic logging controls
   - Media uploader integration

3. **NAT Checker** (`includes/class-nat-checker.php`)
   - STUN protocol implementation (RFC 5389)
   - Binding tests with change-IP/change-port requests
   - Symmetric vs full-cone NAT detection
   - IPv4/IPv6 support
   - Carrier-grade NAT (CGN) detection
   - Confidence scoring system
   - Geo-IP lookup with transient caching

4. **Port Checker** (`includes/class-port-checker.php`)
   - TCP port scanning using `fsockopen()`
   - UDP port testing with PHP sockets
   - Multi-attempt verification (configurable)
   - Latency measurement
   - Packet loss calculation
   - Platform presets (Xbox, GeForce NOW, PS Remote Play, etc.)

5. **Device Detector** (`includes/class-device-detector.php`)
   - Server-side: User-Agent parsing, HTTP headers
   - Client-side (via JS): WebGL GPU, Navigator API, Network Information API
   - Browser/OS detection
   - Device type classification

6. **Shortcodes** (`includes/class-shortcodes.php`)
   - Three shortcodes: full checker, NAT only, port only
   - Dynamic HTML rendering
   - CSS variable injection
   - Asset management (prevent duplicate loading)

### Frontend

- **CSS** (`assets/css/frontend.css`)
  - Modern 2026 design (flat, no shadows)
  - CSS Grid & Flexbox layouts
  - Dark/light theme support via data attributes
  - Responsive breakpoints (<768px for mobile)
  - Animation keyframes (fade-in, spin, pulse)
  - All rules use `!important` to override theme conflicts

- **JavaScript** (`assets/js/frontend.js`)
  - jQuery-based event handling
  - AJAX requests with Promise wrappers
  - LocalStorage for theme/font preferences
  - Auto-refresh timer (30s interval)
  - Client-side device detection (GPU, RAM, CPU, connection type)
  - Copy-to-clipboard functionality
  - Error handling with inline messages

### Admin

- **CSS** (`assets/css/admin.css`)
  - Clean admin panel styling
  - Grid layout for shortcode cards
  - Guide item management UI
  - Color picker integration

- **JavaScript** (`assets/js/admin.js`)
  - `wp.wpColorPicker` integration
  - Dynamic guide field addition/removal
  - Re-indexing after deletions
  - WordPress media uploader integration

## Key Features

### NAT Detection Logic

1. **Detection Methods**:
   - STUN Binding Test (primary)
   - HTTP Header Analysis (X-Forwarded-For chain)
   - Private IP Range Check (RFC 1918)
   - Carrier-Grade NAT Check (100.64.0.0/10)
   - Port Mapping Behavior

2. **Classification**:
   - **Open (Type 1)**: Native IPv6 OR full-cone NAT detected via STUN
   - **Moderate (Type 2)**: Restricted cone OR public IPv4 without proxies
   - **Strict (Type 3)**: Private/CGN IP OR multiple proxy hops OR symmetric NAT

3. **Fallback Strategy**:
   - If STUN fails → heuristic based on headers
   - If all fail → suggest manual console/router tests

### Port Checking Logic

1. **TCP**: `fsockopen()` with 2.5s timeout, 3 attempts
2. **UDP**: PHP socket with 2s timeout, probes with payload
3. **Multi-attempt**: Average latency, packet loss %
4. **Edge Cases**:
   - UDP "unknown" status (connectionless nature)
   - Firewall vs closed port distinction

### Security Measures

- **Nonce Verification**: All AJAX endpoints check `cgnpc_nonce`
- **Input Sanitization**: `sanitize_text_field()`, `intval()`, `esc_url_raw()`
- **Output Escaping**: `esc_html()`, `esc_attr()`, `esc_url()`, `esc_js()`
- **SQL Injection Prevention**: No direct database queries (uses WP functions)
- **XSS Prevention**: All user inputs escaped before rendering
- **IP Truncation**: Logs store only first two octets (e.g., `192.168.xxx.xxx`)

### Performance Optimizations

- **Transient Caching**: Geo-IP results cached for 30 minutes
- **Conditional Asset Loading**: Scripts/styles only load on pages with shortcodes
- **Single Localization**: `cgnpcData` localized once per page load
- **Debounced Requests**: Button states prevent rapid-fire AJAX calls
- **Minimal Bundle**: CSS ~12KB, JS ~15KB (unminified)

### Accessibility (WCAG 2.1)

- **ARIA Labels**: All interactive elements
- **Keyboard Navigation**: Focus states, tab order
- **Semantic HTML**: `<section>`, `<header>`, `<footer>`, `<article>`
- **Color Contrast**: WCAG AA compliant (dark mode default)
- **Screen Reader**: `aria-live` regions, `role="alert"` for errors

## Customization Guide

### Changing Colors

Navigate to **Settings > NAT & Port Checker**:
- Primary Color: Accent (buttons, links, highlights)
- Secondary Color: Gradients, secondary accents
- Dark Background: Main container background
- Light Background: Light theme container
- Dark Text: Text color on dark backgrounds
- Light Text: Text color on light backgrounds

All colors use CSS variables (`--cgnpc-primary`, etc.) for instant updates.

### Adding Custom Port Presets

Edit `includes/class-port-checker.php`:

```php
public static function get_platform_presets() {
    return array(
        'custom_platform' => array(
            'label' => __('My Custom Platform', 'cloud-nat-port-checker'),
            'ports' => array(
                array('port' => 12345, 'protocol' => 'tcp', 'label' => 'Main Port'),
                array('port' => 54321, 'protocol' => 'udp', 'label' => 'Secondary'),
            ),
        ),
        // ... existing presets
    );
}
```

### Extending NAT Detection

To add custom STUN servers, edit `includes/class-nat-checker.php`:

```php
private $stun_servers = array(
    array('host' => 'stun.example.com', 'port' => 3478),
    // ... existing servers
);
```

### Custom Device Detection

Add to `includes/class-device-detector.php`:

```php
public function get_device_info() {
    $info = array(
        // ... existing fields
        'custom_field' => $this->detect_custom_field(),
    );
    return $info;
}
```

## Shortcode Usage

### Full Checker
```
[cloud_nat_port_checker theme="dark"]
```

### NAT Only
```
[cloud_nat_checker]
```

### Port Only
```
[cloud_port_checker]
```

All shortcodes:
- Auto-enqueue assets
- Support theme toggle
- Respect admin settings
- Work in posts/pages/widgets
- No theme conflicts

## Troubleshooting

### "NAT shows Unknown"
- Check if server blocks outbound UDP (STUN requires port 3478/19302)
- Verify PHP `socket` extension is enabled (`php -m | grep socket`)
- Test from different network (VPN may interfere)

### "Port always shows Closed"
- Verify `fsockopen()` is not disabled in `php.ini` (`disable_functions`)
- Check server firewall rules (outbound connections)
- Use `tcp` for local testing (UDP requires open listening services)

### "Device info missing GPU/RAM"
- Client-side detection depends on browser APIs (Chrome/Edge best support)
- Privacy settings may block WebGL or Navigator.deviceMemory
- Safari limits some Navigator APIs

### "Admin color picker not working"
- Ensure `wp-color-picker` script is enqueued
- Check for JavaScript conflicts (browser console)
- Try disabling other plugins temporarily

## Future Enhancements

### Planned Features
- IPv6-specific STUN tests
- Traceroute visualization
- QoS bandwidth testing
- Multi-language support (WPML/Polylang)
- REST API endpoints
- Gutenberg block

### Code Improvements
- Unit tests (PHPUnit)
- E2E tests (Playwright)
- Minified/compiled assets (Webpack/Vite)
- TypeScript migration for JS
- PHP 8.1+ strict types

## Support & Contributions

### Bug Reports
- GitHub Issues: (link to repo)
- Include: WP version, PHP version, browser, error messages

### Pull Requests
- Follow WordPress Coding Standards
- Add PHPDoc blocks
- Test on multiple themes
- Ensure backward compatibility

## License

GPL v2 or later. See LICENSE file.

## Credits

- Developed by: Your Name
- STUN Protocol: RFC 5389
- Geo-IP: ipapi.co
- Icons: Heroicons (MIT)

---

**Version**: 1.0.0  
**Last Updated**: 2024  
**WordPress Compatibility**: 5.6+  
**PHP Compatibility**: 7.4+
