<?php

namespace PE\Tests\Encoders;

use PE\Encoders\XmlEncoder;
use PE\Samples\Farm\Buildings\House;

class XmlEncoderTest extends CoreEncoder {

	protected function setUp()
	{
		$this->_peApp = new XmlEncoder();
	}

	/**
	 * @return XmlEncoder
	 */
	protected function encoder() {
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$encoder = new XmlEncoder();
		$this->assertNotNull($encoder);
		$this->assertTrue($encoder instanceof XmlEncoder);
	}

	public function testEncodeDecode() {
		$house = $this->getHouse();
		$encoded = $this->encoder()->encode($house);
		$xmlString = trim(preg_replace('/\s+/', ' ', $encoded->saveXML()));

		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?> '
			 . '<tb> '
			 .   '<building type="house"> '
			 .     '<animals> '
			 .       '<animal type="cat" name="Cat"/> '
			 .     '</animals> '
			 .   '</building> '
			 . '</tb>', $xmlString);

		$decoded = $this->encoder()->decode(simplexml_import_dom($encoded));
		$this->assertArrayHasKey('building', $decoded);

		/** @var House $houseDecoded */
		$houseDecoded = $decoded['building'];
		$this->assertCount(1, $houseDecoded->getAnimals());
	}
}