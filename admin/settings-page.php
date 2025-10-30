<?php
/**
 * Admin Settings Page
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap ubcg-settings-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="ubcg-settings-header">
        <h2><?php _e('Ultimate Blocks for Cloud Gaming - Settings', 'ubcg'); ?></h2>
        <p><?php _e('Configure your cloud gaming blog blocks and customize colors, layouts, and more.', 'ubcg'); ?></p>
    </div>
    
    <?php settings_errors(); ?>
    
    <div class="ubcg-settings-container">
        <form method="post" action="options.php">
            <?php
            settings_fields('ubcg_settings_group');
            ?>
            
            <div class="ubcg-settings-section">
                <h2><?php _e('Color Settings', 'ubcg'); ?></h2>
                <p class="description"><?php _e('Customize the color scheme for your cloud gaming blocks. Default palette is blue-themed.', 'ubcg'); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ubcg_primary_color"><?php _e('Primary Color', 'ubcg'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="ubcg_primary_color" id="ubcg_primary_color" value="<?php echo esc_attr(get_option('ubcg_primary_color', '#2563eb')); ?>" class="ubcg-color-picker">
                            <p class="description"><?php _e('Main accent color used throughout blocks', 'ubcg'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="ubcg_secondary_color"><?php _e('Secondary Color', 'ubcg'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="ubcg_secondary_color" id="ubcg_secondary_color" value="<?php echo esc_attr(get_option('ubcg_secondary_color', '#1e40af')); ?>" class="ubcg-color-picker">
                            <p class="description"><?php _e('Secondary color for hover states and emphasis', 'ubcg'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="ubcg_accent_color"><?php _e('Accent Color', 'ubcg'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="ubcg_accent_color" id="ubcg_accent_color" value="<?php echo esc_attr(get_option('ubcg_accent_color', '#60a5fa')); ?>" class="ubcg-color-picker">
                            <p class="description"><?php _e('Light accent color for backgrounds and borders', 'ubcg'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="ubcg_text_color"><?php _e('Text Color', 'ubcg'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="ubcg_text_color" id="ubcg_text_color" value="<?php echo esc_attr(get_option('ubcg_text_color', '#1e293b')); ?>" class="ubcg-color-picker">
                            <p class="description"><?php _e('Primary text color', 'ubcg'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="ubcg-settings-section">
                <h2><?php _e('Display Settings', 'ubcg'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ubcg_posts_per_page"><?php _e('Posts Per Page', 'ubcg'); ?></label>
                        </th>
                        <td>
                            <input type="number" name="ubcg_posts_per_page" id="ubcg_posts_per_page" value="<?php echo esc_attr(get_option('ubcg_posts_per_page', 9)); ?>" min="1" max="100" step="1">
                            <p class="description"><?php _e('Default number of posts to display in grid layouts', 'ubcg'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="ubcg_enable_seo"><?php _e('SEO Optimization', 'ubcg'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="ubcg_enable_seo" id="ubcg_enable_seo" value="1" <?php checked(get_option('ubcg_enable_seo', true), 1); ?>>
                                <?php _e('Enable SEO-friendly markup (Schema.org, semantic HTML)', 'ubcg'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="ubcg-settings-section">
                <h2><?php _e('Available Blocks', 'ubcg'); ?></h2>
                <p class="description"><?php _e('The following blocks are available in the WordPress block editor:', 'ubcg'); ?></p>
                
                <div class="ubcg-blocks-list">
                    <div class="ubcg-block-item">
                        <h3><?php _e('Latest Posts Grid', 'ubcg'); ?></h3>
                        <p><?php _e('Display your latest articles in a responsive 3x3 grid with pagination', 'ubcg'); ?></p>
                    </div>
                    
                    <div class="ubcg-block-item">
                        <h3><?php _e('Featured Posts', 'ubcg'); ?></h3>
                        <p><?php _e('Showcase featured content prominently on your homepage', 'ubcg'); ?></p>
                    </div>
                    
                    <div class="ubcg-block-item">
                        <h3><?php _e('Category Showcase', 'ubcg'); ?></h3>
                        <p><?php _e('Display categories with counts and descriptions', 'ubcg'); ?></p>
                    </div>
                    
                    <div class="ubcg-block-item">
                        <h3><?php _e('Tag Cloud Gaming', 'ubcg'); ?></h3>
                        <p><?php _e('Interactive tag cloud with post counts', 'ubcg'); ?></p>
                    </div>
                    
                    <div class="ubcg-block-item">
                        <h3><?php _e('Gaming Hero', 'ubcg'); ?></h3>
                        <p><?php _e('Eye-catching hero section for your homepage', 'ubcg'); ?></p>
                    </div>
                    
                    <div class="ubcg-block-item">
                        <h3><?php _e('Review Card', 'ubcg'); ?></h3>
                        <p><?php _e('Display game reviews with star ratings', 'ubcg'); ?></p>
                    </div>
                    
                    <div class="ubcg-block-item">
                        <h3><?php _e('Game Specs', 'ubcg'); ?></h3>
                        <p><?php _e('Show technical specifications for games', 'ubcg'); ?></p>
                    </div>
                    
                    <div class="ubcg-block-item">
                        <h3><?php _e('Streaming Platforms', 'ubcg'); ?></h3>
                        <p><?php _e('Display available cloud gaming platforms', 'ubcg'); ?></p>
                    </div>
                    
                    <div class="ubcg-block-item">
                        <h3><?php _e('Performance Stats', 'ubcg'); ?></h3>
                        <p><?php _e('Show performance metrics and statistics', 'ubcg'); ?></p>
                    </div>
                    
                    <div class="ubcg-block-item">
                        <h3><?php _e('Newsletter Signup', 'ubcg'); ?></h3>
                        <p><?php _e('Collect email subscriptions from visitors', 'ubcg'); ?></p>
                    </div>
                </div>
            </div>
            
            <?php submit_button(__('Save Settings', 'ubcg')); ?>
        </form>
    </div>
    
    <div class="ubcg-settings-sidebar">
        <div class="ubcg-sidebar-box">
            <h3><?php _e('Documentation', 'ubcg'); ?></h3>
            <p><?php _e('Learn how to use all blocks and widgets effectively for your cloud gaming blog.', 'ubcg'); ?></p>
            <a href="#" class="button button-secondary"><?php _e('View Documentation', 'ubcg'); ?></a>
        </div>
        
        <div class="ubcg-sidebar-box">
            <h3><?php _e('Support', 'ubcg'); ?></h3>
            <p><?php _e('Need help? Get support from our team.', 'ubcg'); ?></p>
            <a href="#" class="button button-secondary"><?php _e('Get Support', 'ubcg'); ?></a>
        </div>
    </div>
</div>
