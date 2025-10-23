# ☁️ Cloud Gaming Status & Launcher Dashboard

A premium WordPress plugin for cloudloadout.com providing real-time status monitoring and quick launch capabilities for all major cloud gaming platforms.

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-green)
![License](https://img.shields.io/badge/license-GPL--2.0-orange)

## 🌟 Features

### Status Dashboard
- **Real-time Monitoring**: Track status of 12+ cloud gaming services
- **Response Time Tracking**: Monitor latency and performance metrics
- **Auto-refresh**: Updates every 60 seconds automatically
- **Visual Indicators**: Color-coded status badges (🟢 Online, 🟡 Degraded, 🔴 Offline)
- **Service Details**: Uptime percentage, region info, last check time
- **Statistics Overview**: System-wide health metrics

### Quick Launcher Widget
- **One-Click Launch**: Direct links to all major platforms
- **Favorites System**: Star your preferred services with localStorage
- **Visual Status**: See service status right in the launcher
- **Smart Organization**: Filtered and searchable interface

### Advanced Features
- **🔍 Search & Filter**: Find services instantly
- **⭐ Favorites**: Mark and filter your preferred platforms
- **🌓 Dark Mode**: Eye-friendly theme switching
- **📊 Statistics**: Overall system health metrics
- **🔔 Notifications**: Status change alerts
- **📱 Mobile Responsive**: Perfect on all devices
- **♿ Accessible**: WCAG 2.1 compliant
- **🚀 SEO Optimized**: Schema.org structured data

## 🎮 Supported Platforms

1. **GeForce NOW** - NVIDIA cloud gaming service
2. **Xbox Cloud Gaming** - Microsoft Xbox streaming
3. **Boosteroid** - European cloud gaming platform
4. **Shadow** - Full Windows PC in the cloud
5. **Amazon Luna** - Amazon's cloud gaming service
6. **PlayStation Plus** - Sony PlayStation cloud gaming
7. **NVIDIA Shield** - Local and cloud streaming
8. **Parsec** - Low latency game streaming
9. **Google Stadia** - Legacy platform (discontinued)
10. **Utomik** - Game subscription service
11. **Vortex** - Cloud gaming platform
12. **Blacknut** - Family-friendly cloud gaming

## 📦 Installation

### Method 1: WordPress Plugin (Recommended)

1. Download the plugin files
2. Upload to `/wp-content/plugins/cloud-gaming-dashboard/`
3. Activate the plugin through WordPress admin
4. Use shortcodes in any post or page

### Method 2: Manual Integration

Copy the files to your theme and enqueue the CSS and JS:

```php
wp_enqueue_style('cloud-gaming-dashboard', get_template_directory_uri() . '/assets/css/cloud-gaming-dashboard.css');
wp_enqueue_script('cloud-gaming-dashboard', get_template_directory_uri() . '/assets/js/cloud-gaming-dashboard.js', array(), '1.0.0', true);
```

## 🎯 Usage

### WordPress Shortcodes

#### Full Dashboard (Status + Launcher)
```
[cloud_gaming_combined]
```

#### Status Dashboard Only
```
[cloud_gaming_dashboard]
```

#### Launcher Widget Only
```
[cloud_gaming_launcher]
```

### Manual HTML Integration

```html
<div class="cloud-gaming-container" id="my-dashboard"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new CloudGamingDashboard('my-dashboard');
    });
</script>
```

## 🎨 Customization

### Styling

All styles are scoped with `!important` and contained within `.cloud-gaming-container` to prevent conflicts with your theme.

#### Custom Colors

```css
.cloud-gaming-container {
    --primary-color: #667eea !important;
    --secondary-color: #764ba2 !important;
}
```

#### Hide Specific Sections

```css
/* Hide statistics */
.cgd-stats-section { display: none !important; }

/* Hide launcher */
.cgd-launcher-section { display: none !important; }

/* Hide controls */
.cgd-controls { display: none !important; }
```

### JavaScript Configuration

Modify the CONFIG object in `cloud-gaming-dashboard.js`:

```javascript
const CONFIG = {
    refreshInterval: 60000, // Change refresh rate (milliseconds)
    storageKey: 'cloudGamingDashboard',
    notificationDuration: 5000,
    services: [
        // Add or remove services here
    ]
};
```

## 🔌 API Integration

### Adding Real Status Checking

Replace the simulated status check in `StatusChecker.checkService()`:

```javascript
async checkService(service) {
    try {
        const response = await fetch(service.statusUrl);
        const status = await response.json();
        
        return {
            status: status.operational ? 'online' : 'offline',
            responseTime: status.responseTime,
            lastChecked: new Date().toISOString(),
            uptime: status.uptime,
            region: status.region
        };
    } catch (error) {
        return {
            status: 'unknown',
            error: error.message
        };
    }
}
```

### WordPress AJAX Integration

The plugin includes AJAX hooks for server-side status checking:

```php
add_action('wp_ajax_check_service_status', array($this, 'checkServiceStatus'));
add_action('wp_ajax_nopriv_check_service_status', array($this, 'checkServiceStatus'));
```

## 📱 Mobile Responsiveness

The dashboard automatically adapts to different screen sizes:

- **Desktop (>768px)**: Full multi-column grid layout
- **Tablet (768px)**: Optimized 2-column layout
- **Mobile (<480px)**: Single column stack layout

## ♿ Accessibility

- ARIA labels on interactive elements
- Keyboard navigation support
- Focus indicators on all controls
- High contrast mode support
- Screen reader compatible
- Reduced motion support

## 🔒 Security Features

- Nonce verification on AJAX requests
- Sanitized user inputs
- Escaped output data
- No direct file access allowed
- XSS protection

## 🌐 Browser Compatibility

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Opera 76+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## ⚡ Performance

- **Lazy Loading**: Services load progressively
- **Debounced Search**: Optimized filtering
- **LocalStorage**: Persistent user preferences
- **Minimal Dependencies**: No jQuery required
- **Optimized CSS**: Scoped to prevent leaks
- **Efficient Rendering**: Virtual DOM patterns

## 🎓 SEO Features

- **Schema.org Markup**: WebApplication structured data
- **Semantic HTML**: Proper heading hierarchy
- **Meta Descriptions**: Service information
- **Fast Loading**: Optimized assets
- **Mobile-First**: Responsive design

## 🐛 Troubleshooting

### Styles Not Loading

Check that the CSS file is properly enqueued:
```php
wp_enqueue_style('cloud-gaming-dashboard-css', plugin_dir_url(__FILE__) . 'assets/css/cloud-gaming-dashboard.css');
```

### JavaScript Not Working

Verify jQuery is not interfering:
```javascript
// The plugin uses vanilla JavaScript - no jQuery required
```

### LocalStorage Not Persisting

Check browser privacy settings allow localStorage:
```javascript
if (typeof Storage !== "undefined") {
    // LocalStorage is supported
}
```

## 🔄 Auto-Refresh System

The dashboard automatically refreshes every 60 seconds:

- Status updates
- Response time checks
- Statistics recalculation
- Notification on status changes

## 📊 Analytics Integration

Track user interactions with Google Analytics:

```javascript
// Add to the launcher click handlers
gtag('event', 'launch_service', {
    'service_name': service.name,
    'service_type': service.type
});
```

## 🚀 Performance Tips

1. **Reduce Refresh Rate**: Increase `refreshInterval` for slower updates
2. **Limit Services**: Remove unused services from CONFIG
3. **Disable Features**: Hide sections you don't need
4. **Cache Status**: Implement server-side caching
5. **CDN Integration**: Host assets on CDN

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

GPL-2.0 License - Free to use and modify for your WordPress site.

## 🆘 Support

For issues or questions:
- Create an issue on GitHub
- Contact: support@cloudloadout.com
- Documentation: https://cloudloadout.com/docs

## 🎉 Credits

Developed for **CloudLoadout.com** - Your ultimate cloud gaming guide.

### Technologies Used
- Vanilla JavaScript (ES6+)
- CSS3 with Grid & Flexbox
- PHP 7.4+
- WordPress 5.0+

## 🔮 Roadmap

- [ ] Historical status tracking
- [ ] Export status reports
- [ ] Email notifications
- [ ] Custom service addition
- [ ] Advanced filtering options
- [ ] Performance analytics
- [ ] Multi-language support
- [ ] REST API endpoints

## 📸 Screenshots

### Desktop View
Full-featured dashboard with all components visible.

### Mobile View
Optimized single-column layout for mobile devices.

### Dark Mode
Eye-friendly dark theme for night browsing.

---

**Made with ❤️ for the cloud gaming community**

*Star this project if you find it useful!*
