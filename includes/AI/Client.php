<?php

namespace FormsVox\AI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * VoiceCore Backend API Client.
 */
class Client {
	private static $api_url = 'https://api.voicecore.ai';

	public static function get_api_url() {
		return apply_filters( 'formsvox_ai_api_url', self::$api_url );
	}

	public static function get_api_key() {
		$settings = get_option( 'formsvox_settings', array() );
		return isset( $settings['voicecore_api_key'] ) ? trim( $settings['voicecore_api_key'] ) : '';
	}

	public static function request( $endpoint, $method = 'GET', $body = null ) {
		$key = self::get_api_key();
		if ( empty( $key ) ) {
			return new \WP_Error( 'missing_api_key', __( 'VoiceCore API key is missing.', 'formsvox' ), array( 'status' => 401 ) );
		}

		$args = array(
			'method'  => $method,
			'headers' => array(
				'Authorization' => 'Bearer ' . $key,
				'Content-Type'  => 'application/json',
			),
			'timeout' => 30,
		);

		if ( ! empty( $body ) ) {
			$args['body'] = wp_json_encode( $body );
		}

		$res = wp_remote_request( self::get_api_url() . $endpoint, $args );
		if ( is_wp_error( $res ) ) {
			return $res;
		}

		$code = wp_remote_retrieve_response_code( $res );
		$data = json_decode( wp_remote_retrieve_body( $res ), true );

		if ( 429 === $code ) {
			return new \WP_Error( 'quota_exceeded', isset( $data['message'] ) ? $data['message'] : __( 'VoiceCore monthly AI quota exceeded.', 'formsvox' ), array( 'status' => 429 ) );
		}

		if ( $code >= 400 ) {
			return new \WP_Error( 'voicecore_api_error', isset( $data['message'] ) ? $data['message'] : __( 'VoiceCore API error.', 'formsvox' ), array( 'status' => $code ) );
		}

		return $data;
	}
}
