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
		$collection = new VariableCollection();
		$variable = $this->collectionAddVariable();
		$variable->setId('var');
		$variable->setType(Variable::TYPE_BOOL);
		$this->assertEquals(true, $collection->processValue('var', 'true'));
	}

	protected function collectionAddVariable($variable = null) {
		$collection = $this->variableCollection();
		return $collection->addVariable($variable ? $variable : new Variable());
	}
}