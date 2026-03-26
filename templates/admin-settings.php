<?php
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('cga_settings', array());
$group_by_availability = get_option('cga_group_by_availability', 'no');
$default_columns = get_option('cga_default_columns', 3);

$defaults = array(
    'primary_color' => '#6366f1',
    'secondary_color' => '#8b5cf6',
    'card_background' => 'rgba(255, 255, 255, 0.9)',
    'card_background_dark' => 'rgba(30, 30, 30, 0.9)',
    'button_style' => 'filled',
    'border_radius' => 12,
    'card_spacing' => 16,
    'show_price' => true,
    'show_tier' => true,
    'show_notes' => true,
    'theme_mode' => 'light',
    'glassmorphism' => true,
    'hover_effect' => 'lift',
    'custom_css' => '',
);

$settings = wp_parse_args($settings, $defaults);
?>

<div class="wrap cga-wrap">
    <div class="cga-page-header">
        <h1><?php _e('Settings', 'cloud-games-availability-v2'); ?></h1>
    </div>

    <form id="cga-settings-form">
        <div class="cga-settings-tabs">
            <div class="cga-settings-nav">
                <button type="button" class="cga-tab-btn cga-tab-active" data-tab="appearance">
                    <span class="dashicons dashicons-admin-appearance"></span>
                    <?php _e('Appearance', 'cloud-games-availability-v2'); ?>
                </button>
                <button type="button" class="cga-tab-btn" data-tab="display">
                    <span class="dashicons dashicons-schedule"></span>
                    <?php _e('Display', 'cloud-games-availability-v2'); ?>
                </button>
                <button type="button" class="cga-tab-btn" data-tab="elements">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php _e('Elements', 'cloud-games-availability-v2'); ?>
                </button>
                <button type="button" class="cga-tab-btn" data-tab="advanced">
                    <span class="dashicons dashicons-admin-tools"></span>
                    <?php _e('Advanced', 'cloud-games-availability-v2'); ?>
                </button>
            </div>

            <div class="cga-settings-content">
                <!-- Appearance Tab -->
                <div class="cga-tab-panel cga-tab-active" id="tab-appearance">
                    <h2><?php _e('Colors & Styling', 'cloud-games-availability-v2'); ?></h2>
                    
                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-primary-color"><?php _e('Primary Color', 'cloud-games-availability-v2'); ?></label>
                            <input type="color" name="primary_color" id="cga-primary-color" value="<?php echo esc_attr($settings['primary_color']); ?>">
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-secondary-color"><?php _e('Secondary Color', 'cloud-games-availability-v2'); ?></label>
                            <input type="color" name="secondary_color" id="cga-secondary-color" value="<?php echo esc_attr($settings['secondary_color']); ?>">
                        </div>
                    </div>

                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-card-background"><?php _e('Card Background (Light)', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="card_background" id="cga-card-background" value="<?php echo esc_attr($settings['card_background']); ?>" placeholder="rgba(255, 255, 255, 0.9)">
                            <small class="cga-help-text"><?php _e('Use rgba for transparency', 'cloud-games-availability-v2'); ?></small>
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-card-background-dark"><?php _e('Card Background (Dark)', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="card_background_dark" id="cga-card-background-dark" value="<?php echo esc_attr($settings['card_background_dark']); ?>" placeholder="rgba(30, 30, 30, 0.9)">
                        </div>
                    </div>

                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-border-radius"><?php _e('Border Radius (px)', 'cloud-games-availability-v2'); ?></label>
                            <input type="number" name="border_radius" id="cga-border-radius" value="<?php echo esc_attr($settings['border_radius']); ?>" min="0" max="50">
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-card-spacing"><?php _e('Card Spacing (px)', 'cloud-games-availability-v2'); ?></label>
                            <input type="number" name="card_spacing" id="cga-card-spacing" value="<?php echo esc_attr($settings['card_spacing']); ?>" min="0" max="100">
                        </div>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-button-style"><?php _e('Button Style', 'cloud-games-availability-v2'); ?></label>
                        <select name="button_style" id="cga-button-style">
                            <option value="filled" <?php selected($settings['button_style'], 'filled'); ?>><?php _e('Filled', 'cloud-games-availability-v2'); ?></option>
                            <option value="outline" <?php selected($settings['button_style'], 'outline'); ?>><?php _e('Outline', 'cloud-games-availability-v2'); ?></option>
                            <option value="ghost" <?php selected($settings['button_style'], 'ghost'); ?>><?php _e('Ghost', 'cloud-games-availability-v2'); ?></option>
                        </select>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-hover-effect"><?php _e('Hover Effect', 'cloud-games-availability-v2'); ?></label>
                        <select name="hover_effect" id="cga-hover-effect">
                            <option value="lift" <?php selected($settings['hover_effect'], 'lift'); ?>><?php _e('Lift Up', 'cloud-games-availability-v2'); ?></option>
                            <option value="scale" <?php selected($settings['hover_effect'], 'scale'); ?>><?php _e('Scale', 'cloud-games-availability-v2'); ?></option>
                            <option value="glow" <?php selected($settings['hover_effect'], 'glow'); ?>><?php _e('Glow', 'cloud-games-availability-v2'); ?></option>
                            <option value="none" <?php selected($settings['hover_effect'], 'none'); ?>><?php _e('None', 'cloud-games-availability-v2'); ?></option>
                        </select>
                    </div>
                </div>

                <!-- Display Tab -->
                <div class="cga-tab-panel" id="tab-display">
                    <h2><?php _e('Display Settings', 'cloud-games-availability-v2'); ?></h2>
                    
                    <div class="cga-form-group">
                        <label for="cga-theme-mode"><?php _e('Theme Mode', 'cloud-games-availability-v2'); ?></label>
                        <select name="theme_mode" id="cga-theme-mode">
                            <option value="light" <?php selected($settings['theme_mode'], 'light'); ?>><?php _e('Light', 'cloud-games-availability-v2'); ?></option>
                            <option value="dark" <?php selected($settings['theme_mode'], 'dark'); ?>><?php _e('Dark', 'cloud-games-availability-v2'); ?></option>
                            <option value="auto" <?php selected($settings['theme_mode'], 'auto'); ?>><?php _e('Auto (System)', 'cloud-games-availability-v2'); ?></option>
                        </select>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-glassmorphism">
                            <input type="checkbox" name="glassmorphism" id="cga-glassmorphism" value="1" <?php checked($settings['glassmorphism'], 1); ?>>
                            <?php _e('Enable Glassmorphism Effect', 'cloud-games-availability-v2'); ?>
                        </label>
                        <small class="cga-help-text"><?php _e('Adds blur and transparency to cards for a modern look', 'cloud-games-availability-v2'); ?></small>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-group-by-availability">
                            <input type="checkbox" name="group_by_availability" id="cga-group-by-availability" value="yes" <?php checked($group_by_availability, 'yes'); ?>>
                            <?php _e('Group Platforms by Availability', 'cloud-games-availability-v2'); ?>
                        </label>
                        <small class="cga-help-text"><?php _e('Show available and unavailable platforms in separate sections', 'cloud-games-availability-v2'); ?></small>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-default-columns"><?php _e('Default Number of Columns', 'cloud-games-availability-v2'); ?></label>
                        <select name="default_columns" id="cga-default-columns">
                            <option value="1" <?php selected($default_columns, 1); ?>>1</option>
                            <option value="2" <?php selected($default_columns, 2); ?>>2</option>
                            <option value="3" <?php selected($default_columns, 3); ?>>3</option>
                            <option value="4" <?php selected($default_columns, 4); ?>>4</option>
                            <option value="6" <?php selected($default_columns, 6); ?>>6</option>
                        </select>
                    </div>
                </div>

                <!-- Elements Tab -->
                <div class="cga-tab-panel" id="tab-elements">
                    <h2><?php _e('UI Elements', 'cloud-games-availability-v2'); ?></h2>
                    
                    <div class="cga-form-group">
                        <label for="cga-show-price">
                            <input type="checkbox" name="show_price" id="cga-show-price" value="1" <?php checked($settings['show_price'], 1); ?>>
                            <?php _e('Show Price', 'cloud-games-availability-v2'); ?>
                        </label>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-show-tier">
                            <input type="checkbox" name="show_tier" id="cga-show-tier" value="1" <?php checked($settings['show_tier'], 1); ?>>
                            <?php _e('Show Tier/Plan Name', 'cloud-games-availability-v2'); ?>
                        </label>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-show-notes">
                            <input type="checkbox" name="show_notes" id="cga-show-notes" value="1" <?php checked($settings['show_notes'], 1); ?>>
                            <?php _e('Show Notes', 'cloud-games-availability-v2'); ?>
                        </label>
                    </div>
                </div>

                <!-- Advanced Tab -->
                <div class="cga-tab-panel" id="tab-advanced">
                    <h2><?php _e('Advanced', 'cloud-games-availability-v2'); ?></h2>
                    
                    <div class="cga-form-group">
                        <label for="cga-custom-css"><?php _e('Custom CSS', 'cloud-games-availability-v2'); ?></label>
                        <textarea name="custom_css" id="cga-custom-css" rows="15" placeholder="/* Add custom CSS here */"><?php echo esc_textarea($settings['custom_css']); ?></textarea>
                        <small class="cga-help-text"><?php _e('Add custom CSS to override default styles', 'cloud-games-availability-v2'); ?></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="cga-settings-footer">
            <button type="submit" class="button button-primary">
                <span class="dashicons dashicons-saved"></span>
                <?php _e('Save Settings', 'cloud-games-availability-v2'); ?>
            </button>
        </div>
    </form>
</div>
