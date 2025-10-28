// Example Node.js/Express API Endpoint for Amazon Product Data
// This is a sample implementation - adapt it to your backend technology

const express = require('express');
const router = express.Router();

// In-memory product database (replace with actual database or Amazon Product Advertising API)
const products = {
    'B07ZPKN6YR': {
        asin: 'B07ZPKN6YR',
        title: 'Amazon Echo Dot (4th Gen) Smart speaker with Alexa',
        description: 'Meet Echo Dot - Our most popular smart speaker with Alexa. The sleek, compact design delivers crisp vocals and balanced bass for full sound.',
        image: 'https://m.media-amazon.com/images/I/714Rq4k05UL._AC_SL1000_.jpg',
        price: 49.99,
        originalPrice: 59.99,
        rating: 4.7,
        reviewCount: '234,567',
        badge: 'Best Seller',
        url: 'https://www.amazon.com/dp/B07ZPKN6YR',
        buttonText: 'Buy Now on Amazon'
    },
    'B08J5F3G18': {
        asin: 'B08J5F3G18',
        title: 'Apple AirPods Pro (2nd Generation)',
        description: 'Active Noise Cancellation reduces unwanted background noise. Adaptive Transparency lets outside sound in while reducing loud environmental noise.',
        image: 'https://m.media-amazon.com/images/I/61f1YfTkTDL._AC_SL1500_.jpg',
        price: 199.99,
        originalPrice: 249.00,
        rating: 4.6,
        reviewCount: '89,432',
        badge: 'Amazon\'s Choice',
        url: 'https://www.amazon.com/dp/B08J5F3G18',
        buttonText: 'Check Price'
    }
};

// GET /api/product?productId=ASIN
router.get('/product', (req, res) => {
    const productId = req.query.productId;
    
    if (!productId) {
        return res.status(400).json({
            error: 'Missing productId parameter'
        });
    }
    
    const product = products[productId];
    
    if (!product) {
        return res.status(404).json({
            error: 'Product not found'
        });
    }
    
    // Enable CORS for WordPress sites
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET');
    
    res.json(product);
});

// Alternative: Integration with Amazon Product Advertising API
// You would need to sign up for Amazon Product Advertising API and get credentials
/*
const amazonPaapi = require('amazon-paapi');

const commonParameters = {
    AccessKey: 'YOUR_ACCESS_KEY',
    SecretKey: 'YOUR_SECRET_KEY',
    PartnerTag: 'YOUR_AFFILIATE_TAG',
    PartnerType: 'Associates',
    Marketplace: 'www.amazon.com'
};

router.get('/product', async (req, res) => {
    const productId = req.query.productId;
    
    try {
        const requestParameters = {
            ItemIds: [productId],
            ItemIdType: 'ASIN',
            Resources: [
                'Images.Primary.Large',
                'ItemInfo.Title',
                'ItemInfo.Features',
                'Offers.Listings.Price',
                'CustomerReviews.StarRating',
                'CustomerReviews.Count'
            ]
        };
        
        const data = await amazonPaapi.GetItems(commonParameters, requestParameters);
        
        if (data && data.ItemsResult && data.ItemsResult.Items) {
            const item = data.ItemsResult.Items[0];
            
            const product = {
                asin: item.ASIN,
                title: item.ItemInfo.Title.DisplayValue,
                description: item.ItemInfo.Features ? item.ItemInfo.Features.DisplayValues.join(' ') : '',
                image: item.Images.Primary.Large.URL,
                price: item.Offers.Listings[0].Price.Amount,
                originalPrice: item.Offers.Listings[0].SavingBasis ? item.Offers.Listings[0].SavingBasis.Amount : null,
                rating: item.CustomerReviews ? item.CustomerReviews.StarRating.Value : null,
                reviewCount: item.CustomerReviews ? item.CustomerReviews.Count : null,
                url: item.DetailPageURL,
                buttonText: 'View on Amazon'
            };
            
            res.header('Access-Control-Allow-Origin', '*');
            res.json(product);
        } else {
            res.status(404).json({ error: 'Product not found' });
        }
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});
*/

module.exports = router;
