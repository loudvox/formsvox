<?php

namespace FormsVox\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Integrations Controller (Stripe, Mailchimp, Free Webhooks).
 */
class IntegrationManager {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function process_submission( $form, $entry_id, $fields_data ) {
		$schema       = isset( $form['schema'] ) ? $form['schema'] : array();
		$integrations = isset( $schema['integrations'] ) && is_array( $schema['integrations'] ) ? $schema['integrations'] : array();

		// Free Webhooks Integration (arbitrary HTTP POST with field mapping)
		if ( ! empty( $integrations['webhook'] ) && ! empty( $integrations['webhook']['url'] ) ) {
			$webhook_url = esc_url_raw( $integrations['webhook']['url'] );
			$payload     = array(
				'form_id'    => $form['id'],
				'entry_id'   => $entry_id,
				'submitted'  => current_time( 'mysql' ),
				'fields'     => $fields_data,
			);

			wp_remote_post( $webhook_url, array(
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => wp_json_encode( $payload ),
				'timeout' => 15,
			) );
		}

		// Mailchimp Opt-in Integration
		if ( ! empty( $integrations['mailchimp'] ) && ! empty( $integrations['mailchimp']['list_id'] ) ) {
			$settings = get_option( 'formsvox_settings', array() );
			$api_key  = isset( $settings['mailchimp_api_key'] ) ? $settings['mailchimp_api_key'] : '';

			if ( ! empty( $api_key ) ) {
				$data_center = substr( $api_key, strpos( $api_key, '-' ) + 1 );
				$url         = "https://{$data_center}.api.mailchimp.com/3.0/lists/" . $integrations['mailchimp']['list_id'] . '/members';
				$email_field = isset( $integrations['mailchimp']['email_field'] ) ? $integrations['mailchimp']['email_field'] : 'email';
				$email_val   = isset( $fields_data[ $email_field ] ) ? $fields_data[ $email_field ] : '';

				if ( is_email( $email_val ) ) {
					wp_remote_post( $url, array(
						'headers' => array(
							'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
							'Content-Type'  => 'application/json',
						),
						'body'    => wp_json_encode( array(
							'email_address' => $email_val,
							'status'        => 'subscribed',
						) ),
					) );
				}
			}
		}

		// Stripe Payments Integration
		if ( ! empty( $integrations['stripe'] ) ) {
			// One-time payment processing token / charge handler stub
			do_action( 'formsvox_stripe_payment_process', $form, $entry_id, $fields_data );
		}
	}
}
