# Usage Guide - Ultimate NAT & Port Checker

## Quick Start

### 1. Installation & Activation
1. Upload the plugin to `/wp-content/plugins/ultimate-nat-port-checker/`
2. Activate via WordPress admin > Plugins
3. Navigate to "NAT & Port Checker" in the admin menu

### 2. Basic Configuration
- **General Tab**: Choose theme (Dark/Light), font size (Small/Medium/Large), container width
- **Display Tab**: Toggle sections on/off (Device Info, Router Logins, Guides, etc.)
- **Colors Tab**: Customize colors for NAT types and UI elements

### 3. Adding to Pages
Insert one of these shortcodes in your page/post:

```
[ultimate_nat_port_checker]        // Full tool with all sections
[ultimate_nat_checker]             // NAT checker only
[ultimate_port_checker]            // Port checker only
```

## Advanced Configuration

### Custom Port Presets
**Location**: Admin > Port Checker Tab > Custom Port Presets

Format: `Label:ports` (one per line)

**Example**:
```
CloudXR:48010,48011
Parsec:8000-8010
Luna Display:1233,1234
```

### Custom STUN Servers
**Location**: Admin > NAT Checker Tab > STUN Servers

Add one server per line:
```
stun:stun.l.google.com:19302
stun:stun1.l.google.com:19302
stun:stun2.l.google.com:19302
```

### Custom Styling
**Location**: Admin > Advanced Tab > Custom CSS

**Example**:
```css
.unpc-wrapper {
    border: 3px solid #ff6b6b;
}

.unpc-section-title {
    color: #4ecdc4;
}
```

### Custom JavaScript
**Location**: Admin > Advanced Tab > Custom JavaScript

**Example**:
```javascript
jQuery(function($){
    console.log('NAT Checker loaded!');
    
    // Custom logic after NAT check
    $(document).on('unpc_nat_checked', function(e, result){
        console.log('NAT Type:', result.type);
    });
});
```

## NAT Type Interpretations

### Type 1 (Open) - GREEN
- **Best for gaming**: Lowest latency, best connectivity
- **Meaning**: Full Cone NAT or no NAT
- **Gaming Impact**: Can host game servers, direct peer-to-peer connections
- **Recommendation**: No action needed

### Type 2 (Moderate) - ORANGE
- **Good for gaming**: Minor restrictions
- **Meaning**: Address-restricted or Port-restricted NAT
- **Gaming Impact**: Can connect to most players, some hosting limitations
- **Recommendation**: Consider enabling UPnP on router

### Type 3 (Strict) - RED
- **Challenging for gaming**: High restrictions
- **Meaning**: Symmetric NAT or highly restrictive firewall
- **Gaming Impact**: Connection issues, cannot host, limited matchmaking
- **Recommendation**: 
  - Enable UPnP
  - Configure port forwarding
  - Consider DMZ (security risk)
  - Contact ISP about NAT type

## Port Checking Guide

### Single Port
```
80
```

### Multiple Ports
```
80,443,8080
```

### Port Range
```
27015-27030
```

### Mixed
```
80,443,3000-3100,8080
```

### Common Gaming Ports

**Xbox Live**: 3074, 53, 80, 500, 3544, 4500  
**PlayStation**: 3478, 3479, 3480, 465, 983, 1935, 3658, 10070-10080  
**Steam**: 27015-27030, 27036-27037  
**Call of Duty**: 3074, 3544, 4500  
**Fortnite**: 80, 443, 3478-3479, 5222, 5795-5847  
**League of Legends**: 5000-5500, 8393-8400, 2099, 5223  
**Overwatch**: 80, 1119, 3724, 6113, 80, 1119, 3724, 6113  

## Router Configuration

### Accessing Router
1. Click your router brand in "Router Login & Setup" section
2. Use provided default credentials (may have been changed)
3. If credentials don't work, check router label or manual

### Enabling UPnP
1. Log into router admin panel
2. Navigate to: Advanced Settings > NAT > UPnP
3. Enable UPnP
4. Save and reboot router
5. Re-run NAT check

### Port Forwarding (Manual)
1. Log into router
2. Find "Port Forwarding" or "Virtual Server"
3. Add rule:
   - **Service Name**: Gaming Console
   - **Port Range**: 3074-3074
   - **Local IP**: Your device IP (check in Device Details)
   - **Protocol**: TCP & UDP
4. Save and apply

### QoS Configuration
1. Log into router
2. Navigate to QoS settings
3. Enable QoS
4. Add rule:
   - **Device**: Your gaming device (by IP or MAC)
   - **Priority**: Highest
   - **Application**: Gaming or Custom Ports
5. Save and apply

## Troubleshooting

### NAT Check Fails
- **Browser Issue**: Try different browser (Chrome/Edge recommended)
- **VPN/Proxy**: Disable temporarily
- **Firewall**: Allow WebRTC connections
- **Browser Settings**: Enable camera/microphone permissions (for WebRTC)

### Port Check Shows All Closed
- **Normal Behavior**: Plugin simulates port checks (server-side limitation)
- **Alternative**: Use external service like canyouseeme.org
- **Router**: Ensure port forwarding configured correctly

### Device Info Not Loading
- **ISP Blocking**: Some ISPs block IP lookup services
- **Cache Issue**: Clear browser cache
- **API Limit**: Free IP lookup API has rate limits

### Styling Issues
- **Theme Conflict**: Try switching to default WordPress theme
- **Cache**: Clear WordPress cache if using caching plugin
- **CSS Priority**: Add `!important` to custom CSS if needed

## Best Practices

### For Cloud Gaming
1. **Always check NAT** before gaming sessions
2. **Use wired connection** when possible
3. **Close bandwidth-heavy apps** during gaming
4. **Enable QoS** on router for gaming device
5. **Keep router firmware updated**

### For Server Hosting
1. **Achieve Type 1 NAT** for best results
2. **Forward all required ports** manually
3. **Use static local IP** for gaming device/server
4. **Configure firewall rules** to allow traffic
5. **Test from external network** to verify

### Performance Tips
1. **Reduce container width** on slower devices (Admin > General)
2. **Disable animations** for better performance
3. **Hide unused sections** via Display tab
4. **Enable caching** for IP lookups (Admin > Advanced)
5. **Limit port scan ranges** to improve speed

## API Usage

### Events (Custom JavaScript)

```javascript
// NAT check completed
$(document).on('unpc_nat_checked', function(e, data){
    console.log('NAT Type Level:', data.level); // 1, 2, or 3
    console.log('NAT Type:', data.type);
    console.log('Description:', data.description);
});

// Port check completed
$(document).on('unpc_port_checked', function(e, data){
    console.log('Open Count:', data.open_count);
    console.log('Results:', data.results);
});

// Device info loaded
$(document).on('unpc_device_loaded', function(e, data){
    console.log('IP:', data.ip);
    console.log('Country:', data.country);
});
```

### Programmatic Access (PHP)

```php
// Get current settings
$theme = get_option('unpc_default_theme', 'dark');
$font_size = get_option('unpc_font_size', 'medium');

// Update settings
update_option('unpc_container_width', '1400');
update_option('unpc_show_device_info', '0');

// Get defaults
$defaults = UNPC_Admin_Settings::get_defaults();
```

## Support & Resources

- **Documentation**: https://cloudgamingblog.com/docs
- **Support Forum**: https://cloudgamingblog.com/support
- **Bug Reports**: https://github.com/cloudgamingblog/ultimate-nat-port-checker/issues

## Credits & License

Developed by Cloud Gaming Blog  
Licensed under GPL v2 or later  
© 2026 All Rights Reserved
