# Ultimate NAT & Port Checker - WordPress Plugin

A comprehensive, futuristic NAT type and port checking tool specifically designed for cloud gamers. Features a 2026-style design with extensive customization options.

## Features

### Core Functionality
- **NAT Type Checker**: Real-time WebRTC-based NAT detection (Type 1, 2, 3)
- **Multi-Port Checker**: Scan single ports, multiple ports, or port ranges
- **Device Information**: Automatic detection of IP, location, browser, OS, and more
- **Router Login Database**: Quick access to popular router admin panels

### Admin Features
- **3 Shortcodes**:
  - `[ultimate_nat_port_checker]` - Full tool suite
  - `[ultimate_nat_checker]` - NAT checker only
  - `[ultimate_port_checker]` - Port checker only
  
- **Comprehensive Settings**:
  - Theme selection (Dark/Light)
  - Font size presets (Small/Medium/Large)
  - Container width customization (800-2000px)
  - Toggle individual sections on/off
  - Custom color schemes
  - STUN server configuration
  - Timeout settings
  - Custom CSS & JavaScript injection
  - IP lookup caching

### Design
- Futuristic 2026 design with gradient backgrounds
- Smooth animations and transitions
- Fully responsive (mobile, tablet, desktop)
- Dark and light theme support
- Color-coded NAT results (Type 1: Green, Type 2: Orange, Type 3: Red)
- Glassmorphism effects
- Radial gradient accents

### Gaming Presets
Built-in port presets for:
- Xbox Live
- PlayStation Network
- Steam
- NVIDIA GeForce NOW
- Google Stadia
- Call of Duty
- Shadow PC
- Custom presets support

## Installation

1. Upload the `ultimate-nat-port-checker` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'NAT & Port Checker' in the admin menu to configure
4. Add any of the 3 shortcodes to your pages

## Usage

### Basic Shortcode
```
[ultimate_nat_port_checker]
```

### NAT Checker Only
```
[ultimate_nat_checker]
```

### Port Checker Only
```
[ultimate_port_checker]
```

## Admin Settings

Navigate to **NAT & Port Checker** in your WordPress admin to access:

### General Tab
- Default theme (Dark/Light)
- Font size preset
- Container width
- Animation toggle

### Display Tab
- Show/hide security note
- Show/hide device information
- Show/hide router login grid
- Show/hide guides
- Show/hide footer

### NAT Checker Tab
- STUN servers configuration
- Timeout settings
- Technical log display

### Port Checker Tab
- Connection timeout
- Maximum ports per scan
- Custom port presets

### Colors Tab
- Primary accent color
- Success color (NAT Type 1)
- Warning color (NAT Type 2)
- Error color (NAT Type 3)
- Info color

### Advanced Tab
- Custom CSS
- Custom JavaScript
- IP info caching
- Cache duration

## Technical Details

### Requirements
- WordPress 5.0 or higher
- PHP 7.2 or higher
- Modern browser with WebRTC support

### Technologies Used
- WebRTC for NAT detection
- jQuery for DOM manipulation
- WordPress Color Picker
- AJAX for asynchronous operations
- ipapi.co for IP geolocation

### Security
- All checks performed client-side where possible
- No user data stored
- Nonce verification for AJAX requests
- Input sanitization and validation

## Support

For issues, feature requests, or contributions, please visit:
https://cloudgamingblog.com/support

## License

GPL v2 or later

## Credits

Developed by Cloud Gaming Blog
© 2026 All rights reserved

## Changelog

### Version 1.0.0
- Initial release
- NAT type detection with WebRTC
- Multi-port checker with range support
- Device information display
- Router login database
- Comprehensive admin settings
- 3 shortcode variations
- Dark/Light theme support
- Fully responsive design
- Custom color schemes
- Technical logging
