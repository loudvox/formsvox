<?php

define( 'ABSPATH', __DIR__ . '/../../' );
define( 'FORMVOX_VERSION', '1.0.0' );
define( 'FORMVOX_FILE', __DIR__ . '/../../formvox.php' );
define( 'FORMVOX_PATH', __DIR__ . '/../../' );
define( 'FORMVOX_URL', 'http://example.org/wp-content/plugins/formvox/' );

require_once __DIR__ . '/../../vendor/autoload.php';

// Stub WordPress functions for PHPUnit testing environment
if ( ! function_exists( 'add_action' ) ) {
	function add_action() {}
}
if ( ! function_exists( 'add_filter' ) ) {
	function add_filter() {}
}
if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $tag, $value ) {
		return $value;
	}
}
if ( ! function_exists( 'do_action' ) ) {
	function do_action() {}
}
if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) {
		return trim( strip_tags( (string) $str ) );
	}
}
if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( $str ) {
		return trim( strip_tags( (string) $str ) );
	}
}
if ( ! function_exists( 'sanitize_email' ) ) {
	function sanitize_email( $str ) {
		return filter_var( $str, FILTER_SANITIZE_EMAIL );
	}
}
if ( ! function_exists( 'is_email' ) ) {
	function is_email( $str ) {
		return filter_var( $str, FILTER_VALIDATE_EMAIL );
	}
}
if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url ) {
		return filter_var( $url, FILTER_SANITIZE_URL );
	}
}
if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $str ) {
		return htmlspecialchars( (string) $str, ENT_QUOTES, 'UTF-8' );
	}
}
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $str ) {
		return htmlspecialchars( (string) $str, ENT_QUOTES, 'UTF-8' );
	}
}
if ( ! function_exists( 'esc_textarea' ) ) {
	function esc_textarea( $str ) {
		return htmlspecialchars( (string) $str, ENT_QUOTES, 'UTF-8' );
	}
}
if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}
if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data ) {
		return json_encode( $data );
	}
}
if ( ! function_exists( 'current_time' ) ) {
	function current_time( $type ) {
		return date( 'Y-m-d H:i:s' );
	}
}
if ( ! function_exists( 'get_option' ) ) {
	function get_option( $opt, $default = false ) {
		return $default;
	}
}
if ( ! function_exists( 'wp_get_referer' ) ) {
	function wp_get_referer() {
		return 'http://example.org';
	}
}
if ( ! function_exists( 'home_url' ) ) {
	function home_url() {
		return 'http://example.org';
	}
}

class WP_Error {
	public $code;
	public $message;
	public function __construct( $code = '', $message = '' ) {
		$this->code    = $code;
		$this->message = $message;
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ) {
		return $thing instanceof WP_Error;
	}
}
