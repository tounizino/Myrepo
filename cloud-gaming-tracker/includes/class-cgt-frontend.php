<?php
/**
 * Frontend Class
 * 
 * @package Cloud_Gaming_Tracker
 */

if (!defined('ABSPATH')) exit;

class CGT_Frontend {
    
    /**
     * Render platforms for frontend display
     */
    public static function render_platforms($atts) {
        $settings = get_option('cgt_settings', array());
        $game_id = intval($atts['game_id']);
        $game_slug = sanitize_title($atts['game_slug']);
        $show_unavailable = $atts['show_unavailable'] === 'true';
        $group_by = $atts['group_by'];
        
        if (!$game_id && $game_slug) {
            global $wpdb;
            $game = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}cgt_games WHERE slug = %s", $game_slug), ARRAY_A);
            if ($game) $game_id = $game['id'];
        }
        
        if ($game_id) {
            $platforms = CGT_Database::get_platforms_for_game($game_id, $show_unavailable);
        } else {
            $platforms = CGT_Database::get_platforms(array('active_only' => true));
            foreach ($platforms as &$platform) {
                $platform['is_available'] = 1;
            }
        }
        
        if (empty($platforms)) {
            return '<div class="cgt-no-platforms">' . __('No platforms available', 'cloud-gaming-tracker') . '</div>';
        }
        
        if ($settings['group_platforms'] && $group_by === 'availability') {
            $available = array_filter($platforms, function($p) { return !empty($p['is_available']); });
            $unavailable = array_filter($platforms, function($p) { return empty($p['is_available']); });
            
            $output = self::render_platform_group($available, __('Available Platforms', 'cloud-gaming-tracker'), $settings, true);
            if ($show_unavailable && !empty($unavailable)) {
                $output .= self::render_platform_group($unavailable, __('Not Available', 'cloud-gaming-tracker'), $settings, false);
            }
        } else {
            $output = self::render_platform_group($platforms, '', $settings, null);
        }
        
        return '<div class="cgt-container cgt-theme-' . esc_attr($settings['theme'] ?? 'auto') . '">' . $output . '</div>';
    }
    
    /**
     * Render a group of platforms
     */
    private static function render_platform_group($platforms, $group_title, $settings, $is_available) {
        if (empty($platforms)) return '';
        $output = '';
        if ($group_title) {
            $output .= '<h3 class="cgt-group-title">' . esc_html($group_title) . '</h3>';
        }
        $output .= '<div class="cgt-platforms-list">';
        foreach ($platforms as $platform) {
            $output .= self::render_platform_card($platform, $settings, $is_available);
        }
        $output .= '</div>';
        return $output;
    }
    
    /**
     * Render a single platform card
     */
    private static function render_platform_card($platform, $settings, $is_available = null) {
        if ($is_available === null) {
            $is_available = !empty($platform['is_available']);
        }
        
        $card_class = 'cgt-platform-card';
        if (!$is_available) $card_class .= ' cgt-unavailable';
        else $card_class .= ' cgt-available';
        if ($settings['glassmorphism']) $card_class .= ' cgt-glass';
        
        $icon_url = !empty($platform['icon_url']) ? $platform['icon_url'] : CGT_PLUGIN_URL . 'assets/images/default-platform.svg';
        $game_status = $platform['game_included'] ? __('Game Included', 'cloud-gaming-tracker') : __('Own Game Required', 'cloud-gaming-tracker');
        $game_status_class = $platform['game_included'] ? 'cgt-included' : 'cgt-required';
        
        $price = '';
        if ($settings['show_price'] && !empty($platform['price'])) {
            $custom_price = !empty($platform['custom_price']) ? $platform['custom_price'] : $platform['price'];
            $currency = !empty($platform['currency']) ? $platform['currency'] : '$';
            $price = sprintf(__('From %s%s / month', 'cloud-gaming-tracker'), $currency, $custom_price);
        }
        
        $tier = '';
        if ($settings['show_tier'] && !empty($platform['tier'])) {
            $tier = sprintf(__('Plan: %s', 'cloud-gaming-tracker'), $platform['tier']);
        }
        
        $cta_text = !empty($platform['cta_text']) ? $platform['cta_text'] : __('Play Now', 'cloud-gaming-tracker');
        $cta_url = !empty($platform['custom_cta_url']) ? $platform['custom_cta_url'] : $platform['cta_url'];
        
        ob_start();
        ?>
        <div class="<?php echo esc_attr($card_class); ?>" data-platform-id="<?php echo esc_attr($platform['id']); ?>">
            <div class="cgt-card-content">
                <div class="cgt-card-header">
                    <div class="cgt-platform-icon">
                        <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($platform['name']); ?>" loading="lazy">
                    </div>
                    <div class="cgt-platform-info">
                        <h4 class="cgt-platform-name"><?php echo esc_html($platform['name']); ?></h4>
                        <span class="cgt-availability-status cgt-status-<?php echo $is_available ? 'available' : 'unavailable'; ?>">
                            <?php echo $is_available ? __('Available', 'cloud-gaming-tracker') : __('Not Available', 'cloud-gaming-tracker'); ?>
                        </span>
                    </div>
                    <?php if ($is_available && $cta_url): ?>
                    <div class="cgt-card-action">
                        <a href="<?php echo esc_url($cta_url); ?>" class="cgt-cta-button" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($cta_text); ?>
                            <svg class="cgt-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="cgt-card-action">
                        <button class="cgt-cta-button cgt-disabled" disabled>
                            <?php echo __('Unavailable', 'cloud-gaming-tracker'); ?>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($is_available): ?>
                <div class="cgt-card-body">
                    <div class="cgt-game-status <?php echo esc_attr($game_status_class); ?>">
                        <svg class="cgt-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="<?php echo $platform['game_included'] ? 'M20 6L9 17l-5-5' : 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z'; ?>"/>
                        </svg>
                        <span><?php echo esc_html($game_status); ?></span>
                    </div>
                    <?php if ($price): ?>
                    <div class="cgt-price">
                        <svg class="cgt-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                        <span><?php echo esc_html($price); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($tier): ?>
                    <div class="cgt-tier">
                        <svg class="cgt-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        <span><?php echo esc_html($tier); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Enqueue frontend assets
     */
    public static function enqueue_assets() {
        wp_enqueue_style('cgt-frontend-css', CGT_PLUGIN_URL . 'assets/css/frontend.css', array(), CGT_VERSION);
        wp_enqueue_script('cgt-frontend-js', CGT_PLUGIN_URL . 'assets/js/frontend.js', array(), CGT_VERSION, true);
        $settings = get_option('cgt_settings', array());
        wp_localize_script('cgt-frontend-js', 'cgtFrontend', array(
            'settings' => $settings,
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ));
    }
}
