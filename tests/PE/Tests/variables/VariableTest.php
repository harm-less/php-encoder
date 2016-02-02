<?php

namespace PE\Tests\Variables;

use PE\Tests\AbstractPETest;
use PE\Variables\Variable;

class VariableTest extends AbstractPETest {

	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new Variable();
	}

	/**
	 * @return Variable
	 */
	protected function variable() {
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$variable = new Variable();
		$this->assertNotNull($variable);
		$this->assertTrue($variable instanceof Variable);
	}

	public function testParseOptionsMethodCombined() {
		$variable = $this->variable();
		$variable->parseOptions(array(
			'method' => 'methodName'
		));
		$this->assertEquals('methodName', $variable->getSetterMethod());
		$this->assertEquals('methodName', $variable->getGetterMethod());
	}
	public function testParseOptionsSetterGetter() {
		$variable = $this->variable();
		$variable->parseOptions(array(
			'setter' => 'setterMethodName',
			'getter' => 'getterMethodName',
		));
		$this->assertEquals('setterMethodName', $variable->getSetterMethod());
		$this->assertEquals('getterMethodName', $variable->getGetterMethod());
	}
	public function testParseOptionsDefaultSetExists() {
		$variable = $this->variable();
		$variable->parseOptions(array(
			'type' => Variable::TYPE_ARRAY,
		));
		$this->assertEquals(Variable::TYPE_ARRAY, $variable->getType());
	}

	public function testSetSetterMethod() {
		$variable = $this->variable();
		$variable->setSetterMethod('setterName');
		$this->assertEquals('setterName', $variable->getSetterMethod());
		$variable->setSetterMethod(null);
		$this->assertNull($variable->getSetterMethod());
	}
	public function testSetSetterMethodNoValue() {
		$this->setExpectedException('PE\\Exceptions\\VariableException', 'Setter method name cannot be empty');
		$variable = $this->variable();
		$variable->setSetterMethod('');
	}

	public function testSetGetterMethod() {
		$variable = $this->variable();
		$variable->setGetterMethod('getterName');
		$this->assertEquals('getterName', $variable->getGetterMethod());
		$variable->setGetterMethod(null);
		$this->assertNull($variable->getGetterMethod());
	}
	public function testSetGetterMethodNoValue() {
		$this->setExpectedException('PE\\Exceptions\\VariableException', 'Getter method name cannot be empty');
		$variable = $this->variable();
		$variable->setGetterMethod('');
	}

	public function testSetType() {
		$variable = $this->variable();
		$variable->setType(Variable::TYPE_ARRAY);
		$this->assertEquals(Variable::TYPE_ARRAY, $variable->getType());
		$variable->setType(Variable::TYPE_STRING);
		$this->assertEquals(Variable::TYPE_STRING, $variable->getType());
		$variable->setType(Variable::TYPE_BOOL);
		$this->assertEquals(Variable::TYPE_BOOL, $variable->getType());
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
		$this->assertEquals('test', $variable->processValue('test'));

		$variable->setType(Variable::TYPE_BOOL);
		$this->assertEquals(true, $variable->processValue(1));
		$this->assertEquals(true, $variable->processValue('1'));
		$this->assertEquals(true, $variable->processValue('true'));
		$this->assertEquals(false, $variable->processValue(0));
		$this->assertEquals(false, $variable->processValue('0'));
		$this->assertEquals(false, $variable->processValue('false'));
		$this->assertEquals(false, $variable->processValue('abc'));

		$variable->setType(Variable::TYPE_STRING);
		$this->assertEquals('1', $variable->processValue(1));
		$this->assertEquals('string', $variable->processValue('string'));

		$variable->setType(Variable::TYPE_ARRAY);
		$this->assertEquals(array(), $variable->processValue(json_encode(array())));
		$this->assertEquals(array('hello' => 'world'), $variable->processValue(json_encode(array('hello' => 'world'))));
	}

	public function testProcessValueArrayException() {
		$this->setExpectedException('PE\\Exceptions\\VariableException', 'The set data type is array but the value cannot be processed');
		$variable = $this->variable();
		$variable->setType(Variable::TYPE_ARRAY);
		$variable->processValue(array());
	}
	public function testProcessValueUnknownTypeException() {
		$this->setExpectedException('PE\\Exceptions\\VariableException', 'Can\'t process value "string" because the data type "unknown" isn\'t recognized.');
		$variable = $this->variable();
		$variable->setType('unknown');
		$variable->processValue('string');
	}
}