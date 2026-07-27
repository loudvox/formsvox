<?php

namespace FormVox\Tests;

use PHPUnit\Framework\TestCase;
use FormVox\Notifications\EmailEngine;

class SmartTagParserTest extends TestCase {
	public function test_smart_tags_replacement() {
		$engine = EmailEngine::get_instance();
		$form   = array( 'title' => 'Contact Us' );
		$entry_id = 105;
		$submitted_fields = array(
			'email_1' => 'user@example.com',
			'name_1'  => 'John Doe',
		);

		$template = 'Submission #{entry_id} from {field_id="name_1"}';
		$result   = $engine->parse_smart_tags( $template, $form, $entry_id, $submitted_fields );

		$this->assertEquals( 'Submission #105 from John Doe', $result );
	}
}
