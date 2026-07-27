<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Address extends BaseField {
	public function get_type() {
		return 'address';
	}

	public function get_title() {
		return __( 'Address', 'formvox' );
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
			'<div class="formvox-field formvox-field-address %s" data-field-id="%s">
				<label class="formvox-field-label">%s %s</label>
				<div class="formvox-field-group">
					<div class="formvox-mb-2">
						<label for="formvox-input-%s-a1" class="formvox-sub-label">%s</label>
						<input type="text" id="formvox-input-%s-a1" name="formvox_fields[%s][address1]" value="%s" class="formvox-input" />
					</div>
					<div class="formvox-mb-2">
						<label for="formvox-input-%s-a2" class="formvox-sub-label">%s</label>
						<input type="text" id="formvox-input-%s-a2" name="formvox_fields[%s][address2]" value="%s" class="formvox-input" />
					</div>
					<div class="formvox-grid-2 formvox-mb-2">
						<div>
							<label for="formvox-input-%s-ct" class="formvox-sub-label">%s</label>
							<input type="text" id="formvox-input-%s-ct" name="formvox_fields[%s][city]" value="%s" class="formvox-input" />
						</div>
						<div>
							<label for="formvox-input-%s-st" class="formvox-sub-label">%s</label>
							<input type="text" id="formvox-input-%s-st" name="formvox_fields[%s][state]" value="%s" class="formvox-input" />
						</div>
					</div>
					<div class="formvox-grid-2">
						<div>
							<label for="formvox-input-%s-zp" class="formvox-sub-label">%s</label>
							<input type="text" id="formvox-input-%s-zp" name="formvox_fields[%s][postal]" value="%s" class="formvox-input" />
						</div>
						<div>
							<label for="formvox-input-%s-cn" class="formvox-sub-label">%s</label>
							<input type="text" id="formvox-input-%s-cn" name="formvox_fields[%s][country]" value="%s" class="formvox-input" />
						</div>
					</div>
				</div>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formvox-required-asterisk">*</span>' : '',
			$field_id, __( 'Street Address', 'formvox' ), $field_id, $field_id, $a1,
			$field_id, __( 'Address Line 2', 'formvox' ), $field_id, $field_id, $a2,
			$field_id, __( 'City', 'formvox' ), $field_id, $field_id, $ct,
			$field_id, __( 'State / Province', 'formvox' ), $field_id, $field_id, $st,
			$field_id, __( 'Postal / Zip Code', 'formvox' ), $field_id, $field_id, $zp,
			$field_id, __( 'Country', 'formvox' ), $field_id, $field_id, $cn
		);
	}
}
