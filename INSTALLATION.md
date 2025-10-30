# Installation Guide - Ultimate Blocks for Cloud Gaming

## Quick Start

### Method 1: Manual Installation

1. **Download** the plugin files
2. **Upload** the entire `ultimate-blocks-cloud-gaming` folder to `/wp-content/plugins/`
3. **Activate** the plugin through the 'Plugins' menu in WordPress
4. **Configure** settings at **UBCG Settings** in the admin menu

### Method 2: WordPress Admin Upload

1. Go to **Plugins > Add New** in WordPress admin
2. Click **Upload Plugin** button
3. Choose the plugin ZIP file
4. Click **Install Now**
5. Click **Activate Plugin**

## Initial Configuration

### Step 1: Configure Colors

1. Navigate to **UBCG Settings** in the WordPress admin sidebar
2. Under **Color Settings**, customize your color palette:
   - **Primary Color**: Main accent color (default: #2563eb - Blue)
   - **Secondary Color**: Hover states and emphasis (default: #1e40af - Dark Blue)
   - **Accent Color**: Light backgrounds and borders (default: #60a5fa - Light Blue)
   - **Text Color**: Primary text (default: #1e293b - Dark Slate)

### Step 2: Display Settings

1. Set **Posts Per Page** (recommended: 9 for 3x3 grid)
2. Enable/disable **SEO Optimization** (recommended: enabled)

### Step 3: Save Settings

Click **Save Settings** to apply your changes.

## Using Blocks

### Adding Your First Block

1. Create or edit a **Page** or **Post**
2. Click the **+** button in the block editor
3. Search for **"UBCG"** or the specific block name (e.g., "Latest Posts Grid")
4. Click to add the block
5. Configure settings in the **right sidebar**
6. Preview and **Publish**

### Recommended Homepage Setup

For a cloud gaming blog homepage, try this layout:

1. **Gaming Hero** - Top banner with welcome message
2. **Latest Posts Grid** - 3x3 grid showing recent articles
3. **Featured Posts** - Highlight 3 most important posts
4. **Category Showcase** - Display main categories (e.g., GeForce NOW, Xbox Cloud Gaming, PS Plus)
5. **Newsletter Signup** - Bottom call-to-action

## Using Widgets

### Adding Sidebar Widgets

1. Go to **Appearance > Widgets**
2. Find widgets prefixed with **"UBCG:"**
3. **Drag** to your desired widget area (e.g., Primary Sidebar)
4. Configure widget options:
   - Set title
   - Choose number of items
   - Enable/disable features
5. Click **Save**

### Available Widgets

- **UBCG: Latest Posts** - Recent posts with thumbnails
- **UBCG: Featured Posts** - Featured content highlights
- **UBCG: Category List** - Category navigation with counts

## Block Configuration Guide

### Latest Posts Grid

**Best for**: Homepage, category pages, archive pages

**Configuration**:
- **Posts Per Page**: 9 (3x3), 8 (4x2), or 6 (3x2)
- **Columns**: 3 for desktop, automatically adjusts for mobile
- **Show Image**: ✓ (Recommended)
- **Show Date**: ✓ (Good for blogs)
- **Show Excerpt**: ✓ (Helps readers decide)
- **Show Category**: ✓ (Improves navigation)
- **Pagination**: ✓ (For more than 9 posts)

### Featured Posts

**Best for**: Homepage hero section, spotlight content

**Configuration**:
- **Number of Posts**: 3-5 posts
- **Layout**: Horizontal for homepage, Grid for sidebar areas
- **Show Image**: ✓
- **Show Excerpt**: ✓

### Category Showcase

**Best for**: Homepage category navigation, topic discovery

**Configuration**:
- **Columns**: 3 or 4 for desktop
- **Show Count**: ✓ (Shows post numbers)
- **Show Description**: ✓ (If you have category descriptions)

### Gaming Hero

**Best for**: Homepage banner, landing pages

**Configuration**:
- **Title**: "Welcome to [Your Site Name]"
- **Subtitle**: Brief description of your site
- **Button**: Link to your most popular category or about page

## Troubleshooting

### Blocks Not Appearing

1. **Clear Cache**: Clear browser cache and any WordPress caching plugins
2. **Check WordPress Version**: Requires WordPress 6.0+
3. **Check Block Editor**: Ensure Gutenberg is active
4. **Reactivate Plugin**: Deactivate and reactivate the plugin

### Styling Issues

1. **Clear Cache**: Clear all caches (browser, WordPress, CDN)
2. **Check Theme Compatibility**: Some themes may have conflicting styles
3. **Custom CSS**: Add custom CSS in Customizer > Additional CSS if needed

### Performance Issues

1. **Optimize Images**: Use image optimization plugins
2. **Enable Caching**: Use a caching plugin (W3 Total Cache, WP Super Cache)
3. **Reduce Posts Per Page**: Lower the number if loading is slow
4. **Use CDN**: Consider a CDN for static assets

## Best Practices

### For Cloud Gaming Blogs

1. **Use Categories Wisely**: Create categories for each major platform
   - GeForce NOW
   - Xbox Cloud Gaming
   - PlayStation Plus
   - Amazon Luna
   - Shadow

2. **Tag Articles**: Use tags for games, features, and topics
   - Game titles (Cyberpunk 2077, Fortnite)
   - Features (Ray Tracing, 4K Streaming)
   - Topics (Reviews, News, Guides)

3. **Featured Images**: Always use high-quality featured images (16:9 ratio)
   - Recommended size: 1200x675px
   - Format: JPG or WebP
   - Optimize for web

4. **Write Good Excerpts**: Custom excerpts improve SEO and user experience

5. **Update Regularly**: Keep content fresh with regular posts

### SEO Tips

1. **Enable SEO Optimization**: In UBCG Settings
2. **Use Proper Headings**: H1 for titles, H2 for sections
3. **Alt Text**: Add descriptive alt text to all images
4. **Internal Links**: Link between related articles
5. **Meta Descriptions**: Write compelling meta descriptions

### Accessibility

1. **Color Contrast**: Ensure text is readable (WCAG AA standard)
2. **Keyboard Navigation**: Test with keyboard only
3. **Alt Text**: Always provide for images
4. **Descriptive Links**: Use meaningful link text

## Advanced Customization

### Custom CSS Examples

```css
/* Increase post card spacing */
.ubcg-latest-posts-grid {
    gap: 2rem;
}

/* Change hover effect */
.ubcg-post-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

/* Custom button style */
.ubcg-hero-button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

### Filters and Hooks

```php
// Change default posts per page
add_filter('ubcg_default_posts_per_page', function($default) {
    return 12;
});

// Customize excerpt length
add_filter('ubcg_excerpt_length', function($length) {
    return 30; // words
});

// Add custom class to blocks
add_filter('ubcg_block_classes', function($classes) {
    $classes[] = 'my-custom-class';
    return $classes;
});
```

## Getting Help

### Documentation

Full documentation available at: [Your documentation URL]

### Support Channels

- **Email**: support@example.com
- **Forum**: [Your support forum]
- **FAQ**: [Your FAQ page]

### Before Requesting Support

1. Check this guide and README.md
2. Clear all caches
3. Try deactivating other plugins
4. Switch to a default WordPress theme temporarily
5. Check for JavaScript errors in browser console

## Updates

The plugin checks for updates automatically. When an update is available:

1. You'll see a notification in WordPress admin
2. Click **Update Now**
3. Wait for completion
4. Clear all caches

## Next Steps

✅ Plugin installed
✅ Settings configured
✅ First block added

**Now you're ready to build an amazing cloud gaming website!**

Explore all 10+ blocks and find the perfect combination for your site. Don't forget to check the settings panel regularly for new features.

---

Need help? Reach out to our support team or check the documentation.
