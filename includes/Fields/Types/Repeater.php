<?php

namespace FormsVox\Fields\Types;

use FormsVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Repeater extends BaseField {
	public function get_type() {
		return 'repeater';
	}

	public function get_title() {
		return __( 'Repeater Field', 'formsvox' );
	}

	public function get_category() {
		return 'fancy';
	}

	public function sanitize( $value, $field ) {
		if ( is_array( $value ) ) {
			$sanitized = array();
			foreach ( $value as $row_idx => $row_data ) {
				if ( is_array( $row_data ) ) {
					$sanitized[ $row_idx ] = array_map( 'sanitize_text_field', $row_data );
				}
			}
			return $sanitized;
		}
		return array();
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id   = esc_attr( $field['id'] );
		$label      = esc_html( $field['label'] );
		$sub_fields = isset( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ? $field['sub_fields'] : array(
			array( 'id' => 'item_name', 'label' => __( 'Item Name', 'formsvox' ) ),
		);
		$rows       = is_array( $value ) && ! empty( $value ) ? $value : array( array() );

		$rows_html = '';
		foreach ( $rows as $r_idx => $row_data ) {
			$sub_html = '';
			foreach ( $sub_fields as $sub ) {
				$sub_id   = esc_attr( $sub['id'] );
				$sub_lbl  = esc_html( $sub['label'] );
				$sub_val  = esc_attr( isset( $row_data[ $sub_id ] ) ? $row_data[ $sub_id ] : '' );
				$sub_html .= sprintf(
					'<div>
						<label class="formsvox-sub-label">%s</label>
						<input type="text" name="formsvox_fields[%s][%d][%s]" value="%s" class="formsvox-input" />
					</div>',
					$sub_lbl,
					$field_id,
					$r_idx,
					$sub_id,
					$sub_val
				);
			}

			$rows_html .= sprintf(
				'<div class="formsvox-repeater-row formsvox-grid-2 formsvox-mb-2">
					%s
					<button type="button" class="formsvox-btn-remove-row button">&times;</button>
				</div>',
				$sub_html
			);
		}

		return sprintf(
			'<div class="formsvox-field formsvox-field-repeater %s" data-field-id="%s">
				<label class="formsvox-field-label">%s %s</label>
				<div class="formsvox-repeater-container">%s</div>
				<button type="button" class="formsvox-btn-add-row button">%s</button>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formsvox-required-asterisk">*</span>' : '',
			$rows_html,
			__( '+ Add Item', 'formsvox' )
		);
	}
}
