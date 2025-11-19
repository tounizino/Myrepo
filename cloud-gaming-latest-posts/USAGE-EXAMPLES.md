# Cloud Gaming Latest Posts - Usage Examples

## Quick Start Guide

After installing and activating the plugin, you have several ways to display your latest posts grid.

## Method 1: Shortcode in Pages/Posts

The simplest way is to add the shortcode directly to any page or post content:

```
[cloud_gaming_posts]
```

### Example in Gutenberg/Block Editor

1. Add a **Shortcode Block**
2. Enter: `[cloud_gaming_posts]`
3. Publish or update the page

### Example in Classic Editor

Simply type or paste the shortcode into your content:

```
Welcome to our blog!

[cloud_gaming_posts]

Check out these latest articles!
```

## Method 2: Custom Posts Per Page

Override the default number of posts per page:

```
[cloud_gaming_posts posts_per_page="12"]
```

This will display 12 posts per page instead of the default 9.

## Method 3: Using in Template Files

If you're a developer and want to add the grid directly to your theme templates:

```php
<?php
// In your page template, single.php, or custom template file
echo do_shortcode('[cloud_gaming_posts]');
?>
```

Or with custom attributes:

```php
<?php
echo do_shortcode('[cloud_gaming_posts posts_per_page="6"]');
?>
```

## Method 4: Using in Widgets

1. Go to **Appearance → Widgets**
2. Add a **Shortcode Widget** or **Custom HTML Widget**
3. Enter the shortcode: `[cloud_gaming_posts]`
4. Save the widget

## Admin Panel Configuration

Navigate to **Settings → Cloud Gaming Latest Posts** to configure:

### Light Mode (Default)
- Clean white cards
- Blue accents (#0077ff)
- Subtle shadows and borders

### Dark Mode
- Dark gray cards (#1f2937)
- Enhanced contrast
- Optimized for dark backgrounds

### Container Settings

**Max Width Example:**
```
1400px (default - contained)
100% (full width)
1200px (narrower)
90% (responsive percentage)
```

**Margin Examples:**
```
0 auto 40px auto (default - centered with bottom margin)
20px auto (centered with equal top/bottom margins)
0 (no margin)
```

**Padding Examples:**
```
0 (default - no padding)
0 20px (horizontal padding only)
20px (equal padding all sides)
```

## Styling Examples

### Full Width Dark Theme
Set in admin:
- Enable Dark Theme: ✓
- Container Max Width: `100%`
- Container Margin: `0`
- Container Padding: `40px 20px`

### Contained Light Theme
Set in admin:
- Enable Dark Theme: (unchecked)
- Container Max Width: `1200px`
- Container Margin: `0 auto 60px auto`
- Container Padding: `0`

### Mobile-Optimized
The plugin automatically adjusts:
- Desktop: 3 columns
- Tablet (≤992px): 2 columns
- Mobile (≤600px): 1 column

## Badge System

Badges are automatically assigned:

- **NEW Badge** (Blue): Posts published in the last 14 days
- **UPDATED Badge** (Green): Posts significantly updated in the last 30 days
- **HOT Badge** (Red): Posts with 5+ comments
- **No Badge**: Older posts without significant activity

## Read Time Calculation

The plugin automatically calculates reading time based on:
- Word count in post content
- Average reading speed of 200 words per minute
- Minimum of 1 minute

## Integration with Page Builders

### Elementor
1. Add a **Shortcode Widget**
2. Paste: `[cloud_gaming_posts]`

### WPBakery
1. Add **Raw HTML** or **Text Block**
2. Enter: `[cloud_gaming_posts]`

### Divi
1. Add a **Code Module**
2. Insert: `[cloud_gaming_posts]`

## Troubleshooting

### Posts Not Showing?
- Check that you have published posts
- Verify the shortcode is spelled correctly: `[cloud_gaming_posts]`
- Clear any caching plugins

### Styling Issues?
- Try clearing your browser cache
- Check for theme CSS conflicts
- Ensure the plugin CSS is loading (check browser developer tools)

### Pagination Not Working?
- Verify jQuery is loaded (bundled with WordPress)
- Check browser console for JavaScript errors
- Ensure AJAX URL is accessible

## Advanced Customization

For developers who want to add custom CSS:

```css
/* Override card hover effect */
.gaming-latest-container.v27 .latest-card:hover {
  transform: translateY(-8px) !important;
}

/* Change primary color */
.gaming-latest-container.v27 .card-badge.new,
.gaming-latest-container.v27 .card-title a:hover {
  color: #ff6b00 !important;
  background: #ff6b00 !important;
}

/* Adjust card spacing */
.gaming-latest-container.v27 .latest-grid {
  gap: 30px !important;
}
```

Add custom CSS to:
- **Appearance → Customize → Additional CSS**, or
- Your child theme's `style.css`

## Performance Tips

1. **Use Caching**: Enable a caching plugin for better performance
2. **Optimize Images**: Ensure featured images are properly sized
3. **Limit Posts Per Page**: Keep it reasonable (9-12 is optimal)
4. **CDN**: Consider using a CDN for assets

## SEO Best Practices

The plugin automatically:
- Uses semantic HTML5 tags (`<article>`, `<h3>`, etc.)
- Includes proper alt text for images
- Maintains clean permalink structure
- Preserves post titles and metadata

## Support

For additional help:
- Check the README.md file
- Visit the plugin settings page for inline help
- Contact support at https://cloudloadout.com
