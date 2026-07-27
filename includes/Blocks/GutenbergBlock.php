<?php

namespace FormsVox\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GutenbergBlock {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'register_block' ) );
	}

	public function register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type( 'formsvox/form-block', array(
			'render_callback' => array( $this, 'render_block' ),
			'attributes'      => array(
				'formId' => array(
					'type'    => 'number',
					'default' => 0,
				),
			),
		) );
	}

	public function render_block( $attributes ) {
		$form_id = isset( $attributes['formId'] ) ? intval( $attributes['formId'] ) : 0;
		if ( $form_id <= 0 ) {
			return '';
		}

		return \FormsVox\Frontend\Renderer::get_instance()->render_form( $form_id );
	}
}
