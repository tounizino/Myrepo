<?php
/**
 * Cloud Layout Builder Pro - AJAX Handlers
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class CLBP_Ajax {

    /**
     * Instance
     *
     * @var CLBP_Ajax
     */
    private static $instance = null;

    /**
     * Singleton instance
     *
     * @return CLBP_Ajax
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'wp_ajax_clbp_load_more_posts', array( $this, 'load_more_posts' ) );
        add_action( 'wp_ajax_nopriv_clbp_load_more_posts', array( $this, 'load_more_posts' ) );

        add_action( 'wp_ajax_clbp_newsletter_signup', array( $this, 'newsletter_signup' ) );
        add_action( 'wp_ajax_nopriv_clbp_newsletter_signup', array( $this, 'newsletter_signup' ) );

        add_action( 'clbp_track_block_view', array( $this, 'track_block_view' ) );
    }

    /**
     * Load more posts handler
     */
    public function load_more_posts() {
        check_ajax_referer( 'clbp_nonce', 'nonce' );

        $raw_query = isset( $_POST['query'] ) ? wp_unslash( $_POST['query'] ) : '';
        $page      = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

        if ( empty( $raw_query ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid query.', 'cloud-layout-builder-pro' ) ) );
        }

        $query_args = json_decode( $raw_query, true );

        if ( ! is_array( $query_args ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid parameters.', 'cloud-layout-builder-pro' ) ) );
        }

        $query_args['paged']          = max( 1, $page );
        $query_args['posts_per_page'] = isset( $query_args['posts_per_page'] ) ? absint( $query_args['posts_per_page'] ) : 9;
        $query_args['post_status']    = 'publish';

        $query = new WP_Query( $query_args );

        if ( ! $query->have_posts() ) {
            wp_send_json_error( array( 'message' => __( 'No additional posts found.', 'cloud-layout-builder-pro' ) ) );
        }

        ob_start();

        while ( $query->have_posts() ) {
            $query->the_post();
            echo CLBP_Helpers::render_post_card( get_post(), array( 'layout' => 'vertical' ) );
        }

        $html = ob_get_clean();
        wp_reset_postdata();

        $has_more = $query->max_num_pages > $page;

        wp_send_json_success(
            array(
                'html'     => $html,
                'hasMore'  => $has_more,
                'nextPage' => $page + 1,
            )
        );
    }

    /**
     * Newsletter signup handler
     */
    public function newsletter_signup() {
        $nonce = isset( $_POST['nonce'] ) ? wp_unslash( $_POST['nonce'] ) : '';

        if ( ! wp_verify_nonce( $nonce, 'clbp_newsletter' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'cloud-layout-builder-pro' ) ) );
        }

        $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

        if ( empty( $email ) || ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'cloud-layout-builder-pro' ) ) );
        }

        // In production, integrate with Mailchimp/Brevo here.
        // For now, we simply store the lead in a transient for demonstration.
        $leads = get_option( 'clbp_newsletter_leads', array() );
        $leads[] = array(
            'email' => $email,
            'time'  => current_time( 'mysql' ),
        );

        update_option( 'clbp_newsletter_leads', $leads );

        wp_send_json_success( array( 'message' => __( 'You are subscribed!', 'cloud-layout-builder-pro' ) ) );
    }

    /**
     * Track block view
     *
     * @param string $block Block slug.
     */
    public function track_block_view( $block ) {
        CLBP_Analytics::record( sanitize_key( $block ) );
    }
}
