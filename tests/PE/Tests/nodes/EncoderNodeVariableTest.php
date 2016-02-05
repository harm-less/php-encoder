<?php

namespace PE\Tests\Nodes;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNodeVariable;
use PE\Tests\Samples;

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

	public function testConstructor()
	{
		$variable = new EncoderNodeVariable('var');
		$this->assertNotNull($variable);
		$this->assertTrue($variable instanceof EncoderNodeVariable);
	}

	public function testParseOptions() {
		$variable = $this->variable();
		$variable->parseOptions(array(
			'setterAction' => 'setterMethodName',
			'getterAction' => 'getterMethodName',
			'unique' => true,
			'alwaysExecute' => true
		));

		// setter
		$this->assertEquals('setVar', $variable->getSetterMethod());
		$this->assertEquals('setterMethodName', $variable->getSetterAction());
		$this->assertEquals(EncoderNodeVariable::ACTION_TYPE_OBJECT, $variable->getSetterActionType());
		$this->assertEquals('setterMethodName', $variable->getSetterActionMethod());

		// getter
		$this->assertEquals('getVar', $variable->getGetterMethod());
		$this->assertEquals('getterMethodName', $variable->getGetterAction());
		$this->assertEquals(EncoderNodeVariable::ACTION_TYPE_OBJECT, $variable->getGetterActionType());
		$this->assertEquals('getterMethodName', $variable->getGetterActionMethod());

		// unique
		$this->assertTrue($variable->mustBeUnique());

		// always execute
		$this->assertTrue($variable->alwaysExecute());
	}

	public function testMustBeUnique() {
		$variable = $this->variable();

		$this->assertFalse($variable->mustBeUnique());

		$this->assertFalse($variable->mustBeUnique(false));
		$this->assertFalse($variable->mustBeUnique());
		$this->assertTrue($variable->mustBeUnique(true));
		$this->assertTrue($variable->mustBeUnique());
	}

	public function testAlwaysExecute() {
		$variable = $this->variable();

		$this->assertFalse($variable->alwaysExecute());

		$this->assertFalse($variable->alwaysExecute(false));
		$this->assertFalse($variable->alwaysExecute());
		$this->assertTrue($variable->alwaysExecute(true));
		$this->assertTrue($variable->alwaysExecute());
	}

	public function testSetterAction() {
		$variable = $this->variable();

		$this->assertFalse($variable->hasSetterAction());

		$variable->setSetterAction('methodName');
		$this->assertEquals('methodName', $variable->getSetterAction());

		$this->assertTrue($variable->hasSetterAction());
	}
	public function testSetterActionMethod() {
		$variable = $this->variable();

		$this->assertNull($variable->getSetterActionMethod());

		$variable->setSetterAction('methodName');
		$this->assertEquals('methodName', $variable->getSetterActionMethod());

		$variable->setSetterAction(array(
			'method' => 'methodNameArray'
		));
		$this->assertEquals('methodNameArray', $variable->getSetterActionMethod());
		$this->assertEquals(EncoderNodeVariable::ACTION_TYPE_OBJECT, $variable->getSetterActionType());

		$variable->setSetterAction(array(
			'method' => 'methodNameArray',
			'type' => EncoderNodeVariable::ACTION_TYPE_NODE
		));
		$this->assertEquals('methodNameArray', $variable->getSetterActionMethod());
		$this->assertEquals(EncoderNodeVariable::ACTION_TYPE_NODE, $variable->getSetterActionType());
	}

	public function testGetterAction() {
		$variable = $this->variable();

		$this->assertFalse($variable->hasGetterAction());

		$variable->setGetterAction('methodName');
		$this->assertEquals('methodName', $variable->getGetterAction());

		$this->assertTrue($variable->hasGetterAction());
	}
	public function testGetterActionWhenWrongDataType() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeVariableException', 'Either method must be a string or an array with a "method" key being a string');
		$this->variable()->setGetterAction(1);
	}
	public function testGetterActionWhenEmptyArray() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderNodeVariableException', 'Either method must be a string or an array with a "method" key being a string');
		$this->variable()->setGetterAction(array());
	}
	public function testGetterActionMethod() {
		$variable = $this->variable();

		$this->assertNull($variable->getGetterActionMethod());

		$variable->setGetterAction('methodName');
		$this->assertEquals('methodName', $variable->getGetterActionMethod());

		$variable->setGetterAction(array(
			'method' => 'methodNameArray'
		));
		$this->assertEquals('methodNameArray', $variable->getGetterActionMethod());
		$this->assertEquals(EncoderNodeVariable::ACTION_TYPE_OBJECT, $variable->getGetterActionType());

		$variable->setGetterAction(array(
			'method' => 'methodNameArray',
			'type' => EncoderNodeVariable::ACTION_TYPE_NODE
		));
		$this->assertEquals('methodNameArray', $variable->getGetterActionMethod());
		$this->assertEquals(EncoderNodeVariable::ACTION_TYPE_NODE, $variable->getGetterActionType());
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
		), $var->applyToSetter($node, array(
			ActionVariable::SETTER_OBJECT => $object,
			ActionVariable::SETTER_NODE_DATA => array(
				'node' => 'test'
			),
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
		), $var->applyToSetter($node, array(
			ActionVariable::SETTER_OBJECT => $object,
			ActionVariable::SETTER_PARENT => $parent,
			ActionVariable::SETTER_NODE_DATA => array(
				'node' => 'test'
			),
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
		), $var->applyToSetter($node, array(
			ActionVariable::SETTER_OBJECT => $object,
			ActionVariable::SETTER_NODE_DATA => array(
				'test' => 'test'
			),
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

		$var->applyToSetter($node, array(
			ActionVariable::SETTER_OBJECT => $object,
			ActionVariable::SETTER_NODE_DATA => array(
				'node' => 'test'
			),
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

		$this->assertTrue($var->applyToSetter($node, array(
			ActionVariable::SETTER_OBJECT => $object,
			ActionVariable::SETTER_VALUE => 'test'
		)));
		$this->assertEquals('test', $object->getVar());
	}
}