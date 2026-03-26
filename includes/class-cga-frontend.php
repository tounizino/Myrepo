<?php

if (!defined('ABSPATH')) {
    exit;
}

class CGA_Frontend {

    private $database;

    public function __construct() {
        $this->database = new CGA_Database();
    }

    public function render_cards($atts) {
        $game_id = intval($atts['game_id']);
        $group_by_availability = $atts['group_by_availability'] === 'yes';
        $columns = intval($atts['columns']);
        $settings = get_option('cga_settings', array());
        
        // Get availability data
        $availability = $this->database->get_game_availability($game_id);
        
        // Group if needed
        if ($group_by_availability) {
            $available = array();
            $unavailable = array();
            
            foreach ($availability as $platform) {
                if ($platform->is_available) {
                    $available[] = $platform;
                } else {
                    $unavailable[] = $platform;
                }
            }
        } else {
            $available = $availability;
            $unavailable = array();
        }

        ob_start();
        
        echo '<div class="cga-container" data-theme="' . esc_attr($settings['theme_mode'] ?? 'light') . '" data-columns="' . esc_attr($columns) . '">';
        
        if ($group_by_availability && !empty($available)) {
            echo '<div class="cga-section">';
            echo '<h3 class="cga-section-title">' . esc_html__('Available', 'cloud-games-availability-v2') . '</h3>';
            echo '<div class="cga-cards-grid">';
            foreach ($available as $platform) {
                echo $this->render_card($platform, $settings);
            }
            echo '</div>';
            echo '</div>';
            
            if (!empty($unavailable)) {
                echo '<div class="cga-section">';
                echo '<h3 class="cga-section-title">' . esc_html__('Not Available', 'cloud-games-availability-v2') . '</h3>';
                echo '<div class="cga-cards-grid">';
                foreach ($unavailable as $platform) {
                    echo $this->render_card($platform, $settings);
                }
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="cga-cards-grid">';
            foreach ($availability as $platform) {
                echo $this->render_card($platform, $settings);
            }
            echo '</div>';
        }
        
        echo '</div>';
        
        return ob_get_clean();
    }

    private function render_card($platform, $settings) {
        $is_available = $platform->is_available ? true : false;
        $game_included = $platform->game_included ? true : false;
        $show_price = $settings['show_price'] ?? true;
        $show_tier = $settings['show_tier'] ?? true;
        $show_notes = $settings['show_notes'] ?? true;
        $button_style = $settings['button_style'] ?? 'filled';
        $border_radius = $settings['border_radius'] ?? 12;
        $glassmorphism = $settings['glassmorphism'] ?? true;
        $hover_effect = $settings['hover_effect'] ?? 'lift';
        
        $price = $platform->custom_price ?: $platform->base_price;
        $price_display = $price > 0 ? '$' . number_format($price, 2) : __('Free', 'cloud-games-availability-v2');
        $period = $platform->price_period === 'year' ? __('/year', 'cloud-games-availability-v2') : __('/month', 'cloud-games-availability-v2');
        
        $cta_text = $platform->cta_text ?: __('Play Now', 'cloud-games-availability-v2');
        $cta_url = $platform->cta_url ?: '#';
        
        $card_classes = array('cga-card');
        if (!$is_available) {
            $card_classes[] = 'cga-card-unavailable';
        }
        if ($hover_effect) {
            $card_classes[] = 'cga-hover-' . $hover_effect;
        }
        if ($glassmorphism) {
            $card_classes[] = 'cga-glass';
        }

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $card_classes)); ?>" style="--cga-border-radius: <?php echo esc_attr($border_radius); ?>px;">
            <div class="cga-card-header">
                <div class="cga-platform-icon">
                    <?php if ($platform->icon_url): ?>
                        <img src="<?php echo esc_url($platform->icon_url); ?>" alt="<?php echo esc_attr($platform->name); ?>" loading="lazy">
                    <?php else: ?>
                        <div class="cga-icon-placeholder"><?php echo esc_html(substr($platform->name, 0, 2)); ?></div>
                    <?php endif; ?>
                </div>
                <div class="cga-platform-info">
                    <h4 class="cga-platform-name"><?php echo esc_html($platform->name); ?></h4>
                    <span class="cga-availability-badge <?php echo $is_available ? 'cga-available' : 'cga-unavailable'; ?>">
                        <?php echo $is_available ? __('Available', 'cloud-games-availability-v2') : __('Not Available', 'cloud-games-availability-v2'); ?>
                    </span>
                </div>
            </div>

            <div class="cga-card-body">
                <?php if ($is_available && $show_tier && $platform->tier_name): ?>
                    <div class="cga-tier">
                        <span class="cga-tier-label"><?php echo esc_html($platform->tier_name); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($is_available && $show_price): ?>
                    <div class="cga-price">
                        <?php echo esc_html($price_display); ?>
                        <span class="cga-price-period"><?php echo esc_html($period); ?></span>
                    </div>
                <?php endif; ?>

                <div class="cga-game-included <?php echo $game_included ? 'cga-included' : 'cga-own-required'; ?>">
                    <?php if ($game_included): ?>
                        <svg class="cga-icon-check" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span><?php _e('Game included', 'cloud-games-availability-v2'); ?></span>
                    <?php else: ?>
                        <svg class="cga-icon-info" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span><?php _e('Own game required', 'cloud-games-availability-v2'); ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($is_available && $show_notes && $platform->notes): ?>
                    <div class="cga-notes">
                        <p><?php echo esc_html($platform->notes); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($is_available && $platform->availability_notes): ?>
                    <div class="cga-availability-notes">
                        <p><?php echo esc_html($platform->availability_notes); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cga-card-footer">
                <?php if ($is_available): ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="cga-cta cga-cta-<?php echo esc_attr($button_style); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html($cta_text); ?>
                        <svg class="cga-cta-arrow" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                <?php else: ?>
                    <button class="cga-cta cga-cta-disabled" disabled>
                        <?php _e('Unavailable', 'cloud-games-availability-v2'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
