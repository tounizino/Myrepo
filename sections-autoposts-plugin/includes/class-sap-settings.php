<?php
/**
 * Settings Manager for Sections AutoPosts
 */

if (!defined('ABSPATH')) {
    exit;
}

class SAP_Settings {

    const OPTION_KEY = 'sap_sections_autoposts';

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Cached configuration
     */
    private $sections_config = array();

    /**
     * Private constructor
     */
    private function __construct() {
        $this->sections_config = $this->build_sections_config();
    }

    /**
     * Get singleton instance
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Build configuration for all sections
     */
    private function build_sections_config() {
        return array(
            'featured' => array(
                'key'   => 'featured',
                'label' => __('Featured Posts + Widget', 'sections-autoposts'),
                'description' => __('Two-column hero layout with large feature and performance widget.', 'sections-autoposts'),
                'defaults' => array(
                    'enabled' => true,
                    'order' => 10,
                    'title' => __('Featured Cloud Gaming', 'sections-autoposts'),
                    'subtitle' => __('Hand-picked highlights and essential guides.', 'sections-autoposts'),
                    'secondary_title' => __('Performance', 'sections-autoposts'),
                    'theme' => 'light',
                    'background_color' => 'transparent',
                    'accent_color' => '#0077ff',
                    'secondary_accent_color' => '#ff3366',
                    'container_width' => '1400px',
                    'padding' => '0',
                    'margin' => '0 auto 40px auto',
                    'custom_classes' => '',
                    'data_source' => 'latest',
                    'categories' => array(),
                    'tags' => array(),
                    'custom_post_ids' => '',
                    'order_by' => 'date',
                    'order_direction' => 'DESC',
                    'counts' => array(
                        'main' => 1,
                        'secondary' => 3,
                        'widget' => 5,
                    ),
                    'excerpt_lengths' => array(
                        'main' => 36,
                        'secondary' => 28,
                    ),
                    'widget_badges' => 'auto', // auto|off
                    'badge_new_days' => 7,
                    'badge_updated_days' => 14,
                    'badge_hot_comments' => 5,
                ),
            ),
            'beginners' => array(
                'key'   => 'beginners',
                'label' => __('Beginner\'s Corner', 'sections-autoposts'),
                'description' => __('Step-by-step getting started cards with numbering.', 'sections-autoposts'),
                'defaults' => array(
                    'enabled' => true,
                    'order' => 20,
                    'title' => __('Beginner\'s Corner', 'sections-autoposts'),
                    'subtitle' => __('Guides that help new players master cloud gaming essentials.', 'sections-autoposts'),
                    'secondary_title' => '',
                    'theme' => 'light',
                    'background_color' => '#f9fafb',
                    'accent_color' => '#10b981',
                    'secondary_accent_color' => '',
                    'container_width' => '1400px',
                    'padding' => '40px 20px',
                    'margin' => '0 auto 40px auto',
                    'custom_classes' => '',
                    'data_source' => 'latest',
                    'categories' => array(),
                    'tags' => array(),
                    'custom_post_ids' => '',
                    'order_by' => 'date',
                    'order_direction' => 'DESC',
                    'counts' => array(
                        'items' => 4,
                    ),
                    'excerpt_length' => 26,
                    'step_prefix' => __('Step', 'sections-autoposts'),
                ),
            ),
            'community' => array(
                'key'   => 'community',
                'label' => __('Community Top Picks', 'sections-autoposts'),
                'description' => __('Two-column layout featuring community favorites with sidebar list.', 'sections-autoposts'),
                'defaults' => array(
                    'enabled' => true,
                    'order' => 30,
                    'title' => __('Community Top Picks', 'sections-autoposts'),
                    'subtitle' => __('Trending tutorials and most-loved articles from the community.', 'sections-autoposts'),
                    'secondary_title' => __('Trending Now', 'sections-autoposts'),
                    'theme' => 'light',
                    'background_color' => '#f9fafb',
                    'accent_color' => '#6366f1',
                    'secondary_accent_color' => '#a855f7',
                    'container_width' => '1400px',
                    'padding' => '40px 20px',
                    'margin' => '0 auto 40px auto',
                    'custom_classes' => '',
                    'data_source' => 'popular',
                    'categories' => array(),
                    'tags' => array(),
                    'custom_post_ids' => '',
                    'order_by' => 'comment_count',
                    'order_direction' => 'DESC',
                    'counts' => array(
                        'highlight' => 1,
                        'list' => 4,
                    ),
                    'excerpt_length' => 28,
                    'list_excerpt_length' => 22,
                ),
            ),
            'featured_games' => array(
                'key'   => 'featured_games',
                'label' => __('Featured Games (Hero + Grid)', 'sections-autoposts'),
                'description' => __('Large hero game with supporting grid of four titles.', 'sections-autoposts'),
                'defaults' => array(
                    'enabled' => true,
                    'order' => 40,
                    'title' => __('Featured Games on the Cloud', 'sections-autoposts'),
                    'subtitle' => __('Peak cloud gaming experiences, tested and curated.', 'sections-autoposts'),
                    'secondary_title' => '',
                    'theme' => 'light',
                    'background_color' => '#f9fafb',
                    'accent_color' => '#f97316',
                    'secondary_accent_color' => '',
                    'container_width' => '1400px',
                    'padding' => '40px 20px',
                    'margin' => '0 auto 40px auto',
                    'custom_classes' => '',
                    'data_source' => 'latest',
                    'categories' => array(),
                    'tags' => array(),
                    'custom_post_ids' => '',
                    'order_by' => 'date',
                    'order_direction' => 'DESC',
                    'counts' => array(
                        'hero' => 1,
                        'grid' => 4,
                    ),
                    'excerpt_length' => 30,
                    'show_stats' => true,
                ),
            ),
            'latest_news' => array(
                'key'   => 'latest_news',
                'label' => __('Latest News (Timeline)', 'sections-autoposts'),
                'description' => __('Chronological timeline for announcements and platform updates.', 'sections-autoposts'),
                'defaults' => array(
                    'enabled' => true,
                    'order' => 50,
                    'title' => __('Latest News & Platform Updates', 'sections-autoposts'),
                    'subtitle' => __('Stay in the loop with the newest releases and patches.', 'sections-autoposts'),
                    'secondary_title' => '',
                    'theme' => 'light',
                    'background_color' => '#f9fafb',
                    'accent_color' => '#eab308',
                    'secondary_accent_color' => '',
                    'container_width' => '1400px',
                    'padding' => '40px 20px',
                    'margin' => '0 auto 40px auto',
                    'custom_classes' => '',
                    'data_source' => 'latest',
                    'categories' => array(),
                    'tags' => array(),
                    'custom_post_ids' => '',
                    'order_by' => 'date',
                    'order_direction' => 'DESC',
                    'counts' => array(
                        'items' => 4,
                    ),
                    'excerpt_length' => 26,
                    'timeline_badges' => 'auto',
                ),
            ),
            'latest_posts' => array(
                'key'   => 'latest_posts',
                'label' => __('Latest Posts Grid', 'sections-autoposts'),
                'description' => __('Paginated grid layout for the newest posts.', 'sections-autoposts'),
                'defaults' => array(
                    'enabled' => true,
                    'order' => 60,
                    'title' => __('Latest Posts', 'sections-autoposts'),
                    'subtitle' => __('Auto-updating grid with paging controls.', 'sections-autoposts'),
                    'secondary_title' => '',
                    'theme' => 'light',
                    'background_color' => 'transparent',
                    'accent_color' => '#0077ff',
                    'secondary_accent_color' => '',
                    'container_width' => '1400px',
                    'padding' => '0',
                    'margin' => '0 auto 40px auto',
                    'custom_classes' => '',
                    'data_source' => 'latest',
                    'categories' => array(),
                    'tags' => array(),
                    'custom_post_ids' => '',
                    'order_by' => 'date',
                    'order_direction' => 'DESC',
                    'counts' => array(
                        'items' => 12,
                    ),
                    'excerpt_length' => 26,
                    'items_per_page' => 9,
                    'grid_columns_desktop' => 3,
                    'grid_columns_tablet' => 2,
                    'grid_columns_mobile' => 1,
                    'show_badges' => true,
                ),
            ),
            'platforms' => array(
                'key'   => 'platforms',
                'label' => __('Platform Comparison & Reviews', 'sections-autoposts'),
                'description' => __('Review cards with ratings and feature highlights per platform.', 'sections-autoposts'),
                'defaults' => array(
                    'enabled' => true,
                    'order' => 70,
                    'title' => __('Platform Comparison & Reviews', 'sections-autoposts'),
                    'subtitle' => __('Scorecards for every major cloud platform.', 'sections-autoposts'),
                    'secondary_title' => '',
                    'theme' => 'light',
                    'background_color' => '#f9fafb',
                    'accent_color' => '#0077ff',
                    'secondary_accent_color' => '',
                    'container_width' => '1400px',
                    'padding' => '40px 20px',
                    'margin' => '0 auto 40px auto',
                    'custom_classes' => '',
                    'data_source' => 'category',
                    'categories' => array(),
                    'tags' => array(),
                    'custom_post_ids' => '',
                    'order_by' => 'date',
                    'order_direction' => 'DESC',
                    'counts' => array(
                        'items' => 3,
                    ),
                    'excerpt_length' => 24,
                    'rating_meta_key' => 'sap_rating',
                    'features_meta_key' => 'sap_features',
                ),
            ),
            'pro_guides' => array(
                'key'   => 'pro_guides',
                'label' => __('Pro Guides & Advanced Configs', 'sections-autoposts'),
                'description' => __('Three-column pro-level tutorials with difficulty badges.', 'sections-autoposts'),
                'defaults' => array(
                    'enabled' => true,
                    'order' => 80,
                    'title' => __('Pro Guides & Advanced Configs', 'sections-autoposts'),
                    'subtitle' => __('Expert-level tuning for latency, graphics, and networks.', 'sections-autoposts'),
                    'secondary_title' => '',
                    'theme' => 'light',
                    'background_color' => '#f9fafb',
                    'accent_color' => '#8b5cf6',
                    'secondary_accent_color' => '',
                    'container_width' => '1400px',
                    'padding' => '40px 20px',
                    'margin' => '0 auto 40px auto',
                    'custom_classes' => '',
                    'data_source' => 'updated',
                    'categories' => array(),
                    'tags' => array(),
                    'custom_post_ids' => '',
                    'order_by' => 'modified',
                    'order_direction' => 'DESC',
                    'counts' => array(
                        'items' => 3,
                    ),
                    'excerpt_length' => 24,
                    'difficulty_meta_key' => 'sap_difficulty',
                    'default_difficulty' => 'PRO',
                ),
            ),
            'troubleshooting' => array(
                'key'   => 'troubleshooting',
                'label' => __('Troubleshooting Hub', 'sections-autoposts'),
                'description' => __('Icon-based solutions grid for common issues.', 'sections-autoposts'),
                'defaults' => array(
                    'enabled' => true,
                    'order' => 90,
                    'title' => __('Troubleshooting Hub', 'sections-autoposts'),
                    'subtitle' => __('Quick fixes for lag, disconnections, controllers, and more.', 'sections-autoposts'),
                    'secondary_title' => '',
                    'theme' => 'light',
                    'background_color' => '#f9fafb',
                    'accent_color' => '#ef4444',
                    'secondary_accent_color' => '',
                    'container_width' => '1400px',
                    'padding' => '40px 20px',
                    'margin' => '0 auto 40px auto',
                    'custom_classes' => '',
                    'data_source' => 'tag',
                    'categories' => array(),
                    'tags' => array(),
                    'custom_post_ids' => '',
                    'order_by' => 'date',
                    'order_direction' => 'DESC',
                    'counts' => array(
                        'items' => 6,
                    ),
                    'excerpt_length' => 22,
                    'icon_list' => array('⚠️','🔌','🎮','🖥️','🔊','📱'),
                ),
            ),
        );
    }

    /**
     * Get configuration array
     */
    public function get_sections_config() {
        return $this->sections_config;
    }

    /**
     * Get defaults for all sections
     */
    public function get_defaults() {
        $defaults = array();
        foreach ($this->sections_config as $key => $section) {
            $defaults[$key] = $section['defaults'];
        }
        return $defaults;
    }

    /**
     * Retrieve saved settings merged with defaults
     */
    public function get_settings() {
        $saved = get_option(self::OPTION_KEY, array());
        return wp_parse_args($saved, $this->get_defaults());
    }

    /**
     * Get specific section settings
     */
    public function get_section($section_key) {
        $settings = $this->get_settings();
        $defaults = $this->get_defaults();

        if (isset($settings[$section_key])) {
            return wp_parse_args($settings[$section_key], isset($defaults[$section_key]) ? $defaults[$section_key] : array());
        }

        return isset($defaults[$section_key]) ? $defaults[$section_key] : array();
    }

    /**
     * Sanitize settings before saving
     */
    public function sanitize($input) {
        if (!is_array($input)) {
            return $this->get_settings();
        }

        $current = $this->get_settings();

        // Optional helper when saving individual section forms
        $active_section = isset($input['section_key']) ? sanitize_key($input['section_key']) : null;
        if ($active_section) {
            unset($input['section_key']);
        }

        foreach ($this->sections_config as $section_key => $config) {
            if ($active_section && $active_section !== $section_key) {
                continue; // no updates for other sections
            }

            if (!isset($input[$section_key])) {
                continue;
            }

            $current_section = isset($current[$section_key]) ? $current[$section_key] : $config['defaults'];
            $current[$section_key] = $this->sanitize_section($section_key, $input[$section_key], $current_section, $config['defaults']);
        }

        return $current;
    }

    /**
     * Sanitize a single section
     */
    private function sanitize_section($section_key, $section_input, $current_section, $defaults) {
        $output = wp_parse_args($current_section, $defaults);

        $output['enabled'] = isset($section_input['enabled']) ? (bool) $section_input['enabled'] : false;
        $output['order'] = isset($section_input['order']) ? intval($section_input['order']) : $output['order'];
        $output['title'] = isset($section_input['title']) ? sanitize_text_field($section_input['title']) : $output['title'];
        $output['subtitle'] = isset($section_input['subtitle']) ? sanitize_text_field($section_input['subtitle']) : '';
        $output['secondary_title'] = isset($section_input['secondary_title']) ? sanitize_text_field($section_input['secondary_title']) : $output['secondary_title'];

        if (isset($section_input['theme']) && in_array($section_input['theme'], array('light','dark'), true)) {
            $output['theme'] = $section_input['theme'];
        }

        if (isset($section_input['background_color'])) {
            $output['background_color'] = $this->sanitize_color_value($section_input['background_color'], $defaults['background_color']);
        }
        if (isset($section_input['accent_color'])) {
            $output['accent_color'] = $this->sanitize_color_value($section_input['accent_color'], $defaults['accent_color']);
        }
        if (isset($section_input['secondary_accent_color'])) {
            $output['secondary_accent_color'] = $this->sanitize_color_value($section_input['secondary_accent_color'], $defaults['secondary_accent_color']);
        }

        if (isset($section_input['container_width'])) {
            $output['container_width'] = $this->sanitize_css_dimension($section_input['container_width'], $defaults['container_width']);
        }
        if (isset($section_input['padding'])) {
            $output['padding'] = $this->sanitize_css_box_value($section_input['padding'], $defaults['padding']);
        }
        if (isset($section_input['margin'])) {
            $output['margin'] = $this->sanitize_css_box_value($section_input['margin'], $defaults['margin']);
        }
        if (isset($section_input['custom_classes'])) {
            $output['custom_classes'] = $this->sanitize_class_list($section_input['custom_classes']);
        }

        if (isset($section_input['data_source']) && in_array($section_input['data_source'], array('latest','updated','category','tag','custom','popular'), true)) {
            $output['data_source'] = $section_input['data_source'];
        }

        $output['order_by'] = isset($section_input['order_by']) ? sanitize_key($section_input['order_by']) : $output['order_by'];
        $order_direction = isset($section_input['order_direction']) ? strtoupper($section_input['order_direction']) : $output['order_direction'];
        $output['order_direction'] = in_array($order_direction, array('ASC','DESC'), true) ? $order_direction : $output['order_direction'];

        $output['categories'] = isset($section_input['categories']) ? array_map('intval', (array) $section_input['categories']) : $output['categories'];
        $output['tags'] = isset($section_input['tags']) ? array_map('intval', (array) $section_input['tags']) : $output['tags'];

        if (isset($section_input['custom_post_ids'])) {
            $output['custom_post_ids'] = implode(',', $this->parse_id_list($section_input['custom_post_ids']));
        }

        // Counts
        if (isset($section_input['counts']) && is_array($section_input['counts'])) {
            foreach ($section_input['counts'] as $key => $value) {
                $output['counts'][$key] = max(0, intval($value));
            }
        }

        if (isset($section_input['excerpt_length'])) {
            $output['excerpt_length'] = max(10, intval($section_input['excerpt_length']));
        }
        if (isset($section_input['excerpt_lengths']) && is_array($section_input['excerpt_lengths'])) {
            foreach ($section_input['excerpt_lengths'] as $key => $length) {
                $output['excerpt_lengths'][$key] = max(10, intval($length));
            }
        }

        if (isset($section_input['items_per_page'])) {
            $output['items_per_page'] = max(1, intval($section_input['items_per_page']));
        }
        if (isset($section_input['grid_columns_desktop'])) {
            $output['grid_columns_desktop'] = max(1, intval($section_input['grid_columns_desktop']));
        }
        if (isset($section_input['grid_columns_tablet'])) {
            $output['grid_columns_tablet'] = max(1, intval($section_input['grid_columns_tablet']));
        }
        if (isset($section_input['grid_columns_mobile'])) {
            $output['grid_columns_mobile'] = max(1, intval($section_input['grid_columns_mobile']));
        }

        if (isset($section_input['widget_badges'])) {
            $output['widget_badges'] = in_array($section_input['widget_badges'], array('auto','off'), true) ? $section_input['widget_badges'] : 'auto';
        }
        if (isset($section_input['badge_new_days'])) {
            $output['badge_new_days'] = max(0, intval($section_input['badge_new_days']));
        }
        if (isset($section_input['badge_updated_days'])) {
            $output['badge_updated_days'] = max(0, intval($section_input['badge_updated_days']));
        }
        if (isset($section_input['badge_hot_comments'])) {
            $output['badge_hot_comments'] = max(0, intval($section_input['badge_hot_comments']));
        }

        if (isset($section_input['rating_meta_key'])) {
            $output['rating_meta_key'] = sanitize_key($section_input['rating_meta_key']);
        }
        if (isset($section_input['features_meta_key'])) {
            $output['features_meta_key'] = sanitize_key($section_input['features_meta_key']);
        }
        if (isset($section_input['difficulty_meta_key'])) {
            $output['difficulty_meta_key'] = sanitize_key($section_input['difficulty_meta_key']);
        }
        if (isset($section_input['default_difficulty'])) {
            $output['default_difficulty'] = sanitize_text_field($section_input['default_difficulty']);
        }
        if (isset($section_input['icon_list'])) {
            $icons = array_filter(array_map('trim', explode(',', wp_strip_all_tags($section_input['icon_list']))));
            $output['icon_list'] = !empty($icons) ? $icons : $defaults['icon_list'];
        }

        return $output;
    }

    /**
     * Sanitize color value (hex or rgba)
     */
    private function sanitize_color_value($value, $fallback) {
        $value = trim($value);
        if ('' === $value) {
            return $fallback;
        }

        $hex = sanitize_hex_color($value);
        if ($hex) {
            return $hex;
        }

        // Allow rgba()/hsla() etc minimal validation
        if (preg_match('/^(rgba?|hsla?)\(([^\)]+)\)$/', $value)) {
            return $value;
        }

        return $fallback;
    }

    /**
     * Sanitize CSS dimensions (px, %, rem, vw)
     */
    private function sanitize_css_dimension($value, $fallback) {
        $value = trim($value);
        if ($value === '') {
            return $fallback;
        }

        if (preg_match('/^(\d+(\.\d+)?)(px|rem|em|vw|vh|%)?$/', $value, $matches)) {
            $unit = isset($matches[3]) ? $matches[3] : 'px';
            return $matches[1] . $unit;
        }

        if (in_array($value, array('auto','100%','full','inherit'), true)) {
            return $value;
        }

        return $fallback;
    }

    /**
     * Sanitize padding/margin values
     */
    private function sanitize_css_box_value($value, $fallback) {
        $value = trim($value);
        if ('' === $value) {
            return $fallback;
        }

        $parts = preg_split('/\s+/', $value);
        $sanitized = array();

        foreach ($parts as $part) {
            if ($part === 'auto') {
                $sanitized[] = 'auto';
                continue;
            }
            if (preg_match('/^(\d+(\.\d+)?)(px|rem|em|%)?$/', $part, $matches)) {
                $unit = isset($matches[3]) ? $matches[3] : 'px';
                $sanitized[] = $matches[1] . $unit;
            }
        }

        if (!empty($sanitized)) {
            return implode(' ', $sanitized);
        }

        return $fallback;
    }

    /**
     * Sanitize class list
     */
    private function sanitize_class_list($value) {
        $classes = preg_split('/\s+/', trim($value));
        $sanitized = array();

        foreach ($classes as $class) {
            $class = sanitize_html_class($class);
            if (!empty($class)) {
                $sanitized[] = $class;
            }
        }

        return implode(' ', array_unique($sanitized));
    }

    /**
     * Parse comma separated IDs
     */
    public function parse_id_list($value) {
        if (is_array($value)) {
            $ids = array_map('intval', $value);
        } else {
            $ids = array_map('intval', preg_split('/\s*,\s*/', trim($value))); 
        }
        return array_values(array_filter($ids));
    }
}
