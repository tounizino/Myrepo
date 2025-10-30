# Cloud Network Analyzer - WordPress Plugin

**Version:** 1.0.0  
**Author:** Cloud Gaming Tools Team  
**Requires WordPress:** 5.0+  
**Tested up to:** 6.4  
**License:** GPL v2 or later  

Professional network diagnostics tool for cloud gaming enthusiasts. Check NAT type, scan ports, analyze device connections, and access comprehensive gaming network guides.

---

## 🚀 Features

### Core Tools

#### 1. **NAT Type Checker**
- Automatically detect and display NAT Type (Open, Moderate, Strict)
- Visual color-coded indicators:
  - 🟢 **Green** → Open NAT (Type 1)
  - 🟡 **Yellow** → Moderate NAT (Type 2)
  - 🔴 **Red** → Strict NAT (Type 3)
- Display public/private IP, ISP, location, and IP version
- Advanced details: Gateway IP, Subnet, DNS, UPnP status, NAT mapping behavior
- Contextual tooltips explaining technical terms
- NAT explanation section tailored for cloud gaming

#### 2. **Port Checker**
- Test single or multiple ports (ranges supported: `3074-3080`)
- Preset port groups for major cloud gaming services:
  - **GeForce NOW**
  - **Xbox Cloud Gaming**
  - **PlayStation Remote Play**
  - **Boosteroid**
  - **Shadow PC**
- Real-time port status with animated transitions
- Support for TCP, UDP, or both protocols
- Detailed notes for each port result

#### 3. **Device & Connection Information**
- Auto-detect device type (Desktop, Mobile, Tablet, Console)
- Identify OS and browser
- Display connection type (Wi-Fi, Ethernet, Cellular)
- Network performance test with ping, jitter, and packet loss metrics

### Admin Dashboard

- **Full Settings Panel**: Toggle visibility of each section
- **Theme Customization**: Light, Dark, or Auto mode with multiple color palettes
- **Shortcode Support**: Embed specific tools in posts/pages
  - `[cloud_network_analyzer]` - Full tool
  - `[cloud_nat_checker]` - NAT checker only
  - `[cloud_port_checker]` - Port checker only
  - `[cloud_device_info]` - Device info only
- **Analytics Dashboard**: Track usage statistics (optional)
- **Custom Router Database**: Manage router login credentials

### Educational Content

Comprehensive gaming network guides included:
- How to Fix Strict NAT Types (UPnP, Port Forwarding, DMZ)
- Router Login Database with default IPs and credentials
- Step-by-step Port Forwarding guide
- QoS & DSCP Tagging for gaming traffic priority
- Cloud Gaming Optimization tips

---

## 📦 Installation

### Method 1: WordPress Admin Panel (Recommended)

1. Download the plugin ZIP file
2. Go to **WordPress Admin** → **Plugins** → **Add New**
3. Click **Upload Plugin** and select the ZIP file
4. Click **Install Now** and then **Activate**

### Method 2: Manual Installation

1. Upload the `cloud-network-analyzer` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Navigate to **Network Analyzer** in the WordPress admin sidebar

---

## 🎯 Usage

### Using Shortcodes

Insert any of the following shortcodes in your posts, pages, or widgets:

```
[cloud_network_analyzer]
```
Displays the complete network analyzer with all tools.

```
[cloud_nat_checker]
```
Displays only the NAT Type Checker.

```
[cloud_port_checker]
```
Displays only the Port Checker.

```
[cloud_device_info]
```
Displays only Device & Connection Information.

### Customization

Customize theme and settings via **WordPress Admin** → **Network Analyzer**.

Available options:
- Enable/disable individual sections
- Choose theme mode (Light/Dark/Auto)
- Select color palette (Blue Gray, Purple, Green, Red, Orange)
- Enable/disable analytics tracking
- Manage router login database
- Add custom guide content

---

## 🛠️ Technical Details

### Requirements

- PHP 7.2 or higher
- WordPress 5.0 or higher
- cURL or allow_url_fopen enabled for external API calls

### APIs Used

The plugin uses free public APIs for IP and location detection:
- `ipapi.co`
- `ipinfo.io`
- `ipify.org`

Fallback mechanisms ensure functionality even if one API is unavailable.

### File Structure

```
cloud-network-analyzer/
├── cloud-network-analyzer.php    # Main plugin file
├── includes/
│   ├── class-shortcodes.php      # Shortcode handlers
│   ├── class-api-handler.php     # API integration & network checks
│   └── helpers.php                # Utility functions
├── assets/
│   ├── css/
│   │   ├── frontend-styles.css   # Public-facing styles
│   │   └── admin-styles.css      # Admin panel styles
│   └── js/
│       └── frontend-scripts.js   # JavaScript for interactive features
└── README.md
```

### Security

- All AJAX requests use WordPress nonces for CSRF protection
- Input sanitization and validation on all user inputs
- Settings escaped before output
- No personal data collected (unless analytics enabled)

---

## 🎨 Design

- Modern, gamer-oriented UI with flat design
- Responsive and mobile-friendly
- Smooth CSS animations
- Light and dark mode support
- Multiple color palettes
- Accessibility-focused tooltips

---

## 🔧 Development

### Extending the Plugin

The plugin is modular and easy to extend:

**Add Custom Port Presets:**
Edit `frontend-scripts.js` and add to the `presets` object:

```javascript
'custom-service': {
    ports: [1234, 5678],
    protocol: 'tcp'
}
```

**Add Custom Router Brands:**
Use the admin panel to add router credentials in the format:
```
Brand | IP | Username | Password
```

**Customize Guides:**
Use the built-in WordPress editor in admin settings to add custom guides.

---

## ❓ FAQ

### Q: Does this plugin collect any personal data?
**A:** No. The plugin only tracks usage statistics if you enable analytics (disabled by default). No personal information like IP addresses or browsing history is stored.

### Q: Why can't UDP ports be accurately tested?
**A:** UDP is a connectionless protocol. Without a listening service responding, it's impossible to definitively determine if a UDP port is open from a remote client.

### Q: Will this work with my theme?
**A:** Yes! The plugin uses WordPress-native classes and is designed to work with any theme. It includes its own styling to ensure consistency.

### Q: Can I use this on multiple sites?
**A:** Yes, under the GPL v2 license you can install this plugin on as many sites as you like.

### Q: Does this plugin make my site slower?
**A:** No. Assets are only loaded on pages where shortcodes are used. The plugin is optimized for performance.

---

## 🐛 Support & Bug Reports

For bug reports, feature requests, or support:
- Open an issue on GitHub (if applicable)
- Contact the plugin author

---

## 📝 Changelog

### Version 1.0.0
- Initial release
- NAT Type Checker with advanced details
- Port Checker with preset gaming services
- Device & Connection Information detector
- Network performance test (ping, jitter, packet loss)
- Educational guides for cloud gaming optimization
- Admin dashboard with full settings control
- Light/Dark mode with multiple color palettes
- Shortcode support for flexible embedding
- Analytics dashboard (optional)
- Responsive design for all devices

---

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2024 Cloud Gaming Tools Team

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

## 🌟 Credits

Developed with ❤️ for the cloud gaming community.

**Technologies Used:**
- WordPress Plugin API
- jQuery for interactivity
- Pure CSS for animations and responsive design
- External IP APIs for network detection

---

## 🚀 Future Enhancements (Roadmap)

- PDF export of test results
- Historical test data visualization
- WebRTC-based STUN/TURN server testing
- Advanced MTU and packet fragmentation testing
- Integration with popular gaming platforms
- Multi-language support

---

**Enjoy optimized cloud gaming!** 🎮
