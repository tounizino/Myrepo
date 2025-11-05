<?php
/**
 * Plugin Name: Cloud Gaming Speed Checker Widget
 * Plugin URI: https://github.com/yourusername/cloud-gaming-speed-checker
 * Description: A beautiful, responsive sidebar widget to check internet speed for cloud gaming with dark, light, and sky blue themes.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-gaming-speed-checker
 */

if (!defined('ABSPATH')) {
    exit;
}

function cloud_gaming_speed_checker_load_textdomain() {
    load_plugin_textdomain('cloud-gaming-speed-checker', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'cloud_gaming_speed_checker_load_textdomain');

class Cloud_Gaming_Speed_Checker_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'cloud_gaming_speed_checker',
            __('Cloud Gaming Speed Checker', 'cloud-gaming-speed-checker'),
            array(
                'description' => __('Check internet speed for optimal cloud gaming performance', 'cloud-gaming-speed-checker'),
                'classname' => 'cloud-gaming-speed-checker-widget'
            )
        );

        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts() {
        if (is_active_widget(false, false, $this->id_base)) {
            cloud_gaming_speed_checker_enqueue_assets();
        }
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Speed Test', 'cloud-gaming-speed-checker');
        $theme = !empty($instance['theme']) ? $instance['theme'] : 'dark';

        echo $args['before_widget'];
        
        echo cloud_gaming_speed_checker_render_component(
            $title,
            $theme,
            array('data-render-context' => 'widget')
        );

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Speed Test', 'cloud-gaming-speed-checker');
        $theme = !empty($instance['theme']) ? $instance['theme'] : 'dark';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php _e('Title:', 'cloud-gaming-speed-checker'); ?>
            </label>
            <input 
                class="widefat" 
                id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                type="text" 
                value="<?php echo esc_attr($title); ?>"
            >
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('theme')); ?>">
                <?php _e('Theme:', 'cloud-gaming-speed-checker'); ?>
            </label>
            <select 
                class="widefat" 
                id="<?php echo esc_attr($this->get_field_id('theme')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('theme')); ?>"
            >
                <option value="dark" <?php selected($theme, 'dark'); ?>>Dark</option>
                <option value="light" <?php selected($theme, 'light'); ?>>Light</option>
                <option value="skyblue" <?php selected($theme, 'skyblue'); ?>>Sky Blue</option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['theme'] = (!empty($new_instance['theme'])) ? sanitize_text_field($new_instance['theme']) : 'dark';
        return $instance;
    }
}

function register_cloud_gaming_speed_checker_widget() {
    register_widget('Cloud_Gaming_Speed_Checker_Widget');
}
add_action('widgets_init', 'register_cloud_gaming_speed_checker_widget');

function cloud_gaming_speed_checker_enqueue_assets() {
    wp_enqueue_style(
        'cloud-gaming-speed-checker-css',
        plugins_url('assets/css/speed-checker-widget.css', __FILE__),
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'cloud-gaming-speed-checker-js',
        plugins_url('assets/js/speed-checker-widget.js', __FILE__),
        array(),
        '1.0.0',
        true
    );
}

function cloud_gaming_speed_checker_render_component($title, $theme, $attributes = array()) {
    $allowed_themes = array('dark', 'light', 'skyblue');

    if (!in_array($theme, $allowed_themes, true)) {
        $theme = 'dark';
    }

    $attributes_output = '';

    foreach ($attributes as $attribute => $value) {
        if (null === $value || '' === $value) {
            continue;
        }

        $attributes_output .= sprintf(' %s="%s"', esc_attr($attribute), esc_attr($value));
    }

    ob_start();
    ?>
    <div class="speed-checker-widget theme-<?php echo esc_attr($theme); ?>"<?php echo $attributes_output; ?>>
        <div class="widget-header">
            <h3><?php echo esc_html($title); ?></h3>
            <span class="theme-badge"><?php echo esc_html(ucfirst($theme)); ?></span>
        </div>
        <div class="widget-body">
            <div class="speed-display">
                <div class="speed-circle">
                    <svg class="progress-ring" width="180" height="180" role="presentation" focusable="false">
                        <circle class="progress-ring-bg" cx="90" cy="90" r="80"></circle>
                        <circle class="progress-ring-circle" cx="90" cy="90" r="80"></circle>
                    </svg>
                    <div class="speed-value" aria-live="polite" aria-atomic="true">
                        <span class="speed-number">--</span>
                        <span class="speed-unit"><?php echo esc_html__('Mbps', 'cloud-gaming-speed-checker'); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-icon" aria-hidden="true">⬇️</span>
                    <span class="stat-label"><?php echo esc_html__('Download', 'cloud-gaming-speed-checker'); ?></span>
                    <span class="stat-value">-- <?php echo esc_html__('Mbps', 'cloud-gaming-speed-checker'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-icon" aria-hidden="true">⬆️</span>
                    <span class="stat-label"><?php echo esc_html__('Upload', 'cloud-gaming-speed-checker'); ?></span>
                    <span class="stat-value">-- <?php echo esc_html__('Mbps', 'cloud-gaming-speed-checker'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-icon" aria-hidden="true">⚡</span>
                    <span class="stat-label"><?php echo esc_html__('Ping', 'cloud-gaming-speed-checker'); ?></span>
                    <span class="stat-value">-- <?php echo esc_html__('ms', 'cloud-gaming-speed-checker'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-icon" aria-hidden="true">📊</span>
                    <span class="stat-label"><?php echo esc_html__('Jitter', 'cloud-gaming-speed-checker'); ?></span>
                    <span class="stat-value">-- <?php echo esc_html__('ms', 'cloud-gaming-speed-checker'); ?></span>
                </div>
            </div>

            <button class="test-button" type="button" aria-label="<?php echo esc_attr__('Start internet speed test', 'cloud-gaming-speed-checker'); ?>">
                <span class="button-icon" aria-hidden="true">▶️</span>
                <?php echo esc_html__('Start Speed Test', 'cloud-gaming-speed-checker'); ?>
            </button>

            <div class="recommendation hidden" role="status" aria-live="polite" aria-atomic="true">
                <div class="recommendation-header">
                    <span class="recommendation-icon" aria-hidden="true">✓</span>
                    <h4><?php echo esc_html__('Connection Status', 'cloud-gaming-speed-checker'); ?></h4>
                </div>
                <p class="recommendation-text"></p>
                <div class="gaming-quality">
                    <div class="quality-bar">
                        <div class="quality-fill"></div>
                    </div>
                    <span class="quality-label"></span>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function cloud_gaming_speed_checker_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'theme' => 'dark',
            'title' => 'Speed Test'
        ),
        $atts,
        'cloud_gaming_speed_checker'
    );

    cloud_gaming_speed_checker_enqueue_assets();

    return cloud_gaming_speed_checker_render_component(
        $atts['title'],
        $atts['theme'],
        array('data-render-context' => 'shortcode')
    );
}
add_shortcode('cloud_gaming_speed_checker', 'cloud_gaming_speed_checker_shortcode');
