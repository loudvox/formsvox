<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Name extends BaseField {
	public function get_type() {
		return 'name';
	}

	public function get_title() {
		return __( 'Name', 'formsvox' );
	}

	public function sanitize( $value, $field ) {
		if ( is_array( $value ) ) {
			return array(
				'first' => sanitize_text_field( isset( $value['first'] ) ? $value['first'] : '' ),
				'last'  => sanitize_text_field( isset( $value['last'] ) ? $value['last'] : '' ),
			);
		}
		return sanitize_text_field( (string) $value );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$first    = esc_attr( isset( $value['first'] ) ? $value['first'] : '' );
		$last     = esc_attr( isset( $value['last'] ) ? $value['last'] : '' );

		return sprintf(
			'<div class="formsvox-field formsvox-field-name %s" data-field-id="%s">
				<label class="formsvox-field-label">%s %s</label>
				<div class="formsvox-field-group formsvox-grid-2">
					<div>
						<label for="formsvox-input-%s-first" class="formsvox-sub-label">%s</label>
						<input type="text" id="formsvox-input-%s-first" name="formsvox_fields[%s][first]" value="%s" class="formsvox-input" />
					</div>
					<div>
						<label for="formsvox-input-%s-last" class="formsvox-sub-label">%s</label>
						<input type="text" id="formsvox-input-%s-last" name="formsvox_fields[%s][last]" value="%s" class="formsvox-input" />
					</div>
				</div>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formsvox-required-asterisk">*</span>' : '',
			$field_id,
			__( 'First', 'formsvox' ),
			$field_id,
			$field_id,
			$first,
			$field_id,
			__( 'Last', 'formsvox' ),
			$field_id,
			$field_id,
			$last
		);
	}
}
