<?php
/**
 * Plugin Name: Cloud Gaming Platforms Tool
 * Plugin URI: https://cloudloadout.com
 * Description: Interactive cloud gaming platforms comparison tool with responsive design, SEO optimization, and mobile support
 * Version: 1.0.0
 * Author: CloudLoadout
 * Author URI: https://cloudloadout.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cloud-gaming-platforms
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class CloudGamingPlatformsTool {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode('cloud_gaming_platforms', array($this, 'render_tool'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_head', array($this, 'add_seo_meta'));
    }
    
    /**
     * Enqueue styles and scripts
     */
    public function enqueue_assets() {
        // Only load on pages with the shortcode
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'cloud_gaming_platforms')) {
            wp_enqueue_style(
                'cloud-gaming-platforms-style',
                plugin_dir_url(__FILE__) . 'assets/css/style.css',
                array(),
                '1.0.0'
            );
            
            wp_enqueue_script(
                'cloud-gaming-platforms-script',
                plugin_dir_url(__FILE__) . 'assets/js/script.js',
                array(),
                '1.0.0',
                true
            );
            
            // Pass data to JavaScript
            wp_localize_script('cloud-gaming-platforms-script', 'cgpData', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('cgp_nonce'),
                'siteUrl' => get_site_url()
            ));
        }
    }
    
    /**
     * Add SEO meta tags
     */
    public function add_seo_meta() {
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'cloud_gaming_platforms')) {
            ?>
            <meta name="description" content="Compare top cloud gaming platforms including GeForce NOW, Boosteroid, Xbox Cloud Gaming, Shadow PC, and Parsec. Find the best cloud gaming service for your needs.">
            <meta name="keywords" content="cloud gaming, GeForce NOW, Boosteroid, Xbox Cloud Gaming, Shadow PC, Parsec, game streaming">
            <meta property="og:title" content="Cloud Gaming Platforms Comparison Tool">
            <meta property="og:description" content="Interactive tool to compare and explore the best cloud gaming platforms and launchers">
            <meta property="og:type" content="website">
            <meta name="twitter:card" content="summary_large_image">
            <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "SoftwareApplication",
                "name": "Cloud Gaming Platforms Comparison Tool",
                "applicationCategory": "WebApplication",
                "description": "Interactive tool to compare and explore cloud gaming platforms",
                "offers": {
                    "@type": "Offer",
                    "price": "0",
                    "priceCurrency": "USD"
                }
            }
            </script>
            <?php
        }
    }
    
    /**
     * Render the tool
     */
    public function render_tool($atts) {
        // Parse shortcode attributes
        $atts = shortcode_atts(array(
            'theme' => 'default',
            'show_comparison' => 'yes',
            'show_stats' => 'yes',
            'default_view' => 'grid'
        ), $atts, 'cloud_gaming_platforms');
        
        ob_start();
        ?>
        <div class="cloud-gaming-tool-wrapper" id="cloudGamingTool" data-theme="<?php echo esc_attr($atts['theme']); ?>">
            <!-- Header -->
            <header class="cgt-header">
                <h1>☁️ Cloud Gaming Platforms & Launchers</h1>
                <p>Compare and discover the best cloud gaming services to optimize your gaming experience</p>
            </header>
            
            <?php if ($atts['show_stats'] === 'yes'): ?>
            <!-- Statistics Bar -->
            <div class="cgt-stats-bar">
                <div class="cgt-stat">
                    <div class="cgt-stat-number" id="totalPlatforms">5</div>
                    <div class="cgt-stat-label">Platforms</div>
                </div>
                <div class="cgt-stat">
                    <div class="cgt-stat-number" id="avgRating">4.6</div>
                    <div class="cgt-stat-label">Avg Rating</div>
                </div>
                <div class="cgt-stat">
                    <div class="cgt-stat-number" id="favoriteCount">0</div>
                    <div class="cgt-stat-label">Favorites</div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Controls -->
            <div class="cgt-controls">
                <div class="cgt-search-box">
                    <input type="search" 
                           id="platformSearch" 
                           placeholder="Search platforms..."
                           aria-label="Search cloud gaming platforms">
                </div>
                
                <div class="cgt-filter-group">
                    <button class="cgt-filter-btn active" data-filter="all">All Platforms</button>
                    <button class="cgt-filter-btn" data-filter="free">Free Tier</button>
                    <button class="cgt-filter-btn" data-filter="premium">Premium</button>
                    <button class="cgt-filter-btn" data-filter="low-latency">Low Latency</button>
                </div>
                
                <div class="cgt-view-toggle">
                    <button class="cgt-view-btn <?php echo $atts['default_view'] === 'grid' ? 'active' : ''; ?>" data-view="grid" aria-label="Grid view">⊞</button>
                    <button class="cgt-view-btn <?php echo $atts['default_view'] === 'list' ? 'active' : ''; ?>" data-view="list" aria-label="List view">☰</button>
                </div>
            </div>
            
            <!-- Platforms Grid -->
            <div class="cgt-platforms-grid <?php echo $atts['default_view'] === 'list' ? 'list-view' : ''; ?>" id="platformsGrid">
                <!-- Platforms will be dynamically inserted here -->
            </div>
            
            <?php if ($atts['show_comparison'] === 'yes'): ?>
            <!-- Comparison Section -->
            <div class="cgt-comparison-section">
                <div class="cgt-comparison-header">
                    <h2>Platform Comparison</h2>
                    <button class="cgt-btn cgt-btn-secondary" id="exportBtn">📊 Export Comparison</button>
                </div>
                <div class="cgt-comparison-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Platform</th>
                                <th>Price</th>
                                <th>Latency</th>
                                <th>Resolution</th>
                                <th>FPS</th>
                                <th>Game Library</th>
                            </tr>
                        </thead>
                        <tbody id="comparisonTableBody">
                            <!-- Table rows will be dynamically inserted -->
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Footer -->
            <footer class="cgt-footer">
                <p>🎮 Powered by <a href="https://cloudloadout.com" target="_blank" rel="noopener">CloudLoadout.com</a> | Your Ultimate Cloud Gaming Guide</p>
            </footer>
            
            <!-- Modal -->
            <div class="cgt-modal" id="detailsModal">
                <div class="cgt-modal-content">
                    <button class="cgt-modal-close" id="modalClose" aria-label="Close modal">×</button>
                    <div id="modalBody"></div>
                </div>
            </div>
            
            <!-- Toast Notification -->
            <div class="cgt-toast" id="toast" role="alert" aria-live="polite"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize the plugin
new CloudGamingPlatformsTool();

/**
 * Activation hook
 */
register_activation_hook(__FILE__, 'cgp_activate');
function cgp_activate() {
    // Create plugin assets directory if it doesn't exist
    $upload_dir = wp_upload_dir();
    $plugin_dir = $upload_dir['basedir'] . '/cloud-gaming-platforms';
    
    if (!file_exists($plugin_dir)) {
        wp_mkdir_p($plugin_dir);
    }
    
    // Set default options
    add_option('cgp_version', '1.0.0');
    add_option('cgp_install_date', current_time('mysql'));
}

/**
 * Deactivation hook
 */
register_deactivation_hook(__FILE__, 'cgp_deactivate');
function cgp_deactivate() {
    // Clean up if needed
}

/**
 * Usage:
 * 
 * Basic shortcode:
 * [cloud_gaming_platforms]
 * 
 * With custom attributes:
 * [cloud_gaming_platforms theme="dark" show_comparison="yes" show_stats="yes" default_view="grid"]
 * 
 * Hide comparison table:
 * [cloud_gaming_platforms show_comparison="no"]
 * 
 * Start with list view:
 * [cloud_gaming_platforms default_view="list"]
 */
