<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class URL extends BaseField {
	public function get_type() {
		return 'url';
	}

	public function get_title() {
		return __( 'Website / URL', 'formsvox' );
	}

	public function sanitize( $value, $field ) {
		return esc_url_raw( (string) $value );
	}

	public function validate( $value, $field, $form = array() ) {
		$parent_valid = parent::validate( $value, $field, $form );
		if ( is_wp_error( $parent_valid ) ) {
			return $parent_valid;
		}

		if ( ! empty( $value ) && false === filter_var( $value, FILTER_VALIDATE_URL ) ) {
			/* translators: %s: Field label */
			return new \WP_Error( 'invalid_url', sprintf( __( 'Please enter a valid URL for %s.', 'formsvox' ), esc_html( $field['label'] ) ) );
		}

		return true;
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id    = esc_attr( $field['id'] );
		$label       = esc_html( $field['label'] );
		$val         = esc_url( is_null( $value ) ? ( isset( $field['default_val'] ) ? $field['default_val'] : '' ) : $value );
		$placeholder = esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : 'https://example.com' );
		$required    = ! empty( $field['required'] ) ? 'required aria-required="true"' : '';
		$desc        = ! empty( $field['description'] ) ? '<span class="formsvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		return sprintf(
			'<div class="formsvox-field formsvox-field-url %s" data-field-id="%s">
				<label for="formsvox-input-%s" class="formsvox-field-label">%s %s</label>
				<input type="url" id="formsvox-input-%s" name="formsvox_fields[%s]" value="%s" placeholder="%s" class="formsvox-input" %s />
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
