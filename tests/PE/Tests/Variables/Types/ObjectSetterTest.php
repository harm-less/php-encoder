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
	protected function objectSetter()
	{
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$setter = new ObjectSetter('test');
		$this->assertNotNull($setter);
		$this->assertTrue($setter instanceof ObjectSetter);
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

	public function testEncodeWithoutVariableGetterMethod()
	{
		$this->setExpectedException('\\PE\\Exceptions\\VariableTypeException', 'Method "setNonExistent" does not exist for class "PE\Tests\Variables\Types\ObjectSetterTestObject');

		$objectSetter = $this->objectSetter();
		$objectSetter->setVariable(new EncoderNodeVariable('non-existent'));

		$this->objectSetter()->apply(new ObjectSetterTestObject(), 'value');
	}

	public function testApplyToSetterObjectWithSetterMethod() {
		$node = $this->getEncoderNodeVariableApplyToSetterNode();

		$object = $this->getEncoderNodeVariableApplyToSetter();
		$collection = $node->getVariableCollection();
		$var = $collection->getVariableById('var');

		$this->assertTrue($var->getObjectSetter()->apply($object, 'test'));
		$this->assertEquals('test', $object->getVar());
	}
}

class ObjectSetterTestObject {

}
?>