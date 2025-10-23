<?php
/**
 * Launcher Only Template - Quick Launch Widget without Status
 * Shortcode: [cloud_gaming_launcher]
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Cloud Gaming Launcher - Widget Only View -->
<div class="cloud-gaming-container cloud-gaming-launcher-only" id="cloud-gaming-launcher-widget">
    <div class="cgd-loading-screen" style="text-align: center; padding: 40px 20px;">
        <div style="font-size: 36px; margin-bottom: 15px;">🚀</div>
        <p style="color: white; font-size: 16px; font-weight: 600;">Loading Quick Launcher...</p>
    </div>
</div>

<!-- Hide status section for launcher-only view -->
<style>
.cloud-gaming-launcher-only .cgd-status-grid,
.cloud-gaming-launcher-only .cgd-stats-section {
    display: none !important;
}

.cloud-gaming-launcher-only {
    padding: 15px !important;
}

.cloud-gaming-launcher-only .cgd-launcher-section {
    background: transparent !important;
    padding: 0 !important;
}
</style>
