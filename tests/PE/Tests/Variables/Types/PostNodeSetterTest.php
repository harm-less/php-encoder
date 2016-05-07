<?php

namespace PE\Tests\Variables\Types;

use PE\Tests\Samples;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\PostNodeSetter;

class PostNodeSetterTest extends Samples
{
	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new PostNodeSetter('method');
	}

	/**
	 * @return PostNodeSetter
	 */
	protected function postNodeSetter()
	{
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$setter = new PostNodeSetter('method');
		$this->assertNotNull($setter);
		$this->assertTrue($setter instanceof PostNodeSetter);
	}

	public function testCallNodeSetterAction() {
		$node = $this->getAccessorMethodActionTypeNodeNode();
		$variable = $node->getVariable('node');

		$this->assertEquals(array(
			'node' => 'hello world',
			'special' => 'hello world',
		), $variable->getPostNodeSetter()->apply(array(
			NodeAccessor::VARIABLE_NODE => $node,
			NodeAccessor::VARIABLE_NODE_DATA => array(
				'node' => 'hello world',
			),
			NodeAccessor::VARIABLE_NAME => 'node'
		)));
	}

	public function testCallNodeSetterActionWithMissingVariable() {
		$this->setExpectedException('\\PE\\Exceptions\\VariableTypeException', 'Parameter with id "node_node_data" is not known');
		$node = $this->getAccessorMethodActionTypeNodeNode();
		$this->getAccessorMethodActionTypeNodeNode()->getVariable('node')->getPostNodeSetter()->apply(array(NodeAccessor::VARIABLE_NODE => $node));
	}

	public function testApplyToSetterNodeSimple() {
		$node = $this->getEncoderNodeVariableApplyToSetterNode();

		$object = $this->getEncoderNodeVariableApplyToSetter();
		$collection = $node->getVariableCollection();
		$var = $collection->getVariableById('node-simple');

		$this->assertEquals(array(
			'node' => 'test',
			'copied' => 'test',
		), $var->getPostNodeSetter()->apply(array(
			NodeAccessor::VARIABLE_OBJECT => $object,
			NodeAccessor::VARIABLE_NODE_DATA => array(
				'node' => 'test'
			),
			NodeAccessor::VARIABLE_NODE => $node,
			NodeAccessor::VARIABLE_NAME => 'node',
			NodeAccessor::VARIABLE_VALUE => 'test'
		)));

		$this->assertNull($object->getVar());
	}

	public function testApplyToSetterNodeFull() {
		$node = $this->getEncoderNodeVariableApplyToSetterNode();

		$object = $this->getEncoderNodeVariableApplyToSetter();
		$collection = $node->getVariableCollection();
		$var = $collection->getVariableById('node-full');

		$parent = $this->getThing();

		$this->assertEquals(array(
			'node' => 'test',
			'name' => 'node',
			'value' => 'test',
			'object' => $object,
			'parent' => $parent,
		), $var->getPostNodeSetter()->apply(array(
			NodeAccessor::VARIABLE_OBJECT => $object,
			NodeAccessor::VARIABLE_PARENT => $parent,
			NodeAccessor::VARIABLE_NODE_DATA => array(
				'node' => 'test'
			),
			NodeAccessor::VARIABLE_NODE => $node,
			NodeAccessor::VARIABLE_NAME => 'node',
			NodeAccessor::VARIABLE_VALUE => 'test'
		)));
	}

	public function testApplyToSetterNodeWithoutVariables() {
		$this->_applyToSetterNodeWithoutVariables('node-without-variables');
		$this->_applyToSetterNodeWithoutVariables('node-without-variables-empty');
		$this->_applyToSetterNodeWithoutVariables('node-without-variables-null');
	}
	protected function _applyToSetterNodeWithoutVariables($variable)
	{
		$node = $this->getEncoderNodeVariableApplyToSetterNode();

		$object = $this->getEncoderNodeVariableApplyToSetter();
		$collection = $node->getVariableCollection();
		$var = $collection->getVariableById($variable);

		$this->assertEquals(array(
			'test' => 'altered',
		), $var->getPostNodeSetter()->apply(array(
			NodeAccessor::VARIABLE_OBJECT => $object,
			NodeAccessor::VARIABLE_NODE_DATA => array(
				'test' => 'test'
			),
			NodeAccessor::VARIABLE_NODE => $node,
			NodeAccessor::VARIABLE_NAME => 'node',
			NodeAccessor::VARIABLE_VALUE => 'test'
		)));

		$this->assertNull($object->getVar());
	}

	public function testApplyToSetterNodeUnknownVariable() {
		$this->setExpectedException('\\PE\\Exceptions\\VariableTypeException', 'Parameter with id "unknown_variable" is not known');
		$node = $this->getEncoderNodeVariableApplyToSetterNode();

		$object = $this->getEncoderNodeVariableApplyToSetter();
		$collection = $node->getVariableCollection();
		$var = $collection->getVariableById('node-unknown-variable');

		$var->getPostNodeSetter()->apply(array(
			NodeAccessor::VARIABLE_OBJECT => $object,
			NodeAccessor::VARIABLE_NODE_DATA => array(
				'node' => 'test'
			),
			NodeAccessor::VARIABLE_NODE => $node,
			NodeAccessor::VARIABLE_NAME => 'node',
			NodeAccessor::VARIABLE_VALUE => 'test'
		));
	}
}