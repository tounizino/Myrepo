<?php
if (!defined('ABSPATH')) {
    exit;
}

$database = new CGA_Database();
$games = $database->get_games();
?>

<div class="wrap cga-wrap">
    <div class="cga-page-header">
        <h1><?php _e('Games', 'cloud-games-availability-v2'); ?></h1>
        <button type="button" class="button button-primary cga-add-game-btn">
            <span class="dashicons dashicons-plus"></span>
            <?php _e('Add New Game', 'cloud-games-availability-v2'); ?>
        </button>
    </div>

    <div class="cga-games-list">
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
                                <span><a href="<?php echo admin_url('admin.php?page=cloud-games-games&action=availability&game_id=' . $game->id); ?>" class="cga-manage-availability"><?php _e('Availability', 'cloud-games-availability-v2'); ?></a></span>
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

    <!-- Modal for Add/Edit Game -->
    <div id="cga-game-modal" class="cga-modal">
        <div class="cga-modal-content">
            <div class="cga-modal-header">
                <h2><?php _e('Add/Edit Game', 'cloud-games-availability-v2'); ?></h2>
                <button type="button" class="cga-modal-close">&times;</button>
            </div>
            <div class="cga-modal-body">
                <form id="cga-game-form">
                    <input type="hidden" name="id" id="cga-game-id">
                    
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
            <div class="cga-modal-footer">
                <button type="button" class="button cga-modal-close"><?php _e('Cancel', 'cloud-games-availability-v2'); ?></button>
                <button type="button" class="button button-primary cga-save-game"><?php _e('Save Game', 'cloud-games-availability-v2'); ?></button>
            </div>
        </div>
    </div>

    <!-- Availability Modal -->
    <div id="cga-availability-modal" class="cga-modal cga-large-modal">
        <div class="cga-modal-content">
            <div class="cga-modal-header">
                <h2><?php _e('Manage Availability', 'cloud-games-availability-v2'); ?></h2>
                <button type="button" class="cga-modal-close">&times;</button>
            </div>
            <div class="cga-modal-body">
                <form id="cga-availability-form">
                    <input type="hidden" name="game_id" id="cga-availability-game-id">
                    <div id="cga-platform-availability-list"></div>
                </form>
            </div>
            <div class="cga-modal-footer">
                <button type="button" class="button cga-modal-close"><?php _e('Cancel', 'cloud-games-availability-v2'); ?></button>
                <button type="button" class="button button-primary cga-save-availability"><?php _e('Save Changes', 'cloud-games-availability-v2'); ?></button>
            </div>
        </div>
    </div>
</div>
