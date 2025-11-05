# ⚡ Quick Start Guide

Get the Cloud Gaming Sidebar Widget embedded in under 5 minutes!

## 🎯 The Simplest Way (Recommended)

### ✅ For WordPress Users

**1. Choose your theme file**
Pick one of the 3 files:
- **`widget-dark.html`** - 🌙 Dark theme (perfect for gaming sites)
- **`widget-light.html`** - ☀️ Light theme (clean professional look)
- **`widget-skyblue.html`** - ☁️ Sky blue theme (#E0F3FE - modern cloud gaming)

**2. Copy ALL the code**
- Open your chosen file in a text editor
- Select everything (Ctrl+A or Cmd+A)
- Copy it (Ctrl+C or Cmd+C)

**3. Paste into WordPress**
1. Login to your WordPress admin
2. Go to **Appearance → Widgets**
3. Find **Custom HTML** widget
4. Drag it to your sidebar (or any widget area)
5. **Paste** the code into the Content box
6. Click **Save**
7. Done! ✅

**4. Update the links (Important!)**
- In the widget code, find each link like `href="#latency-tester"`
- Change it to your actual tool page URL
- Example: `href="https://yoursite.com/latency-tester"`

---

## 🎨 Theme Files

### 🌙 Dark Theme (`widget-dark.html`)
- Deep blue/black gradient background
- Light text for maximum contrast
- Purple/blue accent colors
- **Best for:** Gaming sites with dark designs

### ☀️ Light Theme (`widget-light.html`)
- Clean white/light gray background
- Dark text for readability
- Teal/cyan accent colors
- **Best for:** Professional blogs, content-heavy sites

### ☁️ Sky Blue Theme (`widget-skyblue.html`)
- Sky blue (#E0F3FE) background
- White card overlays
- Blue accents (#0b5eaa)
- **Best for:** Modern cloud gaming branding

---

## 🔗 Update Your Tool Links

Find these 5 sections in the code and update the `href` values:

### 1. Latency Tester
```html
<a href="#latency-tester" class="tool-link">
```
Change to: `href="https://yoursite.com/tools/latency-tester"`

### 2. Network Monitor
```html
<a href="#network-monitor" class="tool-link">
```
Change to: `href="https://yoursite.com/tools/network-monitor"`

### 3. Gaming Launchers
```html
<a href="#gaming-launchers" class="tool-link">
```
Change to: `href="https://yoursite.com/tools/launchers"`

### 4. Gamepad Tester
```html
<a href="#gamepad-tester" class="tool-link">
```
Change to: `href="https://yoursite.com/tools/gamepad"`

### 5. NAT Checker
```html
<a href="#nat-checker" class="tool-link">
```
Change to: `href="https://yoursite.com/tools/nat-checker"`

---

## ✅ Pre-Launch Checklist

Before going live:

- [ ] Widget is visible in your sidebar
- [ ] Icons are showing (Font Awesome loaded)
- [ ] All 5 links point to the correct pages
- [ ] Theme matches your site design
- [ ] Tested on mobile device
- [ ] Widget is responsive

---

## 🌐 For Other Platforms

### Wix
1. Add **Embed Code** element
2. Click **Enter Code**
3. Paste widget code
4. Adjust size

### Squarespace
1. Add **Code Block**
2. Paste widget HTML
3. Make sure you're in **Code** mode

### Blogger
1. Go to **Layout**
2. Add **HTML/JavaScript** gadget
3. Paste widget code

### Any HTML Site
1. Open your sidebar HTML file
2. Paste widget code where you want it
3. Save and upload

---

## 🆘 Quick Troubleshooting

### Icons not showing?
The widget uses Font Awesome CDN. Make sure the link at the bottom is present:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

### Widget looks broken?
Make sure you copied **ALL** the code from `widget-standalone.html`, including the `<style>` tag at the top.

### Links not working?
Update the `href="#..."` to your actual URLs.

---

## 🎥 Preview Before Installing

Want to see how it looks first?

1. Open `cloud-gaming-sidebar-widget.html` in your browser
2. Click the theme buttons to see all 3 variations
3. Test hover effects and interactions
4. Check on mobile (resize browser)

---

## 📱 Mobile Testing

Test your widget on mobile:
1. Open your site on a phone
2. Check widget fits properly
3. Test all links work
4. Verify touch interactions

---

## 💡 Pro Tips

1. **Match Your Site Theme** - Choose dark/light/blue based on your site's colors
2. **Test Links** - Click each tool link to ensure they work
3. **Update Stats** - Change the "1.2M+ Users" numbers to match your actual stats
4. **Custom Title** - Update "Cloud Gaming Tools" to match your branding

---

## 🚀 Next Steps

Once installed:

1. ✅ Monitor which tools get the most clicks
2. ✅ Update tool links as needed
3. ✅ Consider adding analytics tracking
4. ✅ Share feedback on which theme works best

---

**That's it! Your Cloud Gaming Sidebar Widget is live! 🎮**

Need more help? Check:
- [STANDALONE-USAGE.md](STANDALONE-USAGE.md) - Detailed usage guide
- [INSTALLATION.md](INSTALLATION.md) - Complete installation docs
- [EXAMPLES.md](EXAMPLES.md) - Code examples and customizations
