# Cloud Network Analyzer - Developer Documentation

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [File Structure](#file-structure)
3. [Core Classes](#core-classes)
4. [API Endpoints](#api-endpoints)
5. [Shortcodes](#shortcodes)
6. [Hooks & Filters](#hooks--filters)
7. [JavaScript API](#javascript-api)
8. [Styling & Themes](#styling--themes)
9. [Extending the Plugin](#extending-the-plugin)

---

## Architecture Overview

The Cloud Network Analyzer plugin follows WordPress plugin development best practices with a modular, object-oriented architecture.

### Main Components

1. **Main Plugin Class** (`CloudNetworkAnalyzer`)
   - Singleton pattern
   - Handles initialization, hooks, and core functionality
   - Manages settings and admin interface

2. **Shortcode Handler** (`CNA_Shortcodes`)
   - Renders frontend UI components
   - Manages HTML templates for each tool

3. **API Handler** (`CNA_API_Handler`)
   - Interfaces with external IP detection APIs
   - Performs port checking and NAT detection
   - Device and browser detection

4. **Helper Functions** (`helpers.php`)
   - Utility functions for data sanitization
   - Port parsing and validation

### Data Flow

```
User Action (Frontend)
    ↓
JavaScript (AJAX)
    ↓
WordPress AJAX Handler (admin-ajax.php)
    ↓
Plugin AJAX Method (e.g., ajax_check_nat)
    ↓
API Handler (e.g., check_nat())
    ↓
External APIs / System Checks
    ↓
JSON Response
    ↓
JavaScript Processing
    ↓
DOM Update (Results Display)
```

---

## File Structure

```
cloud-network-analyzer/
│
├── cloud-network-analyzer.php        # Main plugin file with headers
│   ├── CloudNetworkAnalyzer class   # Core plugin class
│   ├── Activation hooks              # Setup on activation
│   └── Deactivation hooks            # Cleanup on deactivation
│
├── includes/
│   ├── class-shortcodes.php          # Shortcode rendering
│   │   └── CNA_Shortcodes class
│   ├── class-api-handler.php         # Network diagnostics
│   │   └── CNA_API_Handler class
│   └── helpers.php                   # Utility functions
│
├── assets/
│   ├── css/
│   │   ├── frontend-styles.css       # Public UI styles
│   │   └── admin-styles.css          # Admin panel styles
│   └── js/
│       └── frontend-scripts.js       # Interactive functionality
│
├── README.md                          # User documentation
├── DOCUMENTATION.md                   # Developer documentation
└── .gitignore                         # Version control exclusions
```

---

## Core Classes

### CloudNetworkAnalyzer

**Location:** `cloud-network-analyzer.php`

**Purpose:** Main plugin controller

#### Key Methods

- `get_instance()` - Singleton instance getter
- `load_dependencies()` - Loads required files
- `init_hooks()` - Registers WordPress hooks
- `add_admin_menu()` - Creates admin menu page
- `register_settings()` - Registers plugin settings
- `enqueue_frontend_assets()` - Loads CSS/JS on frontend
- `ajax_check_nat()` - AJAX handler for NAT checks
- `ajax_check_port()` - AJAX handler for port checks
- `ajax_get_device_info()` - AJAX handler for device detection
- `track_analytics()` - Records usage statistics
- `sanitize_settings()` - Sanitizes admin settings input

#### Settings Structure

```php
array(
    'enable_nat_checker' => 1,
    'enable_port_checker' => 1,
    'enable_device_info' => 1,
    'enable_guides' => 1,
    'enable_advanced_details' => 1,
    'enable_router_links' => 1,
    'theme_mode' => 'light', // light, dark, auto
    'color_palette' => 'blue-gray', // blue-gray, purple, green, red, orange
    'enable_analytics' => 0,
    'router_links' => '', // Custom router database
    'custom_guides' => '' // Custom educational content
)
```

---

### CNA_Shortcodes

**Location:** `includes/class-shortcodes.php`

**Purpose:** Handles shortcode rendering

#### Public Methods

- `init()` - Registers all shortcodes
- `full_analyzer($atts)` - Renders complete analyzer
- `nat_checker($atts)` - Renders NAT checker
- `port_checker($atts)` - Renders port checker
- `device_info($atts)` - Renders device info
- `educational_guides()` - Renders guides section

#### Usage Examples

```php
// In theme files:
echo do_shortcode('[cloud_network_analyzer]');

// With attributes:
echo do_shortcode('[cloud_network_analyzer theme="dark"]');
```

---

### CNA_API_Handler

**Location:** `includes/class-api-handler.php`

**Purpose:** Network diagnostics and API integration

#### Key Methods

##### NAT Detection

```php
public function check_nat()
```
Returns:
```php
array(
    'nat_type' => 'Open|Moderate|Strict',
    'nat_type_number' => 1|2|3,
    'public_ip' => '8.8.8.8',
    'isp' => 'ISP Name',
    'location' => 'City, Country',
    'ip_version' => 'IPv4|IPv6',
    'gateway_ip' => '192.168.1.1',
    'subnet_mask' => '255.255.255.0',
    'dns_servers' => 'DNS info',
    'upnp_status' => 'Enabled|Disabled',
    'nat_mapping' => 'Full Cone|Restricted Cone|Symmetric',
    'explanation' => 'Human-readable explanation'
)
```

##### Port Checking

```php
public function check_port($port, $protocol = 'tcp', $target_ip = '')
```
Returns:
```php
array(
    'port' => 3074,
    'protocol' => 'TCP|UDP|TCP/UDP',
    'status' => 'open|closed|unknown',
    'open' => true|false,
    'notes' => 'Additional information'
)
```

##### Device Detection

```php
public function get_device_info()
```
Returns:
```php
array(
    'device_type' => 'Desktop|Mobile|Tablet|Console',
    'browser' => 'Browser name',
    'os' => 'Operating system',
    'connection_type' => 'Wi-Fi|Ethernet|Cellular'
)
```

---

## API Endpoints

All endpoints use WordPress AJAX (`admin-ajax.php`).

### Check NAT Type

**Action:** `cna_check_nat`  
**Method:** POST  
**Authentication:** Nonce (`cna_nonce`)

**Request:**
```javascript
{
    action: 'cna_check_nat',
    nonce: 'nonce_value'
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "nat_type": "Open",
        "nat_type_number": 1,
        "public_ip": "8.8.8.8",
        "isp": "Google LLC",
        "location": "Mountain View, United States",
        "explanation": "Your NAT type is OPEN..."
    }
}
```

### Check Port

**Action:** `cna_check_port`  
**Method:** POST  
**Authentication:** Nonce (`cna_nonce`)

**Request:**
```javascript
{
    action: 'cna_check_port',
    nonce: 'nonce_value',
    port: 3074,
    protocol: 'tcp'
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "port": 3074,
        "protocol": "TCP",
        "status": "open",
        "open": true,
        "notes": "Xbox Gaming - Port is accessible"
    }
}
```

### Get Device Info

**Action:** `cna_get_device_info`  
**Method:** POST  
**Authentication:** Nonce (`cna_nonce`)

**Request:**
```javascript
{
    action: 'cna_get_device_info',
    nonce: 'nonce_value'
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "device_type": "Desktop",
        "browser": "Google Chrome",
        "os": "Windows 10/11",
        "connection_type": "Wi-Fi / Ethernet"
    }
}
```

---

## Shortcodes

### [cloud_network_analyzer]

Renders the complete network analyzer.

**Attributes:**
- `theme` - Override theme mode (light, dark, auto)

**Example:**
```
[cloud_network_analyzer]
[cloud_network_analyzer theme="dark"]
```

### [cloud_nat_checker]

Renders only the NAT Type Checker.

### [cloud_port_checker]

Renders only the Port Checker.

### [cloud_device_info]

Renders only Device & Connection Info.

---

## Hooks & Filters

### Actions

#### Plugin Initialization
```php
do_action('cna_init');
```
Fires after plugin is fully initialized.

#### Before NAT Check
```php
do_action('cna_before_nat_check');
```

#### After NAT Check
```php
do_action('cna_after_nat_check', $result);
```

### Filters

#### Modify NAT Result
```php
apply_filters('cna_nat_result', $result);
```

#### Modify Port Check Result
```php
apply_filters('cna_port_result', $result, $port, $protocol);
```

#### Add Custom Gaming Service
```php
apply_filters('cna_gaming_services', $services);
```

**Example Usage:**
```php
add_filter('cna_gaming_services', function($services) {
    $services[9999] = 'My Custom Game';
    return $services;
});
```

---

## JavaScript API

### Global Object

**Name:** `CNA`

### Methods

#### Initialize
```javascript
CNA.init()
```

#### Check NAT
```javascript
CNA.checkNAT(e)
```

#### Check Ports
```javascript
CNA.checkPorts(e)
```

#### Get Device Info
```javascript
CNA.getDeviceInfo(e)
```

#### Run Network Test
```javascript
CNA.runNetworkTest(e)
```

### Events

Custom events dispatched on document:

```javascript
document.addEventListener('cna:nat-checked', function(e) {
    console.log('NAT Type:', e.detail.nat_type);
});

document.addEventListener('cna:port-checked', function(e) {
    console.log('Port Results:', e.detail);
});
```

---

## Styling & Themes

### CSS Variables

The plugin uses CSS custom properties for theming:

```css
:root {
    --cna-primary: #2563eb;
    --cna-primary-hover: #1d4ed8;
    --cna-bg: #ffffff;
    --cna-text: #1e293b;
    /* ... */
}
```

### Theme Classes

- `.cna-theme-light` - Light mode
- `.cna-theme-dark` - Dark mode
- `.cna-theme-auto` - Auto (respects system preference)

### Color Palettes

Apply via `data-palette` attribute:
- `data-palette="blue-gray"` (default)
- `data-palette="purple"`
- `data-palette="green"`
- `data-palette="red"`
- `data-palette="orange"`

---

## Extending the Plugin

### Add Custom Port Preset

Edit `assets/js/frontend-scripts.js`:

```javascript
const presets = {
    // ... existing presets
    'my-game': {
        ports: [1234, 5678, 9012],
        protocol: 'tcp'
    }
};
```

Then add button in `includes/class-shortcodes.php`:

```php
<button class="cna-btn cna-btn-preset" data-service="my-game">
    My Game
</button>
```

### Add Custom Educational Section

Use the `cna_accordion_content` filter:

```php
add_filter('cna_accordion_content', function($content) {
    $content[] = array(
        'title' => 'My Custom Guide',
        'content' => '<p>Custom guide content here...</p>'
    );
    return $content;
});
```

### Custom Analytics Tracking

Hook into analytics events:

```php
add_action('cna_after_nat_check', function($result) {
    // Send to Google Analytics, Mixpanel, etc.
    // DO NOT log personal data without consent
});
```

---

## Best Practices

### Security

1. Always validate and sanitize user input
2. Use nonces for AJAX requests
3. Check user capabilities for admin functions
4. Escape output with `esc_html()`, `esc_attr()`, etc.

### Performance

1. Enqueue assets only when shortcodes are present
2. Cache API results when possible
3. Use transients for frequently accessed data
4. Minimize external API calls

### Accessibility

1. Use semantic HTML
2. Include ARIA labels where needed
3. Ensure keyboard navigation works
4. Provide text alternatives for icons

### Internationalization

The plugin is translation-ready. To translate:

1. Use POEdit or similar tool
2. Load `.po` file from plugin directory
3. Translate strings
4. Save as `.mo` file
5. Place in `/wp-content/languages/plugins/`

---

## Troubleshooting

### Common Issues

**Issue:** NAT check fails  
**Solution:** Check if server allows outbound HTTP requests. Enable cURL or `allow_url_fopen`.

**Issue:** Ports show as closed but are actually open  
**Solution:** Server firewall may block outbound connections. Use client-side testing when possible.

**Issue:** Styles not loading  
**Solution:** Clear WordPress cache and browser cache. Check for plugin conflicts.

---

## Support

For technical support or contributions, please refer to the plugin repository or contact the development team.

---

**Happy developing!** 🚀
