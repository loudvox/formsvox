<?php

namespace FormVox\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TemplateManager {
	public static function get_all() {
		$templates = array();

		$types = array(
			'contact'      => __( 'Simple Contact Form', 'formvox' ),
			'quote'        => __( 'Request a Quote Form', 'formvox' ),
			'registration' => __( 'Event Registration Form', 'formvox' ),
			'survey'       => __( 'Customer Satisfaction Survey', 'formvox' ),
			'order'        => __( 'Product Order Form', 'formvox' ),
			'donation'     => __( 'Non-Profit Donation Form', 'formvox' ),
			'application'  => __( 'Job Application Form', 'formvox' ),
			'feedback'     => __( 'Website Feedback Form', 'formvox' ),
			'booking'      => __( 'Appointment Booking Form', 'formvox' ),
			'newsletter'   => __( 'Newsletter Signup Form', 'formvox' ),
			'rsvp'         => __( 'Wedding RSVP Form', 'formvox' ),
			'volunteer'    => __( 'Volunteer Signup Form', 'formvox' ),
			'support'      => __( 'IT Support Ticket Form', 'formvox' ),
			'contest'      => __( 'Contest Entry Form', 'formvox' ),
			'scholarship'  => __( 'Scholarship Application Form', 'formvox' ),
			'membership'   => __( 'Club Membership Form', 'formvox' ),
			'catering'     => __( 'Catering Request Form', 'formvox' ),
			'vendor'       => __( 'Vendor Application Form', 'formvox' ),
			'rental'       => __( 'Property Rental Application', 'formvox' ),
			'nps_survey'   => __( 'Net Promoter Score (NPS) Survey', 'formvox' ),
		);

		foreach ( $types as $slug => $title ) {
			$templates[] = array(
				'id'          => $slug,
				'title'       => $title,
				'description' => sprintf( __( 'Pre-configured %s template ready for instant deployment.', 'formvox' ), strtolower( $title ) ),
				'schema'      => array(
					'settings'      => array(
						'title'       => $title,
						'description' => __( 'Please fill out all required fields below.', 'formvox' ),
						'submit_text' => __( 'Submit', 'formvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Name', 'formvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email', 'formvox' ), 'required' => true ),
						array( 'id' => 'text_1', 'type' => 'text', 'label' => __( 'Subject', 'formvox' ) ),
						array( 'id' => 'textarea_1', 'type' => 'textarea', 'label' => __( 'Message', 'formvox' ), 'required' => true ),
					),
					'notifications' => array(
						array(
							'id'       => 'notif_1',
							'name'     => __( 'Admin Notification', 'formvox' ),
							'to_email' => '{admin_email}',
							'subject'  => sprintf( __( 'New Submission from %s', 'formvox' ), $title ),
							'message'  => '{all_fields}',
						),
					),
					'confirmations' => array(
						array(
							'id'      => 'conf_1',
							'type'    => 'message',
							'message' => __( 'Thank you! Your submission has been received.', 'formvox' ),
						),
					),
				),
			);
		}

		return $templates;
	}
}
