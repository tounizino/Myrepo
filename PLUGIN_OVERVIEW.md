# Cloud Games Availability v2 - Plugin Overview

A modern WordPress plugin for displaying cloud gaming platform availability with a premium card-based UI.

## Features Implemented

### Frontend Features
- ✅ Modern card-based UI (not tables)
- ✅ Glassmorphism design with blur effects
- ✅ Platform name + icon support (custom URL)
- ✅ Availability status (Available / Not Available)
- ✅ "Game included" or "Own game required" indicators
- ✅ Starting price display (monthly/yearly)
- ✅ Plan tier info
- ✅ CTA button with hover effects (Play Now, etc.)
- ✅ Disabled state for unavailable platforms
- ✅ Responsive, mobile-first design
- ✅ Light/Dark theme support (auto detection)
- ✅ Smooth hover states and transitions
- ✅ Shortcode support: `[cloud_games_availability game_id="1"]`
- ✅ Gutenberg block integration

### Admin Features
- ✅ Full admin panel with dashboard
- ✅ Add/edit platforms (name, icon, pricing, availability, CTA link)
- ✅ Toggle availability per game
- ✅ Manage multiple games
- ✅ Group platforms (available vs unavailable)
- ✅ Light/Dark theme toggle
- ✅ Global styling controls (colors, button style, radius, spacing)
- ✅ Custom icon via URL
- ✅ Enable/disable UI elements (price, tier, notes)
- ✅ Display order control for platforms
- ✅ Status management (active/inactive)

### Technical Features
- ✅ Clean, modular code structure
- ✅ No heavy dependencies (vanilla JS)
- ✅ Fast loading and SEO-friendly
- ✅ Compatible with Gutenberg
- ✅ REST API endpoints
- ✅ Custom database tables
- ✅ AJAX-powered admin interface
- ✅ CSS variables for easy theming

## File Structure

```
cloud-games-availability-v2.php       # Main plugin file
├── includes/
│   ├── class-cga-activator.php      # Plugin activation
│   ├── class-cga-deactivator.php    # Plugin deactivation
│   ├── class-cga-database.php       # Database operations
│   ├── class-cga-admin.php          # Admin AJAX handlers
│   ├── class-cga-frontend.php       # Frontend rendering
│   ├── class-cga-rest-api.php       # REST API endpoints
│   └── class-cga-settings.php      # Settings management
├── templates/
│   ├── admin-dashboard.php          # Dashboard template
│   ├── admin-games.php              # Games management
│   ├── admin-platforms.php          # Platforms management
│   └── admin-settings.php           # Settings panel
├── assets/
│   ├── css/
│   │   ├── admin.css                # Admin styles
│   │   ├── frontend.css            # Frontend styles
│   │   └── block.css               # Block editor styles
│   └── js/
│       ├── admin.js                 # Admin JavaScript
│       ├── frontend.js             # Frontend JavaScript
│       └── block.js                # Gutenberg block
└── readme.txt                       # WordPress readme

```

## Database Tables

1. `wp_cga_games` - Games information
2. `wp_cga_platforms` - Platform information
3. `wp_cga_availability` - Junction table for game-platform relationships

## Default Platforms Included

- GeForce NOW
- Xbox Cloud Gaming
- PlayStation Cloud
- Amazon Luna
- Boosteroid
- Shadow PC
- Antstream Arcade
- GeForce NOW (Free tier)

## Customization Options

- Primary/Secondary colors
- Card background (light/dark)
- Button style (filled, outline, ghost)
- Border radius
- Card spacing
- Hover effects (lift, scale, glow, none)
- Theme mode (light, dark, auto)
- Glassmorphism toggle
- Show/hide: Price, Tier, Notes
- Custom CSS support

## Shortcode Usage

Basic:
```
[cloud_games_availability game_id="1"]
```

Advanced:
```
[cloud_games_availability game_id="1" group_by_availability="yes" columns="4"]
```

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate through WordPress admin
3. Configure settings
4. Add platforms and games
5. Use shortcode or Gutenberg block to display

## Code Quality

- Follows WordPress coding standards
- Uses WordPress best practices
- Proper security sanitization
- AJAX nonce verification
- Database query prepared statements
- Responsive design principles
- Accessibility considerations
