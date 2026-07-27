<?php

namespace FormsVox\API;

use FormsVox\DB\FormModel;
use FormsVox\DB\EntryModel;
use FormsVox\AntiSpam\Honeypot;
use FormsVox\Notifications\EmailEngine;
use FormsVox\Integrations\IntegrationManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FormsVox REST API Controller.
 */
class RestServer {
	private static $instance = null;
	const NAMESPACE = 'formsvox/v1';

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register all REST API endpoints.
	 */
	public function register_routes() {
		// Forms CRUD
		register_rest_route( self::NAMESPACE, '/forms', array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_forms' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			),
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_form' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			),
		) );

		register_rest_route( self::NAMESPACE, '/forms/(?P<id>\d+)', array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_form' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			),
			array(
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_form' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			),
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_form' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			),
		) );

		// Public Submission Route
		register_rest_route( self::NAMESPACE, '/submit/(?P<id>\d+)', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'submit_form' ),
			'permission_callback' => '__return_true',
		) );

		// Entries CRUD
		register_rest_route( self::NAMESPACE, '/entries', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_entries' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
		) );

		register_rest_route( self::NAMESPACE, '/entries/(?P<id>\d+)', array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_entry' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			),
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_entry' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			),
		) );

		register_rest_route( self::NAMESPACE, '/entries/(?P<id>\d+)/star', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'star_entry' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
		) );

		register_rest_route( self::NAMESPACE, '/entries/export', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( $this, 'export_entries' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
		) );

		// VoiceCore AI Routes
		register_rest_route( self::NAMESPACE, '/ai/chat', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'ai_chat_relay' ),
			'permission_callback' => '__return_true',
		) );

		register_rest_route( self::NAMESPACE, '/ai/account', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_ai_account' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
		) );

		register_rest_route( self::NAMESPACE, '/ai/sync', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'sync_ai_content' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
		) );

		register_rest_route( self::NAMESPACE, '/ai/ingest-status', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_ingest_status' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
		) );

		// Settings & Templates
		register_rest_route( self::NAMESPACE, '/settings', array(
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_settings' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			),
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'update_settings' ),
				'permission_callback' => array( $this, 'check_admin_permission' ),
			),
		) );

		register_rest_route( self::NAMESPACE, '/templates', array(
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_templates' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
		) );

		register_rest_route( self::NAMESPACE, '/import-wpforms', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'import_wpforms' ),
			'permission_callback' => array( $this, 'check_admin_permission' ),
		) );
	}

	public function check_admin_permission( \WP_REST_Request $request ) {
		return current_user_can( 'manage_options' );
	}

	public function get_forms( \WP_REST_Request $request ) {
		$forms = FormModel::all();
		return rest_ensure_response( $forms );
	}

	public function get_form( \WP_REST_Request $request ) {
		$id   = $request->get_param( 'id' );
		$form = FormModel::get( $id );
		if ( ! $form ) {
			return new \WP_Error( 'not_found', __( 'Form not found.', 'formsvox' ), array( 'status' => 404 ) );
		}
		return rest_ensure_response( $form );
	}

	public function create_form( \WP_REST_Request $request ) {
		$title  = $request->get_param( 'title' );
		$schema = $request->get_param( 'schema' );

		if ( empty( $title ) ) {
			$title = __( 'Untitled Form', 'formsvox' );
		}

		$id = FormModel::create( $title, is_array( $schema ) ? $schema : array() );
		return rest_ensure_response( FormModel::get( $id ) );
	}

	public function update_form( \WP_REST_Request $request ) {
		$id     = $request->get_param( 'id' );
		$title  = $request->get_param( 'title' );
		$schema = $request->get_param( 'schema' );
		$status = $request->get_param( 'status' );

		FormModel::update( $id, $title, $schema, $status );
		return rest_ensure_response( FormModel::get( $id ) );
	}

	public function delete_form( \WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );
		FormModel::delete( $id );
		return rest_ensure_response( array( 'success' => true ) );
	}

	public function submit_form( \WP_REST_Request $request ) {
		$form_id = intval( $request->get_param( 'id' ) );
		$form    = FormModel::get( $form_id );

		if ( ! $form ) {
			return new \WP_Error( 'form_not_found', __( 'Form not found.', 'formsvox' ), array( 'status' => 404 ) );
		}

		// Enforce form published status
		if ( isset( $form['status'] ) && 'publish' !== $form['status'] ) {
			return new \WP_Error( 'form_not_published', __( 'This form is not active or published.', 'formsvox' ), array( 'status' => 403 ) );
		}

		// IP-based rate limiting (Max 10 submissions per 60 seconds per IP/form)
		$ip            = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
		$transient_key = 'formsvox_rate_' . md5( $ip . '_' . $form_id );
		$rate_count    = (int) get_transient( $transient_key );
		if ( $rate_count >= 10 ) {
			return new \WP_Error( 'rate_limit_exceeded', __( 'Too many submissions. Please wait a minute and try again.', 'formsvox' ), array( 'status' => 429 ) );
		}
		set_transient( $transient_key, $rate_count + 1, 60 );

		$params = $request->get_params();

		// Anti-Spam Honeypot & Time-Trap Check
		$honeypot = Honeypot::get_instance();
		if ( ! $honeypot->verify( $params ) ) {
			return new \WP_Error( 'spam_detected', __( 'Spam detection triggered.', 'formsvox' ), array( 'status' => 400 ) );
		}

		// Server-Side CAPTCHA Verification (reCAPTCHA, Turnstile, hCaptcha)
		$captcha_result = \FormsVox\AntiSpam\CaptchaVerifier::verify( $params );
		if ( is_wp_error( $captcha_result ) ) {
			return $captcha_result;
		}

		$raw_fields = isset( $params['formsvox_fields'] ) && is_array( $params['formsvox_fields'] ) ? $params['formsvox_fields'] : array();
		$schema     = isset( $form['schema'] ) ? $form['schema'] : array();
		$form_fields = isset( $schema['fields'] ) && is_array( $schema['fields'] ) ? $schema['fields'] : array();

		$registry         = \FormsVox\Fields\FieldRegistry::get_instance();
		$sanitized_fields = array();
		$errors           = array();

		// Field-by-field validation and sanitization
		foreach ( $form_fields as $field_config ) {
			$field_id  = isset( $field_config['id'] ) ? $field_config['id'] : '';
			$type      = isset( $field_config['type'] ) ? $field_config['type'] : 'text';
			$field_obj = $registry->get_field( $type );

			if ( ! $field_id || ! $field_obj ) {
				continue;
			}

			// Check conditional logic visibility for this field
			if ( ! empty( $field_config['conditional_logic'] ) && ! \FormsVox\Logic\Evaluator::evaluate( $field_config['conditional_logic'], $raw_fields ) ) {
				continue; // Skip hidden conditional fields
			}

			$val = isset( $raw_fields[ $field_id ] ) ? $raw_fields[ $field_id ] : null;

			// Validate field value
			$validation_result = $field_obj->validate( $val, $field_config, $form );
			if ( is_wp_error( $validation_result ) ) {
				$errors[ $field_id ] = $validation_result->get_error_message();
			} else {
				// Sanitize field value
				$sanitized_fields[ $field_id ] = $field_obj->sanitize( $val, $field_config );
			}
		}

		if ( ! empty( $errors ) ) {
			return new \WP_Error(
				'validation_failed',
				__( 'Validation failed for one or more fields.', 'formsvox' ),
				array(
					'status' => 400,
					'errors' => $errors,
				)
			);
		}

		// Create Entry
		$entry_id = EntryModel::create( $form_id, $sanitized_fields );

		/**
		 * Action Hook: formsvox_process_entry
		 *
		 * @param int   $entry_id Entry ID.
		 * @param array $form     Form row array.
		 * @param array $fields_data Submitted fields data.
		 */
		do_action( 'formsvox_process_entry', $entry_id, $form, $sanitized_fields );

		// Process Email Notifications & Integrations
		EmailEngine::get_instance()->send_notifications( $form, $entry_id, $sanitized_fields );
		IntegrationManager::get_instance()->process_submission( $form, $entry_id, $sanitized_fields );

		// Confirmation Response with Conditional Routing
		$schema              = isset( $form['schema'] ) ? $form['schema'] : array();
		$all_confirmations   = isset( $schema['confirmations'] ) && is_array( $schema['confirmations'] ) ? $schema['confirmations'] : array();
		$valid_confirmations = array();

		foreach ( $all_confirmations as $conf ) {
			if ( ! empty( $conf['conditional_logic'] ) && ! \FormsVox\Logic\Evaluator::evaluate( $conf['conditional_logic'], $sanitized_fields ) ) {
				continue;
			}
			$valid_confirmations[] = $conf;
		}

		if ( empty( $valid_confirmations ) ) {
			$valid_confirmations[] = array(
				'type'    => 'message',
				'message' => __( 'Thank you! Your submission has been received.', 'formsvox' ),
			);
		}

		return rest_ensure_response( array(
			'success'       => true,
			'entry_id'      => $entry_id,
			'confirmations' => $valid_confirmations,
		) );
	}

	public function get_entries( \WP_REST_Request $request ) {
		$args = array(
			'form_id' => $request->get_param( 'form_id' ),
			'status'  => $request->get_param( 'status' ),
			'starred' => $request->get_param( 'starred' ),
			'limit'   => $request->get_param( 'limit' ),
			'page'    => $request->get_param( 'page' ),
		);
		return rest_ensure_response( EntryModel::query( $args ) );
	}

	public function get_entry( \WP_REST_Request $request ) {
		$id    = $request->get_param( 'id' );
		$entry = EntryModel::get( $id );
		if ( ! $entry ) {
			return new \WP_Error( 'not_found', __( 'Entry not found.', 'formsvox' ), array( 'status' => 404 ) );
		}
		return rest_ensure_response( $entry );
	}

	public function delete_entry( \WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );
		EntryModel::delete( $id );
		return rest_ensure_response( array( 'success' => true ) );
	}

	public function star_entry( \WP_REST_Request $request ) {
		$id      = $request->get_param( 'id' );
		$starred = $request->get_param( 'starred' );
		EntryModel::update( $id, array( 'starred' => intval( $starred ) ) );
		return rest_ensure_response( array( 'success' => true ) );
	}

	public function export_entries( \WP_REST_Request $request ) {
		$form_id = $request->get_param( 'form_id' );
		$query   = EntryModel::query( array( 'form_id' => $form_id, 'limit' => 1000 ) );
		$items   = $query['items'];

		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment; filename="formsvox-entries-' . $form_id . '.csv"' );

		$output = fopen( 'php://output', 'w' );
		if ( ! empty( $items ) ) {
			$first = reset( $items );
			$headers = array( 'Entry ID', 'Created At', 'IP Address' );
			if ( isset( $first['fields'] ) ) {
				foreach ( array_keys( $first['fields'] ) as $fid ) {
					$headers[] = $fid;
				}
			}
			fputcsv( $output, $headers );

			foreach ( $items as $item ) {
				$row = array(
					$this->sanitize_csv_cell( $item['id'] ),
					$this->sanitize_csv_cell( $item['created_at'] ),
					$this->sanitize_csv_cell( $item['ip_address'] ),
				);
				if ( isset( $item['fields'] ) ) {
					foreach ( $item['fields'] as $val ) {
						$row[] = $this->sanitize_csv_cell( $val );
					}
				}
				fputcsv( $output, $row );
			}
		}
		fclose( $output );
		exit;
	}

	private function sanitize_csv_cell( $val ) {
		$str = is_array( $val ) ? wp_json_encode( $val ) : (string) $val;
		if ( '' !== $str && in_array( substr( $str, 0, 1 ), array( '=', '+', '-', '@' ), true ) ) {
			return "'" . $str;
		}
		return $str;
	}

	public function get_settings( \WP_REST_Request $request ) {
		$defaults = array(
			'recaptcha_site_key'    => '',
			'recaptcha_secret_key'  => '',
			'turnstile_site_key'    => '',
			'turnstile_secret_key'  => '',
			'hcaptcha_site_key'     => '',
			'hcaptcha_secret_key'   => '',
			'stripe_publishable'    => '',
			'stripe_secret'         => '',
			'stripe_webhook_secret' => '',
			'mailchimp_api_key'     => '',
			'delete_on_uninstall'   => false,
		);
		$settings = wp_parse_args( get_option( 'formsvox_settings', array() ), $defaults );
		return rest_ensure_response( $settings );
	}

	public function update_settings( \WP_REST_Request $request ) {
		$params   = $request->get_params();
		$settings = array();
		$allowed  = array(
			'recaptcha_site_key',
			'recaptcha_secret_key',
			'turnstile_site_key',
			'turnstile_secret_key',
			'hcaptcha_site_key',
			'hcaptcha_secret_key',
			'stripe_publishable',
			'stripe_secret',
			'stripe_webhook_secret',
			'mailchimp_api_key',
			'voicecore_api_key',
			'ai_transcript_retention',
			'ai_daily_cap',
		);

		foreach ( $allowed as $key ) {
			if ( isset( $params[ $key ] ) ) {
				$settings[ $key ] = sanitize_text_field( $params[ $key ] );
			}
		}

		if ( isset( $params['delete_on_uninstall'] ) ) {
			$settings['delete_on_uninstall'] = rest_sanitize_boolean( $params['delete_on_uninstall'] );
		}

		update_option( 'formsvox_settings', $settings );
		return rest_ensure_response( array( 'success' => true, 'settings' => $settings ) );
	}

	public function get_ai_account( \WP_REST_Request $request ) {
		$status = \FormsVox\AI\Connection::get_status();
		return rest_ensure_response( $status );
	}

	public function sync_ai_content( \WP_REST_Request $request ) {
		$count = \FormsVox\AI\Ingest::get_instance()->sync_content();
		return rest_ensure_response( array( 'success' => true, 'count' => $count ) );
	}

	public function get_ingest_status( \WP_REST_Request $request ) {
		$progress = \FormsVox\AI\Ingest::get_instance()->get_progress();
		return rest_ensure_response( $progress );
	}

	public function ai_chat_relay( \WP_REST_Request $request ) {
		$params  = $request->get_json_params();
		$form_id = isset( $params['form_id'] ) ? intval( $params['form_id'] ) : 0;
		$form    = \FormsVox\DB\FormModel::get( $form_id );

		if ( ! $form || 'publish' !== $form['status'] ) {
			return new \WP_Error( 'form_not_found', __( 'Form not found or unpublished.', 'formsvox' ), array( 'status' => 404 ) );
		}

		// 1. Honeypot check
		if ( ! empty( $params['formsvox_hp'] ) ) {
			return new \WP_Error( 'bot_detected', __( 'Spam activity detected.', 'formsvox' ), array( 'status' => 403 ) );
		}

		// 2. IP Rate Limiting (20 req / 60s per IP per form)
		$ip       = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
		$rate_key = 'formsvox_ai_rate_' . md5( $ip . '_' . $form_id );
		$count    = (int) get_transient( $rate_key );

		if ( $count >= 20 ) {
			return new \WP_Error( 'rate_limit_exceeded', __( 'Too many requests. Please slow down.', 'formsvox' ), array( 'status' => 429 ) );
		}
		set_transient( $rate_key, $count + 1, 60 );

		// 3. Per-site Daily Cap Enforcement
		$settings  = get_option( 'formsvox_settings', array() );
		$daily_cap = isset( $settings['ai_daily_cap'] ) ? intval( $settings['ai_daily_cap'] ) : 500;
		$today_key = 'formsvox_ai_daily_' . date( 'Ymd' );
		$daily_cnt = (int) get_option( $today_key, 0 );

		if ( $daily_cnt >= $daily_cap ) {
			return new \WP_Error( 'daily_cap_exceeded', __( 'Per-site daily AI request cap reached.', 'formsvox' ), array( 'status' => 429 ) );
		}
		update_option( $today_key, $daily_cnt + 1 );

		// Send SSE Headers
		if ( ! headers_sent() ) {
			header( 'Content-Type: text/event-stream' );
			header( 'Cache-Control: no-cache' );
			header( 'X-Accel-Buffering: no' );
		}

		$messages = isset( $params['messages'] ) && is_array( $params['messages'] ) ? $params['messages'] : array();
		$self_ref = $this;

		\FormsVox\AI\Client::stream_request( '/v1/chat', 'POST', array(
			'form_id'    => $form_id,
			'messages'   => $messages,
			'formSchema' => $form['schema'],
		), function( $chunk ) use ( $self_ref, $form_id, $messages ) {
			echo $chunk;
			if ( ob_get_level() > 0 ) {
				ob_flush();
			}
			flush();

			if ( strpos( $chunk, 'tool_call' ) !== false ) {
				$lines = explode( "\n\n", $chunk );
				foreach ( $lines as $line ) {
					if ( strpos( $line, 'data: ' ) === 0 ) {
						$raw  = substr( $line, 6 );
						$json = json_decode( $raw, true );
						if ( isset( $json['type'] ) && 'tool_call' === $json['type'] ) {
							if ( 'submit_form' === $json['name'] ) {
								$fields = isset( $json['arguments']['fields'] ) ? $json['arguments']['fields'] : array();
								$score  = isset( $json['arguments']['score'] ) ? $json['arguments']['score'] : null;
								$self_ref->process_ai_submission( $form_id, $fields, $messages, $score );
							}
						}
					}
				}
			}
		} );

		exit;
	}

	public function process_ai_submission( $form_id, $raw_fields = array(), $messages = array(), $score = null ) {
		$form = FormModel::get( $form_id );
		if ( ! $form || 'publish' !== $form['status'] ) {
			return false;
		}

		$schema           = isset( $form['schema'] ) ? $form['schema'] : array();
		$fields           = isset( $schema['fields'] ) && is_array( $schema['fields'] ) ? $schema['fields'] : array();
		$registry         = \FormsVox\Fields\FieldRegistry::get_instance();
		$sanitized_fields = array();

		foreach ( $fields as $field_config ) {
			$fid       = isset( $field_config['id'] ) ? $field_config['id'] : '';
			$type      = isset( $field_config['type'] ) ? $field_config['type'] : 'text';
			$raw_val   = isset( $raw_fields[ $fid ] ) ? $raw_fields[ $fid ] : '';
			$field_obj = $registry->get_field( $type );

			if ( $field_obj ) {
				$val = $field_obj->sanitize( $raw_val, $field_config );
				if ( ! empty( $field_config['required'] ) && ( null === $val || '' === $val || ( is_array( $val ) && empty( $val ) ) ) ) {
					return false;
				}
				$sanitized_fields[ $fid ] = $val;
			}
		}

		$entry_id = EntryModel::create( $form_id, $sanitized_fields );

		// Save AI meta
		EntryModel::add_meta( $entry_id, '_ai_transcript', $messages );
		if ( null !== $score ) {
			EntryModel::add_meta( $entry_id, '_ai_score', (int) $score );
		}

		// Notifications
		$notifications = isset( $schema['notifications'] ) ? $schema['notifications'] : array();
		\FormsVox\Notifications\EmailEngine::get_instance()->send_notifications( $notifications, $form_id, $entry_id, $sanitized_fields );

		return $entry_id;
	}

	public function get_templates( \WP_REST_Request $request ) {
		$templates = \FormsVox\Templates\TemplateManager::get_all();
		return rest_ensure_response( $templates );
	}

	public function import_wpforms( \WP_REST_Request $request ) {
		$json_str = $request->get_param( 'json' );
		$imported = \FormsVox\Importers\WPFormsImporter::import( $json_str );
		return rest_ensure_response( $imported );
	}
}
