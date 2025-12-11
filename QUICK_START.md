# Platforms for Cloud Games - Quick Start Guide

## Installation & Activation

1. **Upload Plugin**
   - Download or upload the plugin folder to `/wp-content/plugins/`
   - Name should be `cloud-gaming-availability`

2. **Activate**
   - Go to WordPress Admin → Plugins
   - Find "Platforms for Cloud Games"
   - Click "Activate"

3. **Verify**
   - Check that "Cloud Games" menu appears in sidebar
   - You should see:
     - All Cloud Games
     - Manage Games ← **Use this to add/edit games**
     - Settings

## Add Your First Game (New Workflow)

### Using the Custom Game Manager

1. **Navigate to Game Manager**
   - Go to: **Cloud Games > Manage Games**
   - Click **"+ Add New Game"** button

2. **Fill in Game Information**
   - **Game Title** ✓ Required
   - **Game Description** (optional)
   - **Cover Image** (click "Upload Image")
     - Select image from media library
     - Preview appears automatically
     - Click "Remove Image" to change

3. **Select Platforms**
   - Check boxes for platforms your game is available on
   - Options:
     - GeForce NOW
     - Xbox Cloud Gaming
     - PlayStation Plus Premium
     - Amazon Luna
     - Boosteroid
     - Shadow PC
     - Air GPU
     - Blacknut
     - CloudDeck

4. **Save**
   - Click **"Create Game"** button
   - You'll return to games list
   - New game appears in table

## Get Game Shortcode

### Method 1: From Manage Games Page
1. Go to **Cloud Games > Manage Games**
2. Find your game in the table
3. See shortcode in **"Shortcode"** column
4. Click copy button (📋) to copy to clipboard
5. Paste in any post/page

### Method 2: From Game Editor (WordPress Standard)
1. Go to **Cloud Games > All Cloud Games**
2. Find your game
3. Click on title to edit
4. Look in right sidebar: **"Game Shortcode"** box
5. Copy any of the provided shortcodes
6. Paste in post/page where you want it to display

## Using Shortcodes

### Basic Shortcode
```
[cloud_gaming_availability game_id="123"]
```
Replace `123` with your actual game ID (shown in list)

### With Dark Theme
```
[cloud_gaming_availability game_id="123" theme="dark"]
```

### With Custom Columns
```
[cloud_gaming_availability game_id="123" columns="2"]
```
Options: 2, 3, or 4 columns (default: 3)

### Without Description
```
[cloud_gaming_availability game_id="123" show_description="false"]
```

### All Options Combined
```
[cloud_gaming_availability game_id="123" theme="light" columns="4" show_description="true"]
```

## Customize Appearance

1. Go to **Cloud Games > Settings**

### Theme
- Choose: Light, Dark, or Auto (system preference)

### Colors
- **Button Color** - Color of "Play Now" buttons
- **Text Color (Light Theme)** - Text in light theme
- **Text Color (Dark Theme)** - Text in dark theme

### Design
- **Border Radius** - Roundness of elements (0-50px)
- **Spacing** - Gap between platforms (0-50px)
- **Padding** - Internal padding (0-50px)
- **Logo Size** - Platform logo size (24-128px)

### Platform Logos
- Upload custom logos for each platform
- If not uploaded, gray placeholders appear
- Click "Upload Logo" for each platform
- Click "Delete" to remove and use placeholder

### Features
- **Enable Platform Filters** - Toggle platform filter buttons on frontend

## Frontend Display

When you use a shortcode on your blog, visitors see:

✓ Game title
✓ Game cover image
✓ Game description (if enabled)
✓ Platform grid showing:
  - Platform logo (or placeholder)
  - Platform name
  - Availability status (Available/Unavailable)
  - "Play Now" button for available platforms
✓ Platform filter buttons (if enabled)

## Common Workflows

### Adding Multiple Games
1. Go to Manage Games
2. Click "+ Add New Game"
3. Fill form
4. Click "Create Game"
5. Repeat for each game

### Editing a Game
1. Go to Manage Games
2. Find game in table
3. Click "Edit" button
4. Update information
5. Click "Update Game"

### Deleting a Game
1. Go to Manage Games
2. Find game in table
3. Click "Delete"
4. Confirm deletion
5. Game is removed

### Using Multiple Shortcodes
You can use the same game shortcode multiple times:
- Different posts/pages
- Different themes
- Different column layouts
- Same game displays each time

### Creating Game Posts
1. Create new Post or Page
2. Add shortcode: `[cloud_gaming_availability game_id="X"]`
3. Publish
4. Shortcode renders on frontend

## Troubleshooting

### Game doesn't display
- Check game ID in shortcode
- Make sure at least one platform is selected
- Verify game is published/saved

### Images not showing
- Check image upload permissions
- Try uploading a different image
- Clear browser cache

### Shortcode not rendering
- Verify game ID exists
- Check spelling of shortcode
- Look for typos in parameters

### Styling looks off
- Clear cache (Ctrl+Shift+Delete)
- Check Settings page
- Verify CSS file is loading (check browser developer tools)

### Platforms show but buttons don't work
- Check Settings > Platform URLs
- Verify internet connection
- Check browser console for errors

## File Locations

```
/wp-content/plugins/cloud-gaming-availability/
├── cloud-gaming-availability.php    ← Main plugin file
├── admin/
│   ├── class-cga-admin.php         ← Admin setup
│   ├── class-cga-game-manager.php  ← Custom game manager
│   └── class-cga-shortcode-display.php ← Shortcode display
├── frontend/
│   └── class-cga-shortcode.php     ← Frontend display
├── includes/
│   ├── class-cga-loader.php        ← Asset loading
│   ├── class-cga-cpt.php           ← Post type setup
│   ├── class-cga-settings.php      ← Settings page
│   └── functions.php               ← Helper functions
└── assets/
    ├── css/
    │   ├── admin.css               ← Admin styling
    │   ├── admin-game-manager.css  ← Game manager styling
    │   └── frontend.css            ← Frontend styling
    └── js/
        ├── admin.js                ← Admin interactions
        └── frontend.js             ← Frontend interactions
```

## Menu Structure

```
Cloud Games
├── All Cloud Games          (WordPress standard view - less used)
├── Manage Games             ← USE THIS to manage games
├── Settings                 ← Use to customize appearance
└── [Add New Game]           (Quick link)
```

## Key Points to Remember

✅ **Use "Manage Games"** not the standard editor for adding/editing games
✅ **Copy shortcodes** directly from the Manage Games table
✅ **All 9 platforms** are supported and configurable
✅ **Shortcodes** work anywhere in posts/pages
✅ **Themes** can be customized in Settings
✅ **Mobile responsive** - works on all devices
✅ **One-click copy** - copy button makes it easy

## Video/Tutorial Tips

If creating a tutorial, mention:
1. Activate plugin from Plugins menu
2. Go to "Cloud Games > Manage Games" (NOT the standard editor)
3. Click "+ Add New Game"
4. Fill in game info with simple form
5. Select platforms by checking boxes
6. Upload game cover image
7. Click "Create Game"
8. Copy shortcode from table
9. Paste into blog post
10. Publish and view on frontend

## Support & Docs

- **CHANGELOG_UPDATES.md** - What's new and changed
- **TESTING_GUIDE.md** - Full testing checklist
- **USAGE_EXAMPLES.md** - Code examples and templates
- **DEPLOYMENT.md** - Deployment and troubleshooting
- **README_PLUGIN.md** - Complete documentation

## Version Info

**Plugin**: Platforms for Cloud Games
**Version**: 1.0.0
**WordPress**: 5.0+
**PHP**: 7.4+

---

**You're ready to go!** Start by visiting Cloud Games > Manage Games in your WordPress admin.
