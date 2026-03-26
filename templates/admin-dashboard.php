<?php
if (!defined('ABSPATH')) {
    exit;
}

$database = new CGA_Database();
$games_count = count($database->get_games());
$platforms_count = count($database->get_platforms());
?>

<div class="wrap cga-wrap">
    <div class="cga-header">
        <h1>
            <span class="dashicons dashicons-cloud"></span>
            <?php _e('Cloud Games Availability v2', 'cloud-games-availability-v2'); ?>
        </h1>
        <div class="cga-version"><?php echo CGA_VERSION; ?></div>
    </div>

    <div class="cga-dashboard-grid">
        <div class="cga-dashboard-card">
            <div class="cga-stat-icon">
                <span class="dashicons dashicons-games"></span>
            </div>
            <div class="cga-stat-content">
                <div class="cga-stat-number"><?php echo $games_count; ?></div>
                <div class="cga-stat-label"><?php _e('Games', 'cloud-games-availability-v2'); ?></div>
            </div>
            <a href="<?php echo admin_url('admin.php?page=cloud-games-games'); ?>" class="cga-stat-action">
                <?php _e('Manage', 'cloud-games-availability-v2'); ?> &rarr;
            </a>
        </div>

        <div class="cga-dashboard-card">
            <div class="cga-stat-icon">
                <span class="dashicons dashicons-desktop"></span>
            </div>
            <div class="cga-stat-content">
                <div class="cga-stat-number"><?php echo $platforms_count; ?></div>
                <div class="cga-stat-label"><?php _e('Platforms', 'cloud-games-availability-v2'); ?></div>
            </div>
            <a href="<?php echo admin_url('admin.php?page=cloud-games-platforms'); ?>" class="cga-stat-action">
                <?php _e('Manage', 'cloud-games-availability-v2'); ?> &rarr;
            </a>
        </div>

        <div class="cga-dashboard-card">
            <div class="cga-stat-icon">
                <span class="dashicons dashicons-admin-generic"></span>
            </div>
            <div class="cga-stat-content">
                <div class="cga-stat-label"><?php _e('Settings', 'cloud-games-availability-v2'); ?></div>
            </div>
            <a href="<?php echo admin_url('admin.php?page=cloud-games-settings'); ?>" class="cga-stat-action">
                <?php _e('Configure', 'cloud-games-availability-v2'); ?> &rarr;
            </a>
        </div>
    </div>

    <div class="cga-dashboard-sections">
        <div class="cga-section-card">
            <h2><?php _e('Getting Started', 'cloud-games-availability-v2'); ?></h2>
            <ol class="cga-steps">
                <li>
                    <strong><?php _e('Add Platforms', 'cloud-games-availability-v2'); ?></strong>
                    <p><?php _e('Configure cloud gaming platforms like GeForce NOW, Xbox Cloud Gaming, etc.', 'cloud-games-availability-v2'); ?></p>
                </li>
                <li>
                    <strong><?php _e('Add Games', 'cloud-games-availability-v2'); ?></strong>
                    <p><?php _e('Add games to track availability across platforms.', 'cloud-games-availability-v2'); ?></p>
                </li>
                <li>
                    <strong><?php _e('Set Availability', 'cloud-games-availability-v2'); ?></strong>
                    <p><?php _e('Mark which platforms have each game available.', 'cloud-games-availability-v2'); ?></p>
                </li>
                <li>
                    <strong><?php _e('Embed on Site', 'cloud-games-availability-v2'); ?></strong>
                    <p><?php _e('Use the shortcode or Gutenberg block to display availability cards.', 'cloud-games-availability-v2'); ?></p>
                </li>
            </ol>
        </div>

        <div class="cga-section-card">
            <h2><?php _e('Usage', 'cloud-games-availability-v2'); ?></h2>
            <h3><?php _e('Shortcode', 'cloud-games-availability-v2'); ?></h3>
            <div class="cga-code-block">
                <code>[cloud_games_availability game_id="1"]</code>
            </div>
            <p><?php _e('Optional parameters:', 'cloud-games-availability-v2'); ?></p>
            <ul>
                <li><code>game_id</code> - Game ID (required)</li>
                <li><code>group_by_availability</code> - "yes" or "no" (default: no)</li>
                <li><code>columns</code> - Number of columns (default: 3)</li>
            </ul>
        </div>
    </div>
</div>
