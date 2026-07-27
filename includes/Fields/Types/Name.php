<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Name extends BaseField {
	public function get_type() {
		return 'name';
	}

	public function get_title() {
		return __( 'Name', 'formvox' );
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
			'<div class="formvox-field formvox-field-name %s" data-field-id="%s">
				<label class="formvox-field-label">%s %s</label>
				<div class="formvox-field-group formvox-grid-2">
					<div>
						<label for="formvox-input-%s-first" class="formvox-sub-label">%s</label>
						<input type="text" id="formvox-input-%s-first" name="formvox_fields[%s][first]" value="%s" class="formvox-input" />
					</div>
					<div>
						<label for="formvox-input-%s-last" class="formvox-sub-label">%s</label>
						<input type="text" id="formvox-input-%s-last" name="formvox_fields[%s][last]" value="%s" class="formvox-input" />
					</div>
				</div>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formvox-required-asterisk">*</span>' : '',
			$field_id,
			__( 'First', 'formvox' ),
			$field_id,
			$field_id,
			$first,
			$field_id,
			__( 'Last', 'formvox' ),
			$field_id,
			$field_id,
			$last
		);
	}
}
