<?php

if (!defined('ABSPATH')) {
    exit;
}

class AAPD_API_Client {

    private $access_key;
    private $secret_key;
    private $associate_tag;
    private $region;
    private $marketplace;
    private $host;

    private static $instance = null;

    private static $hosts = array(
        'www.amazon.com'      => 'webservices.amazon.com',
        'www.amazon.ca'       => 'webservices.amazon.ca',
        'www.amazon.co.uk'    => 'webservices.amazon.co.uk',
        'www.amazon.de'       => 'webservices.amazon.de',
        'www.amazon.fr'       => 'webservices.amazon.fr',
        'www.amazon.it'       => 'webservices.amazon.it',
        'www.amazon.es'       => 'webservices.amazon.es',
        'www.amazon.co.jp'    => 'webservices.amazon.co.jp',
        'www.amazon.in'       => 'webservices.amazon.in',
        'www.amazon.com.au'   => 'webservices.amazon.com.au',
    );

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $settings = AAPD_Settings::instance()->get_settings();

        $this->access_key    = $settings['access_key'];
        $this->secret_key    = $settings['secret_key'];
        $this->associate_tag = $settings['associate_tag'];
        $this->region        = $settings['region'];
        $this->marketplace   = $settings['marketplace'];
        $this->host          = $this->resolve_host($this->marketplace);
    }

    private function resolve_host($marketplace) {
        if (isset(self::$hosts[$marketplace])) {
            return self::$hosts[$marketplace];
        }
        return 'webservices.amazon.com';
    }

    private function get_endpoint($operation) {
        $path = strtolower($operation);
        return sprintf('https://%s/paapi5/%s', $this->host, $path);
    }

    public function get_products_by_asin($asins) {
        if (!is_array($asins)) {
            $asins = array($asins);
        }

        $asins = array_slice(array_filter(array_map('trim', $asins)), 0, 10);
        if (empty($asins)) {
            return array();
        }

        $cache_key = $this->build_cache_key('asin', implode('-', $asins));
        $cached    = get_transient($cache_key);
        if (false !== $cached) {
            return $cached;
        }

        if (!$this->credentials_ready()) {
            return new WP_Error('missing_credentials', __('Amazon API credentials are not configured.', 'amazon-affiliate-displays'));
        }

        $payload = array(
            'ItemIds'    => array_values($asins),
            'Resources'  => $this->get_default_resources(),
            'PartnerTag' => $this->associate_tag,
            'PartnerType'=> 'Associates',
            'Marketplace'=> $this->marketplace,
        );

        $response = $this->make_request('GetItems', $payload);
        if (is_wp_error($response)) {
            return $response;
        }

        $products = $this->parse_products_response($response);
        $this->maybe_cache($cache_key, $products);

        return $products;
    }

    public function search_products($keyword, $limit = 10) {
        $keyword = trim($keyword);
        if ('' === $keyword) {
            return array();
        }

        $limit = max(1, min(10, absint($limit)));

        $cache_key = $this->build_cache_key('search', $keyword . '_' . $limit);
        $cached    = get_transient($cache_key);
        if (false !== $cached) {
            return $cached;
        }

        if (!$this->credentials_ready()) {
            return new WP_Error('missing_credentials', __('Amazon API credentials are not configured.', 'amazon-affiliate-displays'));
        }

        $payload = array(
            'Keywords'   => $keyword,
            'ItemCount'  => $limit,
            'Resources'  => $this->get_default_resources(),
            'PartnerTag' => $this->associate_tag,
            'PartnerType'=> 'Associates',
            'Marketplace'=> $this->marketplace,
        );

        $response = $this->make_request('SearchItems', $payload);
        if (is_wp_error($response)) {
            return $response;
        }

        $products = $this->parse_search_response($response);
        $this->maybe_cache($cache_key, $products);

        return $products;
    }

    private function credentials_ready() {
        return !empty($this->access_key) && !empty($this->secret_key) && !empty($this->associate_tag);
    }

    private function build_cache_key($type, $identifier) {
        $hash = md5($identifier . '|' . $this->associate_tag . '|' . $this->marketplace . '|' . $this->region);
        return implode(':', array('aapd', sanitize_key($type), $hash));
    }

    private function maybe_cache($cache_key, $data) {
        if (empty($data)) {
            return;
        }
        $settings = AAPD_Settings::instance()->get_settings();
        $duration = isset($settings['cache_duration']) ? absint($settings['cache_duration']) : 3600;
        if ($duration > 0) {
            set_transient($cache_key, $data, $duration);
        }
    }

    private function get_default_resources() {
        return array(
            'Images.Primary.Large',
            'ItemInfo.Title',
            'ItemInfo.Features',
            'ItemInfo.ContentInfo',
            'ItemInfo.ByLineInfo',
            'ItemInfo.Classifications',
            'Offers.Listings.Price',
            'Offers.Listings.SavingBasis',
            'Offers.Listings.DeliveryInfo.IsPrimeEligible',
            'Offers.Listings.DeliveryInfo.IsAmazonFulfilled',
            'Offers.Listings.Promotions',
            'CustomerReviews.StarRating',
            'CustomerReviews.Count',
        );
    }

    private function make_request($operation, $payload) {
        $endpoint = $this->get_endpoint($operation);
        $body     = wp_json_encode($payload);

        $headers = $this->sign_request($operation, $body, $endpoint);

        $response = wp_remote_post($endpoint, array(
            'headers' => $headers,
            'body'    => $body,
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data)) {
            return new WP_Error('invalid_response', __('Invalid response from Amazon API.', 'amazon-affiliate-displays'));
        }

        if ($code >= 400 || isset($data['Errors'])) {
            $message = isset($data['Errors'][0]['Message']) ? $data['Errors'][0]['Message'] : __('Unknown API error', 'amazon-affiliate-displays');
            return new WP_Error('api_error', $message);
        }

        return $data;
    }

    private function sign_request($operation, $payload, $endpoint) {
        $method  = 'POST';
        $service = 'paapi5';
        $host    = parse_url($endpoint, PHP_URL_HOST);
        $uri     = parse_url($endpoint, PHP_URL_PATH);

        $timestamp = gmdate('Ymd\THis\Z');
        $date      = gmdate('Ymd');

        $canonical_headers = "content-encoding:amz-1.0\n"
            . "content-type:application/json; charset=utf-8\n"
            . "host:{$host}\n"
            . "x-amz-date:{$timestamp}\n"
            . "x-amz-target:com.amazon.paapi5.v1.ProductAdvertisingAPIv1.{$operation}\n";

        $signed_headers   = 'content-encoding;content-type;host;x-amz-date;x-amz-target';
        $payload_hash     = hash('sha256', $payload);
        $canonical_request = "{$method}\n{$uri}\n\n{$canonical_headers}\n{$signed_headers}\n{$payload_hash}";

        $algorithm       = 'AWS4-HMAC-SHA256';
        $credential_scope = "{$date}/{$this->region}/{$service}/aws4_request";
        $string_to_sign   = "{$algorithm}\n{$timestamp}\n{$credential_scope}\n" . hash('sha256', $canonical_request);

        $k_date    = hash_hmac('sha256', $date, 'AWS4' . $this->secret_key, true);
        $k_region  = hash_hmac('sha256', $this->region, $k_date, true);
        $k_service = hash_hmac('sha256', $service, $k_region, true);
        $k_signing = hash_hmac('sha256', 'aws4_request', $k_service, true);
        $signature = hash_hmac('sha256', $string_to_sign, $k_signing);

        $authorization = sprintf(
            '%s Credential=%s/%s, SignedHeaders=%s, Signature=%s',
            $algorithm,
            $this->access_key,
            $credential_scope,
            $signed_headers,
            $signature
        );

        return array(
            'Content-Encoding' => 'amz-1.0',
            'Content-Type'     => 'application/json; charset=utf-8',
            'Host'             => $host,
            'X-Amz-Date'       => $timestamp,
            'X-Amz-Target'     => "com.amazon.paapi5.v1.ProductAdvertisingAPIv1.{$operation}",
            'Authorization'    => $authorization,
        );
    }

    private function parse_products_response($response) {
        if (empty($response['ItemsResult']['Items'])) {
            return array();
        }

        return array_map(array($this, 'format_product_data'), $response['ItemsResult']['Items']);
    }

    private function parse_search_response($response) {
        if (empty($response['SearchResult']['Items'])) {
            return array();
        }

        return array_map(array($this, 'format_product_data'), $response['SearchResult']['Items']);
    }

    private function format_product_data($item) {
        $asin  = isset($item['ASIN']) ? $item['ASIN'] : '';
        $title = isset($item['ItemInfo']['Title']['DisplayValue']) ? $item['ItemInfo']['Title']['DisplayValue'] : '';

        $image_url = '';
        if (!empty($item['Images']['Primary']['Large']['URL'])) {
            $image_url = $item['Images']['Primary']['Large']['URL'];
        }

        $price          = '';
        $list_price     = '';
        $discount       = 0;
        $prime_eligible = false;

        if (!empty($item['Offers']['Listings'][0])) {
            $listing = $item['Offers']['Listings'][0];
            if (!empty($listing['Price']['DisplayAmount'])) {
                $price = $listing['Price']['DisplayAmount'];
            }
            if (!empty($listing['SavingBasis']['DisplayAmount'])) {
                $list_price = $listing['SavingBasis']['DisplayAmount'];
            }
            if (!empty($listing['Price']['Amount']) && !empty($listing['SavingBasis']['Amount'])) {
                $save_amount = $listing['SavingBasis']['Amount'] - $listing['Price']['Amount'];
                if ($save_amount > 0 && $listing['SavingBasis']['Amount'] > 0) {
                    $discount = (int) round(($save_amount / $listing['SavingBasis']['Amount']) * 100);
                }
            }
            if (!empty($listing['DeliveryInfo']['IsPrimeEligible'])) {
                $prime_eligible = (bool) $listing['DeliveryInfo']['IsPrimeEligible'];
            }
        }

        $rating = 0;
        if (!empty($item['CustomerReviews']['StarRating']['Value'])) {
            $rating = floatval($item['CustomerReviews']['StarRating']['Value']);
        }

        $review_count = 0;
        if (!empty($item['CustomerReviews']['Count'])) {
            $review_count = intval($item['CustomerReviews']['Count']);
        }

        $features = array();
        if (!empty($item['ItemInfo']['Features']['DisplayValues'])) {
            $features = (array) $item['ItemInfo']['Features']['DisplayValues'];
        }

        $description = '';
        if (!empty($item['ItemInfo']['ContentInfo']['Synopsis']['DisplayValue'])) {
            $description = $item['ItemInfo']['ContentInfo']['Synopsis']['DisplayValue'];
        } elseif (!empty($features[0])) {
            $description = $features[0];
        }

        $detail_url    = !empty($item['DetailPageURL']) ? $item['DetailPageURL'] : '';
        $affiliate_url = $this->build_affiliate_url($asin, $detail_url);

        return array(
            'asin'          => $asin,
            'title'         => $title,
            'image_url'     => $image_url,
            'price'         => $price,
            'list_price'    => $list_price,
            'discount'      => $discount,
            'prime_eligible'=> $prime_eligible,
            'rating'        => $rating,
            'review_count'  => $review_count,
            'features'      => array_slice($features, 0, 5),
            'description'   => $description,
            'affiliate_url' => $affiliate_url,
        );
    }

    private function build_affiliate_url($asin, $detail_url = '') {
        if (!empty($detail_url)) {
            return add_query_arg('tag', $this->associate_tag, $detail_url);
        }
        $base_url = sprintf('https://%s/dp/%s', $this->marketplace, rawurlencode($asin));
        return add_query_arg('tag', $this->associate_tag, $base_url);
    }
}
