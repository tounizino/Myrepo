# 📦 Installation Guide

Quick installation guide for the Cloud Gaming Speed Checker Widget.

## 🚀 For Static Websites / HTML Projects

### Step 1: Download Files
Download these three files:
- `index.html` (demo page)
- `speed-checker-widget.css`
- `speed-checker-widget.js`

### Step 2: Add to Your Project
Place the CSS and JS files in your project directory:
```
your-project/
├── css/
│   └── speed-checker-widget.css
├── js/
│   └── speed-checker-widget.js
└── index.html
```

### Step 3: Include in HTML
Add these lines to your HTML `<head>`:
```html
<link rel="stylesheet" href="css/speed-checker-widget.css">
```

Add before closing `</body>`:
```html
<script src="js/speed-checker-widget.js"></script>
```

### Step 4: Add Widget HTML
Copy the widget HTML from `index.html` and paste it where you want the widget to appear. Choose your theme: `theme-dark`, `theme-light`, or `theme-skyblue`.

---

## 🎨 For WordPress Sites

### Method 1: Manual Installation (Recommended)

1. **Download Plugin**
   - Download the entire `wp-cloud-gaming-speed-checker-widget` folder

2. **Upload to WordPress**
   - Connect to your server via FTP or cPanel File Manager
   - Navigate to `/wp-content/plugins/`
   - Upload the `wp-cloud-gaming-speed-checker-widget` folder

3. **Activate Plugin**
   - Log in to WordPress Admin
   - Go to **Plugins** → **Installed Plugins**
   - Find "Cloud Gaming Speed Checker Widget"
   - Click **Activate**

4. **Add Widget to Sidebar**
   - Go to **Appearance** → **Widgets**
   - Find "Cloud Gaming Speed Checker" in available widgets
   - Drag it to your desired sidebar area
   - Configure title and theme
   - Click **Save**

### Method 2: Via WordPress Admin (ZIP Upload)

1. **Create ZIP File**
   - Compress the `wp-cloud-gaming-speed-checker-widget` folder into a ZIP file

2. **Upload via Admin**
   - Log in to WordPress Admin
   - Go to **Plugins** → **Add New**
   - Click **Upload Plugin**
   - Choose your ZIP file
   - Click **Install Now**
   - Click **Activate Plugin**

3. **Configure Widget**
   - Go to **Appearance** → **Widgets**
   - Add "Cloud Gaming Speed Checker" to your sidebar
   - Configure and save

### Method 3: Using Shortcode

After activating the plugin, you can use the shortcode in any post, page, or custom post type:

```
[cloud_gaming_speed_checker]
```

With custom options:
```
[cloud_gaming_speed_checker theme="skyblue" title="Test Your Connection"]
```

**Shortcode Options:**
- `theme` - Options: `dark`, `light`, `skyblue` (default: `dark`)
- `title` - Any text (default: `Speed Test`)

---

## 🔍 Verification

### Check if Installation is Successful

1. **Static Sites:**
   - Open your page in a browser
   - Look for the speed checker widget
   - Click "Start Speed Test"
   - If the test runs, installation is successful!

2. **WordPress:**
   - Visit your site's frontend
   - Check the sidebar where you placed the widget
   - Click "Start Speed Test"
   - If the test runs, you're all set!

### Troubleshooting

**Widget Not Visible:**
- Check file paths in HTML
- Ensure CSS file is loading (check browser console)
- Clear browser cache
- For WordPress: Clear WordPress cache

**Speed Test Not Working:**
- Check browser console for JavaScript errors
- Ensure JS file is loaded correctly
- Test in a different browser
- Check internet connection

**Styling Issues:**
- Verify CSS file path
- Check for CSS conflicts with theme
- Use browser dev tools to inspect
- Try different theme variation

---

## 🎯 Post-Installation

### For Static Sites
1. Test all three themes to see which fits your design
2. Customize colors in CSS if needed
3. Adjust widget width for your sidebar
4. Test on mobile devices

### For WordPress
1. Check widget appearance on frontend
2. Test on different pages/posts
3. Verify mobile responsiveness
4. Consider adding explanatory text above widget

---

## 📱 Testing Checklist

- [ ] Widget displays correctly on desktop
- [ ] Widget displays correctly on mobile
- [ ] Widget displays correctly on tablet
- [ ] "Start Speed Test" button works
- [ ] Progress circle animates
- [ ] Download speed is measured
- [ ] Upload speed is shown
- [ ] Ping is measured
- [ ] Jitter is calculated
- [ ] Recommendation appears after test
- [ ] Theme colors are correct
- [ ] Widget is responsive

---

## 🆘 Need Help?

1. Check the main README.md for detailed documentation
2. Review USAGE_GUIDE.md for advanced options
3. Open an issue on GitHub
4. Check browser console for error messages

---

## ✅ Next Steps

After successful installation:

1. **Customize**: Adjust colors and styling to match your brand
2. **Optimize**: Place the widget where users will find it useful
3. **Inform**: Add text explaining why speed matters for cloud gaming
4. **Test**: Regularly test the widget to ensure it's working
5. **Update**: Keep the widget files updated

---

**Congratulations! Your Cloud Gaming Speed Checker Widget is now installed! 🎮**
