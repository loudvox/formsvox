<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PageBreak extends BaseField {
	public function get_type() {
		return 'page_break';
	}

	public function get_title() {
		return __( 'Page Break', 'formsvox' );
	}

	public function get_category() {
		return 'layout';
	}

	public function render( $field, $value = null, $form = array() ) {
		$title = esc_html( isset( $field['title'] ) ? $field['title'] : __( 'Next Page', 'formsvox' ) );
		return sprintf(
			'<div class="formsvox-page-break" data-field-id="%s" data-page-title="%s"></div>',
			esc_attr( $field['id'] ),
			$title
		);
	}
}
