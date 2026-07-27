<?php

namespace FormsVox\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stripe Payment Controller & Webhook Listener.
 */
class StripeController {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes() {
		register_rest_route( 'formsvox/v1', '/stripe/create-intent', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'create_intent' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( 'formsvox/v1', '/stripe/webhook', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'handle_webhook' ),
			'permission_callback' => '__return_true',
		) );
	}

	public function create_intent( \WP_REST_Request $request ) {
		$amount   = floatval( $request->get_param( 'amount' ) );
		$currency = strtolower( sanitize_text_field( $request->get_param( 'currency' ) ?: 'usd' ) );
		$form_id  = intval( $request->get_param( 'form_id' ) );

		if ( $amount <= 0 ) {
			return new \WP_Error( 'invalid_amount', __( 'Payment amount must be greater than 0.', 'formsvox' ), array( 'status' => 400 ) );
		}

		$settings = get_option( 'formsvox_settings', array() );
		$secret   = isset( $settings['stripe_secret'] ) ? trim( $settings['stripe_secret'] ) : '';

		if ( empty( $secret ) ) {
			return new \WP_Error( 'stripe_not_configured', __( 'Stripe secret key is missing in settings.', 'formsvox' ), array( 'status' => 400 ) );
		}

		$response = wp_remote_post( 'https://api.stripe.com/v1/payment_intents', array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $secret,
				'Content-Type'  => 'application/x-www-form-urlencoded',
			),
			'body'    => http_build_query( array(
				'amount'   => round( $amount * 100 ), // Convert to cents
				'currency' => $currency,
				'metadata' => array( 'form_id' => $form_id ),
			) ),
		) );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'stripe_api_error', __( 'Failed to communicate with Stripe.', 'formsvox' ), array( 'status' => 500 ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( isset( $body['error'] ) ) {
			return new \WP_Error( 'stripe_error', $body['error']['message'], array( 'status' => 400 ) );
		}

		return rest_ensure_response( array(
			'client_secret'     => $body['client_secret'],
			'payment_intent_id' => $body['id'],
		) );
	}

	public function handle_webhook( \WP_REST_Request $request ) {
		$payload   = $request->get_body();
		$sig_header = $request->get_header( 'stripe_signature' ) ?: $request->get_header( 'Stripe-Signature' );

		$settings       = get_option( 'formsvox_settings', array() );
		$webhook_secret = isset( $settings['stripe_webhook_secret'] ) ? trim( $settings['stripe_webhook_secret'] ) : '';

		// Verify signature if secret is present
		if ( ! empty( $webhook_secret ) && ! empty( $sig_header ) ) {
			if ( ! $this->verify_stripe_signature( $payload, $sig_header, $webhook_secret ) ) {
				return new \WP_Error( 'invalid_signature', __( 'Stripe webhook signature verification failed.', 'formsvox' ), array( 'status' => 400 ) );
			}
		}

		$event = json_decode( $payload, true );
		if ( empty( $event['type'] ) ) {
			return new \WP_Error( 'invalid_event', __( 'Invalid webhook payload.', 'formsvox' ), array( 'status' => 400 ) );
		}

		if ( 'payment_intent.succeeded' === $event['type'] ) {
			$intent   = $event['data']['object'];
			$form_id  = isset( $intent['metadata']['form_id'] ) ? intval( $intent['metadata']['form_id'] ) : 0;
			$entry_id = isset( $intent['metadata']['entry_id'] ) ? intval( $intent['metadata']['entry_id'] ) : 0;

			if ( $entry_id > 0 ) {
				\FormsVox\DB\EntryModel::update( $entry_id, array( 'status' => 'paid' ) );
			}
			do_action( 'formsvox_stripe_payment_succeeded', $intent, $form_id, $entry_id );
		}

		return rest_ensure_response( array( 'received' => true ) );
	}

	private function verify_stripe_signature( $payload, $sig_header, $secret ) {
		$items = explode( ',', $sig_header );
		$t     = '';
		$v1    = '';

		foreach ( $items as $item ) {
			$pair = explode( '=', trim( $item ), 2 );
			if ( 2 === count( $pair ) ) {
				if ( 't' === $pair[0] ) {
					$t = $pair[1];
				} elseif ( 'v1' === $pair[0] ) {
					$v1 = $pair[1];
				}
			}
		}

		if ( empty( $t ) || empty( $v1 ) ) {
			return false;
		}

		$signed_payload = $t . '.' . $payload;
		$expected_sig   = hash_hmac( 'sha256', $signed_payload, $secret );

		return hash_equals( $expected_sig, $v1 );
	}
}
