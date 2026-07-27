<?php
/**
 * FormVox Uninstall Handler.
 *
 * @package FormVox
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings            = get_option( 'formvox_settings', array() );
$delete_on_uninstall = ! empty( $settings['delete_on_uninstall'] );

if ( $delete_on_uninstall ) {
	global $wpdb;

	$table_forms      = $wpdb->prefix . 'formvox_forms';
	$table_entries    = $wpdb->prefix . 'formvox_entries';
	$table_entry_meta = $wpdb->prefix . 'formvox_entry_meta';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$wpdb->query( "DROP TABLE IF EXISTS {$table_entry_meta}" );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$wpdb->query( "DROP TABLE IF EXISTS {$table_entries}" );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$wpdb->query( "DROP TABLE IF EXISTS {$table_forms}" );

	delete_option( 'formvox_db_version' );
	delete_option( 'formvox_version' );
	delete_option( 'formvox_settings' );
	delete_option( 'formvox_dismissed_pro_notice' );
}
