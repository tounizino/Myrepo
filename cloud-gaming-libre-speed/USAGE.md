# Usage Guide - Cloud Gaming Speed Test

This guide covers how to use the plugin as an administrator and what end-users will experience.

## For Site Administrators

### Initial Setup

1. **Install & Activate**
   - Upload plugin to `/wp-content/plugins/cloud-gaming-libre-speed/`
   - Activate via WordPress Admin > Plugins

2. **Configure LibreSpeed Backends**
   - Deploy LibreSpeed on your servers (see INSTALL.md)
   - Configure CORS headers to allow your WordPress domain

3. **Add Server Presets**
   - Go to **Speed Test** > **Server Presets**
   - Click "Add / Update Server"
   - Fill in the form and save

### Managing Server Presets

#### Adding a New Server

1. Navigate to **Speed Test** > **Server Presets**
2. Fill in the form:

   **Required Fields:**
   - **Server Name**: Display name (e.g., "US East - Virginia")
   - **Location Label**: Geographic info (e.g., "Ashburn, VA, USA")
   - **Base Backend URL**: LibreSpeed root URL (e.g., "https://speed.example.com/")

   **Optional Fields:**
   - **Priority Weight**: Lower numbers = higher priority in auto-select
   - **Download/Upload/Ping Paths**: Customize if LibreSpeed uses different endpoints
   - **Dashicon**: WordPress dashicon class for visual identification
   - **Latitude/Longitude**: For future geolocation features
   - **Notes**: Internal notes or user-facing tips

3. Click **Save Server**

#### Editing a Server

1. Find the server in the "Configured Servers" table
2. Click **Edit** button
3. Form populates with existing data
4. Make changes and click **Save Server**

#### Deleting a Server

1. Find the server in the table
2. Click **Delete** button
3. Confirm deletion
4. Server removed from presets

### Managing Articles & Tips

Curate helpful resources that appear on the frontend after users run tests.

#### Adding an Article

1. Navigate to **Speed Test** > **Articles & Tips**
2. Fill in the form:
   - **Article Title**: e.g., "10 Tips to Reduce Cloud Gaming Latency"
   - **Article URL**: Full URL to your article
   - **Description**: Short summary (1-2 sentences)
   - **Category**: e.g., "Optimization", "Networking", "Guides"
3. Click **Save Article**

#### Editing/Deleting Articles

- Click **Edit** to load article into form
- Click **Delete** to remove (with confirmation)

### Viewing Test History

1. Go to **Speed Test** > **Test History**
2. See all recorded tests with:
   - Timestamp
   - Server used
   - Download/Upload speeds
   - Ping and jitter
   - Packet loss
   - Cloud gaming rating
   - Recommendation given

3. Click **Export CSV** to download data for:
   - Analytics and reporting
   - Client presentations
   - Performance tracking over time

### Embedding the Speed Test

#### Method 1: Shortcode in Page/Post

1. Create or edit a page
2. Add shortcode: `[cloudspeedtest]`
3. Publish

The shortcode renders the full speed test interface.

#### Method 2: Widget Area

1. Go to **Appearance** > **Widgets**
2. Add a "Custom HTML" or "Shortcode" widget
3. Enter: `[cloudspeedtest]`
4. Save

#### Method 3: PHP Template

In your theme files:

```php
<?php
if (function_exists('cgst_init')) {
    echo do_shortcode('[cloudspeedtest]');
}
?>
```

### Dashboard Overview

**Speed Test** > **Dashboard** shows:
- Getting started instructions
- Server preset summary
- Recent test results (last 5)
- Featured guides
- Shortcode reference

Use this page to quickly assess plugin status and recent activity.

---

## For End Users (Frontend Experience)

### Running a Speed Test

1. **Visit Test Page**
   - Navigate to the page where the shortcode is embedded
   - You'll see the "Cloud Gaming Speed Test" interface

2. **Select Server Mode**
   - **Auto (Fastest)**: Plugin automatically tests all servers and selects the fastest (recommended)
   - **Manual Selection**: Choose a specific server from the dropdown

3. **Start Test**
   - Click the glowing "Start Test" button
   - Test begins with animated progress bars

4. **Testing Phases**
   - **Testing Latency**: Pings server 10 times to measure ping and jitter
   - **Testing Download**: Downloads data chunks for 10 seconds to measure speed
   - **Testing Upload**: Uploads data chunks for 10 seconds to measure speed

5. **View Results**
   - Results display in 4 animated cards:
     - **Download** (Mbps)
     - **Upload** (Mbps)
     - **Ping** (ms)
     - **Jitter** (ms)

6. **Cloud Gaming Rating**
   - Colored badge shows rating:
     - **Excellent** 🏆 (Green): Perfect for 4K @ 120fps
     - **Good** ✅ (Blue): Great for 1440p @ 60fps
     - **Fair** ⚠️ (Orange): Suitable for 1080p @ 60fps
     - **Poor** ❌ (Red): Best for 720p @ 60fps

7. **Recommendation**
   - Personalized suggestion based on results
   - Includes optimal resolution, framerate, and streaming quality

8. **Run Another Test**
   - Click "Run Another Test" button to retry
   - Useful for testing at different times or after network changes

9. **Optimization Resources**
   - Scroll down to see curated articles and guides
   - Categories include Optimization, Networking, Guides
   - Click to learn how to improve connection

### Understanding Your Results

#### Download Speed
- **Excellent**: ≥150 Mbps
- **Good**: ≥90 Mbps
- **Fair**: ≥45 Mbps
- **Poor**: <45 Mbps

Higher download speed = better video quality streaming from cloud gaming servers.

#### Upload Speed
- **Excellent**: ≥25 Mbps
- **Good**: ≥15 Mbps
- **Fair**: ≥8 Mbps
- **Poor**: <8 Mbps

Upload speed affects controller input responsiveness (low latency uploads critical for gaming).

#### Ping/Latency
- **Excellent**: ≤20 ms
- **Good**: ≤35 ms
- **Fair**: ≤55 ms
- **Poor**: >55 ms

Lower ping = less input lag. Critical for fast-paced games.

#### Jitter
- **Excellent**: ≤5 ms
- **Good**: ≤8 ms
- **Fair**: ≤12 ms
- **Poor**: >12 ms

Jitter measures ping variance. Low jitter = stable connection.

#### Packet Loss
- **Excellent**: ≤0.1%
- **Good**: ≤0.3%
- **Fair**: ≤0.8%
- **Poor**: >0.8%

Any packet loss can cause stuttering or disconnects. Aim for 0%.

### Tips for Best Results

1. **Use Wired Connection**
   - Ethernet cable > WiFi for stability
   - WiFi introduces latency and jitter

2. **Close Background Apps**
   - Stop downloads, uploads, streaming
   - Ensure no one else is using bandwidth

3. **Test Multiple Times**
   - Network conditions vary throughout the day
   - Test during your typical gaming hours

4. **Try Different Servers**
   - Manually test each region
   - Choose server closest to your cloud gaming provider's datacenter

5. **Consider Time of Day**
   - ISP congestion varies (peak hours = slower)
   - Off-peak hours often yield better results

### Troubleshooting for Users

**Test Won't Start**
- Ensure JavaScript is enabled
- Try different browser (Chrome recommended)
- Disable ad blockers temporarily

**Test Fails or Times Out**
- Check your internet connection
- Try manual server selection
- Contact site administrator if issue persists

**Results Seem Inaccurate**
- Close bandwidth-heavy applications
- Connect via Ethernet instead of WiFi
- Test multiple times and average results

**Slow Loading**
- Clear browser cache
- Disable browser extensions
- Check your own internet speed first

---

## Use Cases

### For Gamers
- **Pre-purchase assessment**: Test before subscribing to GeForce NOW, Xbox Cloud Gaming, etc.
- **Server selection**: Determine which region gives best performance
- **Troubleshooting**: Identify connection issues affecting gameplay
- **ISP comparison**: Test different ISPs before switching

### For Content Creators
- **Blog content**: Embed test on articles about cloud gaming
- **Product reviews**: Compare cloud gaming services with real data
- **Tutorials**: Help audience optimize their setup
- **Monetization**: Affiliate links to networking gear, VPNs, ISPs

### For Network Professionals
- **Client reporting**: Export CSV of historical tests
- **Optimization validation**: Before/after network changes
- **SLA verification**: Ensure ISP meets promised speeds
- **Documentation**: Record network performance over time

---

## Advanced Usage

### Custom CSS

Override plugin styles in your theme's `style.css`:

```css
/* Change primary color */
.cgst-wrapper {
    --cgst-primary: #00ff00;
}

/* Adjust font size */
.cgst-title {
    font-size: 3rem;
}

/* Custom button style */
.cgst-btn-start {
    background: linear-gradient(135deg, #ff00ff 0%, #00ffff 100%);
}
```

### Custom JavaScript

Add custom behavior in theme's JS file:

```javascript
jQuery(document).ready(function($) {
    // Log when test completes
    $(document).on('cgst-test-complete', function(event, results) {
        console.log('Test completed:', results);
    });
});
```

### Multiple Instances on Same Page

You can embed multiple shortcodes:

```
[cloudspeedtest]

Some content here...

[cloudspeedtest]
```

Each instance operates independently.

---

## FAQ

**Q: How long does a test take?**  
A: Approximately 30-40 seconds (10s ping, 10s download, 10s upload + transitions).

**Q: Does testing use my data cap?**  
A: Yes, approximately 100-200 MB per test.

**Q: Can I test from mobile?**  
A: Yes! Plugin is fully responsive and mobile-friendly.

**Q: How accurate are the results?**  
A: Very accurate. LibreSpeed is industry-standard and used by many ISPs.

**Q: Can I white-label this plugin?**  
A: Yes, modify branding in template files and CSS. GPL license permits this.

**Q: Does it work with caching plugins?**  
A: Yes, but exclude speed test page from caching for best results.

**Q: Can I translate the plugin?**  
A: Yes, plugin is translation-ready. Use Loco Translate or similar tools.

---

## Support

For questions, issues, or feature requests:
- Check documentation: README.md, INSTALL.md
- Review troubleshooting section
- Contact plugin author or submit GitHub issue

Happy testing! 🎮🚀
