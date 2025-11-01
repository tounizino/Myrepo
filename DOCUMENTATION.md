# Top 5 Cloud Games Section – Developer Documentation

## 📦 Package Overview

This repository ships two ready-to-embed HTML snippets that render a modern "Top 5 Games on the Cloud" showcase for WordPress sites.

| File | Theme | Description |
|------|-------|-------------|
| `top-5-cloud-games-section.html` | **Dark / Neon** | Glassmorphism-inspired background with vibrant gradients and glowing genre chips. |
| `top-5-cloud-games-section-light.html` | **Light / Pastel** | Minimal white cards, pastel genre chips, and professional presentation ideal for corporate blogs. |

Both versions share the same markup structure and JavaScript behaviour, making it easy to switch themes or maintain parity between them.

---

## ✨ Feature Highlights

- **Clickable Cards** – Entire card links to your game review or landing page.
- **Genre Chips** – Platform badges replaced with colourful genre tags displayed inside translucent capsules.
- **Compact Artwork** – Game cover region reduced to ~66% width for slimmer cards while keeping portrait ratio.
- **Responsive Layout** – Five columns on wide screens, gracefully collapsing to 3 / 2 / 1 columns on tablet and mobile.
- **Animation Suite** – Fade-in entrance, hover elevation, parallax background (dark version), and pulsating rank badge.
- **Performance-first** – Lazy-loaded images, GPU-friendly transforms, Intersection Observer driven reveal logic.
- **Accessibility** – Semantic `<article>` elements, descriptive `aria-label`s on links, focus outlines, high-contrast light theme.

---

## 🧱 HTML Structure

Each card follows the same pattern:

```html
<article class="game-card" data-game="1">
    <a class="card-link" href="https://yourdomain.com/cloud-gaming/cyberpunk-2077" aria-label="Explore Cyberpunk 2077 cloud gaming guide">
        <div class="card-inner">
            <div class="card-rank"><span class="rank-number">1</span></div>
            <div class="card-image-wrapper">
                <img src="..." alt="Cyberpunk 2077 - Top Cloud Game" loading="lazy">
                <div class="card-overlay"></div>
            </div>
            <div class="card-content">
                <h3 class="game-title">Cyberpunk 2077</h3>
                <div class="game-stats">
                    <span class="stat-item">…</span>
                </div>
                <div class="game-genres">
                    <span class="genre-chip">RPG</span>
                    <span class="genre-chip">Open World</span>
                </div>
            </div>
        </div>
    </a>
</article>
```

> **Note:** Light-theme classes use the suffix `-light` (e.g., `.game-card-light`, `.genre-chip-light`). All instructions apply equally—replace the class names with the corresponding light theme variants.

---

## 🛠️ Installation Options

### 1. WordPress Custom HTML Block (Recommended)
1. Open the page or post editor.
2. Add a **Custom HTML** block.
3. Paste the entire contents of the chosen HTML file (including `<style>` and `<script>`).
4. Preview → Publish.

### 2. WordPress Template / Theme Integration
- Include the HTML snippet directly inside your theme template file using `get_template_part` or `include`.
- Register a reusable block using ACF, Gutenberg, or a theme builder (Elementor, Divi, etc.).

### 3. PHP Include
```php
<?php
include get_template_directory() . '/partials/top-5-cloud-games-section.html';
?>
```

Ensure the referenced file path matches your theme structure.

---

## 🧩 Customization Guide

### 1. Update Links
- Modify each anchor `href` to point to the relevant article.
- Keep the descriptive `aria-label` for SEO and accessibility.

```html
<a class="card-link" href="https://yourblog.com/reviews/cyberpunk-2077" aria-label="Read the Cyberpunk 2077 cloud gaming review">
```

### 2. Swap Game Titles & Stats
- Edit `<h3 class="game-title">`, rating values, and player counts.
- Player count unit (e.g., `M`, `K`) can be replaced with any string.

### 3. Replace Artwork
- Upload 4:5 portrait covers (recommended 400 × 520 px, ≤150 KB).
- Swap the `src` value with the new image URL.
- Ensure the `alt` attribute is descriptive.

### 4. Edit Genres
- Update or duplicate `<span class="genre-chip">` entries to reflect actual genres.
- Dark theme auto-styles chips based on the parent card index.
- Light theme offers pre-defined colour helper classes: `genre-blue`, `genre-purple`, `genre-pink`, `genre-cyan`, `genre-green`.

```html
<div class="game-genres">
    <span class="genre-chip">MMO</span>
    <span class="genre-chip">Co-op</span>
</div>
```

### 5. Adjust Image Width
- Default width is `66%`. Increase or decrease to taste:

```css
.card-image-wrapper {
    width: 70%; /* Range suggestion: 55% – 80% */
}
```

Apply the corresponding `-light` class for the light theme variant.

---

## 🎨 Theming Notes

### Dark Version Highlights
- Background gradient: `#0f0c29 → #302b63 → #24243e`
- Glass effect achieved with `backdrop-filter` and translucent borders.
- Parallax background driven by a small vanilla JS helper.

### Light Version Highlights
- Soft gray gradient background for subtle depth.
- Solid white cards with pastel-colour genre chips.
- No parallax script (fewer animations for a calmer aesthetic).

### Changing Colour Palettes
- Modify gradients inside the `.top-cloud-games-*` section or individual genre chip blocks.
- Use CSS custom properties if you plan repeated theme swaps.

---

## 🧠 JavaScript Behaviour

### Dark Theme (`top-5-cloud-games-section.html`)
- **Intersection Observer** toggles `.is-visible` as cards enter the viewport.
- **Parallax Effect** adjusts the section background position on scroll.
- Scroll handler throttled with `requestAnimationFrame` for performance.

### Light Theme (`top-5-cloud-games-section-light.html`)
- Uses only the Intersection Observer for reveal animations.
- No parallax to keep the presentation airy and fast.

Both scripts are encapsulated in an IIFE to avoid polluting global scope.

---

## 🔍 SEO & Accessibility

- Descriptive `alt` attributes on every image.
- `aria-label` on card links clarifies navigation for screen readers.
- `<article>` wrappers enable richer semantics for lists of features.
- Focus outlines maintained (`:focus-within`), and `card-link:focus-visible` prevents double outlines.
- Print styles convert backgrounds to white and text to black.

Add structured data via JSON-LD beneath the section if you want Google to treat this as a list of games.

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Top 5 Cloud Games",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "item": {
        "@type": "VideoGame",
        "name": "Cyberpunk 2077",
        "genre": ["RPG", "Open World"],
        "url": "https://yourdomain.com/cloud-gaming/cyberpunk-2077"
      }
    }
  ]
}
</script>
```

---

## 📱 Responsive Breakpoints

| Viewport | Grid Columns | Notes |
|----------|--------------|-------|
| ≥1200 px | 5 columns | Cards centred with reduced max width. |
| 768–1199 px | 3 columns | Equal spacing, last two cards fill second row. |
| 520–767 px | 2 columns | Fifth card spans both columns for symmetry. |
| <520 px | 1 column | Max width capped for better readability. |

Mobile hover transforms are softened to prevent aggressive zooming on touch devices.

---

## ⚙️ Performance Tips

- **Image Optimisation:** Use modern formats (WebP/AVIF) and compress before uploading.
- **Caching:** Works flawlessly with WP Rocket, W3 Total Cache, LiteSpeed Cache, etc.
- **Minification:** Inline `<style>` and `<script>` blocks are small—minify if bundling into larger assets.
- **Lazy Loading:** Already enabled via `loading="lazy"` attributes.

---

## 🧪 Testing Checklist

1. ✅ Card links open the correct article (test in new tab).
2. ✅ Hover animations trigger smoothly on desktop.
3. ✅ Focus outlines appear when tabbing through the section.
4. ✅ Images scale correctly on retina and mobile displays.
5. ✅ Genre chips don’t wrap awkwardly—adjust text length if needed.
6. ✅ Parallax scroll (dark theme) doesn’t conflict with other scripts.

---

## ❓ Troubleshooting

| Issue | Fix |
|-------|-----|
| Cards not clickable | Ensure `<a class="card-link">` wraps the entire `card-inner`. |
| Genre chips misaligned | Reduce text length or adjust `.game-genres` `gap` value. |
| Images stretched | Maintain the 4:5 ratio; use `object-fit: cover` (already set). |
| Layout breaks inside page builders | Wrap the section inside a full-width container block. |
| Colours clash with theme | Override gradients and chip colours inside your theme stylesheet. |

---

## 📚 Additional Resources

- `QUICK-START.md` – Copy/paste checklist for content editors.
- `CHANGES.md` – Full changelog for this release (v2.0).

For feature requests or bug fixes, document them in your tracking tool and include references to the file(s) above.

---

**Maintainer:** UI/UX Specialist @ 2026 Cloud Gaming Project  
**Last Updated:** November 2026  
**Version:** 2.0
