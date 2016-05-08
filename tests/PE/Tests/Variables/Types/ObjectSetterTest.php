<?php

namespace PE\Tests\Variables\Types;

use PE\Nodes\EncoderNodeVariable;
use PE\Tests\Samples;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\ObjectSetter;

class ObjectSetterTest extends Samples
{

	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new ObjectSetter();
	}

	/**
	 * @return ObjectSetter
	 */
	protected function objectSetter() {
		return $this->_peApp;
	}

	protected function objectSetterWithVariable() {
		$objectSetter = $this->objectSetter();
		$objectSetter->setVariable(new EncoderNodeVariable('var'));
		return $objectSetter;
	}

	public function testConstructor()
	{
		$setter = new ObjectSetter('test');
		$this->assertNotNull($setter);
		$this->assertTrue($setter instanceof ObjectSetter);
	}

	public function testGetMethod() {
		$objectSetter = new ObjectSetter('method');
		$this->assertEquals('method', $objectSetter->getMethod());

		$objectSetter = new ObjectSetter();
		$objectSetter->setVariable(new EncoderNodeVariable('variableId'));
		$this->assertEquals('setVariableId', $objectSetter->getMethod());
	}

	public function testRequired() {
		$objectSetter = $this->objectSetter();

		$this->assertFalse($objectSetter->required());

		$this->assertFalse($objectSetter->required(false));
		$this->assertFalse($objectSetter->required());
		$this->assertTrue($objectSetter->required(true));
		$this->assertTrue($objectSetter->required());
	}

	public function testEncodeWithoutVariableGetterMethod() {
		$this->setExpectedException('\\PE\\Exceptions\\VariableTypeException', 'Method "setNonExistent" does not exist for class "PE\Tests\Variables\Types\ObjectSetterTestObject');

		$objectSetter = $this->objectSetter();
		$objectSetter->setVariable(new EncoderNodeVariable('nonExistent'));
		$objectSetter->apply(new ObjectSetterTestObject(), 'value');
	}

	public function testApplyToSetterObjectWithSetterMethod() {
		$node = $this->getEncoderNodeVariableApplyToSetterNode();

		$object = $this->getEncoderNodeVariableApplyToSetter();
		$collection = $node->getVariableCollection();
		$var = $collection->getVariableById('var');

		$this->assertTrue($var->getObjectSetter()->apply($object, 'test'));
		$this->assertEquals('test', $object->getVar());
	}

	public function testProcessValue() {
		$objectSetter = $this->objectSetterWithVariable();
		$variable = $objectSetter->getVariable();
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
		$objectSetter = $this->objectSetterWithVariable();
		$variable = $objectSetter->getVariable();
		$variable->setType(EncoderNodeVariable::TYPE_ARRAY);
		$objectSetter->processValue(array());
	}
	public function testProcessValueUnknownTypeException() {
		$this->setExpectedException('PE\\Exceptions\\VariableTypeException', 'Can\'t process value "string" because the data type "unknown" isn\'t recognized.');
		$objectSetter = $this->objectSetterWithVariable();
		$variable = $objectSetter->getVariable();
		$variable->setType('unknown');
		$objectSetter->processValue('string');
	}

	public function testMustBeUnique() {
		$objectSetter = $this->objectSetter();

		$this->assertFalse($objectSetter->mustBeUnique());

		$this->assertFalse($objectSetter->mustBeUnique(false));
		$this->assertFalse($objectSetter->mustBeUnique());
		$this->assertTrue($objectSetter->mustBeUnique(true));
		$this->assertTrue($objectSetter->mustBeUnique());
	}

	public function testAlwaysExecute() {
		$objectSetter = $this->objectSetter();

		$this->assertFalse($objectSetter->alwaysExecute());

		$this->assertFalse($objectSetter->alwaysExecute(false));
		$this->assertFalse($objectSetter->alwaysExecute());
		$this->assertTrue($objectSetter->alwaysExecute(true));
		$this->assertTrue($objectSetter->alwaysExecute());
	}
}

class ObjectSetterTestObject {

}
?>