<?php

namespace FormsVox\AntiSpam;

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
		$time_token = wp_create_nonce( 'formsvox_time_' . time() );
		$timestamp  = time();

		return sprintf(
			'<div style="display:none !important;" aria-hidden="true">
				<label for="formsvox_hp">Leave this field blank</label>
				<input type="text" name="formsvox_hp" id="formsvox_hp" value="" tabindex="-1" autocomplete="off" />
				<input type="hidden" name="formsvox_tt" value="%d" />
				<input type="hidden" name="formsvox_tn" value="%s" />
			</div>',
			$timestamp,
			esc_attr( $time_token )
		);
	}

	public function verify( $params ) {
		// 1. Honeypot check
		if ( ! empty( $params['formsvox_hp'] ) ) {
			return false;
		}

		// 2. Time-trap check (Submission must take at least 2 seconds)
		if ( isset( $params['formsvox_tt'] ) ) {
			$sub_time = intval( $params['formsvox_tt'] );
			if ( ( time() - $sub_time ) < 2 ) {
				return false;
			}
		}

		return true;
	}
}
