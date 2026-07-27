<?php
/**
 * Plugin Name: FormVox
 * Plugin URI: https://formvox.io
 * Description: The fast, accessible, developer-friendly WordPress form builder. Drag-and-drop builder, 24+ fields, conditional logic, entries, Stripe, Mailchimp & free Webhooks.
 * Version: 1.0.0
 * Author: FormVox Team
 * Author URI: https://formvox.io
 * Text Domain: formvox
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.4
 * Requires PHP: 7.4
 *
 * @package FormVox
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FORMVOX_VERSION', '1.0.0' );
define( 'FORMVOX_FILE', __FILE__ );
define( 'FORMVOX_PATH', plugin_dir_path( __FILE__ ) );
define( 'FORMVOX_URL', plugin_dir_url( __FILE__ ) );

if ( file_exists( FORMVOX_PATH . 'vendor/autoload.php' ) ) {
	require_once FORMVOX_PATH . 'vendor/autoload.php';
} else {
	// Fallback PSR-4 autoloader when composer vendor directory is not committed.
	spl_autoload_register( function( $class ) {
		$prefix   = 'FormVox\\';
		$base_dir = FORMVOX_PATH . 'includes/';
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

register_activation_hook( __FILE__, array( 'FormVox\\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'FormVox\\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'FormVox\\Plugin', 'get_instance' ) );
