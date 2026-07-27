<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PaymentSingle extends BaseField {
	public function get_type() {
		return 'payment_single';
	}

	public function get_title() {
		return __( 'Single Item Payment', 'formsvox' );
	}

	public function get_category() {
		return 'payment';
	}

	public function sanitize( $value, $field ) {
		return is_numeric( $value ) ? (float) $value : 0.00;
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$price    = isset( $field['price'] ) ? number_format( (float) $field['price'], 2 ) : '10.00';
		$desc     = ! empty( $field['description'] ) ? '<span class="formsvox-field-description">' . esc_html( $field['description'] ) . '</span>' : '';

		return sprintf(
			'<div class="formsvox-field formsvox-field-payment-single %s" data-field-id="%s" data-price="%s">
				<label class="formsvox-field-label">%s — $<span class="formsvox-price-display">%s</span></label>
				<input type="hidden" name="formsvox_fields[%s]" value="%s" />
				%s
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$price,
			$label,
			$price,
			$field_id,
			$price,
			$desc
		);
	}
}
