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
		if ( empty( $logic ) || empty( $logic['enabled'] ) || empty( $logic['rules'] ) || ! is_array( $logic['rules'] ) ) {
			return true;
		}

		$match_all = isset( $logic['match'] ) && 'all' === $logic['match'];
		$rules     = $logic['rules'];
		$action    = isset( $logic['action'] ) ? $logic['action'] : 'show';

		$results = array();
		foreach ( $rules as $rule ) {
			$target_id  = isset( $rule['field_id'] ) ? $rule['field_id'] : '';
			$operator   = isset( $rule['operator'] ) ? $rule['operator'] : 'equals';
			$target_val = isset( $rule['value'] ) ? $rule['value'] : '';

			$actual_val = isset( $fields[ $target_id ] ) ? $fields[ $target_id ] : null;
			$is_match   = false;

			switch ( $operator ) {
				case 'equals':
					if ( is_array( $actual_val ) ) {
						$is_match = in_array( (string) $target_val, array_map( 'strval', $actual_val ), true );
					} else {
						$is_match = ( (string) $actual_val === (string) $target_val );
					}
					break;

				case 'not_equals':
					if ( is_array( $actual_val ) ) {
						$is_match = ! in_array( (string) $target_val, array_map( 'strval', $actual_val ), true );
					} else {
						$is_match = ( (string) $actual_val !== (string) $target_val );
					}
					break;

				case 'contains':
					if ( is_array( $actual_val ) ) {
						$is_match = in_array( (string) $target_val, array_map( 'strval', $actual_val ), true );
					} else {
						$is_match = ( false !== strpos( (string) $actual_val, (string) $target_val ) );
					}
					break;

				case 'greater_than':
					$is_match = is_numeric( $actual_val ) && is_numeric( $target_val ) && ( (float) $actual_val > (float) $target_val );
					break;

				case 'less_than':
					$is_match = is_numeric( $actual_val ) && is_numeric( $target_val ) && ( (float) $actual_val < (float) $target_val );
					break;

				case 'empty':
					$is_match = self::is_value_empty( $actual_val );
					break;

				case 'not_empty':
					$is_match = ! self::is_value_empty( $actual_val );
					break;
			}

			$results[] = $is_match;
		}

		$condition_passed = $match_all ? ! in_array( false, $results, true ) : in_array( true, $results, true );

		return 'show' === $action ? $condition_passed : ! $condition_passed;
	}

	/**
	 * Helper to check if a value is truly empty ("0" and 0 are considered NON-empty).
	 *
	 * @param mixed $val Value to test.
	 * @return bool
	 */
	public static function is_value_empty( $val ) {
		if ( null === $val || false === $val || '' === $val ) {
			return true;
		}
		if ( is_array( $val ) ) {
			$filtered = array_filter( $val, function( $item ) {
				return null !== $item && '' !== $item;
			} );
			return empty( $filtered );
		}
		return false;
	}
}
