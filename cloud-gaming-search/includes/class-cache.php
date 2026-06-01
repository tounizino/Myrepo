<?php
/**
 * CGS Cache Layer
 * 
 * Transient-based caching with auto-expiry and cache tagging.
 */

defined('ABSPATH') || exit;

class CGS_Cache {

    private $prefix = 'cgs_';

    /**
     * Get cached value.
     */
    public function get($key) {
        $data = get_transient($this->prefix . $key);
        if (false === $data) {
            return null;
        }
        return $data;
    }

    /**
     * Set cached value.
     */
    public function set($key, $value, $ttl = null) {
        if (null === $ttl) {
            $ttl = get_option('cgs_cache_ttl', HOUR_IN_SECONDS);
        }
        set_transient($this->prefix . $key, $value, (int) $ttl);
    }

    /**
     * Delete a cached key.
     */
    public function delete($key) {
        delete_transient($this->prefix . $key);
    }

    /**
     * Clear all plugin cache.
     */
    public function clear_all() {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
                '_transient_' . $this->prefix . '%',
                '_transient_timeout_' . $this->prefix . '%'
            )
        );
    }

    /**
     * Hashed cache key for consistent lengths.
     */
    public function make_key($parts) {
        return md5(implode('_', (array) $parts));
    }

    /**
     * Get or compute cached value.
     */
    public function remember($key, $callback, $ttl = null) {
        $cached = $this->get($key);
        if (null !== $cached) {
            return $cached;
        }
        $value = call_user_func($callback);
        $this->set($key, $value, $ttl);
        return $value;
    }
}