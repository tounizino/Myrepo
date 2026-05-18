# Cloud Gaming News Bar Component

A **2026-style, minimal, transparent** news ticker + expandable card component for cloud gaming websites. Light theme, no shadows, no glow — designed for professional cloud gaming platforms.

Two versions available: **v1** (original frosted glass style) and **v2 Sky Blue** (improved with pricing tables, spec grids, and richer card content).

---

##  Features (v2 Sky Blue)

-  **Auto-scrolling ticker** — CSS-powered seamless infinite scroll through headlines
-  **Dropdown detail cards** — click any headline, dot, or arrow to open the card
-  **Specs info grid** — key stats displayed in a clean grid layout
-  **Pricing tables** — tier/price/detail comparison tables inside cards
-  **Tag pills** — keyword badges for quick categorization
-  **Live pulsing indicator** — animated dot with ring effect
-  **Progress bar** — auto-advances to next news after 5 seconds
-  **Prev/Next controls** — navigate through news items
-  **Dot indicators** — clickable dots with auto-cycling when idle
-  **Sky blue palette** — `#0284C7`, `#0EA5E9`, `#E0F2FE` accents
-  **Transparent background** — clean, minimal, no shadows or glow
-  **Fully responsive** — mobile, tablet, desktop
-  **Escape key** closes the card
-  **WordPress ready** — paste into any Custom HTML block

---

##  Files

| File | Version | Description |
|------|---------|-------------|
| `cloud-gaming-news-bar.html` | v1 | Original frosted glass style with expandable cards |
| `cloud-gaming-news-bar-wordpress.php` | v1 | WordPress PHP version with `[cloud_gaming_news]` shortcode |
| `cloud-gaming-news-ticker-v2-skyblue.html` | v2 | **Sky Blue Edition** — improved design with pricing tables & spec grids |

> **Recommendation:** Use `cloud-gaming-news-ticker-v2-skyblue.html` — it's the latest and most polished version.

---

##  Quick Start

1. Open `cloud-gaming-news-ticker-v2-skyblue.html`
2. Copy the **entire file contents**
3. Paste into any WordPress page via **Custom HTML block** (Gutenberg) or into any HTML area of your site

> The component is fully self-contained — all CSS and JS are embedded. No plugins needed.

---

##  Adding / Editing News Items (v2 Sky Blue)

Find the `NEWS` array in the JavaScript section:

```js
var NEWS = [
  {
    platform:      'GeForce NOW',          // Platform name
    platformColor: '#16A34A',              // Badge color
    category:      'Performance',          // Category label
    date:          'May 18, 2026',
    headline:      'Your Headline Here',
    summary:       'Short description...',
    tag:           'ULTIMATE',             // Ticker tag (UPPERCASE)
    tagColor:      '#16A34A',              // Ticker tag color
    specs: [                               // Info grid
      { label: 'Max Resolution', value: '4K Native (240 fps)' },
      { label: 'Latency', value: 'Up to 38%' }
    ],
    pricing: {                             // Pricing table (optional)
      title: 'Current Plans',
      rows: [
        { tier: 'Free',       price: '$0/mo',  detail: '1080p/60' },
        { tier: 'Premium',    price: '$9.99/mo', detail: '1440p/120' }
      ],
      footnote: 'Prices may vary.'
    },
    tags: ['NVIDIA', '4K 240fps'],         // Keyword pills
    link: '#'                              // Read More URL
  }
];
```

Just add or remove objects from the array — the component auto-adjusts.

---

##  Customization

**Sky Blue palette** — CSS custom properties at the top of the `<style>` block:

```css
--c-sky-500: #0EA5E9;   /* primary accent */
--c-sky-600: #0284C7;   /* hover / links */
--c-sky-700: #0369A1;   /* label text */
```

Change these values to rebrand with your own colors.

---

##  Behavior (v2 Sky Blue)

| Action | Result |
|--------|--------|
| Click a scrolling headline | Opens that news card |
| Click a dot | Opens that news card |
| Click Prev / Next arrows | Navigate through cards |
| Click close (×) button | Closes the card |
| Press Escape key | Closes the card |
| Progress bar | Auto-advances to next news (5s) |
| Dots auto-cycle | Cycle through dots when no card is open |
| Hover ticker | Pauses auto-scroll |
| Hover away from ticker | Resumes auto-scroll |

---

##  Browser Support

Chrome, Firefox, Safari, Edge — latest 2 versions.  
Uses CSS `mask-image` and custom properties — modern browsers only.

---

##  Design Philosophy (v2 Sky Blue)

- **Sky blue palette** — calming, tech-forward, cloud-inspired
- **Transparent** — lets your site background show through
- **No shadows, no glow** — flat, crisp, modern
- **Barlow Condensed** — for labels, tags, and compact UI text
- **Outfit** — for headlines and body copy
- **2026 aesthetic** — minimal, spacious, typography-forward
- **Content-rich cards** — specs grids + pricing tables for real utility