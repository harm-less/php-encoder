<?php

namespace PE\Tests\Nodes;

use PE\Nodes\EncoderNodeVariable;
use PE\Nodes\EncoderNodeVariableCollection;
use PE\Tests\AbstractPETest;

class EncoderNodeVariableCollectionTest extends AbstractPETest {

	protected function setUp()
	{
		parent::setUp();
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

	public function testGetVariablesSetterAndGetterActionByType() {
		$collection = $this->variableCollection();

		$actionObject = array(
			'type' => EncoderNodeVariable::ACTION_TYPE_OBJECT,
			'method' => 'methodName',
		);
		$actionNode = array(
			'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
			'method' => 'methodName',
		);

		$variable = $this->collectionAddVariable(new EncoderNodeVariable('var'));
		$variable->setSetterAction($actionObject);
		$variable->setGetterAction($actionObject);
		$variable2 = $this->collectionAddVariable(new EncoderNodeVariable('var2'));
		$variable2->setSetterAction($actionNode);
		$variable2->setGetterAction($actionNode);
		$variable3 = $this->collectionAddVariable(new EncoderNodeVariable('var3'));
		$variable3->setSetterAction($actionNode);
		$variable3->setGetterAction($actionNode);

		// setter
		$this->assertEquals(array('var' => $variable), $collection->getVariablesSetterActionByType(EncoderNodeVariable::ACTION_TYPE_OBJECT));
		$this->assertEquals(array('var2' => $variable2, 'var3' => $variable3), $collection->getVariablesSetterActionByType(EncoderNodeVariable::ACTION_TYPE_NODE));

		// getter
		$this->assertEquals(array('var' => $variable), $collection->getVariablesGetterActionByType(EncoderNodeVariable::ACTION_TYPE_OBJECT));
		$this->assertEquals(array('var2' => $variable2, 'var3' => $variable3), $collection->getVariablesGetterActionByType(EncoderNodeVariable::ACTION_TYPE_NODE));
	}

	public function testAddVariableOldWay() {
		$this->setExpectedException('PE\\Exceptions\\EncoderNodeVariableException', 'Use "addNodeVariable" to add variables');
		$this->variableCollection()->addVariable(new EncoderNodeVariable('var'));
	}

	public function testVariablesAreValidWithData() {
		$collection = $this->variableCollection();

		$variable = $this->collectionAddVariable(new EncoderNodeVariable('var'));
		$variable->mustBeUnique(true);

		$this->assertTrue($collection->variablesAreValidWithData(array(array('var' => 'Test'))));
	}

	public function testVariablesAreNotValidWithData() {
		$this->setExpectedException('PE\\Exceptions\\EncoderNodeVariableException', 'Variable "var" must be unique but value "Test" is given at least twice');
		$collection = $this->variableCollection();

		$variable = $this->collectionAddVariable(new EncoderNodeVariable('var'));
		$variable->mustBeUnique(true);

		$collection->variablesAreValidWithData(array(array('var' => 'Test'), array('var' => 'Test')));
	}

	public function testGetAlwaysExecutedVariables() {
		$collection = $this->variableCollection();

		$variable = $this->collectionAddVariable(new EncoderNodeVariable('var'));
		$variable->alwaysExecute(true);

		$this->assertEquals(array('var' => $variable), $collection->getAlwaysExecutedVariables());
		// get from cache
		$this->assertEquals(array('var' => $variable), $collection->getAlwaysExecutedVariables());

		$this->collectionAddVariable(new EncoderNodeVariable('var2'));
		$variable3 = $this->collectionAddVariable(new EncoderNodeVariable('var3'));
		$variable3->alwaysExecute(true);

		// cache has been reset because new variables have been added
		$this->assertEquals(array('var' => $variable, 'var3' => $variable3), $collection->getAlwaysExecutedVariables());
	}

	protected function collectionAddVariable($variable = null) {
		$collection = $this->variableCollection();
		return $collection->addNodeVariable($variable ? $variable : new EncoderNodeVariable('var'));
	}
}