# Amazon Affiliate Product Displays Pro

**Version:** 1.0.0  
**Author:** Your Name  
**License:** GPL v2 or later  

Modern, responsive Amazon affiliate product displays powered by the Amazon Product Advertising API 5.0.

## Features

✅ **Full PA-API 5.0 Integration** – Server-side signed requests with your Access Key, Secret Key, and Associate Tag  
✅ **Secure Credentials** – Stored safely in WordPress database, never exposed on frontend  
✅ **Smart Caching** – Reduce API calls with WordPress transients  
✅ **Multiple Layouts** – Card, Grid, List, Carousel, Badge  
✅ **Modern 2025 Design** – Smooth animations, gradients, shadows, rounded corners  
✅ **Dark Mode Support** – Toggle dark mode styling  
✅ **Prime Badge & Discount Badge** – Highlight Prime-eligible products and savings  
✅ **Customizable Styling** – Color pickers, theme presets (Amazon Classic, Minimal White, Dark Neon, Glassmorphism)  
✅ **Responsive & Mobile-Friendly** – Works beautifully on all screen sizes  
✅ **Custom CSS Override** – Advanced users can add custom styles  

---

## Installation

1. Download the plugin folder.
2. Upload `amazon-affiliate-product-displays-pro` to `/wp-content/plugins/` on your WordPress site.
3. Activate the plugin through the **Plugins** menu in WordPress.
4. Go to **Settings > Amazon Displays** to configure your API credentials.

---

## Configuration

### 1. Amazon API Credentials

1. Sign up for [Amazon Associates](https://affiliate-program.amazon.com/).
2. Register for [Amazon Product Advertising API 5.0](https://webservices.amazon.com/paapi5/documentation/).
3. In WordPress, go to **Settings > Amazon Displays**.
4. Enter your **Access Key**, **Secret Key**, and **Associate Tag**.
5. Select your **PA-API Region** (e.g., `us-east-1` for North America).
6. Choose your default **Marketplace** (e.g., `www.amazon.com` for United States).

### 2. Styling & Appearance

- **Theme Preset**: Choose from Amazon Classic, Minimal White, Dark Neon, or Glassmorphism.
- **Typography**: Use system fonts or specify a Google Font (e.g., `Inter:wght@400;500;700`).
- **Colors**: Customize button gradients, accent colors, and hover colors with color pickers.

### 3. Feature Toggles

- **Show Prime Badge**: Display Prime badge on eligible products.
- **Show Review Count**: Show star ratings and review count.
- **Show Discount Badge**: Display savings percentage when available.
- **Enable Animations**: Smooth hover and entrance animations.
- **Dark Mode**: Enable dark mode styling for your displays.

### 4. Advanced Options

- **Cache Duration**: Set cache duration in seconds (default: 3600 = 1 hour). Set to 0 to disable caching.
- **Custom CSS**: Add your own CSS to further customize layouts.

---

## Shortcode Usage

### Single Product Card

Display a single product in a modern card layout:

```
[amazon_card asin="B0CX57B5F4"]
```

**Parameters:**
- `asin` (required) – Amazon ASIN of the product.
- `layout` (optional) – Layout type. Default: `card`.

---

### Multiple Products by ASIN (Grid Layout)

Display multiple products in a responsive grid:

```
[amazon_products asin="B0CX57B5F4,B0F2TB1KNV,B0ABC123XY" layout="grid" columns="3"]
```

**Parameters:**
- `asin` (required if no keyword) – Comma-separated list of ASINs.
- `layout` (optional) – `card`, `grid`, `list`, `carousel`, or `badge`. Default: `grid`.
- `columns` (optional) – Number of columns (1–6). Default: `3`.

---

### Product Search by Keyword (Carousel Layout)

Display products by keyword in a swipeable carousel:

```
[amazon_products keyword="gaming laptop" layout="carousel" limit="6"]
```

**Parameters:**
- `keyword` (required if no asin) – Search keyword.
- `layout` (optional) – Layout type. Default: `grid`.
- `limit` (optional) – Maximum products to display (1–10). Default: `10`.
- `columns` (optional) – Number of columns (applies to grid layout).

---

### List Layout

Display products in a horizontal list format:

```
[amazon_products asin="B0CX57B5F4,B0F2TB1KNV" layout="list"]
```

---

### Badge Layout

Display products in a minimal badge format:

```
[amazon_products keyword="wireless earbuds" layout="badge" limit="4"]
```

---

## Layout Examples

| Layout       | Description                                           | Best Use Case                      |
|--------------|-------------------------------------------------------|------------------------------------|
| **card**     | Single product card with image, features, rating     | Single product spotlight           |
| **grid**     | Responsive multi-column grid                         | Product comparison grids           |
| **list**     | Horizontal list with thumbnail and info              | Compact lists, sidebars            |
| **carousel** | Swipeable slider with navigation arrows and dots     | Featured product carousels         |
| **badge**    | Minimal design with image, price, and button         | Quick product badges, inline links |

---

## Theme Presets

| Preset              | Description                                      |
|---------------------|--------------------------------------------------|
| **Amazon Classic**  | Amazon's signature orange and blue colors        |
| **Minimal White**   | Clean, minimal white and gray design             |
| **Dark Neon**       | Dark background with vibrant neon accents        |
| **Glassmorphism**   | Modern glass-like translucent design             |

---

## Disclaimer

All product displays include the required FTC disclosure:

> "As an Amazon Associate I earn from qualifying purchases."

This text is automatically added to every display and cannot be removed (per Amazon Associates Program Operating Agreement).

---

## Requirements

- **WordPress:** 5.0 or higher
- **PHP:** 7.4 or higher
- **Amazon PA-API 5.0 Credentials:** Access Key, Secret Key, Associate Tag
- **cURL or WP HTTP API:** For making API requests

---

## Support & Documentation

For support, please visit our [documentation site](#) or contact us at [support@example.com](mailto:support@example.com).

---

## Changelog

### Version 1.0.0
- Initial release
- PA-API 5.0 integration
- Multiple layouts: card, grid, list, carousel, badge
- Theme presets and color customization
- Dark mode support
- Smart caching with WordPress transients
- Responsive design

---

## License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025 Your Name

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
```
