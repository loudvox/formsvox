<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Textarea extends BaseField {
	public function get_type() {
		return 'textarea';
	}

	public function get_title() {
		return __( 'Paragraph Text', 'formvox' );
	}

	public function sanitize( $value, $field ) {
		return sanitize_textarea_field( (string) $value );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id    = esc_attr( $field['id'] );
		$label       = esc_html( $field['label'] );
		$val         = esc_textarea( is_null( $value ) ? ( isset( $field['default_val'] ) ? $field['default_val'] : '' ) : $value );
		$placeholder = esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' );
		$required    = ! empty( $field['required'] ) ? 'required aria-required="true"' : '';
		$desc        = ! empty( $field['description'] ) ? '<span class="formvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		return sprintf(
			'<div class="formvox-field formvox-field-textarea %s" data-field-id="%s">
				<label for="formvox-input-%s" class="formvox-field-label">%s %s</label>
				<textarea id="formvox-input-%s" name="formvox_fields[%s]" placeholder="%s" class="formvox-textarea" rows="5" %s>%s</textarea>
				%s
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formvox-required-asterisk">*</span>' : '',
			$field_id,
			$field_id,
			$placeholder,
			$required,
			$val,
			$desc
		);
	}
}
