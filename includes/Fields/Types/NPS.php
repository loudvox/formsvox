<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NPS extends BaseField {
	public function get_type() {
		return 'nps';
	}

	public function get_title() {
		return __( 'Net Promoter Score (NPS)', 'formsvox' );
	}

	public function get_category() {
		return 'fancy';
	}

	public function sanitize( $value, $field ) {
		return intval( $value );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$val      = is_null( $value ) ? -1 : intval( $value );

		$buttons_html = '';
		for ( $i = 0; $i <= 10; $i++ ) {
			$opt_id  = "formsvox-nps-{$field_id}-{$i}";
			$checked = checked( $val, $i, false );
			$buttons_html .= sprintf(
				'<div class="formsvox-nps-item">
					<input type="radio" id="%s" name="formsvox_fields[%s]" value="%d" %s class="formsvox-nps-input" />
					<label for="%s">%d</label>
				</div>',
				esc_attr( $opt_id ),
				$field_id,
				$i,
				$checked,
				esc_attr( $opt_id ),
				$i
			);
		}

		return sprintf(
			'<div class="formsvox-field formsvox-field-nps %s" data-field-id="%s">
				<fieldset><legend class="formsvox-field-label">%s %s</legend>
				<div class="formsvox-nps-scale">%s</div>
				<div class="formsvox-nps-labels">
					<span>%s</span>
					<span>%s</span>
				</div></fieldset>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formsvox-required-asterisk">*</span>' : '',
			$buttons_html,
			__( '0 - Not likely at all', 'formsvox' ),
			__( '10 - Extremely likely', 'formsvox' )
		);
	}
}
