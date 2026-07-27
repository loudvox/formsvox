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

		$params = $request->get_params();

		// Anti-Spam Check
		$honeypot = Honeypot::get_instance();
		if ( ! $honeypot->verify( $params ) ) {
			return new \WP_Error( 'spam_detected', __( 'Spam detection triggered.', 'formvox' ), array( 'status' => 400 ) );
		}

		$fields_data = isset( $params['formvox_fields'] ) && is_array( $params['formvox_fields'] ) ? $params['formvox_fields'] : array();

		// Create Entry
		$entry_id = EntryModel::create( $form_id, $fields_data );

		/**
		 * Action Hook: formvox_process_entry
		 *
		 * @param int   $entry_id Entry ID.
		 * @param array $form     Form row array.
		 * @param array $fields_data Submitted fields data.
		 */
		do_action( 'formvox_process_entry', $entry_id, $form, $fields_data );

		// Process Email Notifications & Integrations
		EmailEngine::get_instance()->send_notifications( $form, $entry_id, $fields_data );
		IntegrationManager::get_instance()->process_submission( $form, $entry_id, $fields_data );

		// Confirmation Response
		$schema        = isset( $form['schema'] ) ? $form['schema'] : array();
		$confirmations = isset( $schema['confirmations'] ) ? $schema['confirmations'] : array(
			array(
				'type'    => 'message',
				'message' => __( 'Thank you! Your submission has been received.', 'formvox' ),
			),
		);

		return rest_ensure_response( array(
			'success'       => true,
			'entry_id'      => $entry_id,
			'confirmations' => $confirmations,
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
