<?php
/**
 * Main Plugin Class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Cloud_Gaming_Tracker
 */
final class Cloud_Gaming_Tracker extends CGT_Database {
    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

        add_action( 'wp_ajax_cgt_save_platform', array( $this, 'ajax_save_platform' ) );
        add_action( 'wp_ajax_cgt_delete_platform', array( $this, 'ajax_delete_platform' ) );
        add_action( 'wp_ajax_cgt_save_availability', array( $this, 'ajax_save_availability' ) );
        add_action( 'wp_ajax_cgt_save_settings', array( $this, 'ajax_save_settings' ) );
        add_action( 'wp_ajax_cgt_add_game', array( $this, 'ajax_add_game' ) );
        add_action( 'wp_ajax_cgt_delete_game', array( $this, 'ajax_delete_game' ) );
        add_action( 'wp_ajax_cgt_update_unavailable_url', array( $this, 'ajax_update_unavailable_url' ) );

        add_shortcode( 'cloud_gaming_tracker', array( $this, 'render_shortcode' ) );
        add_action( 'init', array( $this, 'register_block' ) );
    }

    public function activate() {
        self::create_tables();
        self::insert_default_platforms();
        self::upgrade_database();

        $default_settings = array(
            'theme'               => 'auto',
            'primary_color'      => '#3b82f6',
            'success_color'      => '#10b981',
            'danger_color'       => '#ef4444',
            'card_radius'        => '3',
            'button_radius'      => '2',
            'spacing'            => '16',
            'show_price'         => 1,
            'show_tier'          => 1,
            'group_platforms'    => 1,
            'glassmorphism'      => 1,
            'show_group_titles'  => 1,
            'available_title'    => 'Available Platforms',
            'unavailable_title'  => 'Not Available',
            'title_font_size'    => 24,
            'unavailable_button_text' => 'Stay Tuned',
        );

        if ( ! get_option( 'cgt_settings' ) ) {
            add_option( 'cgt_settings', $default_settings );
        }

        flush_rewrite_rules();
    }

    private function upgrade_database() {
        global $wpdb;
        
        // Add plan_tier column to game_platforms table if it doesn't exist
        $game_platforms_table = $wpdb->prefix . 'cgt_game_platforms';
        $plan_tier_exists = $wpdb->get_var( $wpdb->prepare( "
            SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = %s 
            AND TABLE_NAME = %s 
            AND COLUMN_NAME = 'plan_tier'
        ", DB_NAME, $game_platforms_table ) );
        
        if ( ! $plan_tier_exists ) {
            $wpdb->query( "ALTER TABLE $game_platforms_table ADD COLUMN plan_tier varchar(100) DEFAULT '' AFTER game_included_override" );
        }
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'cloud-gaming-tracker', false, dirname( CGT_PLUGIN_BASENAME ) . '/languages' );
    }

    public function add_admin_menu() {
        add_menu_page(
            esc_html__( 'Cloud Gaming Tracker', 'cloud-gaming-tracker' ),
            esc_html__( 'Cloud Gaming', 'cloud-gaming-tracker' ),
            'manage_options',
            'cgt-dashboard',
            array( $this, 'render_admin_dashboard' ),
            'dashicons-cloud',
            30
        );

        add_submenu_page( 'cgt-dashboard', esc_html__( 'Platforms', 'cloud-gaming-tracker' ), esc_html__( 'Platforms', 'cloud-gaming-tracker' ), 'manage_options', 'cgt-platforms', array( $this, 'render_platforms_page' ) );
        add_submenu_page( 'cgt-dashboard', esc_html__( 'Games', 'cloud-gaming-tracker' ), esc_html__( 'Games', 'cloud-gaming-tracker' ), 'manage_options', 'cgt-games', array( $this, 'render_games_page' ) );
        add_submenu_page( 'cgt-dashboard', esc_html__( 'Settings', 'cloud-gaming-tracker' ), esc_html__( 'Settings', 'cloud-gaming-tracker' ), 'manage_options', 'cgt-settings', array( $this, 'render_settings_page' ) );
    }

    public function render_admin_dashboard() {
        $platforms       = self::get_platforms( array( 'active_only' => false ) );
        $games           = self::get_games( array( 'active_only' => false ) );
        $active_platforms = array_filter( $platforms, fn( $p ) => $p['is_active'] );
        $active_games     = array_filter( $games, fn( $g ) => $g['is_active'] );
        ?>
        <div class="cgt-admin-wrap">
            <div class="cgt-admin-header">
                <div class="cgt-admin-title">
                    <h1>☁️ <?php esc_html_e( 'Cloud Gaming Tracker', 'cloud-gaming-tracker' ); ?></h1>
                    <span class="version-badge">v<?php echo esc_html( CGT_VERSION ); ?></span>
                </div>
                <div class="cgt-admin-actions">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-platforms' ) ); ?>" class="cgt-btn cgt-btn-primary">
                        <?php esc_html_e( 'Manage Platforms', 'cloud-gaming-tracker' ); ?>
                    </a>
                </div>
            </div>
            <div class="cgt-admin-nav">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-dashboard' ) ); ?>" class="current"><?php esc_html_e( 'Dashboard', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-platforms' ) ); ?>"><?php esc_html_e( 'Platforms', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-games' ) ); ?>"><?php esc_html_e( 'Games', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-settings' ) ); ?>"><?php esc_html_e( 'Settings', 'cloud-gaming-tracker' ); ?></a>
            </div>
            <div class="cgt-stats-grid">
                <div class="cgt-stat-card">
                    <div class="cgt-stat-value"><?php echo esc_html( count( $active_platforms ) ); ?></div>
                    <div class="cgt-stat-label"><?php esc_html_e( 'Active Platforms', 'cloud-gaming-tracker' ); ?></div>
                </div>
                <div class="cgt-stat-card">
                    <div class="cgt-stat-value"><?php echo esc_html( count( $active_games ) ); ?></div>
                    <div class="cgt-stat-label"><?php esc_html_e( 'Active Games', 'cloud-gaming-tracker' ); ?></div>
                </div>
                <div class="cgt-stat-card">
                    <div class="cgt-stat-value"><?php echo esc_html( count( $platforms ) ); ?></div>
                    <div class="cgt-stat-label"><?php esc_html_e( 'Total Platforms', 'cloud-gaming-tracker' ); ?></div>
                </div>
                <div class="cgt-stat-card">
                    <div class="cgt-stat-value"><?php echo esc_html( count( $games ) ); ?></div>
                    <div class="cgt-stat-label"><?php esc_html_e( 'Total Games', 'cloud-gaming-tracker' ); ?></div>
                </div>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php esc_html_e( 'Quick Start Guide', 'cloud-gaming-tracker' ); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <ol style="line-height: 2;">
                        <li><strong><?php esc_html_e( 'Platforms:', 'cloud-gaming-tracker' ); ?></strong> <?php esc_html_e( '9 cloud gaming platforms pre-configured. Add more from Platforms page.', 'cloud-gaming-tracker' ); ?></li>
                        <li><strong><?php esc_html_e( 'Games:', 'cloud-gaming-tracker' ); ?></strong> <?php esc_html_e( 'Create game entries and set platform availability.', 'cloud-gaming-tracker' ); ?></li>
                        <li><strong><?php esc_html_e( 'Embed:', 'cloud-gaming-tracker' ); ?></strong> <?php esc_html_e( 'Use shortcode', 'cloud-gaming-tracker' ); ?> <code>[cloud_gaming_tracker game_id="1"]</code> <?php esc_html_e( 'or Gutenberg block.', 'cloud-gaming-tracker' ); ?></li>
                    </ol>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_platforms_page() {
        $platforms = self::get_platforms( array( 'active_only' => false ) );
        ?>
        <div class="cgt-admin-wrap">
            <div class="cgt-admin-header">
                <div class="cgt-admin-title">
                    <h1><?php esc_html_e( 'Manage Platforms', 'cloud-gaming-tracker' ); ?></h1>
                </div>
                <button class="cgt-btn cgt-btn-primary cgt-add-platform">
                    <span>+</span> <?php esc_html_e( 'Add Platform', 'cloud-gaming-tracker' ); ?>
                </button>
            </div>
            <div class="cgt-admin-nav">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-dashboard' ) ); ?>"><?php esc_html_e( 'Dashboard', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-platforms' ) ); ?>" class="current"><?php esc_html_e( 'Platforms', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-games' ) ); ?>"><?php esc_html_e( 'Games', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-settings' ) ); ?>"><?php esc_html_e( 'Settings', 'cloud-gaming-tracker' ); ?></a>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-body">
                    <table class="cgt-table">
                        <thead>
                             <tr>
                                <th><?php esc_html_e( 'Platform', 'cloud-gaming-tracker' ); ?></th>
                                <th><?php esc_html_e( 'Icon', 'cloud-gaming-tracker' ); ?></th>
                                <th><?php esc_html_e( 'Price', 'cloud-gaming-tracker' ); ?></th>
                                <th><?php esc_html_e( 'Game', 'cloud-gaming-tracker' ); ?></th>
                                <th><?php esc_html_e( 'Unavailable URL', 'cloud-gaming-tracker' ); ?></th>
                                <th><?php esc_html_e( 'Status', 'cloud-gaming-tracker' ); ?></th>
                                <th><?php esc_html_e( 'Order', 'cloud-gaming-tracker' ); ?></th>
                                <th><?php esc_html_e( 'Actions', 'cloud-gaming-tracker' ); ?></th>
                             </tr>
                        </thead>
                        <tbody>
                            <?php if ( ! empty( $platforms ) ) : ?>
                            <?php foreach ( $platforms as $platform ) : ?>
                            <tr class="cgt-platform-row" 
                                data-platform-id="<?php echo esc_attr( $platform['id'] ); ?>"
                                data-name="<?php echo esc_attr( $platform['name'] ); ?>"
                                data-icon="<?php echo esc_attr( $platform['icon_url'] ); ?>"
                                data-game-included="<?php echo esc_attr( $platform['game_included'] ); ?>"
                                data-price="<?php echo esc_attr( $platform['price'] ); ?>"
                                data-currency="<?php echo esc_attr( $platform['currency'] ); ?>"
                                data-cta-text="<?php echo esc_attr( $platform['cta_text'] ); ?>"
                                data-cta-url="<?php echo esc_attr( $platform['cta_url'] ); ?>"
                                data-unavailable-url="<?php echo esc_attr( $platform['unavailable_url'] ?? '' ); ?>"
                                data-order="<?php echo esc_attr( $platform['display_order'] ); ?>"
                                data-active="<?php echo esc_attr( $platform['is_active'] ); ?>">
                                <td><?php echo esc_html( $platform['name'] ); ?></td>
                                <td>
                                    <div class="cgt-icon-preview">
                                        <?php if ( ! empty( $platform['icon_url'] ) ) : ?>
                                            <img src="<?php echo esc_url( $platform['icon_url'] ); ?>" alt="">
                                        <?php else : ?>
                                            <span>🎮</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo esc_html( $platform['currency'] . $platform['price'] ); ?></td>
                                <td>
                                    <span class="cgt-badge <?php echo $platform['game_included'] ? 'cgt-badge-success' : 'cgt-badge-warning'; ?>">
                                        <?php echo $platform['game_included'] ? esc_html__( 'Included', 'cloud-gaming-tracker' ) : esc_html__( 'Required', 'cloud-gaming-tracker' ); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ( ! empty( $platform['unavailable_url'] ) ) : ?>
                                        <a href="<?php echo esc_url( $platform['unavailable_url'] ); ?>" target="_blank" rel="noopener noreferrer">
                                            <?php echo esc_html( substr( $platform['unavailable_url'], 0, 30 ) ) . ( strlen( $platform['unavailable_url'] ) > 30 ? '...' : '' ); ?>
                                        </a>
                                    <?php else : ?>
                                        <span style="color: #9ca3af;"><?php esc_html_e( 'Not set', 'cloud-gaming-tracker' ); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="cgt-badge <?php echo $platform['is_active'] ? 'cgt-badge-success' : 'cgt-badge-danger'; ?>">
                                        <?php echo $platform['is_active'] ? esc_html__( 'Active', 'cloud-gaming-tracker' ) : esc_html__( 'Inactive', 'cloud-gaming-tracker' ); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html( $platform['display_order'] ); ?></td>
                                <td class="cgt-table-actions">
                                    <button class="cgt-btn cgt-btn-secondary cgt-edit-platform" data-platform-id="<?php echo esc_attr( $platform['id'] ); ?>">
                                        <?php esc_html_e( 'Edit', 'cloud-gaming-tracker' ); ?>
                                    </button>
                                    <button class="cgt-btn cgt-btn-danger cgt-delete-platform" data-platform-id="<?php echo esc_attr( $platform['id'] ); ?>">
                                        <?php esc_html_e( 'Delete', 'cloud-gaming-tracker' ); ?>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else : ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">
                                    <?php esc_html_e( 'No platforms yet. Click "Add Platform" to get started!', 'cloud-gaming-tracker' ); ?>
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
                    <h3><?php esc_html_e( 'Add Platform', 'cloud-gaming-tracker' ); ?></h3>
                    <button class="cgt-modal-close">&times;</button>
                </div>
                <div class="cgt-modal-body">
                    <form id="cgt-platform-form">
                        <input type="hidden" name="platform_id" value="0">
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php esc_html_e( 'Platform Name', 'cloud-gaming-tracker' ); ?> *</label>
                            <input type="text" name="name" class="cgt-form-input" required>
                        </div>
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php esc_html_e( 'Icon URL', 'cloud-gaming-tracker' ); ?></label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" name="icon_url" class="cgt-form-input" style="flex: 1;">
                                <button type="button" class="cgt-btn cgt-btn-secondary cgt-upload-icon">
                                    <?php esc_html_e( 'Upload', 'cloud-gaming-tracker' ); ?>
                                </button>
                            </div>
                            <div class="cgt-icon-preview" style="margin-top: 10px; width: 48px; height: 48px;"></div>
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Price', 'cloud-gaming-tracker' ); ?></label>
                                <input type="text" name="price" class="cgt-form-input" placeholder="9.99">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Currency', 'cloud-gaming-tracker' ); ?></label>
                                <select name="currency" class="cgt-form-select">
                                    <option value="$">$ USD</option>
                                    <option value="€">€ EUR</option>
                                    <option value="£">£ GBP</option>
                                </select>
                            </div>
                        </div>
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php esc_html_e( 'Unavailable URL', 'cloud-gaming-tracker' ); ?></label>
                            <input type="url" name="unavailable_url" class="cgt-form-input" placeholder="https://example.com/stay-tuned">
                            <span style="display: block; font-size: 12px; color: #6b7280; margin-top: 5px;"><?php esc_html_e( 'URL to redirect when platform is not available for a game', 'cloud-gaming-tracker' ); ?></span>
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'CTA Text', 'cloud-gaming-tracker' ); ?></label>
                                <input type="text" name="cta_text" class="cgt-form-input" value="Play Now">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'CTA URL', 'cloud-gaming-tracker' ); ?></label>
                                <input type="url" name="cta_url" class="cgt-form-input">
                            </div>
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Display Order', 'cloud-gaming-tracker' ); ?></label>
                                <input type="number" name="display_order" class="cgt-form-input" value="0">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Active', 'cloud-gaming-tracker' ); ?></label>
                                <input type="checkbox" name="is_active" checked style="margin-right: 8px;">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="cgt-modal-footer">
                    <button class="cgt-btn cgt-btn-secondary cgt-modal-close">
                        <?php esc_html_e( 'Cancel', 'cloud-gaming-tracker' ); ?>
                    </button>
                    <button class="cgt-btn cgt-btn-primary cgt-save-platform" data-original-text="<?php esc_html_e( 'Save Platform', 'cloud-gaming-tracker' ); ?>">
                        <?php esc_html_e( 'Save Platform', 'cloud-gaming-tracker' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_games_page() {
        $games     = self::get_games( array( 'active_only' => false ) );
        $platforms = self::get_platforms();
        ?>
        <div class="cgt-admin-wrap">
            <div class="cgt-admin-header">
                <div class="cgt-admin-title">
                    <h1><?php esc_html_e( 'Manage Games', 'cloud-gaming-tracker' ); ?></h1>
                </div>
            </div>
            <div class="cgt-admin-nav">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-dashboard' ) ); ?>"><?php esc_html_e( 'Dashboard', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-platforms' ) ); ?>"><?php esc_html_e( 'Platforms', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-games' ) ); ?>" class="current"><?php esc_html_e( 'Games', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-settings' ) ); ?>"><?php esc_html_e( 'Settings', 'cloud-gaming-tracker' ); ?></a>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php esc_html_e( 'Add New Game', 'cloud-gaming-tracker' ); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <div style="display: flex; gap: 10px; max-width: 500px;">
                        <input type="text" id="cgt-new-game-name" class="cgt-form-input" style="flex: 1;" placeholder="<?php esc_attr_e( 'Enter game name...', 'cloud-gaming-tracker' ); ?>">
                        <button class="cgt-btn cgt-btn-primary cgt-add-game">
                            <?php esc_html_e( 'Add Game', 'cloud-gaming-tracker' ); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php if ( ! empty( $games ) ) : ?>
            <?php foreach ( $games as $game ) : ?>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2>
                        <?php echo esc_html( $game['name'] ); ?>
                        <span class="cgt-badge <?php echo $game['is_active'] ? 'cgt-badge-success' : 'cgt-badge-danger'; ?>">
                            <?php echo $game['is_active'] ? esc_html__( 'Active', 'cloud-gaming-tracker' ) : esc_html__( 'Inactive', 'cloud-gaming-tracker' ); ?>
                        </span>
                    </h2>
                    <div>
                        <button class="cgt-btn cgt-btn-success cgt-save-availability" data-game-id="<?php echo esc_attr( $game['id'] ); ?>">
                            <?php esc_html_e( 'Save Availability', 'cloud-gaming-tracker' ); ?>
                        </button>
                        <button class="cgt-btn cgt-btn-danger cgt-delete-game" data-game-id="<?php echo esc_attr( $game['id'] ); ?>">
                            <?php esc_html_e( 'Delete Game', 'cloud-gaming-tracker' ); ?>
                        </button>
                    </div>
                </div>
                <div class="cgt-admin-card-body">
                    <p style="margin-bottom: 20px; color: #6b7280;">
                        <?php esc_html_e( 'Set availability, plan tier, and game status for each platform, then click "Save Availability":', 'cloud-gaming-tracker' ); ?>
                    </p>
                    <div class="cgt-availability-grid" data-game-id="<?php echo esc_attr( $game['id'] ); ?>">
                        <?php foreach ( $platforms as $platform ) : 
                            $availability              = self::get_game_availability( $game['id'], $platform['id'] );
                            $is_available              = $availability ? $availability['is_available'] : 1;
                            $game_included_override    = $availability ? $availability['game_included_override'] : -1;
                            $plan_tier                 = $availability ? $availability['plan_tier'] : '';
                        ?>
                        <div class="cgt-availability-item" data-platform-id="<?php echo esc_attr( $platform['id'] ); ?>">
                            <div class="cgt-availability-info">
                                <div class="cgt-icon-preview" style="width: 40px; height: 40px;">
                                    <?php if ( ! empty( $platform['icon_url'] ) ) : ?>
                                        <img src="<?php echo esc_url( $platform['icon_url'] ); ?>" alt="">
                                    <?php else : ?>
                                        <span>🎮</span>
                                    <?php endif; ?>
                                </div>
                                <span><?php echo esc_html( $platform['name'] ); ?></span>
                            </div>
                            <div class="cgt-availability-controls">
                                <div class="cgt-availability-toggle">
                                    <button class="cgt-avail-btn cgt-avail-available <?php echo $is_available ? 'active' : ''; ?>" data-status="1" data-type="availability">
                                        <?php esc_html_e( 'Available', 'cloud-gaming-tracker' ); ?>
                                    </button>
                                    <button class="cgt-avail-btn cgt-avail-unavailable <?php echo ! $is_available ? 'active' : ''; ?>" data-status="0" data-type="availability">
                                        <?php esc_html_e( 'Unavailable', 'cloud-gaming-tracker' ); ?>
                                    </button>
                                </div>
                                <div class="cgt-availability-toggle">
                                    <input type="text" class="cgt-plan-tier-input" data-type="plan_tier" placeholder="<?php esc_attr_e( 'Enter plan...', 'cloud-gaming-tracker' ); ?>" value="<?php echo esc_attr( $plan_tier ); ?>" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #e5e7eb; font-size: 13px; min-width: 140px;">
                                </div>
                                <div class="cgt-availability-toggle">
                                    <button class="cgt-avail-btn cgt-avail-included <?php echo $game_included_override == 1 ? 'active' : ''; ?>" data-status="1" data-type="game_included">
                                        <?php esc_html_e( 'Included', 'cloud-gaming-tracker' ); ?>
                                    </button>
                                    <button class="cgt-avail-btn cgt-avail-required <?php echo $game_included_override == 0 ? 'active' : ''; ?>" data-status="0" data-type="game_included">
                                        <?php esc_html_e( 'Required', 'cloud-gaming-tracker' ); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 20px; padding: 15px; background: #dbeafe; border-radius: 6px; font-size: 13px;">
                        <strong><?php esc_html_e( 'Shortcode:', 'cloud-gaming-tracker' ); ?></strong> 
                        <code>[cloud_gaming_tracker game_id="<?php echo esc_attr( $game['id'] ); ?>"]</code>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else : ?>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-body" style="text-align: center; padding: 40px;">
                    <p style="color: #6b7280; font-size: 16px;">
                        <?php esc_html_e( 'No games yet. Add your first game above!', 'cloud-gaming-tracker' ); ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    public function render_settings_page() {
        $settings = get_option( 'cgt_settings', array() );
        $defaults = array(
            'theme'           => 'auto',
            'primary_color'   => '#3b82f6',
            'success_color'   => '#10b981',
            'danger_color'    => '#ef4444',
            'card_radius'     => '6',
            'button_radius'   => '4',
            'spacing'         => '16',
            'show_price'      => true,
            'show_tier'       => true,
            'group_platforms' => true,
            'glassmorphism'   => true,
        );
        $settings = wp_parse_args( $settings, $defaults );
        ?>
        <div class="cgt-admin-wrap">
            <div class="cgt-admin-header">
                <div class="cgt-admin-title">
                    <h1><?php esc_html_e( 'Plugin Settings', 'cloud-gaming-tracker' ); ?></h1>
                </div>
            </div>
            <div class="cgt-admin-nav">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-dashboard' ) ); ?>"><?php esc_html_e( 'Dashboard', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-platforms' ) ); ?>"><?php esc_html_e( 'Platforms', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-games' ) ); ?>"><?php esc_html_e( 'Games', 'cloud-gaming-tracker' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgt-settings' ) ); ?>" class="current"><?php esc_html_e( 'Settings', 'cloud-gaming-tracker' ); ?></a>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php esc_html_e( 'Appearance', 'cloud-gaming-tracker' ); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <form id="cgt-settings-form">
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Theme', 'cloud-gaming-tracker' ); ?></label>
                                <select name="theme" class="cgt-form-select" id="cgt-theme-select">
                                    <option value="auto" <?php selected( $settings['theme'], 'auto' ); ?>><?php esc_html_e( 'Auto (System)', 'cloud-gaming-tracker' ); ?></option>
                                    <option value="light" <?php selected( $settings['theme'], 'light' ); ?>><?php esc_html_e( 'Light', 'cloud-gaming-tracker' ); ?></option>
                                    <option value="dark" <?php selected( $settings['theme'], 'dark' ); ?>><?php esc_html_e( 'Dark', 'cloud-gaming-tracker' ); ?></option>
                                </select>
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Preview Theme', 'cloud-gaming-tracker' ); ?></label>
                                <button type="button" class="cgt-btn cgt-btn-secondary" id="cgt-theme-preview-toggle">
                                    <?php esc_html_e( 'Toggle Preview', 'cloud-gaming-tracker' ); ?>
                                </button>
                            </div>
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Primary Color', 'cloud-gaming-tracker' ); ?></label>
                                <div class="cgt-color-picker-wrapper">
                                    <input type="text" name="primary_color" class="cgt-color-picker" value="<?php echo esc_attr( $settings['primary_color'] ); ?>">
                                </div>
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Success Color', 'cloud-gaming-tracker' ); ?></label>
                                <div class="cgt-color-picker-wrapper">
                                    <input type="text" name="success_color" class="cgt-color-picker" value="<?php echo esc_attr( $settings['success_color'] ); ?>">
                                </div>
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Danger Color', 'cloud-gaming-tracker' ); ?></label>
                                <div class="cgt-color-picker-wrapper">
                                    <input type="text" name="danger_color" class="cgt-color-picker" value="<?php echo esc_attr( $settings['danger_color'] ); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Card Radius (px)', 'cloud-gaming-tracker' ); ?></label>
                                <input type="number" name="card_radius" class="cgt-form-input" value="<?php echo esc_attr( $settings['card_radius'] ); ?>" min="0" max="50">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Button Radius (px)', 'cloud-gaming-tracker' ); ?></label>
                                <input type="number" name="button_radius" class="cgt-form-input" value="<?php echo esc_attr( $settings['button_radius'] ); ?>" min="0" max="50">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Spacing (px)', 'cloud-gaming-tracker' ); ?></label>
                                <input type="number" name="spacing" class="cgt-form-input" value="<?php echo esc_attr( $settings['spacing'] ); ?>" min="8" max="40">
                            </div>
                        </div>
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php esc_html_e( 'Glassmorphism Effect', 'cloud-gaming-tracker' ); ?></label>
                            <input type="checkbox" name="glassmorphism" <?php checked( $settings['glassmorphism'] ); ?> style="margin-right: 8px;">
                            <span><?php esc_html_e( 'Enable glass/blur effect on cards', 'cloud-gaming-tracker' ); ?></span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php esc_html_e( 'Group Titles', 'cloud-gaming-tracker' ); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <form id="cgt-titles-form">
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php esc_html_e( 'Available Platforms Title', 'cloud-gaming-tracker' ); ?></label>
                            <input type="text" name="available_title" class="cgt-form-input" value="<?php echo esc_attr( $settings['available_title'] ?? 'Available Platforms' ); ?>">
                        </div>
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php esc_html_e( 'Not Available Title', 'cloud-gaming-tracker' ); ?></label>
                            <input type="text" name="unavailable_title" class="cgt-form-input" value="<?php echo esc_attr( $settings['unavailable_title'] ?? 'Not Available' ); ?>">
                        </div>
                        <div class="cgt-form-row">
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Title Font Size (px)', 'cloud-gaming-tracker' ); ?></label>
                                <input type="number" name="title_font_size" class="cgt-form-input" value="<?php echo esc_attr( $settings['title_font_size'] ?? 24 ); ?>" min="12" max="48">
                            </div>
                            <div class="cgt-form-group">
                                <label class="cgt-form-label"><?php esc_html_e( 'Show Group Titles', 'cloud-gaming-tracker' ); ?></label>
                                <select name="show_group_titles" class="cgt-form-select">
                                    <option value="1" <?php selected( $settings['show_group_titles'] ?? 1, 1 ); ?>><?php esc_html_e( 'Yes', 'cloud-gaming-tracker' ); ?></option>
                                    <option value="0" <?php selected( $settings['show_group_titles'] ?? 1, 0 ); ?>><?php esc_html_e( 'No', 'cloud-gaming-tracker' ); ?></option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php esc_html_e( 'Unavailable Button', 'cloud-gaming-tracker' ); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <form id="cgt-unavailable-form">
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php esc_html_e( 'Stay Tuned Button Text', 'cloud-gaming-tracker' ); ?></label>
                            <input type="text" name="unavailable_button_text" class="cgt-form-input" value="<?php echo esc_attr( $settings['unavailable_button_text'] ?? 'Stay Tuned' ); ?>">
                        </div>
                    </form>
                </div>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php esc_html_e( 'Display Options', 'cloud-gaming-tracker' ); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <form id="cgt-display-form">
                        <div class="cgt-form-group">
                            <label class="cgt-form-label"><?php esc_html_e( 'Show Elements', 'cloud-gaming-tracker' ); ?></label>
                            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px;">
                                <label style="display: flex; align-items: center; gap: 10px;">
                                    <input type="checkbox" name="show_price" <?php checked( $settings['show_price'] ); ?> style="margin-right: 8px;">
                                    <span><?php esc_html_e( 'Show Price', 'cloud-gaming-tracker' ); ?></span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 10px;">
                                    <input type="checkbox" name="show_tier" <?php checked( $settings['show_tier'] ); ?> style="margin-right: 8px;">
                                    <span><?php esc_html_e( 'Show Plan Tier', 'cloud-gaming-tracker' ); ?></span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 10px;">
                                    <input type="checkbox" name="group_platforms" <?php checked( $settings['group_platforms'] ); ?> style="margin-right: 8px;">
                                    <span><?php esc_html_e( 'Group by Availability', 'cloud-gaming-tracker' ); ?></span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-body">
                    <button class="cgt-btn cgt-btn-primary cgt-save-settings" data-original-text="<?php esc_html_e( 'Save Settings', 'cloud-gaming-tracker' ); ?>">
                        <?php esc_html_e( 'Save Settings', 'cloud-gaming-tracker' ); ?>
                    </button>
                </div>
            </div>
            <div class="cgt-admin-card">
                <div class="cgt-admin-card-header">
                    <h2><?php esc_html_e( 'Shortcode Usage', 'cloud-gaming-tracker' ); ?></h2>
                </div>
                <div class="cgt-admin-card-body">
                    <div style="background: #f9fafb; padding: 20px; border-radius: 6px; font-family: monospace;">
                        <p><strong><?php esc_html_e( 'Basic:', 'cloud-gaming-tracker' ); ?></strong></p>
                        <code>[cloud_gaming_tracker]</code>
                        <p style="margin-top: 15px;"><strong><?php esc_html_e( 'Specific Game:', 'cloud-gaming-tracker' ); ?></strong></p>
                        <code>[cloud_gaming_tracker game_id="1"]</code>
                        <p style="margin-top: 15px;"><strong><?php esc_html_e( 'Hide Unavailable:', 'cloud-gaming-tracker' ); ?></strong></p>
                        <code>[cloud_gaming_tracker game_id="1" show_unavailable="false"]</code>
                    </div>
                    <p style="margin-top: 20px; color: #6b7280;">
                        <?php esc_html_e( 'Or use the Gutenberg block "Cloud Gaming Tracker" in the block editor.', 'cloud-gaming-tracker' ); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    public function enqueue_admin_assets( $hook ) {
        if ( strpos( $hook, 'cgt-' ) === false ) {
            return;
        }

        wp_enqueue_style( 'cgt-admin-css', CGT_PLUGIN_URL . 'assets/css/admin.css', array(), CGT_VERSION );
        wp_enqueue_script( 'cgt-admin-js', CGT_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), CGT_VERSION, true );
        wp_localize_script(
            'cgt-admin-js',
            'cgtAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'  => wp_create_nonce( 'cgt_admin_nonce' ),
                'strings' => array(
                    'confirmDelete' => esc_html__( 'Are you sure?', 'cloud-gaming-tracker' ),
                    'saving'       => esc_html__( 'Saving...', 'cloud-gaming-tracker' ),
                    'saved'        => esc_html__( 'Saved!', 'cloud-gaming-tracker' ),
                    'error'        => esc_html__( 'Error', 'cloud-gaming-tracker' ),
                ),
            )
        );
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
    }

    public function enqueue_frontend_assets() {
        // Enqueue the external CSS file
        wp_enqueue_style( 'cgt-frontend-css', CGT_PLUGIN_URL . 'assets/css/frontend.css', array(), CGT_VERSION );
        
        // Enqueue the external JS file
        wp_enqueue_script( 'cgt-frontend-js', CGT_PLUGIN_URL . 'assets/js/frontend.js', array( 'jquery' ), CGT_VERSION, true );
        
        $settings = get_option( 'cgt_settings', array() );
        wp_localize_script(
            'cgt-frontend-js',
            'cgtFrontend',
            array(
                'settings' => $settings,
                'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
            )
        );
    }

    public function ajax_save_platform() {
        check_ajax_referer( 'cgt_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized', 'cloud-gaming-tracker' ) ) );
        }

        $platform_id = isset( $_POST['platform_id'] ) ? intval( $_POST['platform_id'] ) : 0;
        $data         = array(
            'name'             => sanitize_text_field( $_POST['name'] ),
            'icon_url'         => esc_url_raw( $_POST['icon_url'] ),
            'game_included'    => isset( $_POST['game_included'] ) ? 1 : 0,
            'price'            => sanitize_text_field( $_POST['price'] ),
            'currency'         => sanitize_text_field( $_POST['currency'] ),
            'unavailable_url'  => esc_url_raw( $_POST['unavailable_url'] ),
            'cta_text'         => sanitize_text_field( $_POST['cta_text'] ),
            'cta_url'          => esc_url_raw( $_POST['cta_url'] ),
            'display_order'    => intval( $_POST['display_order'] ),
            'is_active'        => isset( $_POST['is_active'] ) ? 1 : 0,
        );

        if ( $platform_id > 0 ) {
            self::update_platform( $platform_id, $data );
        } else {
            $platform_id = self::add_platform( $data );
        }

        wp_send_json_success( array( 'platform_id' => $platform_id, 'message' => esc_html__( 'Platform saved', 'cloud-gaming-tracker' ) ) );
    }

    public function ajax_delete_platform() {
        check_ajax_referer( 'cgt_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized', 'cloud-gaming-tracker' ) ) );
        }
        $platform_id = isset( $_POST['platform_id'] ) ? intval( $_POST['platform_id'] ) : 0;
        if ( $platform_id <= 0 ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid ID', 'cloud-gaming-tracker' ) ) );
        }
        global $wpdb;
        $wpdb->delete( $wpdb->prefix . 'cgt_platforms', array( 'id' => $platform_id ) );
        wp_send_json_success( array( 'message' => esc_html__( 'Deleted', 'cloud-gaming-tracker' ) ) );
    }

    public function ajax_save_availability() {
        check_ajax_referer( 'cgt_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized', 'cloud-gaming-tracker' ) ) );
        }

        $game_id          = isset( $_POST['game_id'] ) ? intval( $_POST['game_id'] ) : 0;
        $availability_data = isset( $_POST['availability'] ) ? $_POST['availability'] : array();

        if ( $game_id <= 0 ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid game ID', 'cloud-gaming-tracker' ) ) );
        }

        // Verify game exists
        $game = self::get_game( $game_id );
        if ( empty( $game ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Game not found', 'cloud-gaming-tracker' ) ) );
        }

        $saved = 0;
        foreach ( $availability_data as $platform_id => $data ) {
            $platform_id            = intval( $platform_id );
            $is_available           = isset( $data['is_available'] ) ? intval( $data['is_available'] ) : 1;
            $game_included_override = isset( $data['game_included'] ) ? intval( $data['game_included'] ) : -1;
            $plan_tier             = isset( $data['plan_tier'] ) ? sanitize_text_field( $data['plan_tier'] ) : '';

            self::save_game_availability( $game_id, $platform_id, $is_available, $game_included_override, $plan_tier );
            $saved++;
        }

        /* translators: %d = number of platforms updated */
        wp_send_json_success( array( 'message' => sprintf( esc_html__( '%d platforms updated', 'cloud-gaming-tracker' ), $saved ) ) );
    }

    public function ajax_save_settings() {
        check_ajax_referer( 'cgt_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized', 'cloud-gaming-tracker' ) ) );
        }
        
        // Get current settings to merge
        $current_settings = get_option( 'cgt_settings', array() );
        
        $settings = array(
            'theme'                => sanitize_text_field( $_POST['theme'] ),
            'primary_color'       => sanitize_hex_color( $_POST['primary_color'] ),
            'success_color'       => sanitize_hex_color( $_POST['success_color'] ),
            'danger_color'        => sanitize_hex_color( $_POST['danger_color'] ),
            'card_radius'         => intval( $_POST['card_radius'] ),
            'button_radius'       => intval( $_POST['button_radius'] ),
            'spacing'             => intval( $_POST['spacing'] ),
            'glassmorphism'       => isset( $_POST['glassmorphism'] ) ? 1 : 0,
            'show_price'          => isset( $_POST['show_price'] ) ? 1 : 0,
            'show_tier'           => isset( $_POST['show_tier'] ) ? 1 : 0,
            'group_platforms'     => isset( $_POST['group_platforms'] ) ? 1 : 0,
            'show_group_titles'  => intval( $_POST['show_group_titles'] ),
            'available_title'    => sanitize_text_field( $_POST['available_title'] ),
            'unavailable_title'  => sanitize_text_field( $_POST['unavailable_title'] ),
            'title_font_size'   => intval( $_POST['title_font_size'] ),
            'unavailable_button_text' => sanitize_text_field( $_POST['unavailable_button_text'] ),
        );
        
        // Merge with existing settings to preserve any we might have missed
        $settings = array_merge( $current_settings, $settings );
        
        update_option( 'cgt_settings', $settings );
        wp_send_json_success( array( 'message' => esc_html__( 'Settings saved successfully!', 'cloud-gaming-tracker' ) ) );
    }

    public function ajax_add_game() {
        check_ajax_referer( 'cgt_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized', 'cloud-gaming-tracker' ) ) );
        }
        $game_name = sanitize_text_field( $_POST['game_name'] );
        $game_slug = sanitize_title( $_POST['game_name'] );
        global $wpdb;
        $wpdb->insert( $wpdb->prefix . 'cgt_games', array( 'name' => $game_name, 'slug' => $game_slug, 'is_active' => 1 ) );
        wp_send_json_success( array( 'game_id' => $wpdb->insert_id, 'message' => esc_html__( 'Game added', 'cloud-gaming-tracker' ) ) );
    }

    public function ajax_delete_game() {
        check_ajax_referer( 'cgt_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized', 'cloud-gaming-tracker' ) ) );
        }
        $game_id = isset( $_POST['game_id'] ) ? intval( $_POST['game_id'] ) : 0;
        if ( $game_id <= 0 ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Invalid ID', 'cloud-gaming-tracker' ) ) );
        }
        global $wpdb;
        $wpdb->delete( $wpdb->prefix . 'cgt_games', array( 'id' => $game_id ) );
        wp_send_json_success( array( 'message' => esc_html__( 'Deleted', 'cloud-gaming-tracker' ) ) );
    }

    public function render_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'game_id'          => 0,
                'game_slug'        => '',
                'show_unavailable' => 'true',
                'group_by'         => 'availability',
            ),
            $atts,
            'cloud_gaming_tracker'
        );
        return CGT_Frontend::render_platforms( $atts );
    }

    public function register_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }
        register_block_type(
            'cgt/platforms',
            array(
                'editor_script'   => 'cgt-block-js',
                'editor_style'   => 'cgt-frontend-css',
                'style'           => 'cgt-frontend-css',
                'script'          => 'cgt-frontend-js',
                'attributes'      => array(
                    'gameId'           => array( 'type' => 'number', 'default' => 0 ),
                    'gameSlug'         => array( 'type' => 'string', 'default' => '' ),
                    'showUnavailable'  => array( 'type' => 'boolean', 'default' => true ),
                    'groupBy'          => array( 'type' => 'string', 'default' => 'availability' ),
                ),
                'render_callback' => array( $this, 'render_block' ),
            )
        );
    }

    public function render_block( $attributes ) {
        $atts = array(
            'game_id'          => $attributes['gameId'] ?? 0,
            'game_slug'        => $attributes['gameSlug'] ?? '',
            'show_unavailable' => $attributes['showUnavailable'] ? 'true' : 'false',
            'group_by'         => $attributes['groupBy'] ?? 'availability',
        );
        return $this->render_shortcode( $atts );
    }
}