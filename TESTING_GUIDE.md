# Platforms for Cloud Games - Testing Guide

## Pre-Testing Checklist

- [ ] Plugin is activated
- [ ] WordPress version is 5.0+
- [ ] PHP version is 7.4+
- [ ] No conflicting plugins active

## Admin Interface Testing

### Menu Navigation
- [ ] "Cloud Games" menu item appears in sidebar
- [ ] "Manage Games" submenu appears under Cloud Games
- [ ] "Settings" submenu appears under Cloud Games
- [ ] All menu items are clickable

### Game Manager Page

#### Games List View
1. **Page Load**
   - [ ] Page loads without errors
   - [ ] Table displays correctly
   - [ ] "Add New Game" button is visible and clickable

2. **Games Table**
   - [ ] Column headers are visible: Game Title, Available Platforms, Shortcode, Actions
   - [ ] All existing games appear in the table
   - [ ] Platforms show correctly for each game
   - [ ] Shortcode displays with correct game ID
   - [ ] Copy buttons (📋) are visible

3. **Game Actions**
   - [ ] Edit button is visible for each game
   - [ ] Delete button is visible for each game
   - [ ] Edit links to correct edit form
   - [ ] Delete shows confirmation
   - [ ] Delete removes game from list

4. **Shortcode Copying**
   - [ ] Click copy button (📋)
   - [ ] Shortcode is copied to clipboard
   - [ ] Button shows "✓" temporarily
   - [ ] Can paste shortcode in text editor

#### Add New Game Form
1. **Form Display**
   - [ ] Form loads without errors
   - [ ] All form sections are visible
   - [ ] Form is well-formatted
   - [ ] Back button works

2. **Game Information Section**
   - [ ] Game Title field is required
   - [ ] Game Description textarea works
   - [ ] Description textarea accepts multiple lines
   - [ ] Game Title field has focus management

3. **Image Upload**
   - [ ] "Upload Image" button works
   - [ ] Media uploader opens
   - [ ] Can select image
   - [ ] Image preview updates after selection
   - [ ] "Remove Image" button appears after upload
   - [ ] Remove button deletes preview
   - [ ] Different images can be uploaded

4. **Platform Selection**
   - [ ] All 9 platforms display in checkbox grid
   - [ ] Platforms are:
     - GeForce NOW
     - Xbox Cloud Gaming
     - PlayStation Plus Premium
     - Amazon Luna
     - Boosteroid
     - Shadow PC
     - Air GPU
     - Blacknut
     - CloudDeck
   - [ ] Can select multiple platforms
   - [ ] Can deselect platforms
   - [ ] Selected platforms highlight
   - [ ] Platform selection persists on edit

5. **Form Submission**
   - [ ] Submit button is visible
   - [ ] Submit button is clickable
   - [ ] Form validates (title required)
   - [ ] Redirect to games list after submit
   - [ ] New game appears in list

#### Edit Game Form
1. **Loading**
   - [ ] Form loads with existing data
   - [ ] Game title pre-fills
   - [ ] Description pre-fills
   - [ ] Image preview shows current image
   - [ ] Previously selected platforms are checked
   - [ ] Back button shows games list link

2. **Editing**
   - [ ] Can change game title
   - [ ] Can change description
   - [ ] Can upload new image
   - [ ] Can remove image
   - [ ] Can select/deselect platforms
   - [ ] Changes save correctly

3. **Update Functionality**
   - [ ] Submit button says "Update Game"
   - [ ] Changes are saved
   - [ ] Redirect to games list
   - [ ] Updated information displays correctly

### Delete Functionality
1. **Delete Game**
   - [ ] Delete button shows confirmation
   - [ ] Canceling confirmation returns to list
   - [ ] Confirming deletion removes game
   - [ ] Game no longer appears in list
   - [ ] Redirect to games list after delete

## Shortcode Display Testing

### In Post/Page Editor
1. **Metabox Display**
   - [ ] Metabox appears in sidebar for published games
   - [ ] Metabox title is "Game Shortcode"
   - [ ] Metabox shows for published games only
   - [ ] Metabox says "Publish this game to see its shortcode" for drafts

2. **Shortcode Display Content**
   - [ ] Shows "Use these shortcodes" text
   - [ ] Shows "Basic Shortcode:" section
   - [ ] Shows basic shortcode with correct game ID
   - [ ] Shows "With Custom Parameters:" section
   - [ ] Shows full shortcode with example parameters
   - [ ] Copy buttons work for both
   - [ ] Parameters section lists available options

3. **Copy Functionality**
   - [ ] Click copy button on basic shortcode
   - [ ] Shortcode copies to clipboard
   - [ ] Button shows "Copied!" feedback
   - [ ] Can paste in new location
   - [ ] Same works for parameterized version

## Frontend Display Testing

### Shortcode Rendering
1. **Basic Shortcode**
   - [ ] Create new post
   - [ ] Add: `[cloud_gaming_availability game_id="X"]` (replace X with game ID)
   - [ ] Publish post
   - [ ] View post on frontend
   - [ ] Game displays correctly
   - [ ] No error messages

2. **Game Display Content**
   - [ ] Game title displays
   - [ ] Game cover image displays
   - [ ] Game description shows
   - [ ] All platforms appear in grid
   - [ ] Available platforms show "Available" badge
   - [ ] Unavailable platforms show "Unavailable" badge
   - [ ] Available platforms have "Play Now" buttons
   - [ ] Unavailable platforms show disabled buttons

3. **Platform Display**
   - [ ] Platform logos display (or placeholders)
   - [ ] Platform names display
   - [ ] Grid layout is organized
   - [ ] Spacing looks good
   - [ ] No overlapping elements

4. **Buttons**
   - [ ] "Play Now" buttons are visible
   - [ ] Buttons have correct styling
   - [ ] Buttons link to platform URLs
   - [ ] Links open in new tabs
   - [ ] Disabled buttons don't open links

5. **Theme Testing**
   - [ ] Test with light theme: `[cloud_gaming_availability game_id="X" theme="light"]`
   - [ ] Background is light
   - [ ] Text is dark
   - [ ] Contrast is good
   - [ ] Test with dark theme: `[cloud_gaming_availability game_id="X" theme="dark"]`
   - [ ] Background is dark
   - [ ] Text is light
   - [ ] Contrast is good

6. **Column Testing**
   - [ ] Test with columns="2": displays 2 columns
   - [ ] Test with columns="3": displays 3 columns
   - [ ] Test with columns="4": displays 4 columns
   - [ ] Responsive on mobile (stacks to 1 column)

7. **Description Toggle**
   - [ ] Test with show_description="true": description shows
   - [ ] Test with show_description="false": description hidden
   - [ ] Default shows description

### Responsive Testing
1. **Desktop (1200px+)**
   - [ ] Layout looks good
   - [ ] All elements properly spaced
   - [ ] Text is readable
   - [ ] Buttons are clickable

2. **Tablet (768px - 1199px)**
   - [ ] Layout adapts
   - [ ] Columns reduce appropriately
   - [ ] Still readable
   - [ ] Touch targets are adequate

3. **Mobile (< 768px)**
   - [ ] Layout stacks vertically
   - [ ] Single column view
   - [ ] Touch targets are 44px minimum
   - [ ] Text is readable
   - [ ] Images scale properly
   - [ ] Buttons are easily tappable

### Animation Testing
1. **Hover Effects**
   - [ ] Platform items scale up on hover
   - [ ] Buttons change color on hover
   - [ ] Arrow icon moves on button hover
   - [ ] Transitions are smooth

2. **Loading Animations**
   - [ ] Game appears with fade-in
   - [ ] No jarring visual changes
   - [ ] Animations are performant

## Settings Page Testing

### Theme Settings
- [ ] Can select Light theme
- [ ] Can select Dark theme
- [ ] Can select Auto theme
- [ ] Setting saves

### Color Settings
- [ ] Button color picker works
- [ ] Light theme text color picker works
- [ ] Dark theme text color picker works
- [ ] Colors save correctly
- [ ] Changes reflect on frontend

### Design Settings
- [ ] Border radius input accepts numbers
- [ ] Spacing input accepts numbers
- [ ] Padding input accepts numbers
- [ ] Logo size input accepts numbers
- [ ] Enable filters checkbox works
- [ ] All settings save

### Platform Logos
- [ ] Logo upload button works for each platform
- [ ] Media uploader opens
- [ ] Can select image
- [ ] Logo preview updates
- [ ] Delete button appears after upload
- [ ] Delete removes logo
- [ ] Logos display on frontend

## Error Testing

### Should NOT Show Errors
- [ ] Plugin activation
- [ ] Loading game manager
- [ ] Adding game
- [ ] Editing game
- [ ] Deleting game
- [ ] Viewing shortcode display
- [ ] Frontend shortcode rendering
- [ ] Theme switching
- [ ] Settings page

### Browser Console
- [ ] No JavaScript errors
- [ ] No warnings
- [ ] No 404 errors on assets
- [ ] No CORS errors

## Performance Testing

### Load Times
- [ ] Game manager page loads < 2 seconds
- [ ] Add/edit form loads < 1 second
- [ ] Frontend shortcode renders < 1 second
- [ ] Settings page loads < 2 seconds

### Database
- [ ] No excessive queries
- [ ] Efficient post meta queries
- [ ] Settings load quickly

### Assets
- [ ] CSS files load
- [ ] JavaScript files load
- [ ] No duplicate asset loading
- [ ] Images optimize properly

## Cross-Browser Testing

### Chrome/Edge
- [ ] All features work
- [ ] Layout looks correct
- [ ] Colors display properly

### Firefox
- [ ] All features work
- [ ] Layout looks correct
- [ ] Form inputs work

### Safari
- [ ] All features work
- [ ] Layout looks correct
- [ ] Copy buttons work

### Mobile Safari (iOS)
- [ ] Responsive layout works
- [ ] Touch interactions work
- [ ] Media picker works

### Chrome Mobile (Android)
- [ ] Responsive layout works
- [ ] Touch interactions work
- [ ] Media picker works

## Shortcode Parameter Combinations Testing

### Single Parameters
- [ ] `[cloud_gaming_availability game_id="X" theme="light"]`
- [ ] `[cloud_gaming_availability game_id="X" columns="2"]`
- [ ] `[cloud_gaming_availability game_id="X" show_description="false"]`

### Multiple Parameters
- [ ] `[cloud_gaming_availability game_id="X" theme="dark" columns="4"]`
- [ ] `[cloud_gaming_availability game_id="X" theme="auto" show_description="true"]`
- [ ] `[cloud_gaming_availability game_id="X" theme="light" columns="2" show_description="false"]`

### Invalid Parameters
- [ ] Invalid game_id shows error
- [ ] Invalid theme defaults to auto
- [ ] Invalid columns defaults to 3
- [ ] Invalid show_description defaults to true

## Accessibility Testing

### Keyboard Navigation
- [ ] Can tab through all buttons
- [ ] Can activate buttons with Enter/Space
- [ ] Focus management works
- [ ] Form inputs are accessible

### Screen Reader
- [ ] Buttons have readable labels
- [ ] Images have alt text
- [ ] Form labels are associated
- [ ] Semantic HTML structure

### Color Contrast
- [ ] Light theme has sufficient contrast
- [ ] Dark theme has sufficient contrast
- [ ] Buttons are distinguishable

## Final Sign-Off

- [ ] All tests passed
- [ ] No critical issues
- [ ] No performance issues
- [ ] Plugin ready for production
- [ ] Documentation is complete
- [ ] User guide is clear

---

**Testing Status**: ✅ Ready for Production
**Date**: [Date Tested]
**Tester**: [Your Name]
