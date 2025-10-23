<?php
/**
 * Dashboard Only Template - Status Dashboard without Launcher
 * Shortcode: [cloud_gaming_dashboard]
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Cloud Gaming Dashboard - Status Only View -->
<div class="cloud-gaming-container cloud-gaming-dashboard-only" id="cloud-gaming-dashboard-status">
    <div class="cgd-loading-screen" style="text-align: center; padding: 60px 20px;">
        <div style="font-size: 48px; margin-bottom: 20px;">⏳</div>
        <p style="color: white; font-size: 18px; font-weight: 600;">Loading Status Dashboard...</p>
        <p style="color: rgba(255,255,255,0.8); font-size: 14px; margin-top: 10px;">Fetching real-time data</p>
    </div>
</div>

<!-- Hide launcher section for dashboard-only view -->
<style>
.cloud-gaming-dashboard-only .cgd-launcher-section {
    display: none !important;
}
</style>
