# Cloud Gaming Tracker

A WordPress plugin to track and display cloud gaming platform availability for your games.

## Version
3.1.0

## Features
- Track availability across 9+ cloud gaming platforms
- Modern card-based UI with glassmorphism effect
- Dark/Light mode toggle with auto-detection
- Fully responsive design
- Gutenberg block support
- Customizable colors and styling
- Platform and game management in admin panel

## Installation

1. Upload the `cloud-gaming-tracker` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to **Cloud Gaming** in the admin menu to configure

## Usage

### Shortcode
```
[cloud_gaming_tracker game_id="1"]
```

### Shortcode Parameters
- `game_id` - ID of the game to display platforms for
- `game_slug` - Alternative to game_id using slug
- `show_unavailable` - Show unavailable platforms (default: true)
- `group_by` - Group platforms by availability (default: availability)

### Gutenberg Block
Search for "Cloud Gaming Tracker" in the block editor.

## Changelog

### 3.1.0 (2024)
- **FIXED:** Game availability saving - properly validates game_id
- **FIXED:** Platform editing - checkboxes (game_included, is_active) now correctly maintain state
- **ADDED:** Game settings panel - per-game game_included toggle and active status
- **IMPROVED:** Reduced default border radius (card: 6px, button: 4px)
- **IMPROVED:** Better toggle handling in admin interface
- **IMPROVED:** Restructured plugin code for better organization and performance
- **FIXED:** Dark/Light theme toggle now works correctly with localStorage

### 3.0.0
- Initial release

## Structure

```
cloud-gaming-tracker/
├── cloud-gaming-tracker.php     # Main plugin file
├── includes/
│   ├── class-cgt-database.php    # Database operations
│   ├── class-cgt-frontend.php    # Frontend display
│   ├── class-cgt-admin.php       # Admin interface
│   └── class-cgt-main.php       # Main plugin class
├── assets/
│   ├── css/
│   │   ├── frontend.css          # Frontend styles
│   │   └── admin.css             # Admin styles
│   ├── js/
│   │   ├── frontend.js           # Frontend scripts
│   │   └── admin.js              # Admin scripts
│   └── images/
│       └── default-platform.svg  # Default icon
└── languages/                   # Translation files (ready)
```

## Database Tables

- `wp_cgt_platforms` - Cloud gaming platforms
- `wp_cgt_games` - Games
- `wp_cgt_game_platforms` - Game-platform availability mapping

## Requirements

- WordPress 5.8+
- PHP 7.4+

## License

GPL v2 or later

## Author

Cloud Loadout - https://cloudloadout.com
