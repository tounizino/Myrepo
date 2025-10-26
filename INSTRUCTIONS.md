# 🎮 Cloud Loadout Widget - Quick Installation Guide

## For WordPress Users

### Method 1: Gutenberg Editor (Recommended)

1. **Open your WordPress page/post editor**
2. Click the **[+]** button to add a new block
3. Search for **"Custom HTML"** or scroll to find it under "Widgets"
4. **Copy ALL the content** from `wordpress-embed.html` 
5. **Paste** into the Custom HTML block
6. Click **Preview** or **Publish**

### Method 2: Classic Editor

1. **Switch to Text/HTML mode** (not Visual mode)
2. **Copy ALL the content** from `wordpress-embed.html`
3. **Paste** where you want the widget to appear
4. **Save/Update** your page

### Method 3: Page Builders

**Elementor:**
- Add an "HTML" widget
- Paste the `wordpress-embed.html` content
- Update

**WPBakery:**
- Add "Raw HTML" element
- Paste the content
- Save

**Divi:**
- Add "Code" module
- Paste the content
- Save

---

## Testing Locally (No WordPress Needed)

1. Open `cloud-loadout-tester.html` directly in your browser
2. Plug in a gamepad or start typing
3. Watch the magic happen! ✨

---

## What to Expect

### Without Any Input
- Clean interface with "Awaiting Input" status
- "Connect a gamepad to begin" message
- All analytics at zero

### With Gamepad Connected
- Press any button after plugging in (browser security requirement)
- Real-time button press visualization
- Analog stick movement tracking
- Trigger pressure bars
- Analytics update instantly

### With Keyboard Input
- Click anywhere in the widget area first
- Press WASD, Arrow keys, Space, or Shift
- See simultaneous key detection
- Watch rollover counter increase
- All keys tracked and displayed

---

## Quick Troubleshooting

**Widget not showing up?**
- Make sure you copied the ENTIRE file (it's large!)
- Check that your WordPress theme allows Custom HTML
- Try disabling page caching plugins temporarily

**Gamepad not detected?**
- Press ANY button on the gamepad after plugging it in
- Check browser console (F12) for errors
- Try Chrome/Edge for best compatibility

**Keyboard not responding?**
- Click inside the widget area to give it focus
- Some system keys (F11, Ctrl+W, etc.) can't be captured
- Close other apps that might intercept keys (Discord, OBS)

---

## Features You'll See

✅ **Live gamepad visualization** with SVG controller  
✅ **Button press detection** with neon glow effects  
✅ **Analog stick tracking** with movement trails  
✅ **Trigger pressure bars** (L2/R2)  
✅ **Keyboard rollover detection** (test your keyboard's limits!)  
✅ **Latency metrics** (avg, jitter, P95)  
✅ **Sparkline chart** showing latency trends  
✅ **Polar chart** for stick circularity  
✅ **Button usage bars** by category  
✅ **Consistency score** with Gold/Silver/Bronze badges  
✅ **Copy Report** button for sharing results  
✅ **Reset** button to clear analytics  

---

## Performance Notes

- **CPU Usage:** ~1-2% typically (60fps animation loop)
- **Memory:** ~10-20MB (circular buffers prevent memory leaks)
- **Network:** ZERO (everything is inline, no external requests)
- **Compatibility:** Works offline, no internet required

---

## Browser Recommendations

🏆 **Best:** Chrome 90+, Edge 90+  
🥈 **Good:** Firefox 88+, Opera 76+  
🥉 **Limited:** Safari 14+ (gamepad support varies)  

---

## Color Scheme (2026 Futuristic)

- 🔵 **Azure** `#3BC6FF` - Primary accent
- 🟣 **Purple** `#A56CFF` - Secondary accent  
- 🟢 **Lime** `#C6FF3B` - Success/High performance
- 🔴 **Pink** `#FF6B9D` - Special highlights
- ⚫ **Dark** `#0f0f1e` - Background

---

## What Makes This Different?

✨ **Zero dependencies** - No jQuery, no React, no framework bloat  
✨ **Single file** - Everything inline (CSS + JS + HTML)  
✨ **WordPress-safe** - Namespaced to avoid conflicts  
✨ **Accessible** - WCAG compliant, keyboard navigable  
✨ **Responsive** - Mobile to 4K displays  
✨ **Future-proof** - Uses standard Web APIs  

---

## Need Help?

1. Check the main **README.md** for detailed documentation
2. Open browser console (F12) and look for errors
3. Test in a different browser to isolate issues
4. Verify the entire file was copied (should be ~40KB for WordPress version)

---

**🚀 Ready to impress your audience? Let's go!**

*Cloud Loadout - Elevate Your Game*
