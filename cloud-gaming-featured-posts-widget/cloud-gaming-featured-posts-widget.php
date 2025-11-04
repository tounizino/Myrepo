<?php
/**
 * Plugin Name: Cloud Gaming Featured Posts Widget
 * Description: Professional, responsive featured posts widget tailored for cloud gaming websites with multiple blue-forward themes.
 * Version: 1.0.0
 * Author: Cloud Gaming Studio
 * Text Domain: cloud-gaming-featured-posts
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Cloud_Gaming_Featured_Posts_Widget' ) ) {
    class Cloud_Gaming_Featured_Posts_Widget extends WP_Widget {
        protected static $assets_registered = false;

        public function __construct() {
            parent::__construct(
                'cloud_gaming_featured_posts_widget',
                __( 'Cloud Gaming Featured Posts', 'cloud-gaming-featured-posts' ),
                [
                    'classname'   => 'cloud_gaming_featured_posts_widget',
                    'description' => __( 'A responsive Featured Posts widget crafted for cloud gaming brands with four blue-centric themes.', 'cloud-gaming-featured-posts' ),
                ]
            );

            if ( ! self::$assets_registered ) {
                add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
                self::$assets_registered = true;
            }
        }

        public function register_assets() {
            if ( wp_style_is( 'cloud-gaming-featured-posts-widget', 'registered' ) ) {
                return;
            }

            $style_file = plugin_dir_path( __FILE__ ) . 'assets/css/featured-posts-widget.css';
            $style_url  = plugin_dir_url( __FILE__ ) . 'assets/css/featured-posts-widget.css';
            $version    = file_exists( $style_file ) ? filemtime( $style_file ) : '1.0.0';

            wp_register_style(
                'cloud-gaming-featured-posts-widget',
                $style_url,
                [],
                $version
            );
        }

        public function widget( $args, $instance ) {
            wp_enqueue_style( 'cloud-gaming-featured-posts-widget' );

            $title       = isset( $instance['title'] ) ? $instance['title'] : __( 'Featured Stories', 'cloud-gaming-featured-posts' );
            $theme       = isset( $instance['theme'] ) ? $instance['theme'] : 'sky';
            $posts_count = isset( $instance['posts_count'] ) ? absint( $instance['posts_count'] ) : 3;

            if ( $posts_count < 1 ) {
                $posts_count = 1;
            }

            if ( $posts_count > 6 ) {
                $posts_count = 6;
            }

            $query_args = [
                'posts_per_page'      => $posts_count,
                'ignore_sticky_posts' => true,
                'post_status'         => 'publish',
            ];

            $featured_query = new WP_Query( $query_args );
            $theme_class    = $this->map_theme_class( $theme );

            echo isset( $args['before_widget'] ) ? $args['before_widget'] : '';
            ?>
            <section class="cgw-featured-posts-widget <?php echo esc_attr( $theme_class ); ?>">
                <?php if ( ! empty( $title ) ) : ?>
                    <header class="cgw-widget-header">
                        <h2 class="cgw-widget-title"><?php echo esc_html( $title ); ?></h2>
                        <?php $archive_link = $this->resolve_archive_link(); ?>
                        <?php if ( $archive_link ) : ?>
                            <a class="cgw-widget-link" href="<?php echo esc_url( $archive_link ); ?>">
                                <?php esc_html_e( 'View all', 'cloud-gaming-featured-posts' ); ?>
                                <span aria-hidden="true">→</span>
                            </a>
                        <?php endif; ?>
                    </header>
                <?php endif; ?>

                <?php if ( $featured_query->have_posts() ) : ?>
                    <div class="cgw-posts-grid">
                        <?php
                        while ( $featured_query->have_posts() ) :
                            $featured_query->the_post();

                            $category_label = $this->get_primary_category_label( get_the_ID() );
                            $excerpt        = $this->get_trimmed_excerpt();
                            $reading_time   = $this->estimate_reading_time( get_the_ID() );
                            $thumbnail_id   = get_post_thumbnail_id();
                            $thumbnail_url  = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'large' ) : '';
                            ?>
                            <article class="cgw-post-card">
                                <a class="cgw-post-thumbnail" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                                    <?php if ( $thumbnail_url ) : ?>
                                        <img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
                                    <?php else : ?>
                                        <div class="cgw-post-thumbnail-placeholder">
                                            <span><?php esc_html_e( 'Cloud Gaming', 'cloud-gaming-featured-posts' ); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </a>
                                <div class="cgw-post-content">
                                    <?php if ( $category_label ) : ?>
                                        <span class="cgw-post-category"><?php echo esc_html( $category_label ); ?></span>
                                    <?php endif; ?>

                                    <h3 class="cgw-post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>

                                    <?php if ( $excerpt ) : ?>
                                        <p class="cgw-post-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                                    <?php endif; ?>

                                    <footer class="cgw-post-footer">
                                        <span class="cgw-post-meta">
                                            <?php echo esc_html( get_the_date() ); ?>
                                            <?php if ( $reading_time ) : ?>
                                                <span aria-hidden="true">•</span>
                                                <?php echo esc_html( $reading_time ); ?>
                                            <?php endif; ?>
                                        </span>
                                        <a class="cgw-post-cta" href="<?php the_permalink(); ?>">
                                            <?php esc_html_e( 'Read more', 'cloud-gaming-featured-posts' ); ?>
                                            <span aria-hidden="true">↗</span>
                                        </a>
                                    </footer>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <p class="cgw-empty-state"><?php esc_html_e( 'No featured posts available right now. Check back soon.', 'cloud-gaming-featured-posts' ); ?></p>
                <?php endif; ?>
            </section>
            <?php
            wp_reset_postdata();
            echo isset( $args['after_widget'] ) ? $args['after_widget'] : '';
        }

        public function form( $instance ) {
            $title       = isset( $instance['title'] ) ? $instance['title'] : __( 'Featured Stories', 'cloud-gaming-featured-posts' );
            $theme       = isset( $instance['theme'] ) ? $instance['theme'] : 'sky';
            $posts_count = isset( $instance['posts_count'] ) ? absint( $instance['posts_count'] ) : 3;

            $themes = [
                'sky'   => __( 'Sky Blue', 'cloud-gaming-featured-posts' ),
                'blue'  => __( 'Blue', 'cloud-gaming-featured-posts' ),
                'dark'  => __( 'Dark', 'cloud-gaming-featured-posts' ),
                'light' => __( 'Light', 'cloud-gaming-featured-posts' ),
            ];
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                    <?php esc_html_e( 'Title', 'cloud-gaming-featured-posts' ); ?>
                </label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'posts_count' ) ); ?>">
                    <?php esc_html_e( 'Number of posts to show', 'cloud-gaming-featured-posts' ); ?>
                </label>
                <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'posts_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'posts_count' ) ); ?>" type="number" step="1" min="1" max="6" value="<?php echo esc_attr( $posts_count ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'theme' ) ); ?>">
                    <?php esc_html_e( 'Theme', 'cloud-gaming-featured-posts' ); ?>
                </label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'theme' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'theme' ) ); ?>">
                    <?php foreach ( $themes as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $theme, $value ); ?>>
                            <?php echo esc_html( $label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <?php
        }

        public function update( $new_instance, $old_instance ) {
            $instance                 = [];
            $instance['title']        = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
            $instance['theme']        = isset( $new_instance['theme'] ) ? sanitize_key( $new_instance['theme'] ) : 'sky';
            $instance['posts_count']  = isset( $new_instance['posts_count'] ) ? absint( $new_instance['posts_count'] ) : 3;

            if ( $instance['posts_count'] < 1 ) {
                $instance['posts_count'] = 1;
            }

            if ( $instance['posts_count'] > 6 ) {
                $instance['posts_count'] = 6;
            }

            return $instance;
        }

        private function map_theme_class( $theme ) {
            $allowed = [ 'sky', 'blue', 'dark', 'light' ];
            $theme   = in_array( $theme, $allowed, true ) ? $theme : 'sky';

            return 'cgw-theme-' . $theme;
        }

        private function resolve_archive_link() {
            if ( is_home() ) {
                return '';
            }

            $posts_page_id = (int) get_option( 'page_for_posts' );

            if ( $posts_page_id ) {
                $permalink = get_permalink( $posts_page_id );

                if ( $permalink ) {
                    return $permalink;
                }
            }

            $post_type_archive = get_post_type_archive_link( 'post' );

            return $post_type_archive ? $post_type_archive : home_url( '/' );
        }

        private function get_primary_category_label( $post_id ) {
            $categories = get_the_category( $post_id );

            if ( empty( $categories ) || is_wp_error( $categories ) ) {
                return '';
            }

            $primary = $categories[0];

            return $primary ? $primary->name : '';
        }

        private function get_trimmed_excerpt() {
            $excerpt = get_the_excerpt();

            if ( empty( $excerpt ) ) {
                $excerpt = wp_trim_words( wp_strip_all_tags( get_the_content() ), 20 );
            } else {
                $excerpt = wp_trim_words( $excerpt, 20 );
            }

            return $excerpt;
        }

        private function estimate_reading_time( $post_id ) {
            $content = get_post_field( 'post_content', $post_id );

            if ( empty( $content ) ) {
                return '';
            }

            $word_count = str_word_count( wp_strip_all_tags( $content ) );

            if ( ! $word_count ) {
                return '';
            }

            $minutes = max( 1, (int) ceil( $word_count / 200 ) );

            return sprintf(
                _n( '%d min read', '%d mins read', $minutes, 'cloud-gaming-featured-posts' ),
                $minutes
            );
        }
    }
}

add_action( 'widgets_init', static function () {
    register_widget( 'Cloud_Gaming_Featured_Posts_Widget' );
} );
