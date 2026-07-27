<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DateTime extends BaseField {
	public function get_type() {
		return 'date_time';
	}

	public function get_title() {
		return __( 'Date / Time', 'formvox' );
	}

	public function sanitize( $value, $field ) {
		if ( is_array( $value ) ) {
			return array(
				'date' => sanitize_text_field( isset( $value['date'] ) ? $value['date'] : '' ),
				'time' => sanitize_text_field( isset( $value['time'] ) ? $value['time'] : '' ),
			);
		}
		return sanitize_text_field( (string) $value );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$val      = is_array( $value ) ? $value : array();
		$dt       = esc_attr( isset( $val['date'] ) ? $val['date'] : '' );
		$tm       = esc_attr( isset( $val['time'] ) ? $val['time'] : '' );

		return sprintf(
			'<div class="formvox-field formvox-field-datetime %s" data-field-id="%s">
				<label class="formvox-field-label">%s %s</label>
				<div class="formvox-grid-2">
					<div>
						<label for="formvox-input-%s-date" class="formvox-sub-label">%s</label>
						<input type="date" id="formvox-input-%s-date" name="formvox_fields[%s][date]" value="%s" class="formvox-input" />
					</div>
					<div>
						<label for="formvox-input-%s-time" class="formvox-sub-label">%s</label>
						<input type="time" id="formvox-input-%s-time" name="formvox_fields[%s][time]" value="%s" class="formvox-input" />
					</div>
				</div>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formvox-required-asterisk">*</span>' : '',
			$field_id, __( 'Date', 'formvox' ), $field_id, $field_id, $dt,
			$field_id, __( 'Time', 'formvox' ), $field_id, $field_id, $tm
		);
	}
}
