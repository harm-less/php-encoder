<?php

namespace PE\Tests\Encoders;

use PE\Encoders\XmlEncoder;
use PE\Samples\Farm\Buildings\House;
use PE\Samples\Specials\SingleChild;

class XmlEncoderTest extends CoreEncoder {

	protected function setUp()
	{
		parent::setUp();
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
		$this->addHouseNodes();

		$house = $this->getHouse();
		$encoded = $this->encoder()->encode($house);
		$xmlString = trim(preg_replace('/\s+/', ' ', $encoded->saveXML()));

		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?> '
			. '<encoded> '
			.   '<building type="house"> '
			.     '<animals> '
			.       '<animal type="cat" name="Cat"/> '
			.     '</animals> '
			.   '</building> '
			. '</encoded>', $xmlString);

		$decoded = $this->encoder()->decode($xmlString);
		$this->assertArrayHasKey('building', $decoded);

		/** @var House $houseDecoded */
		$houseDecoded = $decoded['building'];
		$this->assertCount(1, $houseDecoded->getAnimals());
	}

	public function testEncodeDecoded() {
		$this->addSingleChildNode();
		$this->addThingNode();

		$singleChild = $this->getSingleChild();
		$thing = $this->getThing();
		$thing->setThingVar('hello world');
		$singleChild->setThing($thing);

		$encoded = $this->encoder()->encode($singleChild);
		$xmlString = trim(preg_replace('/\s+/', ' ', $encoded->saveXML()));

		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?> '
			. '<encoded> '
			.   '<single-child> '
			.     '<thing thingVar="hello world"/> '
			.   '</single-child> '
			. '</encoded>', $xmlString);

		$decoded = $this->encoder()->decode($xmlString);
		$this->assertArrayHasKey('single-child', $decoded);

		/** @var SingleChild $singleChildDecoded */
		$singleChildDecoded = $decoded['single-child'];
		$this->assertNotEmpty($singleChildDecoded->getThing());
	}
}