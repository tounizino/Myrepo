# Cloud Gaming Availability - Deployment Guide

## Pre-Deployment Checklist

### System Requirements
- WordPress 5.0+
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.2+
- Modern web browser with JavaScript enabled

### Installation Steps

1. **Upload Plugin Files**
   - Download/clone the plugin repository
   - Upload to `/wp-content/plugins/cloud-gaming-availability/`

2. **Activate Plugin**
   - Go to WordPress Admin → Plugins
   - Find "Cloud Gaming Availability" and click Activate
   - You should see "Cloud Games" menu item in admin sidebar

3. **Initial Setup**
   - Go to Cloud Games → Settings
   - Choose your default theme
   - Upload platform logos (optional, placeholders work fine)
   - Configure design settings to match your brand
   - Save settings

4. **Add Your First Game**
   - Go to Cloud Games → Add New
   - Enter game title
   - Upload cover image
   - Add description
   - Select available platforms
   - Publish

5. **Use Shortcode**
   - Add to any page/post: `[cloud_gaming_availability game_id="1"]`
   - Customize with parameters as needed

## Verification Steps

After deployment, verify the following:

### Admin Interface
- [ ] Cloud Games menu item appears
- [ ] All Cloud Games page loads
- [ ] Add New game form works
- [ ] Settings page is accessible
- [ ] Platform availability metabox appears on game edit
- [ ] Platform logos can be uploaded/deleted
- [ ] All color pickers work
- [ ] Number inputs accept valid ranges

### Frontend Display
- [ ] Shortcode renders without errors
- [ ] Game cover image displays
- [ ] Platform logos appear
- [ ] "Play Now" buttons are visible for available platforms
- [ ] Buttons link to correct platform URLs
- [ ] Unavailable platforms show as disabled
- [ ] Both light and dark themes work
- [ ] Layout is responsive on mobile (< 480px)
- [ ] Layout is responsive on tablet (480-768px)
- [ ] Layout is responsive on desktop (> 768px)
- [ ] Theme switching works if set to auto
- [ ] Platform filtering works if enabled
- [ ] Hover effects and animations are smooth
- [ ] No JavaScript console errors
- [ ] No CSS layout issues

### Database
- [ ] Custom post type registered
- [ ] Custom taxonomy registered
- [ ] Post metadata saved correctly
- [ ] Settings stored in options table
- [ ] Logo URLs persisted properly

### Performance
- [ ] Pages load within 2 seconds
- [ ] No excessive database queries
- [ ] CSS and JS files load efficiently
- [ ] Images are optimized

## Configuration Examples

### Basic Setup
```
Theme: Light
Button Color: #007cba
Logo Size: 48px
Spacing: 12px
Platform Filters: Enabled
```

### Dark Theme Setup
```
Theme: Dark
Button Color: #4a9eff
Text Color (Dark): #e0e0e0
Logo Size: 56px
Spacing: 16px
Platform Filters: Enabled
```

### Minimal Setup
```
Theme: Auto
Use default colors
Logo Size: 40px
Platform Filters: Disabled
```

## Common Issues & Solutions

### Issue: Plugin doesn't activate
**Solution:**
- Check WordPress version (5.0+)
- Check PHP version (7.4+)
- Check error logs in wp-content/debug.log
- Ensure all files uploaded correctly

### Issue: Shortcode shows error message
**Solution:**
- Verify game ID is correct
- Check game is published
- Verify at least one platform is selected
- Inspect browser console for errors

### Issue: Styling looks off
**Solution:**
- Clear browser cache (Ctrl+Shift+Delete)
- Deactivate other plugins temporarily
- Switch to default WordPress theme
- Check for CSS conflicts

### Issue: Platform logos not showing
**Solution:**
- Upload new logos in Settings page
- Check image file size < 5MB
- Verify image format (PNG, JPG, GIF)
- Check web server file permissions

### Issue: Theme not switching
**Solution:**
- Verify theme setting is saved
- Check if browser supports CSS custom properties
- Try clearing plugin transients

## Maintenance

### Regular Tasks
- **Weekly**: Review new game additions
- **Monthly**: Check plugin updates
- **Quarterly**: Review analytics if available
- **Yearly**: Backup plugin configuration

### Backup Strategy
```sql
-- Backup game data
SELECT * FROM wp_posts WHERE post_type = 'cloud_games';

-- Backup settings
SELECT option_value FROM wp_options WHERE option_name LIKE 'cga_%';
```

### Updates
- Keep WordPress updated to latest stable version
- Update plugin when new versions available
- Test updates on staging site first
- Keep PHP version supported (7.4+)

## Performance Optimization

### WordPress Cache
Enable caching for better performance:
```php
// In wp-config.php
define( 'WP_CACHE', true );
```

### CDN
Serve static assets through CDN:
- CSS files: `/assets/css/`
- JS files: `/assets/js/`

### Image Optimization
- Compress game cover images
- Optimize platform logos (< 50KB each)
- Use WebP format if supported

### Database Optimization
```sql
-- Regular maintenance
OPTIMIZE TABLE wp_posts;
OPTIMIZE TABLE wp_postmeta;
OPTIMIZE TABLE wp_options;
```

## Security Hardening

### File Permissions
```bash
# Plugin directory
chmod 755 /wp-content/plugins/cloud-gaming-availability/
chmod 644 /wp-content/plugins/cloud-gaming-availability/*.php
chmod 644 /wp-content/plugins/cloud-gaming-availability/assets/*
```

### Remove Unnecessary Files
- Delete `package.json` if not using npm
- Delete `composer.json` if not using composer
- Delete this file in production

### SSL/TLS
- Ensure WordPress uses HTTPS
- Update platform URLs if needed
- Configure secure cookies

## Troubleshooting Logs

Enable debug logging:
```php
// In wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Check logs in:
```
/wp-content/debug.log
```

## Support Resources

- **WordPress Codex**: https://developer.wordpress.org/
- **Plugin GitHub**: Repository issues and discussions
- **WordPress Support Forums**: wordpress.org/support/
- **Stack Overflow**: Tag: wordpress, plugin

## Migration Guide

### From Another Platform
1. Export games and availability data
2. Create new games in WordPress
3. Set platform availability for each
4. Update any hardcoded links
5. Test all shortcodes

### Multi-Site Setup
```php
// If using WordPress Multisite:
// Settings are per-site
// Games can be shared across sites with proper configuration
```

## Uninstallation

To safely uninstall:
1. Export any data you want to keep
2. Go to Plugins → Cloud Gaming Availability
3. Click Deactivate
4. Click Delete
5. Choose to delete associated data or keep it

Data will be removed:
- Custom posts (games)
- Post metadata
- Plugin settings
- Uploaded logos
