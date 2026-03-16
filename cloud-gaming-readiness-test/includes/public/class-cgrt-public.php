<?php
/**
 * Public-facing functionality handler.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Public class.
 *
 * @since 1.0.0
 */
class CGRT_Public {

    /**
     * The plugin name.
     *
     * @since 1.0.0
     * @var string
     */
    private $plugin_name;

    /**
     * The plugin version.
     *
     * @since 1.0.0
     * @var string
     */
    private $version;

    /**
     * Initialize the class.
     *
     * @since 1.0.0
     * @param string $plugin_name The plugin name.
     * @param string $version The plugin version.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        // Only enqueue if shortcode is present or on our template page.
        if ( ! $this->should_enqueue_assets() ) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name . '-public',
            CGRT_PLUGIN_URL . 'assets/css/public.css',
            array(),
            $this->version,
            'all'
        );

        // Enqueue theme CSS.
        $default_mode = get_option( 'cgrt_default_mode', 'dark' );
        wp_enqueue_style(
            $this->plugin_name . '-' . $default_mode,
            CGRT_PLUGIN_URL . 'assets/css/themes/' . $default_mode . '.css',
            array( $this->plugin_name . '-public' ),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        // Only enqueue if shortcode is present or on our template page.
        if ( ! $this->should_enqueue_assets() ) {
            return;
        }

        $settings = array(
            'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
            'restUrl'         => rest_url( 'cloud-gaming-test/v1' ),
            'nonce'           => wp_create_nonce( 'cgrt_public_nonce' ),
            'cloudflareEnabled' => get_option( 'cgrt_cloudflare_enabled', false ),
            'pingCount'       => get_option( 'cgrt_ping_count', 10 ),
            'testTimeout'     => get_option( 'cgrt_test_timeout', 30 ),
            'enableAdvanced'  => get_option( 'cgrt_enable_advanced', false ),
            'homeUrl'         => get_option( 'cgrt_home_url', home_url() ),
            'themeColor'      => get_option( 'cgrt_theme_color', '#6366f1' ),
            'defaultMode'     => get_option( 'cgrt_default_mode', 'dark' ),
            'thresholds'      => array(
                'latency' => array(
                    'excellent' => get_option( 'cgrt_latency_excellent', 20 ),
                    'good'      => get_option( 'cgrt_latency_good', 50 ),
                    'fair'      => get_option( 'cgrt_latency_fair', 100 ),
                ),
                'jitter' => array(
                    'excellent' => get_option( 'cgrt_jitter_excellent', 5 ),
                    'good'      => get_option( 'cgrt_jitter_good', 10 ),
                    'fair'      => get_option( 'cgrt_jitter_fair', 20 ),
                ),
                'packetLoss' => array(
                    'good' => get_option( 'cgrt_packet_loss_good', 1 ),
                    'fair' => get_option( 'cgrt_packet_loss_fair', 3 ),
                ),
            ),
            'strings' => array(
                'starting'    => __( 'Starting test...', 'cloud-gaming-readiness-test' ),
                'testingPing' => __( 'Testing latency...', 'cloud-gaming-readiness-test' ),
                'testingJitter' => __( 'Testing jitter...', 'cloud-gaming-readiness-test' ),
                'testingPacketLoss' => __( 'Testing packet loss...', 'cloud-gaming-readiness-test' ),
                'testingSpeed' => __( 'Testing connection speed...', 'cloud-gaming-readiness-test' ),
                'complete'    => __( 'Test complete!', 'cloud-gaming-readiness-test' ),
                'error'       => __( 'An error occurred. Please try again.', 'cloud-gaming-readiness-test' ),
                'ready'       => __( 'Ready for Cloud Gaming', 'cloud-gaming-readiness-test' ),
                'notReady'    => __( 'Not Ready for Cloud Gaming', 'cloud-gaming-readiness-test' ),
                'ms'          => __( 'ms', 'cloud-gaming-readiness-test' ),
                'mbps'        => __( 'Mbps', 'cloud-gaming-readiness-test' ),
                'excellent'   => __( 'Excellent', 'cloud-gaming-readiness-test' ),
                'good'        => __( 'Good', 'cloud-gaming-readiness-test' ),
                'fair'        => __( 'Fair', 'cloud-gaming-readiness-test' ),
                'poor'        => __( 'Poor', 'cloud-gaming-readiness-test' ),
            ),
        );

        wp_enqueue_script(
            $this->plugin_name . '-network-test',
            CGRT_PLUGIN_URL . 'assets/js/public/network-test.js',
            array(),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-speed-test',
            CGRT_PLUGIN_URL . 'assets/js/public/speed-test.js',
            array( $this->plugin_name . '-network-test' ),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-ui-animations',
            CGRT_PLUGIN_URL . 'assets/js/public/ui-animations.js',
            array(),
            $this->version,
            true
        );

        wp_enqueue_script(
            $this->plugin_name . '-app',
            CGRT_PLUGIN_URL . 'assets/js/public/app.js',
            array(
                $this->plugin_name . '-network-test',
                $this->plugin_name . '-speed-test',
                $this->plugin_name . '-ui-animations',
            ),
            $this->version,
            true
        );

        wp_localize_script(
            $this->plugin_name . '-app',
            'cgrtSettings',
            $settings
        );
    }

    /**
     * Register shortcodes.
     *
     * @since 1.0.0
     */
    public function register_shortcodes() {
        add_shortcode( 'cloud_gaming_test', array( $this, 'render_shortcode' ) );
    }

    /**
     * Render the shortcode.
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'mode'     => get_option( 'cgrt_default_mode', 'dark' ),
            'theme'    => get_option( 'cgrt_theme_color', '#6366f1' ),
        ), $atts, 'cloud_gaming_test' );

        ob_start();
        require CGRT_PLUGIN_DIR . 'includes/public/views/test-page.php';
        return ob_get_clean();
    }

    /**
     * Add custom page template.
     *
     * @since 1.0.0
     * @param array $templates Existing templates.
     * @return array Modified templates.
     */
    public function add_page_template( $templates ) {
        if ( get_option( 'cgrt_enable_page_mode', true ) ) {
            $templates['cloud-gaming-test-page.php'] = __( 'Cloud Gaming Test Page', 'cloud-gaming-readiness-test' );
        }
        return $templates;
    }

    /**
     * Load custom page template.
     *
     * @since 1.0.0
     * @param string $template The template path.
     * @return string Modified template path.
     */
    public function load_page_template( $template ) {
        if ( is_page() ) {
            $page_template = get_page_template_slug();

            if ( 'cloud-gaming-test-page.php' === $page_template ) {
                $template = CGRT_PLUGIN_DIR . 'templates/full-page-template.php';
            }
        }

        return $template;
    }

    /**
     * Check if assets should be enqueued.
     *
     * @since 1.0.0
     * @return bool Whether to enqueue.
     */
    private function should_enqueue_assets() {
        global $post;

        if ( is_singular() && is_a( $post, 'WP_Post' ) ) {
            // Check for shortcode.
            if ( has_shortcode( $post->post_content, 'cloud_gaming_test' ) ) {
                return true;
            }

            // Check for custom page template.
            if ( 'cloud-gaming-test-page.php' === get_page_template_slug( $post->ID ) ) {
                return true;
            }
        }

        return false;
    }
}
