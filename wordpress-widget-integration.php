<?php
/**
 * Cloud Gaming Sidebar Widget for WordPress
 * 
 * Add this file to your WordPress theme directory and include it in your functions.php:
 * require_once get_template_directory() . '/wordpress-widget-integration.php';
 */

class Cloud_Gaming_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'cloud_gaming_widget',
            __('Cloud Gaming Tools', 'text_domain'),
            array('description' => __('Display cloud gaming tools for visitors', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        $theme = isset($instance['theme']) ? $instance['theme'] : 'dark';
        
        echo $args['before_widget'];
        
        ?>
        <style>
            <?php include get_template_directory() . '/cloud-gaming-widget-styles.css'; ?>
        </style>

        <div id="cloud-gaming-widget" class="cloud-gaming-widget theme-<?php echo esc_attr($theme); ?>">
            <div class="widget-header">
                <div class="header-icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <div>
                    <h3 class="widget-title">Cloud Gaming Tools</h3>
                    <p class="widget-subtitle">Essential tools for gamers</p>
                </div>
            </div>
            
            <div class="widget-body">
                <ul class="tools-list">
                    <li class="tool-item" data-tool="latency">
                        <a href="<?php echo esc_url($instance['link_latency'] ?? '#latency-tester'); ?>" class="tool-link">
                            <div class="tool-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div class="tool-content">
                                <h4 class="tool-title">Cloud Platforms Latency Tester</h4>
                                <p class="tool-description">Test your connection speed</p>
                            </div>
                            <div class="tool-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </li>

                    <li class="tool-item" data-tool="network">
                        <a href="<?php echo esc_url($instance['link_network'] ?? '#network-monitor'); ?>" class="tool-link">
                            <div class="tool-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="tool-content">
                                <h4 class="tool-title">Network Performance Monitor</h4>
                                <p class="tool-description">Monitor your network stats</p>
                            </div>
                            <div class="tool-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </li>

                    <li class="tool-item" data-tool="launchers">
                        <a href="<?php echo esc_url($instance['link_launchers'] ?? '#gaming-launchers'); ?>" class="tool-link">
                            <div class="tool-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <div class="tool-content">
                                <h4 class="tool-title">Cloud Gaming Launchers</h4>
                                <p class="tool-description">Access popular platforms</p>
                            </div>
                            <div class="tool-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </li>

                    <li class="tool-item" data-tool="gamepad">
                        <a href="<?php echo esc_url($instance['link_gamepad'] ?? '#gamepad-tester'); ?>" class="tool-link">
                            <div class="tool-icon">
                                <i class="fas fa-gamepad"></i>
                            </div>
                            <div class="tool-content">
                                <h4 class="tool-title">Online Gamepad Tester</h4>
                                <p class="tool-description">Test your controller</p>
                            </div>
                            <div class="tool-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </li>

                    <li class="tool-item" data-tool="nat">
                        <a href="<?php echo esc_url($instance['link_nat'] ?? '#nat-checker'); ?>" class="tool-link">
                            <div class="tool-icon">
                                <i class="fas fa-network-wired"></i>
                            </div>
                            <div class="tool-content">
                                <h4 class="tool-title">NAT, IP & Port Checker</h4>
                                <p class="tool-description">Check your connection type</p>
                            </div>
                            <div class="tool-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="widget-footer">
                <div class="footer-stats">
                    <div class="stat-item">
                        <i class="fas fa-users"></i>
                        <span>1.2M+ Users</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-star"></i>
                        <span>4.8/5 Rating</span>
                    </div>
                </div>
            </div>
        </div>

        <script>
            <?php include get_template_directory() . '/cloud-gaming-widget-script.js'; ?>
        </script>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance) {
        $theme = isset($instance['theme']) ? $instance['theme'] : 'dark';
        $link_latency = isset($instance['link_latency']) ? $instance['link_latency'] : '';
        $link_network = isset($instance['link_network']) ? $instance['link_network'] : '';
        $link_launchers = isset($instance['link_launchers']) ? $instance['link_launchers'] : '';
        $link_gamepad = isset($instance['link_gamepad']) ? $instance['link_gamepad'] : '';
        $link_nat = isset($instance['link_nat']) ? $instance['link_nat'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('theme')); ?>"><?php _e('Theme:', 'text_domain'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('theme')); ?>" name="<?php echo esc_attr($this->get_field_name('theme')); ?>">
                <option value="dark" <?php selected($theme, 'dark'); ?>><?php _e('Dark Theme', 'text_domain'); ?></option>
                <option value="light" <?php selected($theme, 'light'); ?>><?php _e('Light Theme', 'text_domain'); ?></option>
                <option value="blue" <?php selected($theme, 'blue'); ?>><?php _e('Sky Blue Theme', 'text_domain'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('link_latency')); ?>"><?php _e('Latency Tester URL:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('link_latency')); ?>" name="<?php echo esc_attr($this->get_field_name('link_latency')); ?>" type="text" value="<?php echo esc_attr($link_latency); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('link_network')); ?>"><?php _e('Network Monitor URL:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('link_network')); ?>" name="<?php echo esc_attr($this->get_field_name('link_network')); ?>" type="text" value="<?php echo esc_attr($link_network); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('link_launchers')); ?>"><?php _e('Gaming Launchers URL:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('link_launchers')); ?>" name="<?php echo esc_attr($this->get_field_name('link_launchers')); ?>" type="text" value="<?php echo esc_attr($link_launchers); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('link_gamepad')); ?>"><?php _e('Gamepad Tester URL:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('link_gamepad')); ?>" name="<?php echo esc_attr($this->get_field_name('link_gamepad')); ?>" type="text" value="<?php echo esc_attr($link_gamepad); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('link_nat')); ?>"><?php _e('NAT Checker URL:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('link_nat')); ?>" name="<?php echo esc_attr($this->get_field_name('link_nat')); ?>" type="text" value="<?php echo esc_attr($link_nat); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['theme'] = (!empty($new_instance['theme'])) ? sanitize_text_field($new_instance['theme']) : 'dark';
        $instance['link_latency'] = (!empty($new_instance['link_latency'])) ? esc_url_raw($new_instance['link_latency']) : '';
        $instance['link_network'] = (!empty($new_instance['link_network'])) ? esc_url_raw($new_instance['link_network']) : '';
        $instance['link_launchers'] = (!empty($new_instance['link_launchers'])) ? esc_url_raw($new_instance['link_launchers']) : '';
        $instance['link_gamepad'] = (!empty($new_instance['link_gamepad'])) ? esc_url_raw($new_instance['link_gamepad']) : '';
        $instance['link_nat'] = (!empty($new_instance['link_nat'])) ? esc_url_raw($new_instance['link_nat']) : '';
        return $instance;
    }
}

function register_cloud_gaming_widget() {
    register_widget('Cloud_Gaming_Widget');
}
add_action('widgets_init', 'register_cloud_gaming_widget');

function enqueue_cloud_gaming_widget_scripts() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_cloud_gaming_widget_scripts');
