<?php
/**
 * General Settings Tab
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="unpc-settings-section">
    <h3><span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e('General Configuration', 'ultimate-nat-port-checker'); ?></h3>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="unpc_default_theme"><?php esc_html_e('Default Theme', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <select name="unpc_default_theme" id="unpc_default_theme" class="regular-text">
                    <option value="dark" <?php selected($options['unpc_default_theme'], 'dark'); ?>><?php esc_html_e('Dark Mode (2026 Futuristic)', 'ultimate-nat-port-checker'); ?></option>
                    <option value="light" <?php selected($options['unpc_default_theme'], 'light'); ?>><?php esc_html_e('Light Mode', 'ultimate-nat-port-checker'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Choose the default theme for the tool.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="unpc_font_size"><?php esc_html_e('Font Size Preset', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <select name="unpc_font_size" id="unpc_font_size" class="regular-text">
                    <option value="small" <?php selected($options['unpc_font_size'], 'small'); ?>><?php esc_html_e('Small (0.75rem)', 'ultimate-nat-port-checker'); ?></option>
                    <option value="medium" <?php selected($options['unpc_font_size'], 'medium'); ?>><?php esc_html_e('Medium (0.875rem)', 'ultimate-nat-port-checker'); ?></option>
                    <option value="large" <?php selected($options['unpc_font_size'], 'large'); ?>><?php esc_html_e('Large (1rem)', 'ultimate-nat-port-checker'); ?></option>
                </select>
                <p class="description"><?php esc_html_e('Choose your preferred font size preset for optimal readability.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="unpc_container_width"><?php esc_html_e('Container Width (px)', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <input type="number" name="unpc_container_width" id="unpc_container_width" class="regular-text" value="<?php echo esc_attr($options['unpc_container_width']); ?>" min="800" max="2000" step="50" />
                <p class="description"><?php esc_html_e('Maximum width of the tool container (800-2000px). Recommended: 1200px', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <?php esc_html_e('Enable Animations', 'ultimate-nat-port-checker'); ?>
            </th>
            <td>
                <label>
                    <input type="checkbox" name="unpc_enable_animations" value="1" <?php checked($options['unpc_enable_animations'], '1'); ?> />
                    <?php esc_html_e('Enable futuristic animations and transitions', 'ultimate-nat-port-checker'); ?>
                </label>
                <p class="description"><?php esc_html_e('Disable for better performance on slower devices.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
    </table>
</div>
