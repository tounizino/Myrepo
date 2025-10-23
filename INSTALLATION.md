# 🚀 Installation Guide

Complete installation instructions for the Cloud Gaming Status & Launcher Dashboard.

## 📋 Prerequisites

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern web browser (Chrome, Firefox, Safari, Edge)
- Optional: Node.js for development

## 🎯 Quick Start (5 Minutes)

### For WordPress

1. **Download the Plugin**
   ```bash
   git clone https://github.com/cloudloadout/cloud-gaming-dashboard.git
   ```

2. **Upload to WordPress**
   - Copy the entire folder to `/wp-content/plugins/cloud-gaming-dashboard/`
   - Or upload via WordPress Admin → Plugins → Add New → Upload Plugin

3. **Activate the Plugin**
   - Go to WordPress Admin → Plugins
   - Find "Cloud Gaming Status & Launcher Dashboard"
   - Click "Activate"

4. **Add to Your Page**
   - Edit any post or page
   - Add the shortcode: `[cloud_gaming_combined]`
   - Publish and view!

### For Static HTML Sites

1. **Copy Files**
   ```bash
   cp -r assets /your-website/
   ```

2. **Add to HTML**
   ```html
   <link rel="stylesheet" href="assets/css/cloud-gaming-dashboard.css">
   <script src="assets/js/cloud-gaming-dashboard.js"></script>
   
   <div class="cloud-gaming-container" id="my-dashboard"></div>
   ```

3. **Done!** Open your page in a browser.

## 📂 File Structure

```
cloud-gaming-dashboard/
├── assets/
│   ├── css/
│   │   └── cloud-gaming-dashboard.css
│   └── js/
│       └── cloud-gaming-dashboard.js
├── templates/
│   ├── combined.php
│   ├── dashboard.php
│   └── launcher.php
├── cloud-gaming-dashboard.php
├── README.md
├── INSTALLATION.md
└── demo.html
```

## 🎨 Customization

### Change Colors

Add custom CSS after the plugin CSS:

```css
.cloud-gaming-container {
    background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%) !important;
}
```

### Modify Services

Edit `assets/js/cloud-gaming-dashboard.js`:

```javascript
const CONFIG = {
    services: [
        {
            id: 'my-service',
            name: 'My Custom Service',
            type: 'Gaming',
            icon: '🎮',
            url: 'https://example.com',
            statusUrl: 'https://status.example.com'
        }
        // ... add more services
    ]
};
```

### Change Refresh Rate

```javascript
const CONFIG = {
    refreshInterval: 120000, // 2 minutes instead of 1
    // ...
};
```

## 🔧 Advanced Configuration

### WordPress Hooks

Add custom functionality:

```php
// In your theme's functions.php
add_filter('cloud_gaming_services', function($services) {
    $services[] = array(
        'id' => 'custom-service',
        'name' => 'Custom Service',
        // ...
    );
    return $services;
});
```

### Custom Status Checking

Replace the demo status checker with real API calls:

```javascript
async checkService(service) {
    try {
        const response = await fetch(`https://api.example.com/status/${service.id}`);
        const data = await response.json();
        
        return {
            status: data.operational ? 'online' : 'offline',
            responseTime: data.responseTime,
            uptime: data.uptime
        };
    } catch (error) {
        return { status: 'unknown' };
    }
}
```

## 🐛 Troubleshooting

### Issue: Styles Not Appearing

**Solution:**
1. Clear WordPress cache
2. Check browser console for errors
3. Verify file permissions (644 for files, 755 for directories)

```bash
chmod 644 assets/css/cloud-gaming-dashboard.css
chmod 644 assets/js/cloud-gaming-dashboard.js
```

### Issue: JavaScript Not Working

**Solution:**
1. Check browser console for errors
2. Ensure no jQuery conflicts
3. Verify file is loaded: View Source → Search for "cloud-gaming-dashboard.js"

### Issue: LocalStorage Not Saving

**Solution:**
1. Check browser privacy settings
2. Ensure site is HTTPS (some browsers block localStorage on HTTP)
3. Test in incognito mode to rule out extensions

### Issue: Status Always Shows "Unknown"

**Solution:**
1. This is expected in demo mode
2. Implement real status checking (see Advanced Configuration)
3. Check CORS settings if using external APIs

## 🔐 Security Considerations

### For WordPress

1. **Keep Updated**
   - Regularly update WordPress core, plugins, and themes

2. **Sanitize Inputs**
   - The plugin sanitizes all inputs by default

3. **Use HTTPS**
   - Ensure your site uses SSL/TLS

4. **Backup Regularly**
   - Backup before any major changes

### For Static Sites

1. **Content Security Policy**
   ```html
   <meta http-equiv="Content-Security-Policy" 
         content="default-src 'self'; script-src 'self' 'unsafe-inline'">
   ```

2. **Subresource Integrity**
   - Use SRI hashes for CDN resources

## 🌐 CDN Integration

### Using a CDN

1. **Upload to CDN**
   - Upload `assets/` folder to your CDN

2. **Update URLs**
   ```php
   // In WordPress
   define('CLOUD_GAMING_CDN', 'https://cdn.example.com/');
   ```

3. **Preload Resources**
   ```html
   <link rel="preload" href="https://cdn.example.com/cloud-gaming-dashboard.css" as="style">
   <link rel="preload" href="https://cdn.example.com/cloud-gaming-dashboard.js" as="script">
   ```

## 📊 Analytics Integration

### Google Analytics

Add to `assets/js/cloud-gaming-dashboard.js`:

```javascript
// Track service launches
document.querySelectorAll('.cgd-launcher-card').forEach(card => {
    card.addEventListener('click', () => {
        gtag('event', 'service_launch', {
            'event_category': 'Cloud Gaming',
            'event_label': card.dataset.service
        });
    });
});
```

### Custom Analytics

```javascript
// Custom tracking
window.cloudGamingAnalytics = {
    trackLaunch: function(service) {
        // Your custom tracking code
    }
};
```

## 🚀 Performance Optimization

### Enable Caching

**WordPress:**
```php
// Enable transient caching
set_transient('cloud_gaming_status', $status, 300); // 5 minutes
```

### Minify Assets

```bash
# Using npm
npm install -g clean-css-cli uglify-js

# Minify CSS
cleancss -o assets/css/cloud-gaming-dashboard.min.css assets/css/cloud-gaming-dashboard.css

# Minify JS
uglifyjs assets/js/cloud-gaming-dashboard.js -o assets/js/cloud-gaming-dashboard.min.js
```

### Lazy Loading

```html
<script src="cloud-gaming-dashboard.js" defer></script>
```

## 🔄 Updating

### WordPress Plugin

1. Deactivate the plugin
2. Replace files with new version
3. Reactivate the plugin
4. Clear cache

### Static HTML

1. Backup current files
2. Replace CSS and JS files
3. Clear browser cache
4. Test functionality

## 📱 Mobile Testing

### Responsive Testing Tools

- Chrome DevTools (F12 → Toggle Device Toolbar)
- Firefox Responsive Design Mode
- Safari Responsive Design Mode
- Online: BrowserStack, LambdaTest

### Test Checklist

- [ ] All cards display correctly
- [ ] Touch interactions work
- [ ] Search is functional
- [ ] Favorites persist
- [ ] Dark mode works
- [ ] Notifications appear
- [ ] Launcher opens links

## 🌍 Browser Testing

Recommended testing:

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile Safari (iOS)
- Chrome Mobile (Android)

## 🆘 Getting Help

### Resources

- **Documentation**: [CloudLoadout.com/docs](https://cloudloadout.com/docs)
- **GitHub Issues**: [Report bugs](https://github.com/cloudloadout/cloud-gaming-dashboard/issues)
- **Email Support**: support@cloudloadout.com
- **Community Forum**: [CloudLoadout Community](https://community.cloudloadout.com)

### Before Asking for Help

1. Check this documentation
2. Search existing GitHub issues
3. Check browser console for errors
4. Test in incognito/private mode
5. Disable other plugins/extensions

### What to Include

When reporting issues:

- WordPress version (if applicable)
- PHP version (if applicable)
- Browser and version
- Console errors (screenshot)
- Steps to reproduce
- Expected vs actual behavior

## ✅ Post-Installation Checklist

- [ ] Plugin installed and activated
- [ ] Shortcode added to page
- [ ] Dashboard displays correctly
- [ ] All services show status
- [ ] Search functionality works
- [ ] Favorites can be saved
- [ ] Dark mode toggles
- [ ] Mobile view is responsive
- [ ] Links open correctly
- [ ] No console errors

## 🎓 Next Steps

1. **Customize Colors**: Match your site's branding
2. **Add Analytics**: Track user interactions
3. **Implement Real Status**: Connect to actual APIs
4. **Share Feedback**: Help improve the plugin
5. **Star on GitHub**: Support the project

---

**Need help?** Contact support@cloudloadout.com or visit our [documentation](https://cloudloadout.com/docs).
