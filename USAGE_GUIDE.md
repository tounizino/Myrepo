# Amazon Affiliate Product Card - Usage Guide

This guide will walk you through how to use the Amazon Affiliate Product Card in your WordPress blog posts to earn affiliate commissions.

## Table of Contents

1. [Quick Start](#quick-start)
2. [WordPress Integration](#wordpress-integration)
3. [Configuration Options](#configuration-options)
4. [API Setup](#api-setup)
5. [Customization](#customization)
6. [Mobile Responsiveness](#mobile-responsiveness)
7. [Best Practices](#best-practices)

## Quick Start

The fastest way to get started is to use the `wordpress-embed-code.html` file with static product data:

1. Open `wordpress-embed-code.html` in a text editor
2. Replace `{UNIQUE_ID}` with something like `product1` (make it unique for each product card on the same page)
3. Update the configuration section:
   - Set your `affiliateTag` (e.g., `'yourname-20'`)
   - Comment out the `apiUrl` and `productId` lines
   - Uncomment the `staticData` section
   - Fill in your product details
4. Copy the entire code
5. In WordPress, add a "Custom HTML" block
6. Paste the code and publish

## WordPress Integration

### Method 1: Custom HTML Block (Recommended)

**Step 1:** In your WordPress editor, click the `+` button to add a new block.

**Step 2:** Search for "Custom HTML" and select it.

**Step 3:** Paste the embed code from `wordpress-embed-code.html`.

**Step 4:** Update the configuration section with your product details.

**Step 5:** Preview or publish your post.

### Method 2: Classic Editor

If you're using the Classic Editor:

**Step 1:** Switch to the "Text" tab (not "Visual").

**Step 2:** Paste the embed code where you want the product card to appear.

**Step 3:** Update the configuration section.

**Step 4:** Save your post.

### Method 3: Shortcode (Advanced)

For advanced users, you can create a WordPress shortcode in your theme's `functions.php`:

```php
function amazon_product_card_shortcode($atts) {
    $atts = shortcode_atts(array(
        'asin' => '',
        'tag' => 'your-affiliate-tag-20'
    ), $atts);
    
    // Include the HTML/CSS/JS from wordpress-embed-code.html
    ob_start();
    include(get_template_directory() . '/amazon-product-card-template.php');
    return ob_get_clean();
}
add_shortcode('amazon_product', 'amazon_product_card_shortcode');
```

Then use it in your posts: `[amazon_product asin="B07ZPKN6YR"]`

## Configuration Options

### Basic Configuration

```javascript
var config = {
    affiliateTag: 'YOUR-AFFILIATE-TAG-20',  // Your Amazon Associates tag
    apiUrl: 'https://api.example.com/product',  // Your API endpoint
    productId: 'B07ZPKN6YR'  // Product ASIN
};
```

### Static Data Configuration

```javascript
var config = {
    affiliateTag: 'YOUR-AFFILIATE-TAG-20',
    staticData: {
        asin: 'B07ZPKN6YR',
        title: 'Product Title',
        description: 'Product description here...',
        image: 'https://example.com/image.jpg',
        price: 49.99,
        originalPrice: 59.99,  // Optional: for showing discounts
        rating: 4.7,  // Optional: 0-5 scale
        reviewCount: '234,567',  // Optional: string or number
        badge: 'Best Seller',  // Optional: display badge on image
        url: 'https://www.amazon.com/dp/B07ZPKN6YR',
        buttonText: 'Buy Now on Amazon'  // Customize button text
    }
};
```

### Configuration Properties

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `affiliateTag` | string | Yes | Your Amazon Associates affiliate tag |
| `apiUrl` | string | No* | Your API endpoint URL |
| `productId` | string | No* | Product ASIN to fetch from API |
| `staticData` | object | No* | Product data object (use if not using API) |

*Either provide `apiUrl` + `productId` OR `staticData`, not both.

## API Setup

### Option 1: Custom API

Create a backend API that returns product data in the following JSON format:

```json
{
  "asin": "B07ZPKN6YR",
  "title": "Product Title",
  "description": "Product description",
  "image": "https://example.com/image.jpg",
  "price": 49.99,
  "originalPrice": 59.99,
  "rating": 4.7,
  "reviewCount": "234,567",
  "badge": "Best Seller",
  "url": "https://www.amazon.com/dp/B07ZPKN6YR",
  "buttonText": "Buy Now on Amazon"
}
```

Your API endpoint should accept a `productId` query parameter:
```
GET https://your-api.com/product?productId=B07ZPKN6YR
```

See `example-api-endpoint.js` for a Node.js/Express implementation example.

### Option 2: Amazon Product Advertising API

For real-time Amazon data, use the [Amazon Product Advertising API](https://webservices.amazon.com/paapi5/documentation/):

1. Sign up for the Product Advertising API
2. Get your Access Key and Secret Key
3. Implement an API endpoint that fetches data from Amazon
4. Transform the response to match the required JSON format

**Important:** Amazon's API has usage limits and requirements. Read their terms of service carefully.

### CORS Considerations

If your API is on a different domain than your WordPress site, ensure your API sends proper CORS headers:

```javascript
res.header('Access-Control-Allow-Origin', '*');
res.header('Access-Control-Allow-Methods', 'GET');
```

## Customization

### Styling

All styles are contained within the `<style>` block in the embed code. You can customize:

- **Colors**: Search for hex colors like `#FF9900` (Amazon orange) and replace them
- **Fonts**: Change the `font-family` property
- **Spacing**: Adjust `padding`, `margin`, and `gap` values
- **Border radius**: Modify `border-radius` for rounded corners
- **Shadows**: Change `box-shadow` values

### Button Text

Customize the button text in your product data:

```javascript
buttonText: 'Check Latest Price'  // Instead of default "View on Amazon"
```

### Display Multiple Cards

To show multiple products on the same page, use unique IDs:

```html
<div id="amazon-affiliate-product-product1"></div>
<script>
    new AmazonProductCard('amazon-affiliate-product-product1', { ... });
</script>

<div id="amazon-affiliate-product-product2"></div>
<script>
    new AmazonProductCard('amazon-affiliate-product-product2', { ... });
</script>
```

## Mobile Responsiveness

The product card is fully responsive with three breakpoints:

- **Desktop** (769px+): Full-size card, maximum 400px width
- **Tablet** (481px - 768px): Full-width card
- **Mobile** (up to 480px): Optimized layout with smaller fonts and padding

The card automatically adapts to different screen sizes without any additional configuration.

### Testing Responsiveness

Test your product card on different devices:
- Use Chrome DevTools (F12) → Toggle Device Toolbar
- Test on actual mobile devices
- Check in WordPress preview mode

## Best Practices

### 1. Affiliate Disclosure

Always disclose that you're using affiliate links. Add this near your product cards:

```html
<p><em>As an Amazon Associate, I earn from qualifying purchases.</em></p>
```

### 2. Product Selection

- Choose products relevant to your content
- Use high-quality product images
- Write compelling descriptions
- Focus on products with good ratings

### 3. Performance

- Use the `loading="lazy"` attribute on images (already included)
- Consider hosting static product images on your server or CDN
- Cache API responses if possible

### 4. SEO

- Use descriptive product titles
- Include keywords in descriptions
- Use proper heading hierarchy in your blog post
- Add `rel="nofollow noopener sponsored"` to affiliate links (already included)

### 5. Legal Compliance

- Follow [Amazon Associates Program Operating Agreement](https://affiliate-program.amazon.com/help/operating/agreement)
- Include proper disclosures
- Don't manipulate prices or availability
- Use the correct affiliate tag for your region

### 6. Updating Prices

- Prices change frequently on Amazon
- Use the API method to fetch real-time prices
- If using static data, update prices regularly
- Consider adding "Last updated" timestamps

### 7. Link Attributes

The card automatically adds proper link attributes:
- `target="_blank"`: Opens in new tab
- `rel="nofollow noopener sponsored"`: Proper SEO and security attributes

## Troubleshooting

### Card doesn't display
- Check browser console for JavaScript errors
- Verify the container ID matches in both HTML and JavaScript
- Ensure all required fields are provided in configuration

### API errors
- Check API endpoint URL is correct
- Verify CORS headers are set
- Test API endpoint directly in browser or Postman
- Check network tab in browser DevTools

### Affiliate tag not working
- Verify your affiliate tag is correct (should end with `-20` or similar)
- Check the generated URL includes `?tag=YOUR-TAG`
- Ensure you're registered with Amazon Associates

### Styling conflicts
- WordPress themes may override styles
- Add `!important` to critical styles if needed
- Use more specific CSS selectors
- Check for conflicting CSS in theme

## Support and Updates

For issues or questions:
1. Check this usage guide first
2. Review the example files provided
3. Test with the standalone demo (`amazon-affiliate-card.html`)
4. Verify your affiliate tag is valid and active

## License

This code is provided as-is for use in your affiliate marketing efforts. Modify and use it as needed for your WordPress blog.
