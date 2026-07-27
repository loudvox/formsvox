<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Section extends BaseField {
	public function get_type() {
		return 'section';
	}

	public function get_title() {
		return __( 'Section Divider', 'formsvox' );
	}

	public function get_category() {
		return 'layout';
	}

	public function render( $field, $value = null, $form = array() ) {
		$label = esc_html( $field['label'] );
		$desc  = ! empty( $field['description'] ) ? '<p class="formsvox-section-description">' . esc_html( $field['description'] ) . '</p>' : '';

		return sprintf(
			'<div class="formsvox-field formsvox-field-section %s" data-field-id="%s">
				<h3 class="formsvox-section-title">%s</h3>
				%s
				<hr class="formsvox-section-hr" />
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			esc_attr( $field['id'] ),
			$label,
			$desc
		);
	}
}
