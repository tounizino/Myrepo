# Quick Start Guide

## 🚀 Get Started in 30 Seconds

### Option 1: Instant Use (Easiest)
1. Open `input-latency-meter.html` in your web browser
2. Press any key or tap the screen
3. See your latency results instantly!

### Option 2: WordPress Installation
1. Copy `wordpress-integration.php` to `/wp-content/plugins/`
2. Go to WordPress Admin → Plugins
3. Activate "Input Latency Meter"
4. Add `[input_latency_meter]` to any page/post
5. Done! 🎉

### Option 3: View the Demo
1. Open `demo.html` in your browser
2. See full documentation and live demo
3. Test all features interactively

## 📖 How to Use

### Measure Input Latency
- **Desktop**: Press any key on keyboard
- **Mobile**: Tap the colored zone
- **Mouse**: Click anywhere in the zone

### Test Network Ping
- Click "Test Network Ping" button
- Wait for measurement to complete
- View round-trip time in milliseconds

### Reset Data
- Click "Reset Data" button
- Confirm the dialog
- Start fresh measurements

## 🎯 Understanding Results

### Input Latency Values
- **< 5ms**: Professional gaming level ⚡
- **5-10ms**: Competitive gaming level 🏆
- **10-15ms**: Good performance ✅
- **15-30ms**: Acceptable for casual gaming 👍
- **> 30ms**: May need optimization 🔧

### Network Ping Values
- **< 30ms**: Professional gaming level ⚡
- **30-50ms**: Competitive gaming level 🏆
- **50-100ms**: Good connection ✅
- **100-150ms**: Playable 👍
- **> 150ms**: May experience lag 🔧

## 💡 Pro Tips

1. **Multiple Tests**: Run 10+ tests for accurate averages
2. **Close Apps**: Close background applications for best results
3. **Wired Connection**: Use ethernet instead of WiFi
4. **Best Browser**: Chrome/Edge typically have lowest latency
5. **Gaming Mode**: Enable if your device has gaming mode
6. **Monitor Refresh**: Higher refresh rate = better latency
7. **Compare Times**: Test at different times of day
8. **Hardware Testing**: Try different keyboards/mice

## 🔧 Customization

### Change Colors
Edit the CSS gradients in the `<style>` section:

```css
/* Flash zone default */
background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);

/* Flash zone active */
background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
```

### Adjust Measurements
Modify these values in the JavaScript:

```javascript
// History limit (default: 20)
if (this.latencyHistory.length > 20) {
    this.latencyHistory.shift();
}

// Flash duration (default: 200ms)
setTimeout(() => {
    flashZone.classList.remove('active');
}, 200);
```

## 🐛 Troubleshooting

### Problem: No measurements showing
**Solution**: Make sure the page is focused. Click on the colored zone first.

### Problem: Ping test fails
**Solution**: This is normal in some browsers due to CORS. The tool provides estimated values.

### Problem: High latency values
**Solutions**:
- Close other browser tabs
- Disable browser extensions
- Close background applications
- Restart browser
- Try different browser

### Problem: Not working on mobile
**Solution**: Make sure you're tapping directly on the colored zone. Some older mobile browsers may not support Web Components.

## 📱 Mobile Usage

### Best Practices
1. Hold device steady
2. Use thumbs for quick taps
3. Tap center of colored zone
4. Avoid edge taps
5. Test in landscape and portrait

### Mobile Optimization
- Touch targets are 44x44px minimum
- Prevents unwanted scrolling
- Ripple shows exact tap location
- Adaptive text sizing
- No horizontal scroll

## 🌐 Browser Compatibility

### Fully Supported ✅
- Chrome 90+
- Edge 90+
- Firefox 88+
- Safari 14+
- Opera 76+

### Mobile Browsers ✅
- iOS Safari 14+
- Chrome Mobile
- Samsung Internet
- Firefox Mobile

### Not Supported ❌
- Internet Explorer (any version)
- Very old browsers (pre-2020)

## 📊 Data Export (Future Feature)

Currently, data is stored in browser memory only. To save results:
1. Take screenshot of results
2. Write down key measurements
3. Use browser developer tools to export console logs

Future updates will include CSV export!

## 🔒 Privacy

- ✅ No data sent to any server
- ✅ No tracking or analytics
- ✅ No cookies stored
- ✅ No personal information collected
- ✅ All processing is local

## 🆘 Support

### Getting Help
1. Read the full [README.md](README.md)
2. Check [FEATURES.md](FEATURES.md) for complete feature list
3. View `demo.html` for interactive examples
4. Open an issue on GitHub

### Common Questions

**Q: Is this tool accurate?**
A: Yes! Uses high-precision Performance API with sub-millisecond accuracy.

**Q: Can I use this for esports?**
A: Absolutely! It's designed for competitive gamers.

**Q: Does it work offline?**
A: Yes! Only the ping test requires internet.

**Q: Can I embed it in my website?**
A: Yes! Use the WordPress plugin or copy the HTML file.

**Q: Is it free?**
A: Yes! MIT License - free forever.

## 🎮 Gaming Setup Optimization

Use this tool to optimize:
- Monitor selection (test input lag)
- Keyboard/mouse selection
- Browser selection
- Network configuration
- Game settings validation
- Tournament equipment verification

## 📈 Track Your Progress

1. Test your setup today
2. Make optimizations
3. Test again
4. Compare results
5. Repeat until optimal!

Target progression:
- Baseline: 20-30ms
- Week 1: 15-20ms
- Week 2: 10-15ms
- Month 1: 5-10ms
- Goal: <5ms consistently

## 🏆 Competition Mode

For tournaments:
1. Test equipment before event
2. Record baseline measurements
3. Test at venue
4. Compare results
5. Adjust if needed
6. Verify before match

## ✨ Have Fun!

This tool was made with ❤️ for gamers who demand precision. Use it to optimize your setup and dominate the competition!

**Happy Gaming! 🎮⚡**

---

For detailed documentation, see [README.md](README.md)
For complete feature list, see [FEATURES.md](FEATURES.md)
