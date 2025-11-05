# 🎮 Cloud Gaming Speed Checker Widget

A beautiful, responsive sidebar widget for checking internet speed optimized for cloud gaming visitors. Features three stunning theme variations (Dark, Light, Sky Blue) and provides intelligent recommendations for cloud gaming performance.

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-green)
![Responsive](https://img.shields.io/badge/responsive-yes-brightgreen)
![License](https://img.shields.io/badge/license-GPL--2.0-orange)

## ✨ Features

- **🎨 Three Beautiful Themes**: Dark, Light, and Sky Blue variations
- **📱 Fully Responsive**: Works perfectly on mobile, tablet, and desktop
- **⚡ Real-time Speed Testing**: Tests download, upload, ping, and jitter
- **🎮 Cloud Gaming Focused**: Tailored recommendations for cloud gaming services
- **🔌 WordPress Ready**: Easy-to-use WordPress widget and shortcode
- **♿ Accessible**: Supports reduced motion preferences
- **🚀 Lightweight**: Minimal dependencies, fast loading

## 🎯 Cloud Gaming Recommendations

The widget provides intelligent recommendations based on connection quality:

- **Excellent** (50+ Mbps, <30ms ping): Perfect for 4K cloud gaming
- **Very Good** (30+ Mbps, <50ms ping): Great for 1080p cloud gaming
- **Good** (20+ Mbps, <80ms ping): Suitable for 720p-1080p gaming
- **Fair** (10+ Mbps, <120ms ping): Limited cloud gaming capability
- **Poor** (<10 Mbps or >120ms ping): Not recommended for cloud gaming

## 📦 Installation

### Standalone HTML

1. Download the repository
2. Include the CSS and JS files in your HTML:

```html
<link rel="stylesheet" href="speed-checker-widget.css">
<script src="speed-checker-widget.js"></script>
```

3. Add the widget HTML with your preferred theme:

```html
<div class="speed-checker-widget theme-dark">
    <!-- Widget content (see index.html for full structure) -->
</div>
```

### WordPress Plugin

1. Download the `wp-cloud-gaming-speed-checker-widget` folder
2. Upload it to `/wp-content/plugins/` directory
3. Activate the plugin through the WordPress admin panel
4. Add the widget to your sidebar via Appearance > Widgets

#### WordPress Shortcode

Use the shortcode anywhere in your content:

```
[cloud_gaming_speed_checker theme="dark" title="Speed Test"]
```

**Shortcode Parameters:**
- `theme`: Choose from `dark`, `light`, or `skyblue` (default: `dark`)
- `title`: Custom title for the widget (default: `Speed Test`)

## 🎨 Theme Variations

### Dark Theme (`theme-dark`)
Perfect for modern, gaming-focused websites with dark mode aesthetics.
```html
<div class="speed-checker-widget theme-dark">
```

### Light Theme (`theme-light`)
Clean and professional appearance for light-themed websites.
```html
<div class="speed-checker-widget theme-light">
```

### Sky Blue Theme (`theme-skyblue`)
Vibrant gradient theme with oceanic blue tones.
```html
<div class="speed-checker-widget theme-skyblue">
```

## 📱 Responsive Design

The widget automatically adapts to different screen sizes:

- **Desktop** (>600px): Full-sized circular progress indicator, 2-column stats grid
- **Mobile** (<600px): Compact layout, single-column stats grid

## 🎯 How It Works

1. **Download Test**: Fetches a test image to measure download speed
2. **Upload Test**: Simulates upload activity (uses estimation)
3. **Ping Test**: Measures latency to Cloudflare servers
4. **Jitter Test**: Calculates connection stability
5. **Recommendations**: Analyzes results and provides cloud gaming advice

## 🔧 Customization

### Changing Colors

Edit the CSS file to customize theme colors:

```css
.theme-dark {
    background: radial-gradient(circle at top left, rgba(148, 163, 184, 0.18), transparent 50%), #0f172a;
    color: #f8fafc;
}
```

### Adding New Themes

1. Create a new theme class in CSS:

```css
.theme-custom {
    background: your-gradient;
    color: your-text-color;
}
```

2. Update the widget HTML to use the new theme:

```html
<div class="speed-checker-widget theme-custom">
```

## 🌐 Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 📊 Technical Details

- **Pure JavaScript**: No jQuery or framework dependencies
- **SVG Progress Ring**: Smooth, scalable circular progress indicator
- **CSS Grid & Flexbox**: Modern, responsive layout
- **WordPress Widget API**: Standard WordPress widget implementation

## 🎮 Supported Cloud Gaming Services

This widget is optimized for testing connections for:

- NVIDIA GeForce NOW
- Xbox Cloud Gaming (xCloud)
- PlayStation Plus Premium
- Amazon Luna
- Google Stadia (discontinued but specs still relevant)
- Shadow PC

## 📄 File Structure

```
cloud-gaming-speed-checker/
├── index.html                          # Demo page with all three themes
├── speed-checker-widget.css            # Widget styles
├── speed-checker-widget.js             # Widget functionality
├── wp-cloud-gaming-speed-checker-widget/
│   ├── cloud-gaming-speed-checker-widget.php  # WordPress plugin
│   └── assets/
│       ├── css/
│       │   └── speed-checker-widget.css
│       └── js/
│           └── speed-checker-widget.js
└── README.md
```

## 🚀 Demo

Open `index.html` in your browser to see all three theme variations in action.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is licensed under the GPL v2 or later - see the LICENSE file for details.

## 💬 Support

For issues, questions, or suggestions, please open an issue on GitHub.

## 🎉 Credits

Created with ❤️ for cloud gaming enthusiasts

---

**Note**: The speed test results are approximate and may vary based on network conditions, server location, and browser capabilities. For most accurate results, use a dedicated speed test service.
