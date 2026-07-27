<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FileUpload extends BaseField {
	public function get_type() {
		return 'file_upload';
	}

	public function get_title() {
		return __( 'File Upload', 'formsvox' );
	}

	public function validate( $value, $field, $form = array() ) {
		$field_id = $field['id'];
		$is_required = ! empty( $field['required'] );

		if ( empty( $_FILES['formsvox_fields']['name'][ $field_id ] ) ) {
			if ( $is_required ) {
				/* translators: %s: Field label */
				return new \WP_Error( 'required_file', sprintf( __( '%s file is required.', 'formsvox' ), esc_html( $field['label'] ) ) );
			}
			return true;
		}

		$file_name = $_FILES['formsvox_fields']['name'][ $field_id ];
		$ext       = strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) );
		$allowed   = array( 'jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'zip', 'txt', 'csv' );

		if ( ! empty( $field['allowed_extensions'] ) && is_array( $field['allowed_extensions'] ) ) {
			$allowed = array_map( 'strtolower', $field['allowed_extensions'] );
		}

		if ( ! in_array( $ext, $allowed, true ) ) {
			/* translators: %s: Field label */
			return new \WP_Error( 'disallowed_file_type', sprintf( __( 'File type .%s is not allowed for %s.', 'formsvox' ), $ext, esc_html( $field['label'] ) ) );
		}

		return true;
	}

	public function sanitize( $value, $field ) {
		$field_id = $field['id'];
		if ( empty( $_FILES['formsvox_fields']['name'][ $field_id ] ) ) {
			return '';
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$upload_dir = wp_upload_dir();
		$target_dir = $upload_dir['basedir'] . '/formsvox_uploads';
		if ( ! file_exists( $target_dir ) ) {
			wp_mkdir_p( $target_dir );
			file_put_contents( $target_dir . '/.htaccess', 'Deny from all' );
		}

		$file = array(
			'name'     => $_FILES['formsvox_fields']['name'][ $field_id ],
			'type'     => $_FILES['formsvox_fields']['type'][ $field_id ],
			'tmp_name' => $_FILES['formsvox_fields']['tmp_name'][ $field_id ],
			'error'    => $_FILES['formsvox_fields']['error'][ $field_id ],
			'size'     => $_FILES['formsvox_fields']['size'][ $field_id ],
		);

		$moved = wp_handle_upload( $file, array( 'test_form' => false ) );
		if ( isset( $moved['url'] ) ) {
			return $moved['url'];
		}

		return '';
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$required = ! empty( $field['required'] ) ? 'required aria-required="true"' : '';
		$desc     = ! empty( $field['description'] ) ? '<span class="formsvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		return sprintf(
			'<div class="formsvox-field formsvox-field-file %s" data-field-id="%s">
				<label for="formsvox-input-%s" class="formsvox-field-label">%s %s</label>
				<input type="file" id="formsvox-input-%s" name="formsvox_fields[%s]" class="formsvox-file-input" %s />
				%s
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formsvox-required-asterisk">*</span>' : '',
			$field_id,
			$field_id,
			$required,
			$desc
		);
	}
}
