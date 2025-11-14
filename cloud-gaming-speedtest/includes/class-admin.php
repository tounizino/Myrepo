<?php
/**
 * Admin Area
 */

if (!defined('ABSPATH')) {
    exit;
}

class CGST_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'register_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function register_menu() {
        add_menu_page(
            __('Cloud Gaming Speed Test', 'cloud-gaming-speedtest'),
            __('Cloud Speed Test', 'cloud-gaming-speedtest'),
            'manage_options',
            'cloud-gaming-speedtest',
            array($this, 'render_settings_page'),
            'dashicons-dashboard',
            65
        );
    }
    
    public function register_settings() {
        register_setting(
            'cgst_settings_group',
            'cgst_color_theme',
            array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_theme'),
                'default' => 'dark'
            )
        );
        
        register_setting(
            'cgst_settings_group',
            'cgst_custom_resources',
            array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_resources'),
                'default' => CGST_Speed_Test::get_default_resources()
            )
        );
        
        register_setting(
            'cgst_settings_group',
            'cgst_intro_text',
            array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_textarea'),
                'default' => __('Run the full cloud gaming connectivity check to see if your network can keep up with ultra responsive streaming.', 'cloud-gaming-speedtest')
            )
        );
        
        register_setting(
            'cgst_settings_group',
            'cgst_footer_note',
            array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_textarea'),
                'default' => __('Tip: For the most accurate results, test on a wired connection and close bandwidth-heavy applications.', 'cloud-gaming-speedtest')
            )
        );
    }
    
    public function sanitize_theme($theme) {
        $allowed = array('dark', 'sky', 'light');
        return in_array($theme, $allowed, true) ? $theme : 'dark';
    }
    
    public function sanitize_resources($resources) {
        return CGST_Speed_Test::sanitize_resources($resources);
    }
    
    public function sanitize_textarea($value) {
        return wp_kses_post($value);
    }
    
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $theme = get_option('cgst_color_theme', 'dark');
        $resources = get_option('cgst_custom_resources', CGST_Speed_Test::get_default_resources());
        $intro_text = get_option('cgst_intro_text', '');
        $footer_note = get_option('cgst_footer_note', '');
        
        $resource_count = max(4, count($resources));
        ?>
        <div class="cgst-admin-page wrap">
            <h1><?php esc_html_e('Cloud Gaming Speed Test', 'cloud-gaming-speedtest'); ?></h1>
            <p class="description"><?php esc_html_e('Configure the global appearance and content for the Cloud Gaming Speed Test shortcode.', 'cloud-gaming-speedtest'); ?></p>
            
            <form action="options.php" method="post" class="cgst-settings-form">
                <?php
                settings_fields('cgst_settings_group');
                do_settings_sections('cgst_settings_group');
                ?>
                
                <h2><?php esc_html_e('Theme Selection', 'cloud-gaming-speedtest'); ?></h2>
                <p><?php esc_html_e('Choose the default visual style. This can be overridden per shortcode.', 'cloud-gaming-speedtest'); ?></p>
                <div class="cgst-theme-options">
                    <label class="cgst-theme-card">
                        <input type="radio" name="cgst_color_theme" value="dark" <?php checked($theme, 'dark'); ?> />
                        <span class="cgst-theme-preview cgst-theme-dark"></span>
                        <strong><?php esc_html_e('Dark Neon', 'cloud-gaming-speedtest'); ?></strong>
                        <em><?php esc_html_e('High contrast, esports-inspired aesthetic', 'cloud-gaming-speedtest'); ?></em>
                    </label>
                    <label class="cgst-theme-card">
                        <input type="radio" name="cgst_color_theme" value="sky" <?php checked($theme, 'sky'); ?> />
                        <span class="cgst-theme-preview cgst-theme-sky"></span>
                        <strong><?php esc_html_e('Sky Pulse', 'cloud-gaming-speedtest'); ?></strong>
                        <em><?php esc_html_e('Energetic gradients with airy layout', 'cloud-gaming-speedtest'); ?></em>
                    </label>
                    <label class="cgst-theme-card">
                        <input type="radio" name="cgst_color_theme" value="light" <?php checked($theme, 'light'); ?> />
                        <span class="cgst-theme-preview cgst-theme-light"></span>
                        <strong><?php esc_html_e('Light Fusion', 'cloud-gaming-speedtest'); ?></strong>
                        <em><?php esc_html_e('Clean, professional interface for corporate sites', 'cloud-gaming-speedtest'); ?></em>
                    </label>
                </div>
                
                <h2><?php esc_html_e('Introductory Message', 'cloud-gaming-speedtest'); ?></h2>
                <textarea name="cgst_intro_text" rows="3" class="large-text"><?php echo esc_textarea($intro_text); ?></textarea>
                
                <h2><?php esc_html_e('Helpful Resources', 'cloud-gaming-speedtest'); ?></h2>
                <p><?php esc_html_e('Provide up to five helpful links that appear below the test results.', 'cloud-gaming-speedtest'); ?></p>
                <div class="cgst-resources-grid">
                    <?php for ($i = 0; $i < $resource_count; $i++) :
                        $title = isset($resources[$i]['title']) ? $resources[$i]['title'] : '';
                        $url = isset($resources[$i]['url']) ? $resources[$i]['url'] : '';
                    ?>
                        <div class="cgst-resource-item">
                            <label>
                                <span><?php printf(__('Resource %d Title', 'cloud-gaming-speedtest'), $i + 1); ?></span>
                                <input type="text" name="cgst_custom_resources[<?php echo esc_attr($i); ?>][title]" value="<?php echo esc_attr($title); ?>" />
                            </label>
                            <label>
                                <span><?php printf(__('Resource %d URL', 'cloud-gaming-speedtest'), $i + 1); ?></span>
                                <input type="url" name="cgst_custom_resources[<?php echo esc_attr($i); ?>][url]" value="<?php echo esc_attr($url); ?>" placeholder="https://" />
                            </label>
                        </div>
                    <?php endfor; ?>
                </div>
                <p>
                    <button type="button" class="button button-secondary" id="cgst-add-resource"><?php esc_html_e('Add Resource', 'cloud-gaming-speedtest'); ?></button>
                </p>
                
                <h2><?php esc_html_e('Footer Note', 'cloud-gaming-speedtest'); ?></h2>
                <textarea name="cgst_footer_note" rows="3" class="large-text"><?php echo esc_textarea($footer_note); ?></textarea>
                <p class="description"><?php esc_html_e('Displayed below the resource links. Perfect for additional tips or support contact.', 'cloud-gaming-speedtest'); ?></p>
                
                <?php submit_button(__('Save Settings', 'cloud-gaming-speedtest')); ?>
            </form>
            
            <div class="cgst-shortcode-help">
                <h2><?php esc_html_e('Usage', 'cloud-gaming-speedtest'); ?></h2>
                <p><?php esc_html_e('Embed the speed test with the shortcode below:', 'cloud-gaming-speedtest'); ?></p>
                <code>[cloud_gaming_speedtest]</code>
                <p><?php esc_html_e('Override the theme per shortcode instance:', 'cloud-gaming-speedtest'); ?></p>
                <code>[cloud_gaming_speedtest theme="sky"]</code>
            </div>
        </div>
        <?php
    }
}
