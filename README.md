# Amazon Affiliate Product Card

This repository contains a lightweight, responsive Amazon affiliate product card built with vanilla HTML, CSS, and JavaScript. The card can be embedded into platforms such as WordPress blog posts and pulls product data either from a custom API or static configuration.

## Files

- `amazon-affiliate-card.html` – Standalone demo page that showcases the product card and the JavaScript class responsible for rendering it.

## Features

- Light theme optimized for Amazon-style content presentation.
- Responsive layout that adapts gracefully from mobile to desktop viewports.
- Supports product image, title, description, pricing (with optional discount), rating, review count, and a customizable call-to-action button.
- Automatic affiliate-tag injection into the outbound Amazon URL.
- Works with either static data or an API endpoint that returns JSON product information.

## Embedding in WordPress

1. Copy the contents of `amazon-affiliate-card.html` and place them inside a Custom HTML block within your WordPress editor.
2. Update the configuration at the bottom of the file:
   ```javascript
   new AmazonProductCard('amazon-affiliate-product', {
       affiliateTag: 'YOUR-AFFILIATE-TAG-20',
       apiUrl: 'https://your-api-endpoint.com/product',
       productId: 'PRODUCT-ASIN'
   });
   ```
   - Replace `YOUR-AFFILIATE-TAG-20` with your Amazon Associates tag.
   - Replace `https://your-api-endpoint.com/product` with your API endpoint that serves product details as JSON.
   - Replace `PRODUCT-ASIN` with the identifier you want to request from your API.
3. Alternatively, provide a `staticData` object (as shown in the demo) for manual configuration without an API.
4. Publish or preview the post—WordPress will render the responsive affiliate card.

## API Response Shape

When using the API mode, the endpoint should return JSON with the following structure:

```json
{
  "asin": "B07ZPKN6YR",
  "title": "Amazon Echo Dot (4th Gen) Smart speaker with Alexa",
  "description": "Product description here...",
  "image": "https://m.media-amazon.com/images/I/714Rq4k05UL._AC_SL1000_.jpg",
  "price": 49.99,
  "originalPrice": 59.99,
  "rating": 4.7,
  "reviewCount": "234,567",
  "badge": "Best Seller",
  "url": "https://www.amazon.com/dp/B07ZPKN6YR",
  "buttonText": "Buy Now on Amazon"
}
```

The script will automatically append your affiliate tag to the outbound product link.

## Demo

Open `amazon-affiliate-card.html` in any modern browser to see the product card in action using sample data.
