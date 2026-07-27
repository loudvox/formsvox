<?php

namespace FormVox\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminManager {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_notices', array( $this, 'render_pro_upsell_notice' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );
	}

	public function register_menu_pages() {
		add_menu_page(
			__( 'FormVox', 'formvox' ),
			__( 'FormVox', 'formvox' ),
			'manage_options',
			'formvox',
			array( $this, 'render_app' ),
			'dashicons-feedback',
			25
		);

		add_submenu_page(
			'formvox',
			__( 'All Forms', 'formvox' ),
			__( 'All Forms', 'formvox' ),
			'manage_options',
			'formvox',
			array( $this, 'render_app' )
		);

		add_submenu_page(
			'formvox',
			__( 'Add New Form', 'formvox' ),
			__( 'Add New Form', 'formvox' ),
			'manage_options',
			'formvox#/new',
			array( $this, 'render_app' )
		);

		add_submenu_page(
			'formvox',
			__( 'Entries', 'formvox' ),
			__( 'Entries', 'formvox' ),
			'manage_options',
			'formvox-entries',
			array( $this, 'render_app' )
		);

		add_submenu_page(
			'formvox',
			__( 'Settings', 'formvox' ),
			__( 'Settings', 'formvox' ),
			'manage_options',
			'formvox-settings',
			array( $this, 'render_app' )
		);
	}

	public function enqueue_admin_assets( $hook ) {
		if ( false === strpos( $hook, 'formvox' ) ) {
			return;
		}

		$asset_file = FORMVOX_PATH . 'build/index.asset.php';
		$deps       = array( 'wp-element', 'wp-components', 'wp-data', 'wp-i18n' );
		$version    = FORMVOX_VERSION;

		if ( file_exists( $asset_file ) ) {
			$asset   = include $asset_file;
			$deps    = $asset['dependencies'];
			$version = $asset['version'];
		}

		wp_enqueue_script(
			'formvox-admin-builder',
			FORMVOX_URL . 'build/index.js',
			$deps,
			$version,
			true
		);

		wp_localize_script( 'formvox-admin-builder', 'formvoxAdmin', array(
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'restUrl' => esc_url_raw( rest_url( 'formvox/v1' ) ),
		) );

		wp_enqueue_style(
			'formvox-admin-style',
			FORMVOX_URL . 'assets/css/admin.css',
			array( 'wp-components' ),
			FORMVOX_VERSION
		);
	}

	public function render_app() {
		echo '<div class="wrap"><div id="formvox-admin-app"></div></div>';
	}

	public function render_pro_upsell_notice() {
		if ( get_option( 'formvox_dismissed_pro_notice', false ) ) {
			return;
		}
		echo '<div class="notice notice-info is-dismissible"><p>' .
			sprintf(
				/* translators: %s: Pro site link */
				__( 'Enjoying FormVox? Upgrade to <a href="%s" target="_blank">FormVox Pro</a> for flat-rate unlimited site activations!', 'formvox' ),
				'https://formvox.io'
			) .
			'</p></div>';
	}

	public function register_dashboard_widget() {
		wp_add_dashboard_widget(
			'formvox_dashboard_widget',
			__( 'FormVox Submissions (Last 7 Days)', 'formvox' ),
			array( $this, 'render_dashboard_widget' )
		);
	}

	public function render_dashboard_widget() {
		$query = \FormVox\DB\EntryModel::query( array( 'limit' => 7 ) );
		echo '<p>' . sprintf( __( 'Total Submissions Recently: %d', 'formvox' ), intval( $query['total'] ) ) . '</p>';
	}
}
