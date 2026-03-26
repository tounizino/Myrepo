# Cloud Games Availability v2 - Change Log

## Recent Updates

### Admin Panel Improvements
- **Fixed backend AJAX errors** - Added missing AJAX handlers for retrieving platforms and games
- **Unified game management page** - Each game now has a single page with:
  - Game details form
  - Platform availability toggles
  - Platform presets and management
- **Simplified platform settings** - All platform management moved to game page
- **Updated platform presets** - Added all requested platforms:
  - GeForce NOW
  - Xbox Cloud Gaming
  - PlayStation Plus Premium
  - Amazon Luna
  - Boosteroid
  - Shadow PC
  - Air GPU
  - Blacknut
  - CloudDeck
- **Added toggle switches** - Better UX for availability toggles
- **Bulk actions** - Mark all platforms available/unavailable at once
- **Enhanced loading states** - Better visual feedback during AJAX operations

### Bug Fixes
- Fixed error when saving games (missing database method)
- Fixed game retrieval in single-game view
- Added proper error handling for all AJAX requests
- Fixed platform list rendering in custom platforms section

### UI/UX Enhancements
- Improved modal design and functionality
- Better responsive layout for mobile devices
- Enhanced toggle switch styling
- Improved platform preset buttons with hover effects
- Added loading spinners for async operations

### Technical Improvements
- Cleaner JavaScript code organization
- Better separation of concerns
- Improved error handling
- Added more localized strings
- Better form validation

## Platform Presets

The following platform presets are now available:
1. GeForce NOW
2. Xbox Cloud Gaming
3. PlayStation Plus Premium
4. Amazon Luna
5. Boosteroid
6. Shadow PC
7. Air GPU
8. Blacknut
9. CloudDeck

Each preset can be quickly added with a single click, and then customized as needed.
