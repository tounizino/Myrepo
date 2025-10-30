# Upgrade Guide: Version 1.0.0 → 1.1.0

## What's New in Version 1.1.0

This release brings several user-requested improvements and refinements to the Ultimate NAT & Port Checker plugin.

### New Features

#### 1. **Go Home Button**
A prominent "Go Home" button is now displayed in the header, allowing visitors to quickly navigate back to your homepage.

- **Location**: Top-right of the tool header
- **Icon**: Home icon (SVG)
- **Mobile-friendly**: Full-width on mobile devices

#### 2. **Three Shortcode Variants**
You can now use three different shortcodes depending on your needs:

```
[nat_port_checker]    - Full tool (all features)
[nat_checker_only]    - Only NAT Intelligence module
[port_checker_only]   - Only Port Availability Suite
```

**Use Cases:**
- Use `[nat_checker_only]` on a dedicated NAT diagnostics page
- Use `[port_checker_only]` on a quick port verification page
- Use `[nat_port_checker]` for the complete experience

#### 3. **Credits & Copyright Footer**
A professional footer now displays at the bottom of the tool with:
- Copyright information with dynamic year
- Credits line acknowledging your site
- Clean, centered layout

### Visual Improvements

#### 4. **Dark Theme Text Enhancement**
In dark mode, the following elements now display in **black (#000000)** for better readability:
- Main title ("Ultimate NAT & Port Checker")
- Subtitle description
- Quick tip badge text

The quick tip badge also features a semi-transparent white background in dark mode.

#### 5. **Section Reordering**
For better information flow:
- **Device Snapshot** now appears **before** Popular Router Login Shortcuts
- This groups related diagnostic information together

### Technical Improvements

#### 6. **Container Width Control**
The container width setting in the admin panel now works correctly:
- Applied via inline CSS styles
- Range: 768px – 2400px (as configured)
- Default: 1400px

#### 7. **Theme CSS Protection**
Critical styles now use `!important` flags to prevent conflicts with GeneratePress and other themes:
- Layout properties protected
- Typography safeguarded
- Spacing preserved
- Button styles enforced

This ensures the tool looks consistent regardless of your active theme.

### Breaking Changes

**None!** Version 1.1.0 is fully backward compatible with 1.0.0.

- Existing `[nat_port_checker]` shortcodes continue to work exactly as before
- All settings are preserved during the upgrade
- No database changes required

### How to Upgrade

1. **Backup your site** (recommended before any plugin update)
2. **Deactivate** the old version in WordPress Admin → Plugins
3. **Delete** the old plugin folder
4. **Upload** the new version
5. **Activate** the plugin
6. **Clear your cache** (if using a caching plugin)

Alternatively, if you're using Git:
```bash
cd wp-content/plugins/ultimate-nat-port-checker
git pull origin main
```

### Post-Upgrade Checklist

- [ ] Verify the "Go Home" button appears and links to your homepage
- [ ] Test dark/light theme toggle
- [ ] Confirm header text is readable in dark mode
- [ ] Check that container width setting applies correctly
- [ ] Test the new shortcode variants if needed
- [ ] Verify footer displays copyright and credits

### Troubleshooting

**Issue**: Container width still doesn't change
- **Solution**: Clear your browser cache and any WordPress caching plugins

**Issue**: Dark theme text not showing in black
- **Solution**: Hard-refresh the page (Ctrl+F5 or Cmd+Shift+R)

**Issue**: Go Home button links to wrong URL
- **Solution**: The button automatically links to your WordPress homepage. If you need a custom URL, you can filter it in your theme's `functions.php`

**Issue**: Theme styles still overriding plugin
- **Solution**: Clear all caches. The new `!important` flags should prevent most conflicts. If issues persist, check for extremely aggressive theme CSS.

### Admin Settings

All existing settings are preserved. No new required configuration.

**Optional**: Review Settings → NAT & Port Checker to see the updated shortcode documentation.

### Need Help?

- Review the [CHANGELOG.md](CHANGELOG.md) for detailed technical changes
- Check the [README.md](README.md) for full documentation
- Report issues on GitHub: https://github.com/yourusername/ultimate-nat-port-checker/issues

---

**Thank you for using Ultimate NAT & Port Checker!**

*Version 1.1.0 – The Cloud Gaming Diagnostics Tool*
