# Cloud Gaming Featured Posts Widget

**Professional, responsive featured posts widget** designed for cloud gaming websites with **4 beautiful blue themes**. Ready to paste into any website - no installation, no dependencies!

---

## 🎮 Features

- ✅ **4 Blue Themes** - Sky Blue, Blue, Dark, Light
- ✅ **Copy & Paste Ready** - Just one code snippet
- ✅ **Fully Responsive** - Mobile, tablet, desktop optimized
- ✅ **No Dependencies** - Self-contained HTML/CSS/JS
- ✅ **Clean Design** - No shadows, no glowing effects
- ✅ **Ultra Lightweight** - Only ~7KB minified
- ✅ **Works Everywhere** - Any website, any CMS
- ✅ **Demo Data Included** - Works immediately
- ✅ **API Ready** - Connect to WordPress or any JSON API

---

## 🚀 Quick Start

### Method 1: Interactive Generator (Recommended)

1. Open `embed-code.html` in your browser
2. Customize your title and choose a theme
3. Click "Copy Code"
4. Paste into your website HTML

### Method 2: Ready-Made Codes

1. Open `READY-TO-PASTE-EMBEDS.html` in your browser
2. Choose your favorite theme
3. Click "Copy Code"
4. Paste into your website HTML

### Method 3: Manual Copy

See **EMBED-CODE-GUIDE.md** for complete code snippets of all themes.

---

## 🎨 Theme Showcase

| Theme | Primary Color | Best For |
|-------|--------------|----------|
| **Sky Blue** | #0EA5E9 | Modern, energetic gaming brands |
| **Blue** | #2563EB | Traditional, trustworthy platforms |
| **Dark** | #60A5FA | Premium experiences, night mode |
| **Light** | #1E40AF | Clean, minimalist designs |

---

## 📋 Example Usage

```html
<!-- Just paste this anywhere in your HTML -->
<div id="cgw-featured-posts-sky"></div>
<script>
(function(){/* ... widget code ... */})();
</script>
```

That's it! The widget will appear with:
- 3 demo gaming posts
- Your chosen theme
- Fully responsive layout
- Smooth hover animations

---

## ⚙️ Customization

### Change the Title
Find `title:"Featured Gaming Stories"` and change it to your text.

### Connect to Your Blog
Add an API URL:
```javascript
apiUrl: 'https://yoursite.com/wp-json/wp/v2/posts?per_page=3'
```

### Use Your Own Data
Replace the `demoData` array with your posts:
```javascript
{
    title: 'Your Post Title',
    excerpt: 'Post excerpt...',
    category: 'Category',
    date: 'Jan 15, 2024',
    readTime: '5 min read',
    link: 'https://yoursite.com/post',
    image: 'https://yoursite.com/image.jpg'
}
```

---

## 🌐 Where to Use

- ✅ **Static HTML** websites
- ✅ **WordPress** (Custom HTML block/widget)
- ✅ **Wix, Squarespace, Webflow**
- ✅ **Shopify** pages
- ✅ **Ghost, Medium** (custom embed)
- ✅ **Any CMS** with HTML support

---

## 📱 Responsive Grid

| Screen Size | Columns | Padding |
|------------|---------|---------|
| Mobile (< 640px) | 1 column | 1.5rem |
| Tablet (640-1023px) | 2 columns | 2rem |
| Desktop (≥ 1024px) | 3 columns | 2rem |

---

## 🎯 What's Included

```
cloud-gaming-featured-posts-widget/
├── embed-code.html              # Interactive code generator
├── READY-TO-PASTE-EMBEDS.html   # All 4 themes ready to copy
├── demo.html                     # Visual demo of all themes
├── README.md                     # This file
└── EMBED-CODE-GUIDE.md          # Detailed embed guide
```

---

## 💡 Pro Tips

1. **Multiple Widgets?** Use unique IDs for each:
   ```html
   <div id="cgw-posts-1"></div>
   <div id="cgw-posts-2"></div>
   ```

2. **Custom Colors?** Modify CSS variables in the code:
   ```javascript
   --cgw-sky-primary: #YOUR_COLOR;
   ```

3. **Different Post Count?** Change the slice value:
   ```javascript
   posts.slice(0, 6)  // Show 6 posts instead of 3
   ```

4. **No Images?** Placeholder displays automatically with "Cloud Gaming" text

5. **API Not Working?** Demo data displays as fallback automatically

---

## 🔧 Browser Support

- ✅ Chrome/Edge (last 2 versions)
- ✅ Firefox (last 2 versions)
- ✅ Safari (last 2 versions)
- ✅ iOS Safari & Chrome Mobile
- ✅ Modern browsers with CSS Grid support

---

## 📖 Files Explained

### `embed-code.html`
Interactive generator where you can:
- Choose your theme visually
- Customize the title
- Add API URL
- Preview live
- Copy generated code

### `READY-TO-PASTE-EMBEDS.html`
All 4 themes in one page:
- Minified & optimized code
- One-click copy buttons
- Production-ready snippets
- Usage instructions

### `demo.html`
Visual demonstration:
- See all 4 themes in action
- Full-featured examples
- Responsive behavior preview

### `EMBED-CODE-GUIDE.md`
Complete documentation:
- Full code for each theme
- Customization examples
- Integration guides
- Troubleshooting tips

---

## 🎁 Bonus Features

- **Hover Animations** - Smooth card lift & image zoom
- **Reading Time** - Automatically calculated
- **Category Badges** - Color-coded categories
- **Semantic HTML** - SEO-friendly structure
- **Lazy Loading** - Images load on demand
- **Graceful Fallback** - Works without API
- **CSS Variables** - Easy color customization
- **Minified Code** - Optimized for performance

---

## 🚨 Troubleshooting

**Widget doesn't show?**
- Check browser console for errors
- Verify div ID matches config
- Ensure script loads after div

**Styles look wrong?**
- Check for CSS conflicts
- Try increasing specificity
- Inspect with browser DevTools

**API not loading?**
- Verify CORS settings
- Check API URL format
- Look for console errors

---

## ✨ Design Philosophy

- ❌ **No shadows** - Clean, flat design
- ❌ **No glowing** - Professional appearance
- ✅ **Blue palette** - Gaming-focused colors
- ✅ **Responsive** - Mobile-first approach
- ✅ **Accessible** - Semantic HTML, ARIA labels
- ✅ **Fast** - Optimized, minified code

---

## 📞 Need Help?

1. Check `EMBED-CODE-GUIDE.md` for examples
2. Open `demo.html` to see it working
3. Use `embed-code.html` for custom generation
4. Review `READY-TO-PASTE-EMBEDS.html` for all themes

---

## 📝 Quick Examples

### Sky Blue on Homepage
```html
<div id="cgw-home-featured"></div>
<script>/* ...sky theme code... */</script>
```

### Dark Theme in Sidebar
```html
<div id="cgw-sidebar-posts"></div>
<script>/* ...dark theme code... */</script>
```

### Light Theme on Blog
```html
<div id="cgw-blog-featured"></div>
<script>/* ...light theme code... */</script>
```

---

## 🎮 Perfect For

- Cloud gaming websites
- Game review blogs
- Esports news sites
- Gaming community platforms
- Streaming service pages
- Gaming technology blogs

---

**Ready to elevate your cloud gaming website? Just copy, paste, and enjoy! 🚀**
