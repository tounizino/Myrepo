<?php
/**
 * Speed Test Data Provider
 */

if (!defined('ABSPATH')) {
    exit;
}

class CGST_Speed_Test {
    
    /**
     * Get performance thresholds for cloud gaming analysis
     */
    public static function get_performance_thresholds() {
        $thresholds = array(
            array(
                'label' => __('Excellent', 'cloud-gaming-speedtest'),
                'icon' => '🏆',
                'min_download' => 50,
                'min_upload' => 10,
                'max_latency' => 20,
                'max_jitter' => 5,
                'class' => 'excellent',
                'headline' => __('Perfect for 4K HDR cloud gaming', 'cloud-gaming-speedtest'),
                'recommendations' => array(
                    __('Enable ultra quality, HDR, and ray tracing features', 'cloud-gaming-speedtest'),
                    __('Competitive-ready latency for fast-paced games', 'cloud-gaming-speedtest'),
                    __('Ideal for GeForce NOW Ultimate and Xbox Cloud Gaming 4K tiers', 'cloud-gaming-speedtest'),
                ),
            ),
            array(
                'label' => __('Great', 'cloud-gaming-speedtest'),
                'icon' => '✅',
                'min_download' => 35,
                'min_upload' => 8,
                'max_latency' => 30,
                'max_jitter' => 10,
                'class' => 'good',
                'headline' => __('Ready for 1440p 60fps experiences', 'cloud-gaming-speedtest'),
                'recommendations' => array(
                    __('Enjoy high visual fidelity with enhanced graphics', 'cloud-gaming-speedtest'),
                    __('Suitable for most AAA games and co-op play', 'cloud-gaming-speedtest'),
                    __('Consider enabling adaptive resolution for peak hours', 'cloud-gaming-speedtest'),
                ),
            ),
            array(
                'label' => __('Good', 'cloud-gaming-speedtest'),
                'icon' => '👍',
                'min_download' => 20,
                'min_upload' => 6,
                'max_latency' => 40,
                'max_jitter' => 15,
                'class' => 'fair',
                'headline' => __('Solid 1080p cloud gaming performance', 'cloud-gaming-speedtest'),
                'recommendations' => array(
                    __('Use balanced presets to maintain smooth gameplay', 'cloud-gaming-speedtest'),
                    __('Switch to wired Ethernet for additional stability', 'cloud-gaming-speedtest'),
                    __('Close background downloads while gaming', 'cloud-gaming-speedtest'),
                ),
            ),
            array(
                'label' => __('Limited', 'cloud-gaming-speedtest'),
                'icon' => '⚠️',
                'min_download' => 10,
                'min_upload' => 4,
                'max_latency' => 60,
                'max_jitter' => 20,
                'class' => 'poor',
                'headline' => __('Basic 720p cloud gaming possible', 'cloud-gaming-speedtest'),
                'recommendations' => array(
                    __('Expect input lag and occasional buffering', 'cloud-gaming-speedtest'),
                    __('Lower graphics settings and disable HDR', 'cloud-gaming-speedtest'),
                    __('Upgrade your internet plan if possible', 'cloud-gaming-speedtest'),
                ),
            ),
            array(
                'label' => __('Not Ready', 'cloud-gaming-speedtest'),
                'icon' => '❌',
                'min_download' => 0,
                'min_upload' => 0,
                'max_latency' => 500,
                'max_jitter' => 100,
                'class' => 'bad',
                'headline' => __('Connection does not meet minimum cloud gaming requirements', 'cloud-gaming-speedtest'),
                'recommendations' => array(
                    __('Upgrade to a faster and more stable connection', 'cloud-gaming-speedtest'),
                    __('Check for Wi-Fi interference or switch to wired', 'cloud-gaming-speedtest'),
                    __('Consult your ISP regarding connection issues', 'cloud-gaming-speedtest'),
                ),
            ),
        );
        
        return apply_filters('cgst_default_performance_thresholds', $thresholds);
    }
    
    /**
     * Quality guidelines table
     */
    public static function get_quality_levels() {
        $quality_levels = array(
            array(
                'quality' => __('4K Ultra (60fps)', 'cloud-gaming-speedtest'),
                'download' => __('50+ Mbps', 'cloud-gaming-speedtest'),
                'upload' => __('10+ Mbps', 'cloud-gaming-speedtest'),
                'latency' => __('< 20ms', 'cloud-gaming-speedtest'),
                'notes' => __('Competitive and cinematic gaming, HDR + Ray Tracing', 'cloud-gaming-speedtest'),
            ),
            array(
                'quality' => __('1440p High (60fps)', 'cloud-gaming-speedtest'),
                'download' => __('35+ Mbps', 'cloud-gaming-speedtest'),
                'upload' => __('8+ Mbps', 'cloud-gaming-speedtest'),
                'latency' => __('< 30ms', 'cloud-gaming-speedtest'),
                'notes' => __('High fidelity visuals, smooth action gameplay', 'cloud-gaming-speedtest'),
            ),
            array(
                'quality' => __('1080p Balanced (60fps)', 'cloud-gaming-speedtest'),
                'download' => __('20+ Mbps', 'cloud-gaming-speedtest'),
                'upload' => __('6+ Mbps', 'cloud-gaming-speedtest'),
                'latency' => __('< 40ms', 'cloud-gaming-speedtest'),
                'notes' => __('Standard experience, ideal for story-driven titles', 'cloud-gaming-speedtest'),
            ),
            array(
                'quality' => __('720p Entry (30fps)', 'cloud-gaming-speedtest'),
                'download' => __('10+ Mbps', 'cloud-gaming-speedtest'),
                'upload' => __('4+ Mbps', 'cloud-gaming-speedtest'),
                'latency' => __('< 60ms', 'cloud-gaming-speedtest'),
                'notes' => __('Playable with compromises, suitable for casual sessions', 'cloud-gaming-speedtest'),
            ),
        );
        
        return apply_filters('cgst_default_quality_levels', $quality_levels);
    }
    
    /**
     * Default resource links
     */
    public static function get_default_resources() {
        $resources = array(
            array(
                'title' => __('NVIDIA GeForce NOW Ultimate', 'cloud-gaming-speedtest'),
                'url' => 'https://www.nvidia.com/en-us/geforce-now/',
            ),
            array(
                'title' => __('Xbox Cloud Gaming (xCloud)', 'cloud-gaming-speedtest'),
                'url' => 'https://www.xbox.com/en-US/play',
            ),
            array(
                'title' => __('PlayStation Plus Premium', 'cloud-gaming-speedtest'),
                'url' => 'https://www.playstation.com/en-us/ps-plus/',
            ),
            array(
                'title' => __('Network Optimization Tips', 'cloud-gaming-speedtest'),
                'url' => 'https://support.microsoft.com/en-us/topic/network-speed-latency-and-packet-loss-in-xbox-live-8109314f-1e68-409b-9f06-2f9e9f59464d',
            ),
        );
        
        return apply_filters('cgst_default_resources', $resources);
    }
    
    /**
     * Sanitize resources from admin
     */
    public static function sanitize_resources($items) {
        $clean = array();
        
        if (!is_array($items)) {
            return $clean;
        }
        
        foreach ($items as $item) {
            if (empty($item['title']) && empty($item['url'])) {
                continue;
            }
            
            $clean[] = array(
                'title' => sanitize_text_field($item['title']),
                'url' => esc_url_raw($item['url'])
            );
        }
        
        return $clean;
    }
}
