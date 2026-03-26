<?php
if (!defined('ABSPATH')) {
    exit;
}

$database = new CGA_Database();
$platforms = $database->get_platforms();
?>

<div class="wrap cga-wrap">
    <div class="cga-page-header">
        <h1><?php _e('Platforms', 'cloud-games-availability-v2'); ?></h1>
        <a href="<?php echo admin_url('admin.php?page=cloud-games-games'); ?>" class="button button-primary">
            <span class="dashicons dashicons-arrow-left-alt"></span>
            <?php _e('Go to Games', 'cloud-games-availability-v2'); ?>
        </a>
    </div>

    <div class="cga-info-card">
        <h2><?php _e('Platform Management', 'cloud-games-availability-v2'); ?></h2>
        <p><?php _e('Platforms are now managed from the Games page. To add or edit platforms, go to any game and use the "Manage Platforms" section.', 'cloud-games-availability-v2'); ?></p>
        <p><?php _e('Current platforms:', 'cloud-games-availability-v2'); ?></p>
    </div>

    <div class="cga-platforms-list">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Icon', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Name', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Price', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Status', 'cloud-games-availability-v2'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($platforms as $platform): ?>
                    <tr>
                        <td>
                            <?php if ($platform->icon_url): ?>
                                <img src="<?php echo esc_url($platform->icon_url); ?>" alt="<?php echo esc_attr($platform->name); ?>" style="width: 40px; height: 40px; object-fit: contain;">
                            <?php else: ?>
                                <div class="cga-placeholder-icon">
                                    <span class="dashicons dashicons-admin-site"></span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo esc_html($platform->name); ?></strong>
                        </td>
                        <td>
                            <?php if ($platform->base_price > 0): ?>
                                <?php echo '$' . number_format($platform->base_price, 2); ?> <?php echo $platform->price_period === 'year' ? __('/year', 'cloud-games-availability-v2') : __('/month', 'cloud-games-availability-v2'); ?>
                            <?php else: ?>
                                <?php _e('Free', 'cloud-games-availability-v2'); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="cga-status-badge cga-status-<?php echo esc_attr($platform->status); ?>">
                                <?php echo $platform->status === 'active' ? __('Active', 'cloud-games-availability-v2') : __('Inactive', 'cloud-games-availability-v2'); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <style>
        .cga-info-card {
            background: #fff;
            border-left: 4px solid #6366f1;
            padding: 20px;
            margin-bottom: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cga-info-card h2 {
            margin-top: 0;
            font-size: 18px;
        }
        .cga-info-card p {
            margin: 8px 0;
            color: #64748b;
        }
    </style>
</div>
