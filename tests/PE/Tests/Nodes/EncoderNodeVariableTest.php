<?php

namespace PE\Tests\Nodes;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNodeVariable;
use PE\Tests\Samples;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\ObjectGetter;
use PE\Variables\Types\ObjectSetter;
use PE\Variables\Types\PostNodeSetter;

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
}