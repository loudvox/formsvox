<?php
/**
 * FormsVox Uninstall Handler.
 *
 * @package FormsVox
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings            = get_option( 'formsvox_settings', array() );
$delete_on_uninstall = ! empty( $settings['delete_on_uninstall'] );

if ( $delete_on_uninstall ) {
	global $wpdb;

	$table_forms      = $wpdb->prefix . 'formsvox_forms';
	$table_entries    = $wpdb->prefix . 'formsvox_entries';
	$table_entry_meta = $wpdb->prefix . 'formsvox_entry_meta';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$wpdb->query( "DROP TABLE IF EXISTS {$table_entry_meta}" );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$wpdb->query( "DROP TABLE IF EXISTS {$table_entries}" );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$wpdb->query( "DROP TABLE IF EXISTS {$table_forms}" );

	delete_option( 'formsvox_db_version' );
	delete_option( 'formsvox_version' );
	delete_option( 'formsvox_settings' );
	delete_option( 'formsvox_dismissed_pro_notice' );
}
