<?php

namespace PE\Tests\Nodes\Children;

use PE\Nodes\Children\NodeChildSetter;
use PE\Tests\Samples;

class NodeChildSetterTest extends Samples
{

	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new NodeChildSetter('method');
	}

	/**
	 * @return NodeChildSetter
	 */
	protected function nodeChildSetter()
	{
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$setter = new NodeChildSetter('test');
		$this->assertNotNull($setter);
		$this->assertTrue($setter instanceof NodeChildSetter);
	}

	public function testSetAfterChildren() {
		$nodeChildSetter = $this->nodeChildSetter();

		$this->assertTrue($nodeChildSetter->setAfterChildren());
		$this->assertFalse($nodeChildSetter->setAfterChildren(false));
		$this->assertTrue($nodeChildSetter->setAfterChildren(true));
	}
	public function testSetAfterAttributes() {
		$nodeChildSetter = $this->nodeChildSetter();

		$this->assertTrue($nodeChildSetter->setAfterAttributes());
		$this->assertFalse($nodeChildSetter->setAfterAttributes(false));
		$this->assertTrue($nodeChildSetter->setAfterAttributes(true));
	}
}
?>