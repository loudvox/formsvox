<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dropdown extends BaseField {
	public function get_type() {
		return 'select';
	}

	public function get_title() {
		return __( 'Dropdown', 'formsvox' );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$options  = isset( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();
		$val      = is_null( $value ) ? ( isset( $field['default_val'] ) ? $field['default_val'] : '' ) : $value;
		$required = ! empty( $field['required'] ) ? 'required aria-required="true"' : '';
		$desc     = ! empty( $field['description'] ) ? '<span class="formsvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		$opt_html = '<option value="">' . esc_html__( '--- Select ---', 'formsvox' ) . '</option>';
		foreach ( $options as $opt ) {
			$opt_val   = is_array( $opt ) ? $opt['value'] : $opt;
			$opt_label = is_array( $opt ) ? $opt['label'] : $opt;
			$selected  = selected( $val, $opt_val, false );
			$opt_html .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $opt_val ), $selected, esc_html( $opt_label ) );
		}

		return sprintf(
			'<div class="formsvox-field formsvox-field-select %s" data-field-id="%s">
				<label for="formsvox-input-%s" class="formsvox-field-label">%s %s</label>
				<select id="formsvox-input-%s" name="formsvox_fields[%s]" class="formsvox-select" %s>%s</select>
				%s
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formsvox-required-asterisk">*</span>' : '',
			$field_id,
			$field_id,
			$required,
			$opt_html,
			$desc
		);
	}
}
