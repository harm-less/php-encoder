<?php

namespace PH\Tests;

use PE\Encoder;

class EncoderTest extends AbstractPETest {

	public function testConstructor()
	{
		$files = new Encoder();
		$this->assertNotNull($files);
		$this->assertTrue($files instanceof Encoder);
	}
}