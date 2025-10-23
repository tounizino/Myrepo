# 🔌 WordPress Integration Guide

Complete guide for integrating the Cloud Gaming Dashboard into WordPress.

## 📦 Installation

### Method 1: Manual Upload

1. **Download the plugin files**
   ```bash
   # All files in this directory
   cloud-gaming-dashboard/
   ```

2. **Upload to WordPress**
   - Connect via FTP/SFTP or use File Manager
   - Navigate to `/wp-content/plugins/`
   - Upload the entire `cloud-gaming-dashboard` folder
   - Folder structure should be:
     ```
     /wp-content/plugins/cloud-gaming-dashboard/
     ├── cloud-gaming-dashboard.php
     ├── assets/
     └── templates/
     ```

3. **Activate**
   - Go to WordPress Admin Dashboard
   - Navigate to **Plugins** → **Installed Plugins**
   - Find "Cloud Gaming Status & Launcher Dashboard"
   - Click **Activate**

### Method 2: ZIP Upload

1. **Create ZIP file**
   ```bash
   zip -r cloud-gaming-dashboard.zip cloud-gaming-dashboard/
   ```

2. **Upload via WordPress Admin**
   - Go to **Plugins** → **Add New**
   - Click **Upload Plugin**
   - Choose the ZIP file
   - Click **Install Now**
   - Click **Activate Plugin**

---

## 🎯 Using Shortcodes

### Full Dashboard (Recommended)

Add to any page or post:

```
[cloud_gaming_combined]
```

**Displays:**
- Complete status dashboard
- Statistics overview
- Quick launcher widget
- Search and filters
- Dark mode toggle

**Best for:**
- Dedicated cloud gaming pages
- Resource centers
- Main landing pages

---

### Status Dashboard Only

```
[cloud_gaming_dashboard]
```

**Displays:**
- Service status cards
- Statistics overview
- Search and filters
- No launcher section

**Best for:**
- Status monitoring pages
- Service availability pages
- Sidebar content (wide sidebars)

---

### Launcher Widget Only

```
[cloud_gaming_launcher]
```

**Displays:**
- Quick launch buttons
- Service icons
- Favorites
- No status details

**Best for:**
- Sidebars
- Footer widgets
- Quick access menus
- Compact spaces

---

## 📝 Page/Post Editor Integration

### Gutenberg (Block Editor)

1. **Create/Edit Page**
2. **Add Shortcode Block**
   - Click the **+** button
   - Search for "Shortcode"
   - Select the **Shortcode** block
3. **Paste Shortcode**
   ```
   [cloud_gaming_combined]
   ```
4. **Publish/Update**

### Classic Editor

1. **Create/Edit Page**
2. **Switch to Text Mode** (if in Visual mode)
3. **Paste Shortcode**
   ```
   [cloud_gaming_combined]
   ```
4. **Publish/Update**

### Page Builders

#### Elementor
1. Add **Shortcode Widget**
2. Paste: `[cloud_gaming_combined]`
3. Save

#### WPBakery
1. Add **Text Block**
2. Insert shortcode
3. Save

#### Divi
1. Add **Code Module**
2. Insert shortcode
3. Save

---

## 🎨 Widget Areas

### Adding to Sidebar

1. **Go to Appearance → Widgets**
2. **Find Text or HTML Widget**
3. **Drag to Sidebar Area**
4. **Add Shortcode**
   ```
   [cloud_gaming_launcher]
   ```
   *(Use launcher shortcode for sidebars)*
5. **Save Widget**

### Adding to Footer

1. **Go to Appearance → Widgets**
2. **Find Footer Widget Area**
3. **Add Text/HTML Widget**
4. **Insert Shortcode**
5. **Save**

---

## 🔧 Theme Integration

### In Template Files

Add to any template file (header.php, footer.php, etc.):

```php
<?php echo do_shortcode('[cloud_gaming_combined]'); ?>
```

### Custom Page Template

Create `page-cloud-gaming.php`:

```php
<?php
/**
 * Template Name: Cloud Gaming Dashboard
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        while (have_posts()) :
            the_post();
            the_content();
            
            // Add dashboard
            echo do_shortcode('[cloud_gaming_combined]');
            
        endwhile;
        ?>
    </main>
</div>

<?php get_footer(); ?>
```

### Functions.php Integration

Add custom functionality:

```php
// Add dashboard to specific pages
function add_cloud_gaming_dashboard($content) {
    if (is_page('cloud-gaming')) {
        $content .= do_shortcode('[cloud_gaming_combined]');
    }
    return $content;
}
add_filter('the_content', 'add_cloud_gaming_dashboard');
```

---

## 🎨 Styling Customization

### In WordPress Customizer

**Appearance → Customize → Additional CSS**

```css
/* Change dashboard background */
.cloud-gaming-container {
    background: linear-gradient(135deg, #FF6B6B 0%, #4ECDC4 100%) !important;
}

/* Adjust card spacing */
.cgd-status-grid {
    gap: 30px !important;
}

/* Hide statistics */
.cgd-stats-section {
    display: none !important;
}

/* Make launcher compact */
.cgd-launcher-grid {
    grid-template-columns: repeat(4, 1fr) !important;
}
```

### In Child Theme

Create `style.css` in child theme:

```css
/* Cloud Gaming Dashboard Customizations */

.cloud-gaming-container {
    max-width: 1400px !important;
    margin: 40px auto !important;
}

.cgd-title {
    font-family: 'Your Theme Font', sans-serif !important;
    color: #your-brand-color !important;
}
```

---

## 🔌 Advanced WordPress Integration

### Custom Post Type Integration

```php
// Add dashboard to custom post type
function add_dashboard_to_cpt($content) {
    if (is_singular('cloud_game')) {
        $dashboard = do_shortcode('[cloud_gaming_launcher]');
        return $content . $dashboard;
    }
    return $content;
}
add_filter('the_content', 'add_dashboard_to_cpt');
```

### Archive Page Integration

```php
// Add to archive pages
function add_dashboard_to_archive() {
    if (is_post_type_archive('cloud_game')) {
        echo do_shortcode('[cloud_gaming_dashboard]');
    }
}
add_action('pre_get_posts', 'add_dashboard_to_archive');
```

### Menu Integration

Add to `functions.php`:

```php
// Add custom menu item
function add_dashboard_menu() {
    add_menu_page(
        'Cloud Gaming',
        'Cloud Gaming',
        'manage_options',
        'cloud-gaming-dashboard',
        'render_dashboard_page',
        'dashicons-games',
        30
    );
}
add_action('admin_menu', 'add_dashboard_menu');

function render_dashboard_page() {
    echo '<div class="wrap">';
    echo '<h1>Cloud Gaming Dashboard</h1>';
    echo do_shortcode('[cloud_gaming_combined]');
    echo '</div>';
}
```

---

## 🔒 Security Best Practices

### User Capabilities

Restrict access to certain users:

```php
// Only show to logged-in users
function secure_cloud_gaming_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '<p>Please log in to view dashboard.</p>';
    }
    return do_shortcode('[cloud_gaming_combined]');
}
add_shortcode('secure_cloud_gaming', 'secure_cloud_gaming_shortcode');
```

### Rate Limiting

Prevent abuse:

```php
// Limit dashboard views
function rate_limit_dashboard() {
    $transient = get_transient('cloud_gaming_view_' . get_current_user_id());
    if ($transient) {
        return '<p>Please wait before refreshing.</p>';
    }
    set_transient('cloud_gaming_view_' . get_current_user_id(), true, 60);
    return do_shortcode('[cloud_gaming_combined]');
}
```

---

## 📊 Analytics Integration

### Google Analytics

Add to `functions.php`:

```php
// Track dashboard views
function track_dashboard_view() {
    if (has_shortcode(get_post()->post_content, 'cloud_gaming_combined')) {
        ?>
        <script>
        gtag('event', 'page_view', {
            'page_title': 'Cloud Gaming Dashboard',
            'page_location': window.location.href
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'track_dashboard_view');
```

---

## 🐛 Troubleshooting

### Dashboard Not Showing

**Check:**
1. Plugin is activated
2. Shortcode is spelled correctly
3. No JavaScript errors in console (F12)
4. Theme compatibility

**Solution:**
```php
// Force load assets
function force_load_dashboard_assets() {
    wp_enqueue_style('cloud-gaming-dashboard-css');
    wp_enqueue_script('cloud-gaming-dashboard-js');
}
add_action('wp_enqueue_scripts', 'force_load_dashboard_assets');
```

### Styling Conflicts

**Check:**
1. Theme CSS conflicts
2. Other plugin conflicts
3. Caching issues

**Solution:**
```php
// Increase CSS specificity
function increase_dashboard_css_priority() {
    wp_dequeue_style('cloud-gaming-dashboard-css');
    wp_enqueue_style('cloud-gaming-dashboard-css', 
        plugin_dir_url(__FILE__) . 'assets/css/cloud-gaming-dashboard.css',
        array(),
        '1.0.0',
        'all'
    );
}
add_action('wp_enqueue_scripts', 'increase_dashboard_css_priority', 999);
```

### JavaScript Errors

**Check:**
1. jQuery conflicts
2. Other JavaScript errors
3. Browser console

**Solution:**
```php
// Load dashboard JS in footer
function load_dashboard_js_footer() {
    wp_deregister_script('cloud-gaming-dashboard-js');
    wp_register_script('cloud-gaming-dashboard-js',
        plugin_dir_url(__FILE__) . 'assets/js/cloud-gaming-dashboard.js',
        array(),
        '1.0.0',
        true // Load in footer
    );
    wp_enqueue_script('cloud-gaming-dashboard-js');
}
add_action('wp_enqueue_scripts', 'load_dashboard_js_footer');
```

---

## ⚡ Performance Optimization

### Conditional Loading

Only load on specific pages:

```php
function conditional_dashboard_assets() {
    // Only load on pages with shortcode
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'cloud_gaming_combined')) {
        wp_enqueue_style('cloud-gaming-dashboard-css');
        wp_enqueue_script('cloud-gaming-dashboard-js');
    }
}
add_action('wp_enqueue_scripts', 'conditional_dashboard_assets');
```

### Caching

Use transient API:

```php
function cached_dashboard_output() {
    $cached = get_transient('cloud_gaming_dashboard_output');
    if ($cached) {
        return $cached;
    }
    
    $output = do_shortcode('[cloud_gaming_combined]');
    set_transient('cloud_gaming_dashboard_output', $output, 300); // 5 minutes
    return $output;
}
```

---

## 🔄 Updates & Maintenance

### Check for Updates

Currently manual updates. Future versions will support:
- WordPress.org plugin repository
- Automatic updates
- Update notifications

### Manual Update Process

1. Deactivate current plugin
2. Backup current files
3. Upload new version
4. Reactivate plugin
5. Clear cache

---

## 📞 WordPress-Specific Support

### Common Questions

**Q: Compatible with my theme?**  
A: Yes, works with all WordPress themes.

**Q: Multisite compatible?**  
A: Yes, can be network activated.

**Q: Translation ready?**  
A: Currently English only, i18n coming soon.

**Q: Gutenberg compatible?**  
A: Yes, via Shortcode block.

**Q: Page builder compatible?**  
A: Yes, all major builders supported.

---

## 🎓 WordPress Resources

- [Plugin Handbook](https://developer.wordpress.org/plugins/)
- [Theme Developer Handbook](https://developer.wordpress.org/themes/)
- [WordPress Codex](https://codex.wordpress.org/)
- [WordPress Support Forums](https://wordpress.org/support/)

---

## ✅ WordPress Checklist

- [ ] Plugin uploaded to correct directory
- [ ] Plugin activated
- [ ] Shortcode added to page
- [ ] Page published
- [ ] Cache cleared
- [ ] Tested in different browsers
- [ ] Mobile responsive verified
- [ ] No console errors

---

**Made for WordPress 5.0+**

For more help: support@cloudloadout.com
