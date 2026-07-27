<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Slider extends BaseField {
	public function get_type() {
		return 'slider';
	}

	public function get_title() {
		return __( 'Number Slider', 'formvox' );
	}

	public function sanitize( $value, $field ) {
		return is_numeric( $value ) ? (float) $value : 0;
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$min      = isset( $field['min'] ) ? intval( $field['min'] ) : 0;
		$max      = isset( $field['max'] ) ? intval( $field['max'] ) : 100;
		$step     = isset( $field['step'] ) ? intval( $field['step'] ) : 1;
		$val      = is_null( $value ) ? ( isset( $field['default_val'] ) ? intval( $field['default_val'] ) : $min ) : intval( $value );

		return sprintf(
			'<div class="formvox-field formvox-field-slider %s" data-field-id="%s">
				<label for="formvox-input-%s" class="formvox-field-label">%s: <span class="formvox-slider-value">%d</span></label>
				<input type="range" id="formvox-input-%s" name="formvox_fields[%s]" value="%d" min="%d" max="%d" step="%d" class="formvox-slider" oninput="this.previousElementSibling.querySelector(\'.formvox-slider-value\').textContent=this.value" />
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$field_id,
			$label,
			$val,
			$field_id,
			$field_id,
			$val,
			$min,
			$max,
			$step
		);
	}
}
