<?php

if (!defined('ABSPATH')) {
    exit;
}

class AAPD_Layout_Renderer {

    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function render_single_product($product, $layout = 'card', $options = array()) {
        $settings = AAPD_Settings::instance()->get_settings();
        $config = $this->prepare_render_config($settings, $options);
        $wrapper_classes = $this->get_wrapper_classes($layout, $settings);

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>">
            <?php echo $this->render_product_block($product, $layout, $settings, $config); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php echo $this->render_disclaimer(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_products($products, $layout = 'grid', $columns = 3, $options = array()) {
        $settings = AAPD_Settings::instance()->get_settings();
        $config = $this->prepare_render_config($settings, $options);
        $wrapper_classes = $this->get_wrapper_classes($layout, $settings);
        $columns = max(1, min(6, $columns));

        ob_start();
        ?>
        <div class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>" style="--aapd-columns: <?php echo esc_attr($columns); ?>;">
            <?php
            switch ($layout) {
                case 'list':
                    echo $this->render_list_layout($products, $settings, $config); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    break;
                case 'carousel':
                    echo $this->render_carousel_layout($products, $settings, $config); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    break;
                case 'badge':
                    echo $this->render_badge_layout($products, $settings, $config); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    break;
                case 'grid':
                default:
                    echo $this->render_grid_layout($products, $settings, $config); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    break;
            }
            ?>
            <?php echo $this->render_disclaimer(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function prepare_render_config($settings, $options) {
        return array(
            'display_price' => isset($options['display_price']) ? $options['display_price'] : (bool) $settings['show_price'],
            'show_prime_badge' => isset($options['show_prime_badge']) ? $options['show_prime_badge'] : (bool) $settings['show_prime_badge'],
            'show_review_count' => isset($options['show_review_count']) ? $options['show_review_count'] : (bool) $settings['show_review_count'],
            'show_discount_badge' => isset($options['show_discount_badge']) ? $options['show_discount_badge'] : (bool) $settings['show_discount_badge'],
        );
    }

    private function get_wrapper_classes($layout, $settings) {
        $classes = array('aapd-wrapper', 'aapd-layout-' . sanitize_html_class($layout), 'aapd-theme-' . sanitize_html_class($settings['theme_preset']));
        if (!empty($settings['dark_mode'])) {
            $classes[] = 'aapd-dark';
        }
        if (!empty($settings['enable_animations'])) {
            $classes[] = 'aapd-animate';
        }
        return $classes;
    }

    private function render_product_block($product, $layout, $settings, $config) {
        switch ($layout) {
            case 'badge':
                return $this->render_badge_item($product, $settings, $config);
            case 'list':
                return $this->render_list_item($product, $settings, $config);
            case 'grid':
            case 'card':
            default:
                return $this->render_card_item($product, $settings, $config, true);
        }
    }

    private function render_grid_layout($products, $settings, $config) {
        $output = '<div class="aapd-products-grid">';
        foreach ($products as $product) {
            $output .= '<div class="aapd-grid-item">' . $this->render_card_item($product, $settings, $config, false) . '</div>';
        }
        $output .= '</div>';
        return $output;
    }

    private function render_list_layout($products, $settings, $config) {
        $output = '<div class="aapd-products-list">';
        foreach ($products as $product) {
            $output .= $this->render_list_item($product, $settings, $config);
        }
        $output .= '</div>';
        return $output;
    }

    private function render_carousel_layout($products, $settings, $config) {
        $output = '<div class="aapd-carousel" data-slides="' . esc_attr(count($products)) . '">';
        $output .= '<div class="aapd-carousel-track">';
        foreach ($products as $product) {
            $output .= '<div class="aapd-carousel-slide">' . $this->render_card_item($product, $settings, $config, false) . '</div>';
        }
        $output .= '</div>';
        $output .= '<div class="aapd-carousel-nav">'
            . '<button type="button" class="aapd-carousel-prev" aria-label="' . esc_attr__('Previous', 'amazon-affiliate-displays') . '">&larr;</button>'
            . '<button type="button" class="aapd-carousel-next" aria-label="' . esc_attr__('Next', 'amazon-affiliate-displays') . '">&rarr;</button>'
            . '</div>';
        $output .= '<div class="aapd-carousel-dots"></div>';
        $output .= '</div>';
        return $output;
    }

    private function render_badge_layout($products, $settings, $config) {
        $output = '<div class="aapd-products-badges">';
        foreach ($products as $product) {
            $output .= $this->render_badge_item($product, $settings, $config);
        }
        $output .= '</div>';
        return $output;
    }

    private function render_card_item($product, $settings, $config, $solo = false) {
        $classes = $solo ? 'aapd-product-card aapd-product-card--solo' : 'aapd-product-card';
        $title = isset($product['title']) ? $product['title'] : '';
        $affiliate_url = isset($product['affiliate_url']) ? $product['affiliate_url'] : '#';
        $price = isset($product['price']) ? $product['price'] : '';
        $list_price = isset($product['list_price']) ? $product['list_price'] : '';
        $discount = isset($product['discount']) ? intval($product['discount']) : 0;
        $image_url = isset($product['image_url']) ? $product['image_url'] : '';
        $rating = isset($product['rating']) ? floatval($product['rating']) : 0;
        $review_count = isset($product['review_count']) ? intval($product['review_count']) : 0;
        $features = isset($product['features']) ? (array) $product['features'] : array();
        $prime = !empty($config['show_prime_badge']) && !empty($product['prime_eligible']);
        $show_reviews = !empty($config['show_review_count']) && ($rating > 0 || $review_count > 0);
        $display_price = !empty($config['display_price']);
        $show_discount = !empty($config['show_discount_badge']) && $display_price && $discount > 0;
        $description = isset($product['description']) ? wp_strip_all_tags($product['description']) : '';
        $summary = $description ? wp_trim_words($description, 28, '…') : '';

        if ('' === $summary && !empty($features)) {
            $summary = wp_trim_words(wp_strip_all_tags($features[0]), 24, '…');
            $features = array_slice($features, 1);
        }

        $feature_highlights = array_slice($features, 0, 3);
        $rating_markup = $this->render_rating($rating, $review_count, !empty($config['show_review_count']));

        $output = '<article class="' . esc_attr($classes) . '">';
        $output .= '<div class="aapd-product-media">';
        if (!empty($image_url)) {
            $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($title) . '" loading="lazy" />';
        } else {
            $output .= '<div class="aapd-image-fallback"></div>';
        }
        if ($prime) {
            $output .= $this->render_prime_badge();
        }
        if ($show_discount) {
            $output .= '<span class="aapd-badge-discount">-' . esc_html($discount) . '%</span>';
        }
        $output .= '</div>';

        $output .= '<div class="aapd-product-content">';
        $output .= '<div class="aapd-product-header">';
        $output .= '<h3 class="aapd-product-title">' . esc_html($title) . '</h3>';
        if (!empty($rating_markup)) {
            $output .= $rating_markup;
        }
        $output .= '</div>';

        if (!empty($summary)) {
            $output .= '<p class="aapd-product-summary">' . esc_html($summary) . '</p>';
        }

        if (!empty($feature_highlights)) {
            $output .= '<ul class="aapd-product-features">';
            foreach ($feature_highlights as $feature) {
                $output .= '<li>' . esc_html($feature) . '</li>';
            }
            $output .= '</ul>';
        }

        if ($display_price && (!empty($price) || !empty($list_price))) {
            $output .= '<div class="aapd-product-pricing">';
            if (!empty($price)) {
                $output .= '<span class="aapd-price">' . esc_html($price) . '</span>';
            }
            if (!empty($list_price)) {
                $output .= '<span class="aapd-price-compare">' . esc_html($list_price) . '</span>';
            }
            $output .= '</div>';
        }

        $output .= '<div class="aapd-product-actions">';
        $output .= '<a href="' . esc_url($affiliate_url) . '" class="aapd-button" target="_blank" rel="nofollow noopener">' . esc_html__('Buy on Amazon', 'amazon-affiliate-displays') . '</a>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</article>';

        return $output;
    }

    private function render_list_item($product, $settings, $config) {
        $title = isset($product['title']) ? $product['title'] : '';
        $affiliate_url = isset($product['affiliate_url']) ? $product['affiliate_url'] : '#';
        $price = isset($product['price']) ? $product['price'] : '';
        $image_url = isset($product['image_url']) ? $product['image_url'] : '';
        $rating = isset($product['rating']) ? floatval($product['rating']) : 0;
        $review_count = isset($product['review_count']) ? intval($product['review_count']) : 0;
        $prime = !empty($config['show_prime_badge']) && !empty($product['prime_eligible']);
        $display_price = !empty($config['display_price']);
        $rating_markup = $this->render_rating($rating, $review_count, !empty($config['show_review_count']));

        $output = '<article class="aapd-product-list-item">';
        if (!empty($image_url)) {
            $output .= '<div class="aapd-list-thumb"><img src="' . esc_url($image_url) . '" alt="' . esc_attr($title) . '" loading="lazy" /></div>';
        }
        $output .= '<div class="aapd-list-content">';
        $output .= '<h3 class="aapd-product-title">' . esc_html($title) . '</h3>';
        if ($prime) {
            $output .= $this->render_prime_badge();
        }
        if ($display_price && !empty($price)) {
            $output .= '<span class="aapd-price">' . esc_html($price) . '</span>';
        }
        $output .= '<div class="aapd-list-actions">';
        $output .= '<a href="' . esc_url($affiliate_url) . '" class="aapd-button" target="_blank" rel="nofollow noopener">' . esc_html__('View on Amazon', 'amazon-affiliate-displays') . '</a>';
        if (!empty($rating_markup)) {
            $output .= $rating_markup;
        }
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</article>';

        return $output;
    }

    private function render_badge_item($product, $settings, $config) {
        $title = isset($product['title']) ? $product['title'] : '';
        $affiliate_url = isset($product['affiliate_url']) ? $product['affiliate_url'] : '#';
        $price = isset($product['price']) ? $product['price'] : '';
        $image_url = isset($product['image_url']) ? $product['image_url'] : '';
        $rating = isset($product['rating']) ? floatval($product['rating']) : 0;
        $review_count = isset($product['review_count']) ? intval($product['review_count']) : 0;
        $prime = !empty($config['show_prime_badge']) && !empty($product['prime_eligible']);
        $display_price = !empty($config['display_price']);
        $rating_markup = $this->render_rating($rating, $review_count, !empty($config['show_review_count']));

        $output = '<article class="aapd-badge-item">';
        if (!empty($image_url)) {
            $output .= '<div class="aapd-badge-thumb"><img src="' . esc_url($image_url) . '" alt="' . esc_attr($title) . '" loading="lazy" /></div>';
        }
        $output .= '<div class="aapd-badge-content">';
        if ($prime) {
            $output .= $this->render_prime_badge();
        }
        $output .= '<h3 class="aapd-product-title">' . esc_html($title) . '</h3>';
        if ($display_price && !empty($price)) {
            $output .= '<span class="aapd-price">' . esc_html($price) . '</span>';
        }
        $output .= '<a href="' . esc_url($affiliate_url) . '" class="aapd-button" target="_blank" rel="nofollow noopener">' . esc_html__('Shop Now', 'amazon-affiliate-displays') . '</a>';
        $output .= '</div>';
        $output .= '</article>';

        return $output;
    }

    private function render_rating($rating, $review_count, $show_count = true) {
        if ($rating <= 0) {
            return '';
        }

        $rating = max(0, min(5, $rating));
        $percentage = ($rating / 5) * 100;
        $output = '<div class="aapd-rating" aria-label="' . esc_attr(sprintf(__('Rated %s out of 5', 'amazon-affiliate-displays'), number_format_i18n($rating, 1))) . '">';
        $output .= '<span class="aapd-stars"><span style="width:' . esc_attr($percentage) . '%"></span></span>';
        if ($show_count && $review_count > 0) {
            $output .= '<span class="aapd-review-count">' . esc_html(number_format_i18n($review_count)) . ' ' . esc_html__('reviews', 'amazon-affiliate-displays') . '</span>';
        }
        $output .= '</div>';
        return $output;
    }

    private function render_prime_badge() {
        $badge = '<span class="aapd-prime-badge"><svg aria-hidden="true" focusable="false" viewBox="0 0 78 24"><path opacity="0.18" d="M2 12c0-5.523 4.477-10 10-10h54c5.523 0 10 4.477 10 10s-4.477 10-10 10H12C6.477 22 2 17.523 2 12z"></path><path d="M17 7l-4 10h2l4-10h-2zm8 0l-4 10h2l4-10h-2zm8 0l-4 10h2l4-10h-2z"></path></svg><span>' . esc_html__('Prime', 'amazon-affiliate-displays') . '</span></span>';
        return $badge;
    }

    private function render_disclaimer() {
        return '<p class="aapd-disclaimer">' . esc_html__('As an Amazon Associate I earn from qualifying purchases.', 'amazon-affiliate-displays') . '</p>';
    }
}
