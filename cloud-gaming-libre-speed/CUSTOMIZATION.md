# Customization Guide

This guide covers how to customize the Cloud Gaming Speed Test plugin to match your brand, adjust thresholds, and extend functionality.

## Table of Contents

1. [Styling & Branding](#styling--branding)
2. [Rating Thresholds](#rating-thresholds)
3. [Server Configuration](#server-configuration)
4. [Template Modifications](#template-modifications)
5. [JavaScript Extensions](#javascript-extensions)
6. [Adding Custom Features](#adding-custom-features)

---

## Styling & Branding

### Changing Color Scheme

Edit `assets/css/style.css` and modify CSS variables:

```css
:root {
    /* Neon Gaming Theme (default) */
    --cgst-primary: #00f7ff;      /* Cyan - main accent */
    --cgst-secondary: #ff006e;    /* Pink - secondary accent */
    --cgst-accent: #ffbe0b;       /* Yellow/gold */
    --cgst-success: #06ffa5;      /* Green for excellent */
    --cgst-warning: #ff9d00;      /* Orange for warnings */
    --cgst-danger: #ff006e;       /* Red for poor */
    --cgst-dark: #0a0e27;         /* Dark background */
    --cgst-darker: #050816;       /* Darker background */
    --cgst-card-bg: #12172e;      /* Card background */
    --cgst-text-primary: #ffffff; /* White text */
    --cgst-text-secondary: #b0b8d4; /* Gray text */
}
```

**Example: Dark Blue Theme**

```css
:root {
    --cgst-primary: #1e90ff;      /* Dodger blue */
    --cgst-secondary: #00bfff;    /* Deep sky blue */
    --cgst-accent: #ffd700;       /* Gold */
    --cgst-success: #32cd32;      /* Lime green */
    --cgst-warning: #ffa500;      /* Orange */
    --cgst-danger: #dc143c;       /* Crimson */
    --cgst-dark: #1a1a2e;         /* Dark navy */
    --cgst-darker: #0f0f1e;       /* Darker navy */
    --cgst-card-bg: #16213e;      /* Card navy */
}
```

**Example: Retro Purple Theme**

```css
:root {
    --cgst-primary: #9d4edd;      /* Purple */
    --cgst-secondary: #c77dff;    /* Light purple */
    --cgst-accent: #e0aaff;       /* Lavender */
    --cgst-success: #7209b7;      /* Deep purple */
    --cgst-warning: #f72585;      /* Hot pink */
    --cgst-danger: #b5179e;       /* Magenta */
    --cgst-dark: #240046;         /* Deep purple black */
    --cgst-darker: #10002b;       /* Darkest purple */
    --cgst-card-bg: #3c096c;      /* Purple card */
}
```

### Custom Fonts

Add custom fonts to your theme's `functions.php`:

```php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('custom-fonts', 'https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap');
});
```

Then override in CSS:

```css
.cgst-wrapper {
    font-family: 'Orbitron', sans-serif;
}

.cgst-title {
    font-family: 'Orbitron', sans-serif;
    font-weight: 900;
}
```

### Logo/Branding

Edit `templates/speed-test-template.php`:

```php
<div class="cgst-header">
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png" alt="Logo" class="cgst-logo">
    <h2 class="cgst-title">
        <?php esc_html_e('Your Brand Speed Test', 'cloud-gaming-speed-test'); ?>
    </h2>
    <!-- ... -->
</div>
```

Add CSS for logo:

```css
.cgst-logo {
    max-width: 200px;
    margin-bottom: 20px;
}
```

### Button Styles

Override button styles:

```css
.cgst-btn-start {
    background: linear-gradient(135deg, #your-color1 0%, #your-color2 100%);
    border-radius: 10px; /* Change from 50px to 10px for less rounded */
    padding: 15px 40px;
    font-size: 1.2rem;
}

.cgst-btn-start:hover {
    transform: scale(1.1);
    box-shadow: 0 20px 50px rgba(your-color, 0.6);
}
```

### Animation Speed

Adjust animation durations:

```css
@keyframes cgstPulse {
    /* Speed up from 2s to 1s */
}

.cgst-rating-badge {
    animation: cgstPulse 1s ease-in-out infinite; /* Changed from 2s */
}

.cgst-progress-fill {
    animation: cgstProgressFlow 0.5s linear infinite; /* Changed from 1s */
}
```

---

## Rating Thresholds

### Adjusting Thresholds

Edit both files for consistency:

**PHP Backend**: `includes/database.php`

```php
public static function get_rating_thresholds() {
    return array(
        'excellent' => array(
            'download'    => 200,  // Changed from 150
            'upload'      => 30,   // Changed from 25
            'ping'        => 15,   // Changed from 20
            'jitter'      => 3,    // Changed from 5
            'packet_loss' => 0.05, // Changed from 0.1
        ),
        'good' => array(
            'download'    => 100,  // Changed from 90
            'upload'      => 20,   // Changed from 15
            'ping'        => 30,   // Changed from 35
            'jitter'      => 6,    // Changed from 8
            'packet_loss' => 0.2,  // Changed from 0.3
        ),
        'fair' => array(
            'download'    => 50,   // Changed from 45
            'upload'      => 10,   // Changed from 8
            'ping'        => 50,   // Changed from 55
            'jitter'      => 10,   // Changed from 12
            'packet_loss' => 0.5,  // Changed from 0.8
        ),
    );
}
```

**JavaScript Frontend**: `assets/js/speed-test.js`

```javascript
calculateRating() {
    const d = this.results.download;
    const u = this.results.upload;
    const p = this.results.ping;
    const j = this.results.jitter;
    const pl = this.results.packetLoss;

    let rating = 'Poor';
    let recommendation = '720p @ 60fps...';
    let icon = '❌';
    let className = 'rating-poor';

    // Update thresholds to match PHP
    if (d >= 200 && u >= 30 && p <= 15 && j <= 3 && pl <= 0.05) {
        rating = 'Excellent';
        recommendation = '4K / 120fps...';
        icon = '🏆';
        className = 'rating-excellent';
    } else if (d >= 100 && u >= 20 && p <= 30 && j <= 6 && pl <= 0.2) {
        rating = 'Good';
        recommendation = '1440p / 60fps...';
        icon = '✅';
        className = 'rating-good';
    } else if (d >= 50 && u >= 10 && p <= 50 && j <= 10 && pl <= 0.5) {
        rating = 'Fair';
        recommendation = '1080p / 60fps...';
        icon = '⚠️';
        className = 'rating-fair';
    }

    return { rating, recommendation, icon, className };
}
```

### Adding New Rating Tier

**Example: Add "Ultra" tier above Excellent**

**PHP Backend**:

```php
public static function get_rating_thresholds() {
    return array(
        'ultra' => array(
            'download'    => 300,
            'upload'      => 50,
            'ping'        => 10,
            'jitter'      => 2,
            'packet_loss' => 0.01,
        ),
        'excellent' => array(
            // existing...
        ),
        // ...
    );
}
```

Then update `calculate_rating()`:

```php
$bands = array(
    'ultra' => array(
        'label'         => __('Ultra', 'cloud-gaming-speed-test'),
        'recommendation'=> __('8K / 240fps – professional esports ready!', 'cloud-gaming-speed-test'),
        'icon'          => 'cgst-icon-ultra',
    ),
    // existing bands...
);

// Add check for ultra tier first
if (
    $download >= $thresholds['ultra']['download'] &&
    $upload >= $thresholds['ultra']['upload'] &&
    // etc...
) {
    $ratingKey = 'ultra';
} elseif (
    // existing excellent check...
) {
    // ...
}
```

**JavaScript Frontend**:

```javascript
if (d >= 300 && u >= 50 && p <= 10 && j <= 2 && pl <= 0.01) {
    rating = 'Ultra';
    recommendation = '8K / 240fps – professional esports ready!';
    icon = '🚀';
    className = 'rating-ultra';
}
```

**CSS** - Add styling:

```css
.cgst-rating-badge.rating-ultra {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #000;
    box-shadow: 0 0 40px #ffd700;
}
```

---

## Server Configuration

### Custom Endpoint Paths

If your LibreSpeed uses custom paths:

**Admin Panel** > **Server Presets** > Edit server:

```
Download Endpoint Path: garbage.php         (default)
Upload Endpoint Path:   empty.php           (default)
Ping Endpoint Path:     empty.php           (default)

OR

Download Endpoint Path: api/test/download
Upload Endpoint Path:   api/test/upload
Ping Endpoint Path:     api/ping
```

### Adding Geolocation Auto-Selection

**Future enhancement placeholder**:

In `assets/js/speed-test.js`, modify `findFastestServer()`:

```javascript
async findFastestServer() {
    // Get user's geolocation
    const userLocation = await this.getUserGeolocation();
    
    if (userLocation) {
        // Calculate distance to each server
        const serversWithDistance = this.servers.map(server => {
            const distance = this.calculateDistance(
                userLocation.lat, userLocation.lng,
                server.geo.lat, server.geo.lng
            );
            return { server, distance };
        });
        
        // Sort by distance and test closest 3
        serversWithDistance.sort((a, b) => a.distance - b.distance);
        const nearestServers = serversWithDistance.slice(0, 3).map(s => s.server);
        
        // Ping nearest servers
        const pingResults = [];
        for (const server of nearestServers) {
            const ping = await this.measurePing(server.pingUrl);
            pingResults.push({ server, ping });
        }
        
        pingResults.sort((a, b) => a.ping - b.ping);
        return pingResults[0].server;
    } else {
        // Fallback to ping all servers
        // ...existing code
    }
}

getUserGeolocation() {
    return new Promise((resolve) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => resolve({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                }),
                () => resolve(null)
            );
        } else {
            resolve(null);
        }
    });
}

calculateDistance(lat1, lng1, lat2, lng2) {
    // Haversine formula
    const R = 6371; // Earth radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}
```

---

## Template Modifications

### Adding Custom Content

Edit `templates/speed-test-template.php`:

**Add promotional banner**:

```php
<div class="cgst-promo-banner">
    <p>
        <?php esc_html_e('Want faster speeds?', 'cloud-gaming-speed-test'); ?>
        <a href="/upgrade">
            <?php esc_html_e('Upgrade your plan', 'cloud-gaming-speed-test'); ?>
        </a>
    </p>
</div>
```

**Add social sharing**:

```php
<div class="cgst-social-share">
    <p><?php esc_html_e('Share your results:', 'cloud-gaming-speed-test'); ?></p>
    <button class="cgst-share-twitter" data-share="twitter">
        <?php esc_html_e('Share on Twitter', 'cloud-gaming-speed-test'); ?>
    </button>
    <button class="cgst-share-facebook" data-share="facebook">
        <?php esc_html_e('Share on Facebook', 'cloud-gaming-speed-test'); ?>
    </button>
</div>
```

Add JS handler:

```javascript
$('.cgst-share-twitter').on('click', function() {
    const text = `I got ${self.results.download.toFixed(0)} Mbps download on my cloud gaming speed test! 🎮`;
    const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}`;
    window.open(url, '_blank');
});
```

### Custom Result Cards

Add a new metric (e.g., "Stability Score"):

```php
<div class="cgst-result-card cgst-card-stability">
    <div class="cgst-result-icon cgst-icon-stability-result"></div>
    <div class="cgst-result-value" id="cgst-result-stability">--</div>
    <div class="cgst-result-label"><?php esc_html_e('Stability', 'cloud-gaming-speed-test'); ?></div>
    <div class="cgst-result-unit"><?php esc_html_e('Score', 'cloud-gaming-speed-test'); ?></div>
</div>
```

Calculate in JS:

```javascript
// In displayResults()
const stabilityScore = this.calculateStability();
$('#cgst-result-stability').text(stabilityScore);

// Add method
calculateStability() {
    // Lower jitter and packet loss = higher stability
    const jitterScore = Math.max(0, 100 - (this.results.jitter * 5));
    const packetLossScore = Math.max(0, 100 - (this.results.packetLoss * 100));
    return Math.round((jitterScore + packetLossScore) / 2);
}
```

---

## JavaScript Extensions

### Custom Events

Dispatch custom events for integration:

In `assets/js/speed-test.js`:

```javascript
// After test completes
displayResults(server) {
    // ...existing code
    
    // Dispatch custom event
    $(document).trigger('cgst-test-complete', [{
        download: this.results.download,
        upload: this.results.upload,
        ping: this.results.ping,
        jitter: this.results.jitter,
        server: server,
        rating: this.calculateRating()
    }]);
}
```

**Listen in your theme**:

```javascript
jQuery(document).on('cgst-test-complete', function(event, results) {
    console.log('Speed test completed:', results);
    
    // Send to analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'speed_test_complete', {
            'download_speed': results.download,
            'upload_speed': results.upload,
            'ping': results.ping,
            'rating': results.rating.rating
        });
    }
    
    // Show custom popup
    if (results.rating.rating === 'Poor') {
        showUpgradeModal();
    }
});
```

### Progress Callbacks

Add real-time callbacks during test:

```javascript
async testDownload(server) {
    // ...existing code
    
    const elapsed = (performance.now() - startTime) / 1000;
    const currentSpeed = (totalBytes * 8) / (elapsed * 1000000);
    
    // Trigger progress callback
    if (typeof this.onDownloadProgress === 'function') {
        this.onDownloadProgress(currentSpeed, elapsed);
    }
    
    // ...
}

// Usage in custom code
const speedTest = new CloudGamingSpeedTest();
speedTest.onDownloadProgress = function(speed, elapsed) {
    console.log(`Current speed: ${speed.toFixed(2)} Mbps at ${elapsed.toFixed(1)}s`);
    // Update custom UI
};
```

---

## Adding Custom Features

### Test History Graph

Add chart.js and display historical trend:

**In theme's functions.php**:

```php
add_action('wp_enqueue_scripts', function() {
    if (is_page('speed-test')) {
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
        wp_enqueue_script('cgst-chart', get_stylesheet_directory_uri() . '/js/speed-test-chart.js', array('jquery', 'chart-js', 'cgst-speed-test'), '1.0', true);
    }
});
```

**Create `speed-test-chart.js`**:

```javascript
jQuery(document).ready(function($) {
    // Fetch last 10 test results
    $.ajax({
        url: cgstData.ajaxurl,
        data: {
            action: 'cgst_get_my_history',
            nonce: cgstData.nonce
        },
        success: function(response) {
            if (response.success) {
                renderChart(response.data.history);
            }
        }
    });
    
    function renderChart(history) {
        const ctx = document.getElementById('cgst-history-chart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: history.map(h => h.created_at),
                datasets: [{
                    label: 'Download (Mbps)',
                    data: history.map(h => h.download_mbps),
                    borderColor: '#00f7ff',
                    backgroundColor: 'rgba(0, 247, 255, 0.1)'
                }, {
                    label: 'Upload (Mbps)',
                    data: history.map(h => h.upload_mbps),
                    borderColor: '#ff006e',
                    backgroundColor: 'rgba(255, 0, 110, 0.1)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
```

### Email Notifications

Add email notification for poor performance:

**In `includes/ajax-handlers.php`**:

```php
public function save_result() {
    // ...existing code
    
    $result_id = CGST_Database::insert_result($data);
    
    if ($result_id && $data['rating'] === 'Poor') {
        $this->send_poor_performance_email($data);
    }
    
    // ...
}

private function send_poor_performance_email($data) {
    $admin_email = get_option('admin_email');
    $subject = 'Poor Network Performance Detected';
    $message = sprintf(
        "A speed test showed poor performance:\n\nDownload: %.2f Mbps\nUpload: %.2f Mbps\nPing: %.2f ms\n\nConsider investigating network issues.",
        $data['download_mbps'],
        $data['upload_mbps'],
        $data['ping_ms']
    );
    
    wp_mail($admin_email, $subject, $message);
}
```

---

## Best Practices

1. **Always test changes** in a staging environment first
2. **Keep modifications documented** for future reference
3. **Use child themes** to avoid losing customizations on plugin updates
4. **Follow WordPress coding standards** for consistency
5. **Backup database** before making structural changes
6. **Test across browsers** when modifying CSS/JS
7. **Consider performance** when adding features (lazy load, optimize queries)
8. **Maintain accessibility** (ARIA labels, keyboard navigation, color contrast)

---

## Support

For custom development or complex integrations, consider hiring a WordPress developer familiar with:
- JavaScript/jQuery
- WordPress plugin architecture
- LibreSpeed API
- Modern CSS (Flexbox, Grid, animations)

---

**Happy customizing! 🎮🚀**
