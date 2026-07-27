<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Radio extends BaseField {
	public function get_type() {
		return 'radio';
	}

	public function get_title() {
		return __( 'Multiple Choice', 'formsvox' );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$options  = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();
		$val      = is_null( $value ) ? ( isset( $field['default_val'] ) ? $field['default_val'] : '' ) : $value;

		$opts_html = '';
		foreach ( $options as $idx => $opt ) {
			$opt_val   = is_array( $opt ) ? $opt['value'] : $opt;
			$opt_label = is_array( $opt ) ? $opt['label'] : $opt;
			$checked   = checked( $val, $opt_val, false );
			$opt_id    = "formsvox-input-{$field_id}-{$idx}";

			$opts_html .= sprintf(
				'<div class="formsvox-choice-item">
					<input type="radio" id="%s" name="formsvox_fields[%s]" value="%s" %s class="formsvox-radio" />
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
			'<div class="formsvox-field formsvox-field-radio %s" data-field-id="%s">
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
