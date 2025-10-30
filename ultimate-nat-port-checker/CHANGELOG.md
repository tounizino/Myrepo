# Changelog

All notable changes to the Ultimate NAT & Port Checker plugin will be documented in this file.

## [1.1.1] - 2024

### Added
- **Editable Tool Title & Subtitle**: Customize the main heading and description text from the admin panel
- **Font Scale Control**: Three typography presets (Compact, Standard, Comfort) for different reading preferences
- **Background Color Settings**: Customize light and dark theme background colors from admin panel
- **Home Button**: Simplified top-left "Go Home" link above the header with clean styling

### Changed
- **Header Visibility**: Removed header from `[nat_checker_only]` and `[port_checker_only]` shortcodes for cleaner single-module views
- **Single Mode Background**: NAT-only and Port-only views now have a subtle gray background for better separation
- **Footer Styling**: Footer section is now always dark (#0f172a) regardless of theme choice
- **Home Button Position**: Moved from header to above the main container at top-left
- **Header Title Size**: Increased from 1.75rem to 2.25rem for better prominence
- **Input Width**: "Ports to check" textarea now matches "Host or IP" input width (max 420px)
- **Container Background**: Light theme containers now have a subtle gray tint (#f4f5f7 default)

### Improved
- Theme toggle reverted to simpler, previous styling without heavy `!important` overrides
- Better visual hierarchy in single-module modes
- More consistent spacing and padding across all modes
- Enhanced readability with larger title font size

### Technical
- Added CSS custom property `--unpc-surface` for theme-aware backgrounds
- Font scale implemented via CSS variable overrides (`.unpc-font-compact`, `.unpc-font-comfort`)
- Single mode detection via `.unpc-mode-single` class for conditional styling

## [1.1.0] - 2024

### Added
- **Go Home button** in header for quick navigation back to homepage
- **Footer** with copyright and credits information
- **Three shortcode variants**:
  - `[nat_port_checker]` – Full tool with all features
  - `[nat_checker_only]` – NAT Intelligence module only
  - `[port_checker_only]` – Port Availability Suite only
- **High-priority CSS overrides** with `!important` flags to prevent theme conflicts
- **Inline max-width control** from admin settings now properly enforces container width

### Changed
- **Section Order**: Swapped "Device Snapshot" and "Popular Router Login Shortcuts" positions
- **Dark Theme Enhancement**: Header title, subtitle, and quick tip badge now display in black (#000000) for better readability
- **Quick Tip Badge Styling**: Enhanced with semi-transparent white background in dark mode

### Improved
- Container width setting from admin panel now applies correctly via inline styles
- All critical layout and typography styles protected from theme CSS interference
- Better visual hierarchy with dedicated classes for header elements
- Enhanced mobile responsiveness with specific breakpoint adjustments

### Fixed
- Container width setting in admin panel now actually controls the displayed width
- Theme CSS conflicts resolved with strategic `!important` declarations
- Dark theme text visibility improved for better user experience

## [1.0.0] - 2024

### Initial Release
- WebRTC-based NAT type detection (Type 1/2/3)
- Comprehensive port diagnostics with TCP/UDP support
- Cloud gaming port presets (PlayStation, Xbox, GeForce NOW, etc.)
- Popular router login shortcuts
- Device information snapshot
- Useful links configuration
- Advanced router configuration guides
- Light/dark theme support with localStorage persistence
- Fully responsive design optimized for mobile devices
- Complete admin settings panel
- REST API endpoints for port checking
- Transparent background for seamless theme integration
- Animated transitions and interactive UI elements
- Accessible HTML structure with proper ARIA labels
- Internationalization-ready with translation functions

---

**Format:** This changelog follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) conventions.
