<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap cgrt-admin">
    <h1><?php echo esc_html__( 'Cloud Gaming Readiness Test', 'cloud-gaming-readiness-test' ); ?></h1>

    <div class="cgrt-admin__hero">
        <div class="cgrt-admin__hero-content">
            <h2><?php echo esc_html__( 'Welcome to your diagnostic control center', 'cloud-gaming-readiness-test' ); ?></h2>
            <p><?php echo esc_html__( 'This plugin provides a professional cloud gaming readiness test designed to evaluate real-world network performance: latency, jitter, packet loss, and stability.', 'cloud-gaming-readiness-test' ); ?></p>
        </div>
    </div>

    <div class="cgrt-admin__cards">
    <div class="cgrt-admin__card">
        <div class="cgrt-admin__card-icon"><span class="dashicons dashicons-shortcode"></span></div>
        <h3><?php echo esc_html__( 'Usage', 'cloud-gaming-readiness-test' ); ?></h3>

            <div class="cgrt-admin__code-block">
                <code>[cloud_gaming_readiness_test]</code>
            </div>
            <p class="cgrt-admin__muted"><?php echo esc_html__( 'Or use the Gutenberg block: "Cloud Gaming Readiness Test"', 'cloud-gaming-readiness-test' ); ?></p>
        </div>

        <div class="cgrt-admin__card">
            <div class="cgrt-admin__card-icon"><span class="dashicons dashicons-admin-site-alt3"></span></div>
            <h3><?php echo esc_html__( 'Configure Platforms', 'cloud-gaming-readiness-test' ); ?></h3>
            <p><?php echo esc_html__( 'Add, edit, and enable cloud gaming platforms and their server endpoints.', 'cloud-gaming-readiness-test' ); ?></p>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgrt-platforms' ) ); ?>" class="button button-primary">
                <?php echo esc_html__( 'Manage platforms', 'cloud-gaming-readiness-test' ); ?>
            </a>
        </div>

        <div class="cgrt-admin__card">
            <div class="cgrt-admin__card-icon"><span class="dashicons dashicons-admin-settings"></span></div>
            <h3><?php echo esc_html__( 'Tune Settings', 'cloud-gaming-readiness-test' ); ?></h3>
            <p><?php echo esc_html__( 'Adjust test duration, intensity, weighting logic, and readiness tiers.', 'cloud-gaming-readiness-test' ); ?></p>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=cgrt-settings' ) ); ?>" class="button button-primary">
                <?php echo esc_html__( 'Adjust settings', 'cloud-gaming-readiness-test' ); ?>
            </a>
        </div>
    </div>

    <?php if ( isset( $stats['enabled'] ) && $stats['enabled'] ) : ?>
        <div class="cgrt-admin__stats">
            <h2><?php echo esc_html__( 'Aggregated Test Statistics', 'cloud-gaming-readiness-test' ); ?></h2>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__( 'Metric', 'cloud-gaming-readiness-test' ); ?></th>
                        <th><?php echo esc_html__( 'Value', 'cloud-gaming-readiness-test' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo esc_html__( 'Total tests submitted', 'cloud-gaming-readiness-test' ); ?></td>
                        <td><?php echo esc_html( number_format_i18n( $stats['total_tests'] ) ); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo esc_html__( 'Average score (all time)', 'cloud-gaming-readiness-test' ); ?></td>
                        <td><?php echo esc_html( $stats['avg_score'] ); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo esc_html__( 'Tests (last 24h)', 'cloud-gaming-readiness-test' ); ?></td>
                        <td><?php echo esc_html( number_format_i18n( $stats['tests_last_24h'] ) ); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo esc_html__( 'Average score (last 24h)', 'cloud-gaming-readiness-test' ); ?></td>
                        <td><?php echo esc_html( $stats['avg_score_24h'] ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <div class="notice notice-info">
            <p><?php echo esc_html__( 'Result storage is currently disabled. Enable it in Settings to see aggregated statistics.', 'cloud-gaming-readiness-test' ); ?></p>
        </div>
    <?php endif; ?>

    <div class="cgrt-admin__footer">
        <p>
            <strong><?php echo esc_html__( 'Important:', 'cloud-gaming-readiness-test' ); ?></strong>
            <?php echo esc_html__( 'For accurate results, configure at least one enabled endpoint that returns fast responses and allows CORS (cross-origin requests).', 'cloud-gaming-readiness-test' ); ?>
        </p>
    </div>
</div>
