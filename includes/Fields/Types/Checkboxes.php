<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Checkboxes extends BaseField {
	public function get_type() {
		return 'checkbox';
	}

	public function get_title() {
		return __( 'Checkboxes', 'formvox' );
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
			$opt_id    = "formvox-input-{$field_id}-{$idx}";

			$opts_html .= sprintf(
				'<div class="formvox-choice-item">
					<input type="checkbox" id="%s" name="formvox_fields[%s][]" value="%s" %s class="formvox-checkbox" />
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
			'<div class="formvox-field formvox-field-checkbox %s" data-field-id="%s">
				<fieldset><legend class="formvox-field-label">%s %s</legend>
				<div class="formvox-choice-list">%s</div></fieldset>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formvox-required-asterisk">*</span>' : '',
			$opts_html
		);
	}
}
