<?php
/**
 * Port Checker Settings Tab
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="unpc-settings-section">
    <h3><span class="dashicons dashicons-admin-tools"></span> <?php esc_html_e('Port Checker Configuration', 'ultimate-nat-port-checker'); ?></h3>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="unpc_port_timeout"><?php esc_html_e('Connection Timeout (ms)', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <input type="number" name="unpc_port_timeout" id="unpc_port_timeout" class="regular-text" value="<?php echo esc_attr($options['unpc_port_timeout']); ?>" min="500" max="10000" step="100" />
                <p class="description"><?php esc_html_e('Time to wait for each port check attempt (500-10000ms).', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="unpc_max_ports_check"><?php esc_html_e('Maximum Ports Per Scan', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <input type="number" name="unpc_max_ports_check" id="unpc_max_ports_check" class="regular-text" value="<?php echo esc_attr($options['unpc_max_ports_check']); ?>" min="10" max="200" step="5" />
                <p class="description"><?php esc_html_e('Limit the number of ports that can be checked per request.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="unpc_custom_presets"><?php esc_html_e('Custom Port Presets', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <textarea name="unpc_custom_presets" id="unpc_custom_presets" class="large-text" rows="4"><?php echo esc_textarea($options['unpc_custom_presets']); ?></textarea>
                <p class="description"><?php esc_html_e('Add custom port presets (Label: ports) each on a new line. Example: CloudXR:48010,48011', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
    </table>
</div>
