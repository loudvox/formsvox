<?php

namespace FormsVox\Tests;

use PHPUnit\Framework\TestCase;
use FormsVox\DB\FormModel;
use FormsVox\DB\EntryModel;

class AIChatRelayTest extends TestCase {
	public function test_rate_limiting_transient_key_generation() {
		$ip = '192.168.1.100';
		$form_id = 5;
		$rate_key = 'formsvox_ai_rate_' . md5( $ip . '_' . $form_id );

		$this->assertStringStartsWith( 'formsvox_ai_rate_', $rate_key );
		$this->assertEquals( 49, strlen( $rate_key ) );
	}

	public function test_entry_meta_add_meta_for_ai_transcript() {
		// Mock entry creation and meta storage
		$form_id = FormModel::create( 'AI Test Form', array( 'fields' => array() ) );
		$entry_id = EntryModel::create( $form_id, array( 'name_1' => 'Jane Smith' ) );

		EntryModel::add_meta( $entry_id, '_ai_transcript', array(
			array( 'role' => 'assistant', 'content' => 'Hello! What is your name?' ),
			array( 'role' => 'user', 'content' => 'Jane Smith' ),
		) );

		EntryModel::add_meta( $entry_id, '_ai_score', 92 );

		$entry = EntryModel::get( $entry_id );
		$this->assertNotNull( $entry );
		$this->assertEquals( 'Jane Smith', $entry['fields']['name_1'] );
		$this->assertEquals( '92', $entry['meta']['_ai_score'] );
	}
}
