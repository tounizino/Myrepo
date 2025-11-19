<?php
/**
 * Plugin Name: Cloud Gaming Latest Posts Grid
 * Plugin URI: https://cloudloadout.com
 * Description: Displays the latest WordPress posts in a cloud-gaming inspired grid with pagination, dark mode, and layout controls.
 * Version: 2.7.0
 * Author: Cloud Gaming
 * Author URI: https://cloudloadout.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-gaming-posts
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CGLP_VERSION', '2.7.0');
define('CGLP_OPTION_KEY', 'cglp_settings');
define('CGLP_TEXTDOMAIN', 'cloud-gaming-posts');

defined('CGLP_PLUGIN_DIR') || define('CGLP_PLUGIN_DIR', plugin_dir_path(__FILE__));
defined('CGLP_PLUGIN_URL') || define('CGLP_PLUGIN_URL', plugin_dir_url(__FILE__));

final class Cloud_Gaming_Latest_Posts {
    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'register_frontend_assets']);
        add_action('admin_menu', [$this, 'register_admin_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_ajax_cglp_load_posts', [$this, 'ajax_load_posts']);
        add_action('wp_ajax_nopriv_cglp_load_posts', [$this, 'ajax_load_posts']);

        load_plugin_textdomain(CGLP_TEXTDOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public static function activate() {
        if (!get_option(CGLP_OPTION_KEY, false)) {
            add_option(CGLP_OPTION_KEY, self::get_default_settings());
        }
    }

    public static function deactivate() {
        // No cleanup required on deactivation.
    }

    public static function get_default_settings() {
        return [
            'posts_per_page' => 9,
            'dark_mode' => 0,
            'container_max_width' => '1400px',
            'container_margin' => '0 auto 40px auto',
            'container_padding' => '0',
        ];
    }

    public function register_shortcode() {
        add_shortcode('cloud_gaming_posts', [$this, 'render_shortcode']);
    }

    public function register_frontend_assets() {
        wp_register_style(
            'cglp-frontend',
            CGLP_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            CGLP_VERSION
        );

        wp_register_script(
            'cglp-frontend',
            CGLP_PLUGIN_URL . 'assets/js/frontend.js',
            ['jquery'],
            CGLP_VERSION,
            true
        );
    }

    public function enqueue_admin_assets($hook) {
        if ('settings_page_cloud-gaming-posts' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'cglp-admin',
            CGLP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            CGLP_VERSION
        );
    }

    public function register_admin_page() {
        add_options_page(
            __('Cloud Gaming Latest Posts', CGLP_TEXTDOMAIN),
            __('Cloud Gaming Latest Posts', CGLP_TEXTDOMAIN),
            'manage_options',
            'cloud-gaming-posts',
            [$this, 'render_admin_page']
        );
    }

    public function register_settings() {
        register_setting(
            'cglp_settings_group',
            CGLP_OPTION_KEY,
            [
                'sanitize_callback' => [$this, 'sanitize_settings'],
                'default' => self::get_default_settings(),
            ]
        );

        add_settings_section(
            'cglp_display_settings',
            __('Display Options', CGLP_TEXTDOMAIN),
            '__return_false',
            'cloud-gaming-posts'
        );

        add_settings_field(
            'cglp_dark_mode',
            __('Enable Dark Theme', CGLP_TEXTDOMAIN),
            [$this, 'render_field_dark_mode'],
            'cloud-gaming-posts',
            'cglp_display_settings'
        );

        add_settings_field(
            'cglp_container_max_width',
            __('Container Max Width', CGLP_TEXTDOMAIN),
            [$this, 'render_field_container_max_width'],
            'cloud-gaming-posts',
            'cglp_display_settings'
        );

        add_settings_field(
            'cglp_container_margin',
            __('Container Margin', CGLP_TEXTDOMAIN),
            [$this, 'render_field_container_margin'],
            'cloud-gaming-posts',
            'cglp_display_settings'
        );

        add_settings_field(
            'cglp_container_padding',
            __('Container Padding', CGLP_TEXTDOMAIN),
            [$this, 'render_field_container_padding'],
            'cloud-gaming-posts',
            'cglp_display_settings'
        );

        add_settings_field(
            'cglp_posts_per_page',
            __('Posts Per Page', CGLP_TEXTDOMAIN),
            [$this, 'render_field_posts_per_page'],
            'cloud-gaming-posts',
            'cglp_display_settings'
        );
    }

    public function sanitize_settings($input) {
        $defaults = self::get_default_settings();

        $output = [];
        $output['dark_mode'] = !empty($input['dark_mode']) ? 1 : 0;
        $output['container_max_width'] = isset($input['container_max_width']) ? sanitize_text_field($input['container_max_width']) : $defaults['container_max_width'];
        $output['container_margin'] = isset($input['container_margin']) ? sanitize_text_field($input['container_margin']) : $defaults['container_margin'];
        $output['container_padding'] = isset($input['container_padding']) ? sanitize_text_field($input['container_padding']) : $defaults['container_padding'];
        $output['posts_per_page'] = isset($input['posts_per_page']) ? max(1, absint($input['posts_per_page'])) : $defaults['posts_per_page'];

        return $output;
    }

    public function render_field_dark_mode() {
        $settings = $this->get_settings();
        ?>
        <label>
            <input type="checkbox" name="<?php echo esc_attr(CGLP_OPTION_KEY); ?>[dark_mode]" value="1" <?php checked(1, (int) $settings['dark_mode']); ?>>
            <?php esc_html_e('Use the dark version of the grid on the frontend.', CGLP_TEXTDOMAIN); ?>
        </label>
        <?php
    }

    public function render_field_container_max_width() {
        $settings = $this->get_settings();
        ?>
        <input type="text" class="regular-text" name="<?php echo esc_attr(CGLP_OPTION_KEY); ?>[container_max_width]" value="<?php echo esc_attr($settings['container_max_width']); ?>" placeholder="1400px">
        <p class="description"><?php esc_html_e('Set a maximum width for the grid container (e.g. 1400px or 100%).', CGLP_TEXTDOMAIN); ?></p>
        <?php
    }

    public function render_field_container_margin() {
        $settings = $this->get_settings();
        ?>
        <input type="text" class="regular-text" name="<?php echo esc_attr(CGLP_OPTION_KEY); ?>[container_margin]" value="<?php echo esc_attr($settings['container_margin']); ?>" placeholder="0 auto 40px auto">
        <p class="description"><?php esc_html_e('CSS margin applied to the outer container (e.g. 0 auto 40px auto).', CGLP_TEXTDOMAIN); ?></p>
        <?php
    }

    public function render_field_container_padding() {
        $settings = $this->get_settings();
        ?>
        <input type="text" class="regular-text" name="<?php echo esc_attr(CGLP_OPTION_KEY); ?>[container_padding]" value="<?php echo esc_attr($settings['container_padding']); ?>" placeholder="0">
        <p class="description"><?php esc_html_e('CSS padding applied inside the container (e.g. 0 or 0 16px).', CGLP_TEXTDOMAIN); ?></p>
        <?php
    }

    public function render_field_posts_per_page() {
        $settings = $this->get_settings();
        ?>
        <input type="number" class="small-text" min="1" name="<?php echo esc_attr(CGLP_OPTION_KEY); ?>[posts_per_page]" value="<?php echo esc_attr($settings['posts_per_page']); ?>">
        <p class="description"><?php esc_html_e('Number of posts to display on each page of the grid.', CGLP_TEXTDOMAIN); ?></p>
        <?php
    }

    private function get_settings() {
        $settings = get_option(CGLP_OPTION_KEY, []);
        return wp_parse_args($settings, self::get_default_settings());
    }

    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings = $this->get_settings();
        include CGLP_PLUGIN_DIR . 'includes/admin-page.php';
    }

    public function render_shortcode($atts) {
        $settings = $this->get_settings();

        $atts = shortcode_atts([
            'posts_per_page' => $settings['posts_per_page'],
        ], $atts, 'cloud_gaming_posts');

        $posts_per_page = max(1, absint($atts['posts_per_page']));

        $query = new WP_Query([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'paged' => 1,
            'ignore_sticky_posts' => true,
        ]);

        $container_id = wp_unique_id('cglp-');
        $total_pages = max(1, (int) $query->max_num_pages);

        wp_enqueue_style('cglp-frontend');
        wp_enqueue_script('cglp-frontend');

        static $localized = false;
        if (!$localized) {
            wp_localize_script('cglp-frontend', 'CloudGamingPosts', [
                'ajaxUrl' => esc_url_raw(admin_url('admin-ajax.php')),
                'nonce' => wp_create_nonce('cglp_load_posts'),
                'i18n' => [
                    'previous' => __('← Previous', CGLP_TEXTDOMAIN),
                    'next' => __('Next →', CGLP_TEXTDOMAIN),
                    'noPosts' => __('No posts found.', CGLP_TEXTDOMAIN),
                    'minRead' => __('%s min read', CGLP_TEXTDOMAIN),
                ],
            ]);
            $localized = true;
        }

        ob_start();
        include CGLP_PLUGIN_DIR . 'includes/posts-grid.php';
        wp_reset_postdata();

        return ob_get_clean();
    }

    public function ajax_load_posts() {
        check_ajax_referer('cglp_load_posts', 'nonce');

        $page = isset($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
        $posts_per_page = isset($_POST['posts_per_page']) ? max(1, absint($_POST['posts_per_page'])) : self::get_default_settings()['posts_per_page'];

        $query = new WP_Query([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'paged' => $page,
            'ignore_sticky_posts' => true,
        ]);

        $html = '';
        if ($query->have_posts()) {
            ob_start();
            while ($query->have_posts()) {
                $query->the_post();
                include CGLP_PLUGIN_DIR . 'includes/post-card.php';
            }
            $html = ob_get_clean();
        }

        wp_reset_postdata();

        wp_send_json_success([
            'html' => $html,
            'total_pages' => max(1, (int) $query->max_num_pages),
            'current_page' => $page,
        ]);
    }

    public static function calculate_read_time($post_id) {
        $content = get_post_field('post_content', $post_id);
        $word_count = str_word_count(wp_strip_all_tags($content));

        if (!$word_count) {
            $excerpt = get_post_field('post_excerpt', $post_id);
            $word_count = str_word_count(wp_strip_all_tags($excerpt));
        }

        $minutes = (int) ceil($word_count / 200);

        return max(1, $minutes);
    }

    public static function get_post_badge($post_id) {
        $now = current_time('timestamp');
        $published = get_post_time('U', true, $post_id);
        $modified = get_post_modified_time('U', true, $post_id);

        if (($now - $published) <= 14 * DAY_IN_SECONDS) {
            return [
                'type' => 'new',
                'label' => __('NEW', CGLP_TEXTDOMAIN),
            ];
        }

        if (($modified - $published) > DAY_IN_SECONDS && ($now - $modified) <= 30 * DAY_IN_SECONDS) {
            return [
                'type' => 'update',
                'label' => __('UPDATED', CGLP_TEXTDOMAIN),
            ];
        }

        $comments = get_comments_number($post_id);
        if ($comments >= 5) {
            return [
                'type' => 'hot',
                'label' => __('HOT', CGLP_TEXTDOMAIN),
            ];
        }

        return null;
    }
}

Cloud_Gaming_Latest_Posts::instance();

register_activation_hook(__FILE__, ['Cloud_Gaming_Latest_Posts', 'activate']);
register_deactivation_hook(__FILE__, ['Cloud_Gaming_Latest_Posts', 'deactivate']);
