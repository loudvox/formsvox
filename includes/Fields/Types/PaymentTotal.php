<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PaymentTotal extends BaseField {
	public function get_type() {
		return 'payment_total';
	}

	public function get_title() {
		return __( 'Total Price Display', 'formsvox' );
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

		return sprintf(
			'<div class="formsvox-field formsvox-field-payment-total %s" data-field-id="%s">
				<label class="formsvox-field-label">%s: $<span class="formsvox-total-amount">0.00</span></label>
				<input type="hidden" name="formsvox_fields[%s]" class="formsvox-total-input" value="0.00" />
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			$field_id
		);
	}
}
