<?php

namespace FormsVox\AI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * VoiceCore Connection Manager.
 */
class Connection {
	public static function is_connected() {
		$key = Client::get_api_key();
		return ! empty( $key ) && 0 === strpos( $key, 'vc_' );
	}

	public static function get_status() {
		if ( ! self::is_connected() ) {
			return array(
				'connected' => false,
				'message'   => __( 'Not connected to VoiceCore AI service.', 'formsvox' ),
			);
		}

		$account_info = Client::request( '/v1/account', 'GET' );
		if ( is_wp_error( $account_info ) ) {
			return array(
				'connected' => false,
				'error'     => $account_info->get_error_message(),
			);
		}

		return array(
			'connected' => true,
			'account'   => $account_info['account'],
			'quota'     => $account_info['quota'],
			'ingest'    => $account_info['ingest'],
		);
	}
}
