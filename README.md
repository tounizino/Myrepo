# Cloud Loadout: Gamepad + Keyboard Tester

> **Futuristic, Production-Ready WordPress Embed Widget** | 2026 Design | Zero Dependencies

A stunning, self-contained HTML widget that detects and visualizes gamepad and keyboard inputs in real-time with advanced analytics, beautiful neon aesthetics, and professional performance metrics.

![Version](https://img.shields.io/badge/version-1.0.0-3BC6FF) ![WordPress](https://img.shields.io/badge/WordPress-Compatible-00A0D2) ![License](https://img.shields.io/badge/license-MIT-A56CFF)

---

## 🚀 Quick Start

### WordPress Installation (Gutenberg)

1. **Edit your page/post** in WordPress
2. **Add a Custom HTML block** (search for "Custom HTML" or "HTML")
3. **Copy the entire contents** of `wordpress-embed.html`
4. **Paste into the Custom HTML block**
5. **Preview or Publish** ✨

### WordPress Installation (Classic Editor)

1. Switch to **Text/HTML mode** (not Visual)
2. **Paste the contents** of `wordpress-embed.html` where you want the widget
3. **Update/Publish** your page

### Standalone Demo

Open `cloud-loadout-tester.html` directly in any modern browser to see the full experience.

---

## ✨ Features

### 🎮 Gamepad Detection
- **Real-time visualization** of button presses with color-coded feedback
- **Analog stick movement** tracking with smooth animations
- **Trigger pressure** display (L2/R2) with progressive fill bars
- **D-Pad input** detection
- **Auto-connect** - plug in any standard gamepad and start testing

### ⌨️ Keyboard Analysis
- **WASD + Arrow keys** + Space/Shift visualization
- **N-Key rollover detection** - see your keyboard's true capabilities
- **Simultaneous key tracking** - know exactly how many keys you can press at once
- **Ghosting detection** - identify keyboard limitations
- **Live active keys display** with instant feedback

### 📊 Performance Analytics

#### Latency Metrics
- **Average latency** between inputs (ms)
- **Jitter calculation** (standard deviation)
- **P95 percentile** - 95th percentile latency
- **Live sparkline chart** showing latency trends

#### Stick Circularity
- **Polar chart visualization** of analog stick movement patterns
- **Left/Right stick paths** color-coded (Azure/Purple)
- Perfect for detecting stick drift or calibration issues

#### Button Usage
- **Categorized tracking**: Face buttons, Shoulder buttons, Triggers, Keys
- **Stacked bar charts** with real-time updates
- **Press counters** for each category

#### Consistency Score
- **0-100 scoring system** based on:
  - Latency stability (low jitter = higher score)
  - Input diversity (varied inputs = bonus points)
  - Rollover capability (more simultaneous keys = bonus)
- **Award badges**: 🏆 Gold (90+) | 🥈 Silver (75+) | 🥉 Bronze (60+)

### 📋 Report Export
- **Copy Report button** generates comprehensive JSON + human-readable summary
- **Clipboard integration** with automatic fallback
- **Session statistics** including timestamp, uptime, device info
- Perfect for sharing with hardware reviews or technical support

---

## 🎨 Design Highlights

### Color Palette
- **Azure**: `#3BC6FF` - Primary accent, gamepad left elements
- **Purple**: `#A56CFF` - Secondary accent, gamepad right elements
- **Lime**: `#C6FF3B` - Success states, high performance indicators
- **Pink**: `#FF6B9D` - Special highlights

### Animations
- **60fps smooth** rendering via requestAnimationFrame
- **Cubic-bezier easing**: `(0.2, 0.8, 0.2, 1)` for premium feel
- **Respects `prefers-reduced-motion`** for accessibility

### Responsive Design
- **Mobile-first** approach
- **Breakpoints**: 
  - Desktop: 1400px max-width
  - Tablet: < 768px
  - Mobile: < 480px
- **Touch-friendly** button sizes

---

## 🔧 Technical Details

### Browser Compatibility
- ✅ **Chrome/Edge** 90+ (full Gamepad API support)
- ✅ **Firefox** 88+
- ✅ **Safari** 14+ (limited gamepad support on macOS)
- ✅ **Mobile browsers** (keyboard only, no gamepad on most mobile devices)

### API Usage
- **Gamepad API** - Standard Web API for controller detection
- **KeyboardEvent API** - Key press/release detection
- **Clipboard API** - Report copying with `document.execCommand` fallback
- **Performance API** - High-resolution timestamp for latency measurement

### Performance
- **Zero external dependencies** - no CDN requests, no jQuery, no frameworks
- **Inline CSS/JS** - single file embed, no additional HTTP requests
- **Optimized rendering** - only updates changed elements
- **Memory efficient** - circular buffers for history data (max 100 samples)

### Accessibility
- **ARIA roles** on interactive elements
- **Keyboard navigation** - all buttons focusable
- **Focus indicators** - visible outline on focus
- **High contrast mode** support via `prefers-contrast`
- **Reduced motion** support for users with vestibular disorders

---

## 🎯 Use Cases

### Gaming Hardware Reviews
- Test controller responsiveness and button quality
- Measure input lag and consistency
- Detect stick drift early
- Compare different gamepad models

### Keyboard Testing
- Verify N-key rollover claims
- Identify ghosting patterns
- Test mechanical vs membrane keyboards
- Validate gaming keyboard specifications

### Esports & Competitive Gaming
- Pre-match equipment check
- Input consistency verification
- Tournament standard compliance
- Setup optimization

### Tech Support & Troubleshooting
- Diagnose input device issues
- Generate detailed reports for RMA/warranty claims
- Validate driver installations
- Test USB port quality

---

## 📦 Files Included

```
cloud-loadout-tester/
├── cloud-loadout-tester.html    # Full HTML document (demo version)
├── wordpress-embed.html          # Minified WordPress embed snippet
└── README.md                     # This file
```

---

## 🛠️ Customization

### Change Colors
Edit the CSS variables in the `<style>` section:
```css
--cl-azure: #3BC6FF;    /* Your primary color */
--cl-purple: #A56CFF;   /* Your secondary color */
--cl-lime: #C6FF3B;     /* Your accent color */
```

### Modify Tracked Keys
Find the keyboard grid in HTML:
```html
<div class="cl-key" data-key="KeyW">W</div>
```
Add more keys using the [KeyboardEvent.code](https://developer.mozilla.org/en-US/docs/Web/API/KeyboardEvent/code) standard.

### Adjust Scoring Thresholds
In the JavaScript section, locate `updateConsistencyScore()`:
```javascript
if (score >= 90) { /* Gold */ }
else if (score >= 75) { /* Silver */ }
else if (score >= 60) { /* Bronze */ }
```

---

## 🔒 WordPress Compatibility

### Namespace Protection
All CSS classes and IDs are prefixed with `cl-` to avoid conflicts with WordPress themes.

### Works With
- ✅ Gutenberg Editor (Custom HTML Block)
- ✅ Classic Editor (Text/HTML mode)
- ✅ Elementor (HTML Widget)
- ✅ WPBakery Page Builder (Raw HTML element)
- ✅ Any WordPress theme

### Security
- No inline event handlers (`onclick`, etc.)
- Event listeners attached via JavaScript only
- Scoped IIFE (Immediately Invoked Function Expression) prevents global pollution
- No `eval()` or `innerHTML` with user input

---

## 🚨 Troubleshooting

### Gamepad Not Detected
1. **Press any button** on the gamepad after plugging in (required by browser security)
2. **Check browser compatibility** - Safari has limited gamepad support
3. **Try a different USB port** or use the Bluetooth connection
4. **Open browser console** (F12) and look for errors

### Keyboard Keys Not Responding
1. **Click inside the widget area** to ensure focus
2. **Check if another program** is intercepting keys (Discord overlay, OBS, etc.)
3. **Try different keys** - some browsers block certain system keys

### Copy Report Not Working
- **Modern browsers** use Clipboard API (requires HTTPS or localhost)
- **Fallback method** uses `document.execCommand` for older browsers
- **Check browser permissions** - some browsers block clipboard access

### Performance Issues
- **Close other tabs** to free up resources
- **Disable browser extensions** that might interfere (ad blockers, etc.)
- **Update browser** to latest version
- **Check CPU usage** - widget uses ~1-2% CPU typically

---

## 📄 License

**MIT License** - Free for personal and commercial use

```
Copyright (c) 2024 Cloud Loadout

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
```

---

## 🌟 Credits

**Design**: Futuristic 2026 aesthetic with neon accents  
**Development**: Vanilla JavaScript, zero dependencies  
**Compatibility**: WordPress 5.0+, all modern browsers  

---

## 💬 Support

For issues, suggestions, or contributions:
- Open an issue on your repository
- Check browser console for errors (F12)
- Test in different browsers to isolate issues

---

## 🎓 Technical Notes

### Why No External Libraries?
- **WordPress compatibility** - no dependency conflicts
- **Performance** - no overhead from frameworks
- **Portability** - works offline, no CDN dependencies
- **Security** - no third-party code injection risks

### Gamepad API Quirks
- Browsers require **user interaction** before detecting gamepads (security feature)
- Button indices vary by gamepad model (standard mapping used)
- Safari has **limited support** on macOS, better on iOS

### Keyboard Event Considerations
- Some keys are **reserved by browsers** (F11, Ctrl+T, etc.)
- **Key repeat** is handled by OS, not the widget
- **IME composition** events not tracked (for international keyboards)

---

**Built with ❤️ for the gaming community**  
*Cloud Loadout - Elevate Your Game*
