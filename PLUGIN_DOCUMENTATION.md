# Internet Speed & Device Info Tool - Complete Documentation

## Overview

This is a professional-grade WordPress plugin designed for cloud gaming blogs and tech websites. It provides comprehensive internet speed testing, IP geolocation, device information detection, and interactive mapping capabilities.

## 🎯 Features

### 1. Internet Speed Testing
- **Download Speed**: Measures actual throughput using configurable data chunks
- **Upload Speed**: Tests upload bandwidth with secure payload transmission
- **Latency Detection**: Multi-sample ping testing with average calculation
- **Jitter Measurement**: Network stability analysis
- **Configurable Parameters**: Adjustable test sizes, iterations, and timeouts

### 2. IP Address Lookup
- Automatic client IP detection (supports proxies and load balancers)
- Geolocation data: Country, region, city, coordinates
- ISP and organization identification
- Timezone detection
- Postal code information
- Configurable API endpoints

### 3. Device Information
- **Device Type**: Desktop, Mobile, Tablet detection
- **Operating System**: Windows, macOS, Linux, iOS, Android
- **Browser**: Chrome, Firefox, Safari, Edge, Opera
- **Screen Resolution**: Physical display dimensions
- **Viewport Size**: Current browser window size
- **Hardware**: CPU thread count, RAM capacity
- **Connection**: Network type and speed capabilities
- **User Agent**: Full browser identification string

### 4. Interactive Mapping
- Leaflet-powered interactive maps
- Automatic location pinpointing
- Customizable map tiles (OpenStreetMap default)
- Configurable attribution
- Responsive map sizing

### 5. Multiple Themes
- **Dark Theme**: Professional dark mode with blue accents
- **Light Theme**: Clean light design with purple gradients
- **Sky Blue Theme**: Vibrant cyan color scheme

### 6. WordPress Integration
- 5 shortcode variations for different use cases
- Admin settings page with comprehensive options
- Feature toggles for selective functionality
- SEO-friendly semantic HTML
- Responsive design for all screen sizes
- Compatible with page builders (Elementor, Divi, WPBakery)

## 📋 Shortcodes

### Full Tool (All Features)

```php
[speed_device_dark]      // Dark theme with all features
[speed_device_light]     // Light theme with all features
[speed_device_skyblue]   // Sky blue theme with all features
```

### Speed Test Only

```php
[speed_test_dark]        // Speed test only, dark theme
[speed_test_skyblue]     // Speed test only, sky blue theme
```

## ⚙️ Configuration Options

### Feature Toggles
- **Enable Speed Test**: Turn on/off speed testing functionality
- **Enable IP Lookup**: Control IP geolocation display
- **Enable Device Info**: Toggle device information section
- **Enable Location Map**: Show/hide interactive map

### Speed Test Calibration
- **Download Chunk Size**: 0.5 - 50 MB (default: 5 MB)
  - Smaller values for low bandwidth
  - Larger values for high-speed connections
- **Upload Payload Size**: 0.25 - 20 MB (default: 3 MB)
  - Adjust based on server upload limits
- **Test Iterations**: 1 - 5 (default: 2)
  - More iterations = better accuracy but longer test time
- **Iteration Timeout**: 3000 - 60000 ms (default: 10000 ms)
  - Maximum time per measurement

### Geolocation & Map
- **IP Lookup Endpoint**: Custom API URL
  - Default: https://ipapi.co/json/
  - Supports: ip-api.com, ipgeolocation.io, ipinfo.io
  - Use `{ip}` placeholder for dynamic IP insertion
- **Map Tile URL**: Leaflet tile provider
  - Default: OpenStreetMap
  - Alternatives: Mapbox, CartoDB, Stamen
- **Map Attribution**: Provider credit text (HTML allowed)

## 🔧 Technical Specifications

### System Requirements
- **WordPress**: 5.0 or higher
- **PHP**: 7.2 or higher
- **MySQL**: 5.6 or higher (WordPress requirement)
- **Server**: Apache or Nginx with mod_rewrite

### Browser Compatibility
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari 14+, Chrome Mobile 90+)

### Performance
- **Asset Loading**: Conditional (only when shortcodes present)
- **File Sizes**:
  - CSS: ~14 KB (minified: ~10 KB)
  - JavaScript: ~18 KB (minified: ~12 KB)
  - Leaflet: ~150 KB (CDN-loaded)
- **HTTP Requests**: 4-5 (styles, scripts, Leaflet)
- **Database**: Minimal (settings only, ~2 KB)

### Dependencies
- **Leaflet.js**: 1.9.4 (CDN: unpkg.com)
- **jQuery**: Bundled with WordPress
- **IP API**: ipapi.co (free tier, no key required)

## 🏗️ File Structure

```
internet-speed-device-info-tool/
│
├── internet-speed-device-info-tool.php    # Main plugin file (28 KB)
│   ├── Plugin registration & activation
│   ├── Settings API integration
│   ├── Shortcode handlers
│   └── Asset management
│
├── includes/
│   ├── class-api-handler.php              # AJAX endpoints (7 KB)
│   │   ├── IP information retrieval
│   │   ├── Download speed endpoint
│   │   └── Upload speed endpoint
│   │
│   └── template-tool.php                  # Frontend HTML (4 KB)
│       └── Conditional feature rendering
│
├── assets/
│   ├── css/
│   │   ├── styles.css                     # Frontend styles (14 KB)
│   │   │   ├── Three theme variations
│   │   │   ├── Responsive breakpoints
│   │   │   └── Smooth animations
│   │   │
│   │   └── admin.css                      # Admin page styles (1 KB)
│   │       └── Settings page layout
│   │
│   └── js/
│       ├── speedtest.js                   # Speed test library (9 KB)
│       │   ├── SpeedTest class
│       │   ├── Download/upload tests
│       │   ├── Latency measurement
│       │   └── Device detection
│       │
│       └── main.js                        # Frontend controller (7 KB)
│           ├── Widget initialization
│           ├── Map rendering
│           ├── UI updates
│           └── Event handling
│
├── README.md                              # User documentation (6 KB)
├── INSTALL.md                             # Installation guide (7 KB)
└── PLUGIN_DOCUMENTATION.md                # This file
```

## 🔐 Security Features

### Data Protection
- **Nonce Verification**: All AJAX requests validated
- **Input Sanitization**: User inputs cleaned before saving
- **Output Escaping**: All dynamic content escaped
- **SQL Safety**: Uses WordPress database abstraction

### Best Practices
- No SQL queries (uses WordPress APIs)
- No file writes (except plugin activation)
- No external includes (except CDN assets)
- Capability checks for admin actions
- CSRF protection via nonces

## 🎨 Theme Customization

### CSS Variables
```css
:root {
    --isdit-primary: #2c7be5;
    --isdit-secondary: #1c3faa;
    --isdit-text: #1a1a1a;
    --isdit-light: #f7f9fc;
    --isdit-dark: #0f172a;
    --isdit-muted: #64748b;
}
```

### Custom Theme Example
```css
/* Add to your theme's style.css */
.isdit-wrapper.theme-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.isdit-wrapper.theme-custom .isdit-header {
    background: rgba(102, 126, 234, 0.9);
}

.isdit-wrapper.theme-custom .isdit-content {
    background: rgba(255, 255, 255, 0.95);
}
```

## 📊 API Endpoints

### WordPress AJAX Actions

#### 1. Get IP Information
```
Action: isdit_get_ip_info
Method: POST
Parameters: nonce (string)
Response: {
    success: true,
    data: {
        ip: "xxx.xxx.xxx.xxx",
        location: {
            country: "United States",
            region: "California",
            city: "San Francisco",
            latitude: 37.7749,
            longitude: -122.4194,
            timezone: "America/Los_Angeles",
            isp: "Example ISP",
            organization: "Example Org"
        }
    }
}
```

#### 2. Download Speed Test
```
Action: isdit_speed_test_download
Method: GET
Parameters: 
    - nonce (string)
    - size (float) - MB to download
Response: Binary stream of specified size
```

#### 3. Upload Speed Test
```
Action: isdit_speed_test_upload
Method: POST
Parameters: nonce (string)
Body: Binary data
Response: {
    success: true,
    data: {
        received: 5242880,
        limit: 20971520,
        timestamp: 1234567890.123
    }
}
```

## 🧪 Testing

### Manual Testing Checklist

**Installation**
- [ ] Plugin activates without errors
- [ ] Settings page accessible
- [ ] Default settings populated

**Frontend Display**
- [ ] Shortcode renders correctly
- [ ] Three themes display properly
- [ ] Responsive on mobile devices
- [ ] No JavaScript console errors

**Speed Test**
- [ ] Download test completes
- [ ] Upload test completes
- [ ] Latency displays
- [ ] Jitter calculates
- [ ] Progress bar animates
- [ ] Status messages update

**IP Lookup**
- [ ] IP address displays
- [ ] Country/region/city show
- [ ] ISP information loads
- [ ] Timezone correct

**Device Info**
- [ ] Device type accurate
- [ ] OS detected correctly
- [ ] Browser identified
- [ ] Screen resolution shown
- [ ] CPU/memory displayed

**Map**
- [ ] Leaflet loads
- [ ] Marker placed correctly
- [ ] Tiles render
- [ ] Attribution shown
- [ ] Zoom/pan work

**Admin Settings**
- [ ] All settings save
- [ ] Validation works
- [ ] Shortcodes listed
- [ ] Documentation accessible

## 🐛 Troubleshooting

### Common Issues

**Issue**: Map doesn't display
**Solution**: 
1. Check browser console for Leaflet errors
2. Verify IP returns valid coordinates
3. Ensure no JavaScript conflicts
4. Test with default tile URL

**Issue**: Speed test times out
**Solution**:
1. Reduce download/upload sizes
2. Increase timeout duration
3. Check server PHP limits
4. Verify AJAX endpoint accessible

**Issue**: Inaccurate speeds
**Solution**:
1. Increase test iterations
2. Use larger chunk sizes
3. Test during off-peak hours
4. Ensure stable connection

**Issue**: Plugin conflicts
**Solution**:
1. Disable other plugins temporarily
2. Check for jQuery conflicts
3. Clear all caches
4. Test with default theme

## 📈 Performance Optimization

### For High-Traffic Sites
```php
// Recommended settings:
Download Size: 10 MB
Upload Size: 5 MB
Iterations: 3
Timeout: 15000 ms
```

### For Shared Hosting
```php
// Recommended settings:
Download Size: 3 MB
Upload Size: 1 MB
Iterations: 2
Timeout: 10000 ms
```

### CDN Configuration
- Enable CDN for plugin assets
- Cache CSS/JS files
- Use HTTP/2 for parallel loading
- Enable gzip compression

## 🌍 Localization

The plugin is translation-ready with text domain `isdit`.

### Available Strings
- Settings page labels
- Frontend UI elements
- Error messages
- Status updates

### Translation Files
Place `.po` and `.mo` files in:
```
/wp-content/languages/plugins/isdit-{locale}.mo
```

## 🔄 Changelog

### Version 1.0.0 (Current)
- ✨ Initial release
- ⚡ Internet speed testing (download/upload)
- 📍 IP geolocation with mapping
- 💻 Comprehensive device information
- 🎨 Three theme variations
- ⚙️ Full admin configuration
- 📱 Responsive design
- ♿ SEO optimized

## 📝 License

GPL v2 or later - Compatible with WordPress license

## 👥 Credits

- **Leaflet**: Open-source JavaScript mapping library
- **OpenStreetMap**: Free map tile provider
- **ipapi.co**: IP geolocation service

## 📞 Support

For issues or questions:
1. Check this documentation
2. Review INSTALL.md for setup help
3. Test with default settings
4. Check browser console for errors
5. Verify WordPress/PHP requirements

---

**Version**: 1.0.0  
**Last Updated**: 2024  
**Minimum WordPress**: 5.0  
**Tested Up To**: 6.4  
**PHP Version**: 7.2+
