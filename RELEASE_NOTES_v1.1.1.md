# Release Notes: Ultimate NAT & Port Checker v1.1.1

**Release Date**: 2024  
**Plugin Version**: 1.1.1  
**Status**: Stable

---

## Summary

Version 1.1.1 is a refinement update focusing on customization, improved single-mode experiences, and UI consistency improvements. This release responds to user feedback about customization options and cleaner presentation of single-function shortcodes.

---

## ✨ New Features

### 1. **Editable Tool Title & Subtitle**
- Admin panel now includes text fields for customizing the main heading and subheading
- Default values: "Ultimate NAT & Port Checker" and descriptive subtitle
- Located in admin under **Settings → NAT & Port Checker → General Settings**

### 2. **Global Font Scale Control**
Choose from three typography presets:
- **Compact**: Smaller text for dense information display (0.63rem–1.61rem scale)
- **Standard** (Default): Balanced readability (0.6875rem–1.75rem scale)
- **Comfort**: Larger text for enhanced accessibility (0.74rem–1.89rem scale)

Applies globally to all text elements throughout the tool.

### 3. **Background Color Customization**
- Light Theme Background: Color picker (default: #f4f5f7)
- Dark Theme Background: Color picker (default: #101827)
- Real-time preview in WordPress admin with color picker

### 4. **Simplified Home Button**
- Moved from header to top-left position above the main container
- Clean, minimal design: arrow icon (←) + "Go Home" text
- Direct link to homepage with hover effect

---

## 🔄 Changes

### UI/UX Improvements

1. **Header Removal for Single Modes**
   - `[nat_checker_only]` and `[port_checker_only]` no longer display the full header
   - Provides cleaner, focused single-function interface
   - Only shows the relevant testing module

2. **Increased Header Title Size**
   - Title font size increased from 1.75rem to 2.25rem
   - Better prominence and hierarchy
   - Changed semantic HTML from `<h2>` to `<h1>` for accessibility

3. **Always-Dark Footer**
   - Footer background is always dark (#0f172a) regardless of theme
   - Text color: #cbd5e1 for consistent readability
   - Provides visual anchor at bottom of tool

4. **Input Width Consistency**
   - "Ports to check" textarea now matches "Host or IP" input width
   - Max-width set to 420px for both inputs
   - Better visual alignment in port checker section

5. **Single Mode Background Enhancement**
   - NAT-only and Port-only views have subtle gray overlay
   - Light theme: 2% dark overlay on background
   - Dark theme: 6% white overlay on background
   - Creates visual separation from page content

6. **Dark Theme Text Consistency**
   - Header title, subtitle, and quick tip badge remain black in dark mode
   - Ensures readability on dark container backgrounds
   - Quick tip has semi-transparent white background (85% opacity)

---

## 🛠️ Technical Changes

### CSS Architecture
- Added CSS custom property `--unpc-surface` for theme-aware backgrounds
- Implemented `.unpc-font-compact` and `.unpc-font-comfort` classes with variable overrides
- Single mode detection via `.unpc-mode-single` wrapper class
- Container backgrounds now use inline CSS variables from admin settings

### PHP Updates
- Added settings: `tool_title`, `tool_subtitle`, `tool_font_scale`, `theme_light_bg`, `theme_dark_bg`
- New sanitization for color picker fields
- Color picker integration with WordPress native `wpColorPicker()`
- Font scale validation (compact|base|comfort)

### Template Updates
- Conditional header rendering based on `$mode` variable
- Home button rendered outside container in separate `.unpc-home-bar` element
- Dynamic inline styles for background colors and font scale
- Mode-specific wrapper classes for targeted styling

---

## 📝 Admin Panel Updates

### New Settings Fields (in order)

**General Settings Section:**
1. Default Theme (light/dark) – *unchanged*
2. Accent Color – *unchanged*
3. Enable Animations – *unchanged*
4. Animation Speed – *unchanged*
5. Container Width – *unchanged*
6. **Tool Title** – *NEW*
7. **Tool Subtitle** – *NEW*
8. **Global Font Size** – *NEW* (dropdown: Compact, Standard, Comfort)
9. **Light Theme Background** – *NEW* (color picker)
10. **Dark Theme Background** – *NEW* (color picker)

All other sections remain unchanged.

---

## 🔧 Migration Notes

### Backward Compatibility
- ✅ Fully backward compatible with v1.1.0
- ✅ All existing settings preserved on upgrade
- ✅ No database schema changes
- ✅ Existing shortcodes work identically

### Post-Upgrade Actions
1. Clear browser cache and WordPress cache plugins
2. Visit admin panel to see new settings
3. Test shortcode variants: `[nat_checker_only]` and `[port_checker_only]`
4. Customize tool title, subtitle, and font scale if desired
5. Adjust background colors to match site branding (optional)

---

## 🐛 Bug Fixes

- **Input Width**: Fixed inconsistent width between "Host or IP" and "Ports to check" inputs
- **Container Background**: Resolved issue where admin background color setting wasn't applying
- **Single Mode Styling**: Fixed missing visual distinction for single-function modes

---

## 📦 Files Changed

```
README.md                                       (8 additions)
ultimate-nat-port-checker/CHANGELOG.md          (28 additions)
ultimate-nat-port-checker/README.md             (6 modifications)
ultimate-nat-port-checker/UPGRADE_GUIDE.md      (29 additions)
ultimate-nat-port-checker/assets/css/...        (141 changes)
ultimate-nat-port-checker/includes/class-unpc-admin.php   (93 additions)
ultimate-nat-port-checker/includes/template-checker.php   (55 changes)
ultimate-nat-port-checker/ultimate-nat-port-checker.php   (9 changes)
```

**Total**: 275 additions, 94 deletions

---

## 🎯 Use Cases

### Before v1.1.1
- Fixed tool title and subtitle
- No control over typography scale
- Hard-coded background colors
- Header always visible on all shortcode variants
- Home button inside header (right side)

### After v1.1.1
- Customizable tool title and subtitle from admin
- Three font scale options for different audiences
- Customizable background colors per theme
- Clean single-function modes without header clutter
- Prominent home button at top-left

---

## 🚀 Recommended Configuration

### For Accessibility-Focused Sites
```
Font Scale: Comfort
Light Background: #f8f9fa
Dark Background: #1a202c
```

### For Information-Dense Sites
```
Font Scale: Compact
Light Background: #ffffff
Dark Background: #0f172a
```

### For Standard/Balanced Sites (Default)
```
Font Scale: Standard
Light Background: #f4f5f7
Dark Background: #101827
```

---

## 📚 Documentation Updates

- Updated README.md with v1.1.1 feature list
- Updated CHANGELOG.md with detailed changes
- Updated UPGRADE_GUIDE.md with migration instructions
- Admin panel includes contextual help text

---

## 🔮 Future Considerations

Potential features for v1.2.0:
- Per-module font size control
- Advanced color scheme generator
- Export/import settings
- Multiple home button positions
- Custom footer text

---

## 🙏 Credits

This update was built based on community feedback emphasizing:
- Customization flexibility
- Cleaner single-function interfaces
- Better integration with various site designs
- Improved accessibility options

---

**For support, feature requests, or bug reports:**  
GitHub Issues: https://github.com/yourusername/ultimate-nat-port-checker/issues

**Version:** 1.1.1  
**Released:** 2024  
**License:** GPL v2 or later
