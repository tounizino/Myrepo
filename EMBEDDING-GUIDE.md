# Cloud Gaming Connectivity Tool - Embedding Guide

## Quick Start

### Option 1: Direct Paste (Recommended for WordPress)

1. Open your WordPress post/page editor
2. Switch to **HTML/Code** view (or use Custom HTML block in Gutenberg)
3. Copy the entire contents of `cloud-gaming-connectivity-tool.html`
4. Paste it into your post/page
5. Publish and test

**Pros:** No external dependencies, works immediately
**Cons:** Larger page size, harder to update across multiple pages

---

### Option 2: Upload and Embed via iframe

1. **Upload the HTML file:**
   - Upload `cloud-gaming-connectivity-tool.html` to your server
   - Example location: `/wp-content/uploads/tools/connectivity-tool.html`

2. **Create iframe embed code:**
   ```html
   <iframe 
       src="/wp-content/uploads/tools/connectivity-tool.html" 
       width="100%" 
       height="2500px" 
       frameborder="0" 
       style="border:none; max-width: 1200px; margin: 0 auto; display: block;"
       title="Cloud Gaming Connectivity Tool"
       loading="lazy">
   </iframe>
   ```

3. **Paste the iframe code** into your WordPress post (HTML view)

**Pros:** Reusable across multiple pages, easier to update
**Cons:** Requires file upload, iframe limitations

---

### Option 3: WordPress Shortcode (Advanced)

Create a custom shortcode in your theme's `functions.php`:

```php
function cloud_gaming_tool_shortcode() {
    $tool_url = get_stylesheet_directory_uri() . '/tools/connectivity-tool.html';
    return '<iframe src="' . esc_url($tool_url) . '" width="100%" height="2500px" frameborder="0" style="border:none; max-width: 1200px; margin: 0 auto; display: block;" title="Cloud Gaming Connectivity Tool" loading="lazy"></iframe>';
}
add_shortcode('gaming_connectivity_tool', 'cloud_gaming_tool_shortcode');
```

Then use in any post/page:
```
[gaming_connectivity_tool]
```

**Pros:** Most flexible, cleanest implementation
**Cons:** Requires theme modification

---

## Recommended iframe Dimensions

- **Desktop (Full):** `width="100%"` `height="2500px"`
- **Compact (Single Tool):** `width="100%"` `height="800px"`
- **Mobile:** Responsive by default, use `width="100%"`

---

## Customization Before Embedding

### 1. Update SEO Metadata

Edit lines 13-22 in the HTML file:

```html
<title>Your Custom Title</title>
<meta name="description" content="Your custom description">
<link rel="canonical" href="https://yoursite.com/your-page">
```

### 2. Configure STUN Servers

Edit line 1273 (inside `<script>` tag):

```javascript
const STUN_SERVERS = [
    'stun:stun.l.google.com:19302',
    'stun:your-custom-stun-server.com:3478'
];
```

### 3. Enable Backend Port Checking

Edit line 1280:

```javascript
const BACKEND_PORT_CHECK_URL = 'https://your-backend.com/api/port-check';
```

---

## Performance Optimization

### Option A: Keep Tailwind CDN (Easiest)
- No changes needed
- ~80KB initial load
- Works immediately

### Option B: Self-Host Tailwind (Production)

1. Install Tailwind CLI:
   ```bash
   npm install -D tailwindcss
   ```

2. Create `tailwind.config.js`:
   ```javascript
   module.exports = {
     content: ["./cloud-gaming-connectivity-tool.html"],
     theme: { extend: {} },
     plugins: [],
   }
   ```

3. Build CSS:
   ```bash
   npx tailwindcss -o output.css --minify
   ```

4. Replace CDN script (line 39) with:
   ```html
   <link rel="stylesheet" href="output.css">
   ```

**Result:** Reduces CSS from 80KB to ~10KB (only used classes)

---

## WordPress-Specific Tips

### Prevent WordPress from Stripping Code

Add to `functions.php`:

```php
// Prevent WordPress from removing iframe attributes
function allow_custom_iframe_attributes($allowedposttags) {
    $allowedposttags['iframe']['loading'] = true;
    $allowedposttags['iframe']['title'] = true;
    return $allowedposttags;
}
add_filter('wp_kses_allowed_html', 'allow_custom_iframe_attributes');
```

### Cache-Busting for Updates

When updating the tool, add a version parameter:

```html
<iframe src="/path/to/tool.html?v=2"></iframe>
```

---

## Testing Checklist

After embedding:

- [ ] NAT detection runs successfully
- [ ] Port checker accepts input and displays results
- [ ] Router accordion sections expand/collapse
- [ ] Tutorial sections are readable
- [ ] Mobile responsive design works
- [ ] No console errors in browser DevTools
- [ ] Sticky header doesn't overlap content
- [ ] All buttons are clickable and functional
- [ ] Privacy checkbox toggles correctly
- [ ] Color contrast meets WCAG AA standards

---

## Common Issues & Solutions

### Issue: Tool doesn't load in iframe
**Solution:** Check CORS settings and X-Frame-Options headers

### Issue: WebRTC blocked
**Solution:** Ensure page is served over HTTPS (required for WebRTC)

### Issue: Styles broken
**Solution:** Verify Tailwind CDN is loading (check network tab)

### Issue: NAT detection shows "Unknown"
**Solution:** User may have WebRTC disabled or be behind corporate firewall

### Issue: Port checker always shows "Unknown"
**Solution:** This is expected for browser-only mode. Deploy backend for accurate results.

---

## Analytics Integration

To track tool usage, add this before the closing `</script>` tag (around line 1595):

```javascript
// Google Analytics 4 example
function sendDiagnostics(eventName, data) {
    if (typeof gtag !== 'undefined') {
        gtag('event', eventName, {
            'event_category': 'Gaming_Tool',
            'event_label': JSON.stringify(data),
            'value': 1
        });
    }
}
```

Track these events:
- `nat_check` - User runs NAT detection
- `port_check` - User checks ports
- `router_expand` - User opens router guide
- `tutorial_view` - User views port forwarding tutorial

---

## Internationalization (i18n)

The tool includes `data-i18n` attributes for future translation support.

To add language support:

1. Create translation JSON files:
   ```json
   {
     "header.title": "Herramienta de Conectividad de Gaming en la Nube",
     "header.subtitle": "Diagnostica NAT, prueba puertos..."
   }
   ```

2. Add translation loader script before closing `</body>`:
   ```javascript
   async function loadTranslations(lang) {
       const res = await fetch(`/translations/${lang}.json`);
       const translations = await res.json();
       
       document.querySelectorAll('[data-i18n]').forEach(el => {
           const key = el.getAttribute('data-i18n');
           if (translations[key]) {
               el.textContent = translations[key];
           }
       });
   }
   
   // Auto-detect user language
   const userLang = navigator.language.split('-')[0];
   if (userLang !== 'en') {
       loadTranslations(userLang);
   }
   ```

---

## Security Best Practices

1. **Always serve over HTTPS** - Required for WebRTC
2. **Keep dependencies updated** - Monitor Tailwind CDN version
3. **Sanitize analytics data** - Don't log IP addresses without consent
4. **Rate limit backend** - Included in server.js
5. **Validate all inputs** - Already implemented in code
6. **Use CSP headers** - Recommended for iframe embeds

---

## Support & Updates

For issues or feature requests:
- Review browser console for errors
- Test with WebRTC enabled
- Verify HTTPS is active
- Check that no ad blockers are interfering with STUN servers

To update across multiple pages:
- Use Option 2 (iframe) for centralized updates
- Or use Option 3 (shortcode) for theme-level control
