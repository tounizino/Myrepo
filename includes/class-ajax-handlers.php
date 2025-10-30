<?php
/**
 * AJAX Handlers
 */

if (!defined('ABSPATH')) {
    exit;
}

class UBCG_Ajax_Handlers {
    
    public function __construct() {
        add_action('wp_ajax_ubcg_newsletter_signup', array($this, 'handle_newsletter_signup'));
        add_action('wp_ajax_nopriv_ubcg_newsletter_signup', array($this, 'handle_newsletter_signup'));
    }
    
    public function handle_newsletter_signup() {
        check_ajax_referer('ubcg_nonce', 'nonce');
        
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        
        if (empty($email)) {
            wp_send_json_error(array(
                'message' => __('Please enter a valid email address.', 'ubcg')
            ));
        }
        
        if (!is_email($email)) {
            wp_send_json_error(array(
                'message' => __('Please enter a valid email address.', 'ubcg')
            ));
        }
        
        $existing = get_option('ubcg_newsletter_subscribers', array());
        
        if (in_array($email, $existing)) {
            wp_send_json_error(array(
                'message' => __('This email is already subscribed.', 'ubcg')
            ));
        }
        
        $existing[] = $email;
        update_option('ubcg_newsletter_subscribers', $existing);
        
        do_action('ubcg_newsletter_subscribed', $email);
        
        wp_send_json_success(array(
            'message' => __('Thank you for subscribing!', 'ubcg')
        ));
    }
}

new UBCG_Ajax_Handlers();
