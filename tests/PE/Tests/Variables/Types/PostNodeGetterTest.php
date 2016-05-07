<?php

namespace PE\Tests\Variables\Types;

use PE\Tests\Samples;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\PostNodeGetter;
use PE\Variables\Types\PostNodeSetter;

class PostNodeGetterTest extends Samples
{
	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new PostNodeGetter('method');
	}

	/**
	 * @return PostNodeGetter
	 */
	protected function postNodeGetter()
	{
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$setter = new PostNodeGetter('method');
		$this->assertNotNull($setter);
		$this->assertTrue($setter instanceof PostNodeGetter);
	}

	public function testCallNodeGetterAction() {
		$node = $this->getAccessorMethodActionTypeNodeNode();
		$variable = $node->getVariable('node');

		$this->assertEquals(array(
			'node' => 'hello world',
			'special' => 'hello world getter',
		), $variable->getPostNodeGetter()->apply(array(
			NodeAccessor::VARIABLE_NODE => $node,
			NodeAccessor::VARIABLE_NODE_DATA => array(
				'node' => 'hello world',
			),
			NodeAccessor::VARIABLE_NAME => 'node'
		)));
	}
}