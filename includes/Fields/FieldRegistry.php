<?php

namespace FormsVox\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field Registry Class for FormsVox.
 */
class FieldRegistry {
	private static $instance = null;
	private $fields = array();

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->register_default_fields();
	}

	private function register_default_fields() {
		$default_classes = array(
			Types\Text::class,
			Types\Textarea::class,
			Types\Name::class,
			Types\Email::class,
			Types\Phone::class,
			Types\Address::class,
			Types\URL::class,
			Types\Number::class,
			Types\Slider::class,
			Types\Dropdown::class,
			Types\Checkboxes::class,
			Types\Radio::class,
			Types\DateTime::class,
			Types\FileUpload::class,
			Types\Password::class,
			Types\Hidden::class,
			Types\PageBreak::class,
			Types\Section::class,
			Types\HTML::class,
			Types\Rating::class,
			Types\Likert::class,
			Types\NPS::class,
			Types\Layout::class,
			Types\Repeater::class,
			Types\PaymentSingle::class,
			Types\PaymentMultiple::class,
			Types\PaymentTotal::class,
		);

		foreach ( $default_classes as $class_name ) {
			if ( class_exists( $class_name ) ) {
				$field_obj = new $class_name();
				$this->fields[ $field_obj->get_type() ] = $field_obj;
			}
		}

		/**
		 * Filter registered FormsVox field types.
		 *
		 * @param array $fields Associative array of field_type => BaseField instance.
		 */
		$this->fields = apply_filters( 'formsvox_field_types', $this->fields );
	}

	public function get_field( $type ) {
		return isset( $this->fields[ $type ] ) ? $this->fields[ $type ] : null;
	}

	public function get_registered_fields() {
		return $this->fields;
	}
}
