# Installation Guide - Cloud Gaming Latest Posts Grid

## Quick Installation (Standard WordPress Plugin)

### Method 1: Upload via WordPress Admin (Recommended)

1. **Package the Plugin**
   - Zip the entire `cloud-gaming-latest-posts` folder
   - Name it: `cloud-gaming-latest-posts.zip`

2. **Upload to WordPress**
   - Log in to your WordPress admin panel
   - Navigate to **Plugins → Add New**
   - Click **Upload Plugin** at the top
   - Choose your `cloud-gaming-latest-posts.zip` file
   - Click **Install Now**
   - Click **Activate Plugin**

3. **Verify Installation**
   - You should see a success message
   - Check that "Gaming Posts" appears in your admin menu

### Method 2: Manual FTP/File Manager Upload

1. **Prepare the Files**
   - Download or have the `cloud-gaming-latest-posts` folder ready

2. **Upload via FTP**
   - Connect to your server via FTP (FileZilla, Cyberduck, etc.)
   - Navigate to `/wp-content/plugins/`
   - Upload the entire `cloud-gaming-latest-posts` folder
   - Ensure all subdirectories and files are uploaded

3. **Activate**
   - Log in to WordPress admin
   - Go to **Plugins → Installed Plugins**
   - Find "Cloud Gaming Latest Posts Grid"
   - Click **Activate**

### Method 3: cPanel File Manager

1. **Access cPanel**
   - Log in to your hosting cPanel
   - Open **File Manager**

2. **Navigate to Plugins**
   - Go to `public_html/wp-content/plugins/`
   - (Path may vary: some hosts use `www` or `html`)

3. **Upload**
   - Click **Upload**
   - Upload `cloud-gaming-latest-posts.zip`
   - Click **Extract** after upload completes
   - Delete the .zip file (optional)

4. **Activate in WordPress**
   - Go to WordPress admin → **Plugins**
   - Activate "Cloud Gaming Latest Posts Grid"

## Post-Installation Setup

### Step 1: Configure Settings

1. Navigate to **Settings → Cloud Gaming Latest Posts**
2. Configure options:
   - **Enable Dark Theme**: Toggle for dark mode
   - **Container Max Width**: Default is `1400px`
   - **Container Margin**: Default is `0 auto 40px auto`
   - **Container Padding**: Default is `0`
   - **Posts Per Page**: Default is `9` (recommended for 3x3 grid)

3. Click **Save Changes**

### Step 2: Add to Your Page

1. **Create or Edit a Page**
   - Go to **Pages → Add New** (or edit existing page)
   - Give it a title (e.g., "Blog" or "Latest Articles")

2. **Add the Shortcode**
   - In Gutenberg/Block Editor: Add a **Shortcode Block**
   - In Classic Editor: Add directly to content
   - Type or paste: `[cloud_gaming_posts]`

3. **Publish**
   - Click **Publish** or **Update**
   - View the page to see your posts grid

### Step 3: Verify Functionality

1. **Check the Grid**
   - Visit the page with the shortcode
   - Verify posts are displaying correctly
   - Check that images, titles, and excerpts appear

2. **Test Pagination**
   - If you have more than 9 posts, pagination buttons should appear
   - Click through pages to ensure AJAX loading works
   - Verify smooth scrolling to grid top

3. **Test Responsiveness**
   - View on desktop (3 columns)
   - View on tablet/iPad (2 columns)
   - View on mobile (1 column)

## Troubleshooting Installation

### Plugin Won't Activate?

**Check PHP Version**
```bash
# Minimum required: PHP 7.2
# Recommended: PHP 8.0+
```

**Check WordPress Version**
```
Minimum required: WordPress 5.0
Recommended: WordPress 6.0+
```

### Files Not Uploading?

**Check File Permissions**
- Plugins folder should be writable (755 or 775)
- Use FTP or hosting panel to adjust permissions

**Check Upload Limits**
- Some hosts limit upload sizes
- Extract locally and upload folder instead of zip

### Posts Not Displaying?

**Verify You Have Published Posts**
```php
// Check in WordPress admin: Posts → All Posts
// Status should be "Published", not "Draft"
```

**Clear Cache**
- If using a caching plugin (WP Super Cache, W3 Total Cache, etc.)
- Clear cache after activating plugin

**Check Shortcode Syntax**
- Must be exactly: `[cloud_gaming_posts]`
- Case-sensitive: `cloud_gaming_posts` (not `Cloud_Gaming_Posts`)

### Styling Issues?

**Theme Conflicts**
- Your theme may override plugin styles
- Try switching to a default WordPress theme (Twenty Twenty-Four)
- If it works, add custom CSS to override theme styles

**Check CSS Loading**
- Open browser Developer Tools (F12)
- Go to Network tab
- Refresh page
- Look for `frontend.css` - should load successfully

### JavaScript Not Working?

**Check jQuery**
- Plugin requires jQuery (bundled with WordPress)
- Some themes incorrectly remove jQuery

**Check Console Errors**
- Open browser Developer Tools (F12)
- Check Console tab for JavaScript errors
- Common issue: jQuery not defined

**Check AJAX URL**
- Verify `admin-ajax.php` is accessible
- Check browser Network tab during page navigation

## Updating the Plugin

### Manual Update

1. **Backup First**
   - Backup your database
   - Backup existing plugin files

2. **Deactivate**
   - Go to **Plugins**
   - Deactivate "Cloud Gaming Latest Posts Grid"
   - (Settings are preserved)

3. **Replace Files**
   - Delete old plugin folder via FTP
   - Upload new plugin folder
   - Or overwrite files directly

4. **Reactivate**
   - Go to **Plugins**
   - Click **Activate**

## Uninstalling the Plugin

### Method 1: Via WordPress Admin

1. **Deactivate**
   - Go to **Plugins**
   - Click **Deactivate** under plugin name

2. **Delete**
   - After deactivation, click **Delete**
   - WordPress will remove all plugin files
   - Settings are automatically cleaned up

### Method 2: Manual Removal

1. **Deactivate in WordPress**
   - Essential step - don't skip!

2. **Delete via FTP**
   - Navigate to `/wp-content/plugins/`
   - Delete `cloud-gaming-latest-posts` folder

3. **Clean Database (Optional)**
   - Run SQL query to remove settings:
   ```sql
   DELETE FROM wp_options WHERE option_name = 'cglp_settings';
   ```

## File Permissions Reference

Recommended permissions for security:

```
cloud-gaming-latest-posts/ (folder)          755
cloud-gaming-latest-posts.php               644
includes/ (folder)                          755
includes/*.php                              644
assets/ (folder)                            755
assets/css/ (folder)                        755
assets/css/*.css                            644
assets/js/ (folder)                         755
assets/js/*.js                              644
assets/images/ (folder)                     755
assets/images/*.svg                         644
```

## System Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.2 or higher (8.0+ recommended)
- **MySQL**: 5.6 or higher (or MariaDB 10.1+)
- **jQuery**: Bundled with WordPress (required for pagination)

## Server Compatibility

The plugin works with all major hosting providers:

- ✅ SiteGround
- ✅ Bluehost
- ✅ WP Engine
- ✅ Kinsta
- ✅ HostGator
- ✅ GoDaddy
- ✅ DreamHost
- ✅ Any standard WordPress hosting

## Need Help?

If you encounter issues during installation:

1. Check the **USAGE-EXAMPLES.md** for common solutions
2. Verify your server meets requirements
3. Try deactivating other plugins temporarily
4. Switch to a default WordPress theme temporarily
5. Enable WordPress debug mode to see detailed errors

Enable debug mode by adding to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check `/wp-content/debug.log` for errors.

## Success Checklist

- [ ] Plugin files uploaded to `/wp-content/plugins/cloud-gaming-latest-posts/`
- [ ] Plugin activated in WordPress admin
- [ ] Settings page accessible at **Settings → Cloud Gaming Latest Posts**
- [ ] Settings configured to your preference
- [ ] Shortcode `[cloud_gaming_posts]` added to a page
- [ ] Posts displaying correctly on frontend
- [ ] Pagination working (if more than 9 posts)
- [ ] Responsive layout working on mobile
- [ ] No JavaScript errors in browser console

---

**Congratulations!** Your Cloud Gaming Latest Posts Grid is now installed and ready to showcase your content.
