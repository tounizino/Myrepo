# Installation Guide

## WordPress Plugin Installation

### Method 1: WordPress Admin Dashboard

1. Navigate to **Plugins → Add New** in your WordPress admin
2. Click **Upload Plugin** at the top
3. Click **Choose File** and select the `internet-speed-device-info-tool.zip` file
4. Click **Install Now**
5. Once installed, click **Activate Plugin**
6. Go to **Speed Tool** in the sidebar to configure settings

### Method 2: FTP/Manual Installation

1. Extract the `internet-speed-device-info-tool` folder from the zip file
2. Upload the folder to `/wp-content/plugins/` on your server
3. Log in to your WordPress admin dashboard
4. Navigate to **Plugins → Installed Plugins**
5. Find "Internet Speed & Device Info Tool" and click **Activate**
6. Go to **Speed Tool** in the sidebar to configure settings

## Initial Configuration

After activation, configure your plugin:

1. Go to **Speed Tool** in WordPress admin
2. Review **Feature Toggles** - all features are enabled by default
3. Adjust **Speed Test Calibration** settings based on your hosting plan:
   - For shared hosting: Use default values (5 MB download, 3 MB upload)
   - For VPS/dedicated: Increase to 10-15 MB for more accuracy
4. (Optional) Configure **Geolocation & Map** settings:
   - Default IP API (ipapi.co) requires no configuration
   - Map tiles use OpenStreetMap by default (free)
5. Click **Save Settings**

## Adding to Your Pages

### Using Classic Editor

1. Edit or create a page/post
2. In the content editor, add one of these shortcodes:
   - `[speed_device_dark]` - Full tool, dark theme
   - `[speed_device_light]` - Full tool, light theme
   - `[speed_device_skyblue]` - Full tool, sky blue theme
   - `[speed_test_dark]` - Speed test only, dark theme
   - `[speed_test_skyblue]` - Speed test only, sky blue theme
3. Publish or update your page

### Using Gutenberg (Block Editor)

1. Edit or create a page/post
2. Click the **+** button to add a new block
3. Search for and select the **Shortcode** block
4. Paste one of the shortcodes listed above
5. Preview your page to see the tool in action
6. Publish or update your page

### Using Page Builders

#### Elementor
1. Add a **Shortcode** widget to your page
2. Paste the shortcode in the widget settings
3. Update and preview

#### WPBakery
1. Add a **Raw HTML** or **Custom HTML** element
2. Paste the shortcode
3. Update and preview

#### Divi
1. Add a **Code** module
2. Paste the shortcode
3. Update and preview

## Recommended Page Setup

### For Gaming Blogs

Create a dedicated "Connection Test" page:

```
Title: Test Your Gaming Connection
URL: /connection-test/

Content:
[speed_device_dark]

Below the shortcode, add:
- Explanation of what the speeds mean for gaming
- Recommended speeds for different game types
- Tips for improving connection quality
```

### For Tech Websites

Create multiple pages:

```
1. Full Diagnostics Page: [speed_device_light]
2. Quick Speed Check Widget: [speed_test_skyblue]
3. Embed in sidebar using WordPress widget area
```

## Advanced Customization

### Custom IP Lookup API

If you have your own IP API or prefer a different provider:

1. Go to **Speed Tool → Geolocation & Map**
2. In **IP Lookup Endpoint**, enter your API URL
3. Use `{ip}` placeholder if needed: `https://api.example.com/{ip}/json`
4. Save settings

Supported APIs:
- ipapi.co (default)
- ip-api.com
- ipgeolocation.io
- ipinfo.io
- Custom JSON endpoints

### Custom Map Tiles

To use different map tiles (e.g., Mapbox, CartoDB):

1. Go to **Speed Tool → Geolocation & Map**
2. Update **Map Tile URL**:
   - Mapbox: `https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={token}`
   - CartoDB: `https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png`
3. Update **Map Attribution** with provider requirements
4. Save settings

## Testing Your Installation

1. View a page with your shortcode
2. Click "Start Speed Test"
3. Verify all features work:
   - ✓ Download speed displays
   - ✓ Upload speed displays
   - ✓ Latency shows
   - ✓ IP information loads
   - ✓ Device info displays
   - ✓ Map renders with marker

## Troubleshooting

### Plugin Not Visible After Activation

- Ensure you're logged in as an administrator
- Check that WordPress version is 5.0 or higher
- Verify PHP version is 7.2 or higher
- Clear any caching plugins

### Shortcode Displays as Text

- Ensure the plugin is activated
- Check that you're using the correct shortcode format
- Try disabling other plugins temporarily to check for conflicts

### Speed Test Doesn't Start

1. Open browser developer console (F12)
2. Check for JavaScript errors
3. Verify that jQuery and plugin scripts are loading
4. Try disabling JavaScript optimization in caching plugins

### Map Not Showing

- Check browser console for Leaflet errors
- Ensure IP lookup is returning valid coordinates
- Test with default OpenStreetMap tiles first
- Verify no JavaScript conflicts with other plugins

### Slow Performance

- Reduce download/upload test sizes in settings
- Decrease test iterations to 1
- Use a CDN if available
- Check your hosting bandwidth limits

## Server Requirements

Minimum:
- WordPress 5.0+
- PHP 7.2+
- Apache or Nginx web server
- 64 MB PHP memory limit

Recommended:
- WordPress 6.0+
- PHP 8.0+
- 128 MB+ PHP memory limit
- HTTP/2 support
- SSL certificate (for accurate connection testing)

## Security Notes

- The plugin uses WordPress nonces for AJAX security
- All user inputs are sanitized
- No sensitive data is stored
- Upload tests use server-generated data streams
- Compatible with WordPress security plugins

## Support & Updates

- Check the plugin settings page for documentation
- Review the README.md for detailed feature explanations
- Test on a staging site before deploying to production
- Keep WordPress and the plugin updated

## Uninstallation

To remove the plugin:

1. Deactivate the plugin from **Plugins** page
2. Click **Delete** to remove all files
3. Plugin settings will be removed automatically

To keep settings for future reinstall:
- Only deactivate (don't delete)
- Settings remain in WordPress database

---

## Quick Start Summary

1. **Install & Activate** the plugin
2. **Configure** settings (or use defaults)
3. **Add shortcode** to your page: `[speed_device_dark]`
4. **Test** functionality on the frontend
5. **Customize** as needed

That's it! Your visitors can now test their internet speed and view their connection details.
