<?php

namespace PH\Tests;

use PH\Encoder;

class EncoderTest extends AbstractPHTest {

	public function testConstructor()
	{
		$files = new Encoder();
		$this->assertNotNull($files);
		$this->assertTrue($files instanceof Encoder);
	}
}