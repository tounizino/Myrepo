<?php
if (!defined('ABSPATH')) exit;

$game_slug = get_query_var('cl_game');
global $wpdb;

$game = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}cl_games WHERE slug = %s",
    $game_slug
));

if (!$game) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part('404');
    exit;
}

// Fetch providers
$providers = $wpdb->get_results($wpdb->prepare(
    "SELECT p.name, p.slug, gp.status, gp.last_checked, gp.confidence_score 
     FROM {$wpdb->prefix}cl_game_provider gp
     JOIN {$wpdb->prefix}cl_providers p ON gp.provider_id = p.id
     WHERE gp.game_id = %d",
    $game->id
));

// Fetch screenshots
$screenshots = $wpdb->get_results($wpdb->prepare(
    "SELECT image_url FROM {$wpdb->prefix}cl_screenshots WHERE game_id = %d",
    $game->id
));

get_header();
?>

<div class="cl-game-page-container">
    <div class="cl-game-hero" style="background-image: url('<?php echo esc_url($game->background_url); ?>');">
        <div class="cl-hero-overlay"></div>
        <div class="cl-container">
            <div class="cl-hero-content">
                <div class="cl-game-sidebar">
                    <img src="<?php echo esc_url($game->cover_url); ?>" alt="<?php echo esc_attr($game->name); ?>" class="cl-game-cover">
                </div>
                <div class="cl-game-main-info">
                    <nav class="cl-breadcrumb">
                        <a href="/">Home</a> / <a href="/games">Games</a> / <?php echo esc_html($game->name); ?>
                    </nav>
                    <h1 class="cl-game-title"><?php echo esc_html($game->name); ?></h1>
                    
                    <div class="cl-game-meta-strip">
                        <span class="cl-meta-item"><?php echo date('Y', strtotime($game->release_date)); ?></span>
                        <span class="cl-meta-item"><?php echo esc_html($game->genres); ?></span>
                        <span class="cl-meta-item rating">★ <?php echo number_format($game->rating, 1); ?></span>
                    </div>

                    <div class="cl-compatibility-matrix">
                        <h2>Cloud Compatibility</h2>
                        <div class="cl-provider-grid">
                            <?php 
                            $all_providers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cl_providers");
                            foreach ($all_providers as $p): 
                                $status = 'Unsupported';
                                $status_class = 'unsupported';
                                foreach ($providers as $gp) {
                                    if ($gp->slug === $p->slug) {
                                        $status = ucfirst($gp->status);
                                        $status_class = strtolower($gp->status);
                                        break;
                                    }
                                }
                            ?>
                            <div class="cl-provider-card <?php echo $status_class; ?>">
                                <div class="cl-provider-name"><?php echo esc_html($p->name); ?></div>
                                <div class="cl-provider-status"><?php echo $status; ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="cl-game-actions">
                        <a href="#play" class="cl-btn cl-btn-primary">Where to Play</a>
                        <button class="cl-btn cl-btn-secondary">Add to Library</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="cl-container cl-game-details">
        <div class="cl-details-grid">
            <div class="cl-main-col">
                <section class="cl-description">
                    <h2>About <?php echo esc_html($game->name); ?></h2>
                    <p><?php echo nl2br(esc_html($game->description)); ?></p>
                </section>

                <?php if ($screenshots): ?>
                <section class="cl-screenshots">
                    <h2>Screenshots</h2>
                    <div class="cl-screenshot-slider">
                        <?php foreach ($screenshots as $s): ?>
                            <img src="<?php echo esc_url($s->image_url); ?>" alt="Screenshot">
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            </div>

            <div class="cl-side-col">
                <div class="cl-info-box">
                    <h3>Game Info</h3>
                    <ul>
                        <li><strong>Developer:</strong> <?php echo esc_html($game->developer); ?></li>
                        <li><strong>Publisher:</strong> <?php echo esc_html($game->publisher); ?></li>
                        <li><strong>Release Date:</strong> <?php echo date('M j, Y', strtotime($game->release_date)); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
