<?php

namespace FormsVox\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Entry Database Repository Model.
 */
class EntryModel {
	/**
	 * Get table names.
	 */
	public static function get_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'formsvox_entries';
	}

	public static function get_meta_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'formsvox_entry_meta';
	}

	/**
	 * Create entry with field values.
	 *
	 * @param int   $form_id Form ID.
	 * @param array $fields  Associative array of field_id => value or field metadata.
	 * @return int Entry ID.
	 */
	public static function create( $form_id, $fields = array() ) {
		global $wpdb;
		$entries_table    = self::get_table_name();
		$entry_meta_table = self::get_meta_table_name();

		$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		$wpdb->insert(
			$entries_table,
			array(
				'form_id'    => intval( $form_id ),
				'status'     => 'publish',
				'starred'    => 0,
				'is_read'    => 0,
				'ip_address' => $ip_address,
				'user_agent' => $user_agent,
				'created_at' => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%d', '%d', '%s', '%s', '%s' )
		);

		$entry_id = $wpdb->insert_id;

		foreach ( $fields as $field_id => $val ) {
			$meta_value = is_array( $val ) ? wp_json_encode( $val ) : (string) $val;
			$wpdb->insert(
				$entry_meta_table,
				array(
					'entry_id'   => $entry_id,
					'field_id'   => sanitize_text_field( $field_id ),
					'meta_key'   => 'value',
					'meta_value' => $meta_value,
				),
				array( '%d', '%s', '%s', '%s' )
			);
		}

		return $entry_id;
	}

	public static function add_meta( $entry_id, $meta_key, $meta_value ) {
		global $wpdb;
		$entry_meta_table = self::get_meta_table_name();
		$val = is_array( $meta_value ) ? wp_json_encode( $meta_value ) : (string) $meta_value;

		$wpdb->insert(
			$entry_meta_table,
			array(
				'entry_id'   => intval( $entry_id ),
				'field_id'   => 'system',
				'meta_key'   => sanitize_text_field( $meta_key ),
				'meta_value' => $val,
			),
			array( '%d', '%s', '%s', '%s' )
		);
	}

	/**
	 * Get single entry with meta.
	 *
	 * @param int $id Entry ID.
	 * @return array|null
	 */
	public static function get( $id ) {
		global $wpdb;
		$entries_table    = self::get_table_name();
		$entry_meta_table = self::get_meta_table_name();

		$entry = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$entries_table} WHERE id = %d", intval( $id ) ),
			ARRAY_A
		);

		if ( ! $entry ) {
			return null;
		}

		$meta_rows = $wpdb->get_results(
			$wpdb->prepare( "SELECT field_id, meta_key, meta_value FROM {$entry_meta_table} WHERE entry_id = %d", intval( $id ) ),
			ARRAY_A
		);

		$fields = array();
		foreach ( $meta_rows as $row ) {
			$val = $row['meta_value'];
			$decoded = json_decode( $val, true );
			if ( json_last_error() === JSON_ERROR_NONE && ( is_array( $decoded ) || is_object( $decoded ) ) ) {
				$val = $decoded;
			}
			$fields[ $row['field_id'] ] = $val;
		}

		$entry['fields'] = $fields;
		return $entry;
	}

	/**
	 * Query entries with pagination, search, and filtering.
	 *
	 * @param array $args Query parameters.
	 * @return array
	 */
	public static function query( $args = array() ) {
		global $wpdb;
		$entries_table = self::get_table_name();

		$form_id = isset( $args['form_id'] ) ? intval( $args['form_id'] ) : 0;
		$status  = isset( $args['status'] ) ? sanitize_text_field( $args['status'] ) : '';
		$starred = isset( $args['starred'] ) ? intval( $args['starred'] ) : null;
		$limit   = isset( $args['limit'] ) ? intval( $args['limit'] ) : 20;
		$page    = isset( $args['page'] ) ? max( 1, intval( $args['page'] ) ) : 1;
		$offset  = ( $page - 1 ) * $limit;

		$where = array( '1=1' );
		$params = array();

		if ( $form_id > 0 ) {
			$where[]  = 'form_id = %d';
			$params[] = $form_id;
		}
		if ( ! empty( $status ) ) {
			$where[]  = 'status = %s';
			$params[] = $status;
		}
		if ( ! is_null( $starred ) ) {
			$where[]  = 'starred = %d';
			$params[] = $starred;
		}

		$where_clause = implode( ' AND ', $where );
		$count_sql    = "SELECT COUNT(*) FROM {$entries_table} WHERE {$where_clause}";
		$total        = (int) $wpdb->get_var( empty( $params ) ? $count_sql : $wpdb->prepare( $count_sql, $params ) );

		$params[] = $limit;
		$params[] = $offset;
		$data_sql = "SELECT * FROM {$entries_table} WHERE {$where_clause} ORDER BY id DESC LIMIT %d OFFSET %d";
		$entries  = $wpdb->get_results( $wpdb->prepare( $data_sql, $params ), ARRAY_A );

		foreach ( $entries as &$entry ) {
			$full_entry     = self::get( $entry['id'] );
			$entry['fields'] = isset( $full_entry['fields'] ) ? $full_entry['fields'] : array();
		}

		return array(
			'items' => $entries,
			'total' => $total,
			'pages' => ceil( $total / $limit ),
		);
	}

	/**
	 * Update entry status or flags.
	 *
	 * @param int   $id   Entry ID.
	 * @param array $data Fields to update (starred, is_read, status).
	 * @return bool
	 */
	public static function update( $id, $data = array() ) {
		global $wpdb;
		$table  = self::get_table_name();
		$fields = array();
		$format = array();

		if ( isset( $data['starred'] ) ) {
			$fields['starred'] = intval( $data['starred'] );
			$format[]          = '%d';
		}
		if ( isset( $data['is_read'] ) ) {
			$fields['is_read'] = intval( $data['is_read'] );
			$format[]          = '%d';
		}
		if ( isset( $data['status'] ) ) {
			$fields['status'] = sanitize_text_field( $data['status'] );
			$format[]         = '%s';
		}

		if ( empty( $fields ) ) {
			return false;
		}

		$result = $wpdb->update( $table, $fields, array( 'id' => intval( $id ) ), $format, array( '%d' ) );
		return false !== $result;
	}

	/**
	 * Delete entry and its meta.
	 *
	 * @param int $id Entry ID.
	 * @return bool
	 */
	public static function delete( $id ) {
		global $wpdb;
		$entries_table    = self::get_table_name();
		$entry_meta_table = self::get_meta_table_name();

		$wpdb->delete( $entry_meta_table, array( 'entry_id' => intval( $id ) ), array( '%d' ) );
		return (bool) $wpdb->delete( $entries_table, array( 'id' => intval( $id ) ), array( '%d' ) );
	}
}
