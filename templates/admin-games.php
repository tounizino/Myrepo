<?php
if (!defined('ABSPATH')) {
    exit;
}

$database = new CGA_Database();
$games = $database->get_games();
$current_game_id = isset($_GET['game_id']) ? intval($_GET['game_id']) : 0;
?>

<div class="wrap cga-wrap">
    <div class="cga-page-header">
        <h1><?php _e('Games', 'cloud-games-availability-v2'); ?></h1>
        <button type="button" class="button button-primary cga-add-game-btn">
            <span class="dashicons dashicons-plus"></span>
            <?php _e('Add New Game', 'cloud-games-availability-v2'); ?>
        </button>
    </div>

    <!-- Games List View -->
    <div class="cga-games-list" id="cga-games-list-view" style="<?php echo $current_game_id ? 'display: none;' : ''; ?>">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Cover', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Name', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Developer', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Status', 'cloud-games-availability-v2'); ?></th>
                    <th><?php _e('Actions', 'cloud-games-availability-v2'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                    <tr data-game-id="<?php echo esc_attr($game->id); ?>">
                        <td>
                            <?php if ($game->cover_image): ?>
                                <img src="<?php echo esc_url($game->cover_image); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <div class="cga-placeholder-cover">
                                    <span class="dashicons dashicons-format-image"></span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo esc_html($game->name); ?></strong>
                            <div class="row-actions">
                                <span><a href="#" class="cga-edit-game"><?php _e('Edit', 'cloud-games-availability-v2'); ?></a></span>
                                <span class="cga-separator">|</span>
                                <span><a href="<?php echo admin_url('admin.php?page=cloud-games-games&game_id=' . $game->id); ?>" class="cga-manage-game"><?php _e('Manage', 'cloud-games-availability-v2'); ?></a></span>
                            </div>
                        </td>
                        <td><?php echo esc_html($game->developer); ?></td>
                        <td>
                            <span class="cga-status-badge cga-status-<?php echo esc_attr($game->status); ?>">
                                <?php echo $game->status === 'active' ? __('Active', 'cloud-games-availability-v2') : __('Inactive', 'cloud-games-availability-v2'); ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="button button-small cga-edit-game">
                                <span class="dashicons dashicons-edit"></span>
                            </button>
                            <button type="button" class="button button-small cga-delete-game">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Single Game Management View -->
    <div class="cga-single-game-view" id="cga-single-game-view" style="<?php echo $current_game_id ? '' : 'display: none;'; ?>">
        <div class="cga-back-nav">
            <a href="<?php echo admin_url('admin.php?page=cloud-games-games'); ?>" class="button">
                <span class="dashicons dashicons-arrow-left-alt"></span>
                <?php _e('Back to Games', 'cloud-games-availability-v2'); ?>
            </a>
        </div>

        <div class="cga-game-editor-container">
            <!-- Game Details Section -->
            <div class="cga-section-card cga-game-details">
                <div class="cga-section-header">
                    <h2><?php _e('Game Details', 'cloud-games-availability-v2'); ?></h2>
                    <button type="button" class="button button-primary cga-save-game-details">
                        <span class="dashicons dashicons-saved"></span>
                        <?php _e('Save Game', 'cloud-games-availability-v2'); ?>
                    </button>
                </div>
                <form id="cga-game-form">
                    <input type="hidden" name="id" id="cga-game-id" value="<?php echo $current_game_id; ?>">
                    
                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-game-name"><?php _e('Game Name *', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="name" id="cga-game-name" required>
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-game-status"><?php _e('Status', 'cloud-games-availability-v2'); ?></label>
                            <select name="status" id="cga-game-status">
                                <option value="active"><?php _e('Active', 'cloud-games-availability-v2'); ?></option>
                                <option value="inactive"><?php _e('Inactive', 'cloud-games-availability-v2'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-game-developer"><?php _e('Developer', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="developer" id="cga-game-developer">
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-game-publisher"><?php _e('Publisher', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="publisher" id="cga-game-publisher">
                        </div>
                    </div>

                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-game-cover"><?php _e('Cover Image URL', 'cloud-games-availability-v2'); ?></label>
                            <input type="url" name="cover_image" id="cga-game-cover">
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-game-release-date"><?php _e('Release Date', 'cloud-games-availability-v2'); ?></label>
                            <input type="date" name="release_date" id="cga-game-release-date">
                        </div>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-game-description"><?php _e('Description', 'cloud-games-availability-v2'); ?></label>
                        <textarea name="description" id="cga-game-description" rows="4"></textarea>
                    </div>
                </form>
            </div>

            <!-- Platform Availability Section -->
            <div class="cga-section-card cga-platform-availability">
                <div class="cga-section-header">
                    <h2><?php _e('Platform Availability', 'cloud-games-availability-v2'); ?></h2>
                    <div class="cga-bulk-actions">
                        <button type="button" class="button cga-toggle-all-available">
                            <?php _e('Mark All Available', 'cloud-games-availability-v2'); ?>
                        </button>
                        <button type="button" class="button cga-toggle-all-unavailable">
                            <?php _e('Mark All Unavailable', 'cloud-games-availability-v2'); ?>
                        </button>
                    </div>
                </div>
                
                <form id="cga-availability-form">
                    <div id="cga-platform-availability-list" class="cga-platform-list">
                        <div class="cga-loading-spinner">
                            <span class="spinner is-active"></span>
                            <p><?php _e('Loading platforms...', 'cloud-games-availability-v2'); ?></p>
                        </div>
                    </div>
                    
                    <div class="cga-save-footer">
                        <button type="button" class="button button-primary cga-save-availability" style="display: none;">
                            <span class="dashicons dashicons-saved"></span>
                            <?php _e('Save Availability', 'cloud-games-availability-v2'); ?>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Platform Presets & Custom -->
            <div class="cga-section-card cga-platforms-manage">
                <div class="cga-section-header">
                    <h2><?php _e('Manage Platforms', 'cloud-games-availability-v2'); ?></h2>
                    <button type="button" class="button button-primary cga-add-platform-btn">
                        <span class="dashicons dashicons-plus"></span>
                        <?php _e('Add Platform', 'cloud-games-availability-v2'); ?>
                    </button>
                </div>

                <div class="cga-platform-presets">
                    <h3><?php _e('Quick Presets', 'cloud-games-availability-v2'); ?></h3>
                    <div class="cga-presets-grid">
                        <?php 
                        $presets = array(
                            'geforce-now' => 'GeForce NOW',
                            'xbox-cloud-gaming' => 'Xbox Cloud Gaming',
                            'playstation-plus-premium' => 'PlayStation Plus Premium',
                            'amazon-luna' => 'Amazon Luna',
                            'boosteroid' => 'Boosteroid',
                            'shadow-pc' => 'Shadow PC',
                            'air-gpu' => 'Air GPU',
                            'blacknut' => 'Blacknut',
                            'clouddeck' => 'CloudDeck',
                        );
                        foreach ($presets as $slug => $name): ?>
                            <button type="button" class="cga-preset-btn" data-slug="<?php echo esc_attr($slug); ?>" data-name="<?php echo esc_attr($name); ?>">
                                <span class="cga-preset-icon">🎮</span>
                                <?php echo esc_html($name); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="cga-custom-platforms">
                    <h3><?php _e('Custom Platforms', 'cloud-games-availability-v2'); ?></h3>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Icon', 'cloud-games-availability-v2'); ?></th>
                                <th><?php _e('Name', 'cloud-games-availability-v2'); ?></th>
                                <th><?php _e('Price', 'cloud-games-availability-v2'); ?></th>
                                <th><?php _e('Actions', 'cloud-games-availability-v2'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="cga-custom-platforms-list">
                            <!-- Loaded via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Add/Edit Game (List View) -->
    <div id="cga-game-modal" class="cga-modal">
        <div class="cga-modal-content">
            <div class="cga-modal-header">
                <h2><?php _e('Add/Edit Game', 'cloud-games-availability-v2'); ?></h2>
                <button type="button" class="cga-modal-close">&times;</button>
            </div>
            <div class="cga-modal-body">
                <form id="cga-game-modal-form">
                    <input type="hidden" name="id" id="cga-game-modal-id">
                    
                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-game-modal-name"><?php _e('Game Name *', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="name" id="cga-game-modal-name" required>
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-game-modal-status"><?php _e('Status', 'cloud-games-availability-v2'); ?></label>
                            <select name="status" id="cga-game-modal-status">
                                <option value="active"><?php _e('Active', 'cloud-games-availability-v2'); ?></option>
                                <option value="inactive"><?php _e('Inactive', 'cloud-games-availability-v2'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-game-modal-developer"><?php _e('Developer', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="developer" id="cga-game-modal-developer">
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-game-modal-publisher"><?php _e('Publisher', 'cloud-games-availability-v2'); ?></label>
                            <input type="text" name="publisher" id="cga-game-modal-publisher">
                        </div>
                    </div>

                    <div class="cga-form-row">
                        <div class="cga-form-group">
                            <label for="cga-game-modal-cover"><?php _e('Cover Image URL', 'cloud-games-availability-v2'); ?></label>
                            <input type="url" name="cover_image" id="cga-game-modal-cover">
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-game-modal-release-date"><?php _e('Release Date', 'cloud-games-availability-v2'); ?></label>
                            <input type="date" name="release_date" id="cga-game-modal-release-date">
                        </div>
                    </div>

                    <div class="cga-form-group">
                        <label for="cga-game-modal-description"><?php _e('Description', 'cloud-games-availability-v2'); ?></label>
                        <textarea name="description" id="cga-game-modal-description" rows="4"></textarea>
                    </div>
                </form>
            </div>
            <div class="cga-modal-footer">
                <button type="button" class="button cga-modal-close"><?php _e('Cancel', 'cloud-games-availability-v2'); ?></button>
                <button type="button" class="button button-primary cga-save-game-modal"><?php _e('Save Game', 'cloud-games-availability-v2'); ?></button>
            </div>
        </div>
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
                        </div>
                        <div class="cga-form-group">
                            <label for="cga-platform-display-order"><?php _e('Display Order', 'cloud-games-availability-v2'); ?></label>
                            <input type="number" name="display_order" id="cga-platform-display-order" value="0" min="0">
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
