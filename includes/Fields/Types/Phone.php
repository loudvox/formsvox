<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Phone extends BaseField {
	public function get_type() {
		return 'phone';
	}

	public function get_title() {
		return __( 'Phone', 'formvox' );
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
				return new \WP_Error( 'invalid_phone', sprintf( __( 'Please enter a valid phone number for %s.', 'formvox' ), esc_html( $field['label'] ) ) );
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
		$desc        = ! empty( $field['description'] ) ? '<span class="formvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		return sprintf(
			'<div class="formvox-field formvox-field-phone %s" data-field-id="%s">
				<label for="formvox-input-%s" class="formvox-field-label">%s %s</label>
				<input type="tel" id="formvox-input-%s" name="formvox_fields[%s]" value="%s" placeholder="%s" class="formvox-input" %s />
				%s
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formvox-required-asterisk">*</span>' : '',
			$field_id,
			$field_id,
			$val,
			$placeholder,
			$required,
			$desc
		);
	}
}
