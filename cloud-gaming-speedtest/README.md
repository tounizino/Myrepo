# Cloud Gaming Speed Test WordPress Plugin

An advanced internet speed testing tool specifically designed for cloud gaming viewers. Features accurate backend-powered speed tests, customizable themes, and comprehensive gaming performance analysis.

## Features

✅ **Backend-Powered Testing** - Uses WordPress HTTP API for accurate speed measurements  
✅ **Three Color Themes** - Dark Neon, Sky Pulse, and Light Fusion  
✅ **Shortcode Support** - Easy embedding with `[cloud_gaming_speedtest]`  
✅ **Admin Dashboard** - Configure themes, resources, and content  
✅ **Animated UI** - Dynamic, engaging interface with real-time updates  
✅ **Performance Analysis** - Cloud gaming-specific recommendations  
✅ **Mobile Responsive** - Works perfectly on all devices  
✅ **Resource Links** - Editable helpful links from admin panel  
✅ **User Info Detection** - Shows IP, location, and ISP  

## Installation

### Method 1: Upload via WordPress Admin

1. Download the `cloud-gaming-speedtest` folder
2. Compress it into a ZIP file
3. Go to **WordPress Admin → Plugins → Add New**
4. Click **Upload Plugin** and select the ZIP file
5. Click **Install Now** then **Activate**

### Method 2: Manual Installation

1. Upload the `cloud-gaming-speedtest` folder to `/wp-content/plugins/`
2. Go to **WordPress Admin → Plugins**
3. Find "Cloud Gaming Speed Test" and click **Activate**

## Usage

### Basic Shortcode

```
[cloud_gaming_speedtest]
```

### With Theme Override

```
[cloud_gaming_speedtest theme="dark"]
[cloud_gaming_speedtest theme="sky"]
[cloud_gaming_speedtest theme="light"]
```

## Admin Settings

Navigate to **Cloud Speed Test** in the WordPress admin menu to configure:

### Theme Selection
Choose from three visual styles:
- **Dark Neon** - High contrast, esports-inspired aesthetic
- **Sky Pulse** - Energetic gradients with airy layout
- **Light Fusion** - Clean, professional interface

### Introductory Message
Customize the text that appears at the top of the speed tester.

### Helpful Resources
Add up to 8 custom resource links (e.g., cloud gaming services, optimization guides).

### Footer Note
Add a tip or support contact info displayed below resources.

## What It Tests

### Download Speed
Measures sustained bandwidth capacity for streaming game frames from the cloud.

### Upload Speed
Tests upstream capacity needed for controller inputs and multiplayer voice chat.

### Latency (Ping)
Calculates round-trip time - crucial for responsive cloud gaming input.

### Jitter
Measures connection stability variance that causes stuttering.

## Performance Tiers

The plugin automatically analyzes results and provides recommendations based on:

| Tier | Download | Upload | Latency | Jitter | Quality |
|------|----------|--------|---------|--------|---------|
| Excellent | 50+ Mbps | 10+ Mbps | <20ms | <5ms | 4K HDR 60fps |
| Great | 35+ Mbps | 8+ Mbps | <30ms | <10ms | 1440p 60fps |
| Good | 20+ Mbps | 6+ Mbps | <40ms | <15ms | 1080p 60fps |
| Limited | 10+ Mbps | 4+ Mbps | <60ms | <20ms | 720p 30fps |
| Not Ready | <10 Mbps | <4 Mbps | >60ms | >20ms | Insufficient |

## Design Philosophy

### Sharp & Modern
- No border-radius (sharp corners)
- No box-shadows (flat design)
- Strong borders for definition
- Clean geometric layouts

### Animated & Dynamic
- Pulsing gauge animations
- Progress bar transitions
- Smooth value counting
- Step highlighting
- Hover effects

### Performance-Focused
- Lightweight code
- Minimal dependencies
- Fast load times
- Efficient AJAX requests

## Browser Compatibility

✅ Chrome 80+  
✅ Firefox 75+  
✅ Safari 13+  
✅ Edge 80+  
✅ Opera 67+  

## Technical Details

### Backend Speed Testing
- Uses `wp_remote_get()` and `wp_remote_post()` for accurate measurements
- Tests against reliable CDN endpoints (Cloudflare, httpbin)
- Multiple samples for latency averaging
- Calculates jitter from latency variance

### AJAX Endpoints
- `cgst_test_latency` - Measures ping and jitter
- `cgst_test_download` - Tests download bandwidth
- `cgst_test_upload` - Tests upload bandwidth
- `cgst_analyze_results` - Provides gaming performance analysis
- `cgst_get_user_info` - Fetches IP and geolocation

### Security
- Nonce verification on all AJAX requests
- Capability checks for admin functions
- Input sanitization and validation
- XSS protection with `esc_*` functions

## Customization

### Overriding Styles
Add custom CSS to your theme:

```css
.cgst-component.cgst-theme-dark {
    /* Your custom dark theme styles */
}
```

### Filtering Resources Programmatically

```php
add_filter('cgst_default_resources', function($resources) {
    $resources[] = array(
        'title' => 'Custom Resource',
        'url' => 'https://example.com'
    );
    return $resources;
});
```

## FAQ

### Does it work with caching plugins?
Yes, the speed test uses AJAX and won't be cached. However, ensure AJAX requests aren't blocked by security plugins.

### Can I use multiple instances on one page?
Yes, you can use multiple shortcodes with different themes on the same page.

### Why do results vary between tests?
Speed tests depend on server load, network congestion, and ISP routing. Multiple tests give a better average.

### Can I test from the backend/server?
The AJAX handlers use WordPress HTTP API which tests from your server. The frontend also performs client-side checks for accuracy.

### Is it GDPR compliant?
Yes, the plugin only uses IP geolocation for display purposes and stores no personal data permanently.

## Troubleshooting

### Test won't start
- Check browser console for JavaScript errors
- Verify jQuery is loaded
- Ensure AJAX URL is accessible

### Inaccurate results
- Test from a wired connection when possible
- Close bandwidth-heavy applications
- Try testing at different times of day

### Admin settings not saving
- Check file permissions on WordPress
- Verify user has `manage_options` capability
- Check for PHP errors in debug log

## Changelog

### Version 1.0.0
- Initial release
- Backend-powered speed testing
- Three customizable themes
- Admin configuration panel
- Shortcode support
- Mobile responsive design

## Credits

Created with precision for cloud gaming communities.

## Support

For support inquiries, feature requests, or bug reports, please contact through your preferred support channel.

## License

GPL v2 or later - Same as WordPress core license.
