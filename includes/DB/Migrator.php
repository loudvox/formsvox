<?php

namespace FormsVox\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Migration & Database Schema Engine.
 */
class Migrator {
	/**
	 * Instance reference.
	 *
	 * @var Migrator|null
	 */
	private static $instance = null;

	/**
	 * Database version option key.
	 */
	const DB_VERSION_OPTION = 'formsvox_db_version';

	/**
	 * Current Database schema version.
	 */
	const DB_VERSION = '1.0.0';

	/**
	 * Singleton instance getter.
	 *
	 * @return Migrator
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Check if database migration is needed.
	 */
	public function __construct() {
		$installed_ver = get_option( self::DB_VERSION_OPTION, '0.0.0' );
		if ( version_compare( $installed_ver, self::DB_VERSION, '<' ) ) {
			add_action( 'admin_init', array( $this, 'migrate' ) );
		}
	}

	/**
	 * Execute dbDelta database migrations.
	 */
	public function migrate() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$table_forms      = $wpdb->prefix . 'formsvox_forms';
		$table_entries    = $wpdb->prefix . 'formsvox_entries';
		$table_entry_meta = $wpdb->prefix . 'formsvox_entry_meta';

		$sql_forms = "CREATE TABLE {$table_forms} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			title varchar(255) NOT NULL,
			status varchar(50) DEFAULT 'publish' NOT NULL,
			schema_json longtext NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY status (status)
		) {$charset_collate};";

		$sql_entries = "CREATE TABLE {$table_entries} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			form_id bigint(20) unsigned NOT NULL,
			status varchar(50) DEFAULT 'publish' NOT NULL,
			starred tinyint(1) DEFAULT 0 NOT NULL,
			is_read tinyint(1) DEFAULT 0 NOT NULL,
			ip_address varchar(100) DEFAULT '' NOT NULL,
			user_agent text NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY form_id (form_id),
			KEY status (status),
			KEY starred (starred),
			KEY is_read (is_read)
		) {$charset_collate};";

		$sql_entry_meta = "CREATE TABLE {$table_entry_meta} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			entry_id bigint(20) unsigned NOT NULL,
			field_id varchar(100) NOT NULL,
			meta_key varchar(255) NOT NULL,
			meta_value longtext NOT NULL,
			PRIMARY KEY  (id),
			KEY entry_id (entry_id),
			KEY field_id (field_id),
			KEY meta_key (meta_key(191))
		) {$charset_collate};";

		dbDelta( $sql_forms );
		dbDelta( $sql_entries );
		dbDelta( $sql_entry_meta );

		update_option( self::DB_VERSION_OPTION, self::DB_VERSION );
	}
}
