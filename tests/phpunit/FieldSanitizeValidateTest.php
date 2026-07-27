<?php

namespace FormVox\Tests;

use PHPUnit\Framework\TestCase;
use FormVox\Fields\Types\Text;
use FormVox\Fields\Types\Email;
use FormVox\Fields\Types\Phone;
use FormVox\Fields\Types\Number;

class FieldSanitizeValidateTest extends TestCase {
	public function test_text_field_sanitize_and_validate() {
		$field = new Text();
		$config = array( 'id' => 'text_1', 'label' => 'Text', 'required' => true );

		$sanitized = $field->sanitize( '<script>alert(1)</script>Hello', $config );
		$this->assertEquals( 'alert(1)Hello', $sanitized );

		$valid = $field->validate( 'Hello', $config );
		$this->assertTrue( $valid );

		$invalid = $field->validate( '', $config );
		$this->assertTrue( is_wp_error( $invalid ) );
	}

	public function test_email_field_validation() {
		$field = new Email();
		$config = array( 'id' => 'email_1', 'label' => 'Email', 'required' => true );

		$valid = $field->validate( 'test@example.com', $config );
		$this->assertTrue( $valid );

		$invalid = $field->validate( 'not-an-email', $config );
		$this->assertTrue( is_wp_error( $invalid ) );
	}

	public function test_phone_field_validation() {
		$field = new Phone();
		$config = array( 'id' => 'phone_1', 'label' => 'Phone', 'required' => false );

		$valid = $field->validate( '+1 (555) 234-5678', $config );
		$this->assertTrue( $valid );

		$invalid = $field->validate( 'invalid-phone-abc', $config );
		$this->assertTrue( is_wp_error( $invalid ) );
	}

	public function test_number_field_validation() {
		$field = new Number();
		$config = array( 'id' => 'num_1', 'label' => 'Number', 'required' => false );

		$valid = $field->validate( '42.5', $config );
		$this->assertTrue( $valid );

		$invalid = $field->validate( 'abc', $config );
		$this->assertTrue( is_wp_error( $invalid ) );
	}
}
