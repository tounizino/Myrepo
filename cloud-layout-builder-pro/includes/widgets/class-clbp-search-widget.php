<?php
/**
 * CLBP Mini Search Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class CLBP_Search_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct( 'clbp_search', __( 'CLBP: Mini Search', 'cloud-layout-builder-pro' ), array( 'description' => __( 'Compact search bar with modern styling', 'cloud-layout-builder-pro' ) ) );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        ?>
        <form role="search" method="get" class="clbp-mini-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label class="screen-reader-text" for="<?php echo esc_attr( $this->get_field_id( 'search' ) ); ?>"><?php esc_html_e( 'Search for:', 'cloud-layout-builder-pro' ); ?></label>
            <input type="search" id="<?php echo esc_attr( $this->get_field_id( 'search' ) ); ?>" class="clbp-mini-search__input" placeholder="<?php echo esc_attr( $instance['placeholder'] ?? __( 'Search guides, reviews, setup tips…', 'cloud-layout-builder-pro' ) ); ?>" value="<?php echo get_search_query(); ?>" name="s" spellcheck="false" autocomplete="off">
            <button type="submit" class="clbp-mini-search__submit" aria-label="<?php esc_attr_e( 'Submit search', 'cloud-layout-builder-pro' ); ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
        </form>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Search the Hub', 'cloud-layout-builder-pro' );
        $placeholder = ! empty( $instance['placeholder'] ) ? $instance['placeholder'] : __( 'Search guides, reviews, setup tips…', 'cloud-layout-builder-pro' );
        ?>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'cloud-layout-builder-pro' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"></p>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>"><?php esc_html_e( 'Placeholder text:', 'cloud-layout-builder-pro' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $placeholder ); ?>"></p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        return array( 'title' => sanitize_text_field( $new_instance['title'] ), 'placeholder' => sanitize_text_field( $new_instance['placeholder'] ) );
    }
}
