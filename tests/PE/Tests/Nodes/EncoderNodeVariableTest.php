<?php

namespace PE\Tests\Nodes;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNodeVariable;
use PE\Tests\Samples;
use PE\Variables\Types\ObjectGetter;
use PE\Variables\Types\ObjectSetter;

class EncoderNodeVariableTest extends Samples {

	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new EncoderNodeVariable('var');
	}

	/**
	 * @return EncoderNodeVariable
	 */
	protected function variable() {
		return $this->_peApp;
	}
	/**
	 * @param $id
	 * @param bool $enableObjectAccessors
	 * @return EncoderNodeVariable
	 */
	protected function newVariable($id, $enableObjectAccessors = true) {
		return new EncoderNodeVariable($id, $enableObjectAccessors);
	}

	public function testConstructor()
	{
		$variable = new EncoderNodeVariable('var');
		$this->assertNotNull($variable);
		$this->assertTrue($variable instanceof EncoderNodeVariable);
	}

	public function testObjectSetter() {
		$variable = $this->newVariable('var', false);
		$this->assertNull($variable->getObjectSetter());
		$objectSetter = new ObjectSetter('setterMethod');
		$variable->objectSetter($objectSetter);
		$this->assertEquals($objectSetter, $variable->getObjectSetter());
	}

	public function testObjectGetter() {
		$variable = $this->newVariable('var', false);
		$this->assertNull($variable->getObjectGetter());
		$objectGetter = new ObjectGetter('setterMethod');
		$variable->objectGetter($objectGetter);
		$this->assertEquals($objectGetter, $variable->getObjectGetter());
	}

	public function testSetType() {
		$variable = $this->variable();
		$variable->setType(EncoderNodeVariable::TYPE_ARRAY);
		$this->assertEquals(EncoderNodeVariable::TYPE_ARRAY, $variable->getType());
		$variable->setType(EncoderNodeVariable::TYPE_STRING);
		$this->assertEquals(EncoderNodeVariable::TYPE_STRING, $variable->getType());
		$variable->setType(EncoderNodeVariable::TYPE_BOOL);
		$this->assertEquals(EncoderNodeVariable::TYPE_BOOL, $variable->getType());
		$variable->setType(null);
		$this->assertNull($variable->getType());
	}

	public function testGetType() {
		$variable = $this->variable();
		$this->assertNull($variable->getType());
	}

	public function testOrder() {
		$variable = $this->variable();
		$this->assertNull($variable->getOrder());
		$variable->setOrder(10);
		$this->assertEquals(10, $variable->getOrder());
	}

	public function testProcessValue() {
		$variable = $this->variable();
		$objectSetter = $variable->getObjectSetter();
		$this->assertEquals('test', $objectSetter->processValue('test'));

		$variable->setType(EncoderNodeVariable::TYPE_BOOL);
		$this->assertEquals(true, $objectSetter->processValue(1));
		$this->assertEquals(true, $objectSetter->processValue('1'));
		$this->assertEquals(true, $objectSetter->processValue('true'));
		$this->assertEquals(false, $objectSetter->processValue(0));
		$this->assertEquals(false, $objectSetter->processValue('0'));
		$this->assertEquals(false, $objectSetter->processValue('false'));
		$this->assertEquals(false, $objectSetter->processValue('abc'));

		$variable->setType(EncoderNodeVariable::TYPE_STRING);
		$this->assertEquals('1', $objectSetter->processValue(1));
		$this->assertEquals('string', $objectSetter->processValue('string'));

		$variable->setType(EncoderNodeVariable::TYPE_ARRAY);
		$this->assertEquals(array(), $objectSetter->processValue(json_encode(array())));
		$this->assertEquals(array('hello' => 'world'), $objectSetter->processValue(json_encode(array('hello' => 'world'))));
	}

	public function testProcessValueArrayException() {
		$this->setExpectedException('PE\\Exceptions\\VariableTypeException', 'The set data type is array but the value cannot be processed');
		$variable = $this->variable();
		$variable->setType(EncoderNodeVariable::TYPE_ARRAY);
		$variable->getObjectSetter()->processValue(array());
	}
	public function testProcessValueUnknownTypeException() {
		$this->setExpectedException('PE\\Exceptions\\VariableTypeException', 'Can\'t process value "string" because the data type "unknown" isn\'t recognized.');
		$variable = $this->variable();
		$variable->setType('unknown');
		$variable->getObjectSetter()->processValue('string');
	}

	public function testCallNodeSetterAction() {
		$node = $this->getAccessorMethodActionTypeNodeNode();
		$variable = $node->getVariable('node');

		$this->assertEquals(array(
			'node' => 'hello world',
			'special' => 'hello world',
		), $variable->callNodeSetterAction($node, array(
			ActionVariable::GETTER_NODE_DATA => array(
				'node' => 'hello world',
			),
			ActionVariable::GETTER_NAME => 'node'
		)));
	}

	public function testCallNodeSetterActionWithActionTypeNotNode() {
		$node = $this->getAccessorMethodActionTypeNodeNode();
		$variable = $node->getVariable('node');
		$setterAction = $variable->getSetterAction();
		$setterAction['type'] = 'not-node';
		$variable->setSetterAction($setterAction);

		$this->assertNull($variable->callNodeSetterAction($node, array(
			ActionVariable::GETTER_NODE_DATA => array(
				'node' => 'hello world',
			),
			ActionVariable::GETTER_NAME => 'node'
		)));
	}

	public function testCallNodeSetterActionWithMissingVariable() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeVariableException', 'Action variable "node_data" is not known');
		$node = $this->getAccessorMethodActionTypeNodeNode();
		$this->getAccessorMethodActionTypeNodeNode()->getVariable('node')->callNodeSetterAction($node, array());
	}

	public function testCallNodeSetterActionWithoutMethod() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeVariableException', 'Either method must be a string or an array with a "method" key being a string');
		$node = $this->getAccessorMethodActionTypeNodeNode();
		$variable = $node->getVariable('node');
		$setterAction = $variable->getSetterAction();
		unset($setterAction['method']);
		$variable->setSetterAction($setterAction);

		$variable->callNodeSetterAction($node, array(
			ActionVariable::GETTER_NODE_DATA => array(
				'node' => 'hello world',
			),
			ActionVariable::GETTER_NAME => 'node'
		));
	}

	public function testCallNodeGetterAction() {
		$node = $this->getAccessorMethodActionTypeNodeNode();
		$variable = $node->getVariable('node');

		$this->assertEquals(array(
			'node' => 'hello world',
			'special' => 'hello world getter',
		), $variable->callNodeGetterAction($node, array(
			ActionVariable::GETTER_NODE_DATA => array(
				'node' => 'hello world',
			),
			ActionVariable::GETTER_NAME => 'node'
		)));
	}

	public function testCallNodeGetterActionWithActionTypeNotNode() {
		$node = $this->getAccessorMethodActionTypeNodeNode();
		$variable = $node->getVariable('node');
		$setterAction = $variable->getGetterAction();
		$setterAction['type'] = 'not-node';
		$variable->setGetterAction($setterAction);

		$this->assertNull($variable->callNodeGetterAction($node, array(
			ActionVariable::GETTER_NODE_DATA => array(
				'node' => 'hello world',
			),
			ActionVariable::GETTER_NAME => 'node'
		)));
	}


	public function testApplyToSetterNodeSimple() {
		$node = $this->getEncoderNodeVariableApplyToSetterNode();

		$object = $this->getEncoderNodeVariableApplyToSetter();
		$var = $node->getVariableById('node-simple');

		$this->assertEquals(array(
			'node' => 'test',
			'copied' => 'test',
		), $var->applyToSetter(array(
			ActionVariable::SETTER_OBJECT => $object,
			ActionVariable::SETTER_NODE_DATA => array(
				'node' => 'test'
			),
			ActionVariable::SETTER_NODE => $node,
			ActionVariable::SETTER_NAME => 'node',
			ActionVariable::SETTER_VALUE => 'test'
		)));

		$this->assertNull($object->getVar());
	}

	public function testApplyToSetterNodeFull() {
		$node = $this->getEncoderNodeVariableApplyToSetterNode();

		$object = $this->getEncoderNodeVariableApplyToSetter();
		$parent = $this->getThing();
		$var = $node->getVariableById('node-full');

		$this->assertEquals(array(
			'node' => 'test',
			'name' => 'node',
			'value' => 'test',
			'object' => $object,
			'parent' => $parent,
		), $var->applyToSetter(array(
			ActionVariable::SETTER_OBJECT => $object,
			ActionVariable::SETTER_PARENT => $parent,
			ActionVariable::SETTER_NODE_DATA => array(
				'node' => 'test'
			),
			ActionVariable::SETTER_NODE => $node,
			ActionVariable::SETTER_NAME => 'node',
			ActionVariable::SETTER_VALUE => 'test'
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
		$var = $node->getVariableById($variable);

		$this->assertEquals(array(
			'test' => 'altered',
		), $var->applyToSetter(array(
			ActionVariable::SETTER_OBJECT => $object,
			ActionVariable::SETTER_NODE_DATA => array(
				'test' => 'test'
			),
			ActionVariable::SETTER_NODE => $node,
			ActionVariable::SETTER_NAME => 'node',
			ActionVariable::SETTER_VALUE => 'test'
		)));

		$this->assertNull($object->getVar());
	}

	public function testApplyToSetterNodeUnknownVariable() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeVariableException', 'Action variable id "unknown_variable" is not known');
		$node = $this->getEncoderNodeVariableApplyToSetterNode();

		$object = $this->getEncoderNodeVariableApplyToSetter();
		$var = $node->getVariableById('node-unknown-variable');

		$var->applyToSetter(array(
			ActionVariable::SETTER_OBJECT => $object,
			ActionVariable::SETTER_NODE_DATA => array(
				'node' => 'test'
			),
			ActionVariable::SETTER_NODE => $node,
			ActionVariable::SETTER_NAME => 'node',
			ActionVariable::SETTER_VALUE => 'test'
		));
	}

	public function testApplyToSetterObjectWithSetterMethod() {
		$this->_applyToSetterObject('var');
		$this->_applyToSetterObject('object-using-setter-action');
		$this->_applyToSetterObject('object-using-setter-method');
	}

	public function testApplyToSetterObjectWithUnknownSetterMethod() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeVariableException', 'Method "unknownMethod" does not exist for class PE\Samples\Specials\EncoderNodeVariableApplyToSetter does not exist');
		$this->_applyToSetterObject('object-using-unknown-setter-method');
	}

	protected function _applyToSetterObject($variable) {
		$node = $this->getEncoderNodeVariableApplyToSetterNode();

		$object = $this->getEncoderNodeVariableApplyToSetter();
		$var = $node->getVariableById($variable);

		$this->assertTrue($var->applyToSetter(array(
			ActionVariable::SETTER_OBJECT => $object,
			ActionVariable::SETTER_VALUE => 'test'
		)));
		$this->assertEquals('test', $object->getVar());
	}
}