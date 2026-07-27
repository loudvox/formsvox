<?php

namespace FormVox\AntiSpam;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Honeypot {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function render_fields() {
		$time_token = wp_create_nonce( 'formvox_time_' . time() );
		$timestamp  = time();

		return sprintf(
			'<div style="display:none !important;" aria-hidden="true">
				<label for="formvox_hp">Leave this field blank</label>
				<input type="text" name="formvox_hp" id="formvox_hp" value="" tabindex="-1" autocomplete="off" />
				<input type="hidden" name="formvox_tt" value="%d" />
				<input type="hidden" name="formvox_tn" value="%s" />
			</div>',
			$timestamp,
			esc_attr( $time_token )
		);
	}

	public function verify( $params ) {
		// 1. Honeypot check
		if ( ! empty( $params['formvox_hp'] ) ) {
			return false;
		}

		// 2. Time-trap check (Submission must take at least 2 seconds)
		if ( isset( $params['formvox_tt'] ) ) {
			$sub_time = intval( $params['formvox_tt'] );
			if ( ( time() - $sub_time ) < 2 ) {
				return false;
			}
		}

		return true;
	}
}
