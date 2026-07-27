<?php

namespace FormVox\API;

use FormVox\DB\FormModel;
use FormVox\DB\EntryModel;
use FormVox\AntiSpam\Honeypot;
use FormVox\Notifications\EmailEngine;
use FormVox\Integrations\IntegrationManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FormVox REST API Controller.
 */
class RestServer {
	private static $instance = null;
	const NAMESPACE = 'formvox/v1';

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
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'export_entries' ),
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
			return new \WP_Error( 'not_found', __( 'Form not found.', 'formvox' ), array( 'status' => 404 ) );
		}
		return rest_ensure_response( $form );
	}

	public function create_form( \WP_REST_Request $request ) {
		$title  = $request->get_param( 'title' );
		$schema = $request->get_param( 'schema' );

		if ( empty( $title ) ) {
			$title = __( 'Untitled Form', 'formvox' );
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
			return new \WP_Error( 'form_not_found', __( 'Form not found.', 'formvox' ), array( 'status' => 404 ) );
		}

		// Enforce form published status
		if ( isset( $form['status'] ) && 'publish' !== $form['status'] ) {
			return new \WP_Error( 'form_not_published', __( 'This form is not active or published.', 'formvox' ), array( 'status' => 403 ) );
		}

		// IP-based rate limiting (Max 10 submissions per 60 seconds per IP/form)
		$ip            = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
		$transient_key = 'formvox_rate_' . md5( $ip . '_' . $form_id );
		$rate_count    = (int) get_transient( $transient_key );
		if ( $rate_count >= 10 ) {
			return new \WP_Error( 'rate_limit_exceeded', __( 'Too many submissions. Please wait a minute and try again.', 'formvox' ), array( 'status' => 429 ) );
		}
		set_transient( $transient_key, $rate_count + 1, 60 );

		$params = $request->get_params();

		// Anti-Spam Honeypot & Time-Trap Check
		$honeypot = Honeypot::get_instance();
		if ( ! $honeypot->verify( $params ) ) {
			return new \WP_Error( 'spam_detected', __( 'Spam detection triggered.', 'formvox' ), array( 'status' => 400 ) );
		}

		// Server-Side CAPTCHA Verification (reCAPTCHA, Turnstile, hCaptcha)
		$captcha_result = \FormVox\AntiSpam\CaptchaVerifier::verify( $params );
		if ( is_wp_error( $captcha_result ) ) {
			return $captcha_result;
		}

		$raw_fields = isset( $params['formvox_fields'] ) && is_array( $params['formvox_fields'] ) ? $params['formvox_fields'] : array();
		$schema     = isset( $form['schema'] ) ? $form['schema'] : array();
		$form_fields = isset( $schema['fields'] ) && is_array( $schema['fields'] ) ? $schema['fields'] : array();

		$registry         = \FormVox\Fields\FieldRegistry::get_instance();
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
			if ( ! empty( $field_config['conditional_logic'] ) && ! \FormVox\Logic\Evaluator::evaluate( $field_config['conditional_logic'], $raw_fields ) ) {
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
				__( 'Validation failed for one or more fields.', 'formvox' ),
				array(
					'status' => 400,
					'errors' => $errors,
				)
			);
		}

		// Create Entry
		$entry_id = EntryModel::create( $form_id, $sanitized_fields );

		/**
		 * Action Hook: formvox_process_entry
		 *
		 * @param int   $entry_id Entry ID.
		 * @param array $form     Form row array.
		 * @param array $fields_data Submitted fields data.
		 */
		do_action( 'formvox_process_entry', $entry_id, $form, $sanitized_fields );

		// Process Email Notifications & Integrations
		EmailEngine::get_instance()->send_notifications( $form, $entry_id, $sanitized_fields );
		IntegrationManager::get_instance()->process_submission( $form, $entry_id, $sanitized_fields );

		// Confirmation Response with Conditional Routing
		$schema              = isset( $form['schema'] ) ? $form['schema'] : array();
		$all_confirmations   = isset( $schema['confirmations'] ) && is_array( $schema['confirmations'] ) ? $schema['confirmations'] : array();
		$valid_confirmations = array();

		foreach ( $all_confirmations as $conf ) {
			if ( ! empty( $conf['conditional_logic'] ) && ! \FormVox\Logic\Evaluator::evaluate( $conf['conditional_logic'], $sanitized_fields ) ) {
				continue;
			}
			$valid_confirmations[] = $conf;
		}

		if ( empty( $valid_confirmations ) ) {
			$valid_confirmations[] = array(
				'type'    => 'message',
				'message' => __( 'Thank you! Your submission has been received.', 'formvox' ),
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
			return new \WP_Error( 'not_found', __( 'Entry not found.', 'formvox' ), array( 'status' => 404 ) );
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
		header( 'Content-Disposition: attachment; filename="formvox-entries-' . $form_id . '.csv"' );

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
				$row = array( $item['id'], $item['created_at'], $item['ip_address'] );
				if ( isset( $item['fields'] ) ) {
					foreach ( $item['fields'] as $val ) {
						$row[] = is_array( $val ) ? wp_json_encode( $val ) : $val;
					}
				}
				fputcsv( $output, $row );
			}
		}
		fclose( $output );
		exit;
	}

	public function get_settings( \WP_REST_Request $request ) {
		$settings = get_option( 'formvox_settings', array(
			'recaptcha_site_key'   => '',
			'recaptcha_secret_key' => '',
			'turnstile_site_key'   => '',
			'turnstile_secret_key' => '',
			'stripe_publishable'   => '',
			'stripe_secret'        => '',
			'mailchimp_api_key'    => '',
			'delete_on_uninstall'  => false,
		) );
		return rest_ensure_response( $settings );
	}

	public function update_settings( \WP_REST_Request $request ) {
		$settings = $request->get_params();
		update_option( 'formvox_settings', $settings );
		return rest_ensure_response( array( 'success' => true ) );
	}

	public function get_templates( \WP_REST_Request $request ) {
		$templates = \FormVox\Templates\TemplateManager::get_all();
		return rest_ensure_response( $templates );
	}

	public function import_wpforms( \WP_REST_Request $request ) {
		$json_str = $request->get_param( 'json' );
		$imported = \FormVox\Importers\WPFormsImporter::import( $json_str );
		return rest_ensure_response( $imported );
	}
}
