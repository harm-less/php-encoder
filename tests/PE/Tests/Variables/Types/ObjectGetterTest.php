<?php

namespace PE\Tests\Variables\Types;

use PE\Nodes\EncoderNodeVariable;
use PE\Tests\Samples;
use PE\Variables\Types\ObjectGetter;

class ObjectGetterTest extends Samples
{

	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new ObjectGetter();
	}

	/**
	 * @return ObjectGetter
	 */
	protected function objectGetter() {
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$setter = new ObjectGetter('test');
		$this->assertNotNull($setter);
		$this->assertTrue($setter instanceof ObjectGetter);
	}

	public function testGetMethod() {
		$objectSetter = new ObjectGetter('method');
		$this->assertEquals('method', $objectSetter->getMethod());

		$objectSetter = new ObjectGetter();
		$objectSetter->setVariable(new EncoderNodeVariable('variableId'));
		$this->assertEquals('getVariableId', $objectSetter->getMethod());
	}

	public function testEncodeWithoutVariableGetterMethod() {
		$this->setExpectedException('\\PE\\Exceptions\\VariableTypeException', 'Method "getNonExistent" does not exist for class "PE\Tests\Variables\Types\ObjectGetterTestObject" does not exist');

		$objectGetter = $this->objectGetter();
		$objectGetter->setVariable(new EncoderNodeVariable('non-existent'));
		$objectGetter->apply(new ObjectGetterTestObject());
	}

	public function testMustBeUnique() {
		$objectGetter = $this->objectGetter();

		$this->assertFalse($objectGetter->mustBeUnique());

		$this->assertFalse($objectGetter->mustBeUnique(false));
		$this->assertFalse($objectGetter->mustBeUnique());
		$this->assertTrue($objectGetter->mustBeUnique(true));
		$this->assertTrue($objectGetter->mustBeUnique());
	}

	public function testAlwaysExecute() {
		$objectGetter = $this->objectGetter();

		$this->assertFalse($objectGetter->alwaysExecute());

		$this->assertFalse($objectGetter->alwaysExecute(false));
		$this->assertFalse($objectGetter->alwaysExecute());
		$this->assertTrue($objectGetter->alwaysExecute(true));
		$this->assertTrue($objectGetter->alwaysExecute());
	}
}

class ObjectGetterTestObject {

}
?>