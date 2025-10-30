# Cloud Layout Builder Pro

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)](https://php.net/)

A powerful, modern WordPress plugin for building stunning homepage layouts with drag-and-drop blocks. Perfect for blogs, cloud gaming websites, and content-rich sites.

## ✨ Features

### 🧱 9 Pre-Designed Content Blocks

- **Latest Articles Grid** - Responsive 1-4 column grid with pagination & infinite scroll
- **Featured Hero** - Large, eye-catching hero section for featured content
- **Category Highlight** - Showcase posts from specific categories
- **Tag/Topic Block** - Display posts by tag or topic
- **Content Carousel** - Auto-rotating slider with smooth transitions
- **Mixed Content** - Combine articles, banners, and YouTube embeds
- **Custom Links** - Resource links with icons and descriptions
- **Newsletter Signup** - Email capture form (Mailchimp/Brevo compatible)
- **Quote/Tip Block** - Highlight insights and gaming tips

### 🧩 6 Custom Widgets

- Latest Posts (with thumbnails)
- Category List (with icon badges)
- Editor's Picks
- Mini Search Bar
- Tag Cloud
- Custom HTML/CTA

### 🎨 Global Customization

- Full color palette control
- Typography settings (font family, sizes)
- Spacing & padding controls
- Border radius customization
- 3 layout styles: Classic Grid, Magazine, Modern Blog
- Light/Dark mode toggle
- Custom CSS & JS editors

### 🚀 SEO & Performance

- Schema.org markup (Blog, BlogPosting, Article)
- Lazy-loading images
- Lightweight code (<50KB total)
- Mobile-first responsive design
- Cache-friendly architecture
- Semantic HTML structure

### 🛠️ Developer Features

- Gutenberg/Block Editor compatible
- Shortcode support for all blocks
- Drag-and-drop block ordering
- Device visibility controls (Desktop/Tablet/Mobile)
- Import/Export settings (JSON)
- WCAG 2.1 accessibility compliant
- Translation-ready (gettext)
- WordPress coding standards compliant

## 📦 Installation

### Automatic Installation

1. Go to **Plugins → Add New** in WordPress
2. Search for "Cloud Layout Builder Pro"
3. Click **Install Now**, then **Activate**

### Manual Installation

1. Download the plugin ZIP file
2. Go to **Plugins → Add New → Upload Plugin**
3. Choose the ZIP file and click **Install Now**
4. Click **Activate Plugin**

### From Source

```bash
cd wp-content/plugins/
git clone https://github.com/yourusername/cloud-layout-builder-pro.git
cd cloud-layout-builder-pro
```

Then activate from WordPress admin.

## 🎯 Quick Start

### 1. Build Your Homepage

1. Go to **Layout Builder → Homepage Builder**
2. Drag and drop blocks to create your layout
3. Click ⚙️ on each block to configure
4. Toggle visibility per device if needed
5. Click **Save Layout**

### 2. Add to Your Page

Add this shortcode to any page:

```
[clbp_homepage]
```

### 3. Customize Design

1. Go to **Layout Builder → Settings**
2. Customize colors, typography, and spacing
3. Click **Save Settings**

## 📚 Shortcodes

### Main Homepage

```
[clbp_homepage]
```

### Individual Blocks

```
[clbp_latest_articles postsToShow="9" columns="3" layout="grid"]
[clbp_featured_hero]
[clbp_category_highlight category="1" items="6"]
[clbp_tag_topic tag="5" items="6"]
[clbp_carousel itemCount="6" autoplay="true"]
[clbp_mixed_content]
[clbp_custom_links]
[clbp_newsletter]
[clbp_quote_tip quote="Your quote here" author="Author Name"]
```

## 🔧 Customization

### Colors

Default palette (fully customizable):

- **Primary:** `#1E88E5` (Blue)
- **Secondary:** `#1565C0`
- **Background:** `#F9FAFB`
- **Text:** `#1F2937`

### Filters & Hooks

#### Modify Block Output

```php
add_filter( 'clbp_block_output', function( $html, $block_type, $attributes ) {
    // Customize block HTML
    return $html;
}, 10, 3 );
```

#### Track Block Views

```php
add_action( 'clbp_track_block_view', function( $block_slug ) {
    // Custom analytics tracking
}, 10, 1 );
```

#### Modify Query Args

```php
add_filter( 'clbp_query_args', function( $args, $block_type ) {
    // Customize WP_Query args
    return $args;
}, 10, 2 );
```

## 🏗️ Project Structure

```
cloud-layout-builder-pro/
├── assets/
│   ├── css/
│   │   ├── frontend.css      # Frontend styles
│   │   ├── admin.css          # Admin panel styles
│   │   └── editor.css         # Block editor styles
│   └── js/
│       ├── frontend.js        # Frontend scripts
│       ├── admin.js           # Admin scripts
│       ├── blocks.js          # Gutenberg blocks
│       └── customizer-preview.js
├── includes/
│   ├── admin/                 # Admin panel classes
│   ├── blocks/                # Block classes
│   ├── frontend/              # Frontend classes
│   ├── utils/                 # Helper classes
│   └── widgets/               # Widget classes
├── templates/
│   └── admin/                 # Admin templates
├── cloud-layout-builder-pro.php  # Main plugin file
├── readme.txt                 # WordPress.org readme
└── README.md                  # This file
```

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Commit your changes: `git commit -m 'Add amazing feature'`
4. Push to the branch: `git push origin feature/amazing-feature`
5. Open a Pull Request

### Code Standards

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- Use PHPDoc comments for all functions
- Write semantic, accessible HTML
- Keep CSS flat and modern (no shadows/glowing by default)
- Test on latest WordPress version

## 🐛 Bug Reports

Found a bug? Please [open an issue](https://github.com/yourusername/cloud-layout-builder-pro/issues) with:

- WordPress version
- PHP version
- Theme name
- Steps to reproduce
- Expected vs actual behavior

## 📝 Changelog

### 1.0.0 (2024-12-XX)

**Initial Release**

- 9 content blocks
- 6 custom widgets
- Global customization panel
- Import/Export functionality
- Schema.org markup
- Full responsive design
- Translation-ready
- WCAG 2.1 compliant

## 📄 License

This plugin is licensed under the GNU General Public License v2.0 or later.

See [LICENSE](LICENSE) for the full license text.

## 💬 Support

- **Documentation:** [Plugin website](https://cloudlayoutbuilder.pro)
- **WordPress Forum:** [Support forum](https://wordpress.org/support/plugin/cloud-layout-builder-pro)
- **Email:** support@cloudlayoutbuilder.pro

## 🙏 Credits

Built with ❤️ for the WordPress community.

- Icons: Built-in WordPress Dashicons
- Font: Inter (system fallback)
- Inspired by modern 2026 web design trends

## 🌟 Show Your Support

If you find this plugin useful, please:

- ⭐ Star this repository
- ✍️ Write a review on [WordPress.org](https://wordpress.org/plugins/cloud-layout-builder-pro)
- 💬 Share it with your network
- ☕ [Buy me a coffee](https://buymeacoffee.com/yourname)

---

**Made for cloud gaming blogs, perfect for any niche.**
