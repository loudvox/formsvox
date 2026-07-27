<?php

namespace FormsVox\Tests;

use PHPUnit\Framework\TestCase;
use FormsVox\Fields\Types\Text;
use FormsVox\Fields\Types\Textarea;
use FormsVox\Fields\Types\Name;
use FormsVox\Fields\Types\Email;
use FormsVox\Fields\Types\Phone;
use FormsVox\Fields\Types\Address;
use FormsVox\Fields\Types\URL;
use FormsVox\Fields\Types\Number;
use FormsVox\Fields\Types\Slider;
use FormsVox\Fields\Types\Dropdown;
use FormsVox\Fields\Types\Checkboxes;
use FormsVox\Fields\Types\Radio;
use FormsVox\Fields\Types\DateTime;
use FormsVox\Fields\Types\FileUpload;
use FormsVox\Fields\Types\Password;
use FormsVox\Fields\Types\Hidden;
use FormsVox\Fields\Types\Rating;
use FormsVox\Fields\Types\Likert;
use FormsVox\Fields\Types\NPS;
use FormsVox\Fields\Types\Repeater;
use FormsVox\Fields\Types\PaymentSingle;
use FormsVox\Fields\Types\PaymentMultiple;
use FormsVox\Fields\Types\PaymentTotal;

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
