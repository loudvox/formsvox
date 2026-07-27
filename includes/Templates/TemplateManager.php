<?php

namespace FormsVox\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pre-Built Form Templates Repository Manager.
 */
class TemplateManager {
	/**
	 * Get all 20 pre-configured JSON form templates.
	 *
	 * @return array
	 */
	public static function get_all() {
		return array(
			// 1. Simple Contact Form
			array(
				'id'          => 'contact',
				'title'       => __( 'Simple Contact Form', 'formsvox' ),
				'description' => __( 'Standard contact form for general inquiries.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Contact Us', 'formsvox' ),
						'description' => __( 'Send us a message and we will respond as soon as possible.', 'formsvox' ),
						'submit_text' => __( 'Send Message', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Full Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'subject_1', 'type' => 'text', 'label' => __( 'Subject', 'formsvox' ), 'required' => true ),
						array( 'id' => 'message_1', 'type' => 'textarea', 'label' => __( 'Message', 'formsvox' ), 'required' => true ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Admin Email', 'to_email' => '{admin_email}', 'subject' => 'New Contact Form Submission', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Thank you for reaching out! We will be in touch shortly.', 'formsvox' ) ),
					),
				),
			),

			// 2. Request a Quote Form
			array(
				'id'          => 'quote',
				'title'       => __( 'Request a Quote Form', 'formsvox' ),
				'description' => __( 'Capture project scope, timeline, and budget for prospective clients.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Request a Custom Quote', 'formsvox' ),
						'description' => __( 'Provide details about your project to receive an estimate.', 'formsvox' ),
						'submit_text' => __( 'Request Quote', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Full Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'phone_1', 'type' => 'phone', 'label' => __( 'Phone Number', 'formsvox' ) ),
						array( 'id' => 'service_1', 'type' => 'select', 'label' => __( 'Service Required', 'formsvox' ), 'options' => array( array( 'label' => 'Web Design', 'value' => 'web_design' ), array( 'label' => 'SEO', 'value' => 'seo' ), array( 'label' => 'Custom Development', 'value' => 'dev' ) ) ),
						array( 'id' => 'budget_1', 'type' => 'select', 'label' => __( 'Estimated Budget', 'formsvox' ), 'options' => array( array( 'label' => '< $1,000', 'value' => 'under_1k' ), array( 'label' => '$1,000 - $5,000', 'value' => '1k_5k' ), array( 'label' => '$5,000+', 'value' => '5k_plus' ) ) ),
						array( 'id' => 'details_1', 'type' => 'textarea', 'label' => __( 'Project Details', 'formsvox' ), 'required' => true ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Quote Request', 'to_email' => '{admin_email}', 'subject' => 'New Quote Request from {field_id="name_1"}', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Thank you! Our team is reviewing your project details.', 'formsvox' ) ),
					),
				),
			),

			// 3. Event Registration Form
			array(
				'id'          => 'registration',
				'title'       => __( 'Event Registration Form', 'formsvox' ),
				'description' => __( 'Collect attendee details, dietary preferences, and ticket count.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Event Registration', 'formsvox' ),
						'description' => __( 'Register your spot for our upcoming event.', 'formsvox' ),
						'submit_text' => __( 'Complete Registration', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Attendee Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'tickets_1', 'type' => 'number', 'label' => __( 'Number of Tickets', 'formsvox' ), 'min' => 1, 'max' => 10, 'default_val' => '1' ),
						array( 'id' => 'diet_1', 'type' => 'checkbox', 'label' => __( 'Dietary Requirements', 'formsvox' ), 'options' => array( array( 'label' => 'Vegetarian', 'value' => 'veg' ), array( 'label' => 'Vegan', 'value' => 'vegan' ), array( 'label' => 'Gluten-Free', 'value' => 'gf' ) ) ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Registration Alert', 'to_email' => '{admin_email}', 'subject' => 'New Event Registration', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Your registration is confirmed!', 'formsvox' ) ),
					),
				),
			),

			// 4. Customer Satisfaction Survey
			array(
				'id'          => 'survey',
				'title'       => __( 'Customer Satisfaction Survey', 'formsvox' ),
				'description' => __( 'Gather rating metrics and feedback on customer experiences.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Customer Experience Survey', 'formsvox' ),
						'description' => __( 'Help us improve our service by leaving feedback.', 'formsvox' ),
						'submit_text' => __( 'Submit Feedback', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'rating_1', 'type' => 'rating', 'label' => __( 'Overall Satisfaction', 'formsvox' ), 'required' => true ),
						array( 'id' => 'likert_1', 'type' => 'likert', 'label' => __( 'Service Quality Assessment', 'formsvox' ) ),
						array( 'id' => 'comments_1', 'type' => 'textarea', 'label' => __( 'Additional Comments', 'formsvox' ) ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Survey Alert', 'to_email' => '{admin_email}', 'subject' => 'New Survey Submission', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Thank you for your valuable feedback!', 'formsvox' ) ),
					),
				),
			),

			// 5. Product Order Form
			array(
				'id'          => 'order',
				'title'       => __( 'Product Order Form', 'formsvox' ),
				'description' => __( 'Sell single or multiple items with calculated total pricing.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Place Your Order', 'formsvox' ),
						'description' => __( 'Select items and provide shipping information.', 'formsvox' ),
						'submit_text' => __( 'Place Order', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Customer Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'address_1', 'type' => 'address', 'label' => __( 'Shipping Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'item_1', 'type' => 'payment_multiple', 'label' => __( 'Select Product', 'formsvox' ), 'required' => true ),
						array( 'id' => 'total_1', 'type' => 'payment_total', 'label' => __( 'Order Total', 'formsvox' ) ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'New Order', 'to_email' => '{admin_email}', 'subject' => 'New Product Order #{entry_id}', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Thank you for your order! We are processing it now.', 'formsvox' ) ),
					),
				),
			),

			// 6. Non-Profit Donation Form
			array(
				'id'          => 'donation',
				'title'       => __( 'Non-Profit Donation Form', 'formsvox' ),
				'description' => __( 'Collect custom online donations for non-profit organizations.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Support Our Cause', 'formsvox' ),
						'description' => __( 'Your donation helps fund our community programs.', 'formsvox' ),
						'submit_text' => __( 'Donate Now', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Donor Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'amount_1', 'type' => 'payment_single', 'label' => __( 'Donation Amount ($)', 'formsvox' ), 'price' => '25.00' ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Donation Alert', 'to_email' => '{admin_email}', 'subject' => 'New Donation Received', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Thank you for your generous donation!', 'formsvox' ) ),
					),
				),
			),

			// 7. Job Application Form
			array(
				'id'          => 'application',
				'title'       => __( 'Job Application Form', 'formsvox' ),
				'description' => __( 'Collect applicant resumes, portfolios, and work histories.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Careers & Job Application', 'formsvox' ),
						'description' => __( 'Apply for an open position at our company.', 'formsvox' ),
						'submit_text' => __( 'Submit Application', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Full Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'phone_1', 'type' => 'phone', 'label' => __( 'Phone Number', 'formsvox' ), 'required' => true ),
						array( 'id' => 'resume_1', 'type' => 'file_upload', 'label' => __( 'Resume / CV (PDF/DOCX)', 'formsvox' ), 'required' => true ),
						array( 'id' => 'cover_1', 'type' => 'textarea', 'label' => __( 'Cover Letter', 'formsvox' ) ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Job Application', 'to_email' => '{admin_email}', 'subject' => 'New Application for {field_id="name_1"}', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Application received! Our HR team will review your application.', 'formsvox' ) ),
					),
				),
			),

			// 8. Website Feedback Form
			array(
				'id'          => 'feedback',
				'title'       => __( 'Website Feedback Form', 'formsvox' ),
				'description' => __( 'Bug reports, design suggestions, and website feedback.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Website Feedback', 'formsvox' ),
						'description' => __( 'Let us know how we can improve our website.', 'formsvox' ),
						'submit_text' => __( 'Submit Feedback', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'category_1', 'type' => 'select', 'label' => __( 'Feedback Type', 'formsvox' ), 'options' => array( array( 'label' => 'Bug Report', 'value' => 'bug' ), array( 'label' => 'Feature Request', 'value' => 'feature' ), array( 'label' => 'General Comment', 'value' => 'general' ) ) ),
						array( 'id' => 'url_1', 'type' => 'url', 'label' => __( 'Page URL', 'formsvox' ) ),
						array( 'id' => 'comments_1', 'type' => 'textarea', 'label' => __( 'Your Feedback', 'formsvox' ), 'required' => true ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Feedback Alert', 'to_email' => '{admin_email}', 'subject' => 'Website Feedback Received', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Thanks for helping us improve our website!', 'formsvox' ) ),
					),
				),
			),

			// 9. Appointment Booking Form
			array(
				'id'          => 'booking',
				'title'       => __( 'Appointment Booking Form', 'formsvox' ),
				'description' => __( 'Schedule dates and times for client consultations.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Schedule an Appointment', 'formsvox' ),
						'description' => __( 'Select a date and time for your session.', 'formsvox' ),
						'submit_text' => __( 'Book Appointment', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Client Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'datetime_1', 'type' => 'date_time', 'label' => __( 'Requested Date & Time', 'formsvox' ), 'required' => true ),
						array( 'id' => 'notes_1', 'type' => 'textarea', 'label' => __( 'Consultation Notes', 'formsvox' ) ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Booking Alert', 'to_email' => '{admin_email}', 'subject' => 'New Appointment Request', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Your appointment request has been submitted.', 'formsvox' ) ),
					),
				),
			),

			// 10. Newsletter Signup Form
			array(
				'id'          => 'newsletter',
				'title'       => __( 'Newsletter Signup Form', 'formsvox' ),
				'description' => __( 'Clean email opt-in form for building mailing lists.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Subscribe to Our Newsletter', 'formsvox' ),
						'description' => __( 'Get the latest news and updates delivered to your inbox.', 'formsvox' ),
						'submit_text' => __( 'Subscribe', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'text', 'label' => __( 'First Name', 'formsvox' ) ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Subscriber Alert', 'to_email' => '{admin_email}', 'subject' => 'New Newsletter Subscriber', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Welcome! You are now subscribed to our newsletter.', 'formsvox' ) ),
					),
				),
			),

			// 11. Wedding RSVP Form
			array(
				'id'          => 'rsvp',
				'title'       => __( 'Wedding RSVP Form', 'formsvox' ),
				'description' => __( 'Collect guest responses, party sizes, and meal choices.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Wedding RSVP', 'formsvox' ),
						'description' => __( 'Please let us know if you can attend.', 'formsvox' ),
						'submit_text' => __( 'Send RSVP', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Guest Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'attending_1', 'type' => 'radio', 'label' => __( 'Will you attend?', 'formsvox' ), 'options' => array( array( 'label' => 'Joyfully Accepts', 'value' => 'yes' ), array( 'label' => 'Regretfully Declines', 'value' => 'no' ) ), 'required' => true ),
						array( 'id' => 'plus_one_1', 'type' => 'number', 'label' => __( 'Total Guests in Party', 'formsvox' ), 'min' => 1, 'max' => 5 ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'RSVP Alert', 'to_email' => '{admin_email}', 'subject' => 'New RSVP from {field_id="name_1"}', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Thank you for your RSVP!', 'formsvox' ) ),
					),
				),
			),

			// 12. Volunteer Signup Form
			array(
				'id'          => 'volunteer',
				'title'       => __( 'Volunteer Signup Form', 'formsvox' ),
				'description' => __( 'Recruit volunteers, availability schedules, and skillsets.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Volunteer Registration', 'formsvox' ),
						'description' => __( 'Join our volunteer network and make a difference.', 'formsvox' ),
						'submit_text' => __( 'Sign Up to Volunteer', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Volunteer Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'skills_1', 'type' => 'checkbox', 'label' => __( 'Areas of Interest', 'formsvox' ), 'options' => array( array( 'label' => 'Event Setup', 'value' => 'events' ), array( 'label' => 'Fundraising', 'value' => 'fundraising' ), array( 'label' => 'Marketing', 'value' => 'marketing' ) ) ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Volunteer Alert', 'to_email' => '{admin_email}', 'subject' => 'New Volunteer Signup', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Thank you for volunteering with us!', 'formsvox' ) ),
					),
				),
			),

			// 13. IT Support Ticket Form
			array(
				'id'          => 'support',
				'title'       => __( 'IT Support Ticket Form', 'formsvox' ),
				'description' => __( 'Technical support request form with urgency levels.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Submit a Support Ticket', 'formsvox' ),
						'description' => __( 'Describe your technical issue below.', 'formsvox' ),
						'submit_text' => __( 'Create Ticket', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Your Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'priority_1', 'type' => 'select', 'label' => __( 'Urgency Level', 'formsvox' ), 'options' => array( array( 'label' => 'Low', 'value' => 'low' ), array( 'label' => 'Medium', 'value' => 'medium' ), array( 'label' => 'Critical', 'value' => 'critical' ) ) ),
						array( 'id' => 'issue_1', 'type' => 'textarea', 'label' => __( 'Issue Description', 'formsvox' ), 'required' => true ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Support Ticket Alert', 'to_email' => '{admin_email}', 'subject' => 'New IT Support Ticket', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Ticket submitted! A support agent will investigate shortly.', 'formsvox' ) ),
					),
				),
			),

			// 14. Contest Entry Form
			array(
				'id'          => 'contest',
				'title'       => __( 'Contest Entry Form', 'formsvox' ),
				'description' => __( 'Run giveaways and contest submissions.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Enter Our Giveaway Contest', 'formsvox' ),
						'description' => __( 'Complete the form for a chance to win.', 'formsvox' ),
						'submit_text' => __( 'Enter Contest', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Full Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'phone_1', 'type' => 'phone', 'label' => __( 'Phone Number', 'formsvox' ), 'required' => true ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Contest Alert', 'to_email' => '{admin_email}', 'subject' => 'New Contest Entry', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Good luck! Your entry has been recorded.', 'formsvox' ) ),
					),
				),
			),

			// 15. Scholarship Application Form
			array(
				'id'          => 'scholarship',
				'title'       => __( 'Scholarship Application Form', 'formsvox' ),
				'description' => __( 'Academic scholarship grant submission form.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Scholarship Grant Application', 'formsvox' ),
						'description' => __( 'Apply for academic scholarship funding.', 'formsvox' ),
						'submit_text' => __( 'Submit Grant Application', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Student Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'gpa_1', 'type' => 'number', 'label' => __( 'Current GPA', 'formsvox' ), 'step' => '0.01' ),
						array( 'id' => 'essay_1', 'type' => 'textarea', 'label' => __( 'Personal Statement Essay', 'formsvox' ), 'required' => true ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Scholarship Alert', 'to_email' => '{admin_email}', 'subject' => 'New Scholarship Application', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Application received by the scholarship committee.', 'formsvox' ) ),
					),
				),
			),

			// 16. Club Membership Form
			array(
				'id'          => 'membership',
				'title'       => __( 'Club Membership Form', 'formsvox' ),
				'description' => __( 'Membership signup form for sports or social clubs.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Join Our Club', 'formsvox' ),
						'description' => __( 'Apply for annual club membership.', 'formsvox' ),
						'submit_text' => __( 'Apply for Membership', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Applicant Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Email Address', 'formsvox' ), 'required' => true ),
						array( 'id' => 'type_1', 'type' => 'select', 'label' => __( 'Membership Tier', 'formsvox' ), 'options' => array( array( 'label' => 'Standard', 'value' => 'standard' ), array( 'label' => 'VIP Gold', 'value' => 'vip' ) ) ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Membership Alert', 'to_email' => '{admin_email}', 'subject' => 'New Membership Application', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Welcome to the club!', 'formsvox' ) ),
					),
				),
			),

			// 17. Catering Request Form
			array(
				'id'          => 'catering',
				'title'       => __( 'Catering Request Form', 'formsvox' ),
				'description' => __( 'Food and catering service order inquiry form.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Catering Request', 'formsvox' ),
						'description' => __( 'Request catering for your private or corporate event.', 'formsvox' ),
						'submit_text' => __( 'Submit Catering Request', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Organizer Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'guests_1', 'type' => 'number', 'label' => __( 'Estimated Guest Count', 'formsvox' ), 'min' => 5 ),
						array( 'id' => 'date_1', 'type' => 'date_time', 'label' => __( 'Event Date & Time', 'formsvox' ), 'required' => true ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Catering Alert', 'to_email' => '{admin_email}', 'subject' => 'New Catering Order Request', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Catering inquiry submitted!', 'formsvox' ) ),
					),
				),
			),

			// 18. Vendor Application Form
			array(
				'id'          => 'vendor',
				'title'       => __( 'Vendor Application Form', 'formsvox' ),
				'description' => __( 'Vendor booth and tradeshow space application.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Vendor Booth Application', 'formsvox' ),
						'description' => __( 'Apply to exhibit your business at our annual expo.', 'formsvox' ),
						'submit_text' => __( 'Submit Vendor Application', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'company_1', 'type' => 'text', 'label' => __( 'Company / Brand Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'email_1', 'type' => 'email', 'label' => __( 'Business Email', 'formsvox' ), 'required' => true ),
						array( 'id' => 'website_1', 'type' => 'url', 'label' => __( 'Company Website', 'formsvox' ) ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Vendor Alert', 'to_email' => '{admin_email}', 'subject' => 'New Vendor Application', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Vendor application received.', 'formsvox' ) ),
					),
				),
			),

			// 19. Property Rental Application
			array(
				'id'          => 'rental',
				'title'       => __( 'Property Rental Application', 'formsvox' ),
				'description' => __( 'Tenant lease and rental application form.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'Rental Application', 'formsvox' ),
						'description' => __( 'Apply for residential property leasing.', 'formsvox' ),
						'submit_text' => __( 'Submit Rental Application', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'name_1', 'type' => 'name', 'label' => __( 'Applicant Name', 'formsvox' ), 'required' => true ),
						array( 'id' => 'income_1', 'type' => 'number', 'label' => __( 'Monthly Income ($)', 'formsvox' ), 'required' => true ),
						array( 'id' => 'pets_1', 'type' => 'radio', 'label' => __( 'Do you have pets?', 'formsvox' ), 'options' => array( array( 'label' => 'Yes', 'value' => 'yes' ), array( 'label' => 'No', 'value' => 'no' ) ) ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'Rental Alert', 'to_email' => '{admin_email}', 'subject' => 'New Rental Application', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Rental application submitted successfully.', 'formsvox' ) ),
					),
				),
			),

			// 20. Net Promoter Score (NPS) Survey
			array(
				'id'          => 'nps_survey',
				'title'       => __( 'Net Promoter Score (NPS) Survey', 'formsvox' ),
				'description' => __( 'Measure customer loyalty and recommendation likelihood.', 'formsvox' ),
				'schema'      => array(
					'settings'      => array(
						'title'       => __( 'NPS Loyalty Survey', 'formsvox' ),
						'description' => __( 'How likely are you to recommend us to a friend or colleague?', 'formsvox' ),
						'submit_text' => __( 'Submit NPS Score', 'formsvox' ),
						'ajax_submit' => true,
					),
					'fields'        => array(
						array( 'id' => 'nps_1', 'type' => 'nps', 'label' => __( 'Recommendation Score', 'formsvox' ), 'required' => true ),
						array( 'id' => 'reason_1', 'type' => 'textarea', 'label' => __( 'Reason for your score', 'formsvox' ) ),
					),
					'notifications' => array(
						array( 'id' => 'notif_1', 'name' => 'NPS Alert', 'to_email' => '{admin_email}', 'subject' => 'New NPS Score Submission', 'message' => '{all_fields}' ),
					),
					'confirmations' => array(
						array( 'id' => 'conf_1', 'type' => 'message', 'message' => __( 'Thank you for rating our business!', 'formsvox' ) ),
					),
				),
			),
		);
	}
}
