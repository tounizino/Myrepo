<?php
/**
 * CLBP Editor's Picks Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class CLBP_Editors_Picks_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct( 'clbp_editors_picks', __( 'CLBP: Editor\'s Picks', 'cloud-layout-builder-pro' ), array( 'description' => __( 'Display featured or pinned posts', 'cloud-layout-builder-pro' ) ) );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];

        $query = new WP_Query( array( 'meta_key' => '_clbp_featured', 'meta_value' => '1', 'posts_per_page' => absint( $instance['count'] ?? 4 ) ) );
        if ( ! $query->have_posts() ) $query = new WP_Query( array( 'posts_per_page' => absint( $instance['count'] ?? 4 ), 'orderby' => 'comment_count' ) );

        if ( $query->have_posts() ) {
            echo '<ul class="clbp-editors-picks">';
            while ( $query->have_posts() ) {
                $query->the_post();
                echo '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a><span class="clbp-pick-date">' . esc_html( get_the_date() ) . '</span></li>';
            }
            echo '</ul>';
            wp_reset_postdata();
        }

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Editor\'s Picks', 'cloud-layout-builder-pro' );
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 4;
        ?>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'cloud-layout-builder-pro' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"></p>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of posts:', 'cloud-layout-builder-pro' ); ?></label>
        <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>" min="1" max="10"></p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        return array( 'title' => sanitize_text_field( $new_instance['title'] ), 'count' => absint( $new_instance['count'] ) );
    }
}
