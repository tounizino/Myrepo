<?php
/**
 * Analytics Template
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$analytics = CLBP_Settings::get( 'analytics', array() );
arsort( $analytics );
?>

<div class="wrap clbp-admin-wrap">
    <div class="clbp-admin-header">
        <h1><?php esc_html_e( 'Block Analytics', 'cloud-layout-builder-pro' ); ?></h1>
    </div>

    <p><?php esc_html_e( 'Track which blocks are getting the most engagement. Data is tracked automatically when users view blocks on the frontend.', 'cloud-layout-builder-pro' ); ?></p>

    <table class="clbp-analytics-table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Block', 'cloud-layout-builder-pro' ); ?></th>
                <th><?php esc_html_e( 'Views', 'cloud-layout-builder-pro' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ( ! empty( $analytics ) ) : ?>
                <?php foreach ( $analytics as $block => $views ) : ?>
                    <tr>
                        <td><?php echo esc_html( ucfirst( str_replace( '-', ' ', $block ) ) ); ?></td>
                        <td><?php echo esc_html( CLBP_Helpers::format_number( $views ) ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="2" style="text-align: center; padding: 24px;">
                        <?php esc_html_e( 'Analytics will appear once your blocks receive traffic.', 'cloud-layout-builder-pro' ); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
