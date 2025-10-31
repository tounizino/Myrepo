# Ultimate NAT & Port Checker for WordPress

**Version:** 1.0.0  
**Author:** Your Name  
**Requires WordPress:** 5.0 or higher  
**Tested up to:** 6.4  
**License:** GPL v2 or later

## Description

The Ultimate NAT & Port Checker is a comprehensive WordPress plugin designed for cloud gaming blogs and networking websites. It provides visitors with powerful network diagnostic tools including:

- **NAT Type Detection** with color-coded results (Type 1 Open, Type 2 Moderate, Type 3 Strict)
- **Port Scanning** with presets for major cloud gaming platforms
- **Detailed Device Information** display
- **Router Login Reference** library
- **WebRTC-based ICE Candidate Analysis**
- **IP Geolocation** with ISP and organizational data
- **Dark/Light Theme Toggle**
- **Fully Responsive Design**

## Features

### NAT Checker
- Real-time NAT type detection using advanced WebRTC STUN analysis
- Displays public IP, ISP, country, region, city, and timezone
- Technical details including AS number, organization, and connection type
- ICE candidate collection for advanced peer-to-peer routing diagnostics
- Symmetric NAT detection
- Color-coded results based on NAT type

### Port Checker
- Cloud gaming platform presets (PlayStation, Xbox, Steam, GeForce NOW, Stadia, Luna)
- Custom port and port range support
- TCP protocol scanning
- Latency measurement
- Real-time port accessibility results

### Device Information
- Operating system detection
- Browser identification and version
- Screen resolution and viewport info
- CPU core count
- RAM estimation
- WebGL support detection
- Touch capability detection
- Detailed user agent breakdown

### Router Quick Access
- Popular router credentials reference
- Searchable database of default gateway IPs, usernames, and passwords
- Brands include TP-Link, Netgear, Asus, Linksys, D-Link, Ubiquiti, and more

### Guide Cards
- Display curated guides from your website
- Fully customizable titles, URLs, and descriptions
- Beautiful card-based layout

## Installation

1. Upload the `ultimate-nat-port-checker` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to 'NAT & Port Checker' in your WordPress admin panel
4. Customize colors, layout, and content
5. Add your custom guides
6. Use shortcodes to display the tool on your pages/posts

## Shortcodes

### Full Tool
```
[ultimate_nat_port_checker]
```
Displays the complete tool with header, footer, NAT checker, port checker, device info, router library, and guides.

### NAT Checker Only
```
[nat_checker_only]
```
Displays only the NAT detection component (standalone, no header/footer).

### Port Checker Only
```
[port_checker_only]
```
Displays only the port scanning component (standalone, no header/footer).

## Admin Panel

The comprehensive admin panel allows you to:

### Colors Tab
- Primary and secondary accent colors
- NAT Type 1, 2, 3 colors
- Port open/closed colors

### Layout Tab
- Container width (800-1920px)
- Font size presets: Small, Medium, Large

### Content Tab
- Header text
- Footer copyright text
- Privacy notice message
- Home button URL

### Guides Tab
- Add/remove guide cards
- Set title, URL, and description
- Unlimited guides supported

### Shortcodes Tab
- Copy-paste ready shortcode references

## Technical Details

### Backend (PHP)
- Clean OOP architecture with singleton patterns
- Secure AJAX handlers with nonce verification
- Sanitized inputs and escaped outputs
- WordPress coding standards compliant
- IP geolocation via ip-api.com
- Port checking via fsockopen
- Custom database table for port presets

### Frontend (JavaScript)
- jQuery-powered interactions
- WebRTC ICE candidate gathering
- Browser fingerprinting
- Device telemetry collection
- Theme persistence via localStorage
- Dynamic color application via CSS variables

### Styling (CSS)
- Modern CSS Grid and Flexbox layouts
- CSS custom properties for theming
- Smooth transitions and animations
- Fully responsive (mobile, tablet, desktop)
- No shadows or glowing effects (clean design)
- Small default font size for space efficiency
- Print-friendly styles

## Security & Privacy

- **No data storage**: All checks are performed in real-time
- **Client-side processing**: Device info collected via JavaScript (no server storage)
- **Transient caching**: IP lookups cached for 1 hour only
- **Secure AJAX**: Nonce-protected endpoints
- **User notification**: Privacy notice displayed prominently

## SEO Friendly

- Semantic HTML5 markup
- ARIA labels for accessibility
- Proper heading hierarchy
- Clean, minified assets
- Fast load times

## Browser Compatibility

- Chrome/Edge (Blink)
- Firefox (Gecko)
- Safari (WebKit)
- Opera
- Mobile browsers

## Frequently Asked Questions

### How accurate is the NAT type detection?
The plugin uses WebRTC STUN servers to gather ICE candidates and infer NAT behavior. Accuracy is high but depends on browser support and network configuration.

### Does this plugin store user data?
No. All network checks are performed in real-time and no personal data is stored on your server.

### Can I customize the colors?
Yes! The admin panel provides complete control over all accent colors, NAT type colors, and port status colors.

### Can I add my own cloud gaming platforms?
Currently, port presets are managed during plugin activation. Future versions will support custom preset management.

### Do the standalone shortcodes work anywhere?
Yes, both `[nat_checker_only]` and `[port_checker_only]` can be embedded in posts, pages, widgets, or custom templates.

## Changelog

### 1.0.0
- Initial release
- NAT type checker with WebRTC analysis
- Port scanner with cloud gaming presets
- Device information display
- Router login reference library
- Guide card system
- Dark/light theme toggle
- Fully responsive design
- Admin panel with extensive customization

## Support

For support, bug reports, or feature requests, please visit:
[Your Support URL]

## Credits

Developed with ❤️ for cloud gaming enthusiasts and network professionals.

## License

This plugin is licensed under the GPL v2 or later.
