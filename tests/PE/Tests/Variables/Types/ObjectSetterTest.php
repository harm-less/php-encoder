<?php

namespace PE\Tests\Variables\Types;

use PE\Tests\AbstractPETest;
use PE\Variables\Types\ObjectSetter;

class ObjectSetterTest extends AbstractPETest
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
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);

		$this->setExpectedException('\\PE\\Exceptions\\VariableTypeException', 'Method "getNonExistent" does not exist for class "PE\Samples\Erroneous\NoVariableGetterMethod" does not exist');

		$this->objectSetter()->apply(new ObjectSetterTestObject(), 'value');
	}
}

class ObjectSetterTestObject {

}
?>