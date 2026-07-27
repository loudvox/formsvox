<?php

namespace FormVox\Logic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Conditional Logic Evaluator Engine.
 */
class Evaluator {
	/**
	 * Evaluate logic object against submitted fields.
	 *
	 * @param array $logic  Conditional logic configuration.
	 * @param array $fields Submitted fields (field_id => value).
	 * @return bool True if condition passes (visible/triggered), false otherwise.
	 */
	public static function evaluate( $logic, $fields ) {
		if ( empty( $logic ) || empty( $logic['enabled'] ) || empty( $logic['rules'] ) ) {
			return true;
		}

		$match_all = isset( $logic['match'] ) && 'all' === $logic['match'];
		$rules     = $logic['rules'];
		$action    = isset( $logic['action'] ) ? $logic['action'] : 'show';

		$results = array();
		foreach ( $rules as $rule ) {
			$target_id = isset( $rule['field_id'] ) ? $rule['field_id'] : '';
			$operator  = isset( $rule['operator'] ) ? $rule['operator'] : 'equals';
			$target_val = isset( $rule['value'] ) ? $rule['value'] : '';

			$actual_val = isset( $fields[ $target_id ] ) ? $fields[ $target_id ] : '';
			$is_match   = false;

			switch ( $operator ) {
				case 'equals':
					$is_match = ( (string) $actual_val === (string) $target_val );
					break;
				case 'not_equals':
					$is_match = ( (string) $actual_val !== (string) $target_val );
					break;
				case 'contains':
					$is_match = ( false !== strpos( (string) $actual_val, (string) $target_val ) );
					break;
				case 'empty':
					$is_match = empty( $actual_val );
					break;
				case 'not_empty':
					$is_match = ! empty( $actual_val );
					break;
			}

			$results[] = $is_match;
		}

		$condition_passed = $match_all ? ! in_array( false, $results, true ) : in_array( true, $results, true );

		return 'show' === $action ? $condition_passed : ! $condition_passed;
	}
}
