# 📚 Usage Examples

Comprehensive examples for integrating the Cloud Gaming Dashboard into your project.

## 🎯 WordPress Examples

### Example 1: Full Dashboard on a Page

```php
<!-- In your WordPress page/post editor -->
[cloud_gaming_combined]
```

**Result:** Full dashboard with status cards, statistics, and launcher widget.

---

### Example 2: Status Dashboard Only (No Launcher)

```php
<!-- Perfect for a dedicated status page -->
[cloud_gaming_dashboard]
```

**Result:** Status monitoring without the launcher section.

---

### Example 3: Launcher Widget Only (Sidebar)

```php
<!-- In your sidebar widget area -->
[cloud_gaming_launcher]
```

**Result:** Quick launch buttons only, perfect for sidebars.

---

### Example 4: Multiple Instances on Same Page

```php
<!-- Status at top -->
<div style="margin-bottom: 40px;">
    [cloud_gaming_dashboard]
</div>

<!-- Launcher at bottom -->
<div>
    [cloud_gaming_launcher]
</div>
```

**Result:** Separate status and launcher sections.

---

### Example 5: Custom Wrapper with WordPress

```php
<!-- In your theme template file -->
<div class="my-custom-wrapper">
    <h2>Check Cloud Gaming Service Status</h2>
    <?php echo do_shortcode('[cloud_gaming_combined]'); ?>
</div>
```

---

## 🌐 HTML/JavaScript Examples

### Example 1: Basic Integration

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud Gaming Dashboard</title>
    <link rel="stylesheet" href="assets/css/cloud-gaming-dashboard.css">
</head>
<body>
    <div class="cloud-gaming-container" id="my-dashboard"></div>
    <script src="assets/js/cloud-gaming-dashboard.js"></script>
</body>
</html>
```

---

### Example 2: Manual Initialization

```html
<div class="cloud-gaming-container" id="custom-dashboard"></div>

<script src="assets/js/cloud-gaming-dashboard.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dashboard = new CloudGamingDashboard('custom-dashboard');
    });
</script>
```

---

### Example 3: With Custom Container Styles

```html
<style>
    .my-dashboard-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .cloud-gaming-container {
        /* Override default gradient */
        background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%) !important;
    }
</style>

<div class="my-dashboard-wrapper">
    <h1>Cloud Gaming Status</h1>
    <div class="cloud-gaming-container" id="status-dashboard"></div>
</div>
```

---

### Example 4: Multiple Dashboards

```html
<!-- Main dashboard -->
<div class="cloud-gaming-container" id="main-dashboard"></div>

<!-- Secondary dashboard with different styling -->
<div class="cloud-gaming-container dark-mode" id="secondary-dashboard" 
     style="margin-top: 40px;">
</div>

<script src="assets/js/cloud-gaming-dashboard.js"></script>
```

---

## 🎨 Styling Examples

### Example 1: Custom Color Scheme

```html
<style>
    /* Blue theme */
    .cloud-gaming-container {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
    }
    
    .cgd-btn-primary {
        background: linear-gradient(135deg, #2196F3, #1976D2) !important;
    }
    
    .cgd-status-badge.online {
        background: #E3F2FD !important;
        color: #1565C0 !important;
    }
</style>

<div class="cloud-gaming-container" id="blue-themed-dashboard"></div>
```

---

### Example 2: Compact Mode

```html
<style>
    .cloud-gaming-container.compact {
        padding: 10px !important;
    }
    
    .cloud-gaming-container.compact .cgd-status-card {
        padding: 12px !important;
    }
    
    .cloud-gaming-container.compact .cgd-title {
        font-size: 20px !important;
        margin-bottom: 15px !important;
    }
    
    .cloud-gaming-container.compact .cgd-controls,
    .cloud-gaming-container.compact .cgd-stats-section {
        display: none !important;
    }
</style>

<div class="cloud-gaming-container compact" id="compact-dashboard"></div>
```

---

### Example 3: Light Theme Override

```html
<style>
    .cloud-gaming-container.light-theme {
        background: linear-gradient(135deg, #ffffff 0%, #f5f5f5 100%) !important;
    }
    
    .cloud-gaming-container.light-theme .cgd-title,
    .cloud-gaming-container.light-theme .cgd-subtitle {
        color: #333333 !important;
    }
    
    .cloud-gaming-container.light-theme .cgd-filter-btn {
        color: #666666 !important;
    }
</style>

<div class="cloud-gaming-container light-theme" id="light-dashboard"></div>
```

---

## ⚙️ JavaScript Configuration Examples

### Example 1: Custom Services

```javascript
// Before loading the dashboard script, override services
window.cloudGamingCustomConfig = {
    services: [
        {
            id: 'my-service',
            name: 'My Gaming Service',
            type: 'Custom',
            icon: '🎮',
            url: 'https://mygaming.com',
            statusUrl: 'https://status.mygaming.com',
            description: 'My custom gaming service'
        }
    ]
};
```

---

### Example 2: Custom Refresh Rate

```html
<script>
    // Modify after DOM loads but before dashboard initialization
    if (window.CloudGamingDashboard) {
        // Change refresh interval to 2 minutes
        const originalInit = window.CloudGamingDashboard.prototype.init;
        window.CloudGamingDashboard.prototype.init = function() {
            this.refreshInterval = 120000; // 2 minutes
            originalInit.call(this);
        };
    }
</script>

<div class="cloud-gaming-container" id="slow-refresh-dashboard"></div>
```

---

### Example 3: Event Listeners

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const dashboard = new CloudGamingDashboard('my-dashboard');
    
    // Listen for service status changes
    document.addEventListener('serviceStatusChanged', function(e) {
        console.log('Service status changed:', e.detail);
        
        // Custom notification
        if (e.detail.status === 'offline') {
            alert(`Warning: ${e.detail.serviceName} is offline!`);
        }
    });
    
    // Listen for favorite toggles
    document.addEventListener('favoriteToggled', function(e) {
        console.log('Favorite toggled:', e.detail);
    });
});
```

---

## 🔌 API Integration Examples

### Example 1: Real Status Checking (Fetch API)

```javascript
// Replace in cloud-gaming-dashboard.js
async checkService(service) {
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 5000);
        
        const startTime = Date.now();
        const response = await fetch(service.statusUrl, {
            signal: controller.signal,
            mode: 'cors'
        });
        clearTimeout(timeoutId);
        
        const responseTime = Date.now() - startTime;
        const data = await response.json();
        
        return {
            status: data.operational ? 'online' : 'offline',
            responseTime: responseTime,
            lastChecked: new Date().toISOString(),
            uptime: data.uptime || '99.9%',
            region: data.region || 'Global'
        };
    } catch (error) {
        return {
            status: 'unknown',
            error: error.message,
            lastChecked: new Date().toISOString()
        };
    }
}
```

---

### Example 2: WordPress AJAX Integration

```php
// In your WordPress theme's functions.php
add_action('wp_ajax_check_gaming_status', 'check_gaming_status_callback');
add_action('wp_ajax_nopriv_check_gaming_status', 'check_gaming_status_callback');

function check_gaming_status_callback() {
    check_ajax_referer('cloud-gaming-nonce', 'nonce');
    
    $service_id = sanitize_text_field($_POST['service_id']);
    
    // Make real API call
    $response = wp_remote_get("https://api.example.com/status/{$service_id}");
    
    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Failed to check status']);
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);
    
    wp_send_json_success([
        'status' => $data->operational ? 'online' : 'offline',
        'responseTime' => $data->responseTime,
        'uptime' => $data->uptime
    ]);
}
```

```javascript
// In your JavaScript
async checkService(service) {
    try {
        const response = await fetch(cloudGamingAjax.ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'action': 'check_gaming_status',
                'nonce': cloudGamingAjax.nonce,
                'service_id': service.id
            })
        });
        
        const data = await response.json();
        return data.data;
    } catch (error) {
        return { status: 'unknown' };
    }
}
```

---

## 📊 Analytics Integration Examples

### Example 1: Google Analytics 4

```javascript
// Track service launches
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cgd-launcher-card').forEach(card => {
        card.addEventListener('click', function(e) {
            const serviceName = this.dataset.service;
            
            // GA4 event
            gtag('event', 'service_launch', {
                'event_category': 'Cloud Gaming',
                'event_label': serviceName,
                'value': 1
            });
        });
    });
    
    // Track favorites
    document.querySelectorAll('.cgd-favorite-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const serviceId = this.dataset.serviceId;
            const isFavorite = this.classList.contains('active');
            
            gtag('event', 'favorite_toggle', {
                'event_category': 'Cloud Gaming',
                'event_label': serviceId,
                'value': isFavorite ? 1 : 0
            });
        });
    });
});
```

---

### Example 2: Facebook Pixel

```javascript
// Track page view
fbq('track', 'PageView');

// Track launcher clicks
document.addEventListener('click', function(e) {
    if (e.target.closest('.cgd-launcher-card')) {
        const serviceName = e.target.closest('.cgd-launcher-card').dataset.service;
        
        fbq('track', 'ServiceLaunch', {
            service: serviceName,
            timestamp: new Date().toISOString()
        });
    }
});
```

---

## 🎨 Advanced Customization Examples

### Example 1: Custom Status Icons

```javascript
// Override getStatusIcon method
UIRenderer.prototype.getStatusIcon = function(status) {
    const customIcons = {
        online: '✅',
        degraded: '⚠️',
        offline: '❌',
        checking: '⏳',
        unknown: '❓'
    };
    return customIcons[status] || customIcons.unknown;
};
```

---

### Example 2: Add Custom Metrics

```javascript
// Extend the status card to show additional metrics
const originalRenderStatusCards = UIRenderer.prototype.renderStatusCards;
UIRenderer.prototype.renderStatusCards = function() {
    let html = originalRenderStatusCards.call(this);
    
    // Add custom metric to each card
    html = html.replace(
        /<\/div>\s*<div class="cgd-card-footer">/g,
        `<div class="cgd-metric-row">
            <span class="cgd-metric-label">Custom Metric</span>
            <span class="cgd-metric-value">Value</span>
        </div>
        </div>
        <div class="cgd-card-footer">`
    );
    
    return html;
};
```

---

### Example 3: Custom Notification Sound

```javascript
// Play sound on status change
class CustomStatusChecker extends StatusChecker {
    async checkService(service) {
        const newStatus = await super.checkService(service);
        const previousStatus = this.state.getServiceStatus(service.id);
        
        if (previousStatus.status === 'online' && newStatus.status === 'offline') {
            // Play alert sound
            const audio = new Audio('/path/to/alert.mp3');
            audio.play();
        }
        
        return newStatus;
    }
}
```

---

## 🔐 Security Examples

### Example 1: Content Security Policy

```html
<meta http-equiv="Content-Security-Policy" 
      content="default-src 'self'; 
               script-src 'self' 'unsafe-inline' https://www.google-analytics.com; 
               style-src 'self' 'unsafe-inline'; 
               img-src 'self' data: https:; 
               connect-src 'self' https://api.example.com;">
```

---

### Example 2: Rate Limiting Status Checks

```javascript
class RateLimitedStatusChecker extends StatusChecker {
    constructor(state, notifications) {
        super(state, notifications);
        this.lastCheck = {};
        this.minInterval = 30000; // 30 seconds minimum between checks
    }
    
    async checkService(service) {
        const now = Date.now();
        const lastCheckTime = this.lastCheck[service.id] || 0;
        
        if (now - lastCheckTime < this.minInterval) {
            // Return cached status
            return this.state.getServiceStatus(service.id);
        }
        
        this.lastCheck[service.id] = now;
        return await super.checkService(service);
    }
}
```

---

## 🌍 Internationalization Examples

### Example 1: Multi-language Support

```javascript
const translations = {
    en: {
        title: 'Cloud Gaming Dashboard',
        subtitle: 'Real-time status monitoring',
        online: 'Online',
        offline: 'Offline',
        degraded: 'Degraded'
    },
    es: {
        title: 'Panel de Juegos en la Nube',
        subtitle: 'Monitoreo de estado en tiempo real',
        online: 'En línea',
        offline: 'Desconectado',
        degraded: 'Degradado'
    }
};

// Use in renderer
const lang = document.documentElement.lang || 'en';
const t = translations[lang] || translations.en;

// Replace title
document.querySelector('.cgd-title').textContent = t.title;
```

---

## 📱 Progressive Web App Example

```html
<!-- Add to your HTML head -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#667eea">

<!-- Service Worker -->
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(reg => console.log('Service Worker registered'))
        .catch(err => console.log('Service Worker registration failed'));
}
</script>
```

```json
// manifest.json
{
    "name": "Cloud Gaming Dashboard",
    "short_name": "CG Dashboard",
    "description": "Monitor cloud gaming services",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#667eea",
    "theme_color": "#667eea",
    "icons": [
        {
            "src": "/icon-192.png",
            "sizes": "192x192",
            "type": "image/png"
        }
    ]
}
```

---

## 🎯 More Examples

For more examples and live demos, visit:

- **Documentation**: https://cloudloadout.com/docs
- **CodePen Examples**: https://codepen.io/cloudloadout
- **GitHub Repository**: https://github.com/cloudloadout/cloud-gaming-dashboard

---

**Need a custom example?** Contact support@cloudloadout.com or open an issue on GitHub!
