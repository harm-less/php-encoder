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
}