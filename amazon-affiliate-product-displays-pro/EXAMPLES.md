# Shortcode Examples

This file contains practical examples of all available shortcodes for the Amazon Affiliate Product Displays Pro plugin.

---

## Single Product Card

Display a single product in a beautiful card layout.

```
[amazon_card asin="B0CX57B5F4"]
```

---

## Grid Layout (3 Columns)

Display multiple products in a 3-column responsive grid.

```
[amazon_products asin="B0CX57B5F4,B0F2TB1KNV,B0ABC123XY" layout="grid" columns="3"]
```

---

## Grid Layout (4 Columns)

Perfect for wider pages or full-width sections.

```
[amazon_products asin="B0CX57B5F4,B0F2TB1KNV,B0ABC123XY,B0DEF456ZA" layout="grid" columns="4"]
```

---

## List Layout

Display products in a horizontal list format (great for sidebars).

```
[amazon_products asin="B0CX57B5F4,B0F2TB1KNV" layout="list"]
```

---

## Carousel Layout (6 Products)

Create a swipeable product carousel with navigation arrows and dots.

```
[amazon_products keyword="gaming laptop" layout="carousel" limit="6"]
```

---

## Carousel by ASIN

```
[amazon_products asin="B0CX57B5F4,B0F2TB1KNV,B0ABC123XY,B0DEF456ZA,B0GHI789BC,B0JKL012DE" layout="carousel"]
```

---

## Badge Layout (Minimal)

Minimal product badges perfect for inline displays or compact sections.

```
[amazon_products keyword="wireless earbuds" layout="badge" limit="4"]
```

---

## Keyword Search Examples

### Gaming Products
```
[amazon_products keyword="gaming mouse" layout="grid" columns="3" limit="6"]
```

### Home & Kitchen
```
[amazon_products keyword="instant pot" layout="carousel" limit="8"]
```

### Electronics
```
[amazon_products keyword="noise cancelling headphones" layout="list" limit="5"]
```

### Books
```
[amazon_products keyword="python programming" layout="grid" columns="2" limit="4"]
```

---

## Advanced Use Cases

### Product Comparison Grid
```
[amazon_products asin="B0ASIN001,B0ASIN002,B0ASIN003" layout="grid" columns="3"]
```

### Sidebar Recommendations
```
[amazon_products keyword="bestselling books" layout="badge" limit="3"]
```

### Full-Width Featured Carousel
```
[amazon_products keyword="4k monitor" layout="carousel" limit="10"]
```

### Compact List for Blog Posts
```
[amazon_products asin="B0ASIN001,B0ASIN002" layout="list"]
```

---

## Tips

- **ASIN vs Keyword**: Use `asin` for specific products you want to promote. Use `keyword` to dynamically display search results.
- **Columns**: Choose 2-3 columns for mobile-friendly layouts, 3-4 for desktop-focused pages.
- **Limit**: Amazon PA-API allows up to 10 products per search. Use `limit` to control how many are displayed.
- **Layout Choice**: 
  - `card` or `grid` for detailed product info
  - `list` for compact displays
  - `carousel` for engaging, swipeable galleries
  - `badge` for minimal, space-saving designs

---

## Customization

All shortcodes respect your global settings (colors, theme, dark mode, animations) configured in **Settings > Amazon Displays**.

For layout-specific overrides, use the **Custom CSS** field in the admin settings.
