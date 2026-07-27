<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Layout extends BaseField {
	public function get_type() {
		return 'layout';
	}

	public function get_title() {
		return __( 'Layout / Columns', 'formvox' );
	}

	public function get_category() {
		return 'layout';
	}

	public function render( $field, $value = null, $form = array() ) {
		$columns = isset( $field['columns'] ) ? intval( $field['columns'] ) : 2;
		return sprintf(
			'<div class="formvox-field formvox-field-layout formvox-grid-%d %s" data-field-id="%s"></div>',
			$columns,
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			esc_attr( $field['id'] )
		);
	}
}
