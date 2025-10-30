<?php
/**
 * Homepage Builder Admin Template
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$available_blocks = array(
    'featured_hero'      => __( 'Featured Hero', 'cloud-layout-builder-pro' ),
    'latest_articles'    => __( 'Latest Articles Grid', 'cloud-layout-builder-pro' ),
    'category_highlight' => __( 'Category Highlight', 'cloud-layout-builder-pro' ),
    'tag_topic'          => __( 'Tag/Topic Block', 'cloud-layout-builder-pro' ),
    'carousel'           => __( 'Content Carousel', 'cloud-layout-builder-pro' ),
    'mixed_content'      => __( 'Mixed Content', 'cloud-layout-builder-pro' ),
    'custom_links'       => __( 'Resource Links', 'cloud-layout-builder-pro' ),
    'newsletter'         => __( 'Newsletter Signup', 'cloud-layout-builder-pro' ),
    'quote_tip'          => __( 'Quote / Tip', 'cloud-layout-builder-pro' ),
);
?>

<div class="wrap clbp-admin-wrap">
    <div class="clbp-admin-header">
        <h1><?php esc_html_e( 'Homepage Builder', 'cloud-layout-builder-pro' ); ?></h1>
        <button type="button" id="clbp-add-block" class="button button-secondary">
            <?php esc_html_e( '+ Add Block', 'cloud-layout-builder-pro' ); ?>
        </button>
    </div>

    <div class="clbp-builder-description">
        <p><?php esc_html_e( 'Drag and drop blocks to reorder. Toggle visibility and configure each block as needed. Use the shortcode [clbp_homepage] to display this layout.', 'cloud-layout-builder-pro' ); ?></p>
    </div>

    <div id="clbp-block-library" class="clbp-block-library" aria-hidden="true">
        <div class="clbp-block-library__inner">
            <div class="clbp-block-library__header">
                <h2><?php esc_html_e( 'Choose a block to add', 'cloud-layout-builder-pro' ); ?></h2>
                <button type="button" class="button-link clbp-library-close" aria-label="<?php esc_attr_e( 'Close', 'cloud-layout-builder-pro' ); ?>">
                    <span class="dashicons dashicons-no"></span>
                </button>
            </div>
            <p><?php esc_html_e( 'Select a pre-designed block. You can customize each block after adding it.', 'cloud-layout-builder-pro' ); ?></p>
            <div class="clbp-block-library__grid">
                <?php foreach ( $available_blocks as $slug => $label ) : ?>
                    <button type="button" class="clbp-library-item" data-block-type="<?php echo esc_attr( $slug ); ?>">
                        <span class="clbp-library-item__title"><?php echo esc_html( $label ); ?></span>
                        <span class="clbp-library-item__slug"><?php echo esc_html( $slug ); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <form id="clbp-builder-form" method="post">
        <div class="clbp-block-list">
            <?php foreach ( $blocks as $block ) : ?>
                <div class="clbp-block-card clbp-block-item" data-settings="<?php echo esc_attr( wp_json_encode( $block ) ); ?>">
                    <div class="clbp-block-info">
                        <span class="dashicons dashicons-menu"></span>
                        <h3 class="clbp-block-title-label"><?php echo esc_html( $available_blocks[ $block['type'] ] ?? $block['type'] ); ?></h3>
                        <label class="clbp-toggle">
                            <input type="checkbox" class="clbp-toggle-enabled" <?php checked( ! empty( $block['enabled'] ) ); ?>>
                            <span><?php esc_html_e( 'Enabled', 'cloud-layout-builder-pro' ); ?></span>
                        </label>
                    </div>
                    <div class="clbp-block-summary"></div>
                    <div class="clbp-block-actions">
                        <button type="button" class="clbp-btn-config" title="<?php esc_attr_e( 'Configure', 'cloud-layout-builder-pro' ); ?>">
                            <span class="dashicons dashicons-admin-generic"></span>
                        </button>
                        <button type="button" class="clbp-btn-delete" title="<?php esc_attr_e( 'Delete', 'cloud-layout-builder-pro' ); ?>">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="clbp-builder-actions">
            <button type="submit" class="button button-primary clbp-save-button">
                <?php esc_html_e( 'Save Layout', 'cloud-layout-builder-pro' ); ?>
            </button>
        </div>
    </form>

    <div class="clbp-shortcode-box" style="margin-top: 32px; padding: 20px; background: #F8FAFC; border-radius: 10px; border: 1px solid #E2E8F0;">
        <h3><?php esc_html_e( 'Usage', 'cloud-layout-builder-pro' ); ?></h3>
        <p><?php esc_html_e( 'Add this shortcode to any page or post to display your homepage layout:', 'cloud-layout-builder-pro' ); ?></p>
        <code style="display: inline-block; padding: 8px 16px; background: #fff; border-radius: 6px;">[clbp_homepage]</code>
        
        <h4 style="margin-top: 24px;"><?php esc_html_e( 'Individual Block Shortcodes:', 'cloud-layout-builder-pro' ); ?></h4>
        <ul style="list-style: none; padding: 0; display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
            <li><code>[clbp_latest_articles]</code></li>
            <li><code>[clbp_featured_hero]</code></li>
            <li><code>[clbp_category_highlight category="1"]</code></li>
            <li><code>[clbp_tag_topic tag="1"]</code></li>
            <li><code>[clbp_carousel]</code></li>
            <li><code>[clbp_mixed_content]</code></li>
            <li><code>[clbp_custom_links]</code></li>
            <li><code>[clbp_newsletter]</code></li>
            <li><code>[clbp_quote_tip]</code></li>
        </ul>
    </div>
</div>
