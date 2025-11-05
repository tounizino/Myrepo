# ⚡ Quick Start Guide

Get the Cloud Gaming Sidebar Widget up and running in under 5 minutes!

## 🎯 Choose Your Integration Method

### 🥇 Method 1: Standalone File (Easiest)
**Best for:** Quick integration, no file management needed

1. Open `widget-standalone.html`
2. Copy everything
3. Paste into your sidebar HTML
4. Done! ✅

[View detailed instructions →](STANDALONE-USAGE.md)

---

### 🥈 Method 2: Separate Files (Best Practice)
**Best for:** Better organization, easier maintenance

1. Add to your HTML `<head>`:
```html
<link rel="stylesheet" href="cloud-gaming-widget-styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

2. Copy the widget HTML from `cloud-gaming-sidebar-widget.html` (lines 28-128)

3. Add before `</body>`:
```html
<script src="cloud-gaming-widget-script.js"></script>
```

[View detailed instructions →](INSTALLATION.md)

---

### 🥉 Method 3: WordPress Widget (WordPress Users)
**Best for:** WordPress sites, customizable admin interface

1. Copy these files to your theme folder:
   - `wordpress-widget-integration.php`
   - `cloud-gaming-widget-styles.css`
   - `cloud-gaming-widget-script.js`

2. Add to `functions.php`:
```php
require_once get_template_directory() . '/wordpress-widget-integration.php';
```

3. Go to **Appearance → Widgets**
4. Drag **Cloud Gaming Tools** to your sidebar
5. Configure and save! ✅

[View detailed instructions →](INSTALLATION.md#wordpress-installation)

---

## 🎨 Changing Themes

Find this in your widget code:
```html
<div class="cloud-gaming-widget theme-dark">
```

Change to:
- `theme-dark` 🌙 Dark Theme
- `theme-light` ☀️ Light Theme
- `theme-blue` ☁️ Sky Blue Theme

---

## 🔗 Updating Tool Links

Find each link and update the `href`:

**Before:**
```html
<a href="#latency-tester" class="tool-link">
```

**After:**
```html
<a href="/your-tool-page" class="tool-link">
```

---

## 📱 Testing Your Widget

1. Open the demo: `cloud-gaming-sidebar-widget.html`
2. Try all three themes
3. Test on mobile (resize browser or use DevTools)
4. Check that all links work

---

## ✅ Checklist

Before going live, make sure:

- [ ] Widget displays correctly
- [ ] Theme matches your site design
- [ ] All 5 tool links point to the right pages
- [ ] Icons are showing (Font Awesome loaded)
- [ ] Widget is responsive on mobile
- [ ] Stats in footer are updated (if needed)
- [ ] Widget title/subtitle are customized (if needed)

---

## 🆘 Quick Troubleshooting

### Icons not showing?
Make sure Font Awesome is loaded:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

### Widget looks broken?
- Check if all files are uploaded
- View browser console (F12) for errors
- Make sure CSS file is loading

### Theme not working?
- Verify class name: `theme-dark`, `theme-light`, or `theme-blue`
- Check for CSS conflicts from your main theme

---

## 📚 Full Documentation

- **[README.md](README.md)** - Project overview
- **[INSTALLATION.md](INSTALLATION.md)** - Complete installation guide
- **[STANDALONE-USAGE.md](STANDALONE-USAGE.md)** - Standalone widget guide
- **[PREVIEW.md](PREVIEW.md)** - Visual preview and testing

---

## 🎮 What's Next?

1. ✅ Install the widget
2. ✅ Customize the links
3. ✅ Choose your theme
4. ✅ Test on mobile
5. ✅ Go live!

**Enjoy your new Cloud Gaming Sidebar Widget!** 🚀
