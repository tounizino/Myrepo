<?php
/**
 * Latest Posts Widget
 */

if (!defined('ABSPATH')) {
    exit;
}

class UBCG_Widget_Latest_Posts extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'ubcg_latest_posts',
            __('UBCG: Latest Posts', 'ubcg'),
            array(
                'description' => __('Display latest posts from your cloud gaming blog', 'ubcg'),
                'classname' => 'ubcg-widget-latest-posts'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Latest Posts', 'ubcg');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : true;
        $show_thumbnail = isset($instance['show_thumbnail']) ? (bool) $instance['show_thumbnail'] : true;
        
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        $query = new WP_Query($query_args);
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        
        if ($query->have_posts()) {
            echo '<ul class="ubcg-widget-posts-list">';
            
            while ($query->have_posts()) {
                $query->the_post();
                
                echo '<li class="ubcg-widget-post-item">';
                
                if ($show_thumbnail && has_post_thumbnail()) {
                    echo '<div class="ubcg-widget-thumbnail">';
                    echo '<a href="' . esc_url(get_permalink()) . '">';
                    the_post_thumbnail('thumbnail', array('loading' => 'lazy'));
                    echo '</a>';
                    echo '</div>';
                }
                
                echo '<div class="ubcg-widget-post-info">';
                echo '<a href="' . esc_url(get_permalink()) . '" class="ubcg-widget-post-title">' . get_the_title() . '</a>';
                
                if ($show_date) {
                    echo '<time class="ubcg-widget-post-date" datetime="' . esc_attr(get_the_date('c')) . '">' . get_the_date() . '</time>';
                }
                
                echo '</div>';
                echo '</li>';
            }
            
            echo '</ul>';
        } else {
            echo '<p>' . __('No posts found.', 'ubcg') . '</p>';
        }
        
        wp_reset_postdata();
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Latest Posts', 'ubcg');
        $number = !empty($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : true;
        $show_thumbnail = isset($instance['show_thumbnail']) ? (bool) $instance['show_thumbnail'] : true;
        ?>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'ubcg'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php _e('Number of posts:', 'ubcg'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_thumbnail); ?> id="<?php echo esc_attr($this->get_field_id('show_thumbnail')); ?>" name="<?php echo esc_attr($this->get_field_name('show_thumbnail')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_thumbnail')); ?>"><?php _e('Display thumbnail', 'ubcg'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> id="<?php echo esc_attr($this->get_field_id('show_date')); ?>" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>"><?php _e('Display date', 'ubcg'); ?></label>
        </p>
        
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;
        $instance['show_date'] = isset($new_instance['show_date']);
        $instance['show_thumbnail'] = isset($new_instance['show_thumbnail']);
        
        return $instance;
    }
}
