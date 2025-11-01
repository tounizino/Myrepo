<?php
/**
 * NAT Checker Settings Tab
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="unpc-settings-section">
    <h3><span class="dashicons dashicons-networking"></span> <?php esc_html_e('NAT Checker Configuration', 'ultimate-nat-port-checker'); ?></h3>
    
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="unpc_nat_stun_servers"><?php esc_html_e('STUN Servers', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <textarea name="unpc_nat_stun_servers" id="unpc_nat_stun_servers" class="large-text" rows="4"><?php echo esc_textarea($options['unpc_nat_stun_servers']); ?></textarea>
                <p class="description"><?php esc_html_e('Enter STUN servers (one per line). Default: stun:stun.l.google.com:19302', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row">
                <label for="unpc_nat_timeout"><?php esc_html_e('Timeout (ms)', 'ultimate-nat-port-checker'); ?></label>
            </th>
            <td>
                <input type="number" name="unpc_nat_timeout" id="unpc_nat_timeout" class="regular-text" value="<?php echo esc_attr($options['unpc_nat_timeout']); ?>" min="1000" max="30000" step="100" />
                <p class="description"><?php esc_html_e('Maximum time to wait for NAT check results (1000-30000ms).', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
        
        <tr>
            <th scope="row"><?php esc_html_e('Technical Log', 'ultimate-nat-port-checker'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="unpc_show_technical_log" value="1" <?php checked($options['unpc_show_technical_log'], '1'); ?> />
                    <?php esc_html_e('Show expandable technical log with WebRTC candidates', 'ultimate-nat-port-checker'); ?>
                </label>
                <p class="description"><?php esc_html_e('Displays detailed ICE candidate information for advanced users.', 'ultimate-nat-port-checker'); ?></p>
            </td>
        </tr>
    </table>
</div>
