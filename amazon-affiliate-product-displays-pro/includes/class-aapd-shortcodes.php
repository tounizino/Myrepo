<?php

if (!defined('ABSPATH')) {
    exit;
}

class AAPD_Shortcodes {

    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('amazon_card', array($this, 'amazon_card_shortcode'));
        add_shortcode('amazon_products', array($this, 'amazon_products_shortcode'));
    }

    public function amazon_card_shortcode($atts) {
        $atts = shortcode_atts(array(
            'asin' => '',
            'layout' => 'card',
            'show_price' => '',
        ), $atts, 'amazon_card');

        if (empty($atts['asin'])) {
            return '<p>' . esc_html__('Please provide an ASIN.', 'amazon-affiliate-displays') . '</p>';
        }

        $api_client = AAPD_API_Client::instance();
        $products = $api_client->get_products_by_asin($atts['asin']);

        if (is_wp_error($products)) {
            if (current_user_can('manage_options')) {
                return '<div class="aapd-error">' . esc_html($products->get_error_message()) . '</div>';
            }
            return '';
        }

        if (empty($products)) {
            return '';
        }

        $settings = AAPD_Settings::instance()->get_settings();
        $options = array(
            'display_price' => $this->normalize_bool_attribute($atts['show_price'], $settings['show_price']),
            'show_review_count' => (bool) $settings['show_review_count'],
        );

        aapd()->enqueue_frontend_assets();

        $renderer = AAPD_Layout_Renderer::instance();
        return $renderer->render_single_product($products[0], $atts['layout'], $options);
    }

    public function amazon_products_shortcode($atts) {
        $atts = shortcode_atts(array(
            'asin' => '',
            'keyword' => '',
            'layout' => 'grid',
            'columns' => '3',
            'limit' => '10',
            'show_price' => '',
        ), $atts, 'amazon_products');

        $api_client = AAPD_API_Client::instance();

        if (!empty($atts['asin'])) {
            $asins = array_map('trim', explode(',', $atts['asin']));
            $products = $api_client->get_products_by_asin($asins);
        } elseif (!empty($atts['keyword'])) {
            $limit = min(absint($atts['limit']), 10);
            $products = $api_client->search_products($atts['keyword'], $limit);
        } else {
            return '<p>' . esc_html__('Please provide either an ASIN or keyword.', 'amazon-affiliate-displays') . '</p>';
        }

        if (is_wp_error($products)) {
            if (current_user_can('manage_options')) {
                return '<div class="aapd-error">' . esc_html($products->get_error_message()) . '</div>';
            }
            return '';
        }

        if (empty($products)) {
            return '';
        }

        $settings = AAPD_Settings::instance()->get_settings();
        $options = array(
            'display_price' => $this->normalize_bool_attribute($atts['show_price'], $settings['show_price']),
            'show_review_count' => (bool) $settings['show_review_count'],
        );

        aapd()->enqueue_frontend_assets();

        $renderer = AAPD_Layout_Renderer::instance();
        return $renderer->render_products($products, $atts['layout'], absint($atts['columns']), $options);
    }

    private function normalize_bool_attribute($attr_value, $default) {
        if ('' === $attr_value || null === $attr_value) {
            return (bool) $default;
        }

        if (is_bool($attr_value)) {
            return $attr_value;
        }

        $value = strtolower(trim((string) $attr_value));

        if (in_array($value, array('true', '1', 'yes', 'on'), true)) {
            return true;
        }

        if (in_array($value, array('false', '0', 'no', 'off'), true)) {
            return false;
        }

        return (bool) $default;
    }
}
