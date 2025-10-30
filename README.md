# Ultimate Blocks for Cloud Gaming

A comprehensive WordPress plugin designed specifically for cloud gaming blogs and websites. Features multiple custom Gutenberg blocks, widgets, and extensive customization options.

## Features

### 🎮 Cloud Gaming Optimized
- Designed specifically for cloud gaming blogs and news sites
- Modern, responsive design with blue color palette
- SEO-friendly markup and semantic HTML
- Mobile-first approach for 2026+ standards

### 📦 10+ Custom Blocks

1. **Latest Posts Grid** - Display articles in a responsive 3x3 grid with pagination
2. **Featured Posts** - Showcase featured content prominently
3. **Category Showcase** - Display categories with counts and descriptions
4. **Tag Cloud Gaming** - Interactive tag cloud with post counts
5. **Gaming Hero** - Eye-catching hero section for homepage
6. **Review Card** - Display game reviews with star ratings
7. **Game Specs** - Show technical specifications for games
8. **Streaming Platforms** - Display available cloud gaming platforms
9. **Performance Stats** - Show performance metrics and statistics
10. **Newsletter Signup** - Collect email subscriptions from visitors

### 🎨 Customization Options

- **Color Settings**: Customize primary, secondary, accent, and text colors
- **Display Options**: Control posts per page, image sizes, excerpt length
- **SEO Optimization**: Built-in Schema.org markup
- **Responsive Design**: Mobile-friendly on all devices
- **No Shadows/Glowing**: Clean, flat design aesthetic

### 🔧 Widgets

- **Latest Posts Widget** - Sidebar widget for recent posts
- **Featured Posts Widget** - Highlight featured content in sidebars
- **Category List Widget** - Display category lists with counts

### ⚙️ Advanced Features

- **Filter by Category/Tag** - Display posts by specific categories or tags
- **Multiple Layout Options** - Grid, horizontal, and custom layouts
- **Adjustable Block Width** - Full width, wide, and default alignments
- **Pagination Support** - Built-in pagination for post grids
- **AJAX-powered** - Smooth interactions without page reloads
- **Lazy Loading** - Optimized image loading for performance
- **Accessibility Ready** - WCAG compliant markup

## Installation

1. Upload the `ultimate-blocks-cloud-gaming` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'UBCG Settings' in the WordPress admin to configure colors and options
4. Start using blocks in the Gutenberg editor!

## Usage

### Adding Blocks

1. Open the WordPress block editor
2. Click the '+' button to add a new block
3. Search for "UBCG" or the specific block name
4. Configure block settings in the right sidebar
5. Publish your page/post

### Configuring Settings

1. Navigate to **UBCG Settings** in the WordPress admin menu
2. Customize colors to match your brand
3. Set default display options
4. Enable/disable SEO features
5. Save settings

### Using Widgets

1. Go to **Appearance > Widgets**
2. Find UBCG widgets in the available widgets list
3. Drag to your desired widget area
4. Configure widget settings
5. Save

## Block Settings

### Latest Posts Grid

- Posts per page (1-50)
- Columns (1-4)
- Show/hide: Image, Date, Excerpt, Author, Category
- Order by: Date, Title, Random
- Enable/disable pagination

### Featured Posts

- Number of posts (1-10)
- Layout: Horizontal or Grid
- Show/hide: Image, Date, Excerpt

### Category Showcase

- Columns (1-4)
- Show/hide: Post count, Description

### Gaming Hero

- Custom title and subtitle
- Background image support
- Call-to-action button
- Button text and link customization

## Customization

### Color Palette

Default blue theme colors:
- **Primary**: #2563eb (Blue 600)
- **Secondary**: #1e40af (Blue 800)
- **Accent**: #60a5fa (Blue 400)
- **Text**: #1e293b (Slate 800)

All colors can be customized in the settings panel.

### CSS Customization

Add custom CSS through:
1. WordPress Customizer > Additional CSS
2. Your theme's style.css
3. A child theme

Target blocks with `.ubcg-*` classes:
```css
.ubcg-post-card {
    /* Your custom styles */
}
```

### PHP Customization

Use WordPress filters to customize block output:
```php
add_filter('ubcg_posts_per_page', function($default) {
    return 12; // Change default posts per page
});
```

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- Modern browser with JavaScript enabled

## Browser Support

- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance

- Optimized CSS and JavaScript
- Lazy loading images
- Minimal HTTP requests
- Efficient database queries
- Caching-friendly markup

## SEO Features

- Semantic HTML5 markup
- Schema.org structured data
- Proper heading hierarchy
- Alt text support for images
- Fast loading times
- Mobile-friendly design

## Accessibility

- WCAG 2.1 AA compliant
- Keyboard navigation support
- Screen reader friendly
- Proper ARIA labels
- Focus indicators

## Support

For support, feature requests, or bug reports:
- Email: support@example.com
- Documentation: [Your documentation URL]
- GitHub: [Your GitHub repo URL]

## Changelog

### Version 1.0.0
- Initial release
- 10+ custom Gutenberg blocks
- 3 custom widgets
- Full customization panel
- Responsive design
- SEO optimization
- Accessibility features

## Credits

Developed for cloud gaming enthusiasts by passionate developers.

## License

GPL v2 or later - http://www.gnu.org/licenses/gpl-2.0.html

---

**Made with ❤️ for the Cloud Gaming Community**
