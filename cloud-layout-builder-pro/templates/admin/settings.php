<?php
/**
 * Settings Template
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap clbp-admin-wrap">
    <div class="clbp-admin-header">
        <h1><?php esc_html_e( 'Design Customization', 'cloud-layout-builder-pro' ); ?></h1>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields( 'clbp_settings_group' ); ?>

        <div class="clbp-settings-grid">
            <div class="clbp-settings-card">
                <h3><?php esc_html_e( 'Color Palette', 'cloud-layout-builder-pro' ); ?></h3>
                <p><?php esc_html_e( 'Adjust your primary palette to match your brand identity.', 'cloud-layout-builder-pro' ); ?></p>
                <label><?php esc_html_e( 'Primary Color', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" class="clbp-color-field" name="clbp_settings[primary_color]" value="<?php echo esc_attr( $settings['primary_color'] ); ?>">

                <label><?php esc_html_e( 'Secondary Color', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" class="clbp-color-field" name="clbp_settings[secondary_color]" value="<?php echo esc_attr( $settings['secondary_color'] ); ?>">

                <label><?php esc_html_e( 'Background Color', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" class="clbp-color-field" name="clbp_settings[background_color]" value="<?php echo esc_attr( $settings['background_color'] ); ?>">

                <label><?php esc_html_e( 'Text Color', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" class="clbp-color-field" name="clbp_settings[text_color]" value="<?php echo esc_attr( $settings['text_color'] ); ?>">
            </div>

            <div class="clbp-settings-card">
                <h3><?php esc_html_e( 'Typography', 'cloud-layout-builder-pro' ); ?></h3>
                <label><?php esc_html_e( 'Font Family', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" name="clbp_settings[font_family]" value="<?php echo esc_attr( $settings['font_family'] ); ?>">

                <label><?php esc_html_e( 'Base Font Size', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" name="clbp_settings[font_size_base]" value="<?php echo esc_attr( $settings['font_size_base'] ); ?>">

                <label><?php esc_html_e( 'Heading Font Size', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" name="clbp_settings[font_size_title]" value="<?php echo esc_attr( $settings['font_size_title'] ); ?>">
            </div>

            <div class="clbp-settings-card">
                <h3><?php esc_html_e( 'Layout', 'cloud-layout-builder-pro' ); ?></h3>
                <label><?php esc_html_e( 'Global Spacing', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" name="clbp_settings[spacing]" value="<?php echo esc_attr( $settings['spacing'] ); ?>">

                <label><?php esc_html_e( 'Padding', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" name="clbp_settings[padding]" value="<?php echo esc_attr( $settings['padding'] ); ?>">

                <label><?php esc_html_e( 'Border Radius', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" name="clbp_settings[border_radius]" value="<?php echo esc_attr( $settings['border_radius'] ); ?>">

                <label><?php esc_html_e( 'Block Width', 'cloud-layout-builder-pro' ); ?></label>
                <select name="clbp_settings[block_width]">
                    <option value="full" <?php selected( $settings['block_width'], 'full' ); ?>><?php esc_html_e( 'Full Width', 'cloud-layout-builder-pro' ); ?></option>
                    <option value="boxed" <?php selected( $settings['block_width'], 'boxed' ); ?>><?php esc_html_e( 'Boxed', 'cloud-layout-builder-pro' ); ?></option>
                    <option value="custom" <?php selected( $settings['block_width'], 'custom' ); ?>><?php esc_html_e( 'Custom', 'cloud-layout-builder-pro' ); ?></option>
                </select>

                <label><?php esc_html_e( 'Custom Width', 'cloud-layout-builder-pro' ); ?></label>
                <input type="text" name="clbp_settings[custom_width]" value="<?php echo esc_attr( $settings['custom_width'] ); ?>">

                <label><?php esc_html_e( 'Layout Style', 'cloud-layout-builder-pro' ); ?></label>
                <select name="clbp_settings[layout_style]">
                    <option value="classic" <?php selected( $settings['layout_style'], 'classic' ); ?>><?php esc_html_e( 'Classic Grid', 'cloud-layout-builder-pro' ); ?></option>
                    <option value="magazine" <?php selected( $settings['layout_style'], 'magazine' ); ?>><?php esc_html_e( 'Magazine', 'cloud-layout-builder-pro' ); ?></option>
                    <option value="modern" <?php selected( $settings['layout_style'], 'modern' ); ?>><?php esc_html_e( 'Modern Blog', 'cloud-layout-builder-pro' ); ?></option>
                </select>
            </div>

            <div class="clbp-settings-card">
                <h3><?php esc_html_e( 'Advanced', 'cloud-layout-builder-pro' ); ?></h3>
                <div class="clbp-toggle">
                    <input type="checkbox" name="clbp_settings[dark_mode]" value="1" <?php checked( $settings['dark_mode'], true ); ?>>
                    <span><?php esc_html_e( 'Enable dark mode toggle', 'cloud-layout-builder-pro' ); ?></span>
                </div>

                <div class="clbp-toggle">
                    <input type="checkbox" name="clbp_settings[enable_shadows]" value="1" <?php checked( $settings['enable_shadows'], true ); ?>>
                    <span><?php esc_html_e( 'Enable soft shadows', 'cloud-layout-builder-pro' ); ?></span>
                </div>

                <div class="clbp-toggle">
                    <input type="checkbox" name="clbp_settings[lazy_load]" value="1" <?php checked( $settings['lazy_load'], true ); ?>>
                    <span><?php esc_html_e( 'Lazy-load images', 'cloud-layout-builder-pro' ); ?></span>
                </div>

                <div class="clbp-toggle">
                    <input type="checkbox" name="clbp_settings[enable_schema]" value="1" <?php checked( $settings['enable_schema'], true ); ?>>
                    <span><?php esc_html_e( 'Enable schema markup', 'cloud-layout-builder-pro' ); ?></span>
                </div>
            </div>
        </div>

        <div class="clbp-settings-card" style="margin-top: 24px;">
            <h3><?php esc_html_e( 'Custom CSS', 'cloud-layout-builder-pro' ); ?></h3>
            <textarea name="clbp_settings[custom_css]" placeholder="/* <?php esc_attr_e( 'Add your custom CSS', 'cloud-layout-builder-pro' ); ?> */"><?php echo esc_textarea( $settings['custom_css'] ); ?></textarea>
        </div>

        <div class="clbp-settings-card" style="margin-top: 24px;">
            <h3><?php esc_html_e( 'Custom JS', 'cloud-layout-builder-pro' ); ?></h3>
            <textarea name="clbp_settings[custom_js]" placeholder="// <?php esc_attr_e( 'Add custom JavaScript (executed in footer)', 'cloud-layout-builder-pro' ); ?>"><?php echo esc_textarea( $settings['custom_js'] ); ?></textarea>
        </div>

        <?php submit_button( __( 'Save Settings', 'cloud-layout-builder-pro' ) ); ?>
    </form>
</div>
