<?php
namespace CloudLoadout;

if (!defined('ABSPATH')) {
    exit;
}

class Shortcodes {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('cl_homepage', [$this, 'render_homepage']);
        add_shortcode('cl_theme_toggle', [$this, 'render_theme_toggle']);
        add_shortcode('cl_search', [$this, 'render_search']);
    }

    public function render_theme_toggle() {
        return '<button id="cl-theme-toggle" class="cl-theme-toggle">
            <span class="dark-icon">🌙</span>
            <span class="light-icon">☀️</span>
        </button>';
    }

    public function render_search() {
        ob_start();
        ?>
        <div class="cl-search-container">
            <input type="text" class="cl-search-input" placeholder="Search 10,000+ cloud games...">
            <div class="cl-search-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_homepage() {
        global $wpdb;
        
        // Fetch trending games (random for demo)
        $trending = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cl_games ORDER BY rating DESC LIMIT 6");
        
        // Fetch recently updated
        $recent = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cl_games ORDER BY last_updated DESC LIMIT 12");

        // Fetch providers
        $providers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cl_providers");

        ob_start();
        ?>
        <div class="cl-homepage">
            <section class="cl-hero-section">
                <h1>The Ultimate Cloud Gaming Database</h1>
                <p>Find where to play your favorite games in the cloud.</p>
                <?php echo $this->render_search(); ?>
            </section>

            <section class="cl-section">
                <div class="cl-section-header">
                    <h2>Trending Games</h2>
                </div>
                <div class="cl-game-grid">
                    <?php foreach ($trending as $game): ?>
                        <a href="/g/<?php echo $game->slug; ?>" class="cl-game-card">
                            <div class="cl-card-image">
                                <img src="<?php echo esc_url($game->cover_url); ?>" alt="<?php echo esc_attr($game->name); ?>">
                                <div class="cl-card-rating">★ <?php echo number_format($game->rating, 1); ?></div>
                            </div>
                            <div class="cl-card-info">
                                <h3><?php echo esc_html($game->name); ?></h3>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="cl-section">
                <div class="cl-section-header">
                    <h2>Recently Updated</h2>
                </div>
                <div class="cl-game-slider">
                    <?php foreach ($recent as $game): ?>
                        <a href="/g/<?php echo $game->slug; ?>" class="cl-game-card-mini">
                            <img src="<?php echo esc_url($game->cover_url); ?>" alt="<?php echo esc_attr($game->name); ?>">
                            <span><?php echo esc_html($game->name); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="cl-section">
                <div class="cl-section-header">
                    <h2>Browse by Provider</h2>
                </div>
                <div class="cl-provider-bubbles">
                    <?php foreach ($providers as $p): ?>
                        <a href="/provider/<?php echo $p->slug; ?>" class="cl-provider-bubble">
                            <?php echo esc_html($p->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
        <?php
        return ob_get_clean();
    }
}
