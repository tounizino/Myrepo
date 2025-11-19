# Sections AutoPosts

A professional WordPress plugin for automatically populating stunning post layout sections similar to PostX. Perfect for cloud gaming blogs, magazines, and content-rich websites.

## Features

✅ **9 Premium Section Layouts:**
1. Featured Posts + Widget (hero + sidebar)
2. Beginner's Corner (step-by-step cards)
3. Community Top Picks (highlight + list)
4. Featured Games (hero + grid)
5. Latest News (timeline layout)
6. Latest Posts Grid (paginated grid)
7. Platform Comparison (review cards with ratings)
8. Pro Guides (card grid with difficulty badges)
9. Troubleshooting Hub (icon-based solutions)

✅ **Powerful Query Engine:**
- Latest published
- Latest updated
- Most popular (by comments)
- Filter by category
- Filter by tags
- Custom post IDs

✅ **Full Customization:**
- Light & dark themes for each section
- Customize colors (accent, secondary, background)
- Adjust padding, margin, and container width
- Custom CSS classes support

✅ **Smart Badges:**
- Auto "NEW" badges (configurable days)
- Auto "UPDATED" badges (configurable days)
- Auto "HOT" badges (based on comments)
- Manual badges via post meta

✅ **Advanced Features:**
- Automatic pagination for grid layouts
- Reading time calculation
- Primary category detection (Yoast compatible)
- Responsive design (desktop, tablet, mobile)
- Custom meta field support (ratings, difficulty, features)
- Image fallback with elegant placeholders

## Installation

1. Upload `sections-autoposts-plugin` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **Sections AutoPosts** in admin sidebar
4. Configure each section and copy shortcodes
5. Insert shortcodes in pages/posts or use theme hooks

## Usage

### Shortcodes

**Display single section:**
```
[sap_section type="featured"]
[sap_section type="beginners"]
[sap_section type="community"]
[sap_section type="featured_games"]
[sap_section type="latest_news"]
[sap_section type="latest_posts"]
[sap_section type="platforms"]
[sap_section type="pro_guides"]
[sap_section type="troubleshooting"]
```

**Display all enabled sections:**
```
[sap_all_sections]
```

### Configuration

Each section has its own admin panel with:

**General Settings:**
- Enable/disable
- Display order (for [sap_all_sections])
- Section title & subtitle
- Secondary title (for sidebars/widgets)

**Post Query:**
- Data source (latest, updated, popular, category, tag, custom)
- Order by (date, modified, comments, title, random)
- Order direction (DESC/ASC)
- Number of posts
- Specific categories/tags

**Styling:**
- Theme variant (light/dark)
- Accent colors
- Background color
- Padding & margin
- Container max-width
- Custom CSS classes

**Layout Options:**
- Excerpt lengths
- Items per page (pagination)
- Grid columns (responsive)
- Badge thresholds

**Advanced:**
- Rating meta key (for platform comparisons)
- Features meta key (comma/newline separated)
- Difficulty meta key (for pro guides)
- Custom icon list (for troubleshooting)

## Custom Meta Fields (Optional)

### Platform Ratings
```php
update_post_meta($post_id, 'sap_rating', '9.2');
// Or: update_post_meta($post_id, 'sap_rating', '9.2/10');
```

### Platform Features
```php
$features = "RTX 4080 rigs available\n1600+ games supported\nBring your own games";
update_post_meta($post_id, 'sap_features', $features);
```

### Pro Guide Difficulty
```php
update_post_meta($post_id, 'sap_difficulty', 'EXPERT');
// Values: BEGINNER, PRO, EXPERT, ADVANCED
```

## Filters & Hooks

### Modify query arguments
```php
add_filter('sap/query_args', function($args, $settings, $options) {
    // Customize WP_Query args
    return $args;
}, 10, 3);
```

## Dark Theme Support

Every section automatically adjusts for dark theme:
- Inverted card backgrounds
- Adjusted text colors
- Opacity-adjusted borders
- Accessible contrast ratios

## Browser Support

- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)

## Requirements

- WordPress 5.0+
- PHP 7.2+
- jQuery (included in WordPress core)

## Support

For issues, feature requests, or contributions, visit:
https://cloudloadout.com/support

## Credits

Created by CloudLoadout Team
Icons: System emoji
Fonts: Inter (system fallback)

## License

GPL v2 or later

## Changelog

### 1.0.0
- Initial release
- 9 premium section layouts
- Full theme customization
- Auto-pagination support
- Smart badge system
- Responsive design
