# 🎮 Ultimate Controller Tester - Xbox Style

A professional, high-end gamepad/controller testing tool with beautiful Xbox-style SVG graphics. Test all controller inputs in real-time with accurate visual feedback and vibration support.

## ✨ Features

- **🎨 Beautiful Xbox-Style Design**: High-quality SVG controller visualization with smooth animations
- **⚡ Real-Time Testing**: Instant feedback for all button presses, triggers, and analog sticks
- **📊 Accurate Readings**: Precise analog values and pressure-sensitive button detection
- **🕹️ Complete Input Coverage**: Tests all standard gamepad inputs:
  - Face buttons (A, B, X, Y)
  - Shoulder buttons (LB, RB)
  - Triggers (LT, RT) with pressure sensitivity
  - D-Pad (Up, Down, Left, Right)
  - Analog sticks with visual position indicators
  - Stick buttons (L3, R3)
  - System buttons (Start/Menu, Back/View, Home/Xbox)
- **💥 Vibration Testing**: Test controller rumble with light, medium, and strong intensities
- **📱 Universal Compatibility**: Works with Xbox, PlayStation, Nintendo Switch Pro, and generic controllers
- **🌐 WordPress Ready**: Single HTML file - easy to embed anywhere
- **🚀 No Dependencies**: Pure HTML, CSS, and JavaScript - no external libraries required

## 🚀 Quick Start

### Option 1: Direct Browser Usage

1. Open `controller-tester.html` directly in any modern web browser
2. Connect your gamepad via USB or Bluetooth
3. Press any button on the controller to activate
4. Start testing!

### Option 2: WordPress Integration

1. Create a new Page or Post in WordPress
2. Switch to "Code Editor" or "HTML" mode
3. Copy the entire contents of `controller-tester.html`
4. Paste it into the editor
5. Publish and view your page

### Option 3: Embed in Website

Simply include the HTML file or embed it via iframe:

```html
<iframe src="controller-tester.html" width="100%" height="1200px" frameborder="0"></iframe>
```

## 🎯 How to Use

1. **Connect Controller**: Plug in your gamepad via USB or connect via Bluetooth
2. **Activate Detection**: Press any button on the controller to trigger detection
3. **Test Inputs**: 
   - Press buttons and see them light up on the visual controller
   - Move analog sticks and watch the real-time position indicators
   - Pull triggers and observe pressure sensitivity
   - Check the detailed status panel for exact numeric values
4. **Test Vibration**: Use the vibration buttons to test rumble functionality (if supported)

## 🎮 Supported Controllers

- ✅ Xbox One / Xbox Series X|S Controllers
- ✅ Xbox 360 Controllers
- ✅ PlayStation 4 / PlayStation 5 DualShock/DualSense Controllers
- ✅ Nintendo Switch Pro Controllers
- ✅ Generic USB/Bluetooth Gamepads
- ✅ Most Xinput and DirectInput compatible controllers

## 🌐 Browser Compatibility

Requires browsers with Gamepad API support:

- ✅ Google Chrome (21+)
- ✅ Microsoft Edge (12+)
- ✅ Firefox (29+)
- ✅ Opera (15+)
- ✅ Safari (10.1+)

## 📋 Technical Details

- **Technology**: HTML5, CSS3, JavaScript (ES6+)
- **API**: Gamepad API for controller detection and input reading
- **SVG Graphics**: Custom-designed Xbox-style controller illustration
- **Animation**: RequestAnimationFrame for smooth 60fps updates
- **Vibration**: Gamepad Vibration API for rumble testing
- **Responsive**: Adapts to different screen sizes

## 🎨 Visual Features

- Gradient backgrounds and shadows for depth
- Smooth button press animations
- Real-time analog stick position visualization
- Trigger pressure sensitivity indicators
- Color-coded face buttons (Xbox standard colors)
- Professional status panels with live data
- Pulsing connection indicator

## 🔧 Customization

The single-file design makes customization easy. You can modify:

- Colors and gradients in the `<style>` section
- Controller SVG design
- Button mappings in the JavaScript
- Layout and panel positions
- Animation speeds and effects

## 📄 File Structure

```
controller-tester.html  # Single standalone file containing everything
├── HTML Structure
├── Embedded CSS Styles
├── SVG Controller Graphics
└── JavaScript Gamepad Logic
```

## 💡 Use Cases

- 🎮 Gaming setup testing
- 🔧 Controller repair verification
- 🎓 Educational demonstrations
- 🏪 Retail controller testing
- 🖥️ Website interactive features
- 📱 Game development input testing

## 🆘 Troubleshooting

**Controller not detected?**
- Make sure it's properly connected
- Press any button to wake up the controller
- Try refreshing the page
- Check browser console for errors

**Buttons not responding correctly?**
- Different controllers may have different button mappings
- Some generic controllers may not follow standard mapping
- Check if your browser supports the Gamepad API

**Vibration not working?**
- Not all controllers support vibration
- Some browsers may not support the Vibration API
- Check controller battery level (low battery may disable rumble)

## 📝 License

Open source - feel free to use, modify, and distribute.

## 🙌 Credits

Created as a professional gamepad testing solution with Xbox-style aesthetics.
Inspired by modern controller testing tools and gaming hardware diagnostics.

---

**Made with ❤️ for gamers and developers**
