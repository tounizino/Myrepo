<?php
/**
 * Featured Posts Widget
 */

if (!defined('ABSPATH')) {
    exit;
}

class UBCG_Widget_Featured_Posts extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'ubcg_featured_posts',
            __('UBCG: Featured Posts', 'ubcg'),
            array(
                'description' => __('Display featured posts from your cloud gaming blog', 'ubcg'),
                'classname' => 'ubcg-widget-featured-posts'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Featured Posts', 'ubcg');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        $show_excerpt = isset($instance['show_excerpt']) ? (bool) $instance['show_excerpt'] : true;
        
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'meta_key' => '_ubcg_featured',
            'meta_value' => '1'
        );
        
        $query = new WP_Query($query_args);
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        
        if ($query->have_posts()) {
            echo '<div class="ubcg-widget-featured-list">';
            
            while ($query->have_posts()) {
                $query->the_post();
                
                echo '<article class="ubcg-widget-featured-item">';
                
                if (has_post_thumbnail()) {
                    echo '<div class="ubcg-widget-featured-thumbnail">';
                    echo '<a href="' . esc_url(get_permalink()) . '">';
                    the_post_thumbnail('medium', array('loading' => 'lazy'));
                    echo '</a>';
                    echo '</div>';
                }
                
                echo '<div class="ubcg-widget-featured-content">';
                echo '<h4><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></h4>';
                
                if ($show_excerpt) {
                    echo '<p>' . wp_trim_words(get_the_excerpt(), 15) . '</p>';
                }
                
                echo '</div>';
                echo '</article>';
            }
            
            echo '</div>';
        } else {
            echo '<p>' . __('No featured posts found.', 'ubcg') . '</p>';
        }
        
        wp_reset_postdata();
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Featured Posts', 'ubcg');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        $show_excerpt = isset($instance['show_excerpt']) ? (bool) $instance['show_excerpt'] : true;
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
            <input class="checkbox" type="checkbox" <?php checked($show_excerpt); ?> id="<?php echo esc_attr($this->get_field_id('show_excerpt')); ?>" name="<?php echo esc_attr($this->get_field_name('show_excerpt')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_excerpt')); ?>"><?php _e('Display excerpt', 'ubcg'); ?></label>
        </p>
        
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 3;
        $instance['show_excerpt'] = isset($new_instance['show_excerpt']);
        
        return $instance;
    }
}
