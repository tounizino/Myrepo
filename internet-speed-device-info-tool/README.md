# Internet Speed & Device Info Tool

A comprehensive WordPress plugin featuring internet speed testing, IP lookup, device information detection, and interactive mapping capabilities. Perfect for cloud gaming blogs and tech websites.

## Features

- **Internet Speed Testing**: Measure download and upload speeds with configurable test parameters
- **Latency & Jitter Detection**: Comprehensive network performance metrics
- **IP Address Lookup**: Automatic geolocation with country, region, city, ISP, and timezone information
- **Device Information**: Browser, OS, screen resolution, CPU, memory, and connection type detection
- **Interactive Maps**: Leaflet-powered location mapping with customizable tile providers
- **Multiple Themes**: Dark, Light, and Sky Blue color schemes
- **Responsive Design**: Fully mobile-friendly and optimized for all screen sizes
- **WordPress-Friendly**: Easy shortcode integration with Gutenberg support
- **SEO Optimized**: Semantic HTML and performance-optimized assets

## Installation

1. Download the plugin folder `internet-speed-device-info-tool`
2. Upload it to your WordPress `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure settings via **Speed Tool** in the WordPress admin menu

## Shortcodes

### Full Tool (All Features)

```
[speed_device_dark]      - Full tool with dark theme
[speed_device_light]     - Full tool with light theme
[speed_device_skyblue]   - Full tool with sky blue theme
```

### Speed Test Only

```
[speed_test_dark]        - Speed test only with dark theme
[speed_test_skyblue]     - Speed test only with sky blue theme
```

## Configuration

The plugin provides an admin settings page with the following options:

### Feature Toggles
- Enable/disable speed testing
- Enable/disable IP lookup
- Enable/disable device information
- Enable/disable location mapping

### Speed Test Calibration
- **Download Chunk Size**: 0.5 - 50 MB (default: 5 MB)
- **Upload Payload Size**: 0.25 - 20 MB (default: 3 MB)
- **Test Iterations**: 1 - 5 (default: 2)
- **Iteration Timeout**: 3000 - 60000 ms (default: 10000 ms)

### Geolocation & Mapping
- **IP Lookup Endpoint**: Configure custom IP geolocation API
- **Map Tile URL**: Leaflet tile provider URL
- **Map Attribution**: Map provider attribution text

## Technical Specifications

### PHP Requirements
- WordPress 5.0 or higher
- PHP 7.2 or higher

### JavaScript Features
- ES6+ with async/await support
- Fetch API for network requests
- Performance API for accurate timing
- Crypto API for secure random data generation

### Dependencies
- **Leaflet.js** (1.9.4) - Map rendering (loaded from CDN)
- **jQuery** - DOM manipulation (bundled with WordPress)

### IP Lookup Services
Default: [ipapi.co](https://ipapi.co) (no API key required for basic usage)

Supports custom endpoints:
- ipapi.co
- ip-api.com
- ipgeolocation.io (with API key)
- Any JSON-based IP geolocation API

## Usage Examples

### Basic Implementation
```
[speed_device_dark]
```

### Speed Test Only Widget
```
[speed_test_skyblue]
```

### Multiple Instances
You can add multiple shortcodes to different pages:
- Gaming page: `[speed_device_dark]`
- Help center: `[speed_test_light]`
- Technical specs: `[speed_device_skyblue]`

## Customization

### Theme Colors
The plugin includes three pre-built themes:
- **Dark Theme**: Deep navy with blue accents
- **Light Theme**: Clean white with purple gradients  
- **Sky Blue Theme**: Vibrant cyan with sky blue tones

### CSS Customization
Override plugin styles by adding custom CSS to your theme:

```css
.isdit-wrapper.theme-dark {
    /* Your custom dark theme styles */
}
```

## Performance Considerations

- Assets are loaded only when shortcodes are present
- Optimized for low bandwidth with configurable test sizes
- Progressive enhancement for older browsers
- Efficient caching and minimal DOM manipulation

## Security Features

- WordPress nonce verification for all AJAX requests
- Sanitized user inputs and settings
- Secure random data generation for upload tests
- XSS protection with proper escaping

## Browser Compatibility

- Chrome/Edge (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Troubleshooting

### Map Not Displaying
- Ensure Leaflet is loading (check browser console)
- Verify IP lookup is returning valid coordinates
- Check for JavaScript conflicts with other plugins

### Speed Test Not Starting
- Check browser console for errors
- Verify WordPress AJAX is functioning
- Ensure nonce validation is passing

### Inaccurate Speed Results
- Increase test duration in settings
- Use larger chunk sizes for high-speed connections
- Run multiple test iterations

## Support

For issues, feature requests, or contributions:
- Report bugs via plugin support forum
- Check documentation at plugin settings page
- Review console logs for JavaScript errors

## Changelog

### Version 1.0.0
- Initial release
- Internet speed testing (download/upload)
- Latency and jitter measurement
- IP geolocation lookup
- Device information detection
- Interactive Leaflet maps
- Three theme variations
- Full WordPress admin integration
- Responsive design
- SEO optimization

## License

GPL v2 or later

## Credits

- **Leaflet**: Open-source mapping library
- **OpenStreetMap**: Default map tile provider
- **ipapi.co**: IP geolocation service

## Author

Developed for cloud gaming blogs and high-performance websites.
