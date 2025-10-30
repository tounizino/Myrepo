# Ultimate Blocks for Cloud Gaming - Quick Start Guide

Get up and running in 5 minutes!

## 🚀 Installation (1 minute)

### Method 1: WordPress Admin
1. Go to **Plugins → Add New**
2. Click **Upload Plugin**
3. Choose the ZIP file
4. Click **Install Now** → **Activate**

### Method 2: Manual Upload
1. Upload folder to `/wp-content/plugins/`
2. Go to **Plugins** in WordPress admin
3. Find "Ultimate Blocks for Cloud Gaming"
4. Click **Activate**

---

## ⚙️ Initial Setup (2 minutes)

### Step 1: Configure Colors
1. Go to **UBCG Settings** in admin sidebar
2. Customize your colors (or keep the blue theme)
3. Click **Save Settings**

### Step 2: You're Ready!
That's it! Now start adding blocks to your pages.

---

## 📝 Create Your First Page (2 minutes)

### Homepage Setup Example

1. **Create a new page** (Pages → Add New)
2. **Set as homepage** (Settings → Reading)
3. **Add blocks in this order**:

```
+ Gaming Hero
  Title: "Cloud Gaming News & Reviews"
  Subtitle: "Your ultimate source for cloud gaming"
  
+ Latest Posts Grid
  Posts: 9
  Columns: 3
  Enable pagination: ✓
  
+ Newsletter Signup
  Title: "Never Miss an Update"
```

4. **Publish!**

---

## 🎯 Essential Blocks

### For Homepage
- **Gaming Hero** - Banner at top
- **Latest Posts Grid** - Main content area (3x3)
- **Category Showcase** - Navigate by platform
- **Newsletter Signup** - Bottom CTA

### For Sidebar (Widgets)
1. Go to **Appearance → Widgets**
2. Add **UBCG: Latest Posts**
3. Add **UBCG: Category List**

### For Review Pages
- **Review Card** - Featured review
- **Game Specs** - Technical details
- **Streaming Platforms** - Where to play
- **Performance Stats** - Benchmarks

---

## 🎨 Customize Appearance

### Change Colors
**UBCG Settings** → Color Settings
- Primary: Main brand color
- Secondary: Darker shade for hover
- Accent: Light backgrounds
- Text: Main text color

### Custom CSS (Optional)
**Appearance → Customize → Additional CSS**

```css
/* Wider spacing */
.ubcg-latest-posts-grid {
    gap: 2rem;
}

/* Round images more */
.ubcg-post-thumbnail img {
    border-radius: 12px;
}
```

---

## 📱 Mobile Optimization

**Automatically handled!** All blocks are:
- ✅ Mobile responsive
- ✅ Touch-friendly
- ✅ Fast loading
- ✅ Optimized images

---

## 🔍 SEO Setup

### Enable SEO Features
**UBCG Settings** → Display Settings
- ✓ Enable SEO Optimization

### Best Practices
1. Use descriptive block content
2. Add alt text to images
3. Write good meta descriptions
4. Use proper heading structure

---

## 🎮 Cloud Gaming Blog Tips

### Recommended Categories
Create these categories:
- GeForce NOW
- Xbox Cloud Gaming
- PlayStation Plus
- Amazon Luna
- Shadow PC
- News
- Reviews
- Guides

### Recommended Tags
Use tags for:
- Game titles (Cyberpunk 2077, Fortnite)
- Features (4K, Ray Tracing, 60 FPS)
- Topics (Streaming, Performance, Latency)

### Content Ideas
- Platform reviews
- Game performance tests
- Setup guides
- Comparison articles
- Industry news
- Tips & tricks

---

## 🛠️ Common Tasks

### Mark Post as Featured
Edit post → Scroll to bottom → Check "Featured Post"

### Create Category Page
1. Create new page
2. Add **Latest Posts Grid** block
3. Configure: Filter by category
4. Add **Category Showcase** at top

### Add Newsletter Form
1. Add **Newsletter Signup** block
2. Customize title and description
3. Emails saved in WordPress database

### Widget Setup
1. **Appearance → Widgets**
2. Drag UBCG widgets to sidebar
3. Configure settings
4. Save

---

## 📊 Block Settings Quick Reference

### Latest Posts Grid
- **Best for**: Homepage, archives
- **Posts**: 9 (for 3x3 grid)
- **Columns**: 3
- **Tips**: Enable pagination if you have many posts

### Gaming Hero
- **Best for**: Homepage top
- **Title**: Keep it short (5-7 words)
- **Button**: Link to your main category

### Featured Posts
- **Best for**: Below hero
- **Number**: 3-5 posts
- **Layout**: Horizontal for homepage

### Category Showcase
- **Best for**: Homepage navigation
- **Columns**: 3 or 4
- **Tip**: Add descriptions to categories

---

## 🚨 Troubleshooting

### Blocks not showing?
1. Clear browser cache
2. Clear WordPress cache
3. Refresh page

### Styling looks off?
1. Check theme compatibility
2. Try default WordPress theme
3. Clear all caches

### Newsletter not working?
1. Check AJAX enabled in browser
2. Verify nonce in form
3. Check error console

### Need more help?
- Read **INSTALLATION.md** for detailed setup
- Check **EXAMPLES.md** for usage examples
- Email: support@example.com

---

## 🎓 Next Steps

### Learn More
1. Read **README.md** - Full features
2. Browse **EXAMPLES.md** - Real layouts
3. Check **INSTALLATION.md** - Advanced setup

### Get Creative
- Experiment with different layouts
- Combine multiple blocks
- Customize colors to match your brand
- Add custom CSS for unique touches

### Join Community
- Share your site
- Request features
- Report bugs
- Help others

---

## ⭐ Recommended First Page Structure

```
┌─────────────────────────────────────────┐
│         GAMING HERO BANNER              │
│   "Welcome to Cloud Gaming News"        │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│         FEATURED POSTS (3)              │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│    CATEGORY SHOWCASE (Platforms)        │
└─────────────────────────────────────────┘

┌───────────┬───────────┬───────────┐
│  Post 1   │  Post 2   │  Post 3   │
├───────────┼───────────┼───────────┤
│  Post 4   │  Post 5   │  Post 6   │
├───────────┼───────────┼───────────┤
│  Post 7   │  Post 8   │  Post 9   │
└───────────┴───────────┴───────────┘
        LATEST POSTS GRID

┌─────────────────────────────────────────┐
│       NEWSLETTER SIGNUP                 │
└─────────────────────────────────────────┘
```

**Time to create**: 5 minutes  
**Result**: Professional cloud gaming homepage

---

## ✅ Checklist

Before going live:
- [ ] Configured colors in settings
- [ ] Created main categories
- [ ] Published 5+ posts
- [ ] Set up homepage
- [ ] Added sidebar widgets
- [ ] Tested on mobile
- [ ] Added newsletter form
- [ ] Checked links work
- [ ] Reviewed on different browsers
- [ ] Cleared all caches

---

## 💡 Pro Tips

1. **Post Regularly** - Use "Latest Posts Grid" for automatic updates
2. **Use Categories** - Helps readers find content
3. **Add Images** - Always use featured images (16:9 ratio)
4. **Write Excerpts** - Custom excerpts improve display
5. **Enable SEO** - Turn on in UBCG Settings
6. **Test Mobile** - Always check mobile view
7. **Use Pagination** - For grids with 10+ posts
8. **Feature Best Posts** - Mark top content as featured
9. **Update Colors** - Match your brand
10. **Read Documentation** - Lots of tips in docs

---

## 🎉 You're Ready!

You now have everything you need to create an amazing cloud gaming website!

**Need help?** Check the full documentation or contact support.

**Have fun!** 🎮

---

**Quick Links**
- [Full Documentation](README.md)
- [Installation Guide](INSTALLATION.md)
- [Usage Examples](EXAMPLES.md)
- Support: support@example.com
