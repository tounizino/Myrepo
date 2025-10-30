# Installation Guide

## Quick Start (5 Minutes)

### 1. Install the Plugin

**Method A: Upload via WordPress Admin**
1. Download the `cloud-layout-builder-pro` folder as a ZIP file
2. Go to **Plugins → Add New → Upload Plugin** in WordPress
3. Select the ZIP file and click **Install Now**
4. Click **Activate Plugin**

**Method B: Manual Installation**
1. Copy the `cloud-layout-builder-pro` folder to `/wp-content/plugins/`
2. Go to **Plugins** in WordPress admin
3. Find "Cloud Layout Builder Pro" and click **Activate**

### 2. Build Your Homepage

1. Navigate to **Layout Builder → Homepage Builder** in your WordPress admin
2. Click **+ Add Block** to add your first block
3. Drag blocks to reorder them
4. Click the ⚙️ (settings) icon to configure each block
5. Toggle blocks on/off as needed
6. Click **Save Layout**

### 3. Add to Your Page

Add this shortcode to any page where you want to display your homepage layout:

```
[clbp_homepage]
```

**Pro Tip:** Set this page as your homepage in **Settings → Reading → Your homepage displays**.

### 4. Customize Design (Optional)

1. Go to **Layout Builder → Settings**
2. Change colors to match your brand
3. Adjust typography, spacing, and layout
4. Add custom CSS if needed
5. Click **Save Settings**

---

## Detailed Setup

### System Requirements

- **WordPress:** 6.0 or higher
- **PHP:** 7.4 or higher
- **Recommended:** PHP 8.0+, WordPress 6.4+

### First-Time Configuration

#### Step 1: Configure Your First Block

After activating the plugin, you'll have 2 default blocks enabled:
- Featured Hero Article
- Latest Articles Grid

To configure them:

1. Go to **Layout Builder → Homepage Builder**
2. Click the ⚙️ icon on "Featured Hero Article"
3. Choose which post to feature (latest or specific)
4. Adjust overlay opacity and button text
5. Click **Save Settings**

#### Step 2: Add More Blocks

Click **+ Add Block** and choose from:

- **Latest Articles Grid** - 3x3 responsive grid with pagination
- **Featured Hero** - Large hero section
- **Category Highlight** - Showcase a specific category
- **Tag/Topic Block** - Filter by tags
- **Carousel** - Auto-rotating content
- **Mixed Content** - Articles + banners + videos
- **Custom Links** - Resource links with icons
- **Newsletter** - Email capture form
- **Quote/Tip** - Highlight insights

#### Step 3: Customize Block Settings

Each block has unique settings:

**Latest Articles Grid:**
- Number of posts (3-24)
- Columns (1-4)
- Layout (Grid or Masonry)
- Category/Tag filters
- Pagination type

**Featured Hero:**
- Post source (Latest or specific)
- Overlay toggle
- Button customization
- Min height

**Category Highlight:**
- Category selection
- Number of items (3, 6, or 9)
- Layout style (Horizontal or Tiles)

...and more!

#### Step 4: Device Visibility

Control which devices see each block:

1. Click ⚙️ on any block
2. Find "Device Visibility" section
3. Toggle Desktop / Tablet / Mobile
4. Save changes

### Using Shortcodes

#### Full Homepage

```
[clbp_homepage]
```

#### Individual Blocks

```
[clbp_latest_articles postsToShow="9" columns="3"]
[clbp_featured_hero]
[clbp_category_highlight category="1" items="6"]
[clbp_carousel autoplay="true" itemCount="6"]
[clbp_newsletter]
[clbp_quote_tip quote="Your quote" author="Author Name"]
```

### Adding Widgets

1. Go to **Appearance → Widgets**
2. Find widgets starting with "CLBP:"
3. Drag to your sidebar or footer
4. Configure and save

Available widgets:
- CLBP: Latest Posts
- CLBP: Category List
- CLBP: Editor's Picks
- CLBP: Mini Search
- CLBP: Tag Cloud
- CLBP: Custom HTML / CTA

---

## Advanced Configuration

### Customizer Integration

1. Go to **Appearance → Customize**
2. Find **Cloud Layout Builder** panel
3. Adjust colors, typography, and spacing
4. See changes in real-time
5. Click **Publish**

### Import/Export Settings

**Export:**
1. Go to **Layout Builder → Import/Export**
2. Click **Download Export File**
3. Save JSON file as backup

**Import:**
1. Go to **Layout Builder → Import/Export**
2. Paste JSON configuration
3. Click **Import Configuration**
4. Page will reload with new settings

### Custom CSS

Add custom styles:

1. Go to **Layout Builder → Settings**
2. Scroll to **Custom CSS** section
3. Add your CSS code
4. Click **Save Settings**

Example:
```css
.clbp-post-card {
    border: 2px solid #1E88E5;
}

.clbp-hero-title {
    text-transform: uppercase;
}
```

### Custom JavaScript

Add custom scripts:

1. Go to **Layout Builder → Settings**
2. Scroll to **Custom JS** section
3. Add your JavaScript code (no `<script>` tags)
4. Click **Save Settings**

Example:
```javascript
console.log('Cloud Layout Builder Pro Loaded');

jQuery(document).ready(function($) {
    $('.clbp-post-card').hover(
        function() { $(this).addClass('highlight'); },
        function() { $(this).removeClass('highlight'); }
    );
});
```

---

## Troubleshooting

### Blocks Not Appearing

1. Check if blocks are enabled in Homepage Builder
2. Verify shortcode is added to page: `[clbp_homepage]`
3. Clear cache (if using caching plugin)
4. Check theme compatibility

### Styles Not Loading

1. Go to **Settings → Permalinks** and click **Save**
2. Clear browser cache
3. Disable caching plugins temporarily
4. Check for CSS conflicts with theme

### Images Not Loading

1. Regenerate thumbnails using a plugin like "Regenerate Thumbnails"
2. Check media library permissions
3. Enable lazy loading in **Settings**

### Performance Issues

1. Enable lazy loading
2. Use fewer posts per block
3. Disable autoplay on carousels
4. Install a caching plugin (WP Super Cache, W3 Total Cache)
5. Optimize images before uploading

---

## Support

### Getting Help

- **Documentation:** Full docs at `/cloud-layout-builder-pro/README.md`
- **WordPress Forum:** Submit support tickets
- **GitHub:** Report bugs or request features

### Before Asking for Help

Please provide:
- WordPress version
- PHP version
- Active theme name
- List of active plugins
- Description of the issue
- Steps to reproduce

---

## Next Steps

✅ **Done installing?** Here's what to do next:

1. **Explore Examples** - Check pre-built block combinations
2. **Customize Colors** - Match your brand identity
3. **Add Widgets** - Enhance your sidebars
4. **Export Settings** - Create a backup
5. **Join Community** - Share your layouts!

**Enjoy building with Cloud Layout Builder Pro!** 🚀
