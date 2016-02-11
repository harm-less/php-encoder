<?php

namespace PE\Tests\Nodes;
use PE\Nodes\EncoderNodeChild;
use PE\Tests\Samples;

class EncoderNodeChildTest extends Samples {

	const DEFAULT_NODE_NAME = 'children';
	const DEFAULT_CHILD_OPTIONS = null;

	protected function setUp() {
		parent::setUp();
		$this->_peApp = $this->nodeChild();
	}

	/**
	 * @param string $nodeName
	 * @param $options
	 * @return EncoderNodeChild
	 */
	protected function nodeChild($nodeName = self::DEFAULT_NODE_NAME, $options = self::DEFAULT_CHILD_OPTIONS) {
		return new EncoderNodeChild($nodeName, $options);
	}

	public function testConstructor() {
		$nodeChild = $this->nodeChild();
		$this->assertNotNull($nodeChild);
		$this->assertTrue($nodeChild instanceof EncoderNodeChild);
		$this->assertEquals(self::DEFAULT_NODE_NAME, $nodeChild->getChildNodeName());
	}

	public function testSetChildNodeName() {
		$nodeChild = $this->nodeChild();
		$nodeChild->setChildNodeName('name');
		$this->assertEquals('name', $nodeChild->getChildNodeName());
	}

	public function testSetChildNodeNameWithEmptyString() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeChildException', 'Node name cannot be null or empty');
		$nodeChild = $this->nodeChild();
		$nodeChild->setChildNodeName('');
	}
	public function testSetChildNodeNameWithNull() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeChildException', 'Node name cannot be null or empty');
		$nodeChild = $this->nodeChild();
		$nodeChild->setChildNodeName(null);
	}

	public function testSetAfterChildren() {
		$nodeChild = $this->nodeChild();
		$this->assertTrue($nodeChild->setAfterChildren());
		$this->assertFalse($nodeChild->setAfterChildren(false));
		$this->assertTrue($nodeChild->setAfterChildren(true));
	}
	public function testSetAfterAttributes() {
		$nodeChild = $this->nodeChild();
		$this->assertTrue($nodeChild->setAfterAttributes());
		$this->assertFalse($nodeChild->setAfterAttributes(false));
		$this->assertTrue($nodeChild->setAfterAttributes(true));
	}

	public function testAddChildrenToObject() {
		$farmNode = $this->addFarmNode();
		$farm = $this->getFarm(false, false);
		$house = $this->getBuildingHouse();
		$farmNode->addChildrenToObject('buildings', $farm, array(
			$house
		));
		$this->assertEquals(array($house), $farm->getBuildings());
	}
	public function testAddChildrenToObjectWithoutMethod() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeChildException', 'Setter method (' . self::DEFAULT_NODE_NAME . ') for class "PE\Samples\General\Thing" does not exist');
		$nodeChild = $this->nodeChild();
		$nodeChild->addChildrenToObject($this->getThing(), array());
	}
	public function testAddChildrenToObjectWhenMethodDoesNotExist() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeChildException', 'Trying to add children to "PE\Samples\General\Thing" with method "methodName", but this method does not exist');
		$nodeChild = $this->nodeChild();
		$nodeChild->setSetterMethod('methodName');
		$nodeChild->addChildrenToObject($this->getThing(), array());
	}
}