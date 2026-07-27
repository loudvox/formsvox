<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Password extends BaseField {
	public function get_type() {
		return 'password';
	}

	public function get_title() {
		return __( 'Password', 'formvox' );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id    = esc_attr( $field['id'] );
		$label       = esc_html( $field['label'] );
		$placeholder = esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' );
		$required    = ! empty( $field['required'] ) ? 'required aria-required="true"' : '';
		$desc        = ! empty( $field['description'] ) ? '<span class="formvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		return sprintf(
			'<div class="formvox-field formvox-field-password %s" data-field-id="%s">
				<label for="formvox-input-%s" class="formvox-field-label">%s %s</label>
				<input type="password" id="formvox-input-%s" name="formvox_fields[%s]" placeholder="%s" class="formvox-input" %s />
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
			$desc
		);
	}
}
