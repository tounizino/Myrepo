# Cloud Loadout Header

Custom header/menu implementation for Cloud Loadout website.

## Features

### Logo Integration
- Custom SVG cloud logo with gradient fill (#2e86c1 to #1f6cab)
- Stylized cloud with configuration/settings indicator (gear-like dots)
- Optimized SVG path for crisp rendering at any size
- Responsive scaling maintained across all breakpoints
- Falls back to icon-only on mobile for space efficiency

### Mobile Optimization
- **Overlay backdrop**: Semi-transparent overlay (#154360 at 40% opacity) for better focus
- **Scroll lock**: Body scroll locked when menu is open for focused navigation
- **Tap target sizing**: 44x44px minimum touch targets on all interactive elements
- **Emoji-free close button**: Uses Font Awesome `fa-xmark` instead of `&times;`
- **Accessibility**: Full ARIA support (`aria-expanded`, `aria-controls`, `aria-hidden`)
- **Escape key**: Menu closes on Escape key press
- **Focus management**: Focus moves to close button when opening, back to toggle when closing
- **Smooth cubic-bezier transition**: `cubic-bezier(.4,0,.2,1)` for natural feel
- **Wider viewport support**: Uses `min(320px, 85vw)` for optimal sizing
- **Reduced motion support**: CSS media query respects `prefers-reduced-motion`

### Performance Optimizations
- **CSS Variables**: Centralized color/spacing system for consistency and easier theming
- **Minified selectors**: Combined similar rules, reduced repetition
- **Lazy-loaded icons**: Font Awesome with `media="print" onload="this.media='all'"` for faster LCP
- **Minified JavaScript**: Production-ready minified code
- **Reduced paint operations**: Removed unnecessary `background:` changes on focus
- **Hardware acceleration**: CSS-only transitions (no JS animation overhead)
- **Font rendering**: `-webkit-font-smoothing:antialiased` for cleaner text
- **Compact file size**: Significant reduction from original (~21KB vs original)
- **WordPress cache-friendly**: No inline styles that change on user interaction
- **Event delegation**: Efficient event handling with early returns

### Accessibility Improvements
- Semantic HTML structure with proper landmark roles
- Skip link support ready
- Focus visible outlines on all interactive elements
- ARIA labels on all icons and controls
- Screen reader friendly search results
- Keyboard navigation support (Tab, Escape)

## Files

- `header-optimized.html` - Complete, ready-to-paste header code
- `README.md` - This documentation

## Installation

1. Copy the content of `header-optimized.html`
2. Paste into your WordPress theme's header file or custom HTML block
3. The code is self-contained and WordPress-compatible

## Browser Support

- All modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile browsers (iOS Safari, Chrome Mobile)
- Graceful degradation for older browsers
- Reduced motion preference respected
