<?php

namespace PE\Tests\Encoders;

use PE\Encoders\JsonEncoder;
use PE\Samples\Farm\Buildings\House;

class JsonEncoderTest extends CoreEncoder {

	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new JsonEncoder();
	}

	/**
	 * @return JsonEncoder
	 */
	protected function encoder() {
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$encoder = new JsonEncoder();
		$this->assertNotNull($encoder);
		$this->assertTrue($encoder instanceof JsonEncoder);
	}

	public function testEncodeDecode() {
		$this->addHouseNodes();

		$house = $this->getHouse();
		$encoded = $this->encoder()->encode($house);
		$jsonString = trim(preg_replace('/\s+/', ' ', $encoded));

		$this->assertEquals('{'
			. '"building":'
			.   '{'
			.     '"type":"house",'
			.     '"animals":['
			.       '{'
			.         '"type":"cat",'
			.         '"name":"Cat"'
			.       '}'
			.     ']'
			.   '}'
			. '}', $jsonString);

		$decoded = $this->encoder()->decode($encoded);
		$this->assertArrayHasKey('building', $decoded);

		/** @var House $houseDecoded */
		$houseDecoded = $decoded['building'];
		$this->assertCount(1, $houseDecoded->getAnimals());
	}
}