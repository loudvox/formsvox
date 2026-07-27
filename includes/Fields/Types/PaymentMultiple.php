<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PaymentMultiple extends BaseField {
	public function get_type() {
		return 'payment_multiple';
	}

	public function get_title() {
		return __( 'Multiple Items Payment', 'formvox' );
	}

	public function get_category() {
		return 'payment';
	}

	public function sanitize( $value, $field ) {
		return sanitize_text_field( (string) $value );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$items    = isset( $field['items'] ) && is_array( $field['items'] ) ? $field['items'] : array(
			array( 'label' => 'Item 1', 'price' => '15.00' ),
			array( 'label' => 'Item 2', 'price' => '25.00' ),
		);

		$opts_html = '';
		foreach ( $items as $idx => $item ) {
			$item_lbl = esc_html( $item['label'] );
			$item_prc = number_format( (float) $item['price'], 2 );
			$opt_id   = "formvox-pay-{$field_id}-{$idx}";
			$checked  = checked( $value, $item_lbl, false );

			$opts_html .= sprintf(
				'<div class="formvox-choice-item">
					<input type="radio" id="%s" name="formvox_fields[%s]" value="%s" data-price="%s" %s class="formvox-payment-radio" />
					<label for="%s">%s ($%s)</label>
				</div>',
				esc_attr( $opt_id ),
				$field_id,
				esc_attr( $item_lbl ),
				esc_attr( $item_prc ),
				$checked,
				esc_attr( $opt_id ),
				$item_lbl,
				$item_prc
			);
		}

		return sprintf(
			'<div class="formvox-field formvox-field-payment-multiple %s" data-field-id="%s">
				<fieldset><legend class="formvox-field-label">%s %s</legend>
				<div class="formvox-choice-list">%s</div></fieldset>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formvox-required-asterisk">*</span>' : '',
			$opts_html
		);
	}
}
