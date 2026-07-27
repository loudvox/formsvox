<?php

namespace FormsVox\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Database Repository Model.
 */
class FormModel {
	/**
	 * Get table name.
	 *
	 * @return string
	 */
	public static function get_table_name() {
		global $wpdb;
		return $wpdb->prefix . 'formsvox_forms';
	}

	/**
	 * Get form by ID.
	 *
	 * @param int $id Form ID.
	 * @return array|null
	 */
	public static function get( $id ) {
		global $wpdb;
		$table = self::get_table_name();
		$query = $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", intval( $id ) );
		$form  = $wpdb->get_row( $query, ARRAY_A );

		if ( $form && ! empty( $form['schema_json'] ) ) {
			$form['schema'] = json_decode( $form['schema_json'], true );
		}

		return $form;
	}

	/**
	 * List forms.
	 *
	 * @param array $args Query arguments.
	 * @return array
	 */
	public static function all( $args = array() ) {
		global $wpdb;
		$table  = self::get_table_name();
		$status = isset( $args['status'] ) ? sanitize_text_field( $args['status'] ) : 'publish';
		$limit  = isset( $args['limit'] ) ? intval( $args['limit'] ) : 100;
		$offset = isset( $args['offset'] ) ? intval( $args['offset'] ) : 0;

		$query = $wpdb->prepare(
			"SELECT * FROM {$table} WHERE status = %s ORDER BY id DESC LIMIT %d OFFSET %d",
			$status,
			$limit,
			$offset
		);
		$forms = $wpdb->get_results( $query, ARRAY_A );

		foreach ( $forms as &$form ) {
			if ( ! empty( $form['schema_json'] ) ) {
				$form['schema'] = json_decode( $form['schema_json'], true );
			}
		}

		return $forms;
	}

	/**
	 * Create a new form.
	 *
	 * @param string $title  Form title.
	 * @param array  $schema Form JSON schema.
	 * @param string $status Form status.
	 * @return int Form ID.
	 */
	public static function create( $title, $schema = array(), $status = 'publish' ) {
		global $wpdb;
		$table = self::get_table_name();

		$wpdb->insert(
			$table,
			array(
				'title'       => sanitize_text_field( $title ),
				'status'      => sanitize_text_field( $status ),
				'schema_json' => wp_json_encode( $schema ),
				'created_at'  => current_time( 'mysql' ),
				'updated_at'  => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s', '%s' )
		);

		return $wpdb->insert_id;
	}

	/**
	 * Update an existing form.
	 *
	 * @param int    $id     Form ID.
	 * @param string $title  Form title.
	 * @param array  $schema Form JSON schema.
	 * @param string $status Form status.
	 * @return bool
	 */
	public static function update( $id, $title = null, $schema = null, $status = null ) {
		global $wpdb;
		$table = self::get_table_name();
		$data  = array(
			'updated_at' => current_time( 'mysql' ),
		);
		$format = array( '%s' );

		if ( ! is_null( $title ) ) {
			$data['title'] = sanitize_text_field( $title );
			$format[]      = '%s';
		}
		if ( ! is_null( $schema ) ) {
			$data['schema_json'] = wp_json_encode( $schema );
			$format[]            = '%s';
		}
		if ( ! is_null( $status ) ) {
			$data['status'] = sanitize_text_field( $status );
			$format[]       = '%s';
		}

		$result = $wpdb->update(
			$table,
			$data,
			array( 'id' => intval( $id ) ),
			$format,
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Delete a form.
	 *
	 * @param int $id Form ID.
	 * @return bool
	 */
	public static function delete( $id ) {
		global $wpdb;
		$table = self::get_table_name();
		return (bool) $wpdb->delete( $table, array( 'id' => intval( $id ) ), array( '%d' ) );
	}
}
