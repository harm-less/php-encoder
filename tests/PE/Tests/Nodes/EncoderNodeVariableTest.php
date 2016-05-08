<?php

namespace PE\Tests\Nodes;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNodeVariable;
use PE\Tests\Samples;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\ObjectGetter;
use PE\Variables\Types\ObjectSetter;
use PE\Variables\Types\PostNodeGetter;
use PE\Variables\Types\PostNodeSetter;
use PE\Variables\Types\PreNodeGetter;
use PE\Variables\Types\PreNodeSetter;

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
		$this->assertEquals('PE\\Variables\\Types\\ObjectSetter', get_class($variable->getObjectSetter()));
		$this->assertEquals('PE\\Variables\\Types\\ObjectGetter', get_class($variable->getObjectGetter()));

		$variable = new EncoderNodeVariable('var', false);
		$this->assertNull($variable->getObjectSetter());
		$this->assertNull($variable->getObjectGetter());
	}

	public function testGetId() {
		$variable = $this->variable();
		$this->assertEquals('var', $variable->getId());
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


	public function testPreNodeSetter() {
		$variable = $this->newVariable('var', false);
		$this->assertNull($variable->getPreNodeSetter());
		$this->assertFalse($variable->hasPreNodeSetter());
		$nodeSetter = new PreNodeSetter('setterMethod');
		$variable->preNodeSetter($nodeSetter);
		$this->assertEquals($nodeSetter, $variable->getPreNodeSetter());
		$this->assertTrue($variable->hasPreNodeSetter());
		$this->assertEquals($variable, $nodeSetter->getVariable());
	}
	public function testPreNodeGetter() {
		$variable = $this->newVariable('var', false);
		$this->assertNull($variable->getPreNodeGetter());
		$this->assertFalse($variable->hasPreNodeGetter());
		$nodeGetter = new PreNodeGetter('getterMethod');
		$variable->preNodeGetter($nodeGetter);
		$this->assertEquals($nodeGetter, $variable->getPreNodeGetter());
		$this->assertTrue($variable->hasPreNodeGetter());
		$this->assertEquals($variable, $nodeGetter->getVariable());
	}

	public function testPostNodeSetter() {
		$variable = $this->newVariable('var', false);
		$this->assertNull($variable->getPostNodeSetter());
		$this->assertFalse($variable->hasPostNodeSetter());
		$nodeSetter = new PostNodeSetter('setterMethod');
		$variable->postNodeSetter($nodeSetter);
		$this->assertEquals($nodeSetter, $variable->getPostNodeSetter());
		$this->assertTrue($variable->hasPostNodeSetter());
		$this->assertEquals($variable, $nodeSetter->getVariable());
	}
	public function testPostNodeGetter() {
		$variable = $this->newVariable('var', false);
		$this->assertNull($variable->getPostNodeGetter());
		$this->assertFalse($variable->hasPostNodeGetter());
		$nodeGetter = new PostNodeGetter('getterMethod');
		$variable->postNodeGetter($nodeGetter);
		$this->assertEquals($nodeGetter, $variable->getPostNodeGetter());
		$this->assertTrue($variable->hasPostNodeGetter());
		$this->assertEquals($variable, $nodeGetter->getVariable());
	}

	public function testObjectSetter() {
		$variable = $this->newVariable('var', false);
		$this->assertNull($variable->getObjectSetter());
		$this->assertFalse($variable->hasObjectSetter());
		$objectSetter = new ObjectSetter('setterMethod');
		$variable->objectSetter($objectSetter);
		$this->assertEquals($objectSetter, $variable->getObjectSetter());
		$this->assertTrue($variable->hasObjectSetter());
		$this->assertEquals($variable, $objectSetter->getVariable());

		// disable the object setter
		$variable->objectSetter(null);
		$this->assertNull($variable->getObjectSetter());
	}

	public function testObjectGetter() {
		$variable = $this->newVariable('var', false);
		$this->assertNull($variable->getObjectGetter());
		$this->assertFalse($variable->hasObjectGetter());
		$objectGetter = new ObjectGetter('setterMethod');
		$variable->objectGetter($objectGetter);
		$this->assertEquals($objectGetter, $variable->getObjectGetter());
		$this->assertTrue($variable->hasObjectGetter());
		$this->assertEquals($variable, $objectGetter->getVariable());

		// disable the object setter
		$variable->objectGetter(null);
		$this->assertNull($variable->getObjectGetter());
	}
}