<?php

namespace FormVox\Fields\Types;

use FormVox\Fields\BaseField;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Likert extends BaseField {
	public function get_type() {
		return 'likert';
	}

	public function get_title() {
		return __( 'Likert Scale', 'formvox' );
	}

	public function get_category() {
		return 'fancy';
	}

	public function sanitize( $value, $field ) {
		if ( is_array( $value ) ) {
			$sanitized = array();
			foreach ( $value as $row => $col ) {
				$sanitized[ sanitize_text_field( $row ) ] = sanitize_text_field( $col );
			}
			return $sanitized;
		}
		return array();
	}

	public function render( $field, $value = null, $form = array() ) {
		$field_id = esc_attr( $field['id'] );
		$label    = esc_html( $field['label'] );
		$rows     = isset( $field['rows'] ) && is_array( $field['rows'] ) ? $field['rows'] : array( 'Statement 1', 'Statement 2' );
		$columns  = isset( $field['columns'] ) && is_array( $field['columns'] ) ? $field['columns'] : array( 'Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree' );
		$vals     = is_array( $value ) ? $value : array();

		$thead = '<tr><th></th>';
		foreach ( $columns as $col ) {
			$thead .= '<th>' . esc_html( $col ) . '</th>';
		}
		$thead .= '</tr>';

		$tbody = '';
		foreach ( $rows as $r_idx => $row_label ) {
			$r_key = "row_{$r_idx}";
			$tbody .= '<tr><td>' . esc_html( $row_label ) . '</td>';

			foreach ( $columns as $c_idx => $col_label ) {
				$c_key   = "col_{$c_idx}";
				$opt_id  = "formvox-likert-{$field_id}-{$r_idx}-{$c_idx}";
				$checked = isset( $vals[ $r_key ] ) && $vals[ $r_key ] === $c_key ? 'checked' : '';

				$tbody .= sprintf(
					'<td class="formvox-text-center">
						<input type="radio" id="%s" name="formvox_fields[%s][%s]" value="%s" %s />
					</td>',
					esc_attr( $opt_id ),
					$field_id,
					esc_attr( $r_key ),
					esc_attr( $c_key ),
					$checked
				);
			}
			$tbody .= '</tr>';
		}

		return sprintf(
			'<div class="formvox-field formvox-field-likert %s" data-field-id="%s">
				<label class="formvox-field-label">%s %s</label>
				<table class="formvox-likert-table"><thead>%s</thead><tbody>%s</tbody></table>
			</div>',
			esc_attr( isset( $field['css_class'] ) ? $field['css_class'] : '' ),
			$field_id,
			$label,
			! empty( $field['required'] ) ? '<span class="formvox-required-asterisk">*</span>' : '',
			$thead,
			$tbody
		);
	}
}
