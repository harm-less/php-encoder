<?php

namespace PE\Tests;

use PE\Encoder;
use PE\Nodes\EncoderNode;
use PE\Options\EncoderOptions;
use PE\Samples\Farm\Building;
use PE\Samples\General\Thing;

class EncoderTest extends Samples {

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
		$this->addThingNode();

		$this->encoder()->encode($this->getNoGetterMethod());
	}
	public function testEncodeWithoutVariableGetterMethod() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Getter method "getNonExistent" does not exist in object "PE\Samples\Erroneous\NoVariableGetterMethod" for node type "default" (PE\Nodes\Erroneous\NoVariableGetterMethodNode) and variable with id "nonExistent".');

		$this->addVariableNoGetterMethodNode();

		$this->encoder()->encode($this->getVariableNoGetterMethod());
	}
	public function testEncodeWithGetterMethodReturningString() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Children object for node "things" must be an array. EncoderNodeChilds are returning an array by default. If this behavior is not desired, turn it off using "$childNode->isArray(false)" or set "isArray" as an options to the EncoderNodeChild instance');

		$this->addNonArrayGetterMethodNode();
		$this->addThingNode();

		$this->encoder()->encode($this->getNonArrayGetterMethod());
	}
	public function testEncodeWhenChildNodeTypeDoesNotExist() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Child node type for object "PE\Samples\Farm\Animals\Cat (child of "buildings")" for node "animals" not found');

		$house = $this->getHouse();
		$this->addBuildingNode();
		$this->addBuildingHouseNode();
		$this->addAnimalNodes(false);

		$this->encoder()->encode($house);
	}
	public function testEncodeNodeByValueButItDoesNotExist() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Option "value" cannot be mapped to "type" because it does not exist in "things"');

		$encoder = $this->encoder();
		$things = $this->getThings();
		$thing = $this->getThing();
		$thing->setThingVar('hello world');
		$things->addThing($thing);

		$this->addThingNode();
		$this->addThingsNode();

		$encoder->encode($things, new EncoderOptions(array(
			'things' => array(
				'value' => 'type'
			)
		)));
	}

	public function testEncodeWithGetterMethodReturningNonArrayObject() {
		$this->addNonArrayGetterMethodOnPurposeNode();
		$this->addThingNode();

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

	public function testEncodeNodeByValue() {
		$encoder = $this->encoder();
		$things = $this->getThings();
		$thing = $this->getThing();
		$thing->setThingVar('hello world');
		$things->addThing($thing);

		$this->addThingNode();
		$this->addThingsNode();

		$encodedThings = $encoder->encode($things, new EncoderOptions(array(
			'things' => array(
				'value' => 'thingVar'
			)
		)));

		$this->assertTrue(is_string($encodedThings['processed']['thingContainer']['things'][0]));
		$this->assertEquals($encodedThings['processed']['thingContainer']['things'][0], 'hello world');
	}

	public function testEncodeIteratedNode() {
		$encoder = $this->encoder();
		$this->addFarmNodes();
		$encodedHouse = $encoder->encode($this->getHouse(), new EncoderOptions(array(
			'animals' => array(
				'iterate' => 2,
			)
		)));

		$this->assertArrayNotHasKey('type', $encodedHouse['processed']['building']['animals'][0]);
		$this->assertArrayHasKey(0, $encodedHouse['processed']['building']['animals'][0]);
		$this->assertCount(2, $encodedHouse['processed']['building']['animals']);
	}

	public function testEncodeIteratedNodeWithChildKey() {
		$encoder = $this->encoder();
		$this->addFarmNodes();
		$encodedHouse = $encoder->encode($this->getHouse(), new EncoderOptions(array(
			'animals' => array(
				'iterate' => 2,
				'key' => 'type'
			)
		)));

		$this->assertArrayNotHasKey('animals', $encodedHouse['processed']['building']);
		$this->assertArrayHasKey('cat', $encodedHouse['processed']['building'][0]);
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