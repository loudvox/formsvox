<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Number extends BaseField {
	public function get_type() {
		return 'number';
	}

	public function get_title() {
		return __( 'Number', 'formvox' );
	}

	public function sanitize( $value, $field ) {
		return is_numeric( $value ) ? (float) $value : '';
	}

	public function validate( $value, $field, $form = array() ) {
		$parent_valid = parent::validate( $value, $field, $form );
		if ( is_wp_error( $parent_valid ) ) {
			return $parent_valid;
		}

		if ( '' !== (string) $value && ! is_numeric( $value ) ) {
			/* translators: %s: Field label */
			return new \WP_Error( 'invalid_number', sprintf( __( '%s must be a valid number.', 'formvox' ), esc_html( $field['label'] ) ) );
		}

		return true;
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id    = esc_attr( $field['id'] );
		$label       = esc_html( $field['label'] );
		$val         = esc_attr( is_null( $value ) ? ( isset( $field['default_val'] ) ? $field['default_val'] : '' ) : $value );
		$placeholder = esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' );
		$required    = ! empty( $field['required'] ) ? 'required aria-required="true"' : '';
		$min         = isset( $field['min'] ) ? 'min="' . esc_attr( $field['min'] ) . '"' : '';
		$max         = isset( $field['max'] ) ? 'max="' . esc_attr( $field['max'] ) . '"' : '';
		$step        = isset( $field['step'] ) ? 'step="' . esc_attr( $field['step'] ) . '"' : '';
		$desc        = ! empty( $field['description'] ) ? '<span class="formvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		return sprintf(
			'<div class="formvox-field formvox-field-number %s" data-field-id="%s">
				<label for="formvox-input-%s" class="formvox-field-label">%s %s</label>
				<input type="number" id="formvox-input-%s" name="formvox_fields[%s]" value="%s" placeholder="%s" %s %s %s class="formvox-input" %s />
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
			$min,
			$max,
			$step,
			$required,
			$desc
		);
	}
}
