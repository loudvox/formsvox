<?php

namespace FormsVox\AI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * VoiceCore AI Module Registry.
 */
class ModuleRegistry {
	private static $instance = null;
	private $modules = array();

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->register_default_modules();
	}

	private function register_default_modules() {
		$this->modules['forms_agent'] = array(
			'id'          => 'forms_agent',
			'name'        => __( 'Conversational Forms Agent', 'formsvox' ),
			'description' => __( 'Conversational AI form filling assistant for website visitors.', 'formsvox' ),
			'status'      => 'active',
		);

		/**
		 * Filter registered VoiceCore AI suite modules.
		 *
		 * @param array $modules Array of registered VoiceCore modules.
		 */
		$this->modules = apply_filters( 'formsvox_ai_modules', $this->modules );
	}

	public function get_modules() {
		return $this->modules;
	}
}
