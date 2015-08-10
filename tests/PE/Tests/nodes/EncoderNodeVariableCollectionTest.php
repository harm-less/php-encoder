<?php

namespace PE\Tests\Nodes;

use PE\Nodes\EncoderNodeVariable;
use PE\Nodes\EncoderNodeVariableCollection;
use PE\Tests\AbstractPETest;
use PE\Variables\Variable;

class EncoderNodeVariableCollectionTest extends AbstractPETest {

	protected function setUp()
	{
		$this->_peApp = new EncoderNodeVariableCollection();
	}

	/**
	 * @return EncoderNodeVariableCollection
	 */
	protected function variableCollection() {
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$collection = new EncoderNodeVariableCollection();
		$this->assertNotNull($collection);
		$this->assertTrue($collection instanceof EncoderNodeVariableCollection);
	}

	public function testGetVariablesSetterActionByType() {
		$collection = $this->variableCollection();

		$variable = $this->collectionAddVariable(new EncoderNodeVariable('var'));

		$variable2 = $this->collectionAddVariable(new EncoderNodeVariable('var2'));

		$variable3 = $this->collectionAddVariable(new EncoderNodeVariable('var3'));

		//$this->assertEquals(array($variable), $collection->getVariablesSetterActionByType(EncoderNodeVariable::ACTION_TYPE_OBJECT));
		//$this->assertEquals(array($variable2, $variable3), $collection->getVariablesSetterActionByType(EncoderNodeVariable::ACTION_TYPE_OBJECT));
	}

	protected function collectionAddVariable($variable = null) {
		$collection = $this->variableCollection();
		return $collection->addNodeVariable($variable ? $variable : new EncoderNodeVariable('var'));
	}
}