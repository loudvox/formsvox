<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Phone extends BaseField {
	public function get_type() {
		return 'phone';
	}

	public function get_title() {
		return __( 'Phone', 'formsvox' );
	}

	public function validate( $value, $field, $form = array() ) {
		$parent_valid = parent::validate( $value, $field, $form );
		if ( is_wp_error( $parent_valid ) ) {
			return $parent_valid;
		}

		if ( ! empty( $value ) ) {
			// Validate E.164 or common international phone number format.
			if ( ! preg_match( '/^\+?[0-9\s\-\(\)\.]{7,20}$/', (string) $value ) ) {
				/* translators: %s: Field label */
				return new \WP_Error( 'invalid_phone', sprintf( __( 'Please enter a valid phone number for %s.', 'formsvox' ), esc_html( $field['label'] ) ) );
			}
		}

		return true;
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id    = esc_attr( $field['id'] );
		$label       = esc_html( $field['label'] );
		$val         = esc_attr( is_null( $value ) ? ( isset( $field['default_val'] ) ? $field['default_val'] : '' ) : $value );
		$placeholder = esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '+1 (555) 000-0000' );
		$required    = ! empty( $field['required'] ) ? 'required aria-required="true"' : '';
		$desc        = ! empty( $field['description'] ) ? '<span class="formsvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		return sprintf(
			'<div class="formsvox-field formsvox-field-phone %s" data-field-id="%s">
				<label for="formsvox-input-%s" class="formsvox-field-label">%s %s</label>
				<input type="tel" id="formsvox-input-%s" name="formsvox_fields[%s]" value="%s" placeholder="%s" class="formsvox-input" %s />
				%s
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formsvox-required-asterisk">*</span>' : '',
			$field_id,
			$field_id,
			$val,
			$placeholder,
			$required,
			$desc
		);
	}
}
