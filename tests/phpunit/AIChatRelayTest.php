<?php

namespace FormsVox\Tests;

use PHPUnit\Framework\TestCase;
use FormsVox\DB\FormModel;
use FormsVox\DB\EntryModel;
use FormsVox\API\RestServer;

class AIChatRelayTest extends TestCase {
	public function test_rate_limiting_transient_key_generation() {
		$ip = '192.168.1.100';
		$form_id = 5;
		$rate_key = 'formsvox_ai_rate_' . md5( $ip . '_' . $form_id );

		$this->assertStringStartsWith( 'formsvox_ai_rate_', $rate_key );
		$this->assertEquals( 49, strlen( $rate_key ) );
	}

	public function test_process_ai_submission_creates_entry_with_transcript_and_score() {
		$form_id = FormModel::create( 'AI Relay Form', array(
			'fields' => array(
				array( 'id' => 'name_1', 'type' => 'text', 'label' => 'Name', 'required' => true ),
				array( 'id' => 'email_1', 'type' => 'email', 'label' => 'Email' ),
			),
		) );

		$raw_fields = array(
			'name_1'  => 'John Doe',
			'email_1' => 'john@example.com',
		);

		$messages = array(
			array( 'role' => 'assistant', 'content' => 'Hello!' ),
			array( 'role' => 'user', 'content' => 'John Doe' ),
		);

		$server   = RestServer::get_instance();
		$entry_id = $server->process_ai_submission( $form_id, $raw_fields, $messages, 88 );

		$this->assertNotFalse( $entry_id );
		$entry = EntryModel::get( $entry_id );
		$this->assertNotNull( $entry );
		$this->assertEquals( 'John Doe', $entry['fields']['name_1'] );
		$this->assertEquals( 'john@example.com', $entry['fields']['email_1'] );
		$this->assertEquals( '88', $entry['meta']['_ai_score'] );
		$this->assertNotEmpty( $entry['meta']['_ai_transcript'] );
	}

	public function test_non_ai_entry_has_no_ai_transcript_or_score() {
		$form_id  = FormModel::create( 'Standard Form', array( 'fields' => array() ) );
		$entry_id = EntryModel::create( $form_id, array( 'text_1' => 'Standard submission' ) );

		$entry = EntryModel::get( $entry_id );
		$this->assertNotNull( $entry );
		$this->assertArrayNotHasKey( '_ai_transcript', $entry['meta'] );
		$this->assertArrayNotHasKey( '_ai_score', $entry['meta'] );
	}

	public function test_ai_submission_with_invalid_email_is_rejected() {
		$form_id = FormModel::create( 'AI Validation Form', array(
			'fields' => array(
				array( 'id' => 'email_1', 'type' => 'email', 'label' => 'Email Address', 'required' => true ),
			),
		) );

		$raw_fields = array(
			'email_1' => 'invalid-email-address',
		);

		$server = RestServer::get_instance();
		$result = $server->process_ai_submission( $form_id, $raw_fields, array() );

		$this->assertFalse( $result );
	}

	public function test_integrations_and_hooks_fire_on_ai_submission() {
		$form_id = FormModel::create( 'AI Hook Test Form', array(
			'fields' => array(
				array( 'id' => 'name_1', 'type' => 'text', 'label' => 'Name' ),
			),
		) );

		$fired = false;
		$captured_id = 0;
		add_action( 'formsvox_process_entry', function( $entry_id ) use ( &$fired, &$captured_id ) {
			$fired = true;
			$captured_id = $entry_id;
		}, 10, 1 );

		$server   = RestServer::get_instance();
		$entry_id = $server->process_ai_submission( $form_id, array( 'name_1' => 'Hook Tester' ), array() );

		$this->assertTrue( $fired );
		$this->assertEquals( $entry_id, $captured_id );
	}
}
