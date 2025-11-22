# Social Video Downloader

A lightweight WordPress-ready tool that lets you generate download options for Facebook Reels, YouTube videos, Instagram videos, and TikTok videos by pasting the source URL. Intended for educational and testing purposes only.

## WordPress Plugin Usage

1. Upload `wp-social-video-downloader.php` and the `assets` folder to your WordPress site under `wp-content/plugins/social-video-downloader/`.
2. Activate the **Social Video Downloader** plugin from the WordPress admin panel.
3. Embed the downloader anywhere using the shortcode:

```php
[social_video_downloader]
```

Optionally, render with the dark theme:

```php
[social_video_downloader theme="dark"]
```

## Standalone Embed (Non-WordPress)

If you prefer not to install the plugin, you can embed the following HTML, CSS, and JavaScript snippet inside any WordPress page/post (switch to the HTML editor) or static site:

```html
<div id="svd-embed">
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/gh/yourusername/wp-social-video-downloader/assets/css/style.css"
  />
  <div class="svd-container" data-theme="light">
    <div class="svd-header">
      <h2 class="svd-title">Social Video Downloader</h2>
      <p class="svd-subtitle">Download videos from Facebook, YouTube, Instagram, and TikTok</p>
      <p class="svd-disclaimer">⚠️ For educational and testing purposes only. Please respect copyright laws.</p>
    </div>
    <div class="svd-input-section">
      <div class="svd-platform-icons">
        <span class="svd-icon" title="Facebook Reels">📘</span>
        <span class="svd-icon" title="YouTube">▶️</span>
        <span class="svd-icon" title="Instagram">📷</span>
        <span class="svd-icon" title="TikTok">🎵</span>
      </div>
      <div class="svd-input-wrapper">
        <input
          type="text"
          id="svd-url-input"
          class="svd-input"
          placeholder="Paste your video URL here..."
          aria-label="Video URL"
        />
        <button id="svd-download-btn" class="svd-button svd-button-primary">
          <span class="svd-button-text">Get Download Options</span>
          <span class="svd-button-loader" style="display: none;">⏳</span>
        </button>
      </div>
    </div>
    <div id="svd-result" class="svd-result" style="display:none;">
      <div class="svd-video-info">
        <div id="svd-thumbnail" class="svd-thumbnail"></div>
        <div class="svd-info-text">
          <h3 id="svd-video-title" class="svd-video-title"></h3>
          <p id="svd-video-platform" class="svd-video-platform"></p>
        </div>
      </div>
      <div id="svd-download-options" class="svd-download-options"></div>
    </div>
    <div id="svd-error" class="svd-error" style="display:none;"></div>
    <div class="svd-footer">
      <p class="svd-supported-platforms">
        <strong>Supported Platforms:</strong>
        YouTube (videos, shorts), TikTok, Instagram (reels, posts, IGTV), Facebook (videos, reels)
      </p>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
  const svdAjax = {
    ajaxurl: 'https://your-wordpress-site.com/wp-admin/admin-ajax.php',
    nonce: 'REPLACE_WITH_WP_NONCE'
  };
</script>
<script src="https://cdn.jsdelivr.net/gh/yourusername/wp-social-video-downloader/assets/js/script.js"></script>
```

Replace the CDN links and WordPress AJAX endpoint with your own hosting location if needed.

## Disclaimer

This tool is provided for educational and testing purposes only. Ensure that you have permission to download and store any content, and always follow the terms of service for each platform.
