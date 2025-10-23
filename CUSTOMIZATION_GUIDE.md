# CloudLoadout Network Tool - Customization Guide

## 🎨 Quick Customization Reference

This guide provides quick snippets for common customizations you might want to make to the CloudLoadout Network & Latency Tool.

---

## 🎯 Color Customization

### Change Primary Brand Color

Add this CSS to override the primary color:

```css
#cloudloadout-network-tool-root {
    --cl-primary: #your-hex-color !important;
    --cl-primary-dark: #darker-shade !important;
    --cl-primary-light: #lighter-shade !important;
}
```

**Example - Red Theme:**
```css
#cloudloadout-network-tool-root {
    --cl-primary: #dc2626 !important;
    --cl-primary-dark: #b91c1c !important;
    --cl-primary-light: #ef4444 !important;
}
```

### Popular Color Schemes

**Gaming Green:**
```css
#cloudloadout-network-tool-root {
    --cl-primary: #10b981 !important;
    --cl-primary-dark: #059669 !important;
    --cl-primary-light: #34d399 !important;
}
```

**Tech Blue:**
```css
#cloudloadout-network-tool-root {
    --cl-primary: #3b82f6 !important;
    --cl-primary-dark: #2563eb !important;
    --cl-primary-light: #60a5fa !important;
}
```

**Royal Purple:**
```css
#cloudloadout-network-tool-root {
    --cl-primary: #8b5cf6 !important;
    --cl-primary-dark: #7c3aed !important;
    --cl-primary-light: #a78bfa !important;
}
```

---

## 📐 Layout Customization

### Make Tool Full Width

```css
.cl-network-tool-wrapper {
    max-width: 100% !important;
}
```

### Reduce Tool Width

```css
.cl-network-tool-wrapper {
    max-width: 900px !important;
}
```

### Add More Padding

```css
.cl-network-tool-wrapper {
    padding: 48px !important;
}
```

### Remove Border Radius (Flat Design)

```css
.cl-network-tool-wrapper,
.cl-status-card,
.cl-result-card,
.cl-stats-panel,
.cl-recommendations {
    border-radius: 0 !important;
}
```

---

## 🔤 Typography Customization

### Change Font Family

Replace Google Fonts link:
```html
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

Then update CSS:
```css
#cloudloadout-network-tool-root {
    font-family: 'Poppins', sans-serif !important;
}
```

### Adjust Font Sizes

```css
.cl-title {
    font-size: 32px !important;
}

.cl-result-card h4 {
    font-size: 18px !important;
}

.cl-latency-value {
    font-size: 42px !important;
}
```

---

## 🖼️ UI Element Customization

### Hide Theme Toggle

```css
.cl-theme-toggle {
    display: none !important;
}
```

### Hide Export Button

```css
#cl-export-btn {
    display: none !important;
}
```

### Change Button Style

```css
.cl-btn-primary {
    background: linear-gradient(135deg, #your-color-1, #your-color-2) !important;
    border-radius: 24px !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
}
```

### Custom Button Colors

```css
.cl-btn-primary {
    background: #10b981 !important;
    color: white !important;
}

.cl-btn-primary:hover {
    background: #059669 !important;
}
```

---

## 📊 Results Card Customization

### Change Card Layout to List View

```css
.cl-results-grid {
    grid-template-columns: 1fr !important;
}
```

### Two Column Layout on Desktop

```css
.cl-results-grid {
    grid-template-columns: repeat(2, 1fr) !important;
}
```

### Add Hover Effects

```css
.cl-result-card:hover {
    transform: translateY(-8px) scale(1.02) !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
}
```

### Change Card Colors Based on Quality

```css
.cl-result-card[data-quality="excellent"] {
    border-left: 4px solid #10b981 !important;
}

.cl-result-card[data-quality="good"] {
    border-left: 4px solid #3b82f6 !important;
}

.cl-result-card[data-quality="fair"] {
    border-left: 4px solid #f59e0b !important;
}

.cl-result-card[data-quality="poor"] {
    border-left: 4px solid #ef4444 !important;
}
```

---

## 🌐 Adding Custom Servers

### Add Your Own Server Endpoint

Find the `CLOUD_GAMING_SERVERS` array in the JavaScript and add:

```javascript
{
    name: 'Custom Service',
    location: 'Your Region',
    url: 'https://your-test-endpoint.com/ping',
    emoji: '🎮',
    provider: 'Your Company'
}
```

### Example - Add More GeForce NOW Locations

```javascript
{
    name: 'GeForce NOW',
    location: 'Asia Southeast',
    url: 'https://reliable-fast-endpoint.com',
    emoji: '🎮',
    provider: 'NVIDIA'
},
{
    name: 'GeForce NOW',
    location: 'South America',
    url: 'https://another-endpoint.com',
    emoji: '🎮',
    provider: 'NVIDIA'
}
```

---

## 🎭 Dark Theme Customization

### Force Dark Theme by Default

Add this JavaScript at the end:

```javascript
// Force dark theme on load
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('cloudloadout-network-tool-root').classList.add('cl-dark-theme');
    localStorage.setItem('cl-network-tool-theme', 'dark');
});
```

### Customize Dark Theme Colors

```css
#cloudloadout-network-tool-root.cl-dark-theme {
    --cl-bg-primary: #0f172a !important;
    --cl-bg-secondary: #020617 !important;
    --cl-text-primary: #f1f5f9 !important;
}
```

---

## 📱 Mobile Customization

### Adjust Mobile Breakpoint

```css
@media (max-width: 640px) {
    .cl-title {
        font-size: 20px !important;
    }
    
    .cl-network-tool-wrapper {
        padding: 12px !important;
    }
}
```

### Force Single Column on Tablet

```css
@media (max-width: 1024px) {
    .cl-results-grid {
        grid-template-columns: 1fr !important;
    }
}
```

---

## 🎨 Animation Customization

### Disable All Animations

```css
#cloudloadout-network-tool-root * {
    transition: none !important;
    animation: none !important;
}
```

### Faster Animations

```css
#cloudloadout-network-tool-root * {
    transition-duration: 0.15s !important;
}
```

### Add Entrance Animation

```css
@keyframes cl-fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.cl-result-card {
    animation: cl-fade-in 0.5s ease-out forwards !important;
}
```

---

## 🔧 Advanced Customization

### Add Custom Logo

Replace the header icon section in HTML:

```html
<div class="cl-header-icon">
    <img src="your-logo.png" alt="Logo" style="width: 32px; height: 32px;">
</div>
```

### Add Watermark/Branding

Add to the footer section:

```html
<p class="cl-footer-brand">
    <img src="your-brand-logo.png" alt="Brand" style="height: 20px; vertical-align: middle;">
    Your Company Name
</p>
```

### Custom Loading Messages

Find `progressText.textContent` in JavaScript and modify:

```javascript
const loadingMessages = [
    'Contacting servers...',
    'Measuring latency...',
    'Analyzing connection...',
    'Almost there...'
];
const randomMessage = loadingMessages[Math.floor(Math.random() * loadingMessages.length)];
progressText.textContent = randomMessage;
```

### Add Google Analytics Tracking

Add before closing `</script>` tag:

```javascript
// Track test completion
function trackTestCompletion(avgLatency) {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'network_test_complete', {
            'event_category': 'CloudLoadout Tools',
            'event_label': 'Network Latency Test',
            'value': avgLatency
        });
    }
}

// Call this after test completes
trackTestCompletion(avgLatency);
```

---

## 💅 Border and Shadow Customization

### Softer Shadows

```css
.cl-network-tool-wrapper {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
}

.cl-result-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08) !important;
}
```

### Strong Shadows (Material Design)

```css
.cl-network-tool-wrapper {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
}

.cl-result-card {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
}
```

### No Shadows (Flat Design)

```css
#cloudloadout-network-tool-root * {
    box-shadow: none !important;
}
```

---

## 🎯 WordPress-Specific Customizations

### Match Your Theme Colors

In WordPress Customizer → Additional CSS:

```css
#cloudloadout-network-tool-root {
    --cl-primary: var(--wp--preset--color--primary) !important;
}
```

### Integrate with Elementor

When using Elementor, wrap the tool in a section:

```html
<div class="elementor-section">
    <!-- Tool code here -->
</div>
```

### Fix Theme Conflicts

If your theme interferes:

```css
#cloudloadout-network-tool-root {
    all: initial !important;
    display: block !important;
}

#cloudloadout-network-tool-root * {
    all: revert !important;
}
```

---

## 🚀 Performance Optimizations

### Preload Google Fonts

```html
<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" as="style">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

### Reduce Number of Servers Tested

In JavaScript, comment out servers you don't need:

```javascript
const CLOUD_GAMING_SERVERS = [
    // Keep only the servers you want
    // Comment out others with //
];
```

### Lazy Load Images (if added)

```html
<img src="placeholder.jpg" data-src="actual-image.jpg" loading="lazy">
```

---

## 📋 Testing Your Customizations

1. **Make changes** in a separate test file first
2. **Test in multiple browsers** (Chrome, Firefox, Safari, Edge)
3. **Check mobile responsiveness** using browser dev tools
4. **Validate HTML** at validator.w3.org
5. **Check console** for JavaScript errors
6. **Test accessibility** with screen readers

---

## 🔄 Reverting Changes

If something breaks, you can always:

1. Remove your custom CSS
2. Reload the original HTML file
3. Clear browser cache (Ctrl+Shift+Delete)
4. Test in incognito mode

---

## 💡 Pro Tips

- **Always use `!important`** when overriding tool styles
- **Test with real network conditions** - not just localhost
- **Keep backups** of working versions
- **Document your changes** for future reference
- **Use browser DevTools** to test CSS changes live
- **Minimize external requests** for faster loading
- **Consider your audience** - gamers prefer dark themes!

---

## 🆘 Need Help?

If your customization isn't working:

1. Check browser console for errors (F12)
2. Verify CSS specificity (use more specific selectors)
3. Clear all caches (browser, WordPress, CDN)
4. Test in incognito/private mode
5. Disable other plugins temporarily
6. Check for JavaScript conflicts

---

**Happy Customizing! 🎨**

Built with ❤️ for CloudLoadout.com
