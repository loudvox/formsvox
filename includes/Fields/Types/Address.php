<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Address extends BaseField {
	public function get_type() {
		return 'address';
	}

	public function get_title() {
		return __( 'Address', 'formsvox' );
	}

	public function sanitize( $value, $field ) {
		if ( is_array( $value ) ) {
			return array(
				'address1' => sanitize_text_field( isset( $value['address1'] ) ? $value['address1'] : '' ),
				'address2' => sanitize_text_field( isset( $value['address2'] ) ? $value['address2'] : '' ),
				'city'     => sanitize_text_field( isset( $value['city'] ) ? $value['city'] : '' ),
				'state'    => sanitize_text_field( isset( $value['state'] ) ? $value['state'] : '' ),
				'postal'   => sanitize_text_field( isset( $value['postal'] ) ? $value['postal'] : '' ),
				'country'  => sanitize_text_field( isset( $value['country'] ) ? $value['country'] : '' ),
			);
		}
		return sanitize_text_field( (string) $value );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );

		$val = is_array( $value ) ? $value : array();
		$a1  = esc_attr( isset( $val['address1'] ) ? $val['address1'] : '' );
		$a2  = esc_attr( isset( $val['address2'] ) ? $val['address2'] : '' );
		$ct  = esc_attr( isset( $val['city'] ) ? $val['city'] : '' );
		$st  = esc_attr( isset( $val['state'] ) ? $val['state'] : '' );
		$zp  = esc_attr( isset( $val['postal'] ) ? $val['postal'] : '' );
		$cn  = esc_attr( isset( $val['country'] ) ? $val['country'] : '' );

		return sprintf(
			'<div class="formsvox-field formsvox-field-address %s" data-field-id="%s">
				<label class="formsvox-field-label">%s %s</label>
				<div class="formsvox-field-group">
					<div class="formsvox-mb-2">
						<label for="formsvox-input-%s-a1" class="formsvox-sub-label">%s</label>
						<input type="text" id="formsvox-input-%s-a1" name="formsvox_fields[%s][address1]" value="%s" class="formsvox-input" />
					</div>
					<div class="formsvox-mb-2">
						<label for="formsvox-input-%s-a2" class="formsvox-sub-label">%s</label>
						<input type="text" id="formsvox-input-%s-a2" name="formsvox_fields[%s][address2]" value="%s" class="formsvox-input" />
					</div>
					<div class="formsvox-grid-2 formsvox-mb-2">
						<div>
							<label for="formsvox-input-%s-ct" class="formsvox-sub-label">%s</label>
							<input type="text" id="formsvox-input-%s-ct" name="formsvox_fields[%s][city]" value="%s" class="formsvox-input" />
						</div>
						<div>
							<label for="formsvox-input-%s-st" class="formsvox-sub-label">%s</label>
							<input type="text" id="formsvox-input-%s-st" name="formsvox_fields[%s][state]" value="%s" class="formsvox-input" />
						</div>
					</div>
					<div class="formsvox-grid-2">
						<div>
							<label for="formsvox-input-%s-zp" class="formsvox-sub-label">%s</label>
							<input type="text" id="formsvox-input-%s-zp" name="formsvox_fields[%s][postal]" value="%s" class="formsvox-input" />
						</div>
						<div>
							<label for="formsvox-input-%s-cn" class="formsvox-sub-label">%s</label>
							<input type="text" id="formsvox-input-%s-cn" name="formsvox_fields[%s][country]" value="%s" class="formsvox-input" />
						</div>
					</div>
				</div>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formsvox-required-asterisk">*</span>' : '',
			$field_id, __( 'Street Address', 'formsvox' ), $field_id, $field_id, $a1,
			$field_id, __( 'Address Line 2', 'formsvox' ), $field_id, $field_id, $a2,
			$field_id, __( 'City', 'formsvox' ), $field_id, $field_id, $ct,
			$field_id, __( 'State / Province', 'formsvox' ), $field_id, $field_id, $st,
			$field_id, __( 'Postal / Zip Code', 'formsvox' ), $field_id, $field_id, $zp,
			$field_id, __( 'Country', 'formsvox' ), $field_id, $field_id, $cn
		);
	}
}
