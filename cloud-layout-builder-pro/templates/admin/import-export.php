<?php
/**
 * Import/Export Template
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap clbp-admin-wrap">
    <div class="clbp-admin-header">
        <h1><?php esc_html_e( 'Import / Export', 'cloud-layout-builder-pro' ); ?></h1>
    </div>

    <div class="clbp-settings-grid">
        <div class="clbp-settings-card">
            <h3><?php esc_html_e( 'Export Settings', 'cloud-layout-builder-pro' ); ?></h3>
            <p><?php esc_html_e( 'Export all your settings and block configurations to a JSON file.', 'cloud-layout-builder-pro' ); ?></p>
            <button type="button" id="clbp-export-button" class="button button-primary">
                <?php esc_html_e( 'Download Export File', 'cloud-layout-builder-pro' ); ?>
            </button>
        </div>

        <div class="clbp-settings-card">
            <h3><?php esc_html_e( 'Import Settings', 'cloud-layout-builder-pro' ); ?></h3>
            <p><?php esc_html_e( 'Paste your JSON configuration below and click import.', 'cloud-layout-builder-pro' ); ?></p>
            <form id="clbp-import-form">
                <textarea id="clbp-import-input" rows="8" placeholder="<?php esc_attr_e( 'Paste your JSON here...', 'cloud-layout-builder-pro' ); ?>" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #CBD5E1;"></textarea>
                <button type="submit" class="button button-primary" style="margin-top: 12px;">
                    <?php esc_html_e( 'Import Configuration', 'cloud-layout-builder-pro' ); ?>
                </button>
            </form>
        </div>
    </div>

    <div class="clbp-settings-card" style="margin-top: 24px; background: #FEF3C7; border: 1px solid #FCD34D;">
        <h3><?php esc_html_e( '⚠️ Important Notes', 'cloud-layout-builder-pro' ); ?></h3>
        <ul style="margin: 0; padding-left: 20px;">
            <li><?php esc_html_e( 'Importing will replace all current settings and blocks', 'cloud-layout-builder-pro' ); ?></li>
            <li><?php esc_html_e( 'Always backup your current settings before importing', 'cloud-layout-builder-pro' ); ?></li>
            <li><?php esc_html_e( 'Use the export feature to create backups before making major changes', 'cloud-layout-builder-pro' ); ?></li>
        </ul>
    </div>
</div>
