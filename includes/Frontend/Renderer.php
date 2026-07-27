<?php

namespace FormVox\Frontend;

use FormVox\DB\FormModel;
use FormVox\Fields\FieldRegistry;
use FormVox\AntiSpam\Honeypot;

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
		add_shortcode( 'formvox', array( $this, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
	}

	public function enqueue_frontend_assets() {
		wp_enqueue_style(
			'formvox-frontend',
			FORMVOX_URL . 'assets/css/frontend.css',
			array(),
			FORMVOX_VERSION
		);

		wp_enqueue_script(
			'formvox-frontend-js',
			FORMVOX_URL . 'assets/js/frontend.js',
			array(),
			FORMVOX_VERSION,
			true
		);

		wp_localize_script( 'formvox-frontend-js', 'formvoxFrontend', array(
			'restUrl' => esc_url_raw( rest_url( 'formvox/v1' ) ),
		) );
	}

	public function render_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts, 'formvox' );

		return $this->render_form( intval( $atts['id'] ) );
	}

	public function render_form( $form_id ) {
		$form = FormModel::get( $form_id );
		if ( ! $form ) {
			return '<p class="formvox-error">' . esc_html__( 'FormVox: Form not found.', 'formvox' ) . '</p>';
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
				$fields_html .= $field_obj->render( $field_config );
			}
		}

		// Anti-Spam Fields
		$honeypot_html = Honeypot::get_instance()->render_fields();

		$ajax_class = ! empty( $settings['ajax_submit'] ) ? 'formvox-ajax-form' : '';
		$form_title = ! empty( $settings['title'] ) ? '<h2 class="formvox-form-title">' . esc_html( $settings['title'] ) . '</h2>' : '';
		$form_desc  = ! empty( $settings['description'] ) ? '<p class="formvox-form-desc">' . esc_html( $settings['description'] ) . '</p>' : '';
		$submit_txt = ! empty( $settings['submit_text'] ) ? esc_attr( $settings['submit_text'] ) : esc_attr__( 'Submit', 'formvox' );

		return sprintf(
			'<div class="formvox-form-wrapper" id="formvox-wrapper-%d">
				%s
				%s
				<form id="formvox-form-%d" class="formvox-form %s" action="%s" method="post" enctype="multipart/form-data" data-form-id="%d">
					%s
					%s
					<div class="formvox-submit-wrap">
						<button type="submit" class="formvox-submit-btn">%s</button>
					</div>
					<div class="formvox-response-message" style="display:none;"></div>
				</form>
			</div>',
			intval( $form_id ),
			$form_title,
			$form_desc,
			intval( $form_id ),
			esc_attr( $ajax_class ),
			esc_url( rest_url( 'formvox/v1/submit/' . $form_id ) ),
			intval( $form_id ),
			$honeypot_html,
			$fields_html,
			$submit_txt
		);
	}
}

// Global PHP Helper Function
if ( ! function_exists( 'formvox_display_form' ) ) {
	function formvox_display_form( $form_id ) {
		echo Renderer::get_instance()->render_form( $form_id );
	}
}
