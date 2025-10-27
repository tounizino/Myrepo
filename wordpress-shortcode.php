<?php
/**
 * Cloud Gaming Home Section - WordPress Shortcode
 * 
 * Add this code to your theme's functions.php file or create a custom plugin.
 * Usage: [cloud_gaming_home]
 * 
 * This allows you to add the cloud gaming home section anywhere using a shortcode.
 */

function cloud_gaming_home_shortcode() {
    ob_start();
    ?>
    <style>
    .cgoh-container * {
        box-sizing: border-box;
    }

    .cgoh-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        line-height: 1.6;
        color: #333;
    }

    .cgoh-hero {
        text-align: center;
        padding: 60px 20px;
        margin-bottom: 60px;
        background: linear-gradient(135deg, #f5f9ff 0%, #e8f4ff 100%);
        border-radius: 12px;
        border: 2px solid #2196F3;
    }

    .cgoh-hero h1 {
        font-size: 3rem;
        color: #0066CC;
        margin-bottom: 20px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .cgoh-hero p {
        font-size: 1.25rem;
        color: #555;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.8;
    }

    .cgoh-section-title {
        font-size: 2.25rem;
        color: #0066CC;
        margin-bottom: 40px;
        font-weight: 600;
        text-align: center;
        position: relative;
        padding-bottom: 15px;
    }

    .cgoh-section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #1E88E5, #2196F3);
        border-radius: 2px;
    }

    .cgoh-section {
        margin-bottom: 80px;
    }

    .cgoh-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .cgoh-card {
        padding: 30px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        background: #fff;
        transition: all 0.3s ease;
    }

    .cgoh-card:hover {
        border-color: #2196F3;
        transform: translateY(-5px);
    }

    .cgoh-card h3 {
        font-size: 1.5rem;
        color: #0066CC;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .cgoh-card p {
        color: #666;
        margin-bottom: 15px;
        line-height: 1.7;
    }

    .cgoh-card ul {
        list-style: none;
        padding-left: 0;
    }

    .cgoh-card li {
        color: #555;
        margin-bottom: 10px;
        padding-left: 20px;
        position: relative;
    }

    .cgoh-card li::before {
        content: '→';
        position: absolute;
        left: 0;
        color: #2196F3;
        font-weight: bold;
    }

    .cgoh-card a {
        display: inline-block;
        margin-top: 15px;
        color: #1E88E5;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .cgoh-card a:hover {
        color: #0066CC;
    }

    .cgoh-cta {
        text-align: center;
        padding: 50px 30px;
        background: linear-gradient(135deg, #0066CC 0%, #2196F3 100%);
        border-radius: 12px;
        margin-top: 60px;
    }

    .cgoh-cta h2 {
        font-size: 2rem;
        color: #fff;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .cgoh-cta p {
        font-size: 1.125rem;
        color: #fff;
        max-width: 600px;
        margin: 0 auto 30px;
        line-height: 1.7;
        opacity: 0.95;
    }

    .cgoh-cta-button {
        display: inline-block;
        padding: 15px 40px;
        background: #fff;
        color: #0066CC;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.125rem;
        transition: all 0.3s ease;
        border: 2px solid #fff;
    }

    .cgoh-cta-button:hover {
        background: transparent;
        color: #fff;
    }

    @media (max-width: 768px) {
        .cgoh-hero h1 {
            font-size: 2rem;
        }

        .cgoh-hero p {
            font-size: 1.125rem;
        }

        .cgoh-section-title {
            font-size: 1.75rem;
        }

        .cgoh-grid {
            grid-template-columns: 1fr;
        }

        .cgoh-container {
            padding: 20px 15px;
        }
    }

    @media (max-width: 480px) {
        .cgoh-hero {
            padding: 40px 20px;
        }

        .cgoh-hero h1 {
            font-size: 1.75rem;
        }

        .cgoh-card {
            padding: 20px;
        }
    }
    </style>

    <div class="cgoh-container">
        <section class="cgoh-hero">
            <h1><?php echo esc_html__('Cloud Gaming Optimization Hub', 'cloud-gaming'); ?></h1>
            <p><?php echo esc_html__('Master your cloud gaming experience with expert troubleshooting guides, performance optimization tools, and proven tips to eliminate lag and maximize quality.', 'cloud-gaming'); ?></p>
        </section>

        <section class="cgoh-section">
            <h2 class="cgoh-section-title"><?php echo esc_html__('Troubleshooting Resources', 'cloud-gaming'); ?></h2>
            <div class="cgoh-grid">
                <article class="cgoh-card">
                    <h3><?php echo esc_html__('Connection Issues', 'cloud-gaming'); ?></h3>
                    <p><?php echo esc_html__('Resolve common connectivity problems that affect your gaming sessions.', 'cloud-gaming'); ?></p>
                    <ul>
                        <li><?php echo esc_html__('Check network bandwidth requirements', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Test connection stability', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Configure router QoS settings', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Switch to ethernet connection', 'cloud-gaming'); ?></li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/connection-guide')); ?>"><?php echo esc_html__('Full Guide →', 'cloud-gaming'); ?></a>
                </article>

                <article class="cgoh-card">
                    <h3><?php echo esc_html__('Input Lag & Latency', 'cloud-gaming'); ?></h3>
                    <p><?php echo esc_html__('Minimize delay between your inputs and on-screen actions.', 'cloud-gaming'); ?></p>
                    <ul>
                        <li><?php echo esc_html__('Optimize network routes', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Reduce wireless interference', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Close bandwidth-heavy applications', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Use gaming mode on displays', 'cloud-gaming'); ?></li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/latency-guide')); ?>"><?php echo esc_html__('Full Guide →', 'cloud-gaming'); ?></a>
                </article>

                <article class="cgoh-card">
                    <h3><?php echo esc_html__('Video Quality Problems', 'cloud-gaming'); ?></h3>
                    <p><?php echo esc_html__('Improve stream quality and eliminate visual artifacts.', 'cloud-gaming'); ?></p>
                    <ul>
                        <li><?php echo esc_html__('Adjust streaming quality settings', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Update graphics drivers', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Check hardware acceleration', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Monitor CPU/GPU usage', 'cloud-gaming'); ?></li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/quality-guide')); ?>"><?php echo esc_html__('Full Guide →', 'cloud-gaming'); ?></a>
                </article>

                <article class="cgoh-card">
                    <h3><?php echo esc_html__('Audio Sync Issues', 'cloud-gaming'); ?></h3>
                    <p><?php echo esc_html__('Fix audio delays and synchronization problems.', 'cloud-gaming'); ?></p>
                    <ul>
                        <li><?php echo esc_html__('Disable audio enhancements', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Update audio drivers', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Check audio buffer settings', 'cloud-gaming'); ?></li>
                        <li><?php echo esc_html__('Test different audio outputs', 'cloud-gaming'); ?></li>
                    </ul>
                    <a href="<?php echo esc_url(home_url('/audio-guide')); ?>"><?php echo esc_html__('Full Guide →', 'cloud-gaming'); ?></a>
                </article>
            </div>
        </section>

        <section class="cgoh-cta">
            <h2><?php echo esc_html__('Ready to Optimize Your Experience?', 'cloud-gaming'); ?></h2>
            <p><?php echo esc_html__('Join thousands of gamers who have transformed their cloud gaming performance with our expert guides and community support.', 'cloud-gaming'); ?></p>
            <a href="<?php echo esc_url(home_url('/get-started')); ?>" class="cgoh-cta-button"><?php echo esc_html__('Get Started Now', 'cloud-gaming'); ?></a>
        </section>
    </div>
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('cloud_gaming_home', 'cloud_gaming_home_shortcode');

/**
 * INSTALLATION INSTRUCTIONS:
 * 
 * Method 1: Add to functions.php
 * 1. Copy this entire file content
 * 2. Paste into your theme's functions.php file (Appearance > Theme Editor)
 * 3. Save the file
 * 4. Use [cloud_gaming_home] shortcode in any page or post
 * 
 * Method 2: Create a custom plugin
 * 1. Create a folder: wp-content/plugins/cloud-gaming-home/
 * 2. Create file: cloud-gaming-home.php with this code
 * 3. Add plugin header (see below)
 * 4. Activate the plugin from WordPress admin
 * 5. Use [cloud_gaming_home] shortcode anywhere
 * 
 * Plugin Header (add at top of file):
 * <?php
 * /*
 * Plugin Name: Cloud Gaming Home Section
 * Description: Adds a cloud gaming optimization home section via shortcode [cloud_gaming_home]
 * Version: 1.0.0
 * Author: Your Name
 * /
 * 
 * CUSTOMIZATION:
 * - Update URLs: Change home_url('/connection-guide') to your actual page URLs
 * - Modify colors: Search and replace color codes in the <style> section
 * - Add sections: Copy existing section structure and paste within the container
 * - Translation ready: Uses WordPress translation functions
 */
?>
