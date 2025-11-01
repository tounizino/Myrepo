<?php
/**
 * Port Checker Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$theme_class = 'theme-' . $settings['theme'];
$font_size_class = 'font-size-' . $settings['fontSize'];
$container_width = $settings['containerWidth'] . 'px';
?>

<div class="unpc-wrapper unpc-port-only <?php echo esc_attr($theme_class . ' ' . $font_size_class); ?>" data-animations="<?php echo esc_attr($settings['enableAnimations']); ?>">
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
            <i class="fas fa-plug"></i>
            <?php esc_html_e('Multi-Port Checker', 'ultimate-nat-port-checker'); ?>
        </h2>
        <div class="unpc-port-presets">
            <button class="unpc-preset-btn" data-ports="3074"><?php esc_html_e('Xbox Live', 'ultimate-nat-port-checker'); ?></button>
            <button class="unpc-preset-btn" data-ports="3478,3479,3480"><?php esc_html_e('PlayStation Network', 'ultimate-nat-port-checker'); ?></button>
            <button class="unpc-preset-btn" data-ports="1935,3478,3479"><?php esc_html_e('Steam', 'ultimate-nat-port-checker'); ?></button>
            <button class="unpc-preset-btn" data-ports="6000-6100"><?php esc_html_e('GeForce NOW', 'ultimate-nat-port-checker'); ?></button>
            <button class="unpc-preset-btn" data-ports="80,443,1935"><?php esc_html_e('Google Stadia', 'ultimate-nat-port-checker'); ?></button>
            <button class="unpc-preset-btn" data-ports="27015-27030"><?php esc_html_e('Steam Gaming', 'ultimate-nat-port-checker'); ?></button>
            <button class="unpc-preset-btn" data-ports="3074,3544,4500"><?php esc_html_e('Call of Duty', 'ultimate-nat-port-checker'); ?></button>
            <button class="unpc-preset-btn" data-ports="5000-5500"><?php esc_html_e('Shadow PC', 'ultimate-nat-port-checker'); ?></button>
            <?php
            $custom_presets = get_option('unpc_custom_presets', '');
            if (!empty($custom_presets)) {
                $presets = explode("\n", $custom_presets);
                foreach ($presets as $preset) {
                    $parts = explode(':', $preset);
                    if (count($parts) === 2) {
                        $label = trim($parts[0]);
                        $ports = trim($parts[1]);
                        echo '<button class="unpc-preset-btn" data-ports="' . esc_attr($ports) . '">' . esc_html($label) . '</button>';
                    }
                }
            }
            ?>
            <button class="unpc-preset-btn" data-ports="custom"><?php esc_html_e('Custom Ports', 'ultimate-nat-port-checker'); ?></button>
        </div>
        <div class="unpc-port-input">
            <input type="text" class="unpc-port-input-field" placeholder="<?php esc_attr_e('Enter port(s) - e.g., 80,443 or 3000-3100', 'ultimate-nat-port-checker'); ?>" value="3074">
            <button class="unpc-check-port-btn"><?php esc_html_e('Check Ports', 'ultimate-nat-port-checker'); ?></button>
        </div>
        <div class="unpc-port-result" style="display:none;"></div>
        <div class="unpc-port-details" style="display:none;"></div>
    </div>
</div>
