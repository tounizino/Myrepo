# Myrepo

## Ultimate NAT & Port Checker WordPress Plugin

This repository contains the **Ultimate NAT & Port Checker**, a comprehensive WordPress plugin designed for cloud gaming websites and tech blogs.

### Features

- 🎮 Advanced NAT type detection (Type 1/2/3) using WebRTC and STUN servers
- 🔌 Comprehensive port diagnostics with cloud gaming presets (PlayStation, Xbox, GeForce NOW, etc.)
- 🛠️ Popular router login shortcuts and configuration guides
- 📱 Device information detection (browser, OS, connection type, bandwidth)
- 🎨 Modern 2026 design with light/dark themes, responsive layout, and smooth animations
- ⚙️ Powerful admin panel for customization
- 🌐 Fully internationalization-ready

### Quick Start

1. Navigate to the plugin directory:
   ```bash
   cd ultimate-nat-port-checker
   ```

2. Install in WordPress:
   - Copy the `ultimate-nat-port-checker` directory to `wp-content/plugins/`
   - Activate via WordPress admin panel
   - Use shortcode `[nat_port_checker]` on any page

### Documentation

See the full documentation in [ultimate-nat-port-checker/README.md](ultimate-nat-port-checker/README.md)

### Technical Stack

- **Frontend**: Vanilla JavaScript (ES6+), Modern CSS (Grid, Flexbox, Custom Properties)
- **Backend**: PHP 7.4+, WordPress REST API
- **NAT Detection**: WebRTC (RTCPeerConnection, STUN servers)
- **Port Scanning**: PHP native socket functions

### Admin Settings

Configure the plugin at **WordPress Admin → Settings → NAT & Port Checker**

---

**Licensed under GPL v2 or later**
