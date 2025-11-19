# Cloud Gaming Latest Posts Grid v2.7

A beautiful WordPress plugin that displays your latest posts in a cloud gaming-inspired grid layout with automatic pagination, dark mode support, and customizable styling.

## Features

- **Automatic Post Display**: Dynamically fetches and displays your latest WordPress posts
- **Smart Pagination**: AJAX-based pagination for seamless browsing without page reloads
- **Dark Mode Support**: Toggle between light and dark themes from the admin panel
- **Fully Responsive**: Optimized for desktop (3 columns), tablet (2 columns), and mobile (1 column)
- **Dynamic Badges**: Automatically assigns NEW, HOT, or UPDATED badges based on post age, comments, and modification date
- **Read Time Calculation**: Automatically calculates estimated reading time
- **Customizable Layout**: Adjust container width, margins, and padding from the admin panel
- **Featured Images**: Displays post featured images with smooth hover effects
- **Category Labels**: Shows the primary category for each post
- **SEO Friendly**: Clean, semantic HTML markup

## Installation

1. Upload the `cloud-gaming-latest-posts` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings → Cloud Gaming Latest Posts to configure options
4. Add the shortcode `[cloud_gaming_posts]` to any page or post

## Usage

### Basic Shortcode

```
[cloud_gaming_posts]
```

### Custom Posts Per Page

```
[cloud_gaming_posts posts_per_page="12"]
```

## Admin Settings

Access the settings page at **Settings → Cloud Gaming Latest Posts** to configure:

- **Enable Dark Theme**: Toggle dark mode appearance
- **Container Max Width**: Set maximum width (e.g., 1400px, 100%)
- **Container Margin**: Control outer spacing (e.g., 0 auto 40px auto)
- **Container Padding**: Adjust inner padding (e.g., 0 or 0 16px)
- **Posts Per Page**: Number of posts to display per page (default: 9)

## Badge Logic

The plugin automatically assigns badges to posts:

- **NEW**: Posts published within the last 14 days
- **UPDATED**: Posts modified more than 1 day after publication and within the last 30 days
- **HOT**: Posts with 5 or more comments

## Styling

The plugin maintains the exact styling from the Cloud Gaming v2.7 design:

- Clean, minimal card design
- Smooth hover animations
- Responsive grid layout
- Professional typography using Inter font
- Optimized spacing and proportions

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- jQuery (bundled with WordPress)

## Developer Notes

### Filters & Hooks

The plugin is designed to be extensible. Key functions are public static methods that can be accessed:

- `Cloud_Gaming_Latest_Posts::calculate_read_time($post_id)` - Calculate reading time
- `Cloud_Gaming_Latest_Posts::get_post_badge($post_id)` - Get badge information

### File Structure

```
cloud-gaming-latest-posts/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   ├── js/
│   │   └── frontend.js
│   └── images/
│       └── placeholder.svg
├── includes/
│   ├── admin-page.php
│   ├── post-card.php
│   └── posts-grid.php
├── cloud-gaming-latest-posts.php
└── README.md
```

## License

GPL v2 or later

## Support

For issues, feature requests, or contributions, please visit [https://cloudloadout.com](https://cloudloadout.com)

## Changelog

### Version 2.7.0
- Initial release
- Dynamic post loading with AJAX pagination
- Dark mode support
- Customizable layout options
- Automatic badge assignment
- Read time calculation
- Responsive design
- Featured image support
