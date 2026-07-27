<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dropdown extends BaseField {
	public function get_type() {
		return 'select';
	}

	public function get_title() {
		return __( 'Dropdown', 'formvox' );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$options  = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();
		$val      = is_null( $value ) ? ( isset( $field['default_val'] ) ? $field['default_val'] : '' ) : $value;
		$required = ! empty( $field['required'] ) ? 'required aria-required="true"' : '';
		$desc     = ! empty( $field['description'] ) ? '<span class="formvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		$opt_html = '<option value="">' . esc_html__( '--- Select ---', 'formvox' ) . '</option>';
		foreach ( $options as $opt ) {
			$opt_val   = is_array( $opt ) ? $opt['value'] : $opt;
			$opt_label = is_array( $opt ) ? $opt['label'] : $opt;
			$selected  = selected( $val, $opt_val, false );
			$opt_html .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $opt_val ), $selected, esc_html( $opt_label ) );
		}

		return sprintf(
			'<div class="formvox-field formvox-field-select %s" data-field-id="%s">
				<label for="formvox-input-%s" class="formvox-field-label">%s %s</label>
				<select id="formvox-input-%s" name="formvox_fields[%s]" class="formvox-select" %s>%s</select>
				%s
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formvox-required-asterisk">*</span>' : '',
			$field_id,
			$field_id,
			$required,
			$opt_html,
			$desc
		);
	}
}
