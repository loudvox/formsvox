<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HTML extends BaseField {
	public function get_type() {
		return 'html';
	}

	public function get_title() {
		return __( 'HTML / Content Block', 'formvox' );
	}

	public function get_category() {
		return 'layout';
	}

	public function render( $field, $value = null, $form = array() ) {
		$content = isset( $field['content'] ) ? wp_kses_post( $field['content'] ) : '';
		return sprintf(
			'<div class="formvox-field formvox-field-html %s" data-field-id="%s">%s</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			esc_attr( $field['id'] ),
			$content
		);
	}
}
