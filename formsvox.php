<?php
/**
 * Plugin Name: FormsVox — Drag & Drop Form Builder
 * Plugin URI: https://formsvox.io
 * Description: A VoiceCore product. The fast, accessible, developer-friendly WordPress form builder with 27 fields, conditional logic, entries, Stripe, Mailchimp & free Webhooks.
 * Version: 1.0.0
 * Author: FormsVox Team
 * Author URI: https://formsvox.io
 * Text Domain: formsvox
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.4
 * Requires PHP: 7.4
 *
 * @package FormsVox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FORMSVOX_VERSION', '1.0.0' );
define( 'FORMSVOX_FILE', __FILE__ );
define( 'FORMSVOX_PATH', plugin_dir_path( __FILE__ ) );
define( 'FORMSVOX_URL', plugin_dir_url( __FILE__ ) );

if ( file_exists( FORMSVOX_PATH . 'vendor/autoload.php' ) ) {
	require_once FORMSVOX_PATH . 'vendor/autoload.php';
} else {
	// Fallback PSR-4 autoloader when composer vendor directory is not committed.
	spl_autoload_register( function( $class ) {
		$prefix   = 'FormsVox\\';
		$base_dir = FORMSVOX_PATH . 'includes/';
		$len      = strlen( $prefix );

		if ( 0 !== strncmp( $prefix, $class, $len ) ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	} );
}

function run_formsvox() {
	return \FormsVox\Plugin::get_instance();
}

register_activation_hook( FORMSVOX_FILE, array( '\\FormsVox\\Plugin', 'activate' ) );
register_deactivation_hook( FORMSVOX_FILE, array( '\\FormsVox\\Plugin', 'deactivate' ) );

run_formsvox();
