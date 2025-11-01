# Quick Start Guide - Top 5 Cloud Games Section

## 🚀 5-Minute Setup

### Step 1: Choose Your Version

Pick the version that matches your website theme:

- **Dark Version** (`top-5-cloud-games-section.html`) → Dark backgrounds, vibrant colors
- **Light Version** (`top-5-cloud-games-section-light.html`) → Light backgrounds, professional look

### Step 2: Copy the Code

1. Open your chosen HTML file
2. Select all content (Ctrl+A / Cmd+A)
3. Copy to clipboard (Ctrl+C / Cmd+C)

### Step 3: Paste in WordPress

1. Go to your WordPress admin
2. Edit the page where you want the section
3. Click the **"+"** button to add a new block
4. Search for **"Custom HTML"**
5. Paste your code into the block
6. Click **"Preview"** to see it live

### Step 4: Customize Your Links

Find each card's link and update the URL:

```html
<a class="card-link" href="YOUR-ARTICLE-URL-HERE">
```

**Example:**
```html
<a class="card-link" href="https://yourblog.com/cyberpunk-2077-review">
```

### Step 5: Update Game Information

For each game card, customize:

**Game Title:**
```html
<h3 class="game-title">Your Game Name</h3>
```

**Rating & Players:**
```html
<span class="stat-item">4.8</span> <!-- Your rating -->
<span class="stat-item">2.5M players</span> <!-- Your player count -->
```

**Genres:**
```html
<div class="game-genres">
    <span class="genre-chip">Action</span>
    <span class="genre-chip">Adventure</span>
</div>
```

**Image:**
```html
<img src="https://your-image-url.com/image.jpg" alt="Game Name">
```

### Step 6: Replace Images

1. Upload your game images to WordPress Media Library
2. Copy the image URLs
3. Replace placeholder URLs in the code
4. **Recommended size:** 400px × 520px

### Step 7: Publish!

Click **"Publish"** or **"Update"** and you're done! 🎉

---

## 📝 Example: Complete Card Setup

```html
<article class="game-card" data-game="1">
    <a class="card-link" href="https://yourblog.com/game-review" target="_self">
        <div class="card-inner">
            <div class="card-rank">
                <span class="rank-number">1</span>
            </div>
            <div class="card-image-wrapper">
                <img src="https://yourdomain.com/wp-content/uploads/game-cover.jpg" 
                     alt="Amazing Game - Cloud Gaming Review" 
                     loading="lazy">
                <div class="card-overlay"></div>
            </div>
            <div class="card-content">
                <h3 class="game-title">Amazing Game</h3>
                <div class="game-stats">
                    <span class="stat-item">
                        <svg>...</svg>
                        4.9
                    </span>
                    <span class="stat-divider">•</span>
                    <span class="stat-item">5.0M players</span>
                </div>
                <div class="game-genres">
                    <span class="genre-chip">Action</span>
                    <span class="genre-chip">Multiplayer</span>
                </div>
            </div>
        </div>
    </a>
</article>
```

---

## 🎨 Genre Chip Colors (Light Version Only)

Add color classes to genre chips:

```html
<!-- Light version uses color classes -->
<span class="genre-chip-light genre-blue">RPG</span>
<span class="genre-chip-light genre-purple">Fantasy</span>
<span class="genre-chip-light genre-pink">Action</span>
<span class="genre-chip-light genre-cyan">Sci-Fi</span>
<span class="genre-chip-light genre-green">Strategy</span>
```

**Dark version** automatically assigns unique colors per card!

---

## ⚠️ Common Mistakes to Avoid

### ❌ Don't Do This:
```html
<!-- Missing quotes -->
<a href=my-link>

<!-- Wrong image path -->
<img src="C:\Users\Desktop\image.jpg">

<!-- Broken HTML structure -->
<div class="card-inner">
</article> <!-- Wrong closing tag -->
```

### ✅ Do This Instead:
```html
<!-- Proper quotes -->
<a href="my-link">

<!-- Web-accessible URL -->
<img src="https://yourdomain.com/image.jpg">

<!-- Correct structure -->
<div class="card-inner">
</div>
```

---

## 🔍 Testing Checklist

Before publishing, verify:

- [ ] All links work correctly
- [ ] Images load properly
- [ ] Genre chips display with correct text
- [ ] Cards are clickable
- [ ] Mobile view looks good (use Preview → Mobile)
- [ ] Ratings and player counts are accurate
- [ ] Alt text is descriptive for SEO

---

## 🆘 Need Help?

### Images Not Showing?
- Ensure image URLs are absolute (start with `https://`)
- Check that images are publicly accessible
- Verify file permissions in WordPress Media Library

### Cards Not Clickable?
- Make sure the `<a class="card-link">` tag wraps all card content
- Check for JavaScript errors in browser console (F12)

### Layout Broken?
- Ensure you copied the **entire** HTML file including `<style>` and `<script>` tags
- Check for conflicting CSS from your theme
- Try adding `!important` to CSS rules if needed

### Links Not Working?
- Verify URLs are correct and pages exist
- Check for typos in `href` attributes
- Ensure links don't have extra spaces

---

## 💡 Pro Tips

1. **Use Your Own Screenshots:** Replace placeholders with actual game screenshots from your articles
2. **Keep Genres Short:** Use 1-3 words max per chip (e.g., "RPG" not "Role-Playing Game")
3. **Optimize Images:** Use tools like TinyPNG before uploading
4. **Update Regularly:** Keep your top 5 list fresh
5. **Track Clicks:** Use Google Analytics to see which games get the most clicks
6. **Match Your Branding:** Adjust colors in CSS to match your site
7. **Test on Mobile:** Most visitors will be on phones!

---

## 📱 Mobile Preview

Before publishing, always preview on mobile:

1. Click **"Preview"** in WordPress
2. Use browser DevTools (F12) → Toggle Device Toolbar
3. Test on different screen sizes:
   - iPhone (375px)
   - iPad (768px)
   - Desktop (1920px)

---

## 🎯 Next Steps

After setup:

1. ✅ Publish your page
2. 📊 Monitor page performance (Google PageSpeed Insights)
3. 📈 Track user engagement (Google Analytics)
4. 🔄 Update game rankings monthly
5. 🖼️ Replace images with higher quality versions
6. ✍️ Write detailed articles for each game
7. 🔗 Share on social media

---

## 🎉 You're Ready!

Your "Top 5 Cloud Games" section is now live and looking amazing. Happy gaming blogging! 🎮

For detailed customization options, check out `DOCUMENTATION.md` and `CHANGES.md`.
