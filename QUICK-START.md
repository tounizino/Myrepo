# ⚡ Quick Start Guide

Get the Cloud Gaming Dashboard running in 5 minutes!

## 🎯 WordPress Installation

### Step 1: Upload Plugin
```bash
# Upload to your WordPress plugins directory
/wp-content/plugins/cloud-gaming-dashboard/
```

### Step 2: Activate
1. Go to WordPress Admin → Plugins
2. Find "Cloud Gaming Status & Launcher Dashboard"
3. Click "Activate"

### Step 3: Add to Page
Add one of these shortcodes to any page:

```
[cloud_gaming_combined]     ← Full dashboard (recommended)
[cloud_gaming_dashboard]    ← Status only
[cloud_gaming_launcher]     ← Launcher only
```

### Done! 🎉
Visit your page to see the dashboard in action.

---

## 🌐 HTML/Static Site Installation

### Step 1: Copy Files
```bash
# Copy assets folder to your website
cp -r assets /your-website/
```

### Step 2: Add to HTML
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="assets/css/cloud-gaming-dashboard.css">
</head>
<body>
    <div class="cloud-gaming-container" id="my-dashboard"></div>
    <script src="assets/js/cloud-gaming-dashboard.js"></script>
</body>
</html>
```

### Step 3: Open in Browser
```bash
# If you need a local server:
python -m http.server 8000
# Then visit: http://localhost:8000
```

### Done! 🎉

---

## 🎨 Quick Customization

### Change Colors
```html
<style>
.cloud-gaming-container {
    background: linear-gradient(135deg, #YOUR_COLOR_1, #YOUR_COLOR_2) !important;
}
</style>
```

### Hide Sections
```html
<style>
/* Hide statistics */
.cgd-stats-section { display: none !important; }

/* Hide launcher */
.cgd-launcher-section { display: none !important; }
</style>
```

---

## 📱 Test It

1. **Desktop**: Open in Chrome, Firefox, Safari
2. **Mobile**: Test on phone/tablet
3. **Features**: Try search, favorites, dark mode
4. **Status**: Watch auto-refresh (every 60 seconds)

---

## 🆘 Troubleshooting

### Styles Not Working?
- Clear browser cache
- Check file paths
- Verify CSS file loaded (View Source)

### JavaScript Not Working?
- Check browser console (F12)
- Look for error messages
- Ensure no jQuery conflicts

### WordPress Issues?
- Reactivate plugin
- Check PHP version (7.4+ required)
- Disable other plugins temporarily

---

## 📚 Learn More

- **Full Documentation**: [README.md](README.md)
- **Installation Guide**: [INSTALLATION.md](INSTALLATION.md)
- **Usage Examples**: [EXAMPLES.md](EXAMPLES.md)
- **Contributing**: [CONTRIBUTING.md](CONTRIBUTING.md)

---

## 🎮 Supported Services

✓ GeForce NOW  
✓ Xbox Cloud Gaming  
✓ Boosteroid  
✓ Shadow  
✓ Amazon Luna  
✓ PlayStation Plus  
✓ And 6 more!

---

## ⚡ Features at a Glance

- ✅ Real-time status monitoring
- ✅ Quick launch buttons
- ✅ Favorites system
- ✅ Dark mode
- ✅ Mobile responsive
- ✅ Auto-refresh (60s)
- ✅ Search & filter
- ✅ Statistics dashboard

---

## 💡 Pro Tips

1. **Use Combined View**: `[cloud_gaming_combined]` for full experience
2. **Enable Dark Mode**: Click moon icon for night browsing
3. **Add Favorites**: Star your preferred services
4. **Filter Results**: Use "All", "Online", "Favorites" buttons
5. **Search**: Type to find specific services

---

## 🚀 Next Steps

1. ✅ Install and activate
2. 🎨 Customize colors to match your brand
3. 📊 Monitor your favorite services
4. ⭐ Star the GitHub repository
5. 📣 Share with the cloud gaming community!

---

## 📞 Need Help?

- **Email**: support@cloudloadout.com
- **GitHub**: https://github.com/cloudloadout/cloud-gaming-dashboard
- **Website**: https://cloudloadout.com

---

**Made with ❤️ for CloudLoadout.com**

🎮 Happy Cloud Gaming! ☁️
