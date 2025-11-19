# Developer Notes - Cloud Gaming Latest Posts Grid

## Code Organization

### Main Plugin File
`cloud-gaming-latest-posts.php` - Entry point with singleton pattern
- Registers hooks and filters
- Handles AJAX endpoints
- Manages settings storage

### Template Files (includes/)
- `admin-page.php` - Admin settings interface
- `posts-grid.php` - Main container and grid wrapper
- `post-card.php` - Individual post card markup

### Assets
- `assets/css/frontend.css` - All frontend styles (light + dark themes)
- `assets/css/admin.css` - Admin panel styling
- `assets/js/frontend.js` - AJAX pagination logic with jQuery
- `assets/images/placeholder.svg` - Fallback for posts without featured images

## Code Standards

- **WordPress Coding Standards**: Follows WP PHP, HTML, CSS, and JS standards
- **Escaping**: All output is properly escaped (esc_html, esc_attr, esc_url)
- **Sanitization**: All input is sanitized before storage
- **Nonces**: AJAX requests use nonce verification
- **Prefixes**: All functions/classes prefixed with `cglp_` or `Cloud_Gaming_Latest_Posts`

## Key Functions

### Badge Logic
```php
Cloud_Gaming_Latest_Posts::get_post_badge($post_id)
```
Returns array with badge type and label, or null:
- NEW: Posts ≤14 days old
- UPDATED: Modified posts within 30 days
- HOT: Posts with 5+ comments

### Read Time Calculation
```php
Cloud_Gaming_Latest_Posts::calculate_read_time($post_id)
```
Calculates based on 200 words/minute, returns integer (min 1)

## AJAX Implementation

**Endpoint**: `wp_ajax_cglp_load_posts` and `wp_ajax_nopriv_cglp_load_posts`

**Request**:
```javascript
{
  action: 'cglp_load_posts',
  nonce: 'security_token',
  page: 2,
  posts_per_page: 9
}
```

**Response**:
```javascript
{
  success: true,
  data: {
    html: '<article>...</article>...',
    total_pages: 5,
    current_page: 2
  }
}
```

## Settings Storage

Stored as single option: `cglp_settings`

```php
[
  'posts_per_page' => 9,
  'dark_mode' => 0|1,
  'container_max_width' => '1400px',
  'container_margin' => '0 auto 40px auto',
  'container_padding' => '0'
]
```

## CSS Architecture

### CSS Custom Properties
Applied via inline style on container:
```css
--cglp-container-max-width: value;
--cglp-container-margin: value;
--cglp-container-padding: value;
```

### Scoped Styling
All styles scoped to `.gaming-latest-container.v27` to prevent conflicts

### Theme Variants
- `.light-theme` - Default (can be omitted)
- `.dark-theme` - Applied when dark mode enabled

## JavaScript Architecture

### Initialization
```javascript
jQuery(function($) {
  $('.cglp-latest-posts').each(function() {
    // Initialize pagination for each instance
  });
});
```

### Event Delegation
```javascript
$container.on('click', '.latest-pagination button[data-page]', handler);
$container.on('click', '.latest-pagination .prev', handler);
$container.on('click', '.latest-pagination .next', handler);
```

## Hooks (Future Enhancement)

### Planned Filters
```php
// Customize badge logic
add_filter('cglp_post_badge', 'custom_badge', 10, 2);

// Modify read time
add_filter('cglp_read_time', 'custom_read_time', 10, 2);

// Change query args
add_filter('cglp_query_args', 'modify_query', 10, 1);

// Customize excerpt length
add_filter('cglp_excerpt_length', 'custom_length', 10, 1);
```

### Planned Actions
```php
// Before grid renders
do_action('cglp_before_grid', $settings);

// After grid renders
do_action('cglp_after_grid', $settings);

// Before single card
do_action('cglp_before_card', $post_id);

// After single card
do_action('cglp_after_card', $post_id);
```

## Testing Checklist

### Functionality Tests
- [ ] Plugin activates without errors
- [ ] Settings save correctly
- [ ] Shortcode renders grid
- [ ] Posts display with correct data
- [ ] Pagination works correctly
- [ ] AJAX loads posts without errors
- [ ] Dark mode toggles properly
- [ ] Custom settings apply correctly

### Browser Tests
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

### Responsive Tests
- [ ] Desktop (1920px) - 3 columns
- [ ] Laptop (1366px) - 3 columns
- [ ] Tablet (768px) - 2 columns
- [ ] Mobile (375px) - 1 column
- [ ] Mobile landscape - Works correctly

### Edge Cases
- [ ] No posts (displays empty message)
- [ ] Single post (no pagination)
- [ ] Exactly 9 posts (no pagination)
- [ ] 10+ posts (pagination appears)
- [ ] Posts without featured images (placeholder shows)
- [ ] Posts without excerpts (auto-generated from content)
- [ ] Posts without categories (shows "Uncategorized")
- [ ] Very long titles (ellipsis or wraps correctly)
- [ ] Multiple shortcodes on same page

### Security Tests
- [ ] XSS prevention (all output escaped)
- [ ] CSRF protection (nonces on AJAX)
- [ ] SQL injection prevention (prepared statements)
- [ ] Directory traversal prevention
- [ ] Capability checks on admin pages

## Performance Considerations

### Optimizations
- Assets only loaded when shortcode present
- AJAX pagination prevents full page reloads
- Efficient WP_Query with proper args
- CSS/JS minification recommended for production
- Image lazy loading compatible

### Database Queries
- Single query per page load
- Uses standard WP post queries
- Leverages WP object cache
- No custom tables needed

## Debugging

### Enable WordPress Debug Mode
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true); // Use non-minified scripts
```

### Check Browser Console
- JavaScript errors appear in console
- Network tab shows AJAX requests/responses
- Elements tab shows applied CSS

### Common Issues

**Posts not loading:**
- Check `admin-ajax.php` is accessible
- Verify nonce is valid
- Check jQuery is loaded

**Styling not applied:**
- Clear cache (browser and WordPress)
- Check CSS file loads in Network tab
- Verify no theme CSS overrides

**Pagination broken:**
- Check console for JS errors
- Verify jQuery loaded before plugin script
- Check AJAX URL is correct

## Extending the Plugin

### Add Custom Badge Type
```php
// In your theme functions.php or custom plugin
add_filter('cglp_post_badge', function($badge, $post_id) {
    // Example: Add "TRENDING" badge for posts with many views
    $views = get_post_meta($post_id, 'post_views', true);
    if ($views > 1000) {
        return [
            'type' => 'trending',
            'label' => 'TRENDING'
        ];
    }
    return $badge;
}, 10, 2);

// Add CSS for new badge type
add_action('wp_head', function() {
    echo '<style>
    .card-badge.trending {
        background: #ff9500 !important;
    }
    </style>';
});
```

### Modify Query Arguments
```php
add_filter('cglp_query_args', function($args) {
    // Example: Only show posts from specific category
    $args['category_name'] = 'gaming';
    return $args;
}, 10, 1);
```

### Custom Template Override
```php
// In your theme, create:
// theme/cloud-gaming-posts/post-card.php
// Plugin will use theme version if it exists
```

## File Modification Guide

### Adding New Settings
1. Add field to `register_settings()` in main file
2. Add render function for field
3. Add to `sanitize_settings()`
4. Add to `get_default_settings()`
5. Use in templates

### Modifying Styles
- Edit `assets/css/frontend.css` for public styles
- Edit `assets/css/admin.css` for admin styles
- Increment CGLP_VERSION constant to bust cache

### Modifying JavaScript
- Edit `assets/js/frontend.js`
- Increment CGLP_VERSION constant to bust cache
- Test with SCRIPT_DEBUG enabled

## Internationalization

### Text Domain
`cloud-gaming-posts`

### Translatable Strings
All user-facing strings use:
```php
__('String', CGLP_TEXTDOMAIN)
esc_html__('String', CGLP_TEXTDOMAIN)
esc_attr__('String', CGLP_TEXTDOMAIN)
```

### Generate POT File
```bash
wp i18n make-pot . languages/cloud-gaming-posts.pot
```

## Version Control

### Semantic Versioning
- MAJOR.MINOR.PATCH (e.g., 2.7.0)
- MAJOR: Breaking changes
- MINOR: New features (backward compatible)
- PATCH: Bug fixes

### Changelog
Update in README.md and main plugin file header

## Deployment Checklist

- [ ] Update version numbers
- [ ] Update changelog
- [ ] Test on fresh WordPress install
- [ ] Test with popular themes
- [ ] Test with common plugins
- [ ] Validate HTML/CSS
- [ ] Check accessibility
- [ ] Review security
- [ ] Create plugin ZIP
- [ ] Test ZIP install process

## Support Resources

- WordPress Plugin Handbook: https://developer.wordpress.org/plugins/
- WordPress Coding Standards: https://developer.wordpress.org/coding-standards/
- WordPress REST API: https://developer.wordpress.org/rest-api/
- WP_Query Reference: https://developer.wordpress.org/reference/classes/wp_query/

## License

GPL v2 or later - Allows free use, modification, and distribution
