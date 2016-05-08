<?php

namespace PE\Tests\Nodes;
use PE\Nodes\EncoderNodeChild;
use PE\Nodes\EncoderNodeChildren;
use PE\Tests\Samples;

class EncoderNodeChildrenTest extends Samples {

	protected function setUp() {
		parent::setUp();
		$this->_peApp = $this->nodeChildren();
	}

	/**
	 * @return EncoderNodeChildren
	 */
	protected function nodeChildren() {
		return new EncoderNodeChildren();
	}

	public function testConstructor() {
		$nodeChildren = $this->nodeChildren();
		$this->assertNotNull($nodeChildren);
		$this->assertTrue($nodeChildren instanceof EncoderNodeChildren);
	}

	public function testAddChild() {
		$nodeChildren = $this->nodeChildren();
		$child = $nodeChildren->addChild(new EncoderNodeChild('test'));
		$this->assertEquals($child, $nodeChildren->getChild('test'));

		$this->assertFalse($nodeChildren->addChild($child));

	}

	public function testGetChild() {
		$nodeChildren = $this->nodeChildren();
		$child = $nodeChildren->addChild(new EncoderNodeChild('test'));
		$this->assertEquals($child, $nodeChildren->getChild('test'));
		$this->assertNull($nodeChildren->getChild('unknown'));
	}

	public function testGetChildren() {
		$nodeChildren = $this->nodeChildren();
		$this->assertEquals(array(), $nodeChildren->getChildren());
		$child = $nodeChildren->addChild(new EncoderNodeChild('test'));
		$this->assertEquals(array(
			'test' => $child
		), $nodeChildren->getChildren());
	}

	public function testChildExists() {
		$nodeChildren = $this->nodeChildren();
		$nodeChildren->addChild(new EncoderNodeChild('test'));
		$this->assertTrue($nodeChildren->childExists('test'));
		$this->assertFalse($nodeChildren->childExists('unknown'));
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
	public function testAddChildrenToObjectWhenChildIsNotFound() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeChildException', 'Trying to add children to object, but the child "unknown" could not be found');
		$nodeChildren = $this->nodeChildren();
		$nodeChildren->addChildrenToObject('unknown', null, null);
	}
}