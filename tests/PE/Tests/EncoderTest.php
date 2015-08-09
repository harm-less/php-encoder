<?php

namespace PE\Tests;

use PE\Encoder;

class EncoderTest extends AbstractPETest {

	public function testConstructor()
	{
		$encoder = new Encoder();
		$this->assertNotNull($encoder);
		$this->assertTrue($encoder instanceof Encoder);
	}
}