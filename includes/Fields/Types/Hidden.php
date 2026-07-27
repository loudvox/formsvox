<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hidden extends BaseField {
	public function get_type() {
		return 'hidden';
	}

	public function get_title() {
		return __( 'Hidden Field', 'formvox' );
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$val      = esc_attr( is_null( $value ) ? ( isset( $field['default_val'] ) ? $field['default_val'] : '' ) : $value );

		return sprintf(
			'<input type="hidden" id="formvox-input-%s" name="formvox_fields[%s]" value="%s" data-field-id="%s" />',
			$field_id,
			$field_id,
			$val,
			$field_id
		);
	}
}
