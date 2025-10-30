# Block Examples & Use Cases

This document provides real-world examples and use cases for each block in the Ultimate Blocks for Cloud Gaming plugin.

## Homepage Layouts

### Layout 1: Classic Blog Homepage

```
┌─────────────────────────────────────────┐
│         Gaming Hero Banner              │
│   "Welcome to Cloud Gaming Central"     │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│       Featured Posts (Horizontal)       │
│         3 Latest Reviews                │
└─────────────────────────────────────────┘

┌───────────┬───────────┬───────────┐
│  Post 1   │  Post 2   │  Post 3   │
├───────────┼───────────┼───────────┤
│  Post 4   │  Post 5   │  Post 6   │
├───────────┼───────────┼───────────┤
│  Post 7   │  Post 8   │  Post 9   │
└───────────┴───────────┴───────────┘
        Latest Posts Grid

┌─────────────────────────────────────────┐
│       Newsletter Signup                 │
└─────────────────────────────────────────┘
```

**Blocks Used:**
1. Gaming Hero
2. Featured Posts (3 posts, horizontal layout)
3. Latest Posts Grid (9 posts, 3 columns, pagination enabled)
4. Newsletter Signup

---

### Layout 2: Platform-Focused Homepage

```
┌─────────────────────────────────────────┐
│         Gaming Hero Banner              │
└─────────────────────────────────────────┘

┌───────────┬───────────┬───────────┐
│ GeForce   │   Xbox    │    PS     │
│   NOW     │   Cloud   │   Plus    │
├───────────┼───────────┼───────────┤
│  Amazon   │  Shadow   │   More    │
│   Luna    │           │           │
└───────────┴───────────┴───────────┘
     Category Showcase (Platforms)

┌───────────┬───────────┬───────────┐
│  Post 1   │  Post 2   │  Post 3   │
├───────────┼───────────┼───────────┤
│  Post 4   │  Post 5   │  Post 6   │
└───────────┴───────────┴───────────┘
     Latest Posts Grid (2 rows)

┌─────────────────────────────────────────┐
│    Streaming Platforms Available        │
│      [Platform Logos/Links]             │
└─────────────────────────────────────────┘
```

---

### Layout 3: Review-Heavy Site

```
┌─────────────────────────────────────────┐
│   "Game Reviews & Performance Tests"    │
│           Gaming Hero                   │
└─────────────────────────────────────────┘

┌───────────────────┬─────────────────────┐
│                   │                     │
│   Latest Review   │   Performance Stats │
│   Review Card     │   60 FPS / 4K / HDR │
│   ★★★★☆           │                     │
│                   │                     │
└───────────────────┴─────────────────────┘

┌───────────┬───────────┬───────────┬───────────┐
│  Post 1   │  Post 2   │  Post 3   │  Post 4   │
└───────────┴───────────┴───────────┴───────────┘
          Latest Posts Grid (4 columns)
```

---

## Block-Specific Examples

### 1. Latest Posts Grid

**Use Case**: Main content area displaying recent articles

**Configuration Example**:
```
Posts Per Page: 9
Columns: 3
Show Image: ✓
Show Date: ✓
Show Excerpt: ✓
Show Category: ✓
Pagination: ✓
Order By: Date
Order: Descending
```

**Best For**:
- Homepage main content
- Category archive pages
- Author archive pages
- Tag pages

---

### 2. Featured Posts

**Use Case**: Highlight top content below hero section

**Configuration Example**:
```
Number of Posts: 3
Layout: Horizontal
Show Image: ✓
Show Date: ✓
Show Excerpt: ✓
```

**Shortcode Alternative** (if needed):
```
[ubcg_featured_posts count="3" layout="horizontal"]
```

**Best For**:
- Homepage featured section
- "Editor's Picks" section
- "Most Popular" section

---

### 3. Category Showcase

**Use Case**: Display gaming platforms as categories

**Configuration Example**:
```
Columns: 3
Show Count: ✓
Show Description: ✓
```

**Example Categories**:
- GeForce NOW (45 articles)
- Xbox Cloud Gaming (38 articles)
- PlayStation Plus (32 articles)
- Amazon Luna (18 articles)
- Shadow (15 articles)
- Boosteroid (12 articles)

**Best For**:
- Homepage navigation
- Sidebar navigation
- Footer links

---

### 4. Gaming Hero

**Use Case**: Homepage banner with call-to-action

**Configuration Examples**:

**Example 1: News Site**
```
Title: "Cloud Gaming News & Reviews"
Subtitle: "Stay updated with the latest in cloud gaming technology"
Button Text: "Browse All Articles"
Button Link: /articles/
Background: Gaming-themed image
```

**Example 2: Review Site**
```
Title: "Unbiased Cloud Gaming Reviews"
Subtitle: "Real performance tests. Real opinions."
Button Text: "See Latest Reviews"
Button Link: /category/reviews/
```

**Example 3: Community Site**
```
Title: "Join the Cloud Gaming Revolution"
Subtitle: "Connect with gamers streaming from anywhere"
Button Text: "Join Community"
Button Link: /community/
```

---

### 5. Review Card

**Use Case**: Showcase individual game review

**Configuration Example**:
```
Post ID: 123
Rating: 4.5 stars
Show Rating: ✓
```

**Example Usage**:
```html
<!-- Review for "Cyberpunk 2077 on GeForce NOW" -->
Rating: ★★★★☆ (4/5)
Performance: Excellent
Latency: Low
Graphics: Ultra Settings @ 1080p 60fps
```

**Best For**:
- Review roundup pages
- Sidebar featured review
- Related reviews section

---

### 6. Game Specs

**Use Case**: Display technical specifications for games

**Configuration Example**:
```json
{
  "gameName": "Cyberpunk 2077",
  "specs": [
    {"label": "Recommended Platform", "value": "GeForce NOW Priority"},
    {"label": "Resolution", "value": "1080p / 1440p"},
    {"label": "Frame Rate", "value": "60 FPS"},
    {"label": "Ray Tracing", "value": "Supported"},
    {"label": "Required Bandwidth", "value": "15 Mbps"},
    {"label": "Latency", "value": "<20ms"}
  ]
}
```

**Best For**:
- Game review pages
- Comparison articles
- Tech analysis posts

---

### 7. Streaming Platforms

**Use Case**: Show where a game is available

**Configuration Example**:
```json
{
  "platforms": [
    {"name": "GeForce NOW", "url": "https://play.geforcenow.com/"},
    {"name": "Xbox Cloud Gaming", "url": "https://xbox.com/play"},
    {"name": "PlayStation Plus", "url": "https://playstation.com/ps-plus/"}
  ]
}
```

**Best For**:
- Game availability pages
- Platform comparison articles
- Footer sections

---

### 8. Performance Stats

**Use Case**: Display performance metrics

**Configuration Example**:
```json
{
  "stats": [
    {"value": "60", "label": "FPS Average"},
    {"value": "1080p", "label": "Resolution"},
    {"value": "15ms", "label": "Average Latency"},
    {"value": "99%", "label": "Uptime"}
  ]
}
```

**Best For**:
- Performance review articles
- Comparison pages
- Platform overview pages

---

### 9. Tag Cloud Gaming

**Use Case**: Display popular topics and games

**Configuration Example**:
```
Number of Tags: 30
Order By: Count
Order: Descending
```

**Example Tags**:
- Cyberpunk 2077 (45 posts)
- Fortnite (38 posts)
- Call of Duty (35 posts)
- FIFA 24 (28 posts)
- Destiny 2 (25 posts)

**Best For**:
- Sidebar widget area
- Archive page top
- Footer tag cloud

---

### 10. Newsletter Signup

**Use Case**: Collect email subscriptions

**Configuration Example**:
```
Title: "Never Miss a Review"
Description: "Get weekly cloud gaming news delivered to your inbox"
Button Text: "Subscribe Now"
```

**Best For**:
- Homepage bottom
- Sidebar widget
- After article content
- Popup (with page builder)

---

## Sidebar Widget Examples

### Sidebar Layout Example

```
┌─────────────────────┐
│  UBCG: Latest Posts │
│  ─────────────────  │
│  • Post Title 1     │
│  • Post Title 2     │
│  • Post Title 3     │
│  • Post Title 4     │
│  • Post Title 5     │
└─────────────────────┘

┌─────────────────────┐
│ UBCG: Category List │
│  ─────────────────  │
│  • GeForce NOW (45) │
│  • Xbox Cloud (38)  │
│  • PS Plus (32)     │
│  • Luna (18)        │
└─────────────────────┘

┌─────────────────────┐
│ UBCG: Featured Post │
│  ─────────────────  │
│  [Featured Image]   │
│  "Top Article"      │
│  Brief excerpt...   │
└─────────────────────┘
```

---

## Page Templates

### Single Post Template

```
┌─────────────────────────────────────────┐
│          Article Title                   │
│          Metadata (Date, Author)         │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│       [Featured Image]                   │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│                                          │
│       Article Content Here               │
│                                          │
└─────────────────────────────────────────┘

┌──────────────────┬──────────────────────┐
│   Game Specs     │  Performance Stats   │
└──────────────────┴──────────────────────┘

┌─────────────────────────────────────────┐
│      Streaming Platforms Available       │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│      Related Posts (Latest Posts Grid)   │
└─────────────────────────────────────────┘
```

---

### Category Archive Template

```
┌─────────────────────────────────────────┐
│      Category: GeForce NOW               │
│      45 Articles about GeForce NOW       │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│    Featured Posts in this Category       │
└─────────────────────────────────────────┘

┌───────────┬───────────┬───────────┐
│  Post 1   │  Post 2   │  Post 3   │
├───────────┼───────────┼───────────┤
│  Post 4   │  Post 5   │  Post 6   │
├───────────┼───────────┼───────────┤
│  Post 7   │  Post 8   │  Post 9   │
└───────────┴───────────┴───────────┘
     Latest Posts Grid (Category Filter)
     
┌─────────────────────────────────────────┐
│          Pagination Links                │
└─────────────────────────────────────────┘
```

---

## Custom CSS Examples

### Example 1: Make Grid 4 Columns on Large Screens

```css
@media (min-width: 1400px) {
    .ubcg-latest-posts-grid.ubcg-columns-3 {
        grid-template-columns: repeat(4, 1fr);
    }
}
```

### Example 2: Add Box Shadow to Cards

```css
.ubcg-post-card {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.ubcg-post-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}
```

### Example 3: Custom Hero Background

```css
.ubcg-gaming-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

### Example 4: Rounded Images

```css
.ubcg-post-thumbnail img {
    border-radius: 12px;
}
```

---

## Integration Examples

### With Contact Form 7

Place a newsletter signup block above or below your contact form for maximum engagement.

### With WooCommerce

Use blocks on product category pages to show related blog content about games available in your store.

### With Yoast SEO

The plugin works seamlessly with Yoast SEO for optimal search engine optimization.

---

## Performance Tips

1. **Limit Posts Per Page**: 9 or 12 posts max for best performance
2. **Optimize Images**: Use WebP format when possible
3. **Enable Caching**: Use a caching plugin
4. **Lazy Loading**: Built-in, but ensure it's not disabled
5. **CDN**: Consider using a CDN for static assets

---

## Need More Examples?

Check the documentation at [your-site.com/docs] or contact support for custom implementation help.
