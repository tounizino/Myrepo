# Cloud Gaming Speed Test - LibreSpeed WordPress Plugin

A professional, feature-rich WordPress plugin designed specifically for cloud gaming enthusiasts and content creators. Test internet speed with LibreSpeed backend integration and get instant cloud gaming suitability ratings with stunning gaming-style animations.

![Plugin Version](https://img.shields.io/badge/version-1.0.0-blue)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue)
![PHP](https://img.shields.io/badge/PHP-7.2%2B-blue)
![License](https://img.shields.io/badge/license-GPL%20v2-green)

## 🎮 Overview

This plugin transforms your WordPress site into a comprehensive cloud gaming performance testing hub. It measures download, upload, ping, jitter, and packet loss using LibreSpeed, then provides instant recommendations for optimal cloud gaming settings (4K/120fps, 1440p/60fps, 1080p/60fps, or 720p/60fps).

Perfect for:
- **Gaming Blogs & Websites**: Offer readers a practical tool to test their setup
- **ISP Review Sites**: Provide objective network performance data
- **Tech Support Portals**: Help users diagnose connection issues
- **Cloud Gaming Communities**: Benchmark performance across different regions

## ✨ Key Features

### Speed Testing
- **Multiple Metrics**: Download, upload, ping, jitter, packet loss
- **LibreSpeed Integration**: Industry-standard accuracy
- **Auto Server Selection**: Automatically finds fastest server by pinging all presets
- **Manual Server Selection**: Choose specific regions (US East, US West, EU, Asia)
- **Real-Time Progress**: Animated progress bars with status updates

### Cloud Gaming Assessment
- **Smart Rating System**: Excellent / Good / Fair / Poor based on comprehensive thresholds
- **Personalized Recommendations**: 4K@120fps, 1440p@60fps, 1080p@60fps, or 720p@60fps suggestions
- **Platform-Specific Tips**: GeForce NOW, Xbox Cloud Gaming, Stadia-optimized advice

### Stunning UI/UX
- **Gaming-Style Design**: Neon colors, glowing effects, smooth animations
- **Fully Responsive**: Beautiful on desktop, tablet, and mobile
- **Animated Progress**: Flowing gradient progress bars
- **Pulsing Badges**: Eye-catching rating display
- **Hover Effects**: Interactive result cards

### Admin Dashboard
- **Server Management**: Add/edit/delete LibreSpeed backend servers
- **Article Curation**: Manage optimization guides and tips
- **Test History**: View all recorded tests with detailed metrics
- **CSV Export**: Download data for analytics and reporting
- **Dashboard Overview**: Quick stats and recent activity

### Developer-Friendly
- **Shortcode**: Simple `[cloudspeedtest]` embed
- **Well-Documented**: Comprehensive inline comments
- **Modular Architecture**: Clean separation of concerns
- **WordPress Standards**: Follows all best practices
- **Secure**: Nonces, sanitization, prepared statements
- **Translation-Ready**: Full i18n support

## 📸 Screenshots

### Frontend Speed Test Interface
```
┌─────────────────────────────────────────┐
│  🎮 Cloud Gaming Speed Test             │
│  Test your internet speed and discover  │
│  if your connection is optimized        │
├─────────────────────────────────────────┤
│  🌐 Select Server:                      │
│  ⚡ Auto (Fastest)  🎯 Manual Selection │
│                                         │
│  ▶ Start Test                          │
└─────────────────────────────────────────┘
```

### Results Display
```
┌──────────┬──────────┬──────────┬──────────┐
│ 📥       │ 📤       │ ⚡       │ 📊       │
│ 152.5    │ 28.3     │ 18       │ 4.2      │
│ Download │ Upload   │ Ping     │ Jitter   │
│ Mbps     │ Mbps     │ ms       │ ms       │
└──────────┴──────────┴──────────┴──────────┘

        🏆 Excellent
    4K / 120fps – perfect for
    GeForce NOW Ultimate & Xbox
    Cloud Gaming Performance preset.

    🔄 Run Another Test
```

## 🚀 Quick Start

### 1. Installation

```bash
# Upload to WordPress plugins directory
cd /path/to/wordpress/wp-content/plugins/
git clone <this-repo> cloud-gaming-libre-speed

# Or download and extract ZIP
# Then activate via WordPress Admin > Plugins
```

### 2. Deploy LibreSpeed Backends

```bash
# Quick Docker deployment
docker run -d -p 8080:80 adolfintel/speedtest

# Or follow LibreSpeed docs for manual installation
# https://github.com/librespeed/speedtest
```

### 3. Configure Plugin

1. Activate plugin in WordPress admin
2. Go to **Speed Test** > **Server Presets**
3. Edit default servers or add your own:
   - Base Backend URL: `https://your-librespeed-server.com/`
   - Configure CORS headers on LibreSpeed server
4. Add optimization articles in **Articles & Tips**

### 4. Embed on Your Site

```
[cloudspeedtest]
```

Add the shortcode to any page, post, or widget!

## 📂 Plugin Structure

```
cloud-gaming-libre-speed/
├── cloud-gaming-speed-test.php    # Main plugin file
├── includes/
│   ├── database.php               # Database operations, rating logic
│   └── ajax-handlers.php          # AJAX endpoints
├── assets/
│   ├── css/
│   │   ├── style.css              # Stunning gaming-style CSS
│   │   └── admin-style.css        # Admin panel styles
│   └── js/
│       ├── speed-test.js          # LibreSpeed integration & animations
│       └── admin-script.js        # Admin CRUD operations
├── templates/
│   ├── speed-test-template.php    # Frontend shortcode template
│   ├── admin-dashboard.php        # Admin overview
│   ├── admin-servers.php          # Server management
│   ├── admin-articles.php         # Article management
│   ├── admin-history.php          # Test history & CSV export
│   └── admin-settings.php         # Documentation & thresholds
├── README.md                       # This file
├── INSTALL.md                      # Detailed installation guide
├── USAGE.md                        # User & admin usage guide
├── CUSTOMIZATION.md                # Theming & extension guide
└── CHANGELOG.md                    # Version history
```

## 🎯 Cloud Gaming Ratings

| Rating | Download | Upload | Ping | Jitter | Packet Loss | Recommendation |
|--------|----------|--------|------|--------|-------------|----------------|
| **🏆 Excellent** | ≥150 Mbps | ≥25 Mbps | ≤20ms | ≤5ms | ≤0.1% | 4K @ 120fps |
| **✅ Good** | ≥90 Mbps | ≥15 Mbps | ≤35ms | ≤8ms | ≤0.3% | 1440p @ 60fps |
| **⚠️ Fair** | ≥45 Mbps | ≥8 Mbps | ≤55ms | ≤12ms | ≤0.8% | 1080p @ 60fps |
| **❌ Poor** | Below Fair | | | | | 720p @ 60fps |

## 🛠️ Technical Details

### Requirements
- **WordPress**: 5.0 or higher
- **PHP**: 7.2 or higher
- **MySQL**: 5.6 or higher
- **LibreSpeed**: Any version (self-hosted or third-party)

### Browser Support
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

### Security Features
- AJAX nonce verification
- Input sanitization & output escaping
- Prepared SQL statements
- Capability checks (`manage_options`)
- ABSPATH protection in all files

### Database
- **Table**: `wp_cgst_results` - Stores historic test results
- **Options**: 
  - `cgst_servers` - Server presets
  - `cgst_articles` - Featured articles
  - `cgst_settings` - Plugin settings (reserved)

## 📖 Documentation

- **[INSTALL.md](cloud-gaming-libre-speed/INSTALL.md)**: Step-by-step installation with troubleshooting
- **[USAGE.md](cloud-gaming-libre-speed/USAGE.md)**: Admin and end-user guides
- **[CUSTOMIZATION.md](cloud-gaming-libre-speed/CUSTOMIZATION.md)**: Theming, extending, and customizing
- **[CHANGELOG.md](cloud-gaming-libre-speed/CHANGELOG.md)**: Version history and updates

## 🎨 Customization

### Change Color Scheme

Edit `assets/css/style.css`:

```css
:root {
    --cgst-primary: #00f7ff;      /* Neon cyan */
    --cgst-secondary: #ff006e;    /* Hot pink */
    --cgst-accent: #ffbe0b;       /* Gold */
}
```

### Adjust Rating Thresholds

Edit `includes/database.php`:

```php
public static function get_rating_thresholds() {
    return array(
        'excellent' => array(
            'download' => 200,  // Increase from 150
            'ping'     => 15,   // Decrease from 20
            // ...
        ),
    );
}
```

See [CUSTOMIZATION.md](cloud-gaming-libre-speed/CUSTOMIZATION.md) for complete guide.

## 🔌 Shortcode Options

```
[cloudspeedtest]
```

Future attributes (planned):
- `theme="neon|dark|light"` - Color scheme
- `layout="full|compact"` - Display mode
- `auto="true|false"` - Default to auto server selection

## 🐛 Troubleshooting

**Tests not starting?**
- Check browser console for CORS errors
- Verify LibreSpeed backend is accessible
- Ensure CORS headers configured correctly

**Inaccurate results?**
- Close bandwidth-heavy applications
- Use Ethernet instead of WiFi
- Test multiple times and average

**Styling issues?**
- Clear browser cache
- Check for theme CSS conflicts
- Increase plugin CSS specificity

See [INSTALL.md](cloud-gaming-libre-speed/INSTALL.md) for detailed troubleshooting.

## 🤝 Contributing

Contributions welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Follow WordPress coding standards
4. Test thoroughly
5. Submit pull request with clear description

## 📄 License

GPL v2 or later - [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

## 🙏 Credits

- **LibreSpeed**: [https://github.com/librespeed/speedtest](https://github.com/librespeed/speedtest)
- **WordPress**: [https://wordpress.org](https://wordpress.org)
- Gaming UI inspiration from modern cloud gaming platforms

## 📧 Support

- **Issues**: Submit via GitHub Issues
- **Documentation**: See `/cloud-gaming-libre-speed/` directory
- **Feature Requests**: Open a GitHub Discussion

## 🌟 Roadmap

- [ ] Multiple theme presets
- [ ] Geolocation-based auto server selection
- [ ] WebRTC peer connection testing
- [ ] Historical performance graphs
- [ ] Comparison with previous tests
- [ ] Social sharing of results
- [ ] Email notifications
- [ ] Integration with cloud gaming APIs
- [ ] Gutenberg block
- [ ] Elementor widget
- [ ] REST API endpoints

---

**Made with ❤️ for Cloud Gamers**

If you find this plugin useful, please star the repository and share it with the gaming community! 🎮🚀
