<?php

namespace FormVox\Importers;

use FormVox\DB\FormModel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPFormsImporter {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function import( $json_data ) {
		$decoded = is_array( $json_data ) ? $json_data : json_decode( $json_data, true );
		if ( ! is_array( $decoded ) ) {
			return new \WP_Error( 'invalid_json', __( 'Invalid WPForms JSON structure.', 'formvox' ) );
		}

		$title  = isset( $decoded['settings']['form_title'] ) ? $decoded['settings']['form_title'] : __( 'Imported WPForms Form', 'formvox' );
		$fields = array();

		if ( isset( $decoded['fields'] ) && is_array( $decoded['fields'] ) ) {
			foreach ( $decoded['fields'] as $wpf_field ) {
				$type = isset( $wpf_field['type'] ) ? $wpf_field['type'] : 'text';

				// Map WPForms field types to FormVox field types
				$type_map = array(
					'text'            => 'text',
					'textarea'        => 'textarea',
					'name'            => 'name',
					'email'           => 'email',
					'phone'           => 'phone',
					'address'         => 'address',
					'url'             => 'url',
					'number'          => 'number',
					'select'          => 'select',
					'radio'           => 'radio',
					'checkbox'        => 'checkbox',
					'file-upload'     => 'file_upload',
					'pagebreak'       => 'page_break',
					'html'            => 'html',
					'divider'         => 'section',
					'rating'          => 'rating',
					'likert_scale'    => 'likert',
					'net_promoter_score' => 'nps',
				);

				$fv_type = isset( $type_map[ $type ] ) ? $type_map[ $type ] : 'text';

				$fields[] = array(
					'id'          => 'field_' . ( isset( $wpf_field['id'] ) ? $wpf_field['id'] : rand( 100, 999 ) ),
					'type'        => $fv_type,
					'label'       => isset( $wpf_field['label'] ) ? $wpf_field['label'] : __( 'Field', 'formvox' ),
					'description' => isset( $wpf_field['description'] ) ? $wpf_field['description'] : '',
					'required'    => ! empty( $wpf_field['required'] ),
				);
			}
		}

		$schema = array(
			'settings'      => array(
				'title'       => $title,
				'description' => isset( $decoded['settings']['form_desc'] ) ? $decoded['settings']['form_desc'] : '',
				'submit_text' => isset( $decoded['settings']['submit_text'] ) ? $decoded['settings']['submit_text'] : __( 'Submit', 'formvox' ),
				'ajax_submit' => true,
			),
			'fields'        => $fields,
			'notifications' => array(
				array(
					'id'       => 'notif_1',
					'name'     => __( 'Default Notification', 'formvox' ),
					'to_email' => '{admin_email}',
					'subject'  => 'New Form Submission',
					'message'  => '{all_fields}',
				),
			),
			'confirmations' => array(
				array(
					'id'      => 'conf_1',
					'type'    => 'message',
					'message' => __( 'Thank you! Your submission has been received.', 'formvox' ),
				),
			),
		);

		$new_id = FormModel::create( $title, $schema );
		return array(
			'success' => true,
			'form_id' => $new_id,
		);
	}
}
