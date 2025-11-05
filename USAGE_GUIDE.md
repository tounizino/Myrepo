# 🎮 Cloud Gaming Speed Checker Widget - Usage Guide

## Quick Start

This guide will help you integrate the Cloud Gaming Speed Checker Widget into your website.

## 📋 Table of Contents

1. [Standalone HTML Integration](#standalone-html-integration)
2. [WordPress Integration](#wordpress-integration)
3. [Theme Selection](#theme-selection)
4. [Customization Options](#customization-options)
5. [Responsive Behavior](#responsive-behavior)
6. [Browser Compatibility](#browser-compatibility)

---

## 1. Standalone HTML Integration

### Basic Setup

Add these files to your project:
- `speed-checker-widget.css`
- `speed-checker-widget.js`

### Include in HTML

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Gaming Site</title>
    <link rel="stylesheet" href="speed-checker-widget.css">
</head>
<body>
    <!-- Your content -->
    
    <aside class="sidebar">
        <div class="speed-checker-widget theme-dark">
            <div class="widget-header">
                <h3>🎮 Speed Test</h3>
                <span class="theme-badge">Dark</span>
            </div>
            <div class="widget-body">
                <div class="speed-display">
                    <div class="speed-circle">
                        <svg class="progress-ring" width="180" height="180">
                            <circle class="progress-ring-bg" cx="90" cy="90" r="80"></circle>
                            <circle class="progress-ring-circle" cx="90" cy="90" r="80"></circle>
                        </svg>
                        <div class="speed-value">
                            <span class="speed-number">--</span>
                            <span class="speed-unit">Mbps</span>
                        </div>
                    </div>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-icon">⬇️</span>
                        <span class="stat-label">Download</span>
                        <span class="stat-value">-- Mbps</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon">⬆️</span>
                        <span class="stat-label">Upload</span>
                        <span class="stat-value">-- Mbps</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon">⚡</span>
                        <span class="stat-label">Ping</span>
                        <span class="stat-value">-- ms</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-icon">📊</span>
                        <span class="stat-label">Jitter</span>
                        <span class="stat-value">-- ms</span>
                    </div>
                </div>

                <button class="test-button">
                    <span class="button-icon">▶️</span>
                    Start Speed Test
                </button>

                <div class="recommendation hidden">
                    <div class="recommendation-header">
                        <span class="recommendation-icon">✓</span>
                        <h4>Connection Status</h4>
                    </div>
                    <p class="recommendation-text"></p>
                    <div class="gaming-quality">
                        <div class="quality-bar">
                            <div class="quality-fill"></div>
                        </div>
                        <span class="quality-label"></span>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <script src="speed-checker-widget.js"></script>
</body>
</html>
```

---

## 2. WordPress Integration

### Installation Steps

1. **Upload Plugin**
   - Navigate to `wp-content/plugins/`
   - Upload the `wp-cloud-gaming-speed-checker-widget` folder
   - Or install via WordPress admin: Plugins > Add New > Upload Plugin

2. **Activate Plugin**
   - Go to Plugins menu
   - Find "Cloud Gaming Speed Checker Widget"
   - Click "Activate"

### Widget Method

1. Go to **Appearance > Widgets**
2. Find "Cloud Gaming Speed Checker" widget
3. Drag to your desired sidebar
4. Configure:
   - **Title**: Enter custom title (e.g., "Test Your Speed")
   - **Theme**: Select Dark, Light, or Sky Blue
5. Click "Save"

### Shortcode Method

Add anywhere in posts, pages, or custom post types:

```
[cloud_gaming_speed_checker]
```

With custom options:

```
[cloud_gaming_speed_checker theme="skyblue" title="Check Gaming Speed"]
```

**Shortcode Parameters:**

| Parameter | Values | Default | Description |
|-----------|--------|---------|-------------|
| `theme` | `dark`, `light`, `skyblue` | `dark` | Theme color scheme |
| `title` | Any text | `Speed Test` | Widget title |

---

## 3. Theme Selection

### Dark Theme
**Best for:** Gaming sites, dark mode designs, modern tech sites

```html
<div class="speed-checker-widget theme-dark">
```

**Features:**
- Deep navy background with subtle gradient
- Cyan progress indicator
- High contrast for easy reading
- Reduces eye strain

### Light Theme
**Best for:** Professional blogs, business sites, light mode designs

```html
<div class="speed-checker-widget theme-light">
```

**Features:**
- Clean white/gray background
- Purple progress indicator
- Professional appearance
- High legibility

### Sky Blue Theme
**Best for:** Tech blogs, cloud service sites, modern websites

```html
<div class="speed-checker-widget theme-skyblue">
```

**Features:**
- Vibrant blue gradient
- White progress indicator
- Eye-catching design
- Ocean-inspired colors

---

## 4. Customization Options

### Change Widget Width

For sidebars:
```css
.sidebar .speed-checker-widget {
    max-width: 350px;
}
```

For full-width:
```css
.speed-checker-widget {
    max-width: 500px;
    margin: 0 auto;
}
```

### Custom Colors

Create your own theme:

```css
.speed-checker-widget.theme-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #ffffff;
}

.theme-custom .progress-ring-circle {
    stroke: #fbbf24;
}

.theme-custom .test-button {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #1f2937;
}
```

### Adjust Size on Mobile

```css
@media (max-width: 480px) {
    .speed-checker-widget {
        padding: 16px;
    }
    
    .speed-circle {
        width: 140px;
        height: 140px;
    }
}
```

---

## 5. Responsive Behavior

### Desktop (>600px)
- Full-sized circular progress (180px)
- 2-column stats grid
- Hover effects enabled

### Mobile (<600px)
- Compact circular progress (160px)
- Single-column stats grid
- Touch-optimized buttons

### Tablet (601px - 1024px)
- Full-sized layout
- Optimized spacing
- Touch-friendly interface

---

## 6. Browser Compatibility

### Fully Supported
✅ Chrome 90+
✅ Firefox 88+
✅ Safari 14+
✅ Edge 90+
✅ Opera 76+

### Mobile Browsers
✅ Chrome Mobile
✅ Safari iOS 14+
✅ Samsung Internet
✅ Firefox Mobile

### Notes
- Requires JavaScript enabled
- Uses modern CSS (Grid, Flexbox)
- SVG support required
- Fetch API for speed tests

---

## 🎯 Best Practices

### Placement
- **Sidebar**: Perfect for blogs and article pages
- **Below Header**: Great for landing pages
- **Footer**: Useful for all pages
- **Dedicated Page**: Create a speed test page

### Performance
- Widget is lightweight (~15KB total)
- Loads asynchronously
- No external dependencies
- Optimized images/assets

### User Experience
- Place where users can easily find it
- Add context about why speed matters
- Consider adding before gaming guides
- Use appropriate theme for your site design

---

## 🔧 Troubleshooting

### Widget Not Showing
1. Check that CSS and JS files are loaded
2. Verify file paths are correct
3. Check browser console for errors

### Speed Test Not Working
1. Ensure JavaScript is enabled
2. Check internet connection
3. Try different browser
4. Check browser console for fetch errors

### Styling Issues
1. Check for CSS conflicts
2. Ensure theme class is correct
3. Verify CSS file is loaded
4. Use browser dev tools to inspect

---

## 📞 Support

For issues or questions:
1. Check the main README.md
2. Review browser console for errors
3. Open an issue on GitHub
4. Contact support

---

## 🎉 Examples

### Example 1: Gaming Blog Sidebar
```html
<!-- Dark theme for gaming aesthetic -->
<aside class="gaming-sidebar">
    <div class="speed-checker-widget theme-dark">
        <!-- Widget content -->
    </div>
</aside>
```

### Example 2: Cloud Gaming Landing Page
```html
<!-- Sky Blue theme for cloud services -->
<section class="hero-section">
    <h1>Ready for Cloud Gaming?</h1>
    <div class="speed-checker-widget theme-skyblue">
        <!-- Widget content -->
    </div>
</section>
```

### Example 3: WordPress Post
```
Welcome to our cloud gaming guide! Before you start, test your connection:

[cloud_gaming_speed_checker theme="light" title="Test Your Connection"]

Now that you know your speed, let's dive into the best cloud gaming services...
```

---

**Happy Testing! 🎮**
