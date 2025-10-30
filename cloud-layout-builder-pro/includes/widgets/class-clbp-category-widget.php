<?php
/**
 * Cloud Layout Builder Pro - Category Widget with Icons
 *
 * @package CloudLayoutBuilderPro\Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CLBP_Category_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'clbp_category_widget',
            __( 'CLBP: Category List', 'cloud-layout-builder-pro' ),
            array( 'description' => __( 'Display categories with icon badges and post counts', 'cloud-layout-builder-pro' ) )
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $categories = get_categories( array(
            'orderby' => $instance['orderby'] ?? 'count',
            'order'   => $instance['order'] ?? 'DESC',
            'number'  => absint( $instance['count'] ?? 6 ),
        ) );

        if ( empty( $categories ) ) {
            echo $args['after_widget'];
            return;
        }

        echo '<ul class="clbp-category-widget">';
        foreach ( $categories as $category ) {
            $icon = get_term_meta( $category->term_id, 'clbp_icon', true );
            ?>
            <li class="clbp-category-widget__item">
                <a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
                    <?php if ( $icon ) : ?>
                        <span class="clbp-category-widget__icon"><i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i></span>
                    <?php else : ?>
                        <span class="clbp-category-widget__icon" aria-hidden="true">#</span>
                    <?php endif; ?>
                    <span class="clbp-category-widget__name"><?php echo esc_html( $category->name ); ?></span>
                    <span class="clbp-category-widget__count"><?php echo esc_html( $category->count ); ?></span>
                </a>
            </li>
            <?php
        }
        echo '</ul>';

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Categories', 'cloud-layout-builder-pro' );
        $count = ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 6;
        $order = ! empty( $instance['order'] ) ? $instance['order'] : 'DESC';
        $orderby = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'count';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'cloud-layout-builder-pro' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of categories:', 'cloud-layout-builder-pro' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>" min="1" max="20">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order by:', 'cloud-layout-builder-pro' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" class="widefat">
                <option value="count" <?php selected( $orderby, 'count' ); ?>><?php esc_html_e( 'Post count', 'cloud-layout-builder-pro' ); ?></option>
                <option value="name" <?php selected( $orderby, 'name' ); ?>><?php esc_html_e( 'Name', 'cloud-layout-builder-pro' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order:', 'cloud-layout-builder-pro' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" class="widefat">
                <option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php esc_html_e( 'Descending', 'cloud-layout-builder-pro' ); ?></option>
                <option value="ASC" <?php selected( $order, 'ASC' ); ?>><?php esc_html_e( 'Ascending', 'cloud-layout-builder-pro' ); ?></option>
            </select>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']  = sanitize_text_field( $new_instance['title'] );
        $instance['count']  = absint( $new_instance['count'] );
        $instance['orderby'] = sanitize_text_field( $new_instance['orderby'] );
        $instance['order']  = sanitize_text_field( $new_instance['order'] );
        return $instance;
    }
}
