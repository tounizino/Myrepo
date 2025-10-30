<?php
/**
 * Cloud Layout Builder Pro - Customizer Integration
 *
 * @package CloudLayoutBuilderPro\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class CLBP_Customizer
 */
class CLBP_Customizer {

    /**
     * Instance
     *
     * @var CLBP_Customizer
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return CLBP_Customizer
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
    public function __construct() {
        add_action( 'customize_register', array( $this, 'register_customizer_settings' ) );
        add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_preview_assets' ) );
    }

    /**
     * Register customizer settings
     *
     * @param WP_Customize_Manager $wp_customize Customizer instance.
     */
    public function register_customizer_settings( $wp_customize ) {
        $wp_customize->add_panel(
            'clbp_panel',
            array(
                'title'       => __( 'Cloud Layout Builder', 'cloud-layout-builder-pro' ),
                'priority'    => 160,
                'description' => __( 'Customize the global look & feel for your layout blocks.', 'cloud-layout-builder-pro' ),
            )
        );

        $this->add_general_section( $wp_customize );
        $this->add_typography_section( $wp_customize );
        $this->add_layout_section( $wp_customize );
    }

    /**
     * Add general section
     *
     * @param WP_Customize_Manager $wp_customize Customizer instance.
     */
    private function add_general_section( $wp_customize ) {
        $wp_customize->add_section(
            'clbp_general_section',
            array(
                'title'    => __( 'Colors & Appearance', 'cloud-layout-builder-pro' ),
                'panel'    => 'clbp_panel',
                'priority' => 10,
            )
        );

        $colors = array(
            'primary_color'   => __( 'Primary Color', 'cloud-layout-builder-pro' ),
            'secondary_color' => __( 'Secondary Color', 'cloud-layout-builder-pro' ),
            'background_color'=> __( 'Background Color', 'cloud-layout-builder-pro' ),
            'text_color'      => __( 'Text Color', 'cloud-layout-builder-pro' ),
        );

        foreach ( $colors as $key => $label ) {
            $wp_customize->add_setting(
                'clbp_settings[' . $key . ']',
                array(
                    'default'           => CLBP_Settings::get( $key ),
                    'type'              => 'option',
                    'sanitize_callback' => 'sanitize_hex_color',
                )
            );

            $wp_customize->add_control(
                new WP_Customize_Color_Control(
                    $wp_customize,
                    'clbp_settings[' . $key . ']',
                    array(
                        'label'   => $label,
                        'section' => 'clbp_general_section',
                    )
                )
            );
        }

        $wp_customize->add_setting(
            'clbp_settings[dark_mode]',
            array(
                'default'           => CLBP_Settings::get( 'dark_mode' ),
                'type'              => 'option',
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            )
        );

        $wp_customize->add_control(
            'clbp_settings[dark_mode]',
            array(
                'type'    => 'checkbox',
                'label'   => __( 'Enable Dark Mode Toggle', 'cloud-layout-builder-pro' ),
                'section' => 'clbp_general_section',
            )
        );

        $wp_customize->add_setting(
            'clbp_settings[enable_shadows]',
            array(
                'default'           => CLBP_Settings::get( 'enable_shadows' ),
                'type'              => 'option',
                'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
            )
        );

        $wp_customize->add_control(
            'clbp_settings[enable_shadows]',
            array(
                'type'    => 'checkbox',
                'label'   => __( 'Enable Soft Shadows', 'cloud-layout-builder-pro' ),
                'section' => 'clbp_general_section',
            )
        );
    }

    /**
     * Add typography section
     *
     * @param WP_Customize_Manager $wp_customize Customizer instance.
     */
    private function add_typography_section( $wp_customize ) {
        $wp_customize->add_section(
            'clbp_typography_section',
            array(
                'title'    => __( 'Typography', 'cloud-layout-builder-pro' ),
                'panel'    => 'clbp_panel',
                'priority' => 20,
            )
        );

        $wp_customize->add_setting(
            'clbp_settings[font_family]',
            array(
                'default'           => CLBP_Settings::get( 'font_family' ),
                'type'              => 'option',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control(
            'clbp_settings[font_family]',
            array(
                'label'       => __( 'Primary Font Family', 'cloud-layout-builder-pro' ),
                'section'     => 'clbp_typography_section',
                'type'        => 'text',
                'description' => __( 'Use a comma-separated list (e.g. "Inter, system-ui, sans-serif").', 'cloud-layout-builder-pro' ),
            )
        );

        $wp_customize->add_setting(
            'clbp_settings[font_size_base]',
            array(
                'default'           => CLBP_Settings::get( 'font_size_base' ),
                'type'              => 'option',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control(
            'clbp_settings[font_size_base]',
            array(
                'label'   => __( 'Body Font Size', 'cloud-layout-builder-pro' ),
                'section' => 'clbp_typography_section',
                'type'    => 'text',
            )
        );

        $wp_customize->add_setting(
            'clbp_settings[font_size_title]',
            array(
                'default'           => CLBP_Settings::get( 'font_size_title' ),
                'type'              => 'option',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control(
            'clbp_settings[font_size_title]',
            array(
                'label'   => __( 'Heading Font Size', 'cloud-layout-builder-pro' ),
                'section' => 'clbp_typography_section',
                'type'    => 'text',
            )
        );
    }

    /**
     * Add layout section
     *
     * @param WP_Customize_Manager $wp_customize Customizer instance.
     */
    private function add_layout_section( $wp_customize ) {
        $wp_customize->add_section(
            'clbp_layout_section',
            array(
                'title'    => __( 'Layout & Spacing', 'cloud-layout-builder-pro' ),
                'panel'    => 'clbp_panel',
                'priority' => 30,
            )
        );

        $wp_customize->add_setting(
            'clbp_settings[block_width]',
            array(
                'default'           => CLBP_Settings::get( 'block_width' ),
                'type'              => 'option',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control(
            'clbp_settings[block_width]',
            array(
                'label'   => __( 'Block Width', 'cloud-layout-builder-pro' ),
                'section' => 'clbp_layout_section',
                'type'    => 'select',
                'choices' => array(
                    'full'   => __( 'Full Width', 'cloud-layout-builder-pro' ),
                    'boxed'  => __( 'Boxed', 'cloud-layout-builder-pro' ),
                    'custom' => __( 'Custom Width', 'cloud-layout-builder-pro' ),
                ),
            )
        );

        $wp_customize->add_setting(
            'clbp_settings[custom_width]',
            array(
                'default'           => CLBP_Settings::get( 'custom_width' ),
                'type'              => 'option',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control(
            'clbp_settings[custom_width]',
            array(
                'label'       => __( 'Custom Width', 'cloud-layout-builder-pro' ),
                'section'     => 'clbp_layout_section',
                'type'        => 'text',
                'description' => __( 'Applies when "Custom Width" is selected. Example: 1200px', 'cloud-layout-builder-pro' ),
            )
        );

        $wp_customize->add_setting(
            'clbp_settings[spacing]',
            array(
                'default'           => CLBP_Settings::get( 'spacing' ),
                'type'              => 'option',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control(
            'clbp_settings[spacing]',
            array(
                'label'   => __( 'Global Spacing', 'cloud-layout-builder-pro' ),
                'section' => 'clbp_layout_section',
                'type'    => 'text',
            )
        );

        $wp_customize->add_setting(
            'clbp_settings[border_radius]',
            array(
                'default'           => CLBP_Settings::get( 'border_radius' ),
                'type'              => 'option',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control(
            'clbp_settings[border_radius]',
            array(
                'label'   => __( 'Border Radius', 'cloud-layout-builder-pro' ),
                'section' => 'clbp_layout_section',
                'type'    => 'text',
            )
        );

        $wp_customize->add_setting(
            'clbp_settings[layout_style]',
            array(
                'default'           => CLBP_Settings::get( 'layout_style' ),
                'type'              => 'option',
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

        $wp_customize->add_control(
            'clbp_settings[layout_style]',
            array(
                'label'   => __( 'Layout Style', 'cloud-layout-builder-pro' ),
                'section' => 'clbp_layout_section',
                'type'    => 'select',
                'choices' => array(
                    'classic'  => __( 'Classic Grid', 'cloud-layout-builder-pro' ),
                    'magazine' => __( 'Magazine', 'cloud-layout-builder-pro' ),
                    'modern'   => __( 'Modern Blog', 'cloud-layout-builder-pro' ),
                ),
            )
        );
    }

    /**
     * Sanitize checkbox value
     *
     * @param mixed $value Value.
     *
     * @return bool
     */
    public function sanitize_checkbox( $value ) {
        return (bool) $value;
    }

    /**
     * Enqueue customizer preview assets
     */
    public function enqueue_customizer_preview_assets() {
        wp_enqueue_script(
            'clbp-customizer-preview',
            CLBP_PLUGIN_URL . 'assets/js/customizer-preview.js',
            array( 'customize-preview' ),
            CLBP_VERSION,
            true
        );
    }
}
