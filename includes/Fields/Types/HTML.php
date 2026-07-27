<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HTML extends BaseField {
	public function get_type() {
		return 'html';
	}

	public function get_title() {
		return __( 'HTML / Content Block', 'formsvox' );
	}

	public function get_category() {
		return 'layout';
	}

	public function render( $field, $value = null, $form = array() ) {
		$content = isset( $field['content'] ) ? wp_kses_post( $field['content'] ) : '';
		return sprintf(
			'<div class="formsvox-field formsvox-field-html %s" data-field-id="%s">%s</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			esc_attr( $field['id'] ),
			$content
		);
	}
}
