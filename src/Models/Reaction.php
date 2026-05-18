<?php

namespace CloudGamersDiscuss\Models;

class Reaction {
	public static function createTable() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'cgd_reactions';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			comment_id bigint(20) DEFAULT 0,
			post_id bigint(20) NOT NULL,
			type varchar(50) NOT NULL,
			ip_address varchar(100) DEFAULT '',
			user_agent varchar(255) DEFAULT '',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY post_id (post_id),
			KEY comment_id (comment_id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}
