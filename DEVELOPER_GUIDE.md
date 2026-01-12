# Developer Guide

Extend and customize the Cloud Gaming Readiness Test plugin.

## Plugin Architecture Overview

The plugin follows a standard WordPress architecture:

```
Main Plugin File (PHP)
    ↓
Template Layer (PHP)
    ↓
Assets (CSS + JS)
    ↓
Browser Execution (JavaScript)
```

## Customization Options

### 1. Modify Test Duration

Change how long the test runs:

```php
// In your theme's functions.php or a custom plugin
add_filter('cgrt_test_duration', function($duration) {
    return 45000; // 45 seconds instead of 30
});
```

### 2. Change Sample Rate

Increase or decrease measurement frequency:

```php
add_filter('cgrt_sample_rate', function($rate) {
    return 500; // 2 samples per second
});
```

### 3. Add Custom Test Endpoints

Add your own servers to test against:

```php
add_filter('cgrt_test_endpoints', function($endpoints) {
    $endpoints[] = 'https://your-gaming-server.com/ping';
    $endpoints[] = 'https://your-cdn.example.com/trace';
    return $endpoints;
});
```

### 4. Customize Scoring Weights

Edit `assets/js/main.js` and find the score calculation:

```javascript
// Around line 300
const latencyScore = 40;    // Change this
const jitterScore = 30;     // Change this
const lossScore = 20;       // Change this
const stabilityScore = 10;  // Change this
```

### 5. Override Templates

Create a `cloud-gaming-test/` folder in your theme:

```
wp-content/themes/your-theme/
└── cloud-gaming-test/
    └── test-widget.php  # Copy from plugin
```

WordPress will automatically use your template.

## Styling Customization

### CSS Variables

The plugin uses CSS variables for easy theming:

```css
/* In your theme's style.css */
:root {
    --cgrt-primary: #8b5cf6;      /* Change primary color */
    --cgrt-success: #10b981;      /* Change success color */
    --cgrt-warning: #f59e0b;      /* Change warning color */
    --cgrt-danger: #ef4444;       /* Change danger color */
    --cgrt-border-radius: 16px;   /* Change border radius */
}
```

### Dark Mode Override

Force dark mode:

```css
.cgrt-container[data-theme="dark"] {
    --cgrt-white: #171717;
    --cgrt-neutral-50: #262626;
    /* ... more variables */
}
```

### Custom Width/Height

Using shortcode attributes:

```php
// Full width
echo do_shortcode('[cloud_gaming_test width="100%"]');

// Fixed width
echo do_shortcode('[cloud_gaming_test width="900px" height="600px"]');
```

## JavaScript Hooks

### Before Test Starts

```javascript
// Add custom code before test begins
document.addEventListener('cgrt:test:start', function(e) {
    console.log('Test starting!');
    // Your custom code
});
```

### After Test Completes

```javascript
// Access results after test
document.addEventListener('cgrt:test:complete', function(e) {
    const results = e.detail;
    console.log('Score:', results.score.total);
    console.log('Latency:', results.metrics.avgLatency);
    // Send to analytics, etc.
});
```

You'll need to dispatch these events in `main.js`:

```javascript
// After calculating results
const event = new CustomEvent('cgrt:test:complete', {
    detail: { score: state.score, metrics: state.metrics }
});
document.dispatchEvent(event);
```

## Adding Custom Metrics

### 1. Add Measurement Logic

In `assets/js/main.js`, add to the `measureLatency` function:

```javascript
async function measureLatency() {
    // ... existing code ...

    // Add your custom metric
    const dnsTime = await measureDNSLatency();
    sample.dnsLatency = dnsTime;

    return sample;
}

async function measureDNSLatency() {
    // Your DNS measurement logic
    const start = performance.now();
    // ... DNS lookup measurement ...
    return performance.now() - start;
}
```

### 2. Add to Scoring

Update the scoring calculation:

```javascript
function calculateReadinessScore(metrics) {
    // ... existing code ...
    
    // Add DNS score
    let dnsScore = 0;
    if (metrics.dnsLatency <= 20) {
        dnsScore = 10;
    } else if (metrics.dnsLatency <= 50) {
        dnsScore = 7;
    }
    // ... more conditions ...
    
    totalScore = latencyScore + jitterScore + lossScore + stabilityScore + dnsScore;
    // ... rest of function ...
}
```

### 3. Update UI Display

Add to the results display in `templates/test-widget.php`:

```html
<div class="cgrt-metric-card cgrt-metric-dns">
    <div class="cgrt-metric-card-header">
        <span class="cgrt-metric-card-title">DNS Latency</span>
    </div>
    <div class="cgrt-metric-card-value">--</div>
    <div class="cgrt-metric-card-sub">Time to resolve domain</div>
</div>
```

## Creating a Child Plugin

For significant customizations, create a child plugin:

```php
<?php
/**
 * Plugin Name: Cloud Gaming Test - Custom
 * Plugin URI: https://your-website.com
 * Description: Customizations for Cloud Gaming Readiness Test
 * Version: 1.0.0
 * Author: Your Name
 */

// Ensure parent plugin is active
add_action('admin_init', function() {
    if (!is_plugin_active('cloud-gaming-readiness-test/cloud-gaming-readiness-test.php')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>Cloud Gaming Readiness Test must be active.</p></div>';
        }
    }
});

// Add custom filters
add_filter('cgrt_test_duration', function($duration) {
    return 60000; // 1 minute test
});

// Add custom endpoints
add_filter('cgrt_test_endpoints', function($endpoints) {
    $endpoints[] = 'https://your-custom-server.com/test';
    return $endpoints;
});

// Enqueue custom styles
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'cgrt-custom',
        plugin_dir_url(__FILE__) . 'custom.css',
        array('cgrt-styles'),
        '1.0.0'
    );
});
```

## Adding Gutenberg Block Variations

Create custom block presets:

```javascript
// In a separate JS file loaded by your theme
wp.blocks.registerBlockVariation('cloud-gaming-test/widget', {
    name: 'cloud-gaming-test/compact',
    title: 'Compact Test',
    description: 'A compact version of the test',
    attributes: {
        width: '100%',
        advanced: false,
        theme: 'light'
    }
});

wp.blocks.registerBlockVariation('cloud-gaming-test/widget', {
    name: 'cloud-gaming-test/full-width-dark',
    title: 'Full Width Dark',
    description: 'Full width dark theme test',
    attributes: {
        width: '100%',
        theme: 'dark',
        advanced: true
    }
});
```

## Internationalization

### Adding Translations

1. Create translation files in `languages/`:

```
languages/
├── cloud-gaming-test-en_US.po
├── cloud-gaming-test-en_US.mo
├── cloud-gaming-test-es_ES.po
├── cloud-gaming-test-es_ES.mo
└── ...
```

2. Make strings translatable:

```php
// In PHP files
__('String to translate', 'cloud-gaming-test');

// In JavaScript (use wp_localize_script)
wp_localize_script('cgrt-main', 'cgrtTranslations', array(
    'startTest' => __('Start Test', 'cloud-gaming-test'),
    'results' => __('Results', 'cloud-gaming-test'),
));
```

## Testing Your Customizations

### 1. Unit Testing

```javascript
// Test scoring logic
describe('Cloud Gaming Test Scoring', function() {
    it('should calculate latency score correctly', function() {
        const metrics = { avgLatency: 30 };
        const score = calculateLatencyScore(metrics);
        expect(score).toBe(35); // Good tier
    });
});
```

### 2. Integration Testing

```php
// Test WordPress integration
class CGRT_Tests extends WP_UnitTestCase {
    function test_shortcode_exists() {
        $output = do_shortcode('[cloud_gaming_test]');
        $this->assertContains('cgrt-container', $output);
    }
    
    function test_filters_work() {
        add_filter('cgrt_test_duration', function() { return 1000; });
        $settings = get_option('cgrt_settings');
        $this->assertEquals(1000, $settings['test_duration']);
    }
}
```

### 3. Browser Testing

Test in multiple browsers:
- Chrome (DevTools: Network throttling)
- Firefox
- Safari
- Edge
- Mobile browsers (Chrome on Android, Safari on iOS)

## Performance Optimization

### 1. Minify Assets

Add build scripts to your package.json:

```json
{
    "scripts": {
        "build:css": "postcss assets/css/style.css -o assets/css/style.min.css",
        "build:js": "terser assets/js/main.js -o assets/js/main.min.js"
    }
}
```

Update the plugin to load minified assets:

```php
wp_enqueue_style(
    'cgrt-styles',
    defined('SCRIPT_DEBUG') && SCRIPT_DEBUG 
        ? CGRT_PLUGIN_URL . 'assets/css/style.css'
        : CGRT_PLUGIN_URL . 'assets/css/style.min.css',
    array(),
    CGRT_VERSION
);
```

### 2. Conditional Loading

Only load assets when needed:

```php
add_action('wp_enqueue_scripts', function() {
    global $post;
    
    // Only load if shortcode is present
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'cloud_gaming_test')) {
        // Enqueue assets
    }
});
```

### 3. Caching

Add caching for expensive operations:

```php
// Cache endpoint list for 1 hour
$cached_endpoints = get_transient('cgrt_endpoints');
if (false === $cached_endpoints) {
    $cached_endpoints = calculate_endpoints();
    set_transient('cgrt_endpoints', $cached_endpoints, HOUR_IN_SECONDS);
}
```

## Security Best Practices

### 1. Sanitize User Input

```php
function cgrt_sanitize_shortcode_atts($atts) {
    return shortcode_atts(array(
        'width' => sanitize_text_field($atts['width'] ?? '100%'),
        'height' => sanitize_text_field($atts['height'] ?? 'auto'),
        'theme' => in_array($atts['theme'], ['light', 'dark']) ? $atts['theme'] : 'light'
    ), $atts);
}
```

### 2. Escape Output

```php
$output = '<div class="cgrt-container" data-theme="' . esc_attr($theme) . '">';
$output .= '<h1>' . esc_html($title) . '</h1>';
echo $output;
```

### 3. Nonce Verification

```php
// For AJAX requests
if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cgrt_nonce')) {
    wp_send_json_error('Invalid nonce');
}
```

## Debugging

### Enable Debug Mode

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Console Logging

Add debugging to JavaScript:

```javascript
// In main.js
const DEBUG = true;

function log(message) {
    if (DEBUG && console) {
        console.log('[CGRT]', message);
    }
}

// Use throughout code
log('Test started');
log('Sample:', sample);
```

### WordPress Debug Bar

Integrate with Debug Bar plugin:

```php
add_filter('debug_bar_panels', function($panels) {
    $panels[] = new CGRT_Debug_Bar_Panel();
    return $panels;
});
```

## Contributing Back

If you create useful customizations, consider:

1. Forking the repository
2. Creating a feature branch
3. Submitting a pull request

## Support

For development questions:

1. Check the ARCHITECTURE.md file
2. Review inline code comments
3. Test with the demo.html file
4. Check browser console for JavaScript errors
5. Review WordPress debug logs for PHP errors

## Resources

- [WordPress Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [JavaScript Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)
- [HTML5 Canvas](https://developer.mozilla.org/en-US/docs/Web/API/Canvas_API)
- [CSS Variables](https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_custom_properties)
