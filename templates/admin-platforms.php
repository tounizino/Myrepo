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
        <button type="button" class="button button-primary cga-add-platform-btn">
            <span class="dashicons dashicons-plus"></span>
            <?php _e('Add New Platform', 'cloud-games-availability-v2'); ?>
        </button>
    </div>

    <div class="cga-platforms-list">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Icon', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Name', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Price', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Status', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Actions', 'cloud-games-availability-v2'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($platforms as $platform): ?>
                    <tr data-platform-id="<?php echo esc_attr($platform->id); ?>">
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
                            <div class="row-actions">
                                <span><a href="#" class="cga-edit-platform"><?php _e('Edit', 'cloud-games-availability-v2'); ?></a></span>
                            </div>
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
                        <td>
                            <button type="button" class="button button-small cga-edit-platform">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button type="button" class="button button-small cga-delete-platform">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Add/Edit Platform -->
    <div id="cga-platform-modal" class="cga-modal">
        <div class="cga-modal-content">
            <div class="cga-modal-header">
                <h2><?php _e('Add/Edit Platform', 'cloud-games-availability-v2'); ?></h2>
                <button type="button" class="cga-modal-close">&times;</button>
            </div>
            <div class="cga-modal-body">
                <form id="cga-platform-form">
                    <input type="hidden" name="id" id="cga-platform-id">
                    
                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-platform-name"><?php _e('Platform Name *', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="name" id="cga-platform-name" required>
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-platform-status"><?php _e('Status', 'cloud-games-availability-v2'); ?></label>
                            <select name="status" id="cga-platform-status">
                                <option value="active"><?php _e('Active', 'cloud-games-availability-v2'); ?></option>
                                <option value="inactive"><?php _e('Inactive', 'cloud-games-availability-v2'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-platform-icon"><?php _e('Icon URL', 'cloud-games-availability-v2'); ?></label>
                            <input type="url" name="icon_url" id="cga-platform-icon" placeholder="https://example.com/icon.png">
                            <small class="cga-help-text"><?php _e('Recommended size: 512x512px PNG or SVG', 'cloud-games-availability-v2'); ?></small>
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-platform-display-order"><?php _e('Display Order', 'cloud-games-availability-v2'); ?></label>
                            <input type="number" name="display_order" id="cga-platform-display-order" value="0" min="0">
                            <small class="cga-help-text"><?php _e('Lower numbers appear first', 'cloud-games-availability-v2'); ?></small>
                        </div>
                    </div>

                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-platform-price"><?php _e('Base Price', 'cloud-games-availability-v2'); ?></label>
                            <input type="number" name="base_price" id="cga-platform-price" step="0.01" min="0" value="0">
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-platform-currency"><?php _e('Currency', 'cloud-games-availability-v2'); ?></label>
                            <select name="price_currency" id="cga-platform-currency">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="GBP">GBP</option>
                                <option value="CAD">CAD</option>
                                <option value="AUD">AUD</option>
                            </select>
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-platform-period"><?php _e('Price Period', 'cloud-games-availability-v2'); ?></label>
                            <select name="price_period" id="cga-platform-period">
                                <option value="month"><?php _e('Monthly', 'cloud-games-availability-v2'); ?></option>
                                <option value="year"><?php _e('Yearly', 'cloud-games-availability-v2'); ?></option>
                                <option value="one-time"><?php _e('One-time', 'cloud-games-availability-v2'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-platform-tier"><?php _e('Tier Name', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="tier_name" id="cga-platform-tier" placeholder="Premium, Ultimate, etc.">
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-platform-cta-text"><?php _e('CTA Button Text', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="cta_text" id="cga-platform-cta-text" value="Play Now">
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-platform-cta-url"><?php _e('CTA Button URL', 'cloud-games-availability-v2'); ?></label>
                            <input type="url" name="cta_url" id="cga-platform-cta-url" placeholder="https://example.com">
                        </div>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-platform-description"><?php _e('Description', 'cloud-games-availability-v2'); ?></label>
                        <textarea name="description" id="cga-platform-description" rows="2"></textarea>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-platform-notes"><?php _e('Notes (shown on frontend)', 'cloud-games-availability-v2'); ?></label>
                        <textarea name="notes" id="cga-platform-notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="cga-modal-footer">
                <button type="button" class="button cga-modal-close"><?php _e('Cancel', 'cloud-games-availability-v2'); ?></button>
                <button type="button" class="button button-primary cga-save-platform"><?php _e('Save Platform', 'cloud-games-availability-v2'); ?></button>
            </div>
        </div>
    </div>
</div>
