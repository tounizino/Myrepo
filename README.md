# Cloud Gaming Latest Posts Grid - WordPress Plugin

A professional WordPress plugin that automatically displays your latest blog posts in a beautiful, cloud gaming-inspired grid layout with automatic pagination, dark mode support, and fully customizable styling.

## 🎮 Features

- ✅ **Automatic Post Loading**: Dynamically fetches and displays latest WordPress posts
- ✅ **Smart AJAX Pagination**: Seamless page navigation without full page reloads
- ✅ **Dark Mode Support**: Toggle between light and dark themes from admin panel
- ✅ **Fully Responsive**: Optimized for desktop (3 columns), tablet (2 columns), and mobile (1 column)
- ✅ **Dynamic Badges**: Auto-assigns NEW, HOT, or UPDATED badges based on post data
- ✅ **Read Time Calculation**: Automatically calculates estimated reading time
- ✅ **Customizable Layout**: Adjust container width, margins, and padding
- ✅ **Featured Images**: Displays post thumbnails with smooth hover effects
- ✅ **SEO Friendly**: Clean, semantic HTML5 markup

## 📦 Installation

### Quick Install

1. Upload the `cloud-gaming-latest-posts` folder to `/wp-content/plugins/`
2. Activate the plugin through WordPress admin → Plugins
3. Configure settings at Settings → Cloud Gaming Latest Posts
4. Add `[cloud_gaming_posts]` shortcode to any page or post

**For detailed installation instructions, see [INSTALL.md](cloud-gaming-latest-posts/INSTALL.md)**

## 🚀 Quick Start

### Basic Usage
```
[cloud_gaming_posts]
```

### Custom Posts Per Page
```
[cloud_gaming_posts posts_per_page="12"]
```

### In PHP Templates
```php
<?php echo do_shortcode('[cloud_gaming_posts]'); ?>
```

## ⚙️ Configuration

Navigate to **Settings → Cloud Gaming Latest Posts** to configure:

- **Enable Dark Theme** - Toggle dark mode appearance
- **Container Max Width** - Set maximum width (default: 1400px)
- **Container Margin** - Control outer spacing (default: 0 auto 40px auto)
- **Container Padding** - Adjust inner padding (default: 0)
- **Posts Per Page** - Number of posts to display (default: 9)

## 📱 Responsive Design

The grid automatically adapts to screen size:

- **Desktop**: 3 columns
- **Tablet (≤992px)**: 2 columns
- **Mobile (≤600px)**: 1 column

## 🎨 Styling

### Light Theme (Default)
Clean white cards with blue accents and subtle shadows

### Dark Theme
Dark gray cards optimized for dark backgrounds with enhanced contrast

### Badge Colors
- **NEW** (Blue) - Posts published within 14 days
- **HOT** (Red) - Posts with 5+ comments
- **UPDATED** (Green) - Recently modified posts

## 📚 Documentation

- **[PLUGIN-SUMMARY.md](PLUGIN-SUMMARY.md)** - Complete feature overview and technical details
- **[INSTALL.md](cloud-gaming-latest-posts/INSTALL.md)** - Comprehensive installation guide
- **[USAGE-EXAMPLES.md](cloud-gaming-latest-posts/USAGE-EXAMPLES.md)** - Integration examples and use cases
- **[README.md](cloud-gaming-latest-posts/README.md)** - Plugin-specific documentation

## 🔧 Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher (8.0+ recommended)
- MySQL 5.6+ or MariaDB 10.1+
- jQuery (bundled with WordPress)

## 🌐 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 📂 Plugin Structure

```
cloud-gaming-latest-posts/
├── cloud-gaming-latest-posts.php    # Main plugin file
├── includes/
│   ├── admin-page.php               # Admin settings
│   ├── posts-grid.php               # Grid container
│   └── post-card.php                # Post card template
├── assets/
│   ├── css/
│   │   ├── frontend.css             # Frontend styles
│   │   └── admin.css                # Admin styles
│   ├── js/
│   │   └── frontend.js              # AJAX pagination
│   └── images/
│       └── placeholder.svg          # Fallback image
├── README.md                        # Plugin docs
├── INSTALL.md                       # Installation guide
└── USAGE-EXAMPLES.md                # Usage examples
```

## 🎯 Key Features Explained

### Automatic Badge Assignment
- Posts ≤14 days old get **NEW** badge
- Posts with 5+ comments get **HOT** badge
- Recently updated posts get **UPDATED** badge

### Read Time Calculation
Automatically calculates based on word count (200 words/minute average)

### AJAX Pagination
Smooth page transitions without full page reloads, with scroll-to-top functionality

## 🛠️ Customization

Add custom CSS to **Appearance → Customize → Additional CSS**:

```css
/* Change primary color */
.gaming-latest-container.v27 .card-badge.new {
  background: #ff6b00 !important;
}

/* Adjust card spacing */
.gaming-latest-container.v27 .latest-grid {
  gap: 30px !important;
}

/* Custom hover effect */
.gaming-latest-container.v27 .latest-card:hover {
  transform: translateY(-8px) !important;
}
```

## 🐛 Troubleshooting

**Posts not displaying?**
- Verify you have published posts (not drafts)
- Check shortcode spelling: `[cloud_gaming_posts]`
- Clear cache if using caching plugins

**Pagination not working?**
- Ensure jQuery is loaded
- Check browser console for errors
- Verify admin-ajax.php is accessible

**Styling issues?**
- Clear browser cache
- Check for theme CSS conflicts
- Verify plugin CSS is loading

## 📝 License

GPL v2 or later

## 💬 Support

For issues, questions, or contributions, please visit [https://cloudloadout.com](https://cloudloadout.com)

## ✨ What Makes This Plugin Special

1. **Zero Configuration** - Works perfectly out of the box
2. **Authentic Design** - Maintains exact v2.7 cloud gaming aesthetic
3. **Performance** - Lightweight and optimized
4. **Flexibility** - Highly customizable without code
5. **Quality** - Professional-grade WordPress standards
6. **UX** - Smooth interactions with AJAX
7. **Accessibility** - Semantic HTML markup

---

**Ready to transform your WordPress posts into a stunning gaming-inspired grid!** 🎮
