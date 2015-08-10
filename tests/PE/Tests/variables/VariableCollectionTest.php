<?php

namespace PE\Tests\Variables;

use PE\Tests\AbstractPETest;
use PE\Variables\Variable;
use PE\Variables\VariableCollection;

class VariableCollectionTest extends AbstractPETest {

	protected function setUp()
	{
		$this->_peApp = new VariableCollection();
	}

	/**
	 * @return VariableCollection
	 */
	protected function variableCollection() {
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$collection = new VariableCollection();
		$this->assertNotNull($collection);
		$this->assertTrue($collection instanceof VariableCollection);
	}

	public function testProcessValue() {
		$collection = $this->variableCollection();

		$variable = $this->collectionAddVariable(new Variable(null, 'var'));
		$variable->setId('var');
		$variable->setType(Variable::TYPE_BOOL);

		$variable2 = $this->collectionAddVariable(new Variable(null, 'var2'));
		$variable2->setId('var2');
		$variable2->setType(Variable::TYPE_ARRAY);

		$this->assertEquals(true, $collection->processValue('var', 'true'));
		$this->assertEquals(array('key' => 'value'), $collection->processValue('var2', '{"key":"value"}'));

		$this->assertNull($collection->processValue('nonExistingVariable', 'doesNotMatter'));
	}

	public function testAlterVariable() {
		$variable = $this->collectionAddVariableDefault();
		$collection = $this->variableCollection();

		$this->assertNull($variable->getGetterMethod());

		$collection->alterVariable('var', array('getter' => 'newMethod'));
		$this->assertEquals('newMethod', $variable->getGetterMethod());

		$this->assertFalse($collection->alterVariable('doesNotExist', array()));
	}

	public function testGetVariable() {
		$variable = $this->collectionAddVariableDefault();
		$collection = $this->variableCollection();

		$this->assertEquals($variable, $collection->getVariable('var'));
		$this->assertEquals($variable, $collection->getVariable($variable));
		$this->assertNull($collection->getVariable('doesNotExist'));
	}

	public function testGetVariableById() {
		$variable = $this->collectionAddVariableDefault();
		$collection = $this->variableCollection();

		$this->assertEquals($variable, $collection->getVariableById('var'));
		$this->assertNull($collection->getVariableById('doesNotExist'));
	}

	public function testGetVariables() {
		$variable = $this->collectionAddVariableDefault();

		$variable2 = $this->collectionAddVariable(new Variable(null, 'var2'));
		$variable2->setType(Variable::TYPE_ARRAY);

		$variable3 = $this->collectionAddVariable(new Variable(null, 'var3'));
		$variable3->setType(Variable::TYPE_ARRAY);

		$collection = $this->variableCollection();

		$this->assertEquals(array('var' => $variable, 'var2' => $variable2, 'var3' => $variable3), $collection->getVariables(false));
		$this->assertEquals(array($variable, $variable2, $variable3), $collection->getVariables());

		$variable2->setOrder(10);
		$this->assertEquals(array($variable2, $variable, $variable3), $collection->getVariables());

		$variable3->setOrder(15);
		$this->assertEquals(array($variable2, $variable3, $variable), $collection->getVariables());

		$variable->setOrder(5);
		$this->assertEquals(array($variable, $variable2, $variable3), $collection->getVariables());
	}

	public function testGetVariablesSameIndex() {
		$this->setExpectedException('PE\\Exceptions\\VariableCollectionException', 'Cannot order variables because position "10" is being used more than once');

		$variable = $this->collectionAddVariableDefault();
		$variable->setOrder(10);

		$variable2 = $this->collectionAddVariable(new Variable(null, 'var2'));
		$variable2->setOrder(10);

		$this->variableCollection()->getVariables();
	}

	public function testVariableExists() {
		$this->collectionAddVariableDefault();
		$collection = $this->variableCollection();

		$this->assertTrue($collection->variableExists('var'));
		$this->assertFalse($collection->variableExists('doesNotExist'));
	}



	protected function collectionAddVariable($variable = null) {
		$collection = $this->variableCollection();
		return $collection->addVariable($variable ? $variable : new Variable());
	}

	protected function collectionAddVariableDefault() {
		$variable = $this->collectionAddVariable(new Variable(null, 'var'));
		$variable->setType(Variable::TYPE_BOOL);
		return $variable;
	}
}