# Using the Standalone Widget

The `widget-standalone.html` file contains everything you need in a single HTML file. This is perfect for quick integration without managing multiple files.

## ✨ What's Inside

The standalone file includes:
- ✅ All CSS styles (no external stylesheet needed)
- ✅ Complete HTML structure
- ✅ All 3 theme variations
- ✅ Responsive design built-in
- ✅ Font loading (Google Fonts)

## 🚀 Quick Integration

### Option 1: Copy & Paste (Simplest)

1. Open `widget-standalone.html`
2. Copy ALL the contents
3. Paste into your HTML page or CMS sidebar
4. Done! The widget will appear with the dark theme

### Option 2: Change Theme

To switch themes, find this line in the code:
```html
<div class="cloud-gaming-widget theme-dark">
```

Change it to:
- `theme-dark` for Dark Theme 🌙
- `theme-light` for Light Theme ☀️
- `theme-blue` for Sky Blue Theme ☁️

### Option 3: Update Links

Find each tool link and update the `href`:
```html
<a href="#latency-tester" class="tool-link">
```

Change `#latency-tester` to your actual page URL:
```html
<a href="/tools/latency-tester" class="tool-link">
```

Or external links:
```html
<a href="https://yoursite.com/tools/latency-tester" class="tool-link">
```

## 📦 WordPress Integration

### Method 1: Custom HTML Widget
1. Go to **Appearance → Widgets**
2. Add a **Custom HTML** widget to your sidebar
3. Copy the entire contents of `widget-standalone.html`
4. Paste into the widget content area
5. Save!

### Method 2: Page Builder
If using Elementor, Divi, or similar:
1. Add an **HTML** or **Code** block
2. Paste the entire widget code
3. Adjust width/padding as needed
4. Publish!

## 🎨 Customization Examples

### Change User Stats
Find this section:
```html
<div class="stat-item">
    <i class="fas fa-users"></i>
    <span>1.2M+ Users</span>
</div>
```

Update to your numbers:
```html
<div class="stat-item">
    <i class="fas fa-users"></i>
    <span>500K+ Users</span>
</div>
```

### Change Widget Title
Find:
```html
<h3 class="widget-title">Cloud Gaming Tools</h3>
<p class="widget-subtitle">Essential tools for gamers</p>
```

Update to:
```html
<h3 class="widget-title">Gaming Resources</h3>
<p class="widget-subtitle">Your ultimate toolkit</p>
```

### Change Tool Names
Find any tool title:
```html
<h4 class="tool-title">Cloud Platforms Latency Tester</h4>
<p class="tool-description">Test your connection speed</p>
```

Update to:
```html
<h4 class="tool-title">Speed Test Tool</h4>
<p class="tool-description">Check your ping to servers</p>
```

## 💻 CMS-Specific Instructions

### WordPress
- Use **Custom HTML Widget** (Appearance → Widgets)
- Or use **HTML Block** in Gutenberg editor
- Or paste in theme's **sidebar.php** file

### Wix
1. Add an **Embed Code** element
2. Click **Enter Code**
3. Paste the widget code
4. Adjust size and position

### Squarespace
1. Add a **Code Block**
2. Paste the widget HTML
3. Make sure you're in **Code** mode, not **Markdown**

### Shopify
1. Edit your theme
2. Add a **Custom Liquid** section
3. Paste the widget code

### Blogger
1. Go to **Layout**
2. Add a **HTML/JavaScript** gadget
3. Paste the widget code

### Ghost
1. Create a new **HTML Card**
2. Paste the widget code
3. Publish!

## 📱 Mobile Considerations

The widget is fully responsive and will automatically:
- Shrink to fit smaller screens
- Stack elements vertically on mobile
- Increase touch target sizes
- Adjust font sizes for readability

Test on mobile using:
- Browser DevTools (F12 → Toggle Device Toolbar)
- Real mobile devices
- Responsive design testing tools

## ⚠️ Important Notes

### Font Awesome Required
The widget uses Font Awesome icons. Make sure the CDN link at the bottom of the file is present:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

If your site already loads Font Awesome, you can remove this line to avoid loading it twice.

### CSS Conflicts
If you experience styling issues:
1. Check if your site has CSS that conflicts
2. Add `!important` to critical styles if needed
3. Increase CSS specificity by adding a wrapper class

Example with wrapper:
```html
<div class="my-site-widget-wrapper">
    <style>
        .my-site-widget-wrapper .cloud-gaming-widget {
            /* Your overrides here */
        }
    </style>
    
    <!-- Widget code here -->
</div>
```

## 🔧 Troubleshooting

### Widget looks broken
- Ensure the entire HTML block was copied
- Check browser console for errors (F12)
- Verify Font Awesome is loading

### Icons not showing
- Font Awesome CDN might be blocked
- Check network tab in DevTools
- Consider self-hosting Font Awesome

### Theme not working
- Verify the class name is correct (theme-dark, theme-light, or theme-blue)
- Check for CSS conflicts from your main theme
- Try the widget on a blank HTML page to test

### Links not working
- Make sure `href` attributes are updated
- Check for JavaScript that might be intercepting clicks
- Test in a different browser

## 💡 Pro Tips

1. **Test First**: Copy the widget to a test page before going live
2. **Backup**: Save a copy of the original code before customizing
3. **Validate**: Use HTML validator to check for errors
4. **Performance**: Consider lazy-loading Font Awesome if it's at the bottom of your page
5. **Analytics**: Add tracking to the tool links to measure engagement

## 📊 Example Tracking Code

Add Google Analytics or other tracking:
```html
<a href="/tools/latency-tester" 
   class="tool-link"
   onclick="gtag('event', 'click', {'event_category': 'widget', 'event_label': 'latency_tester'});">
```

---

**Need more help?** Check out the [INSTALLATION.md](INSTALLATION.md) for detailed instructions or [PREVIEW.md](PREVIEW.md) for visual examples!
