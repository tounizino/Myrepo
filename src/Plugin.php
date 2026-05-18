<?php

namespace CloudGamersDiscuss;

/**
 * Main Plugin Class
 */
class Plugin {
    /**
     * @var Plugin|null
     */
    private static $instance = null;

    /**
     * @var array
     */
    private $modules = [];

    /**
     * Get instance
     */
    public static function getInstance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->initModules();
    }

    /**
     * Initialize Modules
     */
    private function initModules() {
        $this->modules['assets']      = new Frontend\Assets();
        $this->modules['comments']    = new Frontend\Comments();
        $this->modules['reactions']   = new Frontend\Reactions();
        $this->modules['ratings']     = new Frontend\Ratings();
        $this->modules['features']    = new Frontend\ArticleFeatures();
        $this->modules['exit_intent'] = new Frontend\ExitIntent();
        $this->modules['api']         = new API\Router();
        $this->modules['mailchimp']   = new Integrations\Mailchimp();
        $this->modules['seo']         = new Utils\SEO();
        
        if ( is_admin() ) {
            $this->modules['admin'] = new Admin\Dashboard();
            $this->modules['settings'] = new Admin\Settings();
        }
    }

    /**
     * Get a module
     */
    public function getModule( $name ) {
        return $this->modules[ $name ] ?? null;
    }

    /**
     * Activation logic
     */
    public static function activate() {
        // Create tables
        Models\Reaction::createTable();
        Models\Rating::createTable();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Deactivation logic
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
