<?php

namespace FormsVox\Tests;

use PHPUnit\Framework\TestCase;
use FormsVox\AI\ModuleRegistry;
use FormsVox\AI\Connection;
use FormsVox\AI\Client;

class VoiceCoreAITest extends TestCase {
	public function test_ai_module_registry() {
		$registry = ModuleRegistry::get_instance();
		$modules  = $registry->get_modules();

		$this->assertArrayHasKey( 'forms_agent', $modules );
		$this->assertEquals( 'Conversational Forms Agent', $modules['forms_agent']['name'] );
	}

	public function test_connection_status_when_disconnected() {
		$status = Connection::get_status();
		$this->assertFalse( $status['connected'] );
	}

	public function test_client_missing_api_key() {
		$res = Client::request( '/v1/account', 'GET' );
		$this->assertTrue( is_wp_error( $res ) );
		$this->assertEquals( 'missing_api_key', $res->get_error_code() );
	}
}
