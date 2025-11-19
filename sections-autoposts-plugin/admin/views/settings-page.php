<?php
/** @var array $sections_config */
/** @var array $settings */

if (!defined('ABSPATH')) {
    exit;
}

$option_key = SAP_Settings::OPTION_KEY;
$all_categories = get_categories(array('hide_empty' => false));
$all_tags = get_tags(array('hide_empty' => false));
$data_source_options = array(
    'latest'   => __('Latest Published', 'sections-autoposts'),
    'updated'  => __('Latest Updated', 'sections-autoposts'),
    'popular'  => __('Most Popular (by comments)', 'sections-autoposts'),
    'category' => __('Filter by Category', 'sections-autoposts'),
    'tag'      => __('Filter by Tag', 'sections-autoposts'),
    'custom'   => __('Custom Post IDs', 'sections-autoposts'),
);
$order_by_options = array(
    'date' => __('Publish Date', 'sections-autoposts'),
    'modified' => __('Last Updated', 'sections-autoposts'),
    'comment_count' => __('Comment Count', 'sections-autoposts'),
    'title' => __('Title (Alphabetical)', 'sections-autoposts'),
    'rand' => __('Random', 'sections-autoposts'),
);
?>
<div class="wrap sap-admin-wrap">
    <h1 class="sap-page-title"><?php esc_html_e('Sections AutoPosts', 'sections-autoposts'); ?></h1>
    <p class="sap-page-lead"><?php esc_html_e('Configure each layout section, choose the post source, and control spacing, colors, and theme variants.', 'sections-autoposts'); ?></p>

    <form action="options.php" method="post" class="sap-settings-form">
        <?php
        settings_fields('sap_settings_group');
        ?>

        <div class="sap-sections-grid">
            <?php foreach ($sections_config as $section_key => $config) :
                $section_settings = isset($settings[$section_key]) ? $settings[$section_key] : $config['defaults'];
                $section_id = esc_attr('sap-section-' . $section_key);
                $counts = isset($section_settings['counts']) && is_array($section_settings['counts']) ? $section_settings['counts'] : array();
                $excerpt_lengths = isset($section_settings['excerpt_lengths']) && is_array($section_settings['excerpt_lengths']) ? $section_settings['excerpt_lengths'] : array();
                ?>
                <details class="sap-section-card" data-section="<?php echo esc_attr($section_key); ?>" open>
                    <summary class="sap-section-summary">
                        <span class="sap-section-status">
                            <input type="checkbox"
                                name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][enabled]"
                                value="1"
                                <?php checked(!empty($section_settings['enabled']), true); ?>
                                class="sap-section-toggle">
                        </span>
                        <span class="sap-section-title">
                            <?php echo esc_html($config['label']); ?>
                            <small><?php echo esc_html($config['description']); ?></small>
                        </span>
                        <span class="sap-section-shortcode" title="<?php esc_attr_e('Copy shortcode', 'sections-autoposts'); ?>">
                            [sap_section type="<?php echo esc_attr($section_key); ?>"]
                        </span>
                    </summary>

                    <div class="sap-section-content">
                        <div class="sap-field-row">
                            <div class="sap-field">
                                <label for="<?php echo esc_attr($section_id . '-order'); ?>"><?php esc_html_e('Display Order', 'sections-autoposts'); ?></label>
                                <input type="number" id="<?php echo esc_attr($section_id . '-order'); ?>"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][order]"
                                    value="<?php echo esc_attr(isset($section_settings['order']) ? $section_settings['order'] : 50); ?>"
                                    min="0" class="small-text">
                                <p class="description"><?php esc_html_e('Lower numbers appear first when rendering all sections.', 'sections-autoposts'); ?></p>
                            </div>
                            <div class="sap-field">
                                <label for="<?php echo esc_attr($section_id . '-title'); ?>"><?php esc_html_e('Section Title', 'sections-autoposts'); ?></label>
                                <input type="text"
                                    id="<?php echo esc_attr($section_id . '-title'); ?>"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][title]"
                                    value="<?php echo esc_attr($section_settings['title']); ?>"
                                    class="regular-text">
                            </div>
                            <div class="sap-field">
                                <label for="<?php echo esc_attr($section_id . '-subtitle'); ?>"><?php esc_html_e('Subtitle / Description', 'sections-autoposts'); ?></label>
                                <input type="text"
                                    id="<?php echo esc_attr($section_id . '-subtitle'); ?>"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][subtitle]"
                                    value="<?php echo esc_attr(isset($section_settings['subtitle']) ? $section_settings['subtitle'] : ''); ?>"
                                    class="regular-text">
                            </div>
                        </div>

                        <?php if (array_key_exists('secondary_title', $section_settings)) : ?>
                            <div class="sap-field-row">
                                <div class="sap-field">
                                    <label for="<?php echo esc_attr($section_id . '-secondary-title'); ?>"><?php esc_html_e('Secondary Title', 'sections-autoposts'); ?></label>
                                    <input type="text"
                                        id="<?php echo esc_attr($section_id . '-secondary-title'); ?>"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][secondary_title]"
                                        value="<?php echo esc_attr($section_settings['secondary_title']); ?>"
                                        class="regular-text">
                                    <p class="description"><?php esc_html_e('Used for widget headings or supporting column titles when available.', 'sections-autoposts'); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="sap-field-row">
                            <div class="sap-field sap-field--inline">
                                <label><?php esc_html_e('Theme', 'sections-autoposts'); ?></label>
                                <label>
                                    <input type="radio"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][theme]"
                                        value="light" <?php checked($section_settings['theme'], 'light'); ?>>
                                    <?php esc_html_e('Light', 'sections-autoposts'); ?>
                                </label>
                                <label>
                                    <input type="radio"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][theme]"
                                        value="dark" <?php checked($section_settings['theme'], 'dark'); ?>>
                                    <?php esc_html_e('Dark', 'sections-autoposts'); ?>
                                </label>
                            </div>
                            <div class="sap-field">
                                <label><?php esc_html_e('Accent Color', 'sections-autoposts'); ?></label>
                                <input type="text" class="sap-color-field"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][accent_color]"
                                    value="<?php echo esc_attr($section_settings['accent_color']); ?>">
                            </div>
                            <?php if (array_key_exists('secondary_accent_color', $section_settings)) : ?>
                                <div class="sap-field">
                                    <label><?php esc_html_e('Secondary Accent', 'sections-autoposts'); ?></label>
                                    <input type="text" class="sap-color-field"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][secondary_accent_color]"
                                        value="<?php echo esc_attr($section_settings['secondary_accent_color']); ?>">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="sap-field-row">
                            <div class="sap-field">
                                <label><?php esc_html_e('Background Color', 'sections-autoposts'); ?></label>
                                <input type="text" class="sap-color-field"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][background_color]"
                                    value="<?php echo esc_attr($section_settings['background_color']); ?>">
                            </div>
                            <div class="sap-field">
                                <label><?php esc_html_e('Container Max Width', 'sections-autoposts'); ?></label>
                                <input type="text"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][container_width]"
                                    value="<?php echo esc_attr($section_settings['container_width']); ?>"
                                    class="regular-text">
                                <p class="description"><?php esc_html_e('Examples: 1400px, 100%, 90vw', 'sections-autoposts'); ?></p>
                            </div>
                            <div class="sap-field">
                                <label><?php esc_html_e('Custom CSS Classes', 'sections-autoposts'); ?></label>
                                <input type="text"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][custom_classes]"
                                    value="<?php echo esc_attr($section_settings['custom_classes']); ?>"
                                    class="regular-text">
                            </div>
                        </div>
                        <div class="sap-field-row">
                            <div class="sap-field">
                                <label><?php esc_html_e('Padding', 'sections-autoposts'); ?></label>
                                <input type="text"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][padding]"
                                    value="<?php echo esc_attr($section_settings['padding']); ?>"
                                    class="regular-text" placeholder="40px 20px">
                            </div>
                            <div class="sap-field">
                                <label><?php esc_html_e('Margin', 'sections-autoposts'); ?></label>
                                <input type="text"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][margin]"
                                    value="<?php echo esc_attr($section_settings['margin']); ?>"
                                    class="regular-text" placeholder="0 auto 40px auto">
                            </div>
                        </div>

                        <hr class="sap-divider">

                        <div class="sap-field-row">
                            <div class="sap-field">
                                <label><?php esc_html_e('Post Source', 'sections-autoposts'); ?></label>
                                <select name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][data_source]"
                                    class="sap-data-source">
                                    <?php foreach ($data_source_options as $value => $label) : ?>
                                        <option value="<?php echo esc_attr($value); ?>" <?php selected($section_settings['data_source'], $value); ?>>
                                            <?php echo esc_html($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="sap-field">
                                <label><?php esc_html_e('Order By', 'sections-autoposts'); ?></label>
                                <select name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][order_by]">
                                    <?php foreach ($order_by_options as $value => $label) : ?>
                                        <option value="<?php echo esc_attr($value); ?>" <?php selected($section_settings['order_by'], $value); ?>>
                                            <?php echo esc_html($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="sap-field">
                                <label><?php esc_html_e('Order Direction', 'sections-autoposts'); ?></label>
                                <select name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][order_direction]">
                                    <option value="DESC" <?php selected($section_settings['order_direction'], 'DESC'); ?>><?php esc_html_e('Descending', 'sections-autoposts'); ?></option>
                                    <option value="ASC" <?php selected($section_settings['order_direction'], 'ASC'); ?>><?php esc_html_e('Ascending', 'sections-autoposts'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="sap-field-row sap-conditional-wrapper" data-condition="category">
                            <div class="sap-field sap-field--full">
                                <label><?php esc_html_e('Categories', 'sections-autoposts'); ?></label>
                                <select multiple class="sap-multiselect"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][categories][]">
                                    <?php foreach ($all_categories as $category) : ?>
                                        <option value="<?php echo esc_attr($category->term_id); ?>"<?php echo in_array($category->term_id, (array) $section_settings['categories'], true) ? ' selected' : ''; ?>>
                                            <?php echo esc_html($category->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php esc_html_e('Hold CTRL/CMD to choose multiple categories.', 'sections-autoposts'); ?></p>
                            </div>
                        </div>

                        <div class="sap-field-row sap-conditional-wrapper" data-condition="tag">
                            <div class="sap-field sap-field--full">
                                <label><?php esc_html_e('Tags', 'sections-autoposts'); ?></label>
                                <select multiple class="sap-multiselect"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][tags][]">
                                    <?php foreach ($all_tags as $tag) : ?>
                                        <option value="<?php echo esc_attr($tag->term_id); ?>"<?php echo in_array($tag->term_id, (array) $section_settings['tags'], true) ? ' selected' : ''; ?>>
                                            <?php echo esc_html($tag->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="sap-field-row sap-conditional-wrapper" data-condition="custom">
                            <div class="sap-field sap-field--full">
                                <label><?php esc_html_e('Custom Post IDs', 'sections-autoposts'); ?></label>
                                <input type="text"
                                    name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][custom_post_ids]"
                                    value="<?php echo esc_attr($section_settings['custom_post_ids']); ?>"
                                    class="regular-text" placeholder="12,45,87">
                                <p class="description"><?php esc_html_e('Enter comma separated post IDs in the order you want them displayed.', 'sections-autoposts'); ?></p>
                            </div>
                        </div>

                        <?php if (!empty($counts)) : ?>
                            <hr class="sap-divider">
                            <div class="sap-field-row">
                                <?php foreach ($counts as $count_key => $count_value) :
                                    $count_label = ucwords(str_replace(array('_', '-'), ' ', $count_key));
                                    ?>
                                    <div class="sap-field">
                                        <label><?php echo esc_html(sprintf(__('Items (%s)', 'sections-autoposts'), $count_label)); ?></label>
                                        <input type="number"
                                            name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][counts][<?php echo esc_attr($count_key); ?>]"
                                            value="<?php echo esc_attr($count_value); ?>"
                                            min="0" class="small-text">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($excerpt_lengths)) : ?>
                            <div class="sap-field-row">
                                <?php foreach ($excerpt_lengths as $excerpt_key => $excerpt_value) :
                                    $excerpt_label = ucwords(str_replace(array('_', '-'), ' ', $excerpt_key));
                                    ?>
                                    <div class="sap-field">
                                        <label><?php echo esc_html(sprintf(__('Excerpt Length (%s)', 'sections-autoposts'), $excerpt_label)); ?></label>
                                        <input type="number"
                                            name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][excerpt_lengths][<?php echo esc_attr($excerpt_key); ?>]"
                                            value="<?php echo esc_attr($excerpt_value); ?>"
                                            min="10" class="small-text">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (isset($section_settings['excerpt_length'])) : ?>
                            <div class="sap-field-row">
                                <div class="sap-field">
                                    <label><?php esc_html_e('Excerpt Length (words)', 'sections-autoposts'); ?></label>
                                    <input type="number"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][excerpt_length]"
                                        value="<?php echo esc_attr($section_settings['excerpt_length']); ?>"
                                        min="10" class="small-text">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="sap-field-row">
                            <?php if (isset($section_settings['items_per_page'])) : ?>
                                <div class="sap-field">
                                    <label><?php esc_html_e('Items Per Page (Frontend Pagination)', 'sections-autoposts'); ?></label>
                                    <input type="number"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][items_per_page]"
                                        value="<?php echo esc_attr($section_settings['items_per_page']); ?>"
                                        min="1" class="small-text">
                                </div>
                            <?php endif; ?>
                            <?php if (isset($section_settings['grid_columns_desktop'])) : ?>
                                <div class="sap-field">
                                    <label><?php esc_html_e('Grid Columns (Desktop)', 'sections-autoposts'); ?></label>
                                    <input type="number"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][grid_columns_desktop]"
                                        value="<?php echo esc_attr($section_settings['grid_columns_desktop']); ?>"
                                        min="1" class="small-text">
                                </div>
                                <div class="sap-field">
                                    <label><?php esc_html_e('Grid Columns (Tablet)', 'sections-autoposts'); ?></label>
                                    <input type="number"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][grid_columns_tablet]"
                                        value="<?php echo esc_attr($section_settings['grid_columns_tablet']); ?>"
                                        min="1" class="small-text">
                                </div>
                                <div class="sap-field">
                                    <label><?php esc_html_e('Grid Columns (Mobile)', 'sections-autoposts'); ?></label>
                                    <input type="number"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][grid_columns_mobile]"
                                        value="<?php echo esc_attr($section_settings['grid_columns_mobile']); ?>"
                                        min="1" class="small-text">
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($section_settings['widget_badges'])) : ?>
                            <div class="sap-field-row">
                                <div class="sap-field">
                                    <label><?php esc_html_e('Widget Badges', 'sections-autoposts'); ?></label>
                                    <select name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][widget_badges]">
                                        <option value="auto" <?php selected($section_settings['widget_badges'], 'auto'); ?>><?php esc_html_e('Automatic', 'sections-autoposts'); ?></option>
                                        <option value="off" <?php selected($section_settings['widget_badges'], 'off'); ?>><?php esc_html_e('Disabled', 'sections-autoposts'); ?></option>
                                    </select>
                                </div>
                                <div class="sap-field">
                                    <label><?php esc_html_e('New Badge (days)', 'sections-autoposts'); ?></label>
                                    <input type="number"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][badge_new_days]"
                                        value="<?php echo esc_attr($section_settings['badge_new_days']); ?>"
                                        min="0" class="small-text">
                                </div>
                                <div class="sap-field">
                                    <label><?php esc_html_e('Updated Badge (days)', 'sections-autoposts'); ?></label>
                                    <input type="number"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][badge_updated_days]"
                                        value="<?php echo esc_attr($section_settings['badge_updated_days']); ?>"
                                        min="0" class="small-text">
                                </div>
                                <div class="sap-field">
                                    <label><?php esc_html_e('Hot Badge (comments)', 'sections-autoposts'); ?></label>
                                    <input type="number"
                                        name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][badge_hot_comments]"
                                        value="<?php echo esc_attr($section_settings['badge_hot_comments']); ?>"
                                        min="0" class="small-text">
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($section_settings['rating_meta_key']) || isset($section_settings['features_meta_key']) || isset($section_settings['difficulty_meta_key']) || isset($section_settings['icon_list'])) : ?>
                            <hr class="sap-divider">
                            <div class="sap-field-row">
                                <?php if (isset($section_settings['rating_meta_key'])) : ?>
                                    <div class="sap-field">
                                        <label><?php esc_html_e('Rating Meta Key', 'sections-autoposts'); ?></label>
                                        <input type="text"
                                            name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][rating_meta_key]"
                                            value="<?php echo esc_attr($section_settings['rating_meta_key']); ?>"
                                            class="regular-text">
                                        <p class="description"><?php esc_html_e('Meta key storing rating values (e.g., 9.2/10).', 'sections-autoposts'); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($section_settings['features_meta_key'])) : ?>
                                    <div class="sap-field">
                                        <label><?php esc_html_e('Features Meta Key', 'sections-autoposts'); ?></label>
                                        <input type="text"
                                            name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][features_meta_key]"
                                            value="<?php echo esc_attr($section_settings['features_meta_key']); ?>"
                                            class="regular-text">
                                        <p class="description"><?php esc_html_e('Meta key with comma or newline separated feature list.', 'sections-autoposts'); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($section_settings['difficulty_meta_key'])) : ?>
                                    <div class="sap-field">
                                        <label><?php esc_html_e('Difficulty Meta Key', 'sections-autoposts'); ?></label>
                                        <input type="text"
                                            name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][difficulty_meta_key]"
                                            value="<?php echo esc_attr($section_settings['difficulty_meta_key']); ?>"
                                            class="regular-text">
                                        <p class="description"><?php esc_html_e('Meta key that stores difficulty level labels.', 'sections-autoposts'); ?></p>
                                    </div>
                                    <div class="sap-field">
                                        <label><?php esc_html_e('Default Difficulty', 'sections-autoposts'); ?></label>
                                        <input type="text"
                                            name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][default_difficulty]"
                                            value="<?php echo esc_attr($section_settings['default_difficulty']); ?>"
                                            class="regular-text">
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($section_settings['icon_list'])) : ?>
                                    <div class="sap-field sap-field--full">
                                        <label><?php esc_html_e('Icon List (comma separated)', 'sections-autoposts'); ?></label>
                                        <input type="text"
                                            name="<?php echo esc_attr($option_key); ?>[<?php echo esc_attr($section_key); ?>][icon_list]"
                                            value="<?php echo esc_attr(implode(',', (array) $section_settings['icon_list'])); ?>"
                                            class="regular-text">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>

        <?php submit_button(__('Save All Sections', 'sections-autoposts')); ?>
    </form>

    <div class="sap-shortcode-help">
        <h2><?php esc_html_e('Usage Tips', 'sections-autoposts'); ?></h2>
        <ul>
            <li><?php esc_html_e('Use [sap_all_sections] to output every enabled layout on a page.', 'sections-autoposts'); ?></li>
            <li><?php esc_html_e('Each individual layout has its own shortcode: [sap_section type="featured"].', 'sections-autoposts'); ?></li>
            <li><?php esc_html_e('Dark theme variants automatically adjust typography and card colors.', 'sections-autoposts'); ?></li>
        </ul>
    </div>
</div>
