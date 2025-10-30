<?php
/**
 * Cloud Layout Builder Pro - Latest Posts Widget
 *
 * @package CloudLayoutBuilderPro\Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CLBP_Latest_Posts_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'clbp_latest_posts',
            __( 'CLBP: Latest Posts', 'cloud-layout-builder-pro' ),
            array( 'description' => __( 'Display a list of your most recent posts', 'cloud-layout-builder-pro' ) )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $query = new WP_Query( array(
            'posts_per_page'      => absint( $instance['count'] ?? 5 ),
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
            'category__in'        => ! empty( $instance['category'] ) ? array( absint( $instance['category'] ) ) : array(),
        ) );

        if ( $query->have_posts() ) {
            echo '<ul class="clbp-widget-posts">';
            while ( $query->have_posts() ) {
                $query->the_post();
                ?>
                <li class="clbp-widget-post">
                    <?php if ( ! empty( $instance['show_thumbnail'] ) && has_post_thumbnail() ) : ?>
                        <div class="clbp-widget-post__thumbnail">
                            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
                        </div>
                    <?php endif; ?>
                    <div class="clbp-widget-post__content">
                        <h4 class="clbp-widget-post__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                        <span class="clbp-widget-post__date"><?php echo get_the_date(); ?></span>
                    </div>
                </li>
                <?php
            }
            echo '</ul>';
            wp_reset_postdata();
        }

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title          = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Latest Posts', 'cloud-layout-builder-pro' );
        $count          = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 5;
        $show_thumbnail = ! empty( $instance['show_thumbnail'] );
        $category       = ! empty( $instance['category'] ) ? absint( $instance['category'] ) : 0;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'cloud-layout-builder-pro' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of posts:', 'cloud-layout-builder-pro' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>" min="1" max="20">
        </p>
        <p>
            <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'show_thumbnail' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumbnail' ) ); ?>" <?php checked( $show_thumbnail ); ?>>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_thumbnail' ) ); ?>"><?php esc_html_e( 'Show thumbnails', 'cloud-layout-builder-pro' ); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Category:', 'cloud-layout-builder-pro' ); ?></label>
            <?php wp_dropdown_categories( array( 'show_option_all' => __( 'All Categories', 'cloud-layout-builder-pro' ), 'selected' => $category, 'name' => $this->get_field_name( 'category' ), 'id' => $this->get_field_id( 'category' ), 'class' => 'widefat' ) ); ?>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']          = sanitize_text_field( $new_instance['title'] );
        $instance['count']          = absint( $new_instance['count'] );
        $instance['show_thumbnail'] = isset( $new_instance['show_thumbnail'] ) ? 1 : 0;
        $instance['category']       = absint( $new_instance['category'] );
        return $instance;
    }
}
