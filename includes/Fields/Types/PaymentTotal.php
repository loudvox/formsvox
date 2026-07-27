<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PaymentTotal extends BaseField {
	public function get_type() {
		return 'payment_total';
	}

	public function get_title() {
		return __( 'Total Price Display', 'formvox' );
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
			'<div class="formvox-field formvox-field-payment-total %s" data-field-id="%s">
				<label class="formvox-field-label">%s: $<span class="formvox-total-amount">0.00</span></label>
				<input type="hidden" name="formvox_fields[%s]" class="formvox-total-input" value="0.00" />
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			$field_id
		);
	}
}
