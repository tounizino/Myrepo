# Cloud Loadout - Hero Section Themes

Three modern, responsive hero section designs for WordPress with advanced search functionality.

## 🎨 Available Themes

### 1. **Black Theme** (`hero-theme-black.html`)
- **Design**: Dark, premium aesthetic with subtle blue accents
- **Background**: Deep black (#0a0a0a) with radial gradients
- **Best For**: Gaming sites, tech platforms, modern SaaS products
- **Key Features**: 
  - Glassmorphism effects
  - Glow animations on logo
  - High contrast for readability
  - Sophisticated dark UI

### 2. **Light Theme** (`hero-theme-light.html`)
- **Design**: Clean, minimal design with soft gradients
- **Background**: Light gradient (#f5f7fa to #e8ecf1)
- **Best For**: Professional sites, documentation, corporate landing pages
- **Key Features**:
  - Soft shadows and subtle transitions
  - High readability
  - Professional appearance
  - Accessible color contrasts

### 3. **Sky Theme** (`hero-theme-sky.html`)
- **Design**: Vibrant gradient with oceanic colors
- **Background**: Blue-teal gradient (#0b132b to #5bc0be)
- **Best For**: Creative portfolios, gaming communities, dynamic brands
- **Key Features**:
  - Multi-layered gradient backgrounds
  - Enhanced glassmorphism
  - Vibrant, eye-catching colors
  - Premium feel with shimmer effects

## ✨ Key Features (All Themes)

### Modern 2026 Design Elements
- **Glassmorphism**: Frosted glass effects with backdrop blur
- **Smooth Animations**: Float, glow, and shimmer effects
- **Gradient Overlays**: Multi-layered radial gradients
- **Modern Typography**: System fonts with perfect spacing
- **Micro-interactions**: Hover states and transitions

### Search Functionality
- **External Search Button**: Modern layout with button outside input
- **Icon Inside Input**: Search icon positioned inside the input field
- **Real-time Search**: Debounced input with 300ms delay
- **WordPress Integration**: Fetches from WP REST API (posts & pages)
- **Smart Scoring**: Title, excerpt, and content relevance scoring
- **Dropdown Results**: Animated dropdown with smooth transitions
- **Responsive Design**: Stacks vertically on mobile devices

### Navigation Features
- **4 Quick Links**: Performance, Guides, Configs, Troubleshooting
- **Icon-based**: Font Awesome 6.5.2 icons
- **Hover Effects**: Smooth transitions with lift effect
- **Mobile Optimized**: Responsive grid layout

## 🚀 Implementation

### Basic Usage
Simply copy the entire HTML content and paste into your WordPress page/post editor as an HTML block or custom HTML widget.

### WordPress Integration
```html
<!-- Add to your WordPress theme or page builder -->
<div class="custom-hero-section">
  [Paste theme HTML here]
</div>
```

### Customization

#### Change Search Endpoint URLs
Update the feature links (around line 521-540):
```html
<a href="/your-custom-url" class="feature-item">
```

#### Modify Colors
Each theme has color variables in the CSS. Search for these and replace:
- **Black Theme**: `#5dade2` (blue accent)
- **Light Theme**: `#5dade2` (blue accent), `#2c3e50` (dark text)
- **Sky Theme**: `#5bc0be` (teal accent), `#0b132b` (dark blue)

#### Adjust Search Settings
In the JavaScript section (around line 563):
```javascript
const searchParams = new URLSearchParams({
    search: query,
    per_page: 10,        // Number of results
    _embed: 'true',
    orderby: 'relevance'
});
```

## 📱 Responsive Breakpoints

- **Desktop**: Full layout with horizontal search bar
- **Tablet** (≤768px): Vertical search layout, adjusted spacing
- **Mobile** (≤480px): Compact design, optimized touch targets

## 🔧 Technical Details

### Dependencies
- **Font Awesome 6.5.2**: Icons (CDN included)
- **WordPress REST API**: For search functionality
- **Modern Browsers**: Supports CSS backdrop-filter, gradients, animations

### Browser Support
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+

### Performance
- **No jQuery**: Vanilla JavaScript for optimal performance
- **Debounced Search**: Prevents excessive API calls
- **CSS Animations**: Hardware-accelerated transforms
- **Minimal DOM**: Efficient rendering

## 🎯 Search Functionality Details

### Search Flow
1. User types in search input (minimum 2 characters)
2. 300ms debounce delay
3. Fetches from `/wp-json/wp/v2/posts` and `/wp-json/wp/v2/pages`
4. Scores results based on relevance
5. Displays top 8 results in dropdown
6. Click result navigates to page
7. Submit form redirects to WordPress search page

### Search Scoring Algorithm
- **Title match**: +10 points
- **Title starts with query**: +5 points
- **Excerpt match**: +3 points
- **Content match**: +1 point

Results are sorted by score (highest first) and limited to 8 items.

## 💡 Tips

1. **Performance**: For large sites, consider adding caching to the search API
2. **SEO**: Ensure your WordPress site has proper meta descriptions for better search excerpts
3. **Accessibility**: All themes include proper ARIA labels and semantic HTML
4. **Testing**: Test search functionality in your WordPress environment before deploying

## 🔐 Security Notes

- Search uses WordPress REST API (built-in security)
- No SQL injection risks (API handles sanitization)
- XSS protection via proper HTML escaping
- All external links should be reviewed

## 📝 License

These themes are provided as-is for use in WordPress projects. Feel free to modify and customize as needed.

## 🆘 Troubleshooting

### Search Not Working
- Check WordPress REST API is enabled: `/wp-json/wp/v2/posts`
- Verify CORS settings if using subdomain
- Check browser console for errors

### Styling Conflicts
- All styles use `!important` to override theme conflicts
- If needed, increase specificity or adjust z-index values

### Mobile Issues
- Ensure viewport meta tag is present in your theme
- Test on actual devices, not just browser devtools
- Check for conflicting touch event handlers

---

**Created with 2026 design principles** | Modern • Responsive • Fast
