<?php

namespace FormVox;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main FormVox Plugin Singleton Class.
 */
class Plugin {
	/**
	 * Instance reference.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get main plugin instance.
	 *
	 * @return Plugin
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Initialize components.
	 */
	private function init() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		if ( is_admin() ) {
			Admin\AdminManager::get_instance();
		}

		DB\Migrator::get_instance();
		API\RestServer::get_instance();
		Fields\FieldRegistry::get_instance();
		AntiSpam\Honeypot::get_instance();
		Notifications\EmailEngine::get_instance();
		Integrations\IntegrationManager::get_instance();
		Integrations\StripeController::get_instance();
		Frontend\Renderer::get_instance();
		Blocks\GutenbergBlock::get_instance();
		Importers\WPFormsImporter::get_instance();
	}

	/**
	 * Load translation textdomain.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'formvox', false, dirname( plugin_basename( FORMVOX_FILE ) ) . '/languages' );
	}

	/**
	 * Plugin activation hook.
	 */
	public static function activate() {
		DB\Migrator::get_instance()->migrate();
		update_option( 'formvox_version', FORMVOX_VERSION );
	}

	/**
	 * Plugin deactivation hook.
	 */
	public static function deactivate() {
		// Clean up temporary transients if needed.
	}
}
