<?php

namespace FormsVox\Importers;

use FormsVox\DB\FormModel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPForms Import Engine.
 */
class WPFormsImporter {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Map and import WPForms JSON export data into FormsVox schema.
	 *
	 * @param string|array $json_data WPForms JSON export payload.
	 * @return array|\WP_Error
	 */
	public static function import( $json_data ) {
		$decoded = is_array( $json_data ) ? $json_data : json_decode( $json_data, true );

		if ( ! is_array( $decoded ) ) {
			return new \WP_Error( 'invalid_json', __( 'Invalid or corrupted WPForms JSON structure.', 'formsvox' ) );
		}

		// Support both single form JSON export and array wrapped exports
		$wpf_form = isset( $decoded[0] ) && is_array( $decoded[0] ) ? $decoded[0] : $decoded;

		$title = isset( $wpf_form['settings']['form_title'] ) ? sanitize_text_field( $wpf_form['settings']['form_title'] ) : __( 'Imported WPForms Form', 'formsvox' );
		$desc  = isset( $wpf_form['settings']['form_desc'] ) ? sanitize_text_field( $wpf_form['settings']['form_desc'] ) : '';
		$sub_txt = isset( $wpf_form['settings']['submit_text'] ) ? sanitize_text_field( $wpf_form['settings']['submit_text'] ) : __( 'Submit', 'formsvox' );

		$type_map = array(
			'text'               => 'text',
			'textarea'           => 'textarea',
			'select'             => 'select',
			'radio'              => 'radio',
			'checkbox'           => 'checkbox',
			'name'               => 'name',
			'email'              => 'email',
			'phone'              => 'phone',
			'address'            => 'address',
			'url'                => 'url',
			'number'             => 'number',
			'number-slider'      => 'slider',
			'file-upload'        => 'file_upload',
			'pagebreak'          => 'page_break',
			'divider'            => 'section',
			'html'               => 'html',
			'rating'             => 'rating',
			'likert_scale'       => 'likert',
			'net_promoter_score' => 'nps',
			'payment-single'     => 'payment_single',
			'payment-multiple'   => 'payment_multiple',
			'payment-total'      => 'payment_total',
		);

		$fields = array();
		if ( isset( $wpf_form['fields'] ) && is_array( $wpf_form['fields'] ) ) {
			foreach ( $wpf_form['fields'] as $wpf_id => $wpf_field ) {
				$type     = isset( $wpf_field['type'] ) ? $wpf_field['type'] : 'text';
				$fv_type  = isset( $type_map[ $type ] ) ? $type_map[ $type ] : 'text';
				$field_id = 'field_' . ( isset( $wpf_field['id'] ) ? $wpf_field['id'] : $wpf_id );

				$field_config = array(
					'id'          => $field_id,
					'type'        => $fv_type,
					'label'       => isset( $wpf_field['label'] ) ? sanitize_text_field( $wpf_field['label'] ) : __( 'Field', 'formsvox' ),
					'description' => isset( $wpf_field['description'] ) ? sanitize_text_field( $wpf_field['description'] ) : '',
					'placeholder' => isset( $wpf_field['placeholder'] ) ? sanitize_text_field( $wpf_field['placeholder'] ) : '',
					'required'    => ! empty( $wpf_field['required'] ),
					'css_class'   => isset( $wpf_field['css'] ) ? sanitize_text_field( $wpf_field['css'] ) : '',
					'default_val' => isset( $wpf_field['default_value'] ) ? sanitize_text_field( $wpf_field['default_value'] ) : '',
				);

				// Map choices for dropdown, radio, and checkbox fields
				if ( isset( $wpf_field['choices'] ) && is_array( $wpf_field['choices'] ) ) {
					$options = array();
					foreach ( $wpf_field['choices'] as $choice ) {
						$lbl       = isset( $choice['label'] ) ? sanitize_text_field( $choice['label'] ) : '';
						$val       = isset( $choice['value'] ) ? sanitize_text_field( $choice['value'] ) : strtolower( str_replace( ' ', '_', $lbl ) );
						$options[] = array( 'label' => $lbl, 'value' => $val );
					}
					$field_config['options'] = $options;
				}

				// Map number min/max/step settings
				if ( 'number' === $type || 'number-slider' === $type ) {
					if ( isset( $wpf_field['min'] ) ) $field_config['min'] = (float) $wpf_field['min'];
					if ( isset( $wpf_field['max'] ) ) $field_config['max'] = (float) $wpf_field['max'];
					if ( isset( $wpf_field['step'] ) ) $field_config['step'] = (float) $wpf_field['step'];
				}

				$fields[] = $field_config;
			}
		}

		// Map notifications
		$notifications = array();
		if ( isset( $wpf_form['settings']['notifications'] ) && is_array( $wpf_form['settings']['notifications'] ) ) {
			foreach ( $wpf_form['settings']['notifications'] as $nid => $notif ) {
				$notifications[] = array(
					'id'       => 'notif_' . $nid,
					'name'     => isset( $notif['notification_name'] ) ? sanitize_text_field( $notif['notification_name'] ) : 'Notification',
					'to_email' => isset( $notif['email'] ) ? sanitize_text_field( $notif['email'] ) : '{admin_email}',
					'subject'  => isset( $notif['subject'] ) ? sanitize_text_field( $notif['subject'] ) : 'New Form Submission',
					'message'  => isset( $notif['message'] ) ? sanitize_textarea_field( $notif['message'] ) : '{all_fields}',
				);
			}
		}

		if ( empty( $notifications ) ) {
			$notifications[] = array(
				'id'       => 'notif_1',
				'name'     => 'Default Notification',
				'to_email' => '{admin_email}',
				'subject'  => 'New Form Submission',
				'message'  => '{all_fields}',
			);
		}

		// Map confirmations
		$confirmations = array();
		if ( isset( $wpf_form['settings']['confirmations'] ) && is_array( $wpf_form['settings']['confirmations'] ) ) {
			foreach ( $wpf_form['settings']['confirmations'] as $cid => $conf ) {
				$c_type = isset( $conf['type'] ) && 'redirect' === $conf['type'] ? 'redirect' : 'message';
				$confirmations[] = array(
					'id'           => 'conf_' . $cid,
					'type'         => $c_type,
					'message'      => isset( $conf['message'] ) ? sanitize_textarea_field( $conf['message'] ) : __( 'Thank you for your submission.', 'formsvox' ),
					'redirect_url' => isset( $conf['page'] ) ? esc_url_raw( $conf['page'] ) : '',
				);
			}
		}

		if ( empty( $confirmations ) ) {
			$confirmations[] = array(
				'id'      => 'conf_1',
				'type'    => 'message',
				'message' => __( 'Thank you! Your submission has been received.', 'formsvox' ),
			);
		}

		$schema = array(
			'settings'      => array(
				'title'       => $title,
				'description' => $desc,
				'submit_text' => $sub_txt,
				'ajax_submit' => true,
			),
			'fields'        => $fields,
			'notifications' => $notifications,
			'confirmations' => $confirmations,
		);

		$new_id = FormModel::create( $title, $schema );
		return array(
			'success' => true,
			'form_id' => $new_id,
		);
	}
}
