<?php

namespace FormVox\AntiSpam;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Server-Side CAPTCHA Verification Engine.
 */
class CaptchaVerifier {
	/**
	 * Verify CAPTCHA tokens if keys are configured.
	 *
	 * @param array $params Request parameters.
	 * @return true|\WP_Error
	 */
	public static function verify( $params ) {
		$settings = get_option( 'formvox_settings', array() );

		// 1. Google reCAPTCHA v2 / v3
		$recaptcha_secret = isset( $settings['recaptcha_secret_key'] ) ? trim( $settings['recaptcha_secret_key'] ) : '';
		if ( ! empty( $recaptcha_secret ) ) {
			$token = isset( $params['g-recaptcha-response'] ) ? sanitize_text_field( $params['g-recaptcha-response'] ) : '';
			if ( empty( $token ) ) {
				return new \WP_Error( 'recaptcha_failed', __( 'reCAPTCHA verification token missing.', 'formvox' ), array( 'status' => 400 ) );
			}
			$res = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
				'body' => array(
					'secret'   => $recaptcha_secret,
					'response' => $token,
					'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
				),
			) );
			if ( is_wp_error( $res ) ) {
				return new \WP_Error( 'recaptcha_error', __( 'Unable to verify reCAPTCHA with Google.', 'formvox' ), array( 'status' => 400 ) );
			}
			$body = json_decode( wp_remote_retrieve_body( $res ), true );
			if ( empty( $body['success'] ) ) {
				return new \WP_Error( 'recaptcha_failed', __( 'reCAPTCHA verification failed.', 'formvox' ), array( 'status' => 400 ) );
			}
		}

		// 2. Cloudflare Turnstile
		$turnstile_secret = isset( $settings['turnstile_secret_key'] ) ? trim( $settings['turnstile_secret_key'] ) : '';
		if ( ! empty( $turnstile_secret ) ) {
			$token = isset( $params['cf-turnstile-response'] ) ? sanitize_text_field( $params['cf-turnstile-response'] ) : '';
			if ( empty( $token ) ) {
				return new \WP_Error( 'turnstile_failed', __( 'Cloudflare Turnstile token missing.', 'formvox' ), array( 'status' => 400 ) );
			}
			$res = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
				'body' => array(
					'secret'   => $turnstile_secret,
					'response' => $token,
				),
			) );
			if ( is_wp_error( $res ) ) {
				return new \WP_Error( 'turnstile_error', __( 'Unable to verify Turnstile token.', 'formvox' ), array( 'status' => 400 ) );
			}
			$body = json_decode( wp_remote_retrieve_body( $res ), true );
			if ( empty( $body['success'] ) ) {
				return new \WP_Error( 'turnstile_failed', __( 'Cloudflare Turnstile verification failed.', 'formvox' ), array( 'status' => 400 ) );
			}
		}

		// 3. hCaptcha
		$hcaptcha_secret = isset( $settings['hcaptcha_secret_key'] ) ? trim( $settings['hcaptcha_secret_key'] ) : '';
		if ( ! empty( $hcaptcha_secret ) ) {
			$token = isset( $params['h-captcha-response'] ) ? sanitize_text_field( $params['h-captcha-response'] ) : '';
			if ( empty( $token ) ) {
				return new \WP_Error( 'hcaptcha_failed', __( 'hCaptcha verification token missing.', 'formvox' ), array( 'status' => 400 ) );
			}
			$res = wp_remote_post( 'https://api.hcaptcha.com/siteverify', array(
				'body' => array(
					'secret'   => $hcaptcha_secret,
					'response' => $token,
				),
			) );
			if ( is_wp_error( $res ) ) {
				return new \WP_Error( 'hcaptcha_error', __( 'Unable to verify hCaptcha token.', 'formvox' ), array( 'status' => 400 ) );
			}
			$body = json_decode( wp_remote_retrieve_body( $res ), true );
			if ( empty( $body['success'] ) ) {
				return new \WP_Error( 'hcaptcha_failed', __( 'hCaptcha verification failed.', 'formvox' ), array( 'status' => 400 ) );
			}
		}

		return true;
	}
}
