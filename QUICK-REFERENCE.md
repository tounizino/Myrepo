# ⚡ Cloud Loadout - Quick Reference Card

## 🎯 What File Do I Need?

| Use Case | File | Size | Lines |
|----------|------|------|-------|
| **WordPress** | `wordpress-embed.html` | 40KB | 316 |
| **Standalone Demo** | `cloud-loadout-tester.html` | 51KB | 1525 |
| **Testing** | `test-demo.html` | 3KB | 79 |
| **Documentation** | `README.md` | 10KB | 308 |
| **Quick Guide** | `INSTRUCTIONS.md` | 4KB | 156 |

---

## 🚀 WordPress Installation (30 seconds)

1. **Copy** all content from `wordpress-embed.html`
2. **Add** Custom HTML block in Gutenberg
3. **Paste** the content
4. **Publish** ✨

---

## 🎮 Features at a Glance

### Input Detection
- ✅ Gamepad (17+ buttons, 4 axes)
- ✅ Keyboard (all keys, N-key rollover)
- ✅ Analog sticks (movement + circularity)
- ✅ Triggers (pressure visualization)

### Analytics
- 📊 Latency (avg, jitter, P95)
- 📈 Sparkline chart
- 🎯 Polar chart (stick paths)
- 📉 Button usage bars
- 🏆 Consistency score (0-100)
- 🥇 Award badges (Gold/Silver/Bronze)

### Actions
- 📋 Copy Report (JSON + human-readable)
- 🔄 Reset analytics
- 🔔 Toast notifications

---

## 🎨 Color Codes

```
Azure:  #3BC6FF  ███ Primary accent
Purple: #A56CFF  ███ Secondary accent
Lime:   #C6FF3B  ███ Success/Active
Pink:   #FF6B9D  ███ Highlights
Dark:   #0f0f1e  ███ Background
```

---

## 🔧 Common CSS Selectors

| Element | Selector | Purpose |
|---------|----------|---------|
| Container | `#cl-game-input-tester` | Main wrapper |
| Gamepad viz | `#cl-gamepad-viz` | SVG controller |
| Left stick | `#cl-left-stick` | Analog left |
| Right stick | `#cl-right-stick` | Analog right |
| Button (N) | `#cl-btn-N` | Button N |
| Key element | `.cl-key[data-key="KeyW"]` | Keyboard key |
| Copy button | `#cl-copy-report` | Report export |
| Reset button | `#cl-reset` | Analytics reset |

---

## 🐛 Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| Gamepad not detected | Press any button after plugging in |
| Keys not responding | Click inside widget to focus |
| Copy not working | Check HTTPS/localhost requirement |
| Widget not showing | Verify entire file copied (40KB) |
| Performance slow | Close other tabs, update browser |

---

## 📱 Browser Compatibility

| Browser | Version | Status |
|---------|---------|--------|
| Chrome | 90+ | 🟢 Full |
| Edge | 90+ | 🟢 Full |
| Firefox | 88+ | 🟢 Full |
| Safari | 14+ | 🟡 Limited gamepad |
| Mobile | Any | 🟡 Keyboard only |

---

## 🎯 Key Metrics Explained

**Latency Average** - Mean time between inputs (lower = better)  
**Jitter** - Standard deviation of latency (lower = more consistent)  
**P95** - 95th percentile latency (worst-case threshold)  
**Rollover** - Maximum simultaneous keys detected  
**Consistency Score** - Overall quality rating (0-100)

---

## 🏆 Badge Thresholds

| Badge | Score | Requirements |
|-------|-------|-------------|
| 🏆 Gold | 90+ | Low jitter, diverse inputs, high rollover |
| 🥈 Silver | 75-89 | Moderate consistency, good diversity |
| 🥉 Bronze | 60-74 | Acceptable performance |
| None | <60 | Needs improvement |

---

## 📦 Dependencies

**ZERO** ✨

No jQuery, no React, no npm, no build tools, no CDN, no internet required.

---

## ⚡ Performance Targets

- **CPU:** < 2%
- **Memory:** < 20MB
- **FPS:** 60 (constant)
- **Load Time:** Instant (inline)
- **Network:** 0 requests

---

## 🔒 WordPress Safety

✅ Namespaced (`cl-` prefix)  
✅ No global pollution (IIFE)  
✅ No inline handlers  
✅ Theme-independent  
✅ Plugin-safe  

---

## 📞 Support Checklist

Before asking for help:

1. ✅ Copied **entire** file (40KB for WordPress version)
2. ✅ Opened browser console (F12) to check errors
3. ✅ Tested in different browser (Chrome recommended)
4. ✅ Read `INSTRUCTIONS.md` for specific platform
5. ✅ Checked browser version (Chrome 90+, Firefox 88+, etc.)

---

## 🎓 Advanced Customization

### Change Primary Color
Find `--cl-azure: #3BC6FF;` in CSS, change to your color.

### Add More Keys
Add in HTML:
```html
<div class="cl-key" data-key="KeyE">E</div>
```

### Adjust Score Thresholds
Find `updateConsistencyScore()` in JS:
```javascript
if (score >= 90) { /* Gold */ }
```

---

## 📊 File Comparison

| Feature | Standalone | WordPress |
|---------|------------|-----------|
| DOCTYPE | ✅ Yes | ❌ No |
| HTML/HEAD/BODY | ✅ Yes | ❌ No |
| Minified CSS | ❌ No | ✅ Yes |
| Minified JS | ❌ No | ✅ Yes |
| Comments | ✅ Many | ✅ Some |
| Size | 51KB | 40KB |
| Lines | 1525 | 316 |

---

## 🚀 Launch Checklist

Before going live:

- [x] Test in target browser
- [x] Test with actual gamepad
- [x] Test keyboard rollover
- [x] Verify analytics update
- [x] Test report export
- [x] Check mobile responsiveness
- [x] Verify no console errors
- [x] Test in WordPress preview
- [x] Check on actual site
- [x] Share with test users

---

## 🎉 You're Ready!

Everything you need is in this repository. Pick the right file, follow the instructions, and you're live in under a minute.

**Questions?** Check `README.md` for detailed documentation.  
**Problems?** See `INSTRUCTIONS.md` troubleshooting section.  
**Details?** Read `PROJECT-SUMMARY.md` for complete specs.

---

**Now go impress your gaming audience! 🎮✨**

*Cloud Loadout - Elevate Your Game*
