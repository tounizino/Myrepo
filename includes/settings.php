<?php
class CGE_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page() {
        add_options_page(
            'Cloud Game Explorer Settings',
            'Cloud Game Explorer',
            'manage_options',
            'cge-settings',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('cge_settings_group', 'cge_settings');
        
        add_settings_section(
            'cge_main_section',
            'Main Settings',
            null,
            'cge-settings'
        );

        add_settings_field(
            'rawg_api_key',
            'RAWG API Key',
            array($this, 'render_api_key_field'),
            'cge-settings',
            'cge_main_section'
        );
    }

    public function render_api_key_field() {
        $options = get_option('cge_settings');
        $value = isset($options['rawg_api_key']) ? $options['rawg_api_key'] : '';
        echo '<input type="text" name="cge_settings[rawg_api_key]" value="' . esc_attr($value) . '" class="regular-text">';
        echo '<p class="description">Get your free API key at <a href="https://rawg.io/apidocs" target="_blank">RAWG.io</a></p>';
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Cloud Game Explorer Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('cge_settings_group');
                do_settings_sections('cge-settings');
                submit_button();
                ?>
            </form>
            <div class="card">
                <h2>Shortcode</h2>
                <p>Use the following shortcode to display the explorer on any page:</p>
                <code>[cloud_game_explorer]</code>
            </div>
            <div class="card">
                <h2>Cloud Data Synchronization</h2>
                <p>The plugin uses a heuristic for cloud availability by default. For more accurate data, you can connect a data source or wait for future updates.</p>
            </div>
        </div>
        <style>
            .card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 15px;
                margin-top: 20px;
                max-width: 800px;
            }
        </style>
        <?php
    }
}

new CGE_Settings();
