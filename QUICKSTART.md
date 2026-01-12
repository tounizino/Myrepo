# Quick Start Guide

Get the Cloud Gaming Readiness Test up and running in minutes.

## Installation (2 minutes)

### Option 1: WordPress Admin (Recommended)

1. Download the plugin as a ZIP file
2. Go to **WordPress Admin** → **Plugins** → **Add New**
3. Click **Upload Plugin**
4. Select the ZIP file and click **Install Now**
5. Click **Activate Plugin**

### Option 2: FTP/SFTP

1. Upload the `cloud-gaming-readiness-test` folder to `/wp-content/plugins/`
2. Go to **WordPress Admin** → **Plugins**
3. Find "Cloud Gaming Readiness Test" and click **Activate**

## Basic Usage (1 minute)

### Add to a Page

Create a new page or edit an existing one, then add:

```
[cloud_gaming_test]
```

That's it! Save and view the page.

### Using the Block Editor

1. Edit a page with the Block Editor
2. Click the **+** (Add Block) button
3. Search for "Cloud Gaming Test"
4. Click to insert the block

## Customization (Optional)

### Change Width

```
[cloud_gaming_test width="800px"]
```

### Use Dark Theme

```
[cloud_gaming_test theme="dark"]
```

### Disable Advanced Features

```
[cloud_gaming_test advanced="false"]
```

## Testing the Demo

Open `demo.html` in your browser to see the test in action without WordPress:

```bash
# From the project directory
open demo.html  # Mac
start demo.html # Windows
xdg-open demo.html # Linux
```

## Verify Installation

After activation:

1. Go to **Plugins** → "Cloud Gaming Readiness Test" should be listed
2. Create a test page with `[cloud_gaming_test]`
3. View the page - you should see the test widget
4. Click "Start Readiness Test" to verify functionality

## Common Issues

### Plugin not showing

- Clear browser cache
- Check plugin is activated in Plugins menu
- Verify shortcode is `[cloud_gaming_test]` (not `_`)

### Test doesn't start

- Check browser console (F12) for errors
- Ensure JavaScript is enabled
- Try a different browser

### Styling looks wrong

- Check for theme conflicts
- Try disabling other plugins temporarily
- Contact your theme developer if issues persist

## Need Help?

- Check the full [README.md](README.md) for detailed documentation
- Review [ARCHITECTURE.md](ARCHITECTURE.md) for technical details
- Check browser console for JavaScript errors

## Next Steps

1. Add the test to your cloud gaming optimization page
2. Customize styling to match your theme
3. Consider adding a dedicated "Network Test" page
4. Share the test with your community!

**Total setup time: 3-5 minutes** ⚡
