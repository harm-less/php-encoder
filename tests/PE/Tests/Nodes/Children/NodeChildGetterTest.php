<?php

namespace PE\Tests\Nodes\Children;

use PE\Nodes\Children\NodeChildGetter;
use PE\Tests\Samples;

class NodeChildGetterTest extends Samples
{

	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new NodeChildGetter('method');
	}

	/**
	 * @return NodeChildGetter
	 */
	protected function nodeChildSetter()
	{
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$setter = new NodeChildGetter('test');
		$this->assertNotNull($setter);
		$this->assertTrue($setter instanceof NodeChildGetter);
	}

	public function testGetMethod() {
		$childSetter = $this->nodeChildSetter();
		$this->assertEquals('method', $childSetter->getMethod());
	}
}
?>