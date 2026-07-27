<?php

namespace FormVox\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Base Field Class.
 */
abstract class BaseField {
	/**
	 * Get field type identifier.
	 *
	 * @return string
	 */
	abstract public function get_type();

	/**
	 * Get field display title.
	 *
	 * @return string
	 */
	abstract public function get_title();

	/**
	 * Get field icon slug.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'admin-generic';
	}

	/**
	 * Get field category (standard, fancy, layout, payment).
	 *
	 * @return string
	 */
	public function get_category() {
		return 'standard';
	}

	/**
	 * Get default field settings array.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return array(
			'id'          => '',
			'type'        => $this->get_type(),
			'label'       => $this->get_title(),
			'description' => '',
			'required'    => false,
			'css_class'   => '',
			'placeholder' => '',
			'default_val' => '',
		);
	}

	/**
	 * Render HTML field on public front end.
	 *
	 * @param array $field Field configuration.
	 * @param mixed $value User submitted value if re-rendering.
	 * @param array $form  Parent form schema.
	 * @return string HTML output.
	 */
	abstract public function render( $field, $value = null, $form = array() );

	/**
	 * Sanitize user input value.
	 *
	 * @param mixed $value Submitted value.
	 * @param array $field Field configuration.
	 * @return mixed Sanitized value.
	 */
	public function sanitize( $value, $field ) {
		if ( is_array( $value ) ) {
			return array_map( 'sanitize_text_field', $value );
		}
		return sanitize_text_field( (string) $value );
	}

	/**
	 * Validate input value.
	 *
	 * @param mixed $value Submitted value.
	 * @param array $field Field configuration.
	 * @param array $form  Parent form schema.
	 * @return true|\WP_Error
	 */
	public function validate( $value, $field, $form = array() ) {
		$is_required = ! empty( $field['required'] );
		$is_empty    = is_array( $value ) ? empty( array_filter( $value ) ) : ( '' === trim( (string) $value ) );

		if ( $is_required && $is_empty ) {
			/* translators: %s: Field label */
			return new \WP_Error( 'required_field', sprintf( __( '%s is required.', 'formvox' ), esc_html( $field['label'] ) ) );
		}

		return true;
	}
}
