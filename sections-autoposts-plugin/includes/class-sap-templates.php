<?php
/**
 * Template renderer for all 9 sections
 */

if (!defined('ABSPATH')) {
    exit;
}

class SAP_Templates {

    /**
     * Main routing for section rendering
     */
    public static function render($section_key, $settings) {
        if (empty($settings) || empty($settings['enabled'])) {
            return;
        }

        $method = 'render_' . str_replace('-', '_', $section_key);
        if (method_exists(__CLASS__, $method)) {
            call_user_func(array(__CLASS__, $method), $settings);
        }
    }

    /**
     * Helper: Wrap sections with common styles and classes
     */
    private static function wrap_open($section_key, $settings) {
        $context = SAP_Utils::build_wrapper_context($section_key, $settings);
        $class_string = implode(' ', $context['classes']);
        $style_string = SAP_Utils::styles_to_string($context['styles']);

        echo '<div class="' . esc_attr($class_string) . '"';
        if (!empty($style_string)) {
            echo ' style="' . esc_attr($style_string) . '"';
        }
        echo '>';

        $inner_styles = SAP_Utils::styles_to_string($context['inner_styles']);
        echo '<div class="sap-section__inner"';
        if (!empty($inner_styles)) {
            echo ' style="' . esc_attr($inner_styles) . '"';
        }
        echo '>';
    }

    private static function wrap_close() {
        echo '</div></div>';
    }

    /**
     * 1) Featured Posts + Widget
     */
    private static function render_featured($settings) {
        $main_posts = SAP_Post_Query::fetch('featured', $settings, array(
            'count' => isset($settings['counts']['main']) ? $settings['counts']['main'] : 1,
            'excerpt_length' => isset($settings['excerpt_lengths']['main']) ? $settings['excerpt_lengths']['main'] : 36,
        ));
        $secondary_posts = SAP_Post_Query::fetch('featured', $settings, array(
            'count' => isset($settings['counts']['secondary']) ? $settings['counts']['secondary'] : 3,
            'excerpt_length' => isset($settings['excerpt_lengths']['secondary']) ? $settings['excerpt_lengths']['secondary'] : 28,
            'exclude' => wp_list_pluck($main_posts, 'ID'),
        ));
        $widget_posts = SAP_Post_Query::fetch('featured', $settings, array(
            'count' => isset($settings['counts']['widget']) ? $settings['counts']['widget'] : 5,
            'badge_context' => 'widget',
            'exclude' => array_merge(wp_list_pluck($main_posts, 'ID'), wp_list_pluck($secondary_posts, 'ID')),
        ));

        self::wrap_open('featured', $settings);
        ?>
        <div class="gaming-featured-container v27">
            <div class="column-left">
                <?php if (!empty($settings['title'])) : ?>
                    <h2 class="section-title">
                        <span class="title-square blue" style="background-color:<?php echo esc_attr($settings['accent_color']); ?>;"></span>
                        <?php echo esc_html($settings['title']); ?>
                    </h2>
                <?php endif; ?>

                <div class="featured-content">
                    <div class="featured-grid">
                        <?php if (!empty($main_posts)) : ?>
                            <article class="featured-main">
                                <div class="image-wrap">
                                    <img src="<?php echo esc_url($main_posts[0]['image']['url']); ?>" alt="<?php echo esc_attr($main_posts[0]['image']['alt']); ?>">
                                </div>
                                <div class="featured-overlay">
                                    <?php if (!empty($main_posts[0]['category'])) : ?>
                                        <span class="category-tag"><?php echo esc_html($main_posts[0]['category']['name']); ?></span>
                                    <?php endif; ?>
                                    <h2><a href="<?php echo esc_url($main_posts[0]['permalink']); ?>"><?php echo esc_html($main_posts[0]['title']); ?></a></h2>
                                    <p class="lead"><?php echo esc_html($main_posts[0]['excerpt']); ?></p>
                                </div>
                            </article>
                        <?php endif; ?>

                        <?php if (!empty($secondary_posts)) : ?>
                            <div class="side-list">
                                <?php foreach ($secondary_posts as $post) : ?>
                                    <article class="side-item">
                                        <div class="top-row">
                                            <img src="<?php echo esc_url($post['image']['url']); ?>" alt="<?php echo esc_attr($post['image']['alt']); ?>">
                                            <div class="side-text">
                                                <?php if (!empty($post['category'])) : ?>
                                                    <span class="category-tag"><?php echo esc_html($post['category']['name']); ?></span>
                                                <?php endif; ?>
                                                <h4><a href="<?php echo esc_url($post['permalink']); ?>"><?php echo esc_html($post['title']); ?></a></h4>
                                            </div>
                                        </div>
                                        <p class="excerpt"><?php echo esc_html($post['excerpt']); ?></p>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="column-right">
                <?php if (!empty($settings['secondary_title'])) : ?>
                    <h2 class="section-title">
                        <span class="title-square green" style="background-color:<?php echo esc_attr($settings['secondary_accent_color']); ?>;"></span>
                        <?php echo esc_html($settings['secondary_title']); ?>
                    </h2>
                <?php endif; ?>

                <div class="widget-outer">
                    <div class="gaming-widget">
                        <ul class="widget-list">
                            <?php foreach ($widget_posts as $post) : ?>
                                <li>
                                    <?php if (!empty($post['badge'])) : ?>
                                        <div class="badge-wrap">
                                            <span class="featured-badge <?php echo esc_attr($post['badge']['type']); ?>">
                                                <?php echo strtoupper(esc_html($post['badge']['label'])); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url($post['permalink']); ?>"><?php echo esc_html($post['title']); ?></a>
                                    <div class="meta"><span class="date"><?php echo esc_html($post['date']['display']); ?></span> · <span class="read-time"><?php echo esc_html($post['reading_time']); ?></span></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
        self::wrap_close();
    }

    /**
     * 2) Beginner's Corner
     */
    private static function render_beginners($settings) {
        $posts = SAP_Post_Query::fetch('beginners', $settings, array(
            'count' => isset($settings['counts']['items']) ? $settings['counts']['items'] : 4,
        ));

        self::wrap_open('beginners', $settings);
        ?>
        <div class="cloud-gaming-beginners">
            <div class="beginners-section section-v5">
                <?php if (!empty($settings['title'])) : ?>
                    <h2 class="section-title">
                        <span class="title-square green" style="background-color:<?php echo esc_attr($settings['accent_color']); ?>;"></span>
                        <?php echo esc_html($settings['title']); ?>
                    </h2>
                <?php endif; ?>

                <div class="beginners-grid">
                    <?php foreach ($posts as $index => $post) :
                        $step_num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                        ?>
                        <article class="beginner-card">
                            <div class="step-number"><?php echo esc_html($step_num); ?></div>
                            <div class="beginner-content">
                                <?php if (!empty($post['category'])) : ?>
                                    <span class="category-tag"><?php echo esc_html($post['category']['name']); ?></span>
                                <?php endif; ?>
                                <h3><a href="<?php echo esc_url($post['permalink']); ?>"><?php echo esc_html($post['title']); ?></a></h3>
                                <p><?php echo esc_html($post['excerpt']); ?></p>
                                <div class="meta"><?php echo esc_html($post['modified']['display']); ?></div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        self::wrap_close();
    }

    /**
     * 3) Community Top Picks
     */
    private static function render_community($settings) {
        $posts = SAP_Post_Query::fetch('community', $settings, array(
            'count' => isset($settings['counts']['highlight']) ? $settings['counts']['highlight'] : 1,
        ));
        $list_posts = SAP_Post_Query::fetch('community', $settings, array(
            'count' => isset($settings['counts']['list']) ? $settings['counts']['list'] : 4,
            'exclude' => wp_list_pluck($posts, 'ID'),
        ));

        self::wrap_open('community', $settings);
        echo '<div class="sap-community-layout">';
        
        if (!empty($posts)) {
            $highlight = $posts[0];
            ?>
            <div class="sap-community__highlight">
                <?php if (!empty($settings['title'])) : ?>
                    <h2 class="sap-section__title">
                        <span class="sap-title-square" style="background-color:<?php echo esc_attr($settings['accent_color']); ?>;"></span>
                        <?php echo esc_html($settings['title']); ?>
                    </h2>
                <?php endif; ?>
                <article class="sap-highlight-card">
                    <a href="<?php echo esc_url($highlight['permalink']); ?>">
                        <img src="<?php echo esc_url($highlight['image']['url']); ?>" alt="<?php echo esc_attr($highlight['image']['alt']); ?>">
                    </a>
                    <div class="sap-highlight-content">
                        <?php if (!empty($highlight['category'])) : ?>
                            <span class="sap-category-tag"><?php echo esc_html($highlight['category']['name']); ?></span>
                        <?php endif; ?>
                        <h3><a href="<?php echo esc_url($highlight['permalink']); ?>"><?php echo esc_html($highlight['title']); ?></a></h3>
                        <p><?php echo esc_html($highlight['excerpt']); ?></p>
                        <div class="sap-meta"><?php echo esc_html($highlight['date']['display']); ?> · <?php echo esc_html($highlight['reading_time']); ?></div>
                    </div>
                </article>
            </div>
            <?php
        }

        if (!empty($list_posts)) {
            ?>
            <div class="sap-community__list">
                <?php if (!empty($settings['secondary_title'])) : ?>
                    <h2 class="sap-section__title">
                        <span class="sap-title-square" style="background-color:<?php echo esc_attr($settings['secondary_accent_color']); ?>;"></span>
                        <?php echo esc_html($settings['secondary_title']); ?>
                    </h2>
                <?php endif; ?>
                <ul>
                    <?php foreach ($list_posts as $post) : ?>
                        <li>
                            <a href="<?php echo esc_url($post['permalink']); ?>"><?php echo esc_html($post['title']); ?></a>
                            <p><?php echo esc_html($post['excerpt']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        }

        echo '</div>';
        self::wrap_close();
    }

    /**
     * 4) Featured Games (Hero + Grid)
     */
    private static function render_featured_games($settings) {
        $hero_posts = SAP_Post_Query::fetch('featured_games', $settings, array(
            'count' => isset($settings['counts']['hero']) ? $settings['counts']['hero'] : 1,
        ));
        $grid_posts = SAP_Post_Query::fetch('featured_games', $settings, array(
            'count' => isset($settings['counts']['grid']) ? $settings['counts']['grid'] : 4,
            'exclude' => wp_list_pluck($hero_posts, 'ID'),
        ));

        self::wrap_open('featured_games', $settings);
        ?>
        <?php if (!empty($settings['title'])) : ?>
            <h2 class="sap-section__title">
                <span class="sap-title-square" style="background-color:<?php echo esc_attr($settings['accent_color']); ?>;"></span>
                <?php echo esc_html($settings['title']); ?>
            </h2>
        <?php endif; ?>

        <div class="sap-games-layout">
            <?php if (!empty($hero_posts)) : ?>
                <article class="sap-game-hero">
                    <a href="<?php echo esc_url($hero_posts[0]['permalink']); ?>">
                        <img src="<?php echo esc_url($hero_posts[0]['image']['url']); ?>" alt="<?php echo esc_attr($hero_posts[0]['image']['alt']); ?>">
                    </a>
                    <div class="sap-game-hero__content">
                        <span class="sap-platform-tag"><?php echo esc_html($hero_posts[0]['category']['name'] ?? ''); ?></span>
                        <h3><a href="<?php echo esc_url($hero_posts[0]['permalink']); ?>"><?php echo esc_html($hero_posts[0]['title']); ?></a></h3>
                        <p><?php echo esc_html($hero_posts[0]['excerpt']); ?></p>
                    </div>
                </article>
            <?php endif; ?>

            <?php if (!empty($grid_posts)) : ?>
                <div class="sap-games-grid">
                    <?php foreach ($grid_posts as $post) : ?>
                        <article class="sap-game-mini">
                            <a href="<?php echo esc_url($post['permalink']); ?>">
                                <img src="<?php echo esc_url($post['image']['url']); ?>" alt="<?php echo esc_attr($post['image']['alt']); ?>">
                            </a>
                            <div class="sap-game-mini__content">
                                <span class="sap-platform-tag"><?php echo esc_html($post['category']['name'] ?? ''); ?></span>
                                <h4><a href="<?php echo esc_url($post['permalink']); ?>"><?php echo esc_html($post['title']); ?></a></h4>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        self::wrap_close();
    }

    /**
     * 5) Latest News (Timeline)
     */
    private static function render_latest_news($settings) {
        $posts = SAP_Post_Query::fetch('latest_news', $settings, array(
            'count' => isset($settings['counts']['items']) ? $settings['counts']['items'] : 4,
        ));

        self::wrap_open('latest_news', $settings);
        ?>
        <?php if (!empty($settings['title'])) : ?>
            <h2 class="sap-section__title">
                <span class="sap-title-square" style="background-color:<?php echo esc_attr($settings['accent_color']); ?>;"></span>
                <?php echo esc_html($settings['title']); ?>
            </h2>
        <?php endif; ?>

        <div class="sap-timeline">
            <?php foreach ($posts as $post) : ?>
                <article class="sap-timeline-item">
                    <div class="sap-timeline-date">
                        <span class="sap-month"><?php echo esc_html($post['date']['month']); ?></span>
                        <span class="sap-day"><?php echo esc_html($post['date']['day']); ?></span>
                    </div>
                    <div class="sap-timeline-content">
                        <?php if (!empty($post['badge'])) : ?>
                            <span class="sap-badge sap-badge--<?php echo esc_attr($post['badge']['type']); ?>">
                                <?php echo esc_html($post['badge']['label']); ?>
                            </span>
                        <?php endif; ?>
                        <h3><a href="<?php echo esc_url($post['permalink']); ?>"><?php echo esc_html($post['title']); ?></a></h3>
                        <p><?php echo esc_html($post['excerpt']); ?></p>
                        <div class="sap-meta"><?php echo esc_html($post['reading_time']); ?> · <?php echo esc_html($post['category']['name'] ?? ''); ?></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php
        self::wrap_close();
    }

    /**
     * 6) Latest Posts Grid (with JS pagination)
     */
    private static function render_latest_posts($settings) {
        $count = isset($settings['counts']['items']) ? $settings['counts']['items'] : 12;
        $posts = SAP_Post_Query::fetch('latest_posts', $settings, array('count' => $count));
        $per_page = isset($settings['items_per_page']) ? $settings['items_per_page'] : 9;

        self::wrap_open('latest_posts', $settings);
        ?>
        <div class="gaming-latest-container v27">
            <?php if (!empty($settings['title'])) : ?>
                <h2 class="section-title">
                    <span class="title-square blue" style="background-color:<?php echo esc_attr($settings['accent_color']); ?>;"></span>
                    <?php echo esc_html($settings['title']); ?>
                </h2>
            <?php endif; ?>

            <div class="latest-grid sap-latest-grid" data-items-per-page="<?php echo esc_attr($per_page); ?>">
                <?php foreach ($posts as $post) : ?>
                    <article class="latest-card sap-latest-card">
                        <div class="card-image-wrap">
                            <img src="<?php echo esc_url($post['image']['url']); ?>" alt="<?php echo esc_attr($post['image']['alt']); ?>">
                            <?php if (!empty($post['badge'])) : ?>
                                <span class="card-badge <?php echo esc_attr($post['badge']['type']); ?>"><?php echo esc_html($post['badge']['label']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($post['category'])) : ?>
                                <span class="card-category"><?php echo esc_html($post['category']['name']); ?></span>
                            <?php endif; ?>
                            <h3 class="card-title"><a href="<?php echo esc_url($post['permalink']); ?>"><?php echo esc_html($post['title']); ?></a></h3>
                            <p class="card-excerpt"><?php echo esc_html($post['excerpt']); ?></p>
                            <div class="card-meta">
                                <span class="date"><?php echo esc_html($post['date']['display']); ?></span>
                                <span class="separator">·</span>
                                <span class="read-time"><?php echo esc_html($post['reading_time']); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="latest-pagination sap-pagination" id="sap-pagination-<?php echo esc_attr(uniqid()); ?>"></div>
        </div>
        <?php
        self::wrap_close();
    }

    /**
     * 7) Platform Comparison & Reviews
     */
    private static function render_platforms($settings) {
        $posts = SAP_Post_Query::fetch('platforms', $settings, array(
            'count' => isset($settings['counts']['items']) ? $settings['counts']['items'] : 3,
        ));

        self::wrap_open('platforms', $settings);
        ?>
        <?php if (!empty($settings['title'])) : ?>
            <h2 class="sap-section__title">
                <span class="sap-title-square" style="background-color:<?php echo esc_attr($settings['accent_color']); ?>;"></span>
                <?php echo esc_html($settings['title']); ?>
            </h2>
        <?php endif; ?>

        <div class="sap-platforms-grid">
            <?php foreach ($posts as $post) :
                $rating = !empty($settings['rating_meta_key']) ? SAP_Utils::get_rating_meta($post['ID'], $settings['rating_meta_key']) : '';
                $features = !empty($settings['features_meta_key']) ? SAP_Utils::parse_features_meta($post['ID'], $settings['features_meta_key']) : array();
                ?>
                <article class="sap-platform-card">
                    <div class="sap-platform-header">
                        <a href="<?php echo esc_url($post['permalink']); ?>">
                            <img src="<?php echo esc_url($post['image']['url']); ?>" alt="<?php echo esc_attr($post['image']['alt']); ?>">
                        </a>
                        <h3><a href="<?php echo esc_url($post['permalink']); ?>"><?php echo esc_html($post['title']); ?></a></h3>
                        <?php if ($rating) : ?>
                            <span class="sap-rating-badge"><?php echo esc_html($rating); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($features)) : ?>
                        <ul class="sap-features-list">
                            <?php foreach ($features as $feature) : ?>
                                <li><?php echo esc_html($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <a href="<?php echo esc_url($post['permalink']); ?>" class="sap-review-link"><?php esc_html_e('Full Review →', 'sections-autoposts'); ?></a>
                </article>
            <?php endforeach; ?>
        </div>
        <?php
        self::wrap_close();
    }

    /**
     * 8) Pro Guides & Advanced Configs
     */
    private static function render_pro_guides($settings) {
        $posts = SAP_Post_Query::fetch('pro_guides', $settings, array(
            'count' => isset($settings['counts']['items']) ? $settings['counts']['items'] : 3,
        ));

        self::wrap_open('pro_guides', $settings);
        ?>
        <?php if (!empty($settings['title'])) : ?>
            <h2 class="sap-section__title">
                <span class="sap-title-square" style="background-color:<?php echo esc_attr($settings['accent_color']); ?>;"></span>
                <?php echo esc_html($settings['title']); ?>
            </h2>
        <?php endif; ?>

        <div class="sap-guides-grid">
            <?php foreach ($posts as $post) :
                $difficulty = !empty($settings['difficulty_meta_key']) ? SAP_Utils::get_difficulty_badge($post['ID'], $settings['difficulty_meta_key'], $settings['default_difficulty']) : $settings['default_difficulty'];
                ?>
                <article class="sap-guide-card">
                    <a href="<?php echo esc_url($post['permalink']); ?>" class="sap-guide-image">
                        <img src="<?php echo esc_url($post['image']['url']); ?>" alt="<?php echo esc_attr($post['image']['alt']); ?>">
                        <span class="sap-difficulty-badge"><?php echo esc_html($difficulty); ?></span>
                    </a>
                    <div class="sap-guide-content">
                        <?php if (!empty($post['category'])) : ?>
                            <span class="sap-category-tag"><?php echo esc_html($post['category']['name']); ?></span>
                        <?php endif; ?>
                        <h3><a href="<?php echo esc_url($post['permalink']); ?>"><?php echo esc_html($post['title']); ?></a></h3>
                        <p><?php echo esc_html($post['excerpt']); ?></p>
                        <div class="sap-meta"><?php echo esc_html($post['date']['display']); ?> · <?php echo esc_html($post['reading_time']); ?></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php
        self::wrap_close();
    }

    /**
     * 9) Troubleshooting Hub (Icon Grid)
     */
    private static function render_troubleshooting($settings) {
        $posts = SAP_Post_Query::fetch('troubleshooting', $settings, array(
            'count' => isset($settings['counts']['items']) ? $settings['counts']['items'] : 6,
        ));
        $icons = isset($settings['icon_list']) && is_array($settings['icon_list']) ? $settings['icon_list'] : array('⚙️');

        self::wrap_open('troubleshooting', $settings);
        ?>
        <?php if (!empty($settings['title'])) : ?>
            <h2 class="sap-section__title">
                <span class="sap-title-square" style="background-color:<?php echo esc_attr($settings['accent_color']); ?>;"></span>
                <?php echo esc_html($settings['title']); ?>
            </h2>
        <?php endif; ?>

        <div class="sap-troubleshooting-grid">
            <?php foreach ($posts as $index => $post) :
                $icon = SAP_Utils::get_icon_for_index($index, $icons);
                ?>
                <article class="sap-trouble-card">
                    <div class="sap-trouble-icon"><?php echo esc_html($icon); ?></div>
                    <h3><a href="<?php echo esc_url($post['permalink']); ?>"><?php echo esc_html($post['title']); ?></a></h3>
                    <p><?php echo esc_html($post['excerpt']); ?></p>
                    <span class="sap-solutions-count"><?php printf(esc_html__('%d Solutions', 'sections-autoposts'), max(1, $post['comment_count'])); ?></span>
                </article>
            <?php endforeach; ?>
        </div>
        <?php
        self::wrap_close();
    }
}
