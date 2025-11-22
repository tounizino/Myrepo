<?php
/*
Plugin Name: Social Video Downloader
Plugin URI: https://github.com/yourusername/wp-social-video-downloader
Description: Download videos from Facebook Reels, YouTube, Instagram, and TikTok. For educational and testing purposes only.
Version: 1.0.0
Author: Your Name
Author URI: https://yourwebsite.com
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: social-video-downloader
*/

if (!defined('ABSPATH')) {
    exit;
}

class SocialVideoDownloader {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('social_video_downloader', array($this, 'render_downloader'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_download_social_video', array($this, 'handle_download_request'));
        add_action('wp_ajax_nopriv_download_social_video', array($this, 'handle_download_request'));
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('social-video-downloader-css', plugins_url('assets/css/style.css', __FILE__), array(), '1.0.0');
        wp_enqueue_script('social-video-downloader-js', plugins_url('assets/js/script.js', __FILE__), array('jquery'), '1.0.0', true);
        
        wp_localize_script('social-video-downloader-js', 'svdAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('svd_nonce')
        ));
    }
    
    public function render_downloader($atts) {
        $atts = shortcode_atts(array(
            'theme' => 'light',
        ), $atts);
        
        ob_start();
        ?>
        <div class="svd-container" data-theme="<?php echo esc_attr($atts['theme']); ?>">
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
            
            <div id="svd-result" class="svd-result" style="display: none;">
                <div class="svd-video-info">
                    <div id="svd-thumbnail" class="svd-thumbnail"></div>
                    <div class="svd-info-text">
                        <h3 id="svd-video-title" class="svd-video-title"></h3>
                        <p id="svd-video-platform" class="svd-video-platform"></p>
                    </div>
                </div>
                
                <div id="svd-download-options" class="svd-download-options"></div>
            </div>
            
            <div id="svd-error" class="svd-error" style="display: none;"></div>
            
            <div class="svd-footer">
                <p class="svd-supported-platforms">
                    <strong>Supported Platforms:</strong> 
                    YouTube (videos, shorts), TikTok, Instagram (reels, posts, IGTV), Facebook (videos, reels)
                </p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function handle_download_request() {
        check_ajax_referer('svd_nonce', 'nonce');
        
        if (!isset($_POST['url'])) {
            wp_send_json_error(array('message' => 'URL is required'));
        }
        
        $url = sanitize_url($_POST['url']);
        
        $platform = $this->detect_platform($url);
        
        if (!$platform) {
            wp_send_json_error(array('message' => 'Unsupported platform or invalid URL'));
        }
        
        $video_data = $this->fetch_video_data($url, $platform);
        
        if ($video_data) {
            wp_send_json_success($video_data);
        } else {
            wp_send_json_error(array('message' => 'Unable to fetch video data. Please check the URL and try again.'));
        }
    }
    
    private function detect_platform($url) {
        if (preg_match('/(?:youtube\.com|youtu\.be)/i', $url)) {
            return 'youtube';
        } elseif (preg_match('/(?:tiktok\.com)/i', $url)) {
            return 'tiktok';
        } elseif (preg_match('/(?:instagram\.com)/i', $url)) {
            return 'instagram';
        } elseif (preg_match('/(?:facebook\.com|fb\.watch)/i', $url)) {
            return 'facebook';
        }
        return false;
    }
    
    private function fetch_video_data($url, $platform) {
        $api_services = array(
            'https://api.allorigins.win/raw?url=' . urlencode($url),
        );
        
        return array(
            'platform' => $platform,
            'title' => 'Video from ' . ucfirst($platform),
            'thumbnail' => $this->get_default_thumbnail($platform),
            'download_options' => $this->get_download_apis($url, $platform)
        );
    }
    
    private function get_default_thumbnail($platform) {
        $thumbnails = array(
            'youtube' => 'https://via.placeholder.com/320x180/FF0000/FFFFFF?text=YouTube',
            'tiktok' => 'https://via.placeholder.com/320x180/000000/FFFFFF?text=TikTok',
            'instagram' => 'https://via.placeholder.com/320x180/E4405F/FFFFFF?text=Instagram',
            'facebook' => 'https://via.placeholder.com/320x180/1877F2/FFFFFF?text=Facebook',
        );
        return isset($thumbnails[$platform]) ? $thumbnails[$platform] : '';
    }
    
    private function get_download_apis($url, $platform) {
        $encoded_url = urlencode($url);
        
        $apis = array(
            array(
                'name' => 'SaveFrom.net',
                'url' => 'https://savefrom.net/#url=' . $encoded_url,
                'quality' => 'Multiple'
            ),
            array(
                'name' => 'Y2Mate',
                'url' => 'https://y2mate.com/#' . $encoded_url,
                'quality' => 'HD/SD'
            ),
            array(
                'name' => 'SnapTik (TikTok)',
                'url' => 'https://snaptik.app/',
                'quality' => 'HD'
            ),
            array(
                'name' => 'SSSTik (TikTok)',
                'url' => 'https://ssstik.io/',
                'quality' => 'HD'
            ),
            array(
                'name' => 'DownloadGram (Instagram)',
                'url' => 'https://downloadgram.org/',
                'quality' => 'Original'
            ),
            array(
                'name' => 'GetInsta',
                'url' => 'https://getinsta.app/',
                'quality' => 'HD'
            ),
            array(
                'name' => 'FBDown (Facebook)',
                'url' => 'https://fbdown.net/',
                'quality' => 'HD/SD'
            ),
            array(
                'name' => '9xBuddy',
                'url' => 'https://9xbuddy.org/process?url=' . $encoded_url,
                'quality' => 'Multiple'
            )
        );
        
        $filtered_apis = array();
        foreach ($apis as $api) {
            if (
                ($platform === 'tiktok' && (strpos($api['name'], 'TikTok') !== false || strpos($api['name'], 'SaveFrom') !== false)) ||
                ($platform === 'instagram' && (strpos($api['name'], 'Instagram') !== false || strpos($api['name'], 'Insta') !== false || strpos($api['name'], 'SaveFrom') !== false)) ||
                ($platform === 'facebook' && (strpos($api['name'], 'Facebook') !== false || strpos($api['name'], 'FB') !== false || strpos($api['name'], 'SaveFrom') !== false)) ||
                ($platform === 'youtube' && (strpos($api['name'], 'Y2Mate') !== false || strpos($api['name'], 'SaveFrom') !== false || strpos($api['name'], '9xBuddy') !== false))
            ) {
                $filtered_apis[] = $api;
            }
        }
        
        return count($filtered_apis) > 0 ? $filtered_apis : $apis;
    }
}

SocialVideoDownloader::get_instance();
