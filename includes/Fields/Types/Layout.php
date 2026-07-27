<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Layout extends BaseField {
	public function get_type() {
		return 'layout';
	}

	public function get_title() {
		return __( 'Layout / Columns', 'formsvox' );
	}

	public function get_category() {
		return 'layout';
	}

	public function render( $field, $value = null, $form = array() ) {
		$columns = isset( $field['columns'] ) ? intval( $field['columns'] ) : 2;
		return sprintf(
			'<div class="formsvox-field formsvox-field-layout formsvox-grid-%d %s" data-field-id="%s"></div>',
			$columns,
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			esc_attr( $field['id'] )
		);
	}
}
