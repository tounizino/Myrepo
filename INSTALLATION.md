# Installation Guide

This guide provides multiple ways to integrate the Social Video Downloader into your WordPress site.

## Method 1: WordPress Plugin Installation (Recommended)

### Step 1: Download and Upload
1. Download all project files from this repository
2. Create a folder named `social-video-downloader` in your WordPress installation at:
   ```
   /wp-content/plugins/social-video-downloader/
   ```
3. Upload the following files to this directory:
   - `wp-social-video-downloader.php`
   - `assets/` (entire folder with css and js subdirectories)

### Step 2: Activate Plugin
1. Log in to your WordPress admin panel
2. Go to **Plugins** → **Installed Plugins**
3. Find "Social Video Downloader" in the list
4. Click **Activate**

### Step 3: Use the Shortcode
Insert the following shortcode anywhere in your posts, pages, or widgets:

```
[social_video_downloader]
```

**Dark Theme Option:**
```
[social_video_downloader theme="dark"]
```

---

## Method 2: Direct HTML Embed (No Plugin Required)

### For WordPress Posts/Pages:

1. Edit your post or page in WordPress
2. Switch to the **Text** or **HTML** editor (not Visual)
3. Open `embed-standalone.html` from this repository
4. Copy the entire HTML content
5. Paste it into your post/page
6. Save and preview

### For Custom Theme Templates:

Add the following code to your theme template file (e.g., `page-custom.php`):

```php
<?php
// Include the standalone HTML file
include(get_template_directory() . '/path-to/embed-standalone.html');
?>
```

---

## Method 3: Custom Widget or Sidebar

### Using Plugin Method:

1. Go to **Appearance** → **Widgets** in WordPress admin
2. Add a **Shortcode** widget to your desired sidebar
3. Enter the shortcode:
   ```
   [social_video_downloader]
   ```

### Using HTML Method:

1. Add a **Custom HTML** widget to your sidebar
2. Paste the content from `embed-standalone.html`

---

## File Structure

After installation, your directory structure should look like this:

```
wp-content/plugins/social-video-downloader/
├── wp-social-video-downloader.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── README.md
├── INSTALLATION.md
└── embed-standalone.html
```

---

## Troubleshooting

### Plugin Not Showing Up
- Make sure all files are in the correct directory
- Check file permissions (should be 644 for files, 755 for directories)
- Try deactivating and reactivating the plugin

### Styling Issues
- Clear your WordPress cache
- Clear your browser cache
- Check if your theme has conflicting CSS styles
- Try adding `!important` to critical CSS rules if needed

### Downloads Not Working
- Ensure the URLs you're using are from supported platforms:
  - YouTube (youtube.com, youtu.be)
  - TikTok (tiktok.com)
  - Instagram (instagram.com)
  - Facebook (facebook.com, fb.watch)
- Check if the video is public and not restricted

### JavaScript Not Loading
- Ensure jQuery is loaded (it's usually included by default in WordPress)
- Check browser console for errors (F12 key)
- Verify that `assets/js/script.js` is accessible

---

## Supported Platforms

- **YouTube**: Videos, Shorts, Live streams
- **TikTok**: All public TikTok videos
- **Instagram**: Reels, Posts, IGTV, Stories (if public)
- **Facebook**: Videos, Reels, Watch content

---

## Legal Notice

This tool is provided for **educational and testing purposes only**. Users are responsible for ensuring they have the right to download and use any content. Always respect:

- Copyright laws
- Platform Terms of Service
- Content creators' rights
- Privacy considerations

---

## Support

For issues, questions, or contributions, please visit the GitHub repository or contact the maintainer.
