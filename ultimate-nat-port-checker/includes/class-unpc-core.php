<?php
/**
 * Core functionality for the Ultimate NAT & Port Checker plugin.
 *
 * @package UltimateNATPortChecker
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('UNPC_Core')) {
    /**
     * Main plugin core class.
     */
    class UNPC_Core {
        /**
         * Cached settings.
         *
         * @var array
         */
        private $settings = array();

        /**
         * Constructor.
         */
        public function __construct() {
            $this->settings = unpc_get_settings();

            add_action('init', array($this, 'load_textdomain'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
            add_action('wp_head', array($this, 'output_custom_properties'));
        }

        /**
         * Load plugin translations.
         *
         * @return void
         */
        public function load_textdomain() {
            load_plugin_textdomain('ultimate-nat-port-checker', false, dirname(plugin_basename(UNPC_PLUGIN_DIR . 'ultimate-nat-port-checker.php')) . '/languages');
        }

        /**
         * Determine if assets should be enqueued.
         *
         * @return bool
         */
        private function should_enqueue_assets() {
            if (is_admin()) {
                return false;
            }

            /**
             * Filter to force assets to load regardless of shortcode detection.
             *
             * @param bool $force Force enqueue.
             */
            $force_enqueue = apply_filters('unpc_force_enqueue_assets', false);
            if ($force_enqueue) {
                return true;
            }

            if (!is_singular()) {
                return false;
            }

            $post = get_post();
            if (!$post instanceof WP_Post) {
                return false;
            }

            $has_shortcode = has_shortcode($post->post_content, 'nat_port_checker');

            if (!$has_shortcode && function_exists('has_block')) {
                if (has_block('core/shortcode', $post)) {
                    $pattern = '\\[nat_port_checker';
                    $has_shortcode = (bool) preg_match('/' . $pattern . '/i', $post->post_content);
                }
            }

            /**
             * Filter to determine whether assets should be loaded.
             *
             * @param bool $has_shortcode Whether the shortcode is present.
             * @param WP_Post $post The current post object.
             */
            return apply_filters('unpc_should_enqueue_assets', $has_shortcode, $post);
        }

        /**
         * Enqueue front-end assets.
         *
         * @return void
         */
        public function enqueue_assets() {
            if (!$this->should_enqueue_assets()) {
                return;
            }

            wp_enqueue_style(
                'unpc-styles',
                UNPC_PLUGIN_URL . 'assets/css/ultimate-nat-port-checker.css',
                array(),
                UNPC_VERSION
            );

            wp_enqueue_script(
                'unpc-script',
                UNPC_PLUGIN_URL . 'assets/js/ultimate-nat-port-checker.js',
                array(),
                UNPC_VERSION,
                true
            );

            $localized = array(
                'restUrl' => esc_url_raw(rest_url('unpc/v1/')),
                'nonce' => wp_create_nonce('wp_rest'),
                'settings' => array(
                    'defaultTheme' => $this->settings['theme'],
                    'accentColor' => $this->settings['accent_color'],
                    'animationSpeed' => $this->settings['animation_speed'],
                    'enableAnimations' => (bool) $this->settings['enable_animations'],
                    'containerWidth' => (int) $this->settings['container_width'],
                    'customQuickTip' => $this->settings['custom_quick_tip'],
                    'apiTimeout' => (int) $this->settings['api_timeout'],
                ),
                'strings' => array(
                    'checkingNat' => __('Analyzing NAT type…', 'ultimate-nat-port-checker'),
                    'natCheckReady' => __('Run NAT Analysis', 'ultimate-nat-port-checker'),
                    'natCheckComplete' => __('Analysis complete', 'ultimate-nat-port-checker'),
                    'natType1' => __('Type 1 (Open) – Ideal for cloud gaming and peer-to-peer services.', 'ultimate-nat-port-checker'),
                    'natType2' => __('Type 2 (Moderate) – Acceptable, but some services may require additional configuration.', 'ultimate-nat-port-checker'),
                    'natType3' => __('Type 3 (Strict) – Requires configuration changes for optimal connectivity.', 'ultimate-nat-port-checker'),
                    'portCheckRunning' => __('Checking ports…', 'ultimate-nat-port-checker'),
                    'portCheckReady' => __('Check Selected Ports', 'ultimate-nat-port-checker'),
                    'portCheckComplete' => __('Port check complete', 'ultimate-nat-port-checker'),
                    'noPortsProvided' => __('Please add at least one port before running a check.', 'ultimate-nat-port-checker'),
                    'invalidHost' => __('Please provide a valid IP address or hostname.', 'ultimate-nat-port-checker'),
                    'apiError' => __('We could not reach the diagnostics service. Please try again later.', 'ultimate-nat-port-checker'),
                ),
                'portPresets' => $this->get_port_presets(),
            );

            wp_localize_script('unpc-script', 'UNPC_DATA', $localized);
        }

        /**
         * Expose dynamic CSS custom properties.
         *
         * @return void
         */
        public function output_custom_properties() {
            if (!$this->should_enqueue_assets()) {
                return;
            }

            $accent = isset($this->settings['accent_color']) ? sanitize_hex_color($this->settings['accent_color']) : '#3a7afe';
            $container_width = isset($this->settings['container_width']) ? (int) $this->settings['container_width'] : 1400;
            $animation_map = array(
                'slow' => '420ms',
                'normal' => '260ms',
                'fast' => '180ms',
            );
            $animation_speed = isset($animation_map[$this->settings['animation_speed']]) ? $animation_map[$this->settings['animation_speed']] : $animation_map['normal'];

            echo '<style id="unpc-dynamic-styles">';
            echo ':root{--unpc-accent:' . esc_attr($accent) . ';--unpc-max-width:' . esc_attr($container_width) . 'px;--unpc-animation-speed:' . esc_attr($animation_speed) . ';}';
            echo '</style>';
        }

        /**
         * Retrieve predefined port presets.
         *
         * @return array
         */
        private function get_port_presets() {
            $presets = array(
                array(
                    'id' => 'playstation-network',
                    'label' => __('PlayStation Network', 'ultimate-nat-port-checker'),
                    'description' => __('Optimized connectivity for PlayStation cloud gaming services.', 'ultimate-nat-port-checker'),
                    'entries' => array(
                        array('ports' => '80,443,3478-3480', 'protocol' => 'tcp'),
                        array('ports' => '3478-3479', 'protocol' => 'udp'),
                    ),
                ),
                array(
                    'id' => 'xbox-live',
                    'label' => __('Xbox Network & Game Pass', 'ultimate-nat-port-checker'),
                    'description' => __('Required ports for Xbox console and Game Pass cloud gaming services.', 'ultimate-nat-port-checker'),
                    'entries' => array(
                        array('ports' => '53,80,3074,500,3544,4500', 'protocol' => 'udp'),
                        array('ports' => '53,80,3074', 'protocol' => 'tcp'),
                    ),
                ),
                array(
                    'id' => 'geforce-now',
                    'label' => __('NVIDIA GeForce NOW', 'ultimate-nat-port-checker'),
                    'description' => __('Ports recommended by NVIDIA for the best GeForce NOW experience.', 'ultimate-nat-port-checker'),
                    'entries' => array(
                        array('ports' => '49003-49100', 'protocol' => 'udp'),
                        array('ports' => '80,443,8001,1935', 'protocol' => 'tcp'),
                    ),
                ),
                array(
                    'id' => 'playstation-remote',
                    'label' => __('PlayStation Remote Play', 'ultimate-nat-port-checker'),
                    'description' => __('Ensure remote play sessions remain stable and low-latency.', 'ultimate-nat-port-checker'),
                    'entries' => array(
                        array('ports' => '9295-9304', 'protocol' => 'udp'),
                        array('ports' => '9295-9304', 'protocol' => 'tcp'),
                    ),
                ),
                array(
                    'id' => 'cloud-gaming-generic',
                    'label' => __('Generic Cloud Gaming', 'ultimate-nat-port-checker'),
                    'description' => __('A balanced preset for Stadia alternatives, Boosteroid, and Shadow.', 'ultimate-nat-port-checker'),
                    'entries' => array(
                        array('ports' => '3478-3480,1935,48010-48020', 'protocol' => 'udp'),
                        array('ports' => '80,443,1935,8088', 'protocol' => 'tcp'),
                    ),
                ),
            );

            /**
             * Filter the available port presets.
             *
             * @param array $presets Preset definitions.
             */
            return apply_filters('unpc_port_presets', $presets);
        }
    }
}
