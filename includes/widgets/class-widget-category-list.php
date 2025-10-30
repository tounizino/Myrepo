<?php
/**
 * Category List Widget
 */

if (!defined('ABSPATH')) {
    exit;
}

class UBCG_Widget_Category_List extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'ubcg_category_list',
            __('UBCG: Category List', 'ubcg'),
            array(
                'description' => __('Display categories with post counts', 'ubcg'),
                'classname' => 'ubcg-widget-category-list'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Categories', 'ubcg');
        $show_count = isset($instance['show_count']) ? (bool) $instance['show_count'] : true;
        $hierarchical = isset($instance['hierarchical']) ? (bool) $instance['hierarchical'] : false;
        
        $cat_args = array(
            'taxonomy' => 'category',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
            'hierarchical' => $hierarchical
        );
        
        $categories = get_terms($cat_args);
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        
        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<ul class="ubcg-widget-category-list">';
            
            foreach ($categories as $category) {
                echo '<li class="ubcg-widget-category-item">';
                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">';
                echo esc_html($category->name);
                
                if ($show_count) {
                    echo ' <span class="ubcg-category-count">(' . $category->count . ')</span>';
                }
                
                echo '</a>';
                echo '</li>';
            }
            
            echo '</ul>';
        } else {
            echo '<p>' . __('No categories found.', 'ubcg') . '</p>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Categories', 'ubcg');
        $show_count = isset($instance['show_count']) ? (bool) $instance['show_count'] : true;
        $hierarchical = isset($instance['hierarchical']) ? (bool) $instance['hierarchical'] : false;
        ?>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'ubcg'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_count); ?> id="<?php echo esc_attr($this->get_field_id('show_count')); ?>" name="<?php echo esc_attr($this->get_field_name('show_count')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>"><?php _e('Show post counts', 'ubcg'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($hierarchical); ?> id="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>" name="<?php echo esc_attr($this->get_field_name('hierarchical')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>"><?php _e('Show hierarchy', 'ubcg'); ?></label>
        </p>
        
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_count'] = isset($new_instance['show_count']);
        $instance['hierarchical'] = isset($new_instance['hierarchical']);
        
        return $instance;
    }
}
