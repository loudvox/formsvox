<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Checkboxes extends BaseField {
	public function get_type() {
		return 'checkbox';
	}

	public function get_title() {
		return __( 'Checkboxes', 'formsvox' );
	}

	public function sanitize( $value, $field ) {
		if ( is_array( $value ) ) {
			return array_map( 'sanitize_text_field', $value );
		}
		return array( sanitize_text_field( (string) $value ) );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$options  = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();
		$vals     = is_array( $value ) ? $value : (array) $value;

		$opts_html = '';
		foreach ( $options as $idx => $opt ) {
			$opt_val   = is_array( $opt ) ? $opt['value'] : $opt;
			$opt_label = is_array( $opt ) ? $opt['label'] : $opt;
			$checked   = in_array( $opt_val, $vals, true ) ? 'checked' : '';
			$opt_id    = "formsvox-input-{$field_id}-{$idx}";

			$opts_html .= sprintf(
				'<div class="formsvox-choice-item">
					<input type="checkbox" id="%s" name="formsvox_fields[%s][]" value="%s" %s class="formsvox-checkbox" />
					<label for="%s">%s</label>
				</div>',
				esc_attr( $opt_id ),
				$field_id,
				esc_attr( $opt_val ),
				$checked,
				esc_attr( $opt_id ),
				esc_html( $opt_label )
			);
		}

		return sprintf(
			'<div class="formsvox-field formsvox-field-checkbox %s" data-field-id="%s">
				<fieldset><legend class="formsvox-field-label">%s %s</legend>
				<div class="formsvox-choice-list">%s</div></fieldset>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formsvox-required-asterisk">*</span>' : '',
			$opts_html
		);
	}
}
