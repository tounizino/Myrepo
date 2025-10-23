<?php
/**
 * Combined Template - Full Dashboard with Status & Launcher
 * Shortcode: [cloud_gaming_combined]
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Cloud Gaming Dashboard - Combined View -->
<div class="cloud-gaming-container" id="cloud-gaming-dashboard-combined">
    <div class="cgd-loading-screen" style="text-align: center; padding: 60px 20px;">
        <div style="font-size: 48px; margin-bottom: 20px;">⏳</div>
        <p style="color: white; font-size: 18px; font-weight: 600;">Loading Cloud Gaming Dashboard...</p>
        <p style="color: rgba(255,255,255,0.8); font-size: 14px; margin-top: 10px;">Checking service statuses</p>
    </div>
</div>

<!-- Schema.org structured data for SEO -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "Cloud Gaming Status Dashboard",
    "description": "Real-time status monitoring and quick launcher for all major cloud gaming platforms including GeForce NOW, Xbox Cloud Gaming, Boosteroid, Shadow, and more.",
    "applicationCategory": "UtilitiesApplication",
    "operatingSystem": "Any",
    "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
    },
    "featureList": [
        "Real-time status monitoring",
        "Quick launch buttons",
        "Service favorites",
        "Response time tracking",
        "Dark mode support",
        "Mobile responsive"
    ]
}
</script>

<style>
/* Additional inline styles for loading screen */
.cgd-loading-screen {
    animation: fadeIn 0.5s ease !important;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
