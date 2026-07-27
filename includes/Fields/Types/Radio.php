<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Radio extends BaseField {
	public function get_type() {
		return 'radio';
	}

	public function get_title() {
		return __( 'Multiple Choice', 'formvox' );
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
			$opt_id    = "formvox-input-{$field_id}-{$idx}";

			$opts_html .= sprintf(
				'<div class="formvox-choice-item">
					<input type="radio" id="%s" name="formvox_fields[%s]" value="%s" %s class="formvox-radio" />
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
			'<div class="formvox-field formvox-field-radio %s" data-field-id="%s">
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
