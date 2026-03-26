<?php
/**
 * Admin Class
 * 
 * @package Cloud_Gaming_Tracker
 */

if (!defined('ABSPATH')) exit;

class CGT_Admin {
    
    /**
     * Initialize admin hooks
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
        
        // AJAX handlers
        add_action('wp_ajax_cgt_save_platform', array(__CLASS__, 'ajax_save_platform'));
        add_action('wp_ajax_cgt_delete_platform', array(__CLASS__, 'ajax_delete_platform'));
        add_action('wp_ajax_cgt_save_availability', array(__CLASS__, 'ajax_save_availability'));
        add_action('wp_ajax_cgt_save_game_settings', array(__CLASS__, 'ajax_save_game_settings'));
        add_action('wp_ajax_cgt_add_game', array(__CLASS__, 'ajax_add_game'));
        add_action('wp_ajax_cgt_delete_game', array(__CLASS__, 'ajax_delete_game'));
        add_action('wp_ajax_cgt_save_settings', array(__CLASS__, 'ajax_save_settings'));
    }
    
    /**
     * Add admin menu pages
     */
    public static function add_admin_menu() {
        add_menu_page(
            __('Cloud Gaming Tracker', 'cloud-gaming-tracker'),
            __('Cloud Gaming', 'cloud-gaming-tracker'),
            'manage_options',
            'cgt-dashboard',
            array(__CLASS__, 'render_dashboard'),
            'dashicons-cloud',
            30
        );
        
        add_submenu_page('cgt-dashboard', __('Platforms', 'cloud-gaming-tracker'), __('Platforms', 'cloud-gaming-tracker'), 'manage_options', 'cgt-platforms', array(__CLASS__, 'render_platforms_page'));
        add_submenu_page('cgt-dashboard', __('Games', 'cloud-gaming-tracker'), __('Games', 'cloud-gaming-tracker'), 'manage_options', 'cgt-games', array(__CLASS__, 'render_games_page'));
        add_submenu_page('cgt-dashboard', __('Settings', 'cloud-gaming-tracker'), __('Settings', 'cloud-gaming-tracker'), 'manage_options', 'cgt-settings', array(__CLASS__, 'render_settings_page'));
    }
    
    /**
     * Enqueue admin assets
     */
    public static function enqueue_assets($hook) {
        if (strpos($hook, 'cgt-') === false) return;
        
        wp_enqueue_style('cgt-admin-css', CGT_PLUGIN_URL . 'assets/css/admin.css', array(), CGT_VERSION);
        wp_enqueue_script('cgt-admin-js', CGT_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), CGT_VERSION, true);
        wp_localize_script('cgt-admin-js', 'cgtAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cgt_admin_nonce'),
            'strings' => array(
                'confirmDelete' => __('Are you sure?', 'cloud-gaming-tracker'),
                'saving' => __('Saving...', 'cloud-gaming-tracker'),
                'saved' => __('Saved!', 'cloud-gaming-tracker'),
                'error' => __('Error', 'cloud-gaming-tracker'),
            )
        ));
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
    
    /**
     * Render dashboard page
     */
    public static function render_dashboard() {
        $platforms = CGT_Database::get_platforms(array('active_only' => false));
        $games = CGT_Database::get_games(array('active_only' => false));
        $active_platforms = array_filter($platforms, fn($p) => $p['is_active']);
        $active_games = array_filter($games, fn($g) => $g['is_active']);
        ?>
        <div class="cgt-admin-wrap">
            <div class="cgt-admin-header">
                <div class="cgt-admin-title">
                    <h1>☁️ <?php _e('Cloud Gaming Tracker', 'cloud-gaming-tracker'); ?></h1>
                    <span class="version-badge">v<?php echo CGT_VERSION; ?></span>
                </div>
                <div class="cgt-admin-actions">
                    <a href="<?php echo admin_url('admin.php?page=cgt-platforms'); ?>" class="cgt-btn cgt-btn-primary">
                        <?php _e('Manage Platforms', 'cloud-gaming-tracker'); ?>
                    </a>
                </div>
            </div>
            <div class="cgt-admin-nav">
                <a href="<?php echo admin_url('admin.php?page=cgt-dashboard'); ?>" class="current"><?php _e('Dashboard', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-platforms'); ?>"><?php _e('Platforms', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-games'); ?>"><?php _e('Games', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-settings'); ?>"><?php _e('Settings', 'cloud-gaming-tracker'); ?></a>
            </div>
            <div class="cgt-stats-grid">
                <div class="cgt-stat-card">
                    <div class="cgt-stat-value"><?php echo count($active_platforms); ?></div>
                    <div class="cgt-stat-label"><?php _e('Active Platforms', 'cloud-gaming-tracker'); ?></div>
                </div>
                <div class="cgt-stat-card">
                    <div class="cgt-stat-value"><?php echo count($active_games); ?></div>
                    <div class="cgt-stat-label"><?php _e('Active Games', 'cloud-gaming-tracker'); ?></div>
                </div>
                <div class="cgt-stat-card">
                    <div class="cgt-stat-value"><?php echo count($platforms); ?></div>
                    <div class="cgt-stat-label"><?php _e('Total Platforms', 'cloud-gaming-tracker'); ?></div>
                </div>
                <div class="cgt-stat-card">
                    <div class="cgt-stat-value"><?php echo count($games); ?></div>
                    <div class="cgt-stat-label"><?php _e('Total Games', 'cloud-gaming-tracker'); ?></div>
                </div>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php _e('Quick Start Guide', 'cloud-gaming-tracker'); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <ol style="line-height: 2;">
                        <li><strong><?php _e('Platforms:', 'cloud-gaming-tracker'); ?></strong> <?php _e('9 cloud gaming platforms pre-configured. Add more from Platforms page.', 'cloud-gaming-tracker'); ?></li>
                        <li><strong><?php _e('Games:', 'cloud-gaming-tracker'); ?></strong> <?php _e('Create game entries and set platform availability.', 'cloud-gaming-tracker'); ?></li>
                        <li><strong><?php _e('Embed:', 'cloud-gaming-tracker'); ?></strong> <?php _e('Use shortcode <code>[cloud_gaming_tracker game_id="1"]</code> or Gutenberg block.', 'cloud-gaming-tracker'); ?></li>
                    </ol>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render platforms page
     */
    public static function render_platforms_page() {
        $platforms = CGT_Database::get_platforms(array('active_only' => false));
        ?>
        <div class="cgt-admin-wrap">
            <div class="cgt-admin-header">
                <div class="cgt-admin-title">
                    <h1><?php _e('Manage Platforms', 'cloud-gaming-tracker'); ?></h1>
                </div>
                <button class="cgt-btn cgt-btn-primary cgt-add-platform">
                    <span>+</span> <?php _e('Add Platform', 'cloud-gaming-tracker'); ?>
                </button>
            </div>
            <div class="cgt-admin-nav">
                <a href="<?php echo admin_url('admin.php?page=cgt-dashboard'); ?>"><?php _e('Dashboard', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-platforms'); ?>" class="current"><?php _e('Platforms', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-games'); ?>"><?php _e('Games', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-settings'); ?>"><?php _e('Settings', 'cloud-gaming-tracker'); ?></a>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-body">
                    <table class="cgt-table">
                        <thead>
                            <tr>
                                <th><?php _e('Platform', 'cloud-gaming-tracker'); ?></th>
                                <th><?php _e('Icon', 'cloud-gaming-tracker'); ?></th>
                                <th><?php _e('Price', 'cloud-gaming-tracker'); ?></th>
                                <th><?php _e('Game', 'cloud-gaming-tracker'); ?></th>
                                <th><?php _e('Status', 'cloud-gaming-tracker'); ?></th>
                                <th><?php _e('Order', 'cloud-gaming-tracker'); ?></th>
                                <th><?php _e('Actions', 'cloud-gaming-tracker'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($platforms)): ?>
                            <?php foreach ($platforms as $platform): ?>
                            <tr class="cgt-platform-row" 
                                data-platform-id="<?php echo $platform['id']; ?>"
                                data-name="<?php echo esc_attr($platform['name']); ?>"
                                data-icon="<?php echo esc_attr($platform['icon_url']); ?>"
                                data-game-included="<?php echo $platform['game_included']; ?>"
                                data-price="<?php echo esc_attr($platform['price']); ?>"
                                data-currency="<?php echo esc_attr($platform['currency']); ?>"
                                data-tier="<?php echo esc_attr($platform['tier']); ?>"
                                data-cta-text="<?php echo esc_attr($platform['cta_text']); ?>"
                                data-cta-url="<?php echo esc_attr($platform['cta_url']); ?>"
                                data-order="<?php echo $platform['display_order']; ?>"
                                data-active="<?php echo $platform['is_active']; ?>">
                                <td><?php echo esc_html($platform['name']); ?></td>
                                <td>
                                    <div class="cgt-icon-preview">
                                        <?php if ($platform['icon_url']): ?>
                                            <img src="<?php echo esc_url($platform['icon_url']); ?>" alt="">
                                        <?php else: ?>
                                            <span>🎮</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo esc_html($platform['currency'] . $platform['price']); ?></td>
                                <td>
                                    <span class="cgt-badge <?php echo $platform['game_included'] ? 'cgt-badge-success' : 'cgt-badge-warning'; ?>">
                                        <?php echo $platform['game_included'] ? __('Included', 'cloud-gaming-tracker') : __('Required', 'cloud-gaming-tracker'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="cgt-badge <?php echo $platform['is_active'] ? 'cgt-badge-success' : 'cgt-badge-danger'; ?>">
                                        <?php echo $platform['is_active'] ? __('Active', 'cloud-gaming-tracker') : __('Inactive', 'cloud-gaming-tracker'); ?>
                                    </span>
                                </td>
                                <td><?php echo $platform['display_order']; ?></td>
                                <td class="cgt-table-actions">
                                    <button class="cgt-btn cgt-btn-secondary cgt-edit-platform" data-platform-id="<?php echo $platform['id']; ?>">
                                        <?php _e('Edit', 'cloud-gaming-tracker'); ?>
                                    </button>
                                    <button class="cgt-btn cgt-btn-danger cgt-delete-platform" data-platform-id="<?php echo $platform['id']; ?>">
                                        <?php _e('Delete', 'cloud-gaming-tracker'); ?>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">
                                    <?php _e('No platforms yet. Click "Add Platform" to get started!', 'cloud-gaming-tracker'); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Platform Modal -->
        <div class="cgt-modal-overlay cgt-platform-modal">
            <div class="cgt-modal">
                <div class="cgt-modal-header">
                    <h3><?php _e('Add Platform', 'cloud-gaming-tracker'); ?></h3>
                    <button class="cgt-modal-close">&times;</button>
                </div>
                <div class="cgt-modal-body">
                    <form id="cgt-platform-form">
                        <input type="hidden" name="platform_id" value="0">
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php _e('Platform Name', 'cloud-gaming-tracker'); ?> *</label>
                            <input type="text" name="name" class="cgt-form-input" required>
                        </div>
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php _e('Icon URL', 'cloud-gaming-tracker'); ?></label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" name="icon_url" class="cgt-form-input" style="flex: 1;">
                                <button type="button" class="cgt-btn cgt-btn-secondary cgt-upload-icon">
                                    <?php _e('Upload', 'cloud-gaming-tracker'); ?>
                                </button>
                            </div>
                            <div class="cgt-icon-preview" style="margin-top: 10px; width: 48px; height: 48px;"></div>
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Price', 'cloud-gaming-tracker'); ?></label>
                                <input type="text" name="price" class="cgt-form-input" placeholder="9.99">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Currency', 'cloud-gaming-tracker'); ?></label>
                                <select name="currency" class="cgt-form-select">
                                    <option value="$">$ USD</option>
                                    <option value="€">€ EUR</option>
                                    <option value="£">£ GBP</option>
                                </select>
                            </div>
                        </div>
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php _e('Plan Tier', 'cloud-gaming-tracker'); ?></label>
                            <input type="text" name="tier" class="cgt-form-input" placeholder="Premium or higher">
                        </div>
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php _e('Game Included?', 'cloud-gaming-tracker'); ?></label>
                            <label class="cgt-toggle" style="display: inline-block;">
                                <input type="checkbox" name="game_included" style="display: none;">
                            </label>
                            <span style="margin-left: 10px;"><?php _e('Games included with subscription', 'cloud-gaming-tracker'); ?></span>
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('CTA Text', 'cloud-gaming-tracker'); ?></label>
                                <input type="text" name="cta_text" class="cgt-form-input" value="Play Now">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('CTA URL', 'cloud-gaming-tracker'); ?></label>
                                <input type="url" name="cta_url" class="cgt-form-input">
                            </div>
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Display Order', 'cloud-gaming-tracker'); ?></label>
                                <input type="number" name="display_order" class="cgt-form-input" value="0">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Active', 'cloud-gaming-tracker'); ?></label>
                                <label class="cgt-toggle active" style="display: inline-block;">
                                    <input type="checkbox" name="is_active" checked style="display: none;">
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="cgt-modal-footer">
                    <button class="cgt-btn cgt-btn-secondary cgt-modal-close">
                        <?php _e('Cancel', 'cloud-gaming-tracker'); ?>
                    </button>
                    <button class="cgt-btn cgt-btn-primary cgt-save-platform" data-original-text="<?php _e('Save Platform', 'cloud-gaming-tracker'); ?>">
                        <?php _e('Save Platform', 'cloud-gaming-tracker'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render games page
     */
    public static function render_games_page() {
        $games = CGT_Database::get_games(array('active_only' => false));
        $platforms = CGT_Database::get_platforms();
        ?>
        <div class="cgt-admin-wrap">
            <div class="cgt-admin-header">
                <div class="cgt-admin-title">
                    <h1><?php _e('Manage Games', 'cloud-gaming-tracker'); ?></h1>
                </div>
            </div>
            <div class="cgt-admin-nav">
                <a href="<?php echo admin_url('admin.php?page=cgt-dashboard'); ?>"><?php _e('Dashboard', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-platforms'); ?>"><?php _e('Platforms', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-games'); ?>" class="current"><?php _e('Games', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-settings'); ?>"><?php _e('Settings', 'cloud-gaming-tracker'); ?></a>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php _e('Add New Game', 'cloud-gaming-tracker'); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <div style="display: flex; gap: 10px; max-width: 500px;">
                        <input type="text" id="cgt-new-game-name" class="cgt-form-input" placeholder="<?php _e('Enter game name...', 'cloud-gaming-tracker'); ?>">
                        <button class="cgt-btn cgt-btn-primary cgt-add-game">
                            <?php _e('Add Game', 'cloud-gaming-tracker'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php if (!empty($games)): ?>
            <?php foreach ($games as $game): ?>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2>
                        <?php echo esc_html($game['name']); ?>
                        <span class="cgt-badge <?php echo $game['is_active'] ? 'cgt-badge-success' : 'cgt-badge-danger'; ?>">
                            <?php echo $game['is_active'] ? __('Active', 'cloud-gaming-tracker') : __('Inactive', 'cloud-gaming-tracker'); ?>
                        </span>
                    </h2>
                    <div>
                        <button class="cgt-btn cgt-btn-success cgt-save-availability" data-game-id="<?php echo $game['id']; ?>">
                            <?php _e('Save Availability', 'cloud-gaming-tracker'); ?>
                        </button>
                        <button class="cgt-btn cgt-btn-danger cgt-delete-game" data-game-id="<?php echo $game['id']; ?>">
                            <?php _e('Delete Game', 'cloud-gaming-tracker'); ?>
                        </button>
                    </div>
                </div>
                <div class="cgt-admin-card-body">
                    <!-- Game Settings -->
                    <div class="cgt-game-settings" data-game-id="<?php echo $game['id']; ?>" style="margin-bottom: 20px; padding: 15px; background: #f3f4f6; border-radius: 8px;">
                        <h4 style="margin: 0 0 10px; font-size: 14px; color: #374151;"><?php _e('Game Settings', 'cloud-gaming-tracker'); ?></h4>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div>
                                <label class="cgt-form-label" style="margin-bottom: 5px;"><?php _e('Game Included (vs Own Game Required)', 'cloud-gaming-tracker'); ?></label>
                                <label class="cgt-toggle <?php echo $game['game_included'] ? 'active' : ''; ?>" style="display: inline-block;">
                                    <input type="checkbox" name="game_included" <?php echo $game['game_included'] ? 'checked' : ''; ?> style="display: none;">
                                </label>
                            </div>
                            <div>
                                <label class="cgt-form-label" style="margin-bottom: 5px;"><?php _e('Active', 'cloud-gaming-tracker'); ?></label>
                                <label class="cgt-toggle <?php echo $game['is_active'] ? 'active' : ''; ?>" style="display: inline-block;">
                                    <input type="checkbox" name="is_active" <?php echo $game['is_active'] ? 'checked' : ''; ?> style="display: none;">
                                </label>
                            </div>
                            <button class="cgt-btn cgt-btn-secondary cgt-save-game-settings" data-game-id="<?php echo $game['id']; ?>">
                                <?php _e('Save Settings', 'cloud-gaming-tracker'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <p style="margin-bottom: 20px; color: #6b7280;">
                        <?php _e('Toggle availability for each platform, then click "Save Availability":', 'cloud-gaming-tracker'); ?>
                    </p>
                    <div class="cgt-availability-grid" data-game-id="<?php echo $game['id']; ?>">
                        <?php foreach ($platforms as $platform): 
                            $availability = CGT_Database::get_game_availability($game['id'], $platform['id']);
                            $is_available = $availability ? $availability['is_available'] : 1;
                        ?>
                        <div class="cgt-availability-item" data-platform-id="<?php echo $platform['id']; ?>">
                            <div class="cgt-availability-info">
                                <div class="cgt-icon-preview" style="width: 40px; height: 40px;">
                                    <?php if ($platform['icon_url']): ?>
                                        <img src="<?php echo esc_url($platform['icon_url']); ?>" alt="">
                                    <?php else: ?>
                                        <span>🎮</span>
                                    <?php endif; ?>
                                </div>
                                <span><?php echo esc_html($platform['name']); ?></span>
                            </div>
                            <div class="cgt-availability-toggle">
                                <button class="cgt-avail-btn <?php echo $is_available ? 'active' : ''; ?>" data-status="1">
                                    <?php _e('Available', 'cloud-gaming-tracker'); ?>
                                </button>
                                <button class="cgt-avail-btn <?php echo !$is_available ? 'active' : ''; ?>" data-status="0">
                                    <?php _e('Unavailable', 'cloud-gaming-tracker'); ?>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 20px; padding: 15px; background: #dbeafe; border-radius: 8px; font-size: 13px;">
                        <strong><?php _e('Shortcode:', 'cloud-gaming-tracker'); ?></strong> 
                        <code>[cloud_gaming_tracker game_id="<?php echo $game['id']; ?>"]</code>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-body" style="text-align: center; padding: 40px;">
                    <p style="color: #6b7280; font-size: 16px;">
                        <?php _e('No games yet. Add your first game above!', 'cloud-gaming-tracker'); ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render settings page
     */
    public static function render_settings_page() {
        $settings = get_option('cgt_settings', array());
        $defaults = array(
            'theme' => 'auto',
            'primary_color' => '#3b82f6',
            'success_color' => '#10b981',
            'danger_color' => '#ef4444',
            'card_radius' => '6',
            'button_radius' => '4',
            'spacing' => '16',
            'show_price' => true,
            'show_tier' => true,
            'group_platforms' => true,
            'glassmorphism' => true,
        );
        $settings = wp_parse_args($settings, $defaults);
        ?>
        <div class="cgt-admin-wrap">
            <div class="cgt-admin-header">
                <div class="cgt-admin-title">
                    <h1><?php _e('Plugin Settings', 'cloud-gaming-tracker'); ?></h1>
                </div>
            </div>
            <div class="cgt-admin-nav">
                <a href="<?php echo admin_url('admin.php?page=cgt-dashboard'); ?>"><?php _e('Dashboard', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-platforms'); ?>"><?php _e('Platforms', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-games'); ?>"><?php _e('Games', 'cloud-gaming-tracker'); ?></a>
                <a href="<?php echo admin_url('admin.php?page=cgt-settings'); ?>" class="current"><?php _e('Settings', 'cloud-gaming-tracker'); ?></a>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php _e('Appearance', 'cloud-gaming-tracker'); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <form id="cgt-settings-form">
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Theme', 'cloud-gaming-tracker'); ?></label>
                                <select name="theme" class="cgt-form-select" id="cgt-theme-select">
                                    <option value="auto" <?php selected($settings['theme'], 'auto'); ?>><?php _e('Auto (System)', 'cloud-gaming-tracker'); ?></option>
                                    <option value="light" <?php selected($settings['theme'], 'light'); ?>><?php _e('Light', 'cloud-gaming-tracker'); ?></option>
                                    <option value="dark" <?php selected($settings['theme'], 'dark'); ?>><?php _e('Dark', 'cloud-gaming-tracker'); ?></option>
                                </select>
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Preview Theme', 'cloud-gaming-tracker'); ?></label>
                                <button type="button" class="cgt-btn cgt-btn-secondary" id="cgt-theme-preview-toggle">
                                    <?php _e('Toggle Preview', 'cloud-gaming-tracker'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Primary Color', 'cloud-gaming-tracker'); ?></label>
                                <div class="cgt-color-picker-wrapper">
                                    <input type="text" name="primary_color" class="cgt-color-picker" value="<?php echo esc_attr($settings['primary_color']); ?>">
                                </div>
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Success Color', 'cloud-gaming-tracker'); ?></label>
                                <div class="cgt-color-picker-wrapper">
                                    <input type="text" name="success_color" class="cgt-color-picker" value="<?php echo esc_attr($settings['success_color']); ?>">
                                </div>
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Danger Color', 'cloud-gaming-tracker'); ?></label>
                                <div class="cgt-color-picker-wrapper">
                                    <input type="text" name="danger_color" class="cgt-color-picker" value="<?php echo esc_attr($settings['danger_color']); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Card Radius (px)', 'cloud-gaming-tracker'); ?></label>
                                <input type="number" name="card_radius" class="cgt-form-input" value="<?php echo esc_attr($settings['card_radius']); ?>" min="0" max="50">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Button Radius (px)', 'cloud-gaming-tracker'); ?></label>
                                <input type="number" name="button_radius" class="cgt-form-input" value="<?php echo esc_attr($settings['button_radius']); ?>" min="0" max="50">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php _e('Spacing (px)', 'cloud-gaming-tracker'); ?></label>
                                <input type="number" name="spacing" class="cgt-form-input" value="<?php echo esc_attr($settings['spacing']); ?>" min="8" max="40">
                            </div>
                        </div>
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php _e('Glassmorphism Effect', 'cloud-gaming-tracker'); ?></label>
                            <label class="cgt-toggle <?php echo $settings['glassmorphism'] ? 'active' : ''; ?>" style="display: inline-block;">
                                <input type="checkbox" name="glassmorphism" <?php checked($settings['glassmorphism']); ?> style="display: none;">
                            </label>
                            <span style="margin-left: 10px;"><?php _e('Enable glass/blur effect on cards', 'cloud-gaming-tracker'); ?></span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php _e('Display Options', 'cloud-gaming-tracker'); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <form id="cgt-display-form">
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php _e('Show Elements', 'cloud-gaming-tracker'); ?></label>
                            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px;">
                                <label style="display: flex; align-items: center; gap: 10px;">
                                    <label class="cgt-toggle <?php echo $settings['show_price'] ? 'active' : ''; ?>" style="display: inline-block; margin: 0;">
                                        <input type="checkbox" name="show_price" <?php checked($settings['show_price']); ?> style="display: none;">
                                    </label>
                                    <span><?php _e('Show Price', 'cloud-gaming-tracker'); ?></span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 10px;">
                                    <label class="cgt-toggle <?php echo $settings['show_tier'] ? 'active' : ''; ?>" style="display: inline-block; margin: 0;">
                                        <input type="checkbox" name="show_tier" <?php checked($settings['show_tier']); ?> style="display: none;">
                                    </label>
                                    <span><?php _e('Show Plan Tier', 'cloud-gaming-tracker'); ?></span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 10px;">
                                    <label class="cgt-toggle <?php echo $settings['group_platforms'] ? 'active' : ''; ?>" style="display: inline-block; margin: 0;">
                                        <input type="checkbox" name="group_platforms" <?php checked($settings['group_platforms']); ?> style="display: none;">
                                    </label>
                                    <span><?php _e('Group by Availability', 'cloud-gaming-tracker'); ?></span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-body">
                    <button class="cgt-btn cgt-btn-primary cgt-save-settings" data-original-text="<?php _e('Save Settings', 'cloud-gaming-tracker'); ?>">
                        <?php _e('Save Settings', 'cloud-gaming-tracker'); ?>
                    </button>
                </div>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php _e('Shortcode Usage', 'cloud-gaming-tracker'); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <div style="background: #f9fafb; padding: 20px; border-radius: 8px; font-family: monospace;">
                        <p><strong><?php _e('Basic:', 'cloud-gaming-tracker'); ?></strong></p>
                        <code>[cloud_gaming_tracker]</code>
                        <p style="margin-top: 15px;"><strong><?php _e('Specific Game:', 'cloud-gaming-tracker'); ?></strong></p>
                        <code>[cloud_gaming_tracker game_id="1"]</code>
                        <p style="margin-top: 15px;"><strong><?php _e('Hide Unavailable:', 'cloud-gaming-tracker'); ?></strong></p>
                        <code>[cloud_gaming_tracker game_id="1" show_unavailable="false"]</code>
                    </div>
                    <p style="margin-top: 20px; color: #6b7280;">
                        <?php _e('Or use the Gutenberg block "Cloud Gaming Tracker" in the block editor.', 'cloud-gaming-tracker'); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: Save platform
     */
    public static function ajax_save_platform() {
        check_ajax_referer('cgt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'cloud-gaming-tracker')));
        }
        
        $platform_id = isset($_POST['platform_id']) ? intval($_POST['platform_id']) : 0;
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'icon_url' => esc_url_raw($_POST['icon_url']),
            'game_included' => isset($_POST['game_included']) ? 1 : 0,
            'price' => sanitize_text_field($_POST['price']),
            'currency' => sanitize_text_field($_POST['currency']),
            'tier' => sanitize_text_field($_POST['tier']),
            'cta_text' => sanitize_text_field($_POST['cta_text']),
            'cta_url' => esc_url_raw($_POST['cta_url']),
            'display_order' => intval($_POST['display_order']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        );
        
        if ($platform_id > 0) {
            CGT_Database::update_platform($platform_id, $data);
        } else {
            $platform_id = CGT_Database::add_platform($data);
        }
        
        wp_send_json_success(array('platform_id' => $platform_id, 'message' => __('Platform saved', 'cloud-gaming-tracker')));
    }
    
    /**
     * AJAX: Delete platform
     */
    public static function ajax_delete_platform() {
        check_ajax_referer('cgt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'cloud-gaming-tracker')));
        }
        $platform_id = isset($_POST['platform_id']) ? intval($_POST['platform_id']) : 0;
        if ($platform_id <= 0) {
            wp_send_json_error(array('message' => __('Invalid ID', 'cloud-gaming-tracker')));
        }
        CGT_Database::delete_platform($platform_id);
        wp_send_json_success(array('message' => __('Deleted', 'cloud-gaming-tracker')));
    }
    
    /**
     * AJAX: Save availability (FIXED)
     */
    public static function ajax_save_availability() {
        check_ajax_referer('cgt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'cloud-gaming-tracker')));
        }
        
        $game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;
        $availability_data = isset($_POST['availability']) ? $_POST['availability'] : array();
        
        if ($game_id <= 0) {
            wp_send_json_error(array('message' => __('Invalid game ID', 'cloud-gaming-tracker')));
        }
        
        $saved = 0;
        if (!empty($availability_data) && is_array($availability_data)) {
            foreach ($availability_data as $platform_id => $is_available) {
                $result = CGT_Database::save_game_availability($game_id, intval($platform_id), intval($is_available));
                if ($result !== false) {
                    $saved++;
                }
            }
        }
        
        wp_send_json_success(array('message' => sprintf(__('%d platforms updated', 'cloud-gaming-tracker'), $saved)));
    }
    
    /**
     * AJAX: Save game settings
     */
    public static function ajax_save_game_settings() {
        check_ajax_referer('cgt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'cloud-gaming-tracker')));
        }
        
        $game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;
        $game_included = isset($_POST['game_included']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if ($game_id <= 0) {
            wp_send_json_error(array('message' => __('Invalid game ID', 'cloud-gaming-tracker')));
        }
        
        $data = array(
            'game_included' => $game_included,
            'is_active' => $is_active
        );
        
        CGT_Database::update_game($game_id, $data);
        
        wp_send_json_success(array('message' => __('Game settings saved', 'cloud-gaming-tracker')));
    }
    
    /**
     * AJAX: Add game
     */
    public static function ajax_add_game() {
        check_ajax_referer('cgt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'cloud-gaming-tracker')));
        }
        $game_name = sanitize_text_field($_POST['game_name']);
        $game_slug = sanitize_title($_POST['game_name']);
        global $wpdb;
        $wpdb->insert($wpdb->prefix . 'cgt_games', array('name' => $game_name, 'slug' => $game_slug, 'is_active' => 1, 'game_included' => 0));
        wp_send_json_success(array('game_id' => $wpdb->insert_id, 'message' => __('Game added', 'cloud-gaming-tracker')));
    }
    
    /**
     * AJAX: Delete game
     */
    public static function ajax_delete_game() {
        check_ajax_referer('cgt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'cloud-gaming-tracker')));
        }
        $game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;
        if ($game_id <= 0) {
            wp_send_json_error(array('message' => __('Invalid ID', 'cloud-gaming-tracker')));
        }
        CGT_Database::delete_game($game_id);
        wp_send_json_success(array('message' => __('Deleted', 'cloud-gaming-tracker')));
    }
    
    /**
     * AJAX: Save settings
     */
    public static function ajax_save_settings() {
        check_ajax_referer('cgt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'cloud-gaming-tracker')));
        }
        $settings = array(
            'theme' => sanitize_text_field($_POST['theme']),
            'primary_color' => sanitize_hex_color($_POST['primary_color']),
            'success_color' => sanitize_hex_color($_POST['success_color']),
            'danger_color' => sanitize_hex_color($_POST['danger_color']),
            'card_radius' => intval($_POST['card_radius']),
            'button_radius' => intval($_POST['button_radius']),
            'spacing' => intval($_POST['spacing']),
            'show_price' => isset($_POST['show_price']) ? 1 : 0,
            'show_tier' => isset($_POST['show_tier']) ? 1 : 0,
            'group_platforms' => isset($_POST['group_platforms']) ? 1 : 0,
            'glassmorphism' => isset($_POST['glassmorphism']) ? 1 : 0,
        );
        update_option('cgt_settings', $settings);
        wp_send_json_success(array('message' => __('Settings saved', 'cloud-gaming-tracker')));
    }
}
