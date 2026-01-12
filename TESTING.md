# Testing Guide

Comprehensive testing procedures for the Cloud Gaming Readiness Test plugin.

## Manual Testing Checklist

### Installation Tests

- [ ] Plugin installs successfully via WordPress admin
- [ ] Plugin installs successfully via FTP
- [ ] Plugin activates without errors
- [ ] Plugin shows up in Plugins list
- [ ] Plugin deactivates properly
- [ ] Plugin uninstalls cleanly (options removed)
- [ ] No PHP errors in debug log during installation

### Basic Functionality Tests

- [ ] Shortcode `[cloud_gaming_test]` renders widget
- [ ] Shortcode attributes work correctly (width, height, theme)
- [ ] Gutenberg block appears in block editor
- [ ] Gutenberg block inserts correctly
- [ ] Widget displays in different page layouts
- [ ] Widget works in WordPress themes (Twenty Twenty-One, Twenty Twenty-Two, etc.)
- [ ] Widget works with page builders (Elementor, Divi, etc.)

### User Interface Tests

- [ ] Welcome screen displays correctly
- [ ] Start button is visible and clickable
- [ ] Feature icons render correctly
- [ ] Text is readable and properly formatted
- [ ] Mobile responsive design works (320px - 1920px)
- [ ] Dark theme applies correctly
- [ ] Light theme applies correctly
- [ ] No horizontal scrolling on mobile
- [ ] Touch targets are large enough (44px minimum)

### Test Execution Tests

- [ ] Test starts when Start button clicked
- [ ] Progress bar fills smoothly
- [ ] Progress messages update correctly
- [ ] Live metrics display during test
- [ ] Test completes in expected time (~30 seconds)
- [ ] Results screen appears after test completes
- [ ] Restart test button works
- [ ] Can run test multiple times in succession

### Results Display Tests

- [ ] Score gauge displays correctly
- [ ] Score number is accurate
- [ ] Verdict text matches score range
- [ ] All 4 metric cards display
- [ ] Metric values are calculated correctly
- [ ] Metric units are displayed (ms, %)
- [ ] Quality grid shows correct statuses
- [ ] Charts render without errors
- [ ] Latency timeline chart draws correctly
- [ ] Jitter distribution chart draws correctly
- [ ] Recommendations appear when needed
- [ ] Priority indicators show correctly

### Data Accuracy Tests

#### Latency Tests

- [ ] Average latency calculation is correct
- [ ] Minimum latency is identified correctly
- [ ] Maximum latency is identified correctly
- [ ] Range displays correctly
- [ ] Failed samples don't skew results

#### Jitter Tests

- [ ] Jitter calculation follows standard formula
- [ ] Consistent latency shows low jitter
- [ ] Variable latency shows high jitter
- [ ] Single spike doesn't over-influence result

#### Packet Loss Tests

- [ ] Zero failed requests = 0% packet loss
- [ ] All failed requests = 100% packet loss
- [ ] Partial failures calculate correctly
- [ ] Packet loss score penalizes appropriately

#### Stability Tests

- [ ] Consistent connection shows high stability
- [ ] Variable connection shows low stability
- [ ] Spikes reduce stability score
- [ ] Outliers are identified correctly

### Scoring System Tests

- [ ] Excellent (90-100) displays green
- [ ] Good (75-89) displays blue
- [ ] Fair (50-74) displays yellow
- [ ] Poor (25-49) displays red
- [ ] Unplayable (0-24) displays red
- [ ] Total score never exceeds 100
- [ ] Total score never goes below 0
- [ ] Score tiers match thresholds

### Quality Prediction Tests

- [ ] Excellent network supports all qualities
- [ ] Good network supports up to 1440p
- [ ] Fair network supports up to 1080p
- [ ] Poor network supports only 720p
- [ ] Unplayable network supports nothing
- [ ] Recommendations align with quality predictions

### Recommendation Engine Tests

- [ ] High latency triggers Ethernet recommendation
- [ ] High jitter triggers congestion recommendation
- [ ] Packet loss triggers cable check recommendation
- [ ] Spikes trigger interference recommendation
- [ ] Multiple issues show multiple recommendations
- [ ] Priorities are assigned correctly (high/medium/low)
- [ ] Recommendations are cloud gaming specific

### Browser Compatibility Tests

#### Chrome

- [ ] Latest version works correctly
- [ ] Version 90+ works correctly
- [ ] Chrome DevTools shows no errors
- [ ] Performance is acceptable
- [ ] Canvas renders correctly

#### Firefox

- [ ] Latest version works correctly
- [ ] Version 88+ works correctly
- [ ] Console shows no errors
- [ ] Performance is acceptable
- [ ] Canvas renders correctly

#### Safari

- [ ] Latest version works correctly
- [ ] Version 14+ works correctly
- [ ] Web Inspector shows no errors
- [ ] Performance is acceptable
- [ ] Canvas renders correctly

#### Edge

- [ ] Latest version works correctly
- [ ] Version 90+ works correctly
- [ ] DevTools shows no errors
- [ ] Performance is acceptable
- [ ] Canvas renders correctly

### Mobile Tests

#### iOS (Safari)

- [ ] iPhone SE works correctly
- [ ] iPhone 12 works correctly
- [ ] iPhone 14 works correctly
- [ ] iPad works correctly
- [ ] Touch interactions work smoothly
- [ ] No horizontal scrolling
- [ ] Readable at normal zoom

#### Android (Chrome)

- [ ] Samsung Galaxy S21 works correctly
- [ ] Google Pixel works correctly
- [ ] Various screen sizes tested
- [ ] Touch interactions work smoothly
- [ ] No horizontal scrolling
- [ ] Readable at normal zoom

### Performance Tests

- [ ] Page load time is < 2 seconds
- [ ] Test execution doesn't freeze UI
- [ ] Charts render quickly (< 500ms)
- [ ] Memory usage is reasonable
- [ ] No memory leaks during repeated tests
- [ ] CPU usage is reasonable
- [ ] Network bandwidth usage is minimal

### Accessibility Tests

- [ ] Keyboard navigation works
- [ ] All interactive elements have focus indicators
- [ ] Screen readers announce test progress
- [ ] Screen readers read results correctly
- [ ] Color contrast meets WCAG AA standards
- [ ] Text can be resized up to 200%
- [ ] No seizures-inducing animations
- [ ] ARIA labels are present where needed

### Security Tests

- [ ] No XSS vulnerabilities
- [ ] No SQL injection vulnerabilities
- [ ] CSRF protection works
- [ ] Nonce verification works
- [ ] All output is properly escaped
- [ ] All input is sanitized
- [ ] HTTPS endpoints are used
- [ ] No mixed content warnings

### Edge Case Tests

- [ ] Test works with no internet connection (fails gracefully)
- [ ] Test works with very slow connection
- [ ] Test works with very fast connection
- [ ] Test works when all samples fail
- [ ] Test works when samples have extreme variance
- [ ] Test works when user navigates away during test
- [ ] Test works when user refreshes page
- [ ] Test works with browser extensions installed
- [ ] Test works with popup blockers enabled

### Print Functionality Tests

- [ ] Print results button works
- [ ] Print preview shows all content
- [ ] Print layout is clean
- [ ] Background graphics print correctly
- [ ] Text is readable in print
- [ ] Charts print correctly

### Integration Tests

#### WordPress Core

- [ ] Works with latest WordPress version
- [ ] Works with WordPress 5.8+
- [ ] Compatible with REST API
- [ ] Compatible with block editor
- [ ] Compatible with classic editor

#### Popular Plugins

- [ ] Works with caching plugins (WP Rocket, W3 Total Cache)
- [ ] Works with security plugins (Wordfence, iThemes Security)
- [ ] Works with SEO plugins (Yoast SEO, Rank Math)
- [ ] Works with page builders (Elementor, Divi, Beaver Builder)

## Automated Testing

### Unit Tests (JavaScript)

```javascript
// test/scoring.test.js

describe('Scoring System', () => {
    test('calculates latency score correctly', () => {
        const metrics = { avgLatency: 30 };
        const score = calculateLatencyScore(metrics);
        expect(score).toBe(35);
    });

    test('excellent latency gets max score', () => {
        const metrics = { avgLatency: 15 };
        const score = calculateLatencyScore(metrics);
        expect(score).toBe(40);
    });

    test('unplayable latency gets min score', () => {
        const metrics = { avgLatency: 150 };
        const score = calculateLatencyScore(metrics);
        expect(score).toBe(5);
    });
});

describe('Jitter Calculation', () => {
    test('calculates jitter for consistent latency', () => {
        const latencies = [20, 20, 20, 20, 20];
        const jitter = calculateJitter(latencies);
        expect(jitter).toBe(0);
    });

    test('calculates jitter for variable latency', () => {
        const latencies = [20, 25, 20, 30, 20];
        const jitter = calculateJitter(latencies);
        expect(jitter).toBeGreaterThan(0);
    });
});
```

### Integration Tests (PHP)

```php
// tests/integration/test-plugin.php

class CGRT_Integration_Tests extends WP_UnitTestCase {
    
    function test_shortcode_exists() {
        $output = do_shortcode('[cloud_gaming_test]');
        $this->assertStringContainsString('cgrt-container', $output);
    }

    function test_shortcode_attributes() {
        $output = do_shortcode('[cloud_gaming_test theme="dark"]');
        $this->assertStringContainsString('data-theme="dark"', $output);
    }

    function test_settings_save() {
        $settings = array(
            'test_duration' => 45000,
            'sample_rate' => 500
        );
        update_option('cgrt_settings', $settings);
        
        $saved = get_option('cgrt_settings');
        $this->assertEquals(45000, $saved['test_duration']);
    }

    function test_filters_work() {
        add_filter('cgrt_test_duration', function($duration) {
            return 1000;
        });
        
        // Apply filter and verify
        $duration = apply_filters('cgrt_test_duration', 30000);
        $this->assertEquals(1000, $duration);
    }
}
```

### End-to-End Tests (Puppeteer)

```javascript
// tests/e2e/test-full-flow.js

const puppeteer = require('puppeteer');

describe('Full User Flow', () => {
    let browser;
    let page;

    beforeAll(async () => {
        browser = await puppeteer.launch();
        page = await browser.newPage();
        await page.goto('http://localhost:8000/test-page');
    });

    afterAll(async () => {
        await browser.close();
    });

    test('user can run complete test', async () => {
        // Click start button
        await page.click('.cgrt-start-btn');
        
        // Wait for test to start
        await page.waitForSelector('.cgrt-progress', { visible: true });
        
        // Wait for test to complete (35 seconds max)
        await page.waitForSelector('.cgrt-results', { 
            visible: true,
            timeout: 35000 
        });
        
        // Check results are displayed
        const score = await page.$eval('.cgrt-score-number', el => el.textContent);
        expect(score).toMatch(/\d+/);
    });

    test('charts render correctly', async () => {
        await page.waitForSelector('#cgrt-latency-chart');
        
        const canvas = await page.$('#cgrt-latency-chart');
        const hasContent = await page.evaluate(canvas => {
            const ctx = canvas.getContext('2d');
            const pixel = ctx.getImageData(0, 0, 1, 1).data;
            return pixel[3] > 0; // Check if canvas has content
        }, canvas);
        
        expect(hasContent).toBe(true);
    });
});
```

## Test Scenarios

### Scenario 1: Excellent Connection

**Setup**: Wired Ethernet, fast fiber connection

**Expected Results**:
- Score: 90-100
- Latency: <20ms
- Jitter: <5ms
- Packet loss: 0%
- Quality: 4K supported, 4K recommended

### Scenario 2: Good WiFi Connection

**Setup**: WiFi near router, good signal

**Expected Results**:
- Score: 75-89
- Latency: 20-40ms
- Jitter: 5-10ms
- Packet loss: <0.5%
- Quality: 1440p recommended, 4K supported

### Scenario 3: Fair Connection

**Setup**: WiFi further from router, some interference

**Expected Results**:
- Score: 50-74
- Latency: 40-60ms
- Jitter: 10-20ms
- Packet loss: 0.5-1.5%
- Quality: 1080p recommended

### Scenario 4: Poor Connection

**Setup**: Weak WiFi, congested network

**Expected Results**:
- Score: 25-49
- Latency: 60-100ms
- Jitter: 20-40ms
- Packet loss: 1.5-3%
- Quality: 720p recommended

### Scenario 5: Unplayable Connection

**Setup**: Very slow internet, high packet loss

**Expected Results**:
- Score: 0-24
- Latency: >100ms
- Jitter: >40ms
- Packet loss: >3%
- Quality: Nothing supported

### Scenario 6: Latency Spikes

**Setup**: Generally good connection with occasional spikes

**Expected Results**:
- Score reduced due to spikes
- Recommendation mentions latency spikes
- Stability score lower than latency suggests

### Scenario 7: High Jitter

**Setup**: Inconsistent latency, lots of variation

**Expected Results**:
- Low jitter score
- Recommendation focuses on network congestion
- Jitter chart shows wide distribution

## Regression Testing

After each change, run:

1. **Smoke Tests**: Quick check of basic functionality
2. **Critical Path Tests**: Core user flow
3. **UI Tests**: Visual regression
4. **Performance Tests**: No performance degradation

## Bug Reporting Template

```markdown
### Bug Report

**Description**: Brief description of the issue

**Steps to Reproduce**:
1. 
2. 
3. 

**Expected Behavior**: What should happen

**Actual Behavior**: What actually happens

**Environment**:
- WordPress version:
- PHP version:
- Browser:
- Device:

**Screenshots**: If applicable

**Console Errors**: Copy any browser console errors

**Additional Notes**: Any other relevant information
```

## Test Data

Use controlled test data for predictable results:

```javascript
// Consistent latency test
const consistentLatencyData = Array(30).fill(null)
    .map((_, i) => ({
        timestamp: Date.now() + i * 1000,
        latency: 35,
        success: true
    }));

// Variable latency test
const variableLatencyData = Array(30).fill(null)
    .map((_, i) => ({
        timestamp: Date.now() + i * 1000,
        latency: 35 + Math.random() * 20 - 10,
        success: true
    }));

// High packet loss test
const highPacketLossData = Array(30).fill(null)
    .map((_, i) => ({
        timestamp: Date.now() + i * 1000,
        latency: i % 3 === 0 ? null : 35,
        success: i % 3 !== 0
    }));
```

## Continuous Testing

Set up automated testing:

```yaml
# .github/workflows/test.yml

name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup Node.js
      uses: actions/setup-node@v2
      with:
        node-version: '16'
    
    - name: Install dependencies
      run: npm ci
    
    - name: Run JavaScript tests
      run: npm test
    
    - name: Setup WordPress
      uses: wordpress/setup-wordpress@v1
    
    - name: Run PHP tests
      run: |
        wp scaffold plugin-tests cloud-gaming-readiness-test
        bash bin/install-wp-tests.sh wordpress_test '' '' localhost latest true
        phpunit
```

## Performance Benchmarks

Target performance metrics:

| Metric | Target |
|--------|--------|
| Initial page load | < 2s |
| Time to interactive | < 3s |
| Test execution time | 30s ± 2s |
| Chart rendering | < 500ms |
| Score calculation | < 100ms |
| Memory per test | < 50MB |
| CPU per test | < 20% |

## Release Testing Checklist

Before releasing a new version:

- [ ] All automated tests pass
- [ ] Manual smoke tests pass
- [ ] Tested on WordPress latest version
- [ ] Tested on WordPress minimum supported version
- [ ] Tested on all major browsers
- [ ] Tested on mobile devices
- [ ] Performance benchmarks met
- [ ] Accessibility audit passed
- [ ] Security review completed
- [ ] Documentation updated
- [ ] Changelog updated
- [ ] Demo tested in various environments
