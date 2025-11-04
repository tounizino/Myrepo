# Installation Guide

## Cloud Gaming Featured Posts Widget

This guide will walk you through installing and setting up the Cloud Gaming Featured Posts Widget on your WordPress site.

---

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- A WordPress theme with widget support

---

## 🚀 Installation Methods

### Method 1: Plugin Installation (Recommended)

1. **Download or Clone**
   ```bash
   git clone [repository-url]
   ```

2. **Upload to WordPress**
   - Upload the `cloud-gaming-featured-posts-widget` folder to `/wp-content/plugins/`
   - Or compress the folder to a .zip file and upload via WordPress admin

3. **Activate Plugin**
   - Go to WordPress Admin → Plugins
   - Find "Cloud Gaming Featured Posts Widget"
   - Click "Activate"

4. **Add to Widget Area**
   - Go to Appearance → Widgets
   - Find "Cloud Gaming Featured Posts" in available widgets
   - Drag to your desired widget area (sidebar, footer, etc.)
   - Configure settings and save

### Method 2: Direct Theme Integration

1. **Copy Files**
   - Copy the PHP code from `cloud-gaming-featured-posts-widget.php`
   - Paste into your theme's `functions.php`

2. **Update Asset Paths**
   ```php
   $style_url = get_template_directory_uri() . '/assets/css/featured-posts-widget.css';
   ```

3. **Copy CSS File**
   - Copy `assets/css/featured-posts-widget.css` to your theme's assets folder

4. **Register Widget**
   - Widget will be automatically registered
   - Go to Appearance → Widgets to use it

### Method 3: Shortcode/Template Usage

Add this to your theme's `functions.php`:

```php
function cgw_featured_posts_shortcode( $atts ) {
    $atts = shortcode_atts( [
        'title'       => 'Featured Stories',
        'theme'       => 'sky',
        'posts_count' => 3,
    ], $atts );

    ob_start();
    the_widget(
        'Cloud_Gaming_Featured_Posts_Widget',
        [
            'title'       => $atts['title'],
            'theme'       => $atts['theme'],
            'posts_count' => $atts['posts_count'],
        ]
    );
    return ob_get_clean();
}
add_shortcode( 'cloud_gaming_posts', 'cgw_featured_posts_shortcode' );
```

Then use in posts/pages:
```
[cloud_gaming_posts theme="dark" posts_count="3"]
```

---

## ⚙️ Configuration

### Widget Settings

**Title**
- Default: "Featured Stories"
- Customize the header text for your widget

**Theme**
- Options: Sky Blue, Blue, Dark, Light
- Choose the color scheme that matches your site

**Number of Posts**
- Range: 1-6 posts
- Default: 3 posts
- Controls how many posts display in the grid

---

## 🎨 Customization

### Color Customization

Edit `assets/css/featured-posts-widget.css` and modify CSS variables:

```css
:root {
  --cgw-sky-primary: #0EA5E9;    /* Change primary color */
  --cgw-sky-secondary: #38BDF8;  /* Change secondary color */
  /* ... modify other variables ... */
}
```

### Layout Customization

Adjust grid breakpoints:

```css
@media (min-width: 640px) {
  .cgw-posts-grid {
    grid-template-columns: repeat(2, 1fr);  /* 2 columns on tablets */
  }
}

@media (min-width: 1024px) {
  .cgw-posts-grid {
    grid-template-columns: repeat(3, 1fr);  /* 3 columns on desktop */
  }
}
```

### Query Customization

Modify the query in `cloud-gaming-featured-posts-widget.php`:

```php
$query_args = [
    'posts_per_page'      => $posts_count,
    'ignore_sticky_posts' => true,
    'post_status'         => 'publish',
    'category_name'       => 'gaming',  // Add category filter
    'meta_key'            => 'featured', // Add custom field filter
];
```

---

## 🧪 Testing the Widget

1. **Preview Demo**
   - Open `demo.html` in your browser
   - View all four theme variations

2. **Test in WordPress**
   - Add test posts with featured images
   - Assign categories to posts
   - View widget on frontend

3. **Test Responsiveness**
   - Check on mobile devices
   - Test at different screen sizes
   - Verify grid adapts properly

---

## 🔧 Troubleshooting

### Styles Not Loading

**Problem**: Widget appears unstyled

**Solution**:
1. Clear WordPress cache
2. Check file permissions on CSS file
3. Verify CSS file path is correct
4. Check browser console for 404 errors

### Widget Not Appearing

**Problem**: Widget doesn't show in widget list

**Solution**:
1. Ensure plugin is activated
2. Check for PHP errors in debug.log
3. Verify WordPress version compatibility
4. Check for conflicting plugins

### Images Not Displaying

**Problem**: Post thumbnails don't show

**Solution**:
1. Ensure posts have featured images set
2. Check image permissions
3. Regenerate thumbnails
4. Verify image URLs are accessible

### Layout Breaking

**Problem**: Grid layout not working

**Solution**:
1. Check for CSS conflicts with theme
2. Increase CSS specificity if needed
3. Inspect with browser dev tools
4. Ensure container has proper width

---

## 🆘 Support

For issues or questions:
1. Check the README.md documentation
2. Review the demo.html file
3. Contact development team

---

## ✅ Checklist

After installation, verify:

- [ ] Plugin is activated
- [ ] Widget appears in Widgets list
- [ ] Widget can be dragged to widget areas
- [ ] Settings save properly
- [ ] All 4 themes display correctly
- [ ] Grid is responsive on all devices
- [ ] Posts display with proper formatting
- [ ] Links work correctly
- [ ] Images load properly (or placeholder shows)
- [ ] Reading time calculates
- [ ] Categories display

---

## 🔄 Updating

When updating the widget:

1. **Backup Current Version**
   - Backup widget settings
   - Note any customizations made

2. **Update Files**
   - Replace old files with new ones
   - Merge any custom changes

3. **Test Thoroughly**
   - Verify all themes still work
   - Check responsive behavior
   - Test widget settings

4. **Clear Caches**
   - Clear WordPress cache
   - Clear browser cache
   - Purge CDN cache if applicable

---

## 📝 Next Steps

1. Customize colors to match your brand
2. Adjust the number of posts to display
3. Choose your preferred theme
4. Test on different devices
5. Monitor performance
6. Gather user feedback

Enjoy your new Cloud Gaming Featured Posts Widget! 🎮
