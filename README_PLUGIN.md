# Cloud Gaming Availability WordPress Plugin

A fully responsive, production-ready WordPress plugin for displaying game availability across 9 cloud gaming platforms with customizable themes and responsive design.

## Features

### Supported Platforms (9)
- GeForce NOW
- Xbox Cloud Gaming
- PlayStation Plus Premium
- Amazon Luna
- Boosteroid
- Shadow PC
- Air GPU
- Blacknut
- CloudDeck

### Admin Features
- **Game Management**: Add, edit, and delete games from a dedicated admin interface
- **Per-Game Configuration**:
  - Game title and description
  - Cover image/thumbnail support
  - Individual platform availability selection
  - Edit and delete functionality
- **Settings Page**:
  - Dark/Light/Auto theme selector with persistent user preferences
  - Custom logo upload for each platform with fallback placeholders
  - Customization options:
    - Button colors and styles
    - Text colors for light and dark themes
    - Border radius, spacing, padding customization
    - Logo sizing options
    - Enable/disable platform filters

### Frontend Features
- **Responsive Shortcode**: `[cloud_gaming_availability game_id="123"]`
- **Display Elements**:
  - Game title
  - Game cover/thumbnail image
  - Platform logos in responsive grid/row layout
  - Availability status indicator
  - "Play Now" button for available platforms linking to platform websites
  - Fully responsive across desktop, tablet, and mobile devices
- **Shortcode Parameters**:
  - `theme="light|dark|auto"` - Override admin theme setting
  - `columns="2|3|4"` - Responsive grid configuration
  - `show_description="true|false"` - Show/hide game description

### Design Features
- Modern, clean UI with smooth animations
- Prominent platform logos with fallback gray placeholders
- Clear visual distinction between available/unavailable platforms
- Hover effects on buttons and interactive elements
- Smooth transitions and animations
- Mobile-first responsive design
- **Dark Theme**: Dark background with light text, adjusted contrast
- **Light Theme**: Light background with dark text, clean spacing
- Consistent spacing, typography, and color usage

### Technical Features
- Custom Post Type: `cloud_games`
- Custom Taxonomy: `cloud_platform`
- Admin metabox for platform availability selection
- WordPress Settings API integration
- Shortcode handler with parameter parsing
- CSS variables for theming
- Properly enqueued styles/scripts with dependencies
- AJAX handlers for dynamic platform filtering
- Optimized database queries
- Security: Input sanitization, nonce validation, output escaping
- Full code documentation and inline comments

## Installation

1. Download the plugin to your WordPress plugins directory (`/wp-content/plugins/`)
2. Activate the plugin from the WordPress admin panel
3. Navigate to "Cloud Games" in the admin menu to get started

## Usage

### Adding Games

1. Go to **Cloud Games** > **All Cloud Games**
2. Click **Add New**
3. Enter the game title
4. Upload a cover image (featured image)
5. Add game description in the editor
6. Select available platforms using the **Platform Availability** metabox
7. Publish the game

### Configuring Settings

1. Go to **Cloud Games** > **Settings**
2. Choose your default theme (Light, Dark, or Auto)
3. Customize colors:
   - Button color
   - Text colors for light and dark themes
4. Adjust design settings:
   - Border radius
   - Spacing
   - Padding
   - Logo size
   - Platform filters toggle
5. Upload custom platform logos (optional)
6. Save changes

### Using Shortcodes

Basic usage:
```
[cloud_gaming_availability game_id="123"]
```

With custom parameters:
```
[cloud_gaming_availability game_id="123" theme="dark" columns="4" show_description="true"]
```

Parameters:
- `game_id` (required) - The ID of the game post
- `theme` (optional) - "light", "dark", or "auto" (default: "auto")
- `columns` (optional) - 2, 3, or 4 columns (default: 3)
- `show_description` (optional) - "true" or "false" (default: "true")

## File Structure

```
cloud-gaming-availability/
├── cloud-gaming-availability.php     # Main plugin file
├── README_PLUGIN.md                  # Documentation
├── includes/
│   ├── class-cga-loader.php         # Scripts & styles loader
│   ├── class-cga-cpt.php            # Custom post type registration
│   └── class-cga-settings.php       # Settings management
├── admin/
│   └── class-cga-admin.php          # Admin functionality
├── frontend/
│   └── class-cga-shortcode.php      # Shortcode handler
└── assets/
    ├── css/
    │   ├── frontend.css              # Frontend styles
    │   └── admin.css                 # Admin styles
    └── js/
        ├── frontend.js               # Frontend scripts
        └── admin.js                  # Admin scripts
```

## Customization

### CSS Customization

The plugin uses CSS variables for easy customization:

```css
--cga-button-color: #007cba;
--cga-text-color: #000000;
--cga-border-radius: 4px;
--cga-spacing: 12px;
--cga-padding: 8px;
--cga-logo-size: 48px;
```

You can override these in your theme's stylesheet:

```css
.cga-container {
	--cga-button-color: #your-color;
	--cga-logo-size: 64px;
	/* etc. */
}
```

### Theme Customization

The plugin supports theme overrides in your child theme:
1. Create a `cloud-gaming-availability` directory in your theme
2. Add custom CSS files there to override default styles
3. Add custom templates for advanced customization

## Performance

- Optimized database queries with proper indexing
- CSS and JS files are properly enqueued with dependencies
- Lazy loading support for images
- Minimal DOM manipulation
- CSS animations use GPU acceleration

## Security

- All inputs are properly sanitized
- Outputs are escaped using appropriate WordPress functions
- Nonce verification on all admin forms
- Capability checks on all admin functions
- AJAX endpoints are secured with nonce verification

## Browser Support

- Chrome/Edge (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Accessibility

- WCAG 2.1 AA compliant
- Keyboard navigation support
- Focus management
- Semantic HTML structure
- Screen reader friendly
- ARIA labels where appropriate

## Troubleshooting

### Plugin not showing up in admin menu
- Ensure you have administrator capabilities
- Try deactivating and reactivating the plugin

### Games not displaying
- Verify the game post exists and is published
- Check that at least one platform is selected
- Verify shortcode syntax: `[cloud_gaming_availability game_id="123"]`

### Styling issues
- Clear browser cache (Ctrl+Shift+Delete)
- Disable caching plugins temporarily
- Check for CSS conflicts with your theme

## Support

For issues and feature requests, please refer to the GitHub repository.

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### Version 1.0.0
- Initial release
- Full admin interface for game management
- Responsive frontend with light/dark theme support
- Support for 9 cloud gaming platforms
- Customizable design settings
- Platform logo management
- Platform filtering functionality
