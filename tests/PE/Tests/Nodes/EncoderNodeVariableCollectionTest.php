<?php

namespace PE\Tests\Nodes;

use PE\Nodes\EncoderNodeVariable;
use PE\Nodes\EncoderNodeVariableCollection;
use PE\Tests\AbstractPETest;
use PE\Variables\Types\PostNodeGetter;
use PE\Variables\Types\PostNodeSetter;
use PE\Variables\Types\PreNodeGetter;
use PE\Variables\Types\PreNodeSetter;

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

	public function testProcessValue() {
		$collection = $this->variableCollection();

		$variable = $this->collectionAddVariable(new EncoderNodeVariable('var'));
		$variable->setType(EncoderNodeVariable::TYPE_BOOL);

		$variable2 = $this->collectionAddVariable(new EncoderNodeVariable('var2'));
		$variable2->setType(EncoderNodeVariable::TYPE_ARRAY);

		$varObjectSetter = $collection->getVariableById('var')->getObjectSetter();
		$this->assertEquals(true, $varObjectSetter->processValue('true'));

		$var2ObjectSetter = $collection->getVariableById('var2')->getObjectSetter();
		$this->assertEquals(array('key' => 'value'), $var2ObjectSetter->processValue('{"key":"value"}'));
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

		$variable2 = $this->collectionAddVariable(new EncoderNodeVariable('var2'));
		$variable2->setType(EncoderNodeVariable::TYPE_ARRAY);

		$collection = $this->variableCollection();

		$this->assertEquals(array('var' => $variable, 'var2' => $variable2), $collection->getVariables());
	}

	public function testGetVariablesSameIndex() {
		$this->setExpectedException('PE\\Exceptions\\EncoderNodeVariableCollectionException', 'Cannot order variables because position "10" is being used more than once');

		$variable = $this->collectionAddVariableDefault();
		$variable->setOrder(10);

		$variable2 = $this->collectionAddVariable(new EncoderNodeVariable('var2'));
		$variable2->setOrder(10);

		$this->variableCollection()->getObjectSetterVariables();
	}

	public function testVariableExists() {
		$this->collectionAddVariableDefault();
		$collection = $this->variableCollection();

		$this->assertTrue($collection->variableExists('var'));
		$this->assertFalse($collection->variableExists('doesNotExist'));
	}




	protected function collectionAddVariableDefault() {
		$variable = $this->collectionAddVariable(new EncoderNodeVariable('var'));
		$variable->setType(EncoderNodeVariable::TYPE_BOOL);
		return $variable;
	}


	public function testGetVariablesNodeSetterAndGetter() {
		$collection = $this->variableCollection();

		$variable = $this->collectionAddVariable(new EncoderNodeVariable('var'));
		$variable->preNodeSetter(new PreNodeSetter('preNodeSetter'));
		$variable->preNodeGetter(new PreNodeGetter('preNodeGetter'));
		$variable->setOrder(3);
		$variable2 = $this->collectionAddVariable(new EncoderNodeVariable('var2'));
		$variable2->preNodeSetter(new PreNodeSetter('preNodeSetter2'));
		$variable2->preNodeGetter(new PreNodeGetter('preNodeGetter2'));
		$variable2->postNodeSetter(new PostNodeSetter('postNodeSetter2'));
		$variable2->postNodeGetter(new PostNodeGetter('postNodeGetter2'));
		$variable2->setOrder(2);
		$variable3 = $this->collectionAddVariable(new EncoderNodeVariable('var3'));
		$variable3->postNodeSetter(new PostNodeSetter('postNodeSetter3'));
		$variable3->postNodeGetter(new PostNodeGetter('postNodeGetter3'));
		$variable3->setOrder(1);

		// pre setter
		$preSetters = $collection->getPreNodeSetterVariables(false);
		$this->assertEquals(array($variable, $variable2), $preSetters);

		$preSettersSorted = $collection->getPreNodeSetterVariables();
		$this->assertEquals(array($variable2, $variable), $preSettersSorted);

		// pre getter
		$preSetters = $collection->getPreNodeGetterVariables(false);
		$this->assertEquals(array($variable, $variable2), $preSetters);

		$preSettersSorted = $collection->getPreNodeGetterVariables();
		$this->assertEquals(array($variable2, $variable), $preSettersSorted);


		// post setter
		$preSetters = $collection->getPostNodeSetterVariables(false);
		$this->assertEquals(array($variable2, $variable3), $preSetters);

		$preSettersSorted = $collection->getPostNodeSetterVariables();
		$this->assertEquals(array($variable3, $variable2), $preSettersSorted);

		// post getter
		$preSetters = $collection->getPostNodeGetterVariables(false);
		$this->assertEquals(array($variable2, $variable3), $preSetters);

		$preSettersSorted = $collection->getPostNodeGetterVariables();
		$this->assertEquals(array($variable3, $variable2), $preSettersSorted);
	}

	public function testVariablesAreValidWithData() {
		$collection = $this->variableCollection();

		$variable = $this->collectionAddVariable(new EncoderNodeVariable('var'));
		$variable->getObjectSetter()->mustBeUnique(true);

		$this->assertTrue($collection->objectVariablesAreValidWithData(array(array('var' => 'Test'))));
		$this->assertFalse($collection->objectVariablesAreValidWithData(array(array('var' => 'Test'), array('var' => 'Test'))));
	}

	public function testVariablesAreNotValidWithData() {
		$this->setExpectedException('PE\\Exceptions\\EncoderNodeVariableException', 'Variable "var" must be unique but value "Test" is given at least twice');
		$collection = $this->variableCollection();

		$variable = $this->collectionAddVariable(new EncoderNodeVariable('var'));
		$variable->getObjectSetter()->mustBeUnique(true);

		$collection->objectVariablesAreValidWithData(array(array('var' => 'Test'), array('var' => 'Test')), true);
	}

	/**
	 * @param null $variable
	 * @return EncoderNodeVariable
	 */
	protected function collectionAddVariable($variable = null) {
		$collection = $this->variableCollection();
		return $collection->addVariable($variable ? $variable : new EncoderNodeVariable('var'));
	}
}