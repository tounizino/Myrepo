# Testing Guide for Sections AutoPosts Plugin

## Pre-Installation Checklist

- [ ] WordPress 5.0 or higher installed
- [ ] PHP 7.2 or higher
- [ ] At least 10 published posts with featured images
- [ ] Posts assigned to categories
- [ ] Posts with tags (optional)

## Installation Testing

### 1. Plugin Activation
```bash
# Upload to WordPress
1. Upload sections-autoposts-plugin folder to wp-content/plugins/
2. Go to Plugins > Installed Plugins
3. Find "Sections AutoPosts"
4. Click "Activate"
```

**Expected Result:** ✅ Plugin activates without errors. New "Sections AutoPosts" menu appears in WordPress admin sidebar with dashboard icon.

## Admin Panel Testing

### 2. Access Admin Panel
```
1. Navigate to "Sections AutoPosts" in WordPress admin
2. Verify all 9 sections are visible
3. Check each section has a toggle, title, and settings
```

**Expected Result:** ✅ Settings page loads with collapsible cards for all sections.

### 3. Test Color Picker
```
1. Expand "Featured Posts + Widget" section
2. Click on "Accent Color" field
3. Verify WordPress color picker appears
4. Select a new color
5. Save settings
```

**Expected Result:** ✅ Color picker opens, allows color selection, and saves properly.

### 4. Test Conditional Fields
```
1. Expand any section
2. Change "Post Source" dropdown:
   - Select "Filter by Category" → Categories checkboxes should appear
   - Select "Filter by Tag" → Tags checkboxes should appear
   - Select "Custom Post IDs" → Text field should appear
3. Verify other options hide/show correctly
```

**Expected Result:** ✅ Only relevant fields display based on Post Source selection.

### 5. Test Shortcode Copy
```
1. Click on shortcode badge (blue text) for any section
2. Wait for "Copied!" message
```

**Expected Result:** ✅ Shortcode copies to clipboard, temporary "Copied!" message appears.

### 6. Save Settings Test
```
1. Expand "Beginners Corner" section
2. Check "Enable Section" checkbox
3. Change title to "Getting Started with Cloud Gaming"
4. Select "Light" theme
5. Click "Save All Sections" button
6. Verify success message appears
7. Refresh page and confirm changes persist
```

**Expected Result:** ✅ Settings save successfully and persist after page refresh.

## Frontend Rendering Testing

### 7. Featured Section Test
```
1. Create a new page or post
2. Add shortcode: [sap_section type="featured"]
3. Publish and view
```

**Check for:**
- [ ] Large hero image on left
- [ ] 3 smaller posts with thumbnails
- [ ] Widget sidebar on right with 5 posts
- [ ] Badges (NEW, HOT, UPDATED) display correctly
- [ ] Hover effects work (image zoom, link colors)
- [ ] Category tags visible
- [ ] Reading time shows
- [ ] All links work

### 8. Beginners Section Test
```
Shortcode: [sap_section type="beginners"]
```

**Check for:**
- [ ] Step numbers (01, 02, 03, 04) display correctly
- [ ] Green accent border on left side
- [ ] Hover effect (shadow and border color change)
- [ ] Excerpt text displays
- [ ] Updated date shows

### 9. Latest Posts Grid Test
```
Shortcode: [sap_section type="latest_posts"]
```

**Check for:**
- [ ] 3-column grid on desktop
- [ ] 2-column grid on tablet
- [ ] 1-column grid on mobile
- [ ] Pagination buttons appear if more than 9 posts
- [ ] Clicking pagination changes visible posts
- [ ] Page scrolls to grid top on page change
- [ ] Badge overlays on images
- [ ] Card hover effects (lift and shadow)

### 10. Dark Theme Test
```
1. Go to admin settings
2. Expand "Featured Posts" section
3. Select "Dark" theme
4. Set background to #0f172a
5. Save and view frontend
```

**Check for:**
- [ ] Dark background (#1e293b for cards)
- [ ] Light text (#e2e8f0)
- [ ] Proper contrast ratios
- [ ] Border colors adjusted (#334155)
- [ ] Badges still visible

## Styling Conflict Testing

### 11. Theme Compatibility
```
1. Activate a popular theme (Astra, GeneratePress, OceanWP)
2. View sections on frontend
3. Verify plugin styles override theme defaults
```

**Check for:**
- [ ] Font family is "Inter" (or system fallback)
- [ ] Border radius is 3px
- [ ] Colors match settings (not theme colors)
- [ ] No margin/padding from theme affecting layout
- [ ] Grid columns work correctly

### 12. Other Plugin Conflicts
```
1. Install popular plugins (Elementor, WooCommerce, Contact Form 7)
2. View sections on page
3. Check for conflicts
```

**Expected Result:** ✅ Sections render correctly regardless of other plugins.

## Responsive Testing

### 13. Mobile Breakpoints
```
Test at these widths:
- 1400px+ (Desktop)
- 992px-1399px (Laptop)
- 768px-991px (Tablet)
- 600px-767px (Large Phone)
- Below 600px (Small Phone)
```

**Check at each breakpoint:**
- [ ] Featured section switches to single column below 992px
- [ ] Side items become horizontal scrollable
- [ ] Beginners cards stack properly
- [ ] Latest posts grid adjusts columns
- [ ] Pagination buttons remain accessible
- [ ] Text remains readable
- [ ] Images scale properly

## Performance Testing

### 14. Page Load Speed
```
1. Add [sap_all_sections] to a page
2. Test page load with:
   - GTmetrix
   - Google PageSpeed Insights
   - Pingdom
```

**Target Metrics:**
- [ ] CSS file < 50KB
- [ ] JS file < 20KB
- [ ] No render-blocking issues
- [ ] Images lazy-load (if theme supports)

### 15. Query Performance
```
1. Enable Query Monitor plugin
2. Load page with all sections
3. Check database query count and time
```

**Expected Result:** ✅ No slow queries (< 0.1s each), reasonable total count (< 50 queries).

## Edge Cases

### 16. No Posts Available
```
1. Set section to query category with no posts
2. View frontend
```

**Expected Result:** ✅ Section renders empty gracefully (no errors).

### 17. Missing Featured Images
```
1. View section with posts that have no featured images
```

**Expected Result:** ✅ Placeholder image (gradient SVG) displays.

### 18. Long Post Titles
```
1. Create post with very long title (100+ characters)
2. View in sections
```

**Expected Result:** ✅ Title wraps properly, doesn't break layout.

### 19. Special Characters
```
1. Create post with title: "Test & <Script> "Quotes" Special™"
2. View in sections
```

**Expected Result:** ✅ All characters properly escaped and display correctly.

## Security Testing

### 20. XSS Prevention
```
1. Try injecting HTML in post title: <script>alert('XSS')</script>
2. View section
```

**Expected Result:** ✅ Script doesn't execute, displays as plain text.

### 21. SQL Injection
```
1. Try entering: 1' OR '1'='1 in Custom Post IDs field
2. Save settings
```

**Expected Result:** ✅ Input sanitized, no database errors.

## Final Checklist

- [ ] All 9 sections render correctly
- [ ] Admin panel fully functional
- [ ] Color pickers work
- [ ] Conditional fields show/hide properly
- [ ] Settings save and persist
- [ ] Shortcodes work on pages and posts
- [ ] Dark theme renders properly
- [ ] Light theme renders properly
- [ ] Responsive breakpoints working
- [ ] No JavaScript errors in console
- [ ] No PHP errors in error log
- [ ] Pagination functions correctly
- [ ] Hover effects work
- [ ] Links work correctly
- [ ] Images load properly
- [ ] Badges display when appropriate
- [ ] Reading time calculates correctly
- [ ] Excerpts trim properly
- [ ] Category tags display
- [ ] Meta information shows

## Common Issues & Solutions

### Issue: Styles not applying
**Solution:** Check theme CSS specificity. All plugin styles use `!important` to override theme defaults.

### Issue: Pagination not working
**Solution:** Ensure jQuery is loaded. Check browser console for JavaScript errors.

### Issue: Images not displaying
**Solution:** Verify posts have featured images. Check placeholder.svg exists in assets/img/

### Issue: Admin color picker not appearing
**Solution:** Verify WordPress version 5.0+. Check wpColorPicker is enqueued.

### Issue: Conditional fields not showing
**Solution:** Clear browser cache. Check admin.js is loading properly.

## Test Report Template

```
Date: ___________
Tester: ___________
WordPress Version: ___________
PHP Version: ___________
Theme: ___________

✅ PASS | ❌ FAIL | ⚠️ PARTIAL

Installation: ___
Admin Panel: ___
Frontend Rendering: ___
Responsive Design: ___
Dark Theme: ___
Performance: ___
Security: ___

Notes:
_________________________________
_________________________________
_________________________________
```

## Automated Testing (Optional)

For developers who want to run automated tests:

```bash
# Install WP-CLI
wp plugin activate sections-autoposts

# Test shortcode rendering
wp eval 'echo do_shortcode("[sap_section type=\"featured\"]");'

# Check for PHP errors
wp plugin verify-checksums sections-autoposts
```

## Success Criteria

The plugin passes testing if:
1. ✅ All admin functions work without errors
2. ✅ All 9 sections render correctly on frontend
3. ✅ Styling matches original design (Inter font, 3px radius, correct colors)
4. ✅ No JavaScript errors in console
5. ✅ No PHP errors in error log
6. ✅ Responsive design works at all breakpoints
7. ✅ Dark and light themes both functional
8. ✅ Pagination works smoothly
9. ✅ Settings save and persist correctly
10. ✅ Shortcodes work in pages and posts
