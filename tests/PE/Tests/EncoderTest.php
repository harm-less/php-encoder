<?php

namespace PE\Tests;

use PE\Encoder;
use PE\Options\EncoderOptions;

class EncoderTest extends Samples {

	protected function setUp()
	{
		$this->_peApp = new Encoder();
	}

	/**
	 * @return Encoder
	 */
	protected function encoder() {
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$encoder = new Encoder();
		$this->assertNotNull($encoder);
		$this->assertTrue($encoder instanceof Encoder);
	}

	public function testEncode() {
		$encoder = $this->encoder();
		$encodedFarm = $encoder->encode($this->getFarm());

		$this->assertArrayHasKey('processed', $encodedFarm);
		$this->assertArrayHasKey('raw', $encodedFarm);

		$rawEncoded = $encodedFarm['raw'];
		$this->assertArrayHasKey('attributes', $rawEncoded);
		$this->assertArrayHasKey('children', $rawEncoded);
		$this->assertArrayHasKey('nodeName', $rawEncoded);
	}

	public function testEncodeUnknownObject() {
		$encoder = $this->encoder();
		$this->assertNull($encoder->encode(new Noop()));
	}

	public function testEncodeWithWrapperName() {
		$encoder = $this->encoder();
		$encodedFarm = $encoder->encode($this->getFarm(), new EncoderOptions(array('wrapper' => 'test')));

		$this->assertArrayHasKey('test', $encodedFarm['processed']);
	}

	public function testEncodeWithoutNodeAttributes() {
		$encoder = $this->encoder();
		$encodedFarm = $encoder->encode($this->getFarm(), new EncoderOptions(array(
			'buildings' => array(
				'attributes' => false,
			)
		)));

		$this->assertArrayNotHasKey('type', $encodedFarm['processed']['farm']['buildings'][0], 'The \'type\' variable from \'buildings\' should not be there because the attributes/variables were turned off');
	}
}

class Noop {
}