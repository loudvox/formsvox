<?php

namespace FormsVox\Frontend;

use FormsVox\DB\FormModel;
use FormsVox\Fields\FieldRegistry;
use FormsVox\AntiSpam\Honeypot;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public Front-End Form Renderer.
 */
class Renderer {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_shortcode( 'formsvox', array( $this, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
	}

	public function enqueue_frontend_assets() {
		wp_enqueue_style(
			'formsvox-frontend',
			FORMSVOX_URL . 'assets/css/frontend.css',
			array(),
			FORMSVOX_VERSION
		);

		wp_enqueue_script(
			'formsvox-frontend-js',
			FORMSVOX_URL . 'assets/js/frontend.js',
			array(),
			FORMSVOX_VERSION,
			true
		);

		wp_localize_script( 'formsvox-frontend-js', 'formsvoxFrontend', array(
			'restUrl' => esc_url_raw( rest_url( 'formsvox/v1' ) ),
		) );
	}

	public function render_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts, 'formsvox' );

		return $this->render_form( intval( $atts['id'] ) );
	}

	public function render_form( $form_id ) {
		$form = FormModel::get( $form_id );
		if ( ! $form ) {
			return '<p class="formsvox-error">' . esc_html__( 'FormsVox: Form not found.', 'formsvox' ) . '</p>';
		}

		$schema   = isset( $form['schema'] ) ? $form['schema'] : array();
		$fields   = isset( $schema['fields'] ) && is_array( $schema['fields'] ) ? $schema['fields'] : array();
		$settings = isset( $schema['settings'] ) ? $schema['settings'] : array();

		$registry = FieldRegistry::get_instance();
		$fields_html = '';

		foreach ( $fields as $field_config ) {
			$type      = isset( $field_config['type'] ) ? $field_config['type'] : 'text';
			$field_obj = $registry->get_field( $type );

			if ( $field_obj ) {
				$rendered = $field_obj->render( $field_config );
				if ( ! empty( $field_config['conditional_logic'] ) && ! empty( $field_config['conditional_logic']['enabled'] ) ) {
					$logic_json = esc_attr( wp_json_encode( $field_config['conditional_logic'] ) );
					$rendered   = preg_replace( '/class="([^"]*formsvox-field[^"]*)"/', 'class="$1" data-conditional-logic="' . $logic_json . '"', $rendered, 1 );
				}
				$fields_html .= $rendered;
			}
		}

		// Anti-Spam Fields
		$honeypot_html = Honeypot::get_instance()->render_fields();

		$ajax_class = ! empty( $settings['ajax_submit'] ) ? 'formsvox-ajax-form' : '';
		$form_title = ! empty( $settings['title'] ) ? '<h2 class="formsvox-form-title">' . esc_html( $settings['title'] ) . '</h2>' : '';
		$form_desc  = ! empty( $settings['description'] ) ? '<p class="formsvox-form-desc">' . esc_html( $settings['description'] ) . '</p>' : '';
		$submit_txt = ! empty( $settings['submit_text'] ) ? esc_attr( $settings['submit_text'] ) : esc_attr__( 'Submit', 'formsvox' );

		return sprintf(
			'<div class="formsvox-form-wrapper" id="formsvox-wrapper-%d">
				%s
				%s
				<form id="formsvox-form-%d" class="formsvox-form %s" action="%s" method="post" enctype="multipart/form-data" data-form-id="%d">
					%s
					%s
					<div class="formsvox-submit-wrap">
						<button type="submit" class="formsvox-submit-btn">%s</button>
					</div>
					<div class="formsvox-response-message" style="display:none;"></div>
				</form>
			</div>',
			intval( $form_id ),
			$form_title,
			$form_desc,
			intval( $form_id ),
			esc_attr( $ajax_class ),
			esc_url( rest_url( 'formsvox/v1/submit/' . $form_id ) ),
			intval( $form_id ),
			$honeypot_html,
			$fields_html,
			$submit_txt
		);
	}
}

// Global PHP Helper Function
if ( ! function_exists( 'formsvox_display_form' ) ) {
	function formsvox_display_form( $form_id ) {
		echo Renderer::get_instance()->render_form( $form_id );
	}
}
