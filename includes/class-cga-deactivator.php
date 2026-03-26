<?php

if (!defined('ABSPATH')) {
    exit;
}

class CGA_Deactivator {

    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
