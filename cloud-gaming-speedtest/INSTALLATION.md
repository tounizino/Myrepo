# Cloud Gaming Speed Test - Installation Guide

## Quick Start

### Step 1: Upload the Plugin

#### Option A: Via WordPress Admin (Recommended)
1. Zip the entire `cloud-gaming-speedtest` folder
2. Go to your WordPress admin panel
3. Navigate to **Plugins → Add New**
4. Click **Upload Plugin** at the top
5. Choose the ZIP file and click **Install Now**
6. Click **Activate Plugin**

#### Option B: Via FTP/File Manager
1. Upload the entire `cloud-gaming-speedtest` folder to `/wp-content/plugins/`
2. Go to **Plugins** in WordPress admin
3. Find "Cloud Gaming Speed Test" and click **Activate**

### Step 2: Configure Settings

1. After activation, go to **Cloud Speed Test** in the admin menu
2. **Choose your preferred theme:**
   - Dark Neon (default)
   - Sky Pulse
   - Light Fusion

3. **Customize the intro message** (optional)
4. **Add resource links** for your viewers (optional)
5. **Add a footer note** with tips or support info (optional)
6. Click **Save Settings**

### Step 3: Add to Your Site

#### Using the Block Editor (Gutenberg)
1. Edit the page or post where you want the speed test
2. Click the **+** button to add a new block
3. Search for "Shortcode"
4. Paste: `[cloud_gaming_speedtest]`
5. **Update/Publish** the page

#### Using the Classic Editor
1. Edit the page or post
2. Paste `[cloud_gaming_speedtest]` where you want it
3. **Update/Publish** the page

#### In a Widget
1. Go to **Appearance → Widgets**
2. Add a **Custom HTML** widget
3. Paste: `[cloud_gaming_speedtest]`
4. Save the widget

#### Theme Override
To use a specific theme on a particular page:
```
[cloud_gaming_speedtest theme="dark"]
[cloud_gaming_speedtest theme="sky"]
[cloud_gaming_speedtest theme="light"]
```

## Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.0 or higher
- **jQuery**: Included with WordPress
- **Server**: Must allow outbound HTTP requests

## Troubleshooting

### Plugin doesn't appear after activation
- Clear your WordPress cache (if using a caching plugin)
- Check **Plugins** to ensure it's actually activated
- Look for PHP errors in **Tools → Site Health → Info → Server**

### Speed test won't start
1. **Check jQuery**: Go to a page with the speed test, right-click → Inspect → Console
   - If you see jQuery errors, your theme might not be loading jQuery properly
   - Add this to your theme's `functions.php`:
     ```php
     add_action('wp_enqueue_scripts', function() {
         wp_enqueue_script('jquery');
     });
     ```

2. **Check AJAX**: Look in the browser console for AJAX errors
   - Ensure your site can make AJAX requests to `/wp-admin/admin-ajax.php`
   - Some security plugins block AJAX - add an exception for `cgst_*` actions

3. **Check Permissions**: Ensure non-admin users can access the AJAX endpoints
   - The plugin registers both `wp_ajax_*` and `wp_ajax_nopriv_*` actions

### Inaccurate speed results
- Test multiple times for consistency
- Use a wired Ethernet connection for accurate baseline
- Close other applications using bandwidth
- Test at different times (ISP speeds vary)
- Some shared hosting limits outbound HTTP request speeds

### Admin settings not saving
- Check file permissions: `/wp-content/plugins/cloud-gaming-speedtest/` should be writable
- Ensure you're logged in as an Administrator
- Disable other plugins temporarily to check for conflicts
- Check for PHP errors in **Site Health**

### Shortcode displays as text
- Ensure the plugin is activated
- Check you're using the correct shortcode: `[cloud_gaming_speedtest]`
- Some page builders need shortcode blocks specifically

### CSS not loading / looks broken
1. Clear all caches (browser, WordPress, CDN)
2. Check the browser console for 404 errors on CSS files
3. Verify file exists: `/wp-content/plugins/cloud-gaming-speedtest/assets/css/frontend.css`
4. Check file permissions (should be 644)
5. Try disabling other plugins to check for CSS conflicts

## Advanced Configuration

### Changing the Home Button URL
The "Return Home" button uses `home_url('/')` by default. To customize:

```php
add_filter('cgst_home_url', function($url) {
    return 'https://your-custom-url.com';
});
```

### Adding Custom Test Endpoints
To use your own test servers:

```php
add_filter('cgst_download_test_urls', function($urls) {
    return array(
        'https://yourserver.com/testfile1.bin',
        'https://yourserver.com/testfile2.bin'
    );
});
```

### Modifying Performance Thresholds
To adjust what qualifies as "Excellent" vs "Good":

```php
add_filter('cgst_performance_thresholds', function($thresholds) {
    // Modify $thresholds array
    return $thresholds;
});
```

### Disabling IP Geolocation
If you want to skip the IP/location detection:

```php
add_filter('cgst_enable_geolocation', '__return_false');
```

## Server Requirements

### Outbound HTTP Requests
The plugin MUST be able to make outbound HTTP requests. Some hosts block these for security.

**Test if your server can make outbound requests:**
1. Install the "WP Crontrol" plugin
2. Go to **Tools → Crontrol → PHP**
3. Run this code:
   ```php
   $response = wp_remote_get('https://www.google.com');
   echo is_wp_error($response) ? 'BLOCKED' : 'ALLOWED';
   ```

If blocked, contact your host to whitelist:
- `https://speed.cloudflare.com`
- `https://httpbin.org`
- `https://ipapi.co`
- `https://api.ipify.org`

### Firewall / Security Plugin Conflicts
If using security plugins (WordFence, Sucuri, iThemes Security):

1. Whitelist the AJAX actions:
   - `cgst_test_latency`
   - `cgst_test_download`
   - `cgst_test_upload`
   - `cgst_analyze_results`
   - `cgst_get_user_info`

2. Don't block `/wp-admin/admin-ajax.php` for non-logged-in users

## Performance Optimization

### With Caching Plugins
The speed test works with caching, but ensure:
- AJAX requests (`admin-ajax.php`) are NOT cached
- JavaScript files are loaded (not deferred/delayed)
- jQuery is loaded before the plugin's JS

### With CDN
If using a CDN (Cloudflare, etc.):
- Ensure plugin assets are served from CDN for faster loading
- Don't cache the AJAX endpoint responses

### Page Builders
Works with:
- ✅ Elementor - Add "Shortcode" widget
- ✅ WPBakery - Add "Raw HTML" element
- ✅ Divi - Add "Code" module
- ✅ Beaver Builder - Add "HTML" module
- ✅ Gutenberg - Add "Shortcode" block

## Getting Help

1. Check the **README.md** for detailed documentation
2. Look in browser console for JavaScript errors
3. Check WordPress **Site Health** for server issues
4. Temporarily disable other plugins to check for conflicts
5. Switch to a default theme (Twenty Twenty-Three) to test

## Uninstallation

To completely remove the plugin:

1. Go to **Plugins**
2. **Deactivate** Cloud Gaming Speed Test
3. Click **Delete**
4. Confirm deletion

This will:
- Remove all plugin files
- Keep your settings (in case you reinstall)

To also delete settings:
```php
delete_option('cgst_color_theme');
delete_option('cgst_custom_resources');
delete_option('cgst_intro_text');
delete_option('cgst_footer_note');
```

## Updates

Future updates will:
- Preserve your theme selection
- Preserve your resource links
- Preserve your custom text
- Not affect existing shortcodes

Always backup before updating!

---

**Need more help?** Check the README.md for FAQ and troubleshooting tips.
