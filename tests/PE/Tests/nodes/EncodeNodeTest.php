<?php

namespace PE\Tests\Nodes;

use PE\Nodes\EncoderNode;
use PE\Nodes\Farm\BuildingNode;
use PE\Nodes\Farm\Buildings\HouseNode;
use PE\Tests\Samples;

class EncoderNodeTest extends Samples
{

	const DEFAULT_NODE_NAME = 'nodes';
	const DEFAULT_NODE_NAME_SINGLE = 'node';
	const DEFAULT_NODE_TYPE_NAME = null;
	const DEFAULT_CLASS_PREPEND = null;

	const DEFAULT_NODE_TYPE = 'type';

	protected function setUp() {
		parent::setUp();
		$this->_peApp = $this->node();
	}

	/**
	 * @param string $nodeName
	 * @param string $nodeNameSingle
	 * @param string $classPrepend
	 * @param string $nodeTypeName
	 * @return EncoderNode
	 */
	protected function node($nodeName = self::DEFAULT_NODE_NAME, $nodeNameSingle = self::DEFAULT_NODE_NAME_SINGLE, $classPrepend = self::DEFAULT_CLASS_PREPEND, $nodeTypeName = self::DEFAULT_NODE_TYPE_NAME) {
		return new EncoderNode($nodeName, $nodeNameSingle, $classPrepend, $nodeTypeName);
	}

	/**
	 * @param string $nodeTypeName
	 * @return EncoderNode
	 */
	protected function nodeType($nodeTypeName = self::DEFAULT_NODE_TYPE_NAME) {
		return new EncoderNode(self::DEFAULT_NODE_NAME, self::DEFAULT_NODE_NAME_SINGLE, self::DEFAULT_CLASS_PREPEND, $nodeTypeName);
	}

	public function testConstructor() {
		$node = $this->node();
		$this->assertNotNull($node);
		$this->assertTrue($node instanceof EncoderNode);
	}

	public function testStaticAddNode() {
		$node = $this->node();
		$this->assertTrue(EncoderNode::addNode($node));
		// both the nodeName and the nodeNameSingle are being added, that's why there should be two.
		$this->assertCount(2, EncoderNode::getNodes());

		$nodeTypes = EncoderNode::getNodeTypes(self::DEFAULT_NODE_NAME);
		$this->assertArrayHasKey(EncoderNode::DEFAULT_TYPE, $nodeTypes);
		$this->assertEquals($nodeTypes[EncoderNode::DEFAULT_TYPE], $node);

		$types = EncoderNode::getNodeTypes();
		$this->assertCount(1, $types);
		$this->assertEquals('default', $node->getNodeTypeName());
	}

	public function testStaticAddNodeWithNonStringNodeName() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeException', 'Node without a name has been added. It must be a string and it cannot be empty.');
		EncoderNode::addNode($this->node(null));
	}
	public function testStaticAddNodeWithNodeType() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeException', 'The node you\'re trying to add seems to be a node type because it has a type name');
		EncoderNode::addNode($this->nodeType('cannot-be-a-type'));
	}
	public function testStaticAddNodeWithEmptyNodeName() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeException', 'Node without a name has been added. It must be a string and it cannot be empty.');
		EncoderNode::addNode($this->node(''));
	}
	public function testStaticAddNodeThatAlreadyExists() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeException', 'Node with name "nodes" already exists');
		EncoderNode::addNode($this->node());
		EncoderNode::addNode($this->node());
	}
	public function testStaticAddSingleNodeThatAlreadyExists() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeException', 'Node with single name "node" already exists');
		EncoderNode::addNode($this->node());
		EncoderNode::addNode($this->node('nodes2'));
	}

	public function testStaticGetNode() {
		$node = $this->node();
		EncoderNode::addNode($node);
		$this->assertEquals($node, EncoderNode::getNode('nodes'));
		$this->assertEquals($node, EncoderNode::getNode('node'));
		$this->assertNull(EncoderNode::getNode('unknown'));
	}

	public function testStaticGetNodeByObject() {
		$thingsNode = $this->addThingsNode();
		$things = $this->getThings();
		$this->assertEquals($thingsNode, EncoderNode::getNodeByObject($things));
		$this->assertNull(EncoderNode::getNodeByObject($this->getThing()));

		// from cache
		$this->assertEquals($thingsNode, EncoderNode::getNodeByObject($things));
	}

	public function testStaticGetNodes() {
		$node = $this->node();
		EncoderNode::addNode($node);
		$this->assertEquals(array(
			self::DEFAULT_NODE_NAME_SINGLE => $node,
			self::DEFAULT_NODE_NAME => $node
		), EncoderNode::getNodes());
	}

	public function testStaticIsSingleNode() {
		EncoderNode::addNode($this->node());
		$this->assertTrue(EncoderNode::isSingleNode('node'));
		$this->assertFalse(EncoderNode::isSingleNode('nodes'));
	}

	public function testStaticNodeExists() {
		EncoderNode::addNode($this->node());
		$this->assertTrue(EncoderNode::nodeExists(self::DEFAULT_NODE_NAME));
		$this->assertTrue(EncoderNode::nodeExists(self::DEFAULT_NODE_NAME_SINGLE));
		$this->assertFalse(EncoderNode::nodeExists('unknown'));
	}

	public function testStaticAddNodeType() {
		$this->addBuildingNode();
		$this->addBuildingHouseNode();

		$types = EncoderNode::getNodeTypes();
		$this->assertCount(2, $types);
		$this->assertArrayHasKey('buildings:default', $types);
		$this->assertArrayHasKey('buildings:house', $types);

		$this->assertEquals('PE\\Nodes\\Farm\\BuildingNode', get_class($types['buildings:default']));
		$this->assertEquals('PE\\Nodes\\Farm\\Buildings\HouseNode', get_class($types['buildings:house']));
	}
	public function testStaticAddNodeTypeTwice() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeException', 'Node type with name "buildings" and node type name "house" already exists');
		$this->addBuildingNode();
		$this->addBuildingHouseNode();
		$this->addBuildingHouseNode();
	}
	public function testStaticAddNodeTypeWithoutNodeType() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeException', 'The node type you\'re trying to add seems to be a regular node because it has a no type name. Make sure you try to add an EncoderNode with a type name');
		EncoderNode::addNodeType($this->nodeType());
	}

	public function testStaticGetNodeTypeByObject() {
		$this->addBuildingNode();
		$houseNode = $this->addBuildingHouseNode();
		$house = $this->getBuildingHouse();
		$this->assertEquals($houseNode, EncoderNode::getNodeTypeByObject($house));
		$this->assertNull(EncoderNode::getNodeTypeByObject($this->getThing()));

		// from cache
		$this->assertEquals($houseNode, EncoderNode::getNodeTypeByObject($house));
	}

	public function testStaticGetNodeTypesAll() {
		$this->addBuildingNode();
		$this->addBuildingHouseNode();

		$types = EncoderNode::getNodeTypes();
		$this->assertCount(2, $types);
		$this->assertArrayHasKey('buildings:default', $types);
		$this->assertArrayHasKey('buildings:house', $types);

		$this->assertEquals('PE\\Nodes\\Farm\\BuildingNode', get_class($types['buildings:default']));
		$this->assertEquals('PE\\Nodes\\Farm\\Buildings\HouseNode', get_class($types['buildings:house']));
	}

	public function testStaticGetNodeTypesOfType() {
		$this->addHouseNodes();

		$types = EncoderNode::getNodeTypes('buildings');
		$this->assertCount(2, $types);
		$this->assertArrayHasKey('default', $types);
		$this->assertArrayHasKey('house', $types);

		$this->assertEquals('PE\\Nodes\\Farm\\BuildingNode', get_class($types['default']));
		$this->assertEquals('PE\\Nodes\\Farm\\Buildings\HouseNode', get_class($types['house']));
	}

	public function testStaticGetNodeType() {
		$this->addBuildingNode();
		$this->addBuildingHouseNode();

		$this->assertEquals('PE\\Nodes\\Farm\\Buildings\HouseNode', get_class(EncoderNode::getNodeType('buildings', 'house')));
		$this->assertNull(EncoderNode::getNodeType('buildings', 'unknown'));
		$this->assertNull(EncoderNode::getNodeType('unknown', 'unknown'));
	}

	public function testStaticNodeTypeExists() {
		$this->addBuildingNode();
		$this->addBuildingHouseNode();

		$this->assertTrue(EncoderNode::nodeTypeExists('buildings', 'house'));
		$this->assertFalse(EncoderNode::nodeTypeExists('buildings', 'unknown'));
		$this->assertFalse(EncoderNode::nodeTypeExists('unknown', 'unknown'));
	}

	public function testStaticClean() {
		$this->addBuildingNode();
		$this->addBuildingHouseNode();

		$this->assertCount(2, EncoderNode::getNodeTypes());
		$this->assertCount(2, EncoderNode::getNodes());

		EncoderNode::clean();

		$this->assertEmpty(EncoderNode::getNodeTypes());
		$this->assertEmpty(EncoderNode::getNodes());
	}




	public function testClassPrepend() {
		$buildingNode = $this->addBuildingNode();

		$this->assertEquals('\\PE\\Samples\\Farm', $buildingNode->classPrepend());
	}

	public function testGetNodeObjectName() {
		$buildingNode = $this->addBuildingNode();

		$this->assertEquals('Building', $buildingNode->getNodeObjectName());

		// get it from cache
		$this->assertEquals('Building', $buildingNode->getNodeObjectName());
	}

	public function testNodeIsObject() {
		$this->addBuildingNode();
		$houseNode = $this->addBuildingHouseNode();
		$house = $this->getBuildingHouse();

		$this->assertFalse($houseNode->nodeIsObject($this->getThing()));
		$this->assertTrue($houseNode->nodeIsObject($house));

		// get from cache
		$this->assertTrue($houseNode->nodeIsObject($house));
	}

	public function testGetNodeName() {
		$this->addBuildingNode();
		$houseNode = $this->addBuildingHouseNode();

		$this->assertEquals('buildings', $houseNode->getNodeName());
	}
	public function testGetNodeNameSingle() {
		$this->addBuildingNode();
		$houseNode = $this->addBuildingHouseNode();

		$this->assertEquals('building', $houseNode->getNodeNameSingle());
	}
	public function testGetNodeTypeName() {
		$this->addBuildingNode();
		$houseNode = $this->addBuildingHouseNode();

		$this->assertEquals('house', $houseNode->getNodeTypeName());
	}
}