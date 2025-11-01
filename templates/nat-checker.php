<?php
/**
 * NAT Checker Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$theme_class = 'theme-' . $settings['theme'];
$font_size_class = 'font-size-' . $settings['fontSize'];
$container_width = $settings['containerWidth'] . 'px';
?>

<div class="unpc-wrapper unpc-nat-only <?php echo esc_attr($theme_class . ' ' . $font_size_class); ?>" data-animations="<?php echo esc_attr($settings['enableAnimations']); ?>">
    <style>
        .unpc-wrapper {
            --container-width: <?php echo esc_attr($container_width); ?>;
            --color-primary: <?php echo esc_attr($settings['colorPrimary']); ?>;
            --color-success: <?php echo esc_attr($settings['colorSuccess']); ?>;
            --color-warning: <?php echo esc_attr($settings['colorWarning']); ?>;
            --color-error: <?php echo esc_attr($settings['colorError']); ?>;
            --color-info: <?php echo esc_attr($settings['colorInfo']); ?>;
        }
        <?php if (!empty($settings['customCSS'])) : ?>
        <?php echo wp_kses_post($settings['customCSS']); ?>
        <?php endif; ?>
    </style>

    <div class="unpc-tool-section">
        <h2 class="unpc-section-title">
            <i class="fas fa-network-wired"></i>
            <?php esc_html_e('NAT Type Checker', 'ultimate-nat-port-checker'); ?>
        </h2>
        <button class="unpc-check-nat-btn">
            <i class="fas fa-search"></i>
            <?php esc_html_e('Check NAT Type', 'ultimate-nat-port-checker'); ?>
        </button>
        <div class="unpc-nat-result" style="display:none;"></div>
        <?php if ($settings['showTechnicalLog'] === '1') : ?>
        <div class="unpc-technical-log-wrapper" style="display:none;">
            <button class="unpc-toggle-log">
                <i class="fas fa-chevron-down"></i>
                <span><?php esc_html_e('Show Technical Details', 'ultimate-nat-port-checker'); ?></span>
            </button>
            <div class="unpc-technical-log" style="display:none;"></div>
        </div>
        <?php endif; ?>
    </div>
</div>
