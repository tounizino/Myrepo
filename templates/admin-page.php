<?php
/**
 * Admin Settings Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$defaults = UNPC_Admin_Settings::get_defaults();
$options = array();

foreach ($defaults as $key => $default) {
    $options[$key] = get_option($key, $default);
}

$tabs = array(
    'general' => __('General', 'ultimate-nat-port-checker'),
    'display' => __('Display', 'ultimate-nat-port-checker'),
    'nat' => __('NAT Checker', 'ultimate-nat-port-checker'),
    'port' => __('Port Checker', 'ultimate-nat-port-checker'),
    'colors' => __('Colors', 'ultimate-nat-port-checker'),
    'advanced' => __('Advanced', 'ultimate-nat-port-checker'),
);
?>
<div class="wrap unpc-admin">
    <h1 class="unpc-admin__title"><span class="dashicons dashicons-networking"></span> <?php esc_html_e('Ultimate NAT & Port Checker Settings', 'ultimate-nat-port-checker'); ?></h1>
    <p class="unpc-admin__subtitle"><?php esc_html_e('Customize your futuristic 2026 cloud gaming diagnostic suite with precision controls.', 'ultimate-nat-port-checker'); ?></p>

    <h2 class="nav-tab-wrapper">
        <?php foreach ($tabs as $tab_slug => $tab_label) : ?>
            <a href="<?php echo esc_url(add_query_arg('tab', $tab_slug)); ?>" class="nav-tab <?php echo $active_tab === $tab_slug ? 'nav-tab-active' : ''; ?>">
                <?php echo esc_html($tab_label); ?>
            </a>
        <?php endforeach; ?>
    </h2>

    <form method="post" action="">
        <?php wp_nonce_field('unpc_settings_nonce'); ?>
        <input type="hidden" name="active_tab" value="<?php echo esc_attr($active_tab); ?>" />

        <div class="unpc-admin__panel">
            <?php switch ($active_tab) {
                case 'general':
                    include UNPC_PLUGIN_DIR . 'templates/settings-general.php';
                    break;
                case 'display':
                    include UNPC_PLUGIN_DIR . 'templates/settings-display.php';
                    break;
                case 'nat':
                    include UNPC_PLUGIN_DIR . 'templates/settings-nat.php';
                    break;
                case 'port':
                    include UNPC_PLUGIN_DIR . 'templates/settings-port.php';
                    break;
                case 'colors':
                    include UNPC_PLUGIN_DIR . 'templates/settings-colors.php';
                    break;
                case 'advanced':
                    include UNPC_PLUGIN_DIR . 'templates/settings-advanced.php';
                    break;
                default:
                    include UNPC_PLUGIN_DIR . 'templates/settings-general.php';
                    break;
            } ?>
        </div>

        <p class="submit">
            <button class="button button-primary button-hero" name="unpc_settings_submit" value="1">
                <span class="dashicons dashicons-saved"></span> <?php esc_html_e('Save Futuristic Config', 'ultimate-nat-port-checker'); ?>
            </button>
        </p>
    </form>

    <div class="unpc-admin__shortcodes">
        <h2><span class="dashicons dashicons-admin-links"></span> <?php esc_html_e('Available Shortcodes', 'ultimate-nat-port-checker'); ?></h2>
        <ul>
            <li><strong>[ultimate_nat_port_checker]</strong> – <?php esc_html_e('Displays the full NAT & Port checker suite.', 'ultimate-nat-port-checker'); ?></li>
            <li><strong>[ultimate_nat_checker]</strong> – <?php esc_html_e('Displays only the NAT checker module.', 'ultimate-nat-port-checker'); ?></li>
            <li><strong>[ultimate_port_checker]</strong> – <?php esc_html_e('Displays only the port checker module.', 'ultimate-nat-port-checker'); ?></li>
        </ul>
    </div>
</div>
