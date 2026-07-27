<?php

namespace FormVox\Tests;

use PHPUnit\Framework\TestCase;
use FormVox\Logic\Evaluator;

class ConditionalLogicTest extends TestCase {
	public function test_equals_operator() {
		$logic = array(
			'enabled' => true,
			'action'  => 'show',
			'match'   => 'all',
			'rules'   => array(
				array( 'field_id' => 'select_1', 'operator' => 'equals', 'value' => 'yes' ),
			),
		);

		$fields_matching = array( 'select_1' => 'yes' );
		$this->assertTrue( Evaluator::evaluate( $logic, $fields_matching ) );

		$fields_not_matching = array( 'select_1' => 'no' );
		$this->assertFalse( Evaluator::evaluate( $logic, $fields_not_matching ) );
	}

	public function test_contains_operator() {
		$logic = array(
			'enabled' => true,
			'action'  => 'show',
			'match'   => 'all',
			'rules'   => array(
				array( 'field_id' => 'text_1', 'operator' => 'contains', 'value' => 'hello' ),
			),
		);

		$fields = array( 'text_1' => 'hello world' );
		$this->assertTrue( Evaluator::evaluate( $logic, $fields ) );
	}
}
