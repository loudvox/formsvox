<?php

namespace FormsVox\Notifications;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Notifications and Smart Tags Engine.
 */
class EmailEngine {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function send_notifications( $form, $entry_id, $submitted_fields ) {
		$schema        = isset( $form['schema'] ) ? $form['schema'] : array();
		$notifications = isset( $schema['notifications'] ) && is_array( $schema['notifications'] ) ? $schema['notifications'] : array();

		foreach ( $notifications as $notification ) {
			// Check conditional logic routing for notification
			if ( ! empty( $notification['conditional_logic'] ) && ! \FormsVox\Logic\Evaluator::evaluate( $notification['conditional_logic'], $submitted_fields ) ) {
				continue;
			}

			$to      = $this->parse_smart_tags( $notification['to_email'], $form, $entry_id, $submitted_fields );
			$subject = $this->parse_smart_tags( $notification['subject'], $form, $entry_id, $submitted_fields );
			$body    = $this->parse_smart_tags( $notification['message'], $form, $entry_id, $submitted_fields );

			if ( empty( $to ) ) {
				$to = get_option( 'admin_email' );
			}

			/**
			 * Filter notification email attributes.
			 */
			$email_data = apply_filters( 'formsvox_notification_email', array(
				'to'      => $to,
				'subject' => $subject,
				'body'    => $body,
				'headers' => array( 'Content-Type: text/html; charset=UTF-8' ),
			), $form, $entry_id );

			wp_mail( $email_data['to'], $email_data['subject'], $email_data['body'], $email_data['headers'] );
		}
	}

	public function parse_smart_tags( $content, $form, $entry_id, $submitted_fields ) {
		$content = str_replace( '{admin_email}', get_option( 'admin_email' ), $content );
		$content = str_replace( '{entry_id}', (string) $entry_id, $content );
		$content = str_replace( '{form_name}', isset( $form['title'] ) ? $form['title'] : 'Form', $content );
		$content = str_replace( '{page_url}', wp_get_referer() ? wp_get_referer() : home_url(), $content );

		// Parse {all_fields}
		if ( false !== strpos( $content, '{all_fields}' ) ) {
			$fields_table = '<table style="width:100%; border-collapse:collapse;" border="1" cellpadding="6">';
			foreach ( $submitted_fields as $fid => $fval ) {
				$val_str = is_array( $fval ) ? wp_json_encode( $fval ) : esc_html( $fval );
				$fields_table .= sprintf( '<tr><td><strong>%s</strong></td><td>%s</td></tr>', esc_html( $fid ), $val_str );
			}
			$fields_table .= '</table>';
			$content = str_replace( '{all_fields}', $fields_table, $content );
		}

		// Parse {field_id="id"}
		$content = preg_replace_callback( '/\{field_id="([^"]+)"\}/', function( $matches ) use ( $submitted_fields ) {
			$fid = $matches[1];
			if ( isset( $submitted_fields[ $fid ] ) ) {
				return is_array( $submitted_fields[ $fid ] ) ? wp_json_encode( $submitted_fields[ $fid ] ) : esc_html( $submitted_fields[ $fid ] );
			}
			return '';
		}, $content );

		return $content;
	}
}
