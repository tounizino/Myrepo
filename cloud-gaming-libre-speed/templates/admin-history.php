<?php
/**
 * Admin test history page.
 *
 * @package CloudGamingSpeedTest
 */

if (!defined('ABSPATH')) {
    exit;
}

$results = CGST_Database::get_results(200);
$export_url = wp_nonce_url(admin_url('admin-ajax.php?action=cgst_export_csv'), 'cgst_admin_nonce', 'nonce');
?>

<div class="wrap cgst-admin">
    <h1><?php esc_html_e('Historic Test Results', 'cloud-gaming-speed-test'); ?></h1>
    <p class="description">
        <?php esc_html_e('Review the performance logs captured from the frontend widget. Use the export button to download a CSV report for deeper analytics or client reporting.', 'cloud-gaming-speed-test'); ?>
    </p>

    <p>
        <a href="<?php echo esc_url($export_url); ?>" class="button button-secondary">
            <span class="dashicons dashicons-download"></span>
            <?php esc_html_e('Export CSV', 'cloud-gaming-speed-test'); ?>
        </a>
    </p>

    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Timestamp', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Server', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Download (Mbps)', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Upload (Mbps)', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Ping (ms)', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Jitter (ms)', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Packet Loss (%)', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Rating', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Recommendation', 'cloud-gaming-speed-test'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($results)) : ?>
                <?php foreach ($results as $row) : ?>
                    <tr>
                        <td><?php echo esc_html(get_date_from_gmt($row['created_at'], get_option('date_format') . ' ' . get_option('time_format'))); ?></td>
                        <td>
                            <strong><?php echo esc_html($row['server_name']); ?></strong><br />
                            <span class="cgst-pill"><?php echo esc_html($row['server_id']); ?></span>
                        </td>
                        <td><?php echo esc_html(number_format_i18n($row['download_mbps'], 2)); ?></td>
                        <td><?php echo esc_html(number_format_i18n($row['upload_mbps'], 2)); ?></td>
                        <td><?php echo esc_html(number_format_i18n($row['ping_ms'], 2)); ?></td>
                        <td><?php echo esc_html(number_format_i18n($row['jitter_ms'], 2)); ?></td>
                        <td><?php echo esc_html(number_format_i18n($row['packet_loss'], 2)); ?></td>
                        <td><?php echo esc_html($row['rating']); ?></td>
                        <td><?php echo esc_html($row['recommendation']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="9"><?php esc_html_e('No recorded tests yet.', 'cloud-gaming-speed-test'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
