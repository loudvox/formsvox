<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Email extends BaseField {
	public function get_type() {
		return 'email';
	}

	public function get_title() {
		return __( 'Email', 'formvox' );
	}

	public function sanitize( $value, $field ) {
		return sanitize_email( (string) $value );
	}

	public function validate( $value, $field, $form = array() ) {
		$parent_valid = parent::validate( $value, $field, $form );
		if ( is_wp_error( $parent_valid ) ) {
			return $parent_valid;
		}

		if ( ! empty( $value ) && ! is_email( $value ) ) {
			/* translators: %s: Field label */
			return new \WP_Error( 'invalid_email', sprintf( __( 'Please enter a valid email address for %s.', 'formvox' ), esc_html( $field['label'] ) ) );
		}

		return true;
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id    = esc_attr( $field['id'] );
		$label       = esc_html( $field['label'] );
		$val         = esc_attr( is_null( $value ) ? ( isset( $field['default_val'] ) ? $field['default_val'] : '' ) : $value );
		$placeholder = esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' );
		$required    = ! empty( $field['required'] ) ? 'required aria-required="true"' : '';
		$desc        = ! empty( $field['description'] ) ? '<span class="formvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		return sprintf(
			'<div class="formvox-field formvox-field-email %s" data-field-id="%s">
				<label for="formvox-input-%s" class="formvox-field-label">%s %s</label>
				<input type="email" id="formvox-input-%s" name="formvox_fields[%s]" value="%s" placeholder="%s" class="formvox-input" %s />
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
