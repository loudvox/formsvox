<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Rating extends BaseField {
	public function get_type() {
		return 'rating';
	}

	public function get_title() {
		return __( 'Star Rating', 'formvox' );
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
		$val      = intval( $value );
		$max      = isset( $field['max_stars'] ) ? intval( $field['max_stars'] ) : 5;

		$stars_html = '';
		for ( $i = 1; $i <= $max; $i++ ) {
			$checked = checked( $val, $i, false );
			$opt_id  = "formvox-rating-{$field_id}-{$i}";
			$stars_html .= sprintf(
				'<input type="radio" id="%s" name="formvox_fields[%s]" value="%d" %s class="formvox-star-input" />
				<label for="%s" title="%d Stars">&#9733;</label>',
				esc_attr( $opt_id ),
				$field_id,
				$i,
				$checked,
				esc_attr( $opt_id ),
				$i
			);
		}

		return sprintf(
			'<div class="formvox-field formvox-field-rating %s" data-field-id="%s">
				<fieldset><legend class="formvox-field-label">%s %s</legend>
				<div class="formvox-star-rating">%s</div></fieldset>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formvox-required-asterisk">*</span>' : '',
			$stars_html
		);
	}
}
