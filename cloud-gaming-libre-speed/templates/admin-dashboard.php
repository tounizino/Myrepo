<?php
/**
 * Admin dashboard overview page.
 *
 * @package CloudGamingSpeedTest
 */

if (!defined('ABSPATH')) {
    exit;
}

$recent_results = CGST_Database::get_results(5);
$servers        = CGST_Database::get_servers();
$articles       = CGST_Database::get_articles();
?>

<div class="wrap cgst-admin">
    <h1 class="cgst-admin-title">
        <?php esc_html_e('Cloud Gaming Speed Test Dashboard', 'cloud-gaming-speed-test'); ?>
    </h1>

    <div class="cgst-admin-grid">
        <div class="cgst-admin-card">
            <h2><?php esc_html_e('Getting Started', 'cloud-gaming-speed-test'); ?></h2>
            <ol>
                <li><?php esc_html_e('Configure your LibreSpeed backend URLs in the Server Presets tab.', 'cloud-gaming-speed-test'); ?></li>
                <li><?php esc_html_e('Embed the speed test anywhere using the shortcode [cloudspeedtest].', 'cloud-gaming-speed-test'); ?></li>
                <li><?php esc_html_e('Optionally curate optimization articles and highlight them for gamers.', 'cloud-gaming-speed-test'); ?></li>
            </ol>
        </div>

        <div class="cgst-admin-card">
            <h2><?php esc_html_e('Preset Summary', 'cloud-gaming-speed-test'); ?></h2>
            <p><strong><?php esc_html_e('Configured Servers:', 'cloud-gaming-speed-test'); ?></strong> <?php echo esc_html(count($servers)); ?></p>
            <ul class="cgst-list">
                <?php foreach ($servers as $server) : ?>
                    <li>
                        <span class="dashicons dashicons-location"></span>
                        <?php echo esc_html($server['name']); ?>
                        <small><?php echo esc_html($server['location']); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="cgst-admin-card">
            <h2><?php esc_html_e('Recent Test Results', 'cloud-gaming-speed-test'); ?></h2>
            <?php if (!empty($recent_results)) : ?>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Date', 'cloud-gaming-speed-test'); ?></th>
                            <th><?php esc_html_e('Server', 'cloud-gaming-speed-test'); ?></th>
                            <th><?php esc_html_e('Down / Up', 'cloud-gaming-speed-test'); ?></th>
                            <th><?php esc_html_e('Ping', 'cloud-gaming-speed-test'); ?></th>
                            <th><?php esc_html_e('Rating', 'cloud-gaming-speed-test'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recent_results as $row) : ?>
                        <tr>
                            <td><?php echo esc_html(get_date_from_gmt($row['created_at'], get_option('date_format') . ' ' . get_option('time_format'))); ?></td>
                            <td><?php echo esc_html($row['server_name']); ?></td>
                            <td><?php echo esc_html(round($row['download_mbps'], 2) . ' / ' . round($row['upload_mbps'], 2) . ' Mbps'); ?></td>
                            <td><?php echo esc_html(round($row['ping_ms'], 2) . ' ms'); ?></td>
                            <td><?php echo esc_html($row['rating']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e('No tests recorded yet. Run the speed test on the frontend to start building history.', 'cloud-gaming-speed-test'); ?></p>
            <?php endif; ?>
        </div>

        <div class="cgst-admin-card">
            <h2><?php esc_html_e('Featured Guides', 'cloud-gaming-speed-test'); ?></h2>
            <?php if (!empty($articles)) : ?>
                <ul class="cgst-list">
                    <?php foreach ($articles as $article) : ?>
                        <li>
                            <span class="dashicons dashicons-media-document"></span>
                            <a href="<?php echo esc_url($article['url']); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo esc_html($article['title']); ?>
                            </a>
                            <small><?php echo esc_html($article['category']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p><?php esc_html_e('Add your best optimisation resources to help players achieve lower latency.', 'cloud-gaming-speed-test'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="cgst-admin-callout">
        <h2><?php esc_html_e('Quick Shortcode Reference', 'cloud-gaming-speed-test'); ?></h2>
        <p>
            <code>[cloudspeedtest theme="neon" layout="compact"]</code> –
            <?php esc_html_e('Embed the animated test interface on any page or post. Optional attributes let you define preset themes or condensed layouts. Additional layout options can be expanded in templates/speed-test-template.php.', 'cloud-gaming-speed-test'); ?>
        </p>
    </div>
</div>
