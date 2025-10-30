<?php
/**
 * CLBP Tag Cloud Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class CLBP_Tag_Cloud_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct( 'clbp_tag_cloud', __( 'CLBP: Tag Cloud', 'cloud-layout-builder-pro' ), array( 'description' => __( 'Styled tag cloud for topics and keywords', 'cloud-layout-builder-pro' ) ) );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];

        $tags = get_tags( array( 'orderby' => 'count', 'order' => 'DESC', 'number' => absint( $instance['count'] ?? 15 ) ) );

        if ( empty( $tags ) ) {
            echo $args['after_widget'];
            return;
        }

        echo '<div class="clbp-tag-cloud">';
        foreach ( $tags as $tag ) {
            echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" class="clbp-tag-item" data-count="' . esc_attr( $tag->count ) . '">' . esc_html( $tag->name ) . '</a>';
        }
        echo '</div>';

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Popular Topics', 'cloud-layout-builder-pro' );
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 15;
        ?>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'cloud-layout-builder-pro' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"></p>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of tags:', 'cloud-layout-builder-pro' ); ?></label>
        <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>" min="5" max="50"></p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        return array( 'title' => sanitize_text_field( $new_instance['title'] ), 'count' => absint( $new_instance['count'] ) );
    }
}
