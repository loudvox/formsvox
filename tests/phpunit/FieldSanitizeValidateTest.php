<?php

namespace FormVox\Tests;

use PHPUnit\Framework\TestCase;
use FormVox\Fields\Types\Text;
use FormVox\Fields\Types\Textarea;
use FormVox\Fields\Types\Name;
use FormVox\Fields\Types\Email;
use FormVox\Fields\Types\Phone;
use FormVox\Fields\Types\Address;
use FormVox\Fields\Types\URL;
use FormVox\Fields\Types\Number;
use FormVox\Fields\Types\Slider;
use FormVox\Fields\Types\Dropdown;
use FormVox\Fields\Types\Checkboxes;
use FormVox\Fields\Types\Radio;
use FormVox\Fields\Types\DateTime;
use FormVox\Fields\Types\FileUpload;
use FormVox\Fields\Types\Password;
use FormVox\Fields\Types\Hidden;
use FormVox\Fields\Types\Rating;
use FormVox\Fields\Types\Likert;
use FormVox\Fields\Types\NPS;
use FormVox\Fields\Types\Repeater;
use FormVox\Fields\Types\PaymentSingle;
use FormVox\Fields\Types\PaymentMultiple;
use FormVox\Fields\Types\PaymentTotal;

class FieldSanitizeValidateTest extends TestCase {
	public function test_text_field_sanitize_and_validate() {
		$field = new Text();
		$config = array( 'id' => 'text_1', 'label' => 'Text', 'required' => true );
		$this->assertEquals( 'alert(1)Hello', $field->sanitize( '<script>alert(1)</script>Hello', $config ) );
		$this->assertTrue( $field->validate( 'Hello', $config ) );
		$this->assertTrue( is_wp_error( $field->validate( '', $config ) ) );
	}

	public function test_email_field_validation() {
		$field = new Email();
		$config = array( 'id' => 'email_1', 'label' => 'Email', 'required' => true );
		$this->assertTrue( $field->validate( 'user@example.com', $config ) );
		$this->assertTrue( is_wp_error( $field->validate( 'invalid-email', $config ) ) );
	}

	public function test_phone_field_validation() {
		$field = new Phone();
		$config = array( 'id' => 'phone_1', 'label' => 'Phone' );
		$this->assertTrue( $field->validate( '+1 (555) 000-1234', $config ) );
		$this->assertTrue( is_wp_error( $field->validate( 'invalid-phone-xyz', $config ) ) );
	}

	public function test_url_field_validation() {
		$field = new URL();
		$config = array( 'id' => 'url_1', 'label' => 'Website' );
		$this->assertTrue( $field->validate( 'https://example.com', $config ) );
		$this->assertTrue( is_wp_error( $field->validate( 'not-a-url', $config ) ) );
	}

	public function test_number_and_slider_fields() {
		$num = new Number();
		$this->assertEquals( 42.5, $num->sanitize( '42.5', array() ) );
		$this->assertTrue( $num->validate( '42', array( 'label' => 'Number' ) ) );
		$this->assertTrue( is_wp_error( $num->validate( 'abc', array( 'label' => 'Number' ) ) ) );

		$slider = new Slider();
		$this->assertEquals( 75, $slider->sanitize( '75', array() ) );
	}

	public function test_name_and_address_fields() {
		$name = new Name();
		$name_val = array( 'first' => '<b>John</b>', 'last' => 'Doe' );
		$sanitized_name = $name->sanitize( $name_val, array() );
		$this->assertEquals( 'John', $sanitized_name['first'] );
		$this->assertEquals( 'Doe', $sanitized_name['last'] );

		$addr = new Address();
		$addr_val = array( 'address1' => '123 Main St', 'city' => 'New York', 'country' => 'USA' );
		$sanitized_addr = $addr->sanitize( $addr_val, array() );
		$this->assertEquals( 'New York', $sanitized_addr['city'] );
	}

	public function test_payment_fields() {
		$ps = new PaymentSingle();
		$this->assertEquals( 19.99, $ps->sanitize( '19.99', array() ) );

		$pm = new PaymentMultiple();
		$this->assertEquals( 'Option 1', $pm->sanitize( 'Option 1', array() ) );

		$pt = new PaymentTotal();
		$this->assertEquals( 50.00, $pt->sanitize( '50.00', array() ) );
	}
}
