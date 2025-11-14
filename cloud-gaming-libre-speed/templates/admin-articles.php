<?php
/**
 * Admin articles/tips management page.
 *
 * @package CloudGamingSpeedTest
 */

if (!defined('ABSPATH')) {
    exit;
}

$articles = CGST_Database::get_articles();
?>

<div class="wrap cgst-admin">
    <h1><?php esc_html_e('Articles & Tips', 'cloud-gaming-speed-test'); ?></h1>
    <p class="description">
        <?php esc_html_e('Manage helpful optimization articles, guides, and resources displayed on the frontend after testing.', 'cloud-gaming-speed-test'); ?>
    </p>

    <form id="cgst-article-form" class="cgst-admin-form">
        <input type="hidden" name="id" id="cgst-article-id" value="" />

        <h2><?php esc_html_e('Add / Update Article', 'cloud-gaming-speed-test'); ?></h2>

        <label for="cgst-article-title"><?php esc_html_e('Article Title', 'cloud-gaming-speed-test'); ?></label>
        <input type="text" id="cgst-article-title" name="title" required />

        <label for="cgst-article-url"><?php esc_html_e('Article URL', 'cloud-gaming-speed-test'); ?></label>
        <input type="url" id="cgst-article-url" name="url" required />

        <label for="cgst-article-description"><?php esc_html_e('Description', 'cloud-gaming-speed-test'); ?></label>
        <textarea id="cgst-article-description" name="description" rows="3"></textarea>

        <label for="cgst-article-category"><?php esc_html_e('Category', 'cloud-gaming-speed-test'); ?></label>
        <input type="text" id="cgst-article-category" name="category" placeholder="Optimization, Networking, Guides" />

        <div class="cgst-admin-actions">
            <button type="submit" class="button button-primary"><?php esc_html_e('Save Article', 'cloud-gaming-speed-test'); ?></button>
            <button type="button" id="cgst-article-reset" class="button"><?php esc_html_e('Reset Form', 'cloud-gaming-speed-test'); ?></button>
        </div>
    </form>

    <h2><?php esc_html_e('Configured Articles', 'cloud-gaming-speed-test'); ?></h2>

    <table class="cgst-admin-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Title', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Category', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Description', 'cloud-gaming-speed-test'); ?></th>
                <th><?php esc_html_e('Actions', 'cloud-gaming-speed-test'); ?></th>
            </tr>
        </thead>
        <tbody id="cgst-article-list">
            <?php if (!empty($articles)) : ?>
                <?php foreach ($articles as $article) : ?>
                    <tr data-article='<?php echo wp_json_encode($article); ?>'>
                        <td>
                            <strong><?php echo esc_html($article['title']); ?></strong><br />
                            <a href="<?php echo esc_url($article['url']); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo esc_html($article['url']); ?>
                            </a>
                        </td>
                        <td><span class="cgst-pill"><?php echo esc_html($article['category']); ?></span></td>
                        <td><?php echo esc_html($article['description']); ?></td>
                        <td>
                            <div class="cgst-admin-actions">
                                <button type="button" class="button cgst-edit-article"><?php esc_html_e('Edit', 'cloud-gaming-speed-test'); ?></button>
                                <button type="button" class="button button-danger cgst-delete-article"><?php esc_html_e('Delete', 'cloud-gaming-speed-test'); ?></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4"><?php esc_html_e('No articles configured yet.', 'cloud-gaming-speed-test'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
