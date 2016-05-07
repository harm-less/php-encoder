<?php

namespace PE\Tests\Nodes;

use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;
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
	 * @param NodeChildSetter $setter
	 * @param NodeChildGetter $getter
	 * @return EncoderNodeChild
	 */
	protected function nodeChild($nodeName = self::DEFAULT_NODE_NAME, NodeChildSetter $setter = null, NodeChildGetter $getter = null) {
		return new EncoderNodeChild($nodeName, $setter);
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

	public function testAddChildrenToObject() {
		$farmNode = $this->addFarmNode();
		$farm = $this->getFarm(false, false);
		$house = $this->getBuildingHouse();
		$farmNode->addChildrenToObject('buildings', $farm, array(
			$house
		));
		$this->assertEquals(array($house), $farm->getBuildings());
	}
	public function testAddChildrenToObjectWithNonExistentMethod() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeChildException', 'Setter method (children) for class "PE\Samples\General\Thing" does not exist');
		$nodeChild = $this->nodeChild(self::DEFAULT_NODE_NAME, new NodeChildSetter(null));
		$nodeChild->addChildrenToObject($this->getThing(), array());
	}
	public function testAddChildrenToObjectWhenMethodDoesNotExist() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeChildException', 'Trying to add children to "PE\Samples\General\Thing" with method "unknown", but this method does not exist');
		$nodeChild = $this->nodeChild(self::DEFAULT_NODE_NAME, new NodeChildSetter('unknown'));
		$nodeChild->addChildrenToObject($this->getThing(), array());
	}
}