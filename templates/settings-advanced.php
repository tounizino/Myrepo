<?php
/**
 * Advanced Settings Tab
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="unpc-settings-section">
    <h3><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e('Advanced Controls', 'ultimate-nat-port-checker'); ?></h3>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="unpc_custom_css"><?php esc_html_e('Custom CSS', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <textarea name="unpc_custom_css" id="unpc_custom_css" class="large-text code" rows="6"><?php echo esc_textarea($options['unpc_custom_css']); ?></textarea>
                <p class="description"><?php esc_html_e('Add additional CSS to adjust the design beyond preset options.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="unpc_custom_js"><?php esc_html_e('Custom JavaScript', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <textarea name="unpc_custom_js" id="unpc_custom_js" class="large-text code" rows="6"><?php echo esc_textarea($options['unpc_custom_js']); ?></textarea>
                <p class="description"><?php esc_html_e('Inject additional JavaScript for advanced integrations.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Enable IP Info Cache', 'ultimate-nat-port-checker'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="unpc_enable_cache" value="1" <?php checked($options['unpc_enable_cache'], '1'); ?> />
                    <?php esc_html_e('Cache IP lookup responses', 'ultimate-nat-port-checker'); ?>
                </label>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="unpc_cache_duration"><?php esc_html_e('Cache Duration (seconds)', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <input type="number" name="unpc_cache_duration" id="unpc_cache_duration" class="regular-text" value="<?php echo esc_attr($options['unpc_cache_duration']); ?>" min="60" max="86400" step="60" />
                <p class="description"><?php esc_html_e('How long to cache IP lookup responses (60-86400 seconds).', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
    </table>
</div>
