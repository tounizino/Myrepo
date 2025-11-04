# 🎮 Cloud Gaming Featured Posts Widget - Embed Code

## Quick Start - Copy & Paste Ready!

This widget is **100% self-contained** with no external dependencies. Just copy and paste into your HTML!

---

## 🚀 Method 1: Use the Generator (Recommended)

1. **Open the generator**: `embed-code.html`
2. **Customize your widget**:
   - Choose your theme (Sky Blue, Blue, Dark, or Light)
   - Set your title
   - Optionally add an API URL for dynamic posts
3. **Click "Copy Code"**
4. **Paste** into your website

---

## 📋 Method 2: Manual Embed (All Themes)

### Sky Blue Theme

```html
<!-- Cloud Gaming Featured Posts Widget - Sky Blue -->
<div id="cgw-featured-posts-sky" class="cgw-widget-container"></div>

<script>
(function() {
    const widgetConfig = {
        theme: 'sky',
        title: 'Featured Gaming Stories',
        apiUrl: '',  // Leave empty for demo data, or add your API endpoint
        containerId: 'cgw-featured-posts-sky'
    };

    // Widget CSS
    const css = `
:root {
  --cgw-sky-primary: #0EA5E9; --cgw-sky-bg: #F0F9FF; --cgw-sky-card: #FFFFFF;
  --cgw-sky-text: #0F172A; --cgw-sky-text-muted: #475569; --cgw-sky-border: #BAE6FD;
  --cgw-blue-primary: #2563EB; --cgw-blue-bg: #EFF6FF; --cgw-blue-card: #FFFFFF;
  --cgw-blue-text: #1E293B; --cgw-blue-text-muted: #64748B; --cgw-blue-border: #BFDBFE;
  --cgw-dark-primary: #60A5FA; --cgw-dark-bg: #0F172A; --cgw-dark-card: #1E293B;
  --cgw-dark-text: #F1F5F9; --cgw-dark-text-muted: #94A3B8; --cgw-dark-border: #334155;
  --cgw-light-primary: #1E40AF; --cgw-light-bg: #FFFFFF; --cgw-light-card: #F8FAFC;
  --cgw-light-text: #0F172A; --cgw-light-text-muted: #64748B; --cgw-light-border: #E2E8F0;
}
.cgw-widget-container { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
.cgw-featured-posts { padding: 2rem; border-radius: 1.5rem; border: 1px solid transparent; }
.cgw-theme-sky { background: var(--cgw-sky-bg); color: var(--cgw-sky-text); border-color: var(--cgw-sky-border); }
.cgw-theme-blue { background: var(--cgw-blue-bg); color: var(--cgw-blue-text); border-color: var(--cgw-blue-border); }
.cgw-theme-dark { background: var(--cgw-dark-bg); color: var(--cgw-dark-text); border-color: var(--cgw-dark-border); }
.cgw-theme-light { background: var(--cgw-light-bg); color: var(--cgw-light-text); border-color: var(--cgw-light-border); }
.cgw-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem; }
.cgw-title { margin: 0; font-size: 1.75rem; font-weight: 700; letter-spacing: -0.025em; }
.cgw-posts-grid { display: grid; gap: 1.5rem; grid-template-columns: 1fr; }
@media (min-width: 640px) { .cgw-posts-grid { grid-template-columns: repeat(2, 1fr); gap: 1.75rem; } }
@media (min-width: 1024px) { .cgw-posts-grid { grid-template-columns: repeat(3, 1fr); gap: 2rem; } }
.cgw-post-card { display: flex; flex-direction: column; border: 1px solid; border-radius: 0.75rem; overflow: hidden; transition: transform 0.2s ease; }
.cgw-theme-sky .cgw-post-card { background: var(--cgw-sky-card); border-color: var(--cgw-sky-border); }
.cgw-theme-blue .cgw-post-card { background: var(--cgw-blue-card); border-color: var(--cgw-blue-border); }
.cgw-theme-dark .cgw-post-card { background: var(--cgw-dark-card); border-color: var(--cgw-dark-border); }
.cgw-theme-light .cgw-post-card { background: var(--cgw-light-card); border-color: var(--cgw-light-border); }
.cgw-post-card:hover { transform: translateY(-4px); }
.cgw-thumbnail { display: block; width: 100%; aspect-ratio: 16 / 9; overflow: hidden; }
.cgw-thumbnail img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.cgw-post-card:hover .cgw-thumbnail img { transform: scale(1.05); }
.cgw-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; }
.cgw-theme-sky .cgw-placeholder { background: #E0F2FE; color: var(--cgw-sky-primary); }
.cgw-theme-blue .cgw-placeholder { background: #DBEAFE; color: var(--cgw-blue-primary); }
.cgw-theme-dark .cgw-placeholder { background: #293548; color: var(--cgw-dark-primary); }
.cgw-theme-light .cgw-placeholder { background: #F1F5F9; color: var(--cgw-light-primary); }
.cgw-content { padding: 1.25rem; display: flex; flex-direction: column; flex-grow: 1; }
.cgw-category { display: inline-block; padding: 0.25rem 0.75rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem; border-radius: 0.25rem; }
.cgw-theme-sky .cgw-category { background: var(--cgw-sky-primary); color: white; }
.cgw-theme-blue .cgw-category { background: var(--cgw-blue-primary); color: white; }
.cgw-theme-dark .cgw-category { background: var(--cgw-dark-primary); color: #0F172A; }
.cgw-theme-light .cgw-category { background: var(--cgw-light-primary); color: white; }
.cgw-post-title { margin: 0 0 0.75rem; font-size: 1.125rem; font-weight: 700; line-height: 1.4; }
.cgw-post-title a { text-decoration: none; transition: opacity 0.2s; }
.cgw-post-title a:hover { opacity: 0.7; }
.cgw-theme-sky .cgw-post-title a { color: var(--cgw-sky-text); }
.cgw-theme-blue .cgw-post-title a { color: var(--cgw-blue-text); }
.cgw-theme-dark .cgw-post-title a { color: var(--cgw-dark-text); }
.cgw-theme-light .cgw-post-title a { color: var(--cgw-light-text); }
.cgw-excerpt { margin: 0 0 1rem; font-size: 0.875rem; line-height: 1.6; flex-grow: 1; }
.cgw-theme-sky .cgw-excerpt { color: var(--cgw-sky-text-muted); }
.cgw-theme-blue .cgw-excerpt { color: var(--cgw-blue-text-muted); }
.cgw-theme-dark .cgw-excerpt { color: var(--cgw-dark-text-muted); }
.cgw-theme-light .cgw-excerpt { color: var(--cgw-light-text-muted); }
.cgw-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 1rem; border-top: 1px solid; gap: 0.75rem; flex-wrap: wrap; }
.cgw-theme-sky .cgw-footer { border-color: var(--cgw-sky-border); }
.cgw-theme-blue .cgw-footer { border-color: var(--cgw-blue-border); }
.cgw-theme-dark .cgw-footer { border-color: var(--cgw-dark-border); }
.cgw-theme-light .cgw-footer { border-color: var(--cgw-light-border); }
.cgw-meta { font-size: 0.8125rem; display: flex; align-items: center; gap: 0.5rem; }
.cgw-theme-sky .cgw-meta { color: var(--cgw-sky-text-muted); }
.cgw-theme-blue .cgw-meta { color: var(--cgw-blue-text-muted); }
.cgw-theme-dark .cgw-meta { color: var(--cgw-dark-text-muted); }
.cgw-theme-light .cgw-meta { color: var(--cgw-light-text-muted); }
.cgw-cta { text-decoration: none; font-size: 0.875rem; font-weight: 600; transition: opacity 0.2s; }
.cgw-cta:hover { opacity: 0.7; }
.cgw-theme-sky .cgw-cta { color: var(--cgw-sky-primary); }
.cgw-theme-blue .cgw-cta { color: var(--cgw-blue-primary); }
.cgw-theme-dark .cgw-cta { color: var(--cgw-dark-primary); }
.cgw-theme-light .cgw-cta { color: var(--cgw-light-primary); }
`;

    // Inject CSS
    if (!document.getElementById('cgw-styles')) {
        const style = document.createElement('style');
        style.id = 'cgw-styles';
        style.textContent = css;
        document.head.appendChild(style);
    }

    // Demo data
    const demoData = [
        {
            title: 'The Future of Cloud Gaming: What to Expect in 2024',
            excerpt: 'Discover the latest innovations and trends shaping the cloud gaming landscape this year. From improved latency to new platforms...',
            category: 'Game Reviews',
            date: 'Jan 15, 2024',
            readTime: '5 min read',
            link: '#',
            image: ''
        },
        {
            title: '5G and Cloud Gaming: A Perfect Match',
            excerpt: 'How 5G technology is revolutionizing cloud gaming experiences with ultra-low latency and improved streaming quality...',
            category: 'Technology',
            date: 'Jan 12, 2024',
            readTime: '4 min read',
            link: '#',
            image: ''
        },
        {
            title: 'Top 10 Games to Play on Cloud Gaming Platforms',
            excerpt: 'Our curated list of the best games optimized for cloud gaming, featuring AAA titles and indie favorites...',
            category: 'News',
            date: 'Jan 10, 2024',
            readTime: '8 min read',
            link: '#',
            image: ''
        }
    ];

    // Fetch posts or use demo data
    function fetchPosts() {
        if (widgetConfig.apiUrl) {
            fetch(widgetConfig.apiUrl)
                .then(res => res.json())
                .then(posts => {
                    const formattedPosts = posts.slice(0, 3).map(post => ({
                        title: post.title.rendered || post.title,
                        excerpt: post.excerpt?.rendered?.replace(/<[^>]*>/g, '').substring(0, 150) || '',
                        category: post.categories?.[0] || 'Uncategorized',
                        date: new Date(post.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
                        readTime: Math.ceil((post.content?.rendered?.split(' ').length || 300) / 200) + ' min read',
                        link: post.link || '#',
                        image: post.featured_media_url || post.jetpack_featured_media_url || ''
                    }));
                    renderWidget(formattedPosts);
                })
                .catch(() => renderWidget(demoData));
        } else {
            renderWidget(demoData);
        }
    }

    // Render widget
    function renderWidget(posts) {
        const container = document.getElementById(widgetConfig.containerId);
        if (!container) return;

        const html = `
            <section class="cgw-featured-posts cgw-theme-${widgetConfig.theme}">
                <header class="cgw-header">
                    <h2 class="cgw-title">${widgetConfig.title}</h2>
                </header>
                <div class="cgw-posts-grid">
                    ${posts.map(post => `
                        <article class="cgw-post-card">
                            <a href="${post.link}" class="cgw-thumbnail">
                                ${post.image 
                                    ? `<img src="${post.image}" alt="${post.title}" loading="lazy">`
                                    : `<div class="cgw-placeholder"><span>Cloud Gaming</span></div>`
                                }
                            </a>
                            <div class="cgw-content">
                                <span class="cgw-category">${post.category}</span>
                                <h3 class="cgw-post-title">
                                    <a href="${post.link}">${post.title}</a>
                                </h3>
                                <p class="cgw-excerpt">${post.excerpt}</p>
                                <footer class="cgw-footer">
                                    <span class="cgw-meta">
                                        ${post.date} <span>•</span> ${post.readTime}
                                    </span>
                                    <a href="${post.link}" class="cgw-cta">Read more ↗</a>
                                </footer>
                            </div>
                        </article>
                    `).join('')}
                </div>
            </section>
        `;

        container.innerHTML = html;
    }

    // Initialize
    fetchPosts();
})();
</script>
```

---

## ⚙️ Customization Options

### Change Theme
Replace `theme: 'sky'` with:
- `'sky'` - Sky Blue theme
- `'blue'` - Blue theme  
- `'dark'` - Dark theme
- `'light'` - Light theme

### Change Title
Modify: `title: 'Your Custom Title Here'`

### Connect to Your Blog
Add your WordPress REST API endpoint:
```javascript
apiUrl: 'https://yoursite.com/wp-json/wp/v2/posts?per_page=3'
```

### Use Custom Data
Replace the `demoData` array with your own posts:
```javascript
const demoData = [
    {
        title: 'Your Post Title',
        excerpt: 'Post excerpt...',
        category: 'Category Name',
        date: 'Jan 15, 2024',
        readTime: '5 min read',
        link: 'https://yoursite.com/post',
        image: 'https://yoursite.com/image.jpg'
    }
];
```

---

## 🎯 Where to Use It

### ✅ Static HTML Websites
Paste directly into your HTML files

### ✅ WordPress (without plugin)
Add to a Custom HTML block or widget

### ✅ Wix, Squarespace, Webflow
Use their HTML embed components

### ✅ Shopify
Add to a page using custom liquid/HTML

### ✅ Any CMS
Use HTML/JavaScript embed fields

---

## 📱 Features

- ✅ **Fully Responsive** (mobile, tablet, desktop)
- ✅ **No Dependencies** (no jQuery, no libraries needed)
- ✅ **4 Beautiful Themes** (all blue-focused)
- ✅ **No Shadows/Glowing** (clean, professional design)
- ✅ **API Ready** (connect to WordPress or any JSON API)
- ✅ **Demo Data Included** (works immediately)
- ✅ **Lightweight** (~10KB total)
- ✅ **Modern CSS** (CSS Grid, Flexbox, Custom Properties)
- ✅ **Smooth Animations** (hover effects, transitions)
- ✅ **SEO Friendly** (semantic HTML)

---

## 🔧 Advanced Configuration

### Multiple Widgets on Same Page

Use unique IDs for each widget:
```html
<!-- Widget 1 -->
<div id="cgw-posts-1"></div>
<script>/* ... set containerId: 'cgw-posts-1' ... */</script>

<!-- Widget 2 -->
<div id="cgw-posts-2"></div>
<script>/* ... set containerId: 'cgw-posts-2' ... */</script>
```

### Custom Colors

Modify the CSS variables:
```javascript
--cgw-sky-primary: #YOUR_COLOR;
```

### Different Post Count

Modify the slice value:
```javascript
posts.slice(0, 6)  // Show 6 posts instead of 3
```

---

## 🌐 Browser Support

- ✅ Chrome/Edge (last 2 versions)
- ✅ Firefox (last 2 versions)
- ✅ Safari (last 2 versions)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

---

## 📖 Examples

### WordPress Integration
```javascript
apiUrl: 'https://yoursite.com/wp-json/wp/v2/posts?per_page=3&_embed'
```

### Custom JSON API
```javascript
apiUrl: 'https://yourapi.com/posts.json'
```

### Static Data Only
```javascript
apiUrl: ''  // Leave empty to use demo data
```

---

## 💡 Tips

1. **Test First**: Open `embed-code.html` to see it in action
2. **One Script Tag**: The CSS is included in the JavaScript (no separate CSS file needed)
3. **Safe to Reuse**: CSS only gets injected once even with multiple widgets
4. **Fallback Ready**: If API fails, demo data displays automatically
5. **CORS**: Make sure your API allows requests from your domain

---

## 🎨 Theme Preview

| Theme | Primary Color | Best For |
|-------|--------------|----------|
| Sky Blue | #0EA5E9 | Modern, energetic brands |
| Blue | #2563EB | Traditional, trustworthy sites |
| Dark | #60A5FA | Premium experiences, night mode |
| Light | #1E40AF | Clean, minimalist designs |

---

## 🚨 Troubleshooting

**Widget doesn't appear?**
- Check browser console for errors
- Verify the container ID matches the config
- Ensure script tag is after the div

**API not working?**
- Check CORS settings on your server
- Verify API URL is correct
- Look for errors in browser console

**Styles look wrong?**
- Check for CSS conflicts with your site
- Try increasing CSS specificity
- Ensure no other styles override widget classes

---

## ⭐ Quick Copy Commands

### Sky Blue Theme
```bash
# Change theme to 'sky' in the config
```

### Blue Theme
```bash
# Change theme to 'blue' in the config
```

### Dark Theme
```bash
# Change theme to 'dark' in the config
```

### Light Theme
```bash
# Change theme to 'light' in the config
```

---

## 📞 Support

For questions or issues:
1. Check `demo.html` for working example
2. Review `PROJECT_SUMMARY.md` for technical details
3. Open `embed-code.html` to generate custom code

---

**That's it! Just copy, paste, and enjoy your cloud gaming widget! 🎮✨**
