<?php

namespace PE\Tests;

use PE\Encoder;
use PE\Nodes\EncoderNode;
use PE\Options\EncoderOptions;
use PE\Samples\Farm\Building;
use PE\Samples\General\Thing;

class EncoderTest extends Samples {

	protected function tearDown()
	{

	}

	protected function setUp()
	{
		EncoderNode::clean();
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

	public function testEncodeWithoutChildObjectNodes() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Cannot set the node name (buildings) of a node child because it doesn\'t exist. Please add the requested node with "EncoderNode::addNode()". Current node name "farms" with class name "PE\Nodes\Farm\FarmNode"');

		$this->addFarmNode();

		$this->encoder()->encode($this->getFarm(false));
	}
	public function testEncodeWithoutGetterMethod() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Getter method "getThings" for node "things" does not exist in class "PE\Samples\Erroneous\NoGetterMethod"');

		$this->addNoGetterMethodNode();
		$this->addThingsNode();

		$this->encoder()->encode($this->getNoGetterMethod());
	}
	public function testEncodeWithGetterMethodReturningString() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Children object for node "things" must be an array. EncoderNodeChilds are returning an array by default. If this behavior is not desired, turn it off using "$childNode->isArray(false)" or set "isArray" as an options to the EncoderNodeChild instance');

		$this->addNonArrayGetterMethodNode();
		$this->addThingsNode();

		$this->encoder()->encode($this->getNonArrayGetterMethod());
	}

	public function testEncodeWithGetterMethodReturningNonArrayObject() {
		$this->addNonArrayGetterMethodOnPurposeNode();
		$this->addThingsNode();

		$obj = $this->getNonArrayGetterMethodOnPurpose();
		$obj->addThing(new Thing());

		$encoder = $this->encoder();
		$encoded = $encoder->encode($obj);

		$this->assertArrayHasKey('thingVar', $encoded['processed']['non-array-getter-method-on-purpose']['things']);
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

	public function testEncodeNodeByKey() {
		$encoder = $this->encoder();
		$encodedFarm = $encoder->encode($this->getFarm(), new EncoderOptions(array(
			'buildings' => array(
				'key' => 'type',
			)
		)));

		$this->assertArrayNotHasKey('buildings', $encodedFarm['processed']['farm']);
		$this->assertArrayHasKey('house', $encodedFarm['processed']['farm']);
		$this->assertArrayHasKey('greenhouse', $encodedFarm['processed']['farm']);
		$this->assertArrayHasKey('barn', $encodedFarm['processed']['farm']);
	}

	public function testEncodeWithoutNodeChildren() {
		$encoder = $this->encoder();
		$encodedFarm = $encoder->encode($this->getFarm(), new EncoderOptions(array(
			'buildings' => array(
				'children' => false,
			)
		)));

		$this->assertArrayNotHasKey('animals', $encodedFarm['processed']['farm']['buildings'][0]);
		$this->assertArrayHasKey('type', $encodedFarm['processed']['farm']['buildings'][0]);
		$this->assertEmpty($encodedFarm['raw']['children']['buildings'][0]['children']);
	}
}

class Noop {
}

class UnknownBuilding extends Building {

}