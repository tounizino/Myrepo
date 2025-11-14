# Installation & Setup Guide

## Prerequisites

- WordPress 5.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher
- One or more LibreSpeed backend instances

## Step-by-Step Installation

### 1. Plugin Installation

**Method A: Manual Upload**

1. Download or copy the `cloud-gaming-libre-speed` folder
2. Upload to `/wp-content/plugins/` directory via FTP/SFTP
3. Ensure folder name is exactly `cloud-gaming-libre-speed`

**Method B: WordPress Admin**

1. Zip the `cloud-gaming-libre-speed` folder
2. Go to WordPress Admin > Plugins > Add New > Upload Plugin
3. Select the zip file and click "Install Now"

### 2. Plugin Activation

1. Navigate to WordPress Admin > Plugins
2. Find "Cloud Gaming Speed Test - LibreSpeed"
3. Click "Activate"

The plugin will automatically:
- Create database table `wp_cgst_results`
- Insert default server presets (with example URLs)
- Insert default optimization articles
- Register admin menu pages

### 3. Deploy LibreSpeed Backends

You need at least one LibreSpeed instance to perform tests.

#### Option A: Docker Deployment (Recommended)

```bash
# Deploy a single LibreSpeed instance
docker run -d \
  --name librespeed-us-east \
  -p 8080:80 \
  -e MODE=standalone \
  adolfintel/speedtest
```

Access at: `http://your-server-ip:8080/`

#### Option B: Multiple Regional Instances

```bash
# US East
docker run -d --name librespeed-us-east -p 8081:80 adolfintel/speedtest

# US West
docker run -d --name librespeed-us-west -p 8082:80 adolfintel/speedtest

# EU Central
docker run -d --name librespeed-eu -p 8083:80 adolfintel/speedtest
```

#### Option C: Manual Installation

Follow [LibreSpeed documentation](https://github.com/librespeed/speedtest):

1. Clone LibreSpeed repo: `git clone https://github.com/librespeed/speedtest.git`
2. Configure your web server (Apache/Nginx)
3. Point document root to speedtest folder
4. Ensure PHP is enabled

### 4. Configure CORS on LibreSpeed Servers

**For Apache** - Add to `.htaccess`:

```apache
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "https://yourdomain.com"
    Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, X-Requested-With"
    Header set Access-Control-Allow-Credentials "true"
</IfModule>
```

**For Nginx** - Add to site config:

```nginx
location / {
    add_header 'Access-Control-Allow-Origin' 'https://yourdomain.com' always;
    add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS' always;
    add_header 'Access-Control-Allow-Headers' 'Content-Type, X-Requested-With' always;
    add_header 'Access-Control-Allow-Credentials' 'true' always;

    if ($request_method = 'OPTIONS') {
        return 204;
    }
}
```

**Important**: Replace `https://yourdomain.com` with your actual WordPress site URL.

### 5. Configure Server Presets in WordPress

1. Go to WordPress Admin > **Speed Test** > **Server Presets**
2. Edit the default servers or add new ones:

**Example Configuration:**

| Field | Value |
|-------|-------|
| Server Name | US East - New York |
| Location Label | New York, NY, USA |
| Base Backend URL | `https://speed.example.com/us-east/` |
| Download Endpoint Path | `garbage.php` or `download.php` |
| Upload Endpoint Path | `empty.php` or `upload.php` |
| Ping Endpoint Path | `empty.php` or `ping.php` |
| Priority Weight | 1 |
| Latitude | 40.7128 |
| Longitude | -74.0060 |

3. Click **Save Server**
4. Repeat for all regions

### 6. Add Optimization Articles (Optional)

1. Go to **Speed Test** > **Articles & Tips**
2. Add helpful resources for your users:
   - Network optimization guides
   - VPN recommendations
   - Cloud gaming setup tutorials
3. These will appear in the frontend after tests

### 7. Embed the Speed Test

**On a Page:**

1. Create or edit a page
2. Add the shortcode: `[cloudspeedtest]`
3. Publish

**In a Widget:**

1. Go to Appearance > Widgets
2. Add "Shortcode" widget
3. Enter: `[cloudspeedtest]`

**In Theme Template:**

```php
<?php echo do_shortcode('[cloudspeedtest]'); ?>
```

### 8. Test the Setup

1. Visit the page where you embedded the shortcode
2. Select "Auto (Fastest)" or choose a specific server
3. Click "Start Test"
4. Monitor browser console for any errors

**Expected Behavior:**
- Progress bars animate during testing
- Download/Upload speeds display in Mbps
- Ping/Jitter display in milliseconds
- Cloud gaming rating appears with recommendation
- Result saved in WordPress admin history

### 9. Verify Admin Dashboard

1. Go to **Speed Test** > **Dashboard**
2. Check recent test results appear
3. Verify server list is displayed
4. Review featured guides section

## Troubleshooting

### Issue: "No server available for testing"

**Solution:**
- Verify at least one server preset exists
- Check server URLs are accessible
- Test LibreSpeed backend directly in browser

### Issue: CORS Error in Browser Console

**Solution:**
- Configure CORS headers on LibreSpeed servers (see Step 4)
- Use browser dev tools Network tab to verify OPTIONS preflight succeeds
- Ensure WordPress site URL matches CORS origin

### Issue: Tests timeout or fail

**Solution:**
- Check LibreSpeed server is running: `curl http://your-librespeed-url/ping.php`
- Verify firewall rules allow HTTP/HTTPS traffic
- Test with shorter timeout in `speed-test.js` (default: 10 seconds)

### Issue: Results not saving

**Solution:**
- Check WordPress debug log for PHP errors
- Verify database table `wp_cgst_results` exists
- Confirm AJAX nonce validation passes

### Issue: Styling looks broken

**Solution:**
- Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
- Check WordPress theme doesn't override plugin styles
- Inspect element and look for CSS conflicts
- Increase plugin CSS specificity if needed

### Issue: JavaScript errors

**Solution:**
- Ensure jQuery is loaded (WordPress default)
- Check browser console for specific error messages
- Verify `cgstData` object is defined in page source
- Disable other plugins to test for conflicts

## Advanced Configuration

### Custom LibreSpeed Endpoints

If your LibreSpeed installation uses different endpoint paths:

1. Edit server preset in admin
2. Update paths:
   - Standard: `garbage.php`, `empty.php`, `empty.php`
   - Alternative: `download.php`, `upload.php`, `ping.php`

### Adjusting Test Duration

Edit `assets/js/speed-test.js`:

```javascript
// Change from 10 seconds to 5 seconds
const testDuration = 5000;
```

### Custom Rating Thresholds

Edit `includes/database.php` (PHP backend):

```php
public static function calculate_rating($download, $upload, $ping, $jitter, $packet_loss) {
    // Modify thresholds here
    if ($download >= 200 && $upload >= 30 && $ping <= 15) {
        $rating = 'Excellent';
        // ...
    }
}
```

And `assets/js/speed-test.js` (frontend):

```javascript
calculateRating() {
    // Modify thresholds here
    if (d >= 200 && u >= 30 && p <= 15) {
        rating = 'Excellent';
        // ...
    }
}
```

### Enable Debug Mode

Add to WordPress `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check `/wp-content/debug.log` for errors.

## Performance Optimization

### Caching

If using a caching plugin (WP Super Cache, W3 Total Cache):
- Exclude the speed test page from caching
- Speed tests rely on real-time AJAX calls

### CDN Integration

For LibreSpeed backends:
- Deploy behind Cloudflare or similar CDN
- Use edge locations for lowest latency
- Configure CORS at CDN level if needed

### Database Cleanup

Periodically clean old test results:

```sql
DELETE FROM wp_cgst_results WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

Or add auto-cleanup (future enhancement).

## Security Checklist

- ✅ AJAX nonces verified on all requests
- ✅ Input sanitization with WordPress functions
- ✅ Database queries use prepared statements
- ✅ Admin functions check `manage_options` capability
- ✅ No eval() or dynamic code execution
- ✅ Files check `ABSPATH` constant

## Support & Resources

- **LibreSpeed GitHub**: https://github.com/librespeed/speedtest
- **WordPress Codex**: https://codex.wordpress.org/
- **Plugin Directory**: [Link to your plugin page]

## Next Steps

1. ✅ Install and activate plugin
2. ✅ Deploy LibreSpeed backends
3. ✅ Configure CORS headers
4. ✅ Add server presets in admin
5. ✅ Embed shortcode on page
6. ✅ Test functionality
7. ✅ Customize design (optional)
8. ✅ Add optimization articles
9. ✅ Monitor test history

**You're all set!** Users can now test their internet speed and get cloud gaming assessments on your site. 🎮🚀
