# Cloud Gaming Speed Test - LibreSpeed WordPress Plugin

A stunning, feature-rich WordPress plugin designed specifically for cloud gaming enthusiasts. Test internet speed with LibreSpeed backend integration, get instant cloud gaming suitability ratings, and receive personalized recommendations.

## 🎮 Features

### Core Functionality
- **LibreSpeed Integration**: Uses LibreSpeed as the backend for accurate speed measurements
- **Multiple Test Metrics**: Download, upload, ping, jitter, and packet loss
- **Auto Server Selection**: Automatically pings multiple servers and selects the fastest
- **Manual Server Selection**: Choose from pre-configured server presets (US East, US West, EU Central, Asia Pacific)
- **Cloud Gaming Assessment**: Instant rating (Excellent/Good/Fair/Poor) based on cloud gaming requirements
- **Smart Recommendations**: Suggests optimal settings (4K/120fps, 1440p/60fps, 1080p/60fps, 720p/60fps)

### Visual Design
- **Gaming-Style UI**: Eye-catching neon color scheme with bold gradients
- **Smooth Animations**: Progress bars, glowing effects, pulsing badges, and slide-in transitions
- **Responsive Layout**: Mobile-friendly design that adapts to all screen sizes
- **Real-Time Progress**: Visual feedback during testing with animated progress bars
- **Result Cards**: Beautifully designed cards with icons and animated hover effects

### Admin Features
- **Dashboard Overview**: Quick stats, recent tests, and server summary
- **Server Management**: Add/edit/delete LibreSpeed backend servers with full configuration
- **Article Management**: Curate optimization guides and resources for users
- **Test History**: View all recorded tests with detailed metrics
- **CSV Export**: Download test history for analytics and reporting
- **Settings Reference**: Comprehensive documentation and threshold information

## 📦 Installation

1. Download the plugin folder `cloud-gaming-libre-speed`
2. Upload to `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to **Speed Test** > **Server Presets** in admin dashboard
5. Configure your LibreSpeed backend URLs

## 🚀 Quick Start

### Step 1: Deploy LibreSpeed Backends

You need to deploy LibreSpeed on your servers or use existing instances. LibreSpeed is open-source and can be self-hosted.

**Option A: Use Docker**
```bash
docker run -d -p 80:80 adolfintel/speedtest
```

**Option B: Manual Installation**
Follow the [LibreSpeed documentation](https://github.com/librespeed/speedtest)

### Step 2: Configure Server Presets

1. Go to **Speed Test** > **Server Presets**
2. Add your server details:
   - **Server Name**: e.g., "US East - New York"
   - **Location Label**: e.g., "New York, NY, USA"
   - **Base Backend URL**: e.g., "https://speed.example.com/us-east/"
   - **Download/Upload/Ping Paths**: Usually `download.php`, `upload.php`, `ping.php` (LibreSpeed defaults)
   - **Geo Coordinates** (optional): For geolocation-based auto-selection

### Step 3: Embed on Your Site

Use the shortcode anywhere:

```
[cloudspeedtest]
```

Add to a page, post, or widget area!

## 🎨 Customization

### CSS Variables

Edit `assets/css/style.css` to customize colors:

```css
:root {
    --cgst-primary: #00f7ff;      /* Main neon cyan */
    --cgst-secondary: #ff006e;    /* Hot pink accent */
    --cgst-accent: #ffbe0b;       /* Yellow/gold */
    --cgst-success: #06ffa5;      /* Green for excellent */
    --cgst-dark: #0a0e27;         /* Dark background */
}
```

### Rating Thresholds

Modify cloud gaming assessment criteria in two places:

**Backend (PHP)**: `includes/database.php` - `calculate_rating()` method

```php
if ($download >= 150 && $upload >= 25 && $ping <= 20 && $jitter <= 5 && $packetLoss <= 0.1) {
    $rating = 'Excellent';
    $recommendation = '4K / 120fps – perfect for cloud gaming';
}
```

**Frontend (JS)**: `assets/js/speed-test.js` - `calculateRating()` method

### Template Modification

Edit `templates/speed-test-template.php` to change the layout, add custom sections, or modify text.

## 🔧 Server Configuration

### Server Preset Structure

```json
{
  "id": "us-east",
  "name": "US East - Ashburn",
  "location": "Ashburn, VA, USA",
  "backend": "https://speed.example.com/us-east/",
  "download_path": "download.php",
  "upload_path": "upload.php",
  "ping_path": "ping.php",
  "icon": "dashicons-location-alt",
  "weight": 1,
  "geo": {
    "lat": 39.0438,
    "lng": -77.4874
  },
  "notes": "Ideal for gamers on the US East Coast."
}
```

### CORS Configuration

LibreSpeed backends must allow CORS from your WordPress site. Add to your LibreSpeed server `.htaccess` or nginx config:

**Apache (.htaccess)**
```apache
Header set Access-Control-Allow-Origin "https://yourdomain.com"
Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
Header set Access-Control-Allow-Headers "Content-Type"
```

**Nginx**
```nginx
add_header 'Access-Control-Allow-Origin' 'https://yourdomain.com';
add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS';
add_header 'Access-Control-Allow-Headers' 'Content-Type';
```

## 📊 Cloud Gaming Ratings

| Rating | Download | Upload | Ping | Jitter | Packet Loss | Recommendation |
|--------|----------|--------|------|--------|-------------|----------------|
| **Excellent** | ≥150 Mbps | ≥25 Mbps | ≤20 ms | ≤5 ms | ≤0.1% | 4K @ 120fps |
| **Good** | ≥90 Mbps | ≥15 Mbps | ≤35 ms | ≤8 ms | ≤0.3% | 1440p @ 60fps |
| **Fair** | ≥45 Mbps | ≥8 Mbps | ≤55 ms | ≤12 ms | ≤0.8% | 1080p @ 60fps |
| **Poor** | Below Fair thresholds | | | | | 720p @ 60fps |

## 📱 Usage Examples

### Basic Shortcode
```
[cloudspeedtest]
```

### With Attributes (Future Enhancement)
```
[cloudspeedtest theme="neon" layout="compact"]
```

## 🗂️ File Structure

```
cloud-gaming-libre-speed/
├── cloud-gaming-speed-test.php       # Main plugin file
├── includes/
│   ├── database.php                  # Database operations, rating calculation
│   └── ajax-handlers.php             # AJAX request handlers
├── assets/
│   ├── css/
│   │   ├── style.css                 # Frontend gaming-style CSS
│   │   └── admin-style.css           # Admin panel styles
│   └── js/
│       ├── speed-test.js             # LibreSpeed integration & animations
│       └── admin-script.js           # Admin panel interactions
├── templates/
│   ├── speed-test-template.php       # Frontend shortcode template
│   ├── admin-dashboard.php           # Admin dashboard
│   ├── admin-servers.php             # Server management
│   ├── admin-articles.php            # Article management
│   ├── admin-history.php             # Test history viewer
│   └── admin-settings.php            # Settings & documentation
└── README.md                          # This file
```

## 🛠️ Technical Details

### Database Tables

**cgst_results**: Stores historic test results
- `id`, `created_at`, `server_id`, `server_name`
- `download_mbps`, `upload_mbps`, `ping_ms`, `jitter_ms`, `packet_loss`
- `rating`, `recommendation`, `settings_json`

### WordPress Options

- `cgst_servers`: Server presets (JSON array)
- `cgst_articles`: Featured articles/tips (JSON array)
- `cgst_settings`: Plugin settings (reserved for future use)

### AJAX Actions

**Frontend:**
- `cgst_save_result`: Save speed test result
- `cgst_get_articles`: Fetch articles for display

**Admin:**
- `cgst_save_server`: Add/update server preset
- `cgst_delete_server`: Remove server preset
- `cgst_save_article`: Add/update article
- `cgst_delete_article`: Remove article
- `cgst_export_csv`: Download test history CSV

## 🎯 Best Practices

### Server Selection
- Deploy LibreSpeed backends in multiple regions (US East, West, EU, Asia)
- Use CDN or edge locations for lowest latency
- Monitor backend performance regularly

### Performance Optimization
- Test duration: 10 seconds per test (download/upload)
- Number of ping tests: 10 samples for jitter calculation
- Chunk size: 10MB download, 1MB upload

### User Experience
- Always show real-time progress during tests
- Provide clear feedback on ratings
- Link to optimization resources after testing

## 🔒 Security

- All inputs are sanitized using WordPress sanitization functions
- AJAX requests use WordPress nonces for CSRF protection
- Database queries use `$wpdb->prepare()` for SQL injection prevention
- Admin functions check `manage_options` capability

## 📝 Extending the Plugin

### Add Custom Rating Tier

Edit `includes/database.php` and `assets/js/speed-test.js`:

```php
// PHP (backend)
elseif ($download >= 200 && $upload >= 50 && $ping <= 10 && $jitter <= 3 && $packetLoss <= 0.05) {
    $rating = 'Ultra';
    $recommendation = '8K @ 240fps – professional esports ready';
    $icon = 'cgst-icon-ultra';
}
```

```javascript
// JS (frontend)
else if (d >= 200 && u >= 50 && p <= 10 && j <= 3 && pl <= 0.05) {
    rating = 'Ultra';
    recommendation = '8K @ 240fps – professional esports ready';
    icon = '🚀';
    className = 'rating-ultra';
}
```

### Add Custom Animations

Edit `assets/css/style.css`:

```css
@keyframes myCustomAnimation {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.my-element {
    animation: myCustomAnimation 2s ease-in-out infinite;
}
```

## 🐛 Troubleshooting

### Tests Not Running
1. Check browser console for CORS errors
2. Verify LibreSpeed backend is accessible
3. Ensure server URLs end with trailing slash

### Inaccurate Results
1. Verify LibreSpeed backend is properly configured
2. Check network conditions on server side
3. Test with multiple server presets

### Styling Issues
1. Clear browser cache and reload
2. Check for theme CSS conflicts
3. Inspect element and adjust specificity

## 📄 License

GPL v2 or later

## 🤝 Credits

- **LibreSpeed**: [https://github.com/librespeed/speedtest](https://github.com/librespeed/speedtest)
- **WordPress**: [https://wordpress.org](https://wordpress.org)

## 🎮 Support

For issues, feature requests, or contributions, please visit the plugin repository or contact the plugin author.

## 🌟 Future Enhancements

- [ ] Multiple theme presets (neon, dark, light, retro)
- [ ] Geolocation-based auto server selection
- [ ] WebRTC peer connection testing
- [ ] Browser capability detection
- [ ] Network quality score over time (graphs)
- [ ] Comparison with previous tests
- [ ] Social sharing of results
- [ ] Email notifications for poor performance
- [ ] Integration with cloud gaming platforms APIs (Stadia, GeForce NOW, etc.)

---

**Made with ❤️ for Cloud Gamers**
