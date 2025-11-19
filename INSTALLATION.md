# Installation & Quick Start Guide

## Installation Steps

### Method 1: Upload via WordPress Admin
1. Download the entire `sections-autoposts-plugin` folder as a ZIP
2. Go to **WordPress Admin → Plugins → Add New → Upload Plugin**
3. Choose the ZIP file and click **Install Now**
4. Click **Activate Plugin**

### Method 2: Manual FTP/SFTP Upload
1. Upload the `sections-autoposts-plugin` folder to `/wp-content/plugins/`
2. Go to **WordPress Admin → Plugins**
3. Find **Sections AutoPosts** and click **Activate**

## First-Time Setup

### Step 1: Access Settings
1. Go to **WordPress Admin → Sections AutoPosts** (look for the layout icon in sidebar)
2. You'll see 9 expandable section cards

### Step 2: Configure Your First Section
1. Click on **Featured Posts + Widget** to expand
2. Ensure the checkbox at top is **enabled** (checked)
3. Customize:
   - **Section Title**: e.g., "Featured Cloud Gaming"
   - **Post Source**: Choose "Latest Published" or select a category
   - **Theme**: Select Light or Dark
   - **Accent Color**: Pick your brand color (default: #0077ff)
   - **Container Width**: Keep default "1400px" or adjust
4. Click **Save All Sections** at bottom

### Step 3: Add to Page
1. Edit any page or post
2. Add a Shortcode block (or Classic Editor shortcode)
3. Insert: `[sap_section type="featured"]`
4. Preview or Publish

### Step 4: Add All Sections
To show all enabled sections automatically:
```
[sap_all_sections]
```
This respects the **Display Order** field in each section settings.

## Common Use Cases

### Homepage Hero Section
```
[sap_section type="featured"]
```
Shows large featured post + widget sidebar.

### Latest Articles Archive
```
[sap_section type="latest_posts"]
```
Paginated grid with automatic "Next/Previous" controls.

### Beginner's Guide Hub
```
[sap_section type="beginners"]
```
Step-by-step numbered cards perfect for tutorials.

### News Timeline
```
[sap_section type="latest_news"]
```
Timeline layout with date badges and category tags.

## Configuration Reference

### Post Sources
- **Latest Published**: Newest posts first
- **Latest Updated**: Recently modified posts
- **Most Popular**: Sorted by comment count
- **Filter by Category**: Select one or more categories
- **Filter by Tag**: Select one or more tags
- **Custom Post IDs**: Enter comma-separated IDs (e.g., `12,45,78`)

### Dark Theme
Toggle **Theme → Dark** to enable:
- Inverted card backgrounds
- Light text on dark surfaces
- Adjusted borders and shadows
- Maintains readability and contrast

### Styling Controls
- **Background Color**: Section wrapper background (use "transparent" for none)
- **Accent Color**: Primary brand color (buttons, badges, hover states)
- **Secondary Accent**: Used for widget/sidebar elements in dual-column layouts
- **Padding**: Inner spacing (e.g., `40px 20px` = top/bottom 40px, left/right 20px)
- **Margin**: Outer spacing (e.g., `0 auto 40px auto` = center horizontally, 40px bottom gap)
- **Container Width**: Max-width for content (e.g., `1400px`, `100%`, `90vw`)
- **Custom CSS Classes**: Add your own classes for theme-specific styling

### Badge Thresholds
Configure automatic badges:
- **New Badge (days)**: Posts published within X days show "NEW" (default: 7)
- **Updated Badge (days)**: Posts modified within X days show "UPDATED" (default: 14)
- **Hot Badge (comments)**: Posts with X+ comments show "HOT" (default: 5)

Set to `0` to disable a badge type.

## Advanced Customization

### Custom Meta Fields

#### Platform Ratings (for "Platform Comparison" section)
```php
// In your theme functions.php or custom plugin
add_action('save_post', function($post_id) {
    // Example: Add custom rating field
    update_post_meta($post_id, 'sap_rating', '9.2/10');
});
```

#### Features List
```php
$features = "RTX 4080 rigs\n1600+ games\nBring your own games";
update_post_meta($post_id, 'sap_features', $features);
```

#### Difficulty Badge
```php
update_post_meta($post_id, 'sap_difficulty', 'EXPERT');
// Options: BEGINNER, PRO, EXPERT, ADVANCED
```

### PHP Theme Integration
Instead of shortcodes, call directly in theme files:
```php
<?php
if (class_exists('SAP_Templates')) {
    $settings = SAP_Settings::instance()->get_section('featured');
    SAP_Templates::render('featured', $settings);
}
?>
```

### CSS Overrides
Target sections with specific classes:
```css
/* Custom styling for featured section in dark mode */
.sap-theme-dark .sap-section--featured {
    background: linear-gradient(135deg, #1a202c, #2d3748);
}

/* Adjust card hover effects */
.sap-latest-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 24px 48px rgba(0,0,0,0.2);
}
```

### JavaScript Hooks
Extend pagination or add custom behavior:
```javascript
jQuery(document).ready(function($){
    // After pagination renders
    $(document).on('sap-pagination-rendered', function(e, page){
        console.log('Page changed to:', page);
    });
});
```

## Troubleshooting

### No Posts Showing
1. Check section is **enabled** (checkbox in admin)
2. Verify you have published posts matching the query
3. If using categories/tags, ensure posts are properly assigned
4. Check **Display Order** isn't set too high

### Styling Issues
1. Ensure your theme doesn't override plugin styles
2. Try adding `!important` to custom CSS overrides
3. Check browser console for CSS conflicts
4. Disable other layout plugins temporarily

### Shortcode Not Rendering
1. Verify plugin is activated
2. Check shortcode syntax: `[sap_section type="featured"]` (no typos)
3. Ensure section type exists (featured, beginners, community, etc.)
4. Look for PHP errors in WP debug log

### Performance Optimization
1. Reduce **Number of Posts** in settings (fewer queries)
2. Use caching plugin (W3 Total Cache, WP Rocket)
3. Optimize images with WebP format
4. Consider lazy loading for images

## Support & Documentation

- **Full Documentation**: See `README.md` in plugin folder
- **GitHub Issues**: Report bugs or request features
- **Support Forum**: https://cloudloadout.com/support

## Credits

Created by **CloudLoadout Team**
Version 1.0.0 | GPL v2 or later
