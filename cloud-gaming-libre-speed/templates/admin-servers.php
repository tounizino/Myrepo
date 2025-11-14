<?php
/**
 * Admin server presets page.
 *
 * @package CloudGamingSpeedTest
 */

if (!defined('ABSPATH')) {
    exit;
}

$servers = CGST_Database::get_servers();
?>

<div class="wrap cgst-admin">
    <h1><?php esc_html_e('Server Presets', 'cloud-gaming-speed-test'); ?></h1>
    <p class="description">
        <?php esc_html_e('Manage LibreSpeed server backends used for auto-selection or manual testing. Each server should point to your deployed LibreSpeed backend endpoints.', 'cloud-gaming-speed-test'); ?>
    </p>

    <form id="cgst-server-form" class="cgst-admin-form">
        <input type="hidden" name="id" id="cgst-server-id" value="" />

        <h2><?php esc_html_e('Add / Update Server', 'cloud-gaming-speed-test'); ?></h2>

        <div class="cgst-admin-grid">
            <div>
                <label for="cgst-server-name"><?php esc_html_e('Server Name', 'cloud-gaming-speed-test'); ?></label>
                <input type="text" id="cgst-server-name" name="name" required />
            </div>
            <div>
                <label for="cgst-server-location"><?php esc_html_e('Location Label', 'cloud-gaming-speed-test'); ?></label>
                <input type="text" id="cgst-server-location" name="location" required />
            </div>
            <div>
                <label for="cgst-server-backend"><?php esc_html_e('Base Backend URL', 'cloud-gaming-speed-test'); ?></label>
                <input type="url" id="cgst-server-backend" name="backend" placeholder="https://speed.example.com/region/" required />
            </div>
            <div>
                <label for="cgst-server-weight"><?php esc_html_e('Priority Weight', 'cloud-gaming-speed-test'); ?></label>
                <input type="number" id="cgst-server-weight" name="weight" value="0" min="0" />
            </div>
        </div>

        <div class="cgst-admin-grid">
            <div>
                <label for="cgst-download-path"><?php esc_html_e('Download Endpoint Path', 'cloud-gaming-speed-test'); ?></label>
                <input type="text" id="cgst-download-path" name="download_path" value="download.php" />
                <p class="description"><?php esc_html_e('Default LibreSpeed endpoint is typically garbage.php or download.php', 'cloud-gaming-speed-test'); ?></p>
            </div>
            <div>
                <label for="cgst-upload-path"><?php esc_html_e('Upload Endpoint Path', 'cloud-gaming-speed-test'); ?></label>
                <input type="text" id="cgst-upload-path" name="upload_path" value="upload.php" />
            </div>
            <div>
                <label for="cgst-ping-path"><?php esc_html_e('Ping Endpoint Path', 'cloud-gaming-speed-test'); ?></label>
                <input type="text" id="cgst-ping-path" name="ping_path" value="ping.php" />
            </div>
            <div>
                <label for="cgst-server-icon"><?php esc_html_e('Dashicon', 'cloud-gaming-speed-test'); ?></label>
                <input type="text" id="cgst-server-icon" name="icon" value="dashicons-admin-site" />
                <p class="description">
                    <?php esc_html_e('Use any Dashicons class (e.g., dashicons-admin-site, dashicons-location-alt).', 'cloud-gaming-speed-test'); ?>
                </p>
            </div>
        </div>

        <div class="cgst-admin-grid">
            <div>
                <label for="cgst-geo-lat"><?php esc_html_e('Latitude (optional)', 'cloud-gaming-speed-test'); ?></label>
                <input type="text" id="cgst-geo-lat" name="geo_lat" />
            </div>
            <div>
                <label for="cgst-geo-lng"><?php esc_html_e('Longitude (optional)', 'cloud-gaming-speed-test'); ?></label>
                <input type="text" id="cgst-geo-lng" name="geo_lng" />
            </div>
        </div>

        <label for="cgst-server-notes"><?php esc_html_e('Notes / Tips', 'cloud-gaming-speed-test'); ?></label>
        <textarea id="cgst-server-notes" name="notes" rows="3" placeholder="Ideal for Stadia, fiber optimized, etc"></textarea>

        <div class="cgst-admin-actions">
            <button type="submit" class="button button-primary">&nbsp;<?php esc_html_e('Save Server', 'cloud-gaming-speed-test'); ?>&nbsp;</button>
            <button type="button" id="cgst-server-reset" class="button"><?php esc_html_e('Reset Form', 'cloud-gaming-speed-test'); ?></button>
        </div>
    </form>

    <h2><?php esc_html_e('Configured Servers', 'cloud-gaming-speed-test'); ?></h2>

    <table class="cgst-admin-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Name', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Location', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Backend URL', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Weight', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Actions', 'cloud-gaming-speed-test'); ?></th>
            </tr>
        </thead>
        <tbody id="cgst-server-list">
            <?php if (!empty($servers)) : ?>
                <?php foreach ($servers as $server) : ?>
                    <tr data-server='<?php echo wp_json_encode($server); ?>'>
                        <td>
                            <strong><?php echo esc_html($server['name']); ?></strong><br />
                            <span class="cgst-pill"><?php echo esc_html($server['id']); ?></span>
                        </td>
                        <td><?php echo esc_html($server['location']); ?></td>
                        <td><code><?php echo esc_html($server['backend']); ?></code></td>
                        <td><?php echo esc_html(isset($server['weight']) ? $server['weight'] : 0); ?></td>
                        <td>
                            <div class="cgst-admin-actions">
                                <button type="button" class="button cgst-edit-server"><?php esc_html_e('Edit', 'cloud-gaming-speed-test'); ?></button>
                                <button type="button" class="button button-danger cgst-delete-server"><?php esc_html_e('Delete', 'cloud-gaming-speed-test'); ?></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5"><?php esc_html_e('No servers configured yet.', 'cloud-gaming-speed-test'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2><?php esc_html_e('Server JSON Example', 'cloud-gaming-speed-test'); ?></h2>
    <p><?php esc_html_e('Use this structure when importing servers or syncing with external configuration tools.', 'cloud-gaming-speed-test'); ?></p>
    <pre><code>{
  "id": "us-east",
  "name": "US East - Ashburn",
  "location": "Ashburn, VA, USA",
  "backend": "https://speed.example.com/us-east/",
  "download_path": "download.php",
  "upload_path": "upload.php",
  "ping_path": "ping.php",
  "geo": { "lat": 39.0438, "lng": -77.4874 },
  "notes": "Ideal for gamers on the US East Coast"
}</code></pre>
</div>
