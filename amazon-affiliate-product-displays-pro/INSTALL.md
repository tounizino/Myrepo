# Installation Guide

## Requirements

Before installing the plugin, ensure your WordPress environment meets these requirements:

- **WordPress:** 5.0 or higher
- **PHP:** 7.4 or higher
- **MySQL:** 5.6 or higher
- **Amazon PA-API 5.0 Credentials:**
  - Access Key
  - Secret Key
  - Associate Tag

---

## Step 1: Download the Plugin

Download the `amazon-affiliate-product-displays-pro` folder.

---

## Step 2: Upload to WordPress

### Method A: Via WordPress Admin

1. Log in to your WordPress admin dashboard.
2. Navigate to **Plugins** > **Add New**.
3. Click **Upload Plugin** at the top of the page.
4. Click **Choose File** and select the plugin ZIP file (if zipped).
5. Click **Install Now**.
6. Once installed, click **Activate Plugin**.

### Method B: Via FTP/SFTP

1. Connect to your WordPress site via FTP or SFTP.
2. Upload the `amazon-affiliate-product-displays-pro` folder to `/wp-content/plugins/`.
3. Go to your WordPress admin dashboard.
4. Navigate to **Plugins**.
5. Find **Amazon Affiliate Product Displays Pro** and click **Activate**.

---

## Step 3: Configure Amazon API Credentials

1. Go to **Settings** > **Amazon Displays** in your WordPress admin.
2. Enter the following credentials:
   - **Access Key**: Your Amazon PA-API 5.0 Access Key
   - **Secret Key**: Your Amazon PA-API 5.0 Secret Key
   - **Associate Tag**: Your Amazon Associates Tracking ID
3. Select your **PA-API Region** (e.g., `us-east-1` for North America).
4. Choose your default **Marketplace** (e.g., `www.amazon.com` for United States).
5. Click **Save Changes**.

---

## Step 4: Customize Styling (Optional)

### Theme Presets
Select from pre-configured themes:
- **Amazon Classic** – Amazon's signature orange and blue
- **Minimal White** – Clean, minimal design
- **Dark Neon** – Dark mode with vibrant accents
- **Glassmorphism** – Modern translucent glass effect

### Color Customization
Use the color pickers to customize:
- Button gradient colors
- Accent colors
- Hover states

### Typography
- **System Font Stack** (default) – Uses native system fonts for fast loading
- **Google Font** – Specify a Google Font family (e.g., `Inter:wght@400;500;700`)

---

## Step 5: Configure Feature Toggles

Enable or disable the following features:
- **Prime Badge** – Show Prime eligibility on products
- **Review Count** – Display star ratings and review count
- **Discount Badge** – Show savings percentage when available
- **Animations** – Enable hover and entrance animations
- **Dark Mode** – Apply dark mode styling

---

## Step 6: Advanced Options

### Cache Duration
Set the cache duration in seconds to reduce API calls:
- Default: `3600` (1 hour)
- Set to `0` to disable caching

### Custom CSS
Add your own CSS code to further personalize the layouts.

---

## Step 7: Use Shortcodes

Once configured, add shortcodes to your posts, pages, or widgets.

### Examples:

**Single Product Card:**
```
[amazon_card asin="B0CX57B5F4"]
```

**Product Grid (3 columns):**
```
[amazon_products asin="B0CX57B5F4,B0F2TB1KNV,B0ABC123XY" layout="grid" columns="3"]
```

**Keyword-based Carousel:**
```
[amazon_products keyword="gaming laptop" layout="carousel" limit="6"]
```

For more examples, see [EXAMPLES.md](EXAMPLES.md).

---

## Troubleshooting

### Plugin doesn't activate
- Check PHP version (must be 7.4+)
- Check WordPress version (must be 5.0+)
- Look for PHP errors in your error log

### No products showing
- Verify your Amazon PA-API credentials are correct
- Check that your Associate Tag matches your region
- Enable WP_DEBUG to see error messages (only for admins)

### API Error Messages
- **"Missing credentials"** – Enter Access Key, Secret Key, and Associate Tag
- **"Invalid signature"** – Check that your Secret Key is correct
- **"Access denied"** – Verify your PA-API account is approved and active

### Styling not applying
- Clear browser cache
- Clear WordPress cache (if using a caching plugin)
- Check the Custom CSS field for syntax errors

---

## Getting Your Amazon PA-API Credentials

### 1. Sign up for Amazon Associates
Visit: https://affiliate-program.amazon.com/

### 2. Register for Product Advertising API
Visit: https://webservices.amazon.com/paapi5/documentation/

### 3. Generate Your API Keys
- Log in to your PA-API account
- Navigate to **Tools** > **Product Advertising API**
- Click **Add a New User** or **Manage Credentials**
- Copy your **Access Key** and **Secret Key**

### 4. Get Your Associate Tag
- Go to your Amazon Associates dashboard
- Your Associate Tag (Tracking ID) is visible in the header or under **Manage Your Tracking IDs**

---

## Support

For help, documentation, or feature requests:
- Email: support@example.com
- Documentation: [Link to docs]
- GitHub Issues: [Link to repo issues]

---

## Uninstalling

To completely remove the plugin:

1. Deactivate the plugin from **Plugins** > **Installed Plugins**.
2. Click **Delete** to remove all plugin files.
3. (Optional) Manually delete the option `aapd_settings` from your database if needed:
   ```sql
   DELETE FROM wp_options WHERE option_name LIKE 'aapd_%';
   ```

---

## License

GPL v2 or later. See [LICENSE](LICENSE) for details.
