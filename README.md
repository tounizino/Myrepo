# Cloud Gaming News Bar Component

A **2026-style, minimal, transparent** news ticker + expandable card component for cloud gaming websites. Light theme, no shadows, no glow — designed for professional cloud gaming platforms.

---

##  Features

-  **Auto-scrolling ticker** — seamless infinite scroll through headlines
-  **Expandable cards** — click a headline or the bar to reveal detailed news cards
-  **Per-card detail** — click any card to expand with full story, stats, and links
-  **"Live" indicator** — pulsing dot + label
-  **Fully responsive** — mobile, tablet, desktop
-  **Transparent background** — frost-blur glass effect on light themes
-  **No shadows, no glow** — clean 2026 minimal aesthetic
-  **WordPress ready** — shortcode + functions.php snippet included

---

##  Files

| File | Description |
|------|-------------|
| `cloud-gaming-news-bar.html` | Standalone HTML + CSS + JS — paste anywhere |
| `cloud-gaming-news-bar-wordpress.php` | WordPress-ready PHP file with `[cloud_gaming_news]` shortcode |

---

##  Quick Start — HTML Version

1. Open `cloud-gaming-news-bar.html`
2. Copy the **entire file contents**
3. Paste into any WordPress page via **Custom HTML block** (Gutenberg) or into any HTML area of your site

> The component is fully self-contained — all CSS and JS are embedded.

---

##  Quick Start — WordPress Version

### Option A: Theme `functions.php`
1. Open your theme's `functions.php`
2. Copy the entire contents of `cloud-gaming-news-bar-wordpress.php` and paste at the bottom
3. Use the shortcode `[cloud_gaming_news]` in any page, post, or widget area

### Option B: Custom Plugin (recommended)
1. Create a new file: `/wp-content/plugins/cloud-gaming-news/cloud-gaming-news.php`
2. Paste the contents of `cloud-gaming-news-wordpress.php` into it
3. Activate the plugin from WordPress Admin → Plugins
4. Use `[cloud_gaming_news]` anywhere

---

##  Adding / Editing News Items

Find the `NEWS_ITEMS` array in the JavaScript (HTML version) or the `$news_items` array in the PHP (WordPress version).

Each item supports:

```js
{
  id:        1,                      // unique number
  title:     'Headline text',
  category:  'Hardware',             // badge label
  date:      '2 hours ago',
  icon:      '⚡',                   // emoji or text
  iconColor: 'purple',               // purple | green | orange | pink | cyan
  desc:      'Short preview text (2 lines)',
  detail:    'Full story text...',
  stats:     ['240 FPS', '4K Native'],  // pill badges
  link:      'https://...',
  linkText:  'Read full announcement'
}
```

Just add or remove objects from the array — the component auto-adjusts.

---

##  Color Customization

The accent color is `#3478f6` (blue). To change it:

**CSS:** replace `#3478f6` throughout with your brand color  
**Convenient:** do a find-and-replace for `#3478f6` → your color

Icon accent options: `purple` (#8b5cf6), `green` (#10b981), `orange` (#fb923c), `pink` (#ec4899), `cyan` (#06b6d4)

---

##  Behavior

| Action | Result |
|--------|--------|
| Click ticker bar (not on a headline) | Toggle card panel open/closed |
| Click a scrolling headline | Open card panel + scroll to that card + expand it |
| Click a card | Expand/collapse that card's detail section |
| Hover ticker | Pause auto-scroll |

---

##  Browser Support

Chrome, Firefox, Safari, Edge — latest 2 versions.  
Uses `backdrop-filter: blur()` which requires modern browsers.

---

##  Design Philosophy

- **Transparent** — lets your site background show through
- **Light theme** — clean whites and soft grays
- **No shadows** — flat, crisp, modern
- **No glow** — relies on clean borders and spacing
- **Frost glass** — subtle backdrop blur for depth without heavy visual weight
- **2026 aesthetic** — minimal, spacious, typography-forward
