<?php

namespace FormsVox\Admin;

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
			__( 'FormsVox', 'formsvox' ),
			__( 'FormsVox', 'formsvox' ),
			'manage_options',
			'formsvox',
			array( $this, 'render_app' ),
			'dashicons-feedback',
			25
		);

		add_submenu_page(
			'formsvox',
			__( 'All Forms', 'formsvox' ),
			__( 'All Forms', 'formsvox' ),
			'manage_options',
			'formsvox',
			array( $this, 'render_app' )
		);

		add_submenu_page(
			'formsvox',
			__( 'Add New Form', 'formsvox' ),
			__( 'Add New Form', 'formsvox' ),
			'manage_options',
			'formsvox#/new',
			array( $this, 'render_app' )
		);

		add_submenu_page(
			'formsvox',
			__( 'Entries', 'formsvox' ),
			__( 'Entries', 'formsvox' ),
			'manage_options',
			'formsvox-entries',
			array( $this, 'render_app' )
		);

		add_submenu_page(
			'formsvox',
			__( 'Settings', 'formsvox' ),
			__( 'Settings', 'formsvox' ),
			'manage_options',
			'formsvox-settings',
			array( $this, 'render_app' )
		);
	}

	public function enqueue_admin_assets( $hook ) {
		if ( false === strpos( $hook, 'formsvox' ) ) {
			return;
		}

		$asset_file = FORMSVOX_PATH . 'build/index.asset.php';
		$deps       = array( 'wp-element', 'wp-components', 'wp-data', 'wp-i18n' );
		$version    = FORMSVOX_VERSION;

		if ( file_exists( $asset_file ) ) {
			$asset   = include $asset_file;
			$deps    = $asset['dependencies'];
			$version = $asset['version'];
		}

		wp_enqueue_script(
			'formsvox-admin-builder',
			FORMSVOX_URL . 'build/index.js',
			$deps,
			$version,
			true
		);

		wp_localize_script( 'formsvox-admin-builder', 'formsvoxAdmin', array(
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'restUrl' => esc_url_raw( rest_url( 'formsvox/v1' ) ),
		) );

		wp_enqueue_style(
			'formsvox-admin-style',
			FORMSVOX_URL . 'assets/css/admin.css',
			array( 'wp-components' ),
			FORMSVOX_VERSION
		);
	}

	public function render_app() {
		echo '<div class="wrap"><div id="formsvox-admin-app"></div></div>';
	}

	public function render_pro_upsell_notice() {
		if ( get_option( 'formsvox_dismissed_pro_notice', false ) ) {
			return;
		}
		echo '<div class="notice notice-info is-dismissible"><p>' .
			sprintf(
				/* translators: %s: Pro site link */
				__( 'Enjoying FormsVox? Upgrade to <a href="%s" target="_blank">FormsVox Pro</a> for flat-rate unlimited site activations!', 'formsvox' ),
				'https://formsvox.io'
			) .
			'</p></div>';
	}

	public function register_dashboard_widget() {
		wp_add_dashboard_widget(
			'formsvox_dashboard_widget',
			__( 'FormsVox Submissions (Last 7 Days)', 'formsvox' ),
			array( $this, 'render_dashboard_widget' )
		);
	}

	public function render_dashboard_widget() {
		$query = \FormsVox\DB\EntryModel::query( array( 'limit' => 7 ) );
		echo '<p>' . sprintf( __( 'Total Submissions Recently: %d', 'formsvox' ), intval( $query['total'] ) ) . '</p>';
	}
}
