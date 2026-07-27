<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DateTime extends BaseField {
	public function get_type() {
		return 'date_time';
	}

	public function get_title() {
		return __( 'Date / Time', 'formsvox' );
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
			'<div class="formsvox-field formsvox-field-datetime %s" data-field-id="%s">
				<label class="formsvox-field-label">%s %s</label>
				<div class="formsvox-grid-2">
					<div>
						<label for="formsvox-input-%s-date" class="formsvox-sub-label">%s</label>
						<input type="date" id="formsvox-input-%s-date" name="formsvox_fields[%s][date]" value="%s" class="formsvox-input" />
					</div>
					<div>
						<label for="formsvox-input-%s-time" class="formsvox-sub-label">%s</label>
						<input type="time" id="formsvox-input-%s-time" name="formsvox_fields[%s][time]" value="%s" class="formsvox-input" />
					</div>
				</div>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formsvox-required-asterisk">*</span>' : '',
			$field_id, __( 'Date', 'formsvox' ), $field_id, $field_id, $dt,
			$field_id, __( 'Time', 'formsvox' ), $field_id, $field_id, $tm
		);
	}
}
