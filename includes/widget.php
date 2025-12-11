<?php
/**
 * Cloud Loadout Latency Tester Widget
 * Adds a sidebar widget for the latency tester
 */

if (!defined('ABSPATH')) {
    exit;
}

class CloudLoadoutLatencyWidget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'cloudloadout_latency_widget',
            __('Cloud Loadout Latency Tester', 'cloud-loadout-latency-tester'),
            array(
                'description' => __('Display a compact latency tester in your sidebar', 'cloud-loadout-latency-tester'),
                'classname' => 'cloudloadout-latency-widget'
            )
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        if (!empty($instance['description'])) {
            echo '<p class="widget-description">' . esc_html($instance['description']) . '</p>';
        }
        
        // Get enabled platforms
        $options = get_option('cloudloadout_latency_options', array());
        $enabled_platforms = array();
        if (!empty($options['supported_platforms'])) {
            foreach ($options['supported_platforms'] as $key => $platform) {
                if (!empty($platform['enabled'])) {
                    $enabled_platforms[$key] = $platform['name'];
                }
            }
        }
        
        if (empty($enabled_platforms)) {
            echo '<p>' . __('No platforms are currently enabled. Please contact the administrator.', 'cloud-loadout-latency-tester') . '</p>';
            echo $args['after_widget'];
            return;
        }
        
        // Widget content
        echo '<div class="cloudloadout-widget-content">';
        echo '<form class="cloudloadout-widget-form">';
        
        echo '<div class="form-group">';
        echo '<label for="widget-platform-' . $this->id . '">' . __('Platform:', 'cloud-loadout-latency-tester') . '</label>';
        echo '<select id="widget-platform-' . $this->id . '" class="widget-platform-selector">';
        echo '<option value="">' . __('Select Platform', 'cloud-loadout-latency-tester') . '</option>';
        foreach ($enabled_platforms as $key => $name) {
            echo '<option value="' . esc_attr($key) . '">' . esc_html($name) . '</option>';
        }
        echo '</select>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<button type="submit" class="widget-test-button">' . __('Test Latency', 'cloud-loadout-latency-tester') . '</button>';
        echo '</div>';
        
        echo '<div class="widget-results" style="display: none;"></div>';
        echo '<div class="widget-loading" style="display: none;">' . __('Testing...', 'cloud-loadout-latency-tester') . '</div>';
        
        echo '</form>';
        echo '</div>';
        
        // Add widget-specific styles
        $this->add_widget_styles();
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Latency Test', 'cloud-loadout-latency-tester');
        $description = !empty($instance['description']) ? $instance['description'] : __('Test your connection to cloud gaming platforms', 'cloud-loadout-latency-tester');
        $show_full_form = !empty($instance['show_full_form']) ? $instance['show_full_form'] : false;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'cloud-loadout-latency-tester'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', 'cloud-loadout-latency-tester'); ?></label>
            <textarea class="widefat" rows="3" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo esc_textarea($description); ?></textarea>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_full_form); ?> id="<?php echo $this->get_field_id('show_full_form'); ?>" name="<?php echo $this->get_field_name('show_full_form'); ?>">
            <label for="<?php echo $this->get_field_id('show_full_form'); ?>"><?php _e('Show advanced options', 'cloud-loadout-latency-tester'); ?></label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['description'] = (!empty($new_instance['description'])) ? sanitize_textarea_field($new_instance['description']) : '';
        $instance['show_full_form'] = !empty($new_instance['show_full_form']);
        
        return $instance;
    }
    
    private function add_widget_styles() {
        ?>
        <style>
        .cloudloadout-widget-content {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .cloudloadout-widget-content .form-group {
            margin-bottom: 15px;
        }
        
        .cloudloadout-widget-content .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .cloudloadout-widget-content select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .cloudloadout-widget-content .widget-test-button {
            width: 100%;
            padding: 10px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .cloudloadout-widget-content .widget-test-button:hover {
            background: #5a67d8;
        }
        
        .cloudloadout-widget-content .widget-test-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .cloudloadout-widget-content .widget-results {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #667eea;
        }
        
        .cloudloadout-widget-content .widget-loading {
            margin-top: 10px;
            text-align: center;
            font-style: italic;
            color: #666;
        }
        
        .cloudloadout-widget-content .widget-result-item {
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .cloudloadout-widget-content .widget-result-latency {
            font-weight: 600;
            color: #667eea;
        }
        
        .cloudloadout-widget-content .widget-result-quality {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 3px;
            margin-left: 5px;
        }
        
        .cloudloadout-widget-content .quality-excellent {
            background: #d5f4e6;
            color: #27ae60;
        }
        
        .cloudloadout-widget-content .quality-good {
            background: #fef5e7;
            color: #f39c12;
        }
        
        .cloudloadout-widget-content .quality-fair {
            background: #fdedec;
            color: #e67e22;
        }
        
        .cloudloadout-widget-content .quality-poor {
            background: #fadbd8;
            color: #e74c3c;
        }
        
        .cloudloadout-widget-content .widget-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            $('.cloudloadout-widget-form').on('submit', function(e) {
                e.preventDefault();
                
                var $form = $(this);
                var $platform = $form.find('.widget-platform-selector');
                var $results = $form.find('.widget-results');
                var $loading = $form.find('.widget-loading');
                var $button = $form.find('.widget-test-button');
                
                var platform = $platform.val();
                
                if (!platform) {
                    alert('<?php _e('Please select a platform', 'cloud-loadout-latency-tester'); ?>');
                    return;
                }
                
                // Show loading state
                $loading.show();
                $results.hide();
                $button.prop('disabled', true);
                
                // Perform test
                $.post(cloudloadout_ajax.ajax_url, {
                    action: 'cloudloadout_run_widget_test',
                    nonce: cloudloadout_ajax.nonce,
                    platform: platform
                }, function(response) {
                    $loading.hide();
                    $button.prop('disabled', false);
                    
                    if (response.success) {
                        displayResults(response.data);
                    } else {
                        $results.html('<div class="widget-result-item">' + 
                                     (response.data && response.data.error ? response.data.error : 'Test failed') + 
                                     '</div>').show();
                    }
                }).fail(function() {
                    $loading.hide();
                    $button.prop('disabled', false);
                    $results.html('<div class="widget-result-item"><?php _e('Connection error', 'cloud-loadout-latency-tester'); ?></div>').show();
                });
            });
            
            function displayResults(data) {
                var html = '<div class="widget-result-item">';
                html += '<strong><?php _e('Average Latency:', 'cloud-loadout-latency-tester'); ?></strong> ';
                html += '<span class="widget-result-latency">' + data.average.toFixed(1) + 'ms</span>';
                html += '<span class="widget-result-quality quality-' + data.quality + '">' + data.quality_label + '</span>';
                html += '</div>';
                
                html += '<div class="widget-result-item">';
                html += '<strong><?php _e('Tests:', 'cloud-loadout-latency-tester'); ?></strong> ' + data.tests_count;
                html += '</div>';
                
                if (data.tips && data.tips.length > 0) {
                    html += '<div class="widget-result-item">';
                    html += '<small><?php _e('Tip:', 'cloud-loadout-latency-tester'); ?> ' + data.tips[0] + '</small>';
                    html += '</div>';
                }
                
                $('.widget-results').html(html).show();
            }
        });
        </script>
        <?php
    }
}

// Register the widget
function cloudloadout_register_widget() {
    register_widget('CloudLoadoutLatencyWidget');
}
add_action('widgets_init', 'cloudloadout_register_widget');