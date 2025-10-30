<?php
/**
 * CLBP Custom HTML / CTA Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class CLBP_Custom_HTML_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct( 'clbp_custom_html', __( 'CLBP: Custom HTML / CTA', 'cloud-layout-builder-pro' ), array( 'description' => __( 'Add custom HTML, buttons, or CTAs', 'cloud-layout-builder-pro' ) ) );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        echo '<div class="clbp-custom-widget">' . wp_kses_post( $instance['content'] ?? '' ) . '</div>';
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title   = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Custom CTA', 'cloud-layout-builder-pro' );
        $content = ! empty( $instance['content'] ) ? $instance['content'] : '<p>' . __( 'Add your call-to-action content here.', 'cloud-layout-builder-pro' ) . '</p>';
        ?>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'cloud-layout-builder-pro' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"></p>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"><?php esc_html_e( 'Content (HTML allowed):', 'cloud-layout-builder-pro' ); ?></label>
        <textarea class="widefat" rows="6" id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>"><?php echo esc_textarea( $content ); ?></textarea></p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        return array( 'title' => sanitize_text_field( $new_instance['title'] ), 'content' => wp_kses_post( $new_instance['content'] ) );
    }
}
