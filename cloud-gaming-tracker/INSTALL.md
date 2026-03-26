# Cloud Gaming Tracker - Installation Guide

## Quick Start

1. **Upload to WordPress**
   - Upload the entire `cloud-gaming-tracker` folder to:
     ```
     /wp-content/plugins/cloud-gaming-tracker/
     ```

2. **Activate Plugin**
   - Log in to WordPress Admin
   - Go to **Plugins** → **Installed Plugins**
   - Find "Cloud Gaming Tracker" and click **Activate**

3. **Access Admin Panel**
   - Navigate to **Cloud Gaming** in the WordPress admin menu
   - You'll find:
     - **Dashboard** - Overview and quick stats
     - **Platforms** - Manage cloud gaming platforms
     - **Games** - Add games and set platform availability
     - **Settings** - Customize appearance and display options

## First-Time Setup

1. **Platforms** (Already Configured)
   - 9 popular cloud gaming platforms are pre-configured
   - You can add, edit, or remove platforms as needed

2. **Add Your First Game**
   - Go to **Games** page
   - Enter game name and click "Add Game"
   - Set platform availability for each platform
   - Click "Save Availability"
   - Configure game settings (included vs required, active status)

3. **Display on Your Site**
   - Use shortcode: `[cloud_gaming_tracker game_id="1"]`
   - Or use the Gutenberg block: "Cloud Gaming Tracker"

## Plugin Structure

```
cloud-gaming-tracker/
├── cloud-gaming-tracker.php          # Main plugin file (load this in WordPress)
├── includes/
│   ├── class-cgt-database.php        # Database table operations
│   ├── class-cgt-frontend.php        # Frontend display logic
│   ├── class-cgt-admin.php           # Admin interface and AJAX
│   └── class-cgt-main.php            # Main plugin class
├── assets/
│   ├── css/
│   │   ├── frontend.css              # Frontend styles
│   │   └── admin.css                 # Admin panel styles
│   ├── js/
│   │   ├── frontend.js               # Frontend JavaScript
│   │   └── admin.js                 # Admin panel JavaScript
│   └── images/
│       └── default-platform.svg       # Default platform icon
└── languages/                       # Translation files (ready)
```

## Database Tables Created

The plugin creates 3 tables in your WordPress database:

1. `wp_cgt_platforms` - Stores cloud gaming platform information
2. `wp_cgt_games` - Stores game information
3. `wp_cgt_game_platforms` - Maps games to platforms with availability

## Compatibility

- **WordPress:** 5.8 or higher
- **PHP:** 7.4 or higher
- **Browser:** Modern browsers with CSS Grid and Flexbox support

## Support

For issues, questions, or feature requests, visit:
https://cloudloadout.com/cloud-gaming-tracker

## License

GPL v2 or later
