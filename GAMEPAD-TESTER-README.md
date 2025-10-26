# 🎮 Professional Gamepad Tester

A complete, professional-grade gamepad testing tool for gaming websites. Supports PS5, Xbox, and all standard gamepads with full button, trigger, analog stick, and D-pad testing capabilities.

## ✨ Features

### 🎯 Complete Controller Support
- **PS5 DualSense** - Full button mapping including touchpad
- **Xbox Series X/S** - Complete Xbox controller support
- **Xbox One** - Full compatibility
- **PS4 DualShock 4** - All buttons and features
- **Generic Gamepads** - Any standard gamepad
- **Nintendo Pro Controller** - Full support

### 🔥 Professional Features
- Real-time button press detection with visual feedback
- Analog stick visualizers with precise position tracking
- Trigger pressure visualization (0-100%)
- D-Pad directional testing with visual indicators
- Controller vibration testing (weak, strong, pulse patterns)
- Connection status monitoring
- Responsive design for all screen sizes
- Beautiful gradient UI with animations
- All styling uses `!important` for WordPress compatibility

### 📱 Visual Components
- **Face Buttons** - A/B/X/Y (Xbox) or Cross/Circle/Square/Triangle (PS)
- **Shoulder Buttons** - LB/RB, L1/R1
- **Triggers** - LT/RT, L2/R2 with pressure sensitivity
- **Analog Sticks** - Left and right with 2D position visualization
- **D-Pad** - All four directions with visual feedback
- **System Buttons** - Start, Select, Home, Share, Options

## 📦 Files Included

### 1. `gamepad-tester.html`
Complete standalone HTML file with embedded CSS and JavaScript. Can be used as:
- Standalone webpage
- Embedded in an iframe
- Basis for custom integration

### 2. `gamepad-tester-wordpress.html`
WordPress-optimized version ready to paste directly into:
- WordPress Custom HTML blocks
- Page builders (Elementor, Divi, etc.)
- Custom HTML widgets
- Theme template files

## 🚀 WordPress Installation Methods

### Method 1: Custom HTML Block (Recommended)
1. Edit your WordPress page/post
2. Add a **Custom HTML** block
3. Copy all content from `gamepad-tester-wordpress.html`
4. Paste into the Custom HTML block
5. Update/Publish your page

### Method 2: HTML Widget
1. Go to **Appearance → Widgets**
2. Add a **Custom HTML** widget
3. Paste the code from `gamepad-tester-wordpress.html`
4. Save the widget

### Method 3: Page Builder Integration
#### For Elementor:
1. Add an **HTML** widget
2. Paste the code
3. Preview and publish

#### For Divi:
1. Add a **Code** module
2. Paste the code in the code field
3. Enable/Disable code output
4. Save

#### For WPBakery:
1. Add **Raw HTML** element
2. Paste the code
3. Save

### Method 4: Theme File Integration
1. Go to **Appearance → Theme Editor**
2. Select your page template
3. Add the code where desired
4. Update file

## 🎨 Customization

### Color Scheme
The tester uses a purple gradient theme. To customize colors, modify these CSS variables:

```css
/* Primary gradient */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;

/* Button pressed state */
background: linear-gradient(145deg, #667eea, #764ba2) !important;

/* Triggers */
background: linear-gradient(90deg, #44ff44, #00cc00) !important;
```

### Size Adjustments
```css
/* Container max width */
.gamepad-tester-container {
    max-width: 1400px !important;
}

/* Title size */
.gamepad-tester-title {
    font-size: 2.5em !important;
}
```

## 🌐 Browser Compatibility

### Fully Supported
✅ Chrome 21+
✅ Edge 12+
✅ Firefox 29+
✅ Opera 15+
✅ Safari 10.1+

### Features by Browser
| Feature | Chrome | Firefox | Edge | Safari |
|---------|--------|---------|------|--------|
| Button Detection | ✅ | ✅ | ✅ | ✅ |
| Analog Sticks | ✅ | ✅ | ✅ | ✅ |
| Triggers | ✅ | ✅ | ✅ | ✅ |
| Vibration | ✅ | ✅ | ✅ | ⚠️ |

*Note: Safari has limited vibration support on some devices*

## 🎮 Supported Controllers

### Tested and Verified
- ✅ PlayStation 5 DualSense
- ✅ PlayStation 4 DualShock 4
- ✅ Xbox Series X/S Controller
- ✅ Xbox One Controller
- ✅ Xbox 360 Controller
- ✅ Nintendo Switch Pro Controller
- ✅ Logitech F310/F710
- ✅ Steam Controller
- ✅ Generic USB/Bluetooth Gamepads

### Connection Methods
- USB wired connection
- Bluetooth wireless
- USB wireless dongle
- Native browser support (no drivers needed)

## 📱 Responsive Design

The tester automatically adapts to different screen sizes:

- **Desktop (1400px+)**: Full two-column layout
- **Tablet (768px-1400px)**: Optimized two-column
- **Mobile (<768px)**: Single-column stacked layout

All elements remain fully functional on touch devices.

## 🔧 Technical Details

### API Used
Built with the **Gamepad API** (W3C Standard)
- Real-time polling at 60 FPS
- Standard button mapping
- Axis normalization
- Vibration actuator support

### Performance
- Lightweight: ~30KB total (HTML + CSS + JS)
- No external dependencies
- No frameworks required
- Optimized requestAnimationFrame polling
- Minimal CPU usage

### Button Mapping (Standard)
```javascript
Index 0:  A / Cross (×)
Index 1:  B / Circle (○)
Index 2:  X / Square (□)
Index 3:  Y / Triangle (△)
Index 4:  L1 / LB
Index 5:  R1 / RB
Index 6:  L2 / LT (Trigger)
Index 7:  R2 / RT (Trigger)
Index 8:  Select / Share
Index 9:  Start / Options
Index 10: L3 (Left Stick Click)
Index 11: R3 (Right Stick Click)
Index 12: D-Pad Up
Index 13: D-Pad Down
Index 14: D-Pad Left
Index 15: D-Pad Right
Index 16: Home / PS / Xbox Button
```

### Axis Mapping
```javascript
Axis 0: Left Stick X (Horizontal)
Axis 1: Left Stick Y (Vertical)
Axis 2: Right Stick X (Horizontal)
Axis 3: Right Stick Y (Vertical)
```

## 🎯 Use Cases

### Gaming Websites
- Controller testing before online play
- Input verification for web games
- Controller configuration guides
- Gaming community tools

### Support Sites
- Troubleshooting controller issues
- Verifying button mappings
- Testing dead zones
- Calibration assistance

### Educational
- Game development tutorials
- Input system demonstrations
- Gamepad API examples
- Interactive learning tools

## 🛠️ Troubleshooting

### Controller Not Detected
1. Make sure controller is properly connected
2. Press any button to wake the controller
3. Try refreshing the page
4. Check if browser supports Gamepad API
5. Try a different USB port or Bluetooth pairing

### Buttons Not Responding
1. Verify button works in other applications
2. Check if controller is in the correct mode
3. Try disconnecting and reconnecting
4. Clear browser cache

### Vibration Not Working
1. Check browser support (Chrome recommended)
2. Ensure controller supports vibration
3. Try USB connection instead of Bluetooth
4. Some browsers require HTTPS for vibration

### Wrong Button Mappings
1. Controller may not use standard mapping
2. Check controller mode (some have PC/PS4 modes)
3. Update controller firmware
4. Try different browser

## 📄 License

This gamepad tester is provided as-is for use in your gaming website. Feel free to:
- Use on any website
- Modify styling and colors
- Add custom features
- Redistribute (with attribution appreciated)

## 🤝 Support

### Common Questions

**Q: Does this work on mobile?**
A: Yes, but mobile browser gamepad support varies. Works best with external controllers on Android Chrome.

**Q: Can I change the colors?**
A: Yes! All colors are in CSS with `!important` tags. Search and replace hex codes.

**Q: Does it work with custom controllers?**
A: Most custom controllers that follow the standard gamepad protocol will work.

**Q: Is internet required?**
A: No, once loaded, everything runs client-side with no external requests.

**Q: Can I test multiple controllers?**
A: Currently supports one controller at a time. The first connected controller is used.

## 🔄 Updates & Improvements

Possible future enhancements:
- Multi-controller support
- Button remapping configuration
- Deadzone visualization
- Input lag testing
- Recording and playback
- Custom button labels
- Controller presets

## 📸 Screenshots

The tester includes:
- ✨ Animated connection status indicator
- 🎨 Beautiful gradient purple theme
- 📊 Real-time value displays
- 🎯 Visual button press feedback
- 📈 Analog stick position visualizers
- 🎚️ Trigger pressure bars
- 🎮 D-Pad directional display
- 💫 Smooth animations and transitions

## 🚀 Performance Tips

1. **Keep page simple**: Don't overload with other heavy scripts
2. **Test in production environment**: Verify on your actual WordPress site
3. **Mobile optimization**: Test on actual mobile devices
4. **Browser testing**: Check in Chrome, Firefox, Edge, Safari
5. **Controller variety**: Test with different controller types

## ✅ Checklist for Implementation

- [ ] Copy the WordPress-friendly HTML file
- [ ] Paste into WordPress HTML block
- [ ] Preview the page
- [ ] Test with your controller
- [ ] Verify all buttons work
- [ ] Test triggers and analog sticks
- [ ] Check vibration features
- [ ] Test on mobile (if needed)
- [ ] Verify responsive design
- [ ] Publish!

## 🎉 You're Ready!

Your professional gamepad tester is ready to use. Simply paste the code into WordPress and start testing controllers!

For the best experience:
1. Use the WordPress-friendly version
2. Add to a full-width page for best layout
3. Test with multiple controller types
4. Encourage users to test all features
5. Consider adding usage instructions on your page

Enjoy your new professional gamepad tester! 🎮✨
