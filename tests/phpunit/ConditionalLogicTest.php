<?php

namespace FormsVox\Tests;

use PHPUnit\Framework\TestCase;
use FormsVox\Logic\Evaluator;

class ConditionalLogicTest extends TestCase {
	public function test_equals_and_not_equals() {
		$logic = array(
			'enabled' => true,
			'action'  => 'show',
			'match'   => 'all',
			'rules'   => array(
				array( 'field_id' => 'select_1', 'operator' => 'equals', 'value' => 'yes' ),
			),
		);

		$this->assertTrue( Evaluator::evaluate( $logic, array( 'select_1' => 'yes' ) ) );
		$this->assertFalse( Evaluator::evaluate( $logic, array( 'select_1' => 'no' ) ) );
	}

	public function test_array_checkboxes_evaluation() {
		$logic = array(
			'enabled' => true,
			'action'  => 'show',
			'match'   => 'all',
			'rules'   => array(
				array( 'field_id' => 'check_1', 'operator' => 'equals', 'value' => 'option_2' ),
			),
		);

		$this->assertTrue( Evaluator::evaluate( $logic, array( 'check_1' => array( 'option_1', 'option_2' ) ) ) );
		$this->assertFalse( Evaluator::evaluate( $logic, array( 'check_1' => array( 'option_1', 'option_3' ) ) ) );
	}

	public function test_zero_is_treated_as_non_empty() {
		$logic_empty = array(
			'enabled' => true,
			'action'  => 'show',
			'match'   => 'all',
			'rules'   => array(
				array( 'field_id' => 'num_1', 'operator' => 'empty', 'value' => '' ),
			),
		);

		$logic_not_empty = array(
			'enabled' => true,
			'action'  => 'show',
			'match'   => 'all',
			'rules'   => array(
				array( 'field_id' => 'num_1', 'operator' => 'not_empty', 'value' => '' ),
			),
		);

		// "0" or 0 must be treated as NON-EMPTY
		$this->assertFalse( Evaluator::evaluate( $logic_empty, array( 'num_1' => '0' ) ) );
		$this->assertTrue( Evaluator::evaluate( $logic_not_empty, array( 'num_1' => '0' ) ) );
		$this->assertFalse( Evaluator::evaluate( $logic_empty, array( 'num_1' => 0 ) ) );
		$this->assertTrue( Evaluator::evaluate( $logic_not_empty, array( 'num_1' => 0 ) ) );
	}

	public function test_greater_than_and_less_than() {
		$logic_gt = array(
			'enabled' => true,
			'action'  => 'show',
			'match'   => 'all',
			'rules'   => array(
				array( 'field_id' => 'score', 'operator' => 'greater_than', 'value' => '50' ),
			),
		);

		$logic_lt = array(
			'enabled' => true,
			'action'  => 'show',
			'match'   => 'all',
			'rules'   => array(
				array( 'field_id' => 'score', 'operator' => 'less_than', 'value' => '50' ),
			),
		);

		$this->assertTrue( Evaluator::evaluate( $logic_gt, array( 'score' => '75' ) ) );
		$this->assertFalse( Evaluator::evaluate( $logic_gt, array( 'score' => '25' ) ) );
		$this->assertTrue( Evaluator::evaluate( $logic_lt, array( 'score' => '25' ) ) );
	}
}
