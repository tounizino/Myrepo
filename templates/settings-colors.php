<?php
/**
 * Colors Settings Tab
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="unpc-settings-section">
    <h3><span class="dashicons dashicons-art"></span> <?php esc_html_e('Color Customization', 'ultimate-nat-port-checker'); ?></h3>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="unpc_color_primary"><?php esc_html_e('Primary Accent Color', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <input type="text" name="unpc_color_primary" id="unpc_color_primary" class="unpc-color-picker" value="<?php echo esc_attr($options['unpc_color_primary']); ?>" />
                <p class="description"><?php esc_html_e('Main accent color for headers and highlights.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="unpc_color_success"><?php esc_html_e('Success Color (NAT Type 1)', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <input type="text" name="unpc_color_success" id="unpc_color_success" class="unpc-color-picker" value="<?php echo esc_attr($options['unpc_color_success']); ?>" />
                <p class="description"><?php esc_html_e('Color for open NAT and successful port checks.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="unpc_color_warning"><?php esc_html_e('Warning Color (NAT Type 2)', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <input type="text" name="unpc_color_warning" id="unpc_color_warning" class="unpc-color-picker" value="<?php echo esc_attr($options['unpc_color_warning']); ?>" />
                <p class="description"><?php esc_html_e('Color for moderate NAT type.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="unpc_color_error"><?php esc_html_e('Error Color (NAT Type 3)', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <input type="text" name="unpc_color_error" id="unpc_color_error" class="unpc-color-picker" value="<?php echo esc_attr($options['unpc_color_error']); ?>" />
                <p class="description"><?php esc_html_e('Color for strict NAT and closed ports.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="unpc_color_info"><?php esc_html_e('Info Color', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <input type="text" name="unpc_color_info" id="unpc_color_info" class="unpc-color-picker" value="<?php echo esc_attr($options['unpc_color_info']); ?>" />
                <p class="description"><?php esc_html_e('Color for informational elements.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
    </table>
</div>
