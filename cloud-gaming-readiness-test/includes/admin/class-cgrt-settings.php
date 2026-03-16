<?php
/**
 * Settings API handler for the admin panel.
 *
 * @package Cloud_Gaming_Readiness_Test
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Settings class.
 *
 * @since 1.0.0
 */
class CGRT_Settings {

    /**
     * Register all settings.
     *
     * @since 1.0.0
     */
    public function register() {
        // General settings section.
        $this->register_general_settings();

        // Appearance settings section.
        $this->register_appearance_settings();

        // Cloudflare API settings section.
        $this->register_cloudflare_settings();

        // Results thresholds settings section.
        $this->register_thresholds_settings();

        // Test settings section.
        $this->register_test_settings();
    }

    /**
     * Register general settings.
     *
     * @since 1.0.0
     */
    private function register_general_settings() {
        add_settings_section(
            'cgrt_general',
            __( 'General Settings', 'cloud-gaming-readiness-test' ),
            array( $this, 'general_section_callback' ),
            'cgrt_settings'
        );

        add_settings_field(
            'cgrt_home_url',
            __( 'Home URL', 'cloud-gaming-readiness-test' ),
            array( $this, 'text_field_callback' ),
            'cgrt_settings',
            'cgrt_general',
            array(
                'label_for' => 'cgrt_home_url',
                'desc'      => __( 'The base URL for the test page.', 'cloud-gaming-readiness-test' ),
            )
        );

        add_settings_field(
            'cgrt_show_shortcode',
            __( 'Show Shortcode Helper', 'cloud-gaming-readiness-test' ),
            array( $this, 'checkbox_field_callback' ),
            'cgrt_settings',
            'cgrt_general',
            array(
                'label_for' => 'cgrt_show_shortcode',
                'desc'      => __( 'Display shortcode usage information.', 'cloud-gaming-readiness-test' ),
            )
        );

        add_settings_field(
            'cgrt_enable_page_mode',
            __( 'Enable Page Template', 'cloud-gaming-readiness-test' ),
            array( $this, 'checkbox_field_callback' ),
            'cgrt_settings',
            'cgrt_general',
            array(
                'label_for' => 'cgrt_enable_page_mode',
                'desc'      => __( 'Enable full-page template mode.', 'cloud-gaming-readiness-test' ),
            )
        );

        register_setting( 'cgrt_settings', 'cgrt_home_url', 'esc_url_raw' );
        register_setting( 'cgrt_settings', 'cgrt_show_shortcode', 'intval' );
        register_setting( 'cgrt_settings', 'cgrt_enable_page_mode', 'intval' );
    }

    /**
     * Register appearance settings.
     *
     * @since 1.0.0
     */
    private function register_appearance_settings() {
        add_settings_section(
            'cgrt_appearance',
            __( 'Appearance', 'cloud-gaming-readiness-test' ),
            array( $this, 'appearance_section_callback' ),
            'cgrt_settings'
        );

        add_settings_field(
            'cgrt_theme_color',
            __( 'Theme Color', 'cloud-gaming-readiness-test' ),
            array( $this, 'color_field_callback' ),
            'cgrt_settings',
            'cgrt_appearance',
            array(
                'label_for' => 'cgrt_theme_color',
                'desc'      => __( 'Primary theme color for the interface.', 'cloud-gaming-readiness-test' ),
            )
        );

        add_settings_field(
            'cgrt_default_mode',
            __( 'Default Theme Mode', 'cloud-gaming-readiness-test' ),
            array( $this, 'select_field_callback' ),
            'cgrt_settings',
            'cgrt_appearance',
            array(
                'label_for' => 'cgrt_default_mode',
                'options'   => array(
                    'dark'  => __( 'Dark Mode', 'cloud-gaming-readiness-test' ),
                    'light' => __( 'Light Mode', 'cloud-gaming-readiness-test' ),
                ),
                'desc'      => __( 'The default theme for the test interface.', 'cloud-gaming-readiness-test' ),
            )
        );

        add_settings_field(
            'cgrt_custom_css',
            __( 'Custom CSS', 'cloud-gaming-readiness-test' ),
            array( $this, 'textarea_field_callback' ),
            'cgrt_settings',
            'cgrt_appearance',
            array(
                'label_for' => 'cgrt_custom_css',
                'desc'      => __( 'Additional custom CSS for styling.', 'cloud-gaming-readiness-test' ),
            )
        );

        register_setting( 'cgrt_settings', 'cgrt_theme_color', 'sanitize_hex_color' );
        register_setting( 'cgrt_settings', 'cgrt_default_mode', array( $this, 'sanitize_theme_mode' ) );
        register_setting( 'cgrt_settings', 'cgrt_custom_css', 'wp_kses_post' );
    }

    /**
     * Register Cloudflare API settings.
     *
     * @since 1.0.0
     */
    private function register_cloudflare_settings() {
        add_settings_section(
            'cgrt_cloudflare',
            __( 'Cloudflare API Settings', 'cloud-gaming-readiness-test' ),
            array( $this, 'cloudflare_section_callback' ),
            'cgrt_settings'
        );

        add_settings_field(
            'cgrt_cloudflare_enabled',
            __( 'Enable Cloudflare Speed Test', 'cloud-gaming-readiness-test' ),
            array( $this, 'checkbox_field_callback' ),
            'cgrt_settings',
            'cgrt_cloudflare',
            array(
                'label_for' => 'cgrt_cloudflare_enabled',
                'desc'      => __( 'Use Cloudflare API for speed testing.', 'cloud-gaming-readiness-test' ),
            )
        );

        add_settings_field(
            'cgrt_cloudflare_api_key',
            __( 'Cloudflare API Key', 'cloud-gaming-readiness-test' ),
            array( $this, 'password_field_callback' ),
            'cgrt_settings',
            'cgrt_cloudflare',
            array(
                'label_for' => 'cgrt_cloudflare_api_key',
                'desc'      => __( 'Your Cloudflare API key.', 'cloud-gaming-readiness-test' ),
            )
        );

        add_settings_field(
            'cgrt_cloudflare_email',
            __( 'Cloudflare Email', 'cloud-gaming-readiness-test' ),
            array( $this, 'email_field_callback' ),
            'cgrt_settings',
            'cgrt_cloudflare',
            array(
                'label_for' => 'cgrt_cloudflare_email',
                'desc'      => __( 'Your Cloudflare account email.', 'cloud-gaming-readiness-test' ),
            )
        );

        register_setting( 'cgrt_settings', 'cgrt_cloudflare_enabled', 'intval' );
        register_setting( 'cgrt_settings', 'cgrt_cloudflare_api_key', 'sanitize_text_field' );
        register_setting( 'cgrt_settings', 'cgrt_cloudflare_email', 'sanitize_email' );
    }

    /**
     * Register results thresholds settings.
     *
     * @since 1.0.0
     */
    private function register_thresholds_settings() {
        add_settings_section(
            'cgrt_thresholds',
            __( 'Results Thresholds', 'cloud-gaming-readiness-test' ),
            array( $this, 'thresholds_section_callback' ),
            'cgrt_settings'
        );

        // Latency thresholds.
        add_settings_field(
            'cgrt_latency_thresholds',
            __( 'Latency Thresholds (ms)', 'cloud-gaming-readiness-test' ),
            array( $this, 'three_number_field_callback' ),
            'cgrt_settings',
            'cgrt_thresholds',
            array(
                'field1' => 'cgrt_latency_excellent',
                'field2' => 'cgrt_latency_good',
                'field3' => 'cgrt_latency_fair',
                'labels' => array(
                    __( 'Excellent', 'cloud-gaming-readiness-test' ),
                    __( 'Good', 'cloud-gaming-readiness-test' ),
                    __( 'Fair', 'cloud-gaming-readiness-test' ),
                ),
            )
        );

        // Jitter thresholds.
        add_settings_field(
            'cgrt_jitter_thresholds',
            __( 'Jitter Thresholds (ms)', 'cloud-gaming-readiness-test' ),
            array( $this, 'three_number_field_callback' ),
            'cgrt_settings',
            'cgrt_thresholds',
            array(
                'field1' => 'cgrt_jitter_excellent',
                'field2' => 'cgrt_jitter_good',
                'field3' => 'cgrt_jitter_fair',
                'labels' => array(
                    __( 'Excellent', 'cloud-gaming-readiness-test' ),
                    __( 'Good', 'cloud-gaming-readiness-test' ),
                    __( 'Fair', 'cloud-gaming-readiness-test' ),
                ),
            )
        );

        // Packet loss thresholds.
        add_settings_field(
            'cgrt_packet_loss_thresholds',
            __( 'Packet Loss Thresholds (%)', 'cloud-gaming-readiness-test' ),
            array( $this, 'two_number_field_callback' ),
            'cgrt_settings',
            'cgrt_thresholds',
            array(
                'field1' => 'cgrt_packet_loss_good',
                'field2' => 'cgrt_packet_loss_fair',
                'labels' => array(
                    __( 'Good', 'cloud-gaming-readiness-test' ),
                    __( 'Fair', 'cloud-gaming-readiness-test' ),
                ),
            )
        );

        register_setting( 'cgrt_settings', 'cgrt_latency_excellent', 'absint' );
        register_setting( 'cgrt_settings', 'cgrt_latency_good', 'absint' );
        register_setting( 'cgrt_settings', 'cgrt_latency_fair', 'absint' );
        register_setting( 'cgrt_settings', 'cgrt_jitter_excellent', 'absint' );
        register_setting( 'cgrt_settings', 'cgrt_jitter_good', 'absint' );
        register_setting( 'cgrt_settings', 'cgrt_jitter_fair', 'absint' );
        register_setting( 'cgrt_settings', 'cgrt_packet_loss_good', 'absint' );
        register_setting( 'cgrt_settings', 'cgrt_packet_loss_fair', 'absint' );
    }

    /**
     * Register test settings.
     *
     * @since 1.0.0
     */
    private function register_test_settings() {
        add_settings_section(
            'cgrt_test',
            __( 'Test Settings', 'cloud-gaming-readiness-test' ),
            array( $this, 'test_section_callback' ),
            'cgrt_settings'
        );

        add_settings_field(
            'cgrt_ping_count',
            __( 'Ping Count', 'cloud-gaming-readiness-test' ),
            array( $this, 'number_field_callback' ),
            'cgrt_settings',
            'cgrt_test',
            array(
                'label_for' => 'cgrt_ping_count',
                'desc'      => __( 'Number of ping tests to perform.', 'cloud-gaming-readiness-test' ),
                'min'       => 5,
                'max'       => 50,
            )
        );

        add_settings_field(
            'cgrt_test_timeout',
            __( 'Test Timeout (seconds)', 'cloud-gaming-readiness-test' ),
            array( $this, 'number_field_callback' ),
            'cgrt_settings',
            'cgrt_test',
            array(
                'label_for' => 'cgrt_test_timeout',
                'desc'      => __( 'Maximum time for each test.', 'cloud-gaming-readiness-test' ),
                'min'       => 10,
                'max'       => 60,
            )
        );

        add_settings_field(
            'cgrt_enable_advanced',
            __( 'Enable Advanced Tests', 'cloud-gaming-readiness-test' ),
            array( $this, 'checkbox_field_callback' ),
            'cgrt_settings',
            'cgrt_test',
            array(
                'label_for' => 'cgrt_enable_advanced',
                'desc'      => __( 'Enable additional network diagnostics.', 'cloud-gaming-readiness-test' ),
            )
        );

        register_setting( 'cgrt_settings', 'cgrt_ping_count', 'absint' );
        register_setting( 'cgrt_settings', 'cgrt_test_timeout', 'absint' );
        register_setting( 'cgrt_settings', 'cgrt_enable_advanced', 'intval' );
    }

    /**
     * Section callback: General.
     *
     * @since 1.0.0
     */
    public function general_section_callback() {
        echo '<p>' . esc_html__( 'Configure general plugin settings.', 'cloud-gaming-readiness-test' ) . '</p>';
    }

    /**
     * Section callback: Appearance.
     *
     * @since 1.0.0
     */
    public function appearance_section_callback() {
        echo '<p>' . esc_html__( 'Customize the appearance of the test interface.', 'cloud-gaming-readiness-test' ) . '</p>';
    }

    /**
     * Section callback: Cloudflare.
     *
     * @since 1.0.0
     */
    public function cloudflare_section_callback() {
        echo '<p>' . esc_html__( 'Configure Cloudflare API integration for speed testing.', 'cloud-gaming-readiness-test' ) . '</p>';
        echo '<p class="description">' . esc_html__( 'Get your API key from https://dash.cloudflare.com/profile/api-tokens', 'cloud-gaming-readiness-test' ) . '</p>';
    }

    /**
     * Section callback: Thresholds.
     *
     * @since 1.0.0
     */
    public function thresholds_section_callback() {
        echo '<p>' . esc_html__( 'Set the thresholds for interpreting test results.', 'cloud-gaming-readiness-test' ) . '</p>';
    }

    /**
     * Section callback: Test.
     *
     * @since 1.0.0
     */
    public function test_section_callback() {
        echo '<p>' . esc_html__( 'Configure test parameters and behavior.', 'cloud-gaming-readiness-test' ) . '</p>';
    }

    /**
     * Field callback: Text input.
     *
     * @since 1.0.0
     * @param array $args Field arguments.
     */
    public function text_field_callback( $args ) {
        $value = get_option( $args['label_for'], '' );
        ?>
        <input type="text"
               id="<?php echo esc_attr( $args['label_for'] ); ?>"
               name="<?php echo esc_attr( $args['label_for'] ); ?>"
               value="<?php echo esc_attr( $value ); ?>"
               class="regular-text">
        <?php if ( isset( $args['desc'] ) ) : ?>
            <p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
        <?php endif;
    }

    /**
     * Field callback: Email input.
     *
     * @since 1.0.0
     * @param array $args Field arguments.
     */
    public function email_field_callback( $args ) {
        $value = get_option( $args['label_for'], '' );
        ?>
        <input type="email"
               id="<?php echo esc_attr( $args['label_for'] ); ?>"
               name="<?php echo esc_attr( $args['label_for'] ); ?>"
               value="<?php echo esc_attr( $value ); ?>"
               class="regular-text">
        <?php if ( isset( $args['desc'] ) ) : ?>
            <p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
        <?php endif;
    }

    /**
     * Field callback: Password input.
     *
     * @since 1.0.0
     * @param array $args Field arguments.
     */
    public function password_field_callback( $args ) {
        $value = get_option( $args['label_for'], '' );
        ?>
        <input type="password"
               id="<?php echo esc_attr( $args['label_for'] ); ?>"
               name="<?php echo esc_attr( $args['label_for'] ); ?>"
               value="<?php echo esc_attr( $value ); ?>"
               class="regular-text">
        <?php if ( isset( $args['desc'] ) ) : ?>
            <p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
        <?php endif;
    }

    /**
     * Field callback: Number input.
     *
     * @since 1.0.0
     * @param array $args Field arguments.
     */
    public function number_field_callback( $args ) {
        $value = get_option( $args['label_for'], '' );
        $min = isset( $args['min'] ) ? 'min="' . intval( $args['min'] ) . '"' : '';
        $max = isset( $args['max'] ) ? 'max="' . intval( $args['max'] ) . '"' : '';
        ?>
        <input type="number"
               id="<?php echo esc_attr( $args['label_for'] ); ?>"
               name="<?php echo esc_attr( $args['label_for'] ); ?>"
               value="<?php echo esc_attr( $value ); ?>"
               class="small-text"
               <?php echo $min; ?>
               <?php echo $max; ?>>
        <?php if ( isset( $args['desc'] ) ) : ?>
            <p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
        <?php endif;
    }

    /**
     * Field callback: Color input.
     *
     * @since 1.0.0
     * @param array $args Field arguments.
     */
    public function color_field_callback( $args ) {
        $value = get_option( $args['label_for'], '' );
        ?>
        <input type="color"
               id="<?php echo esc_attr( $args['label_for'] ); ?>"
               name="<?php echo esc_attr( $args['label_for'] ); ?>"
               value="<?php echo esc_attr( $value ); ?>"
               class="cgrt-color-picker">
        <?php if ( isset( $args['desc'] ) ) : ?>
            <p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
        <?php endif;
    }

    /**
     * Field callback: Select dropdown.
     *
     * @since 1.0.0
     * @param array $args Field arguments.
     */
    public function select_field_callback( $args ) {
        $value = get_option( $args['label_for'], '' );
        ?>
        <select id="<?php echo esc_attr( $args['label_for'] ); ?>"
                name="<?php echo esc_attr( $args['label_for'] ); ?>">
            <?php foreach ( $args['options'] as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>>
                    <?php echo esc_html( $label ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ( isset( $args['desc'] ) ) : ?>
            <p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
        <?php endif;
    }

    /**
     * Field callback: Checkbox.
     *
     * @since 1.0.0
     * @param array $args Field arguments.
     */
    public function checkbox_field_callback( $args ) {
        $value = get_option( $args['label_for'], 0 );
        ?>
        <label>
            <input type="checkbox"
                   id="<?php echo esc_attr( $args['label_for'] ); ?>"
                   name="<?php echo esc_attr( $args['label_for'] ); ?>"
                   value="1" <?php checked( $value, 1 ); ?>>
            <?php if ( isset( $args['desc'] ) ) : ?>
                <?php echo esc_html( $args['desc'] ); ?>
            <?php endif; ?>
        </label>
        <?php
    }

    /**
     * Field callback: Textarea.
     *
     * @since 1.0.0
     * @param array $args Field arguments.
     */
    public function textarea_field_callback( $args ) {
        $value = get_option( $args['label_for'], '' );
        ?>
        <textarea id="<?php echo esc_attr( $args['label_for'] ); ?>"
                  name="<?php echo esc_attr( $args['label_for'] ); ?>"
                  class="large-text code"
                  rows="10"><?php echo esc_textarea( $value ); ?></textarea>
        <?php if ( isset( $args['desc'] ) ) : ?>
            <p class="description"><?php echo esc_html( $args['desc'] ); ?></p>
        <?php endif;
    }

    /**
     * Field callback: Three number inputs in a row.
     *
     * @since 1.0.0
     * @param array $args Field arguments.
     */
    public function three_number_field_callback( $args ) {
        $value1 = get_option( $args['field1'], '' );
        $value2 = get_option( $args['field2'], '' );
        $value3 = get_option( $args['field3'], '' );
        ?>
        <div class="cgrt-three-number-inputs">
            <?php foreach ( $args['labels'] as $index => $label ) : ?>
                <div class="cgrt-input-group">
                    <label for="<?php echo esc_attr( ${'field' . ($index + 1)} ); ?>">
                        <?php echo esc_html( $label ); ?>
                    </label>
                    <input type="number"
                           id="<?php echo esc_attr( $args['field' . ($index + 1)] ); ?>"
                           name="<?php echo esc_attr( $args['field' . ($index + 1)] ); ?>"
                           value="<?php echo esc_attr( ${'value' . ($index + 1)} ); ?>"
                           class="small-text"
                           min="1">
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Field callback: Two number inputs in a row.
     *
     * @since 1.0.0
     * @param array $args Field arguments.
     */
    public function two_number_field_callback( $args ) {
        $value1 = get_option( $args['field1'], '' );
        $value2 = get_option( $args['field2'], '' );
        ?>
        <div class="cgrt-two-number-inputs">
            <?php foreach ( $args['labels'] as $index => $label ) : ?>
                <div class="cgrt-input-group">
                    <label for="<?php echo esc_attr( $args['field' . ($index + 1)] ); ?>">
                        <?php echo esc_html( $label ); ?>
                    </label>
                    <input type="number"
                           id="<?php echo esc_attr( $args['field' . ($index + 1)] ); ?>"
                           name="<?php echo esc_attr( $args['field' . ($index + 1)] ); ?>"
                           value="<?php echo esc_attr( ${'value' . ($index + 1)} ); ?>"
                           class="small-text"
                           min="0"
                           max="100">
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Sanitize theme mode value.
     *
     * @since 1.0.0
     * @param string $value The value to sanitize.
     * @return string Sanitized value.
     */
    public function sanitize_theme_mode( $value ) {
        return in_array( $value, array( 'dark', 'light' ) ) ? $value : 'dark';
    }
}
