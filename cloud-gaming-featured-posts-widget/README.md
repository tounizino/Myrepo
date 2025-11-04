# Cloud Gaming Featured Posts Widget

A professional, responsive featured posts widget designed specifically for cloud gaming websites with four beautiful blue-themed variations. Built with WordPress best practices, accessibility standards, and modern design principles.

## 🎮 Features

- **4 Theme Variations**: Sky Blue, Blue, Dark, Light
- **Fully Responsive**: Adapts seamlessly to all screen sizes
- **WordPress Native**: Built as a standard WordPress widget
- **Clean Design**: No shadows, no glowing effects - just professional aesthetics
- **Performance Optimized**: Lightweight CSS, efficient queries
- **Accessibility Focused**: ARIA labels, semantic HTML
- **Reading Time Estimation**: Automatically calculates reading time
- **Category Badges**: Displays primary category for each post
- **Thumbnail Support**: Graceful fallback for posts without images

## 📦 Installation

### As a Plugin

1. Upload the `cloud-gaming-featured-posts-widget` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Appearance → Widgets
4. Drag "Cloud Gaming Featured Posts" to your desired widget area

### As Theme Code

Copy the contents of `cloud-gaming-featured-posts-widget.php` to your theme's `functions.php` and adjust the asset paths accordingly.

## 🎨 Theme Variations

### 1. Sky Blue Theme
- Primary: #0EA5E9 (Bright sky blue)
- Background: #F0F9FF (Very light blue)
- Perfect for: Modern, energetic gaming brands

### 2. Blue Theme
- Primary: #2563EB (Classic blue)
- Background: #EFF6FF (Soft blue background)
- Perfect for: Traditional, trustworthy gaming platforms

### 3. Dark Theme
- Primary: #60A5FA (Bright blue on dark)
- Background: #0F172A (Deep navy)
- Perfect for: Premium gaming experiences, night mode

### 4. Light Theme
- Primary: #1E40AF (Deep blue)
- Background: #FFFFFF (Pure white)
- Perfect for: Clean, minimalist gaming sites

## 🛠️ Widget Settings

- **Title**: Customize the widget header text
- **Theme**: Choose from 4 theme variations
- **Number of Posts**: Display 1-6 posts (default: 3)

## 📱 Responsive Breakpoints

- Mobile: Single column layout
- Tablet (640px+): 2 column grid
- Desktop (1024px+): 3 column grid

## 🎯 Usage in Code

### Display Widget in Template

```php
<?php
if ( is_active_sidebar( 'your-sidebar-id' ) ) {
    dynamic_sidebar( 'your-sidebar-id' );
}
?>
```

### Programmatic Display

```php
<?php
the_widget(
    'Cloud_Gaming_Featured_Posts_Widget',
    array(
        'title' => 'Featured Gaming News',
        'theme' => 'dark',
        'posts_count' => 3
    )
);
?>
```

## 🎨 Customization

### Color Customization

All theme colors are defined as CSS custom properties in `assets/css/featured-posts-widget.css`:

```css
:root {
  --cgw-sky-primary: #0EA5E9;
  --cgw-sky-secondary: #38BDF8;
  /* ... more variables */
}
```

### Adding Custom Themes

1. Add your theme to the `$themes` array in the `form()` method
2. Add corresponding CSS custom properties
3. Add theme-specific CSS rules following the existing pattern

## 🌐 Browser Support

- Chrome/Edge (last 2 versions)
- Firefox (last 2 versions)
- Safari (last 2 versions)
- iOS Safari (last 2 versions)
- Android Chrome (last 2 versions)

## ♿ Accessibility

- Semantic HTML5 elements
- ARIA labels where appropriate
- Keyboard navigation support
- Focus states on interactive elements
- Sufficient color contrast ratios

## 📄 License

This widget is provided as-is for cloud gaming website projects.

## 🤝 Support

For issues or feature requests, please contact the development team.

## 📝 Changelog

### Version 1.0.0
- Initial release
- 4 theme variations
- Responsive grid layout
- Reading time estimation
- Category display
- Thumbnail support with fallback
