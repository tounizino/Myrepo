<?php
/**
 * Display Settings Tab
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="unpc-settings-section">
    <h3><span class="dashicons dashicons-visibility"></span> <?php esc_html_e('Display Controls', 'ultimate-nat-port-checker'); ?></h3>
    
    <table class="form-table">
        <tr>
            <th scope="row"><?php esc_html_e('Security Note', 'ultimate-nat-port-checker'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="unpc_show_security_note" value="1" <?php checked($options['unpc_show_security_note'], '1'); ?> />
                    <?php esc_html_e('Show security/privacy note at the top', 'ultimate-nat-port-checker'); ?>
                </label>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Device Information Panel', 'ultimate-nat-port-checker'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="unpc_show_device_info" value="1" <?php checked($options['unpc_show_device_info'], '1'); ?> />
                    <?php esc_html_e('Display user device details and ISP data', 'ultimate-nat-port-checker'); ?>
                </label>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Router Login Grid', 'ultimate-nat-port-checker'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="unpc_show_router_logins" value="1" <?php checked($options['unpc_show_router_logins'], '1'); ?> />
                    <?php esc_html_e('Show popular router login shortcuts', 'ultimate-nat-port-checker'); ?>
                </label>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Helpful Guides', 'ultimate-nat-port-checker'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="unpc_show_guides" value="1" <?php checked($options['unpc_show_guides'], '1'); ?> />
                    <?php esc_html_e('Display contextual guides and tips', 'ultimate-nat-port-checker'); ?>
                </label>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php esc_html_e('Footer', 'ultimate-nat-port-checker'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="unpc_show_footer" value="1" <?php checked($options['unpc_show_footer'], '1'); ?> />
                    <?php esc_html_e('Display immersive 2026 footer', 'ultimate-nat-port-checker'); ?>
                </label>
            </td>
        </tr>
    </table>
</div>
