<?php

namespace PE\Tests\Nodes\Children;

use PE\Helpers\NodeVariables\EncoderNodeVariableBool;
use PE\Nodes\EncoderNodeVariable;
use PE\Tests\Samples;

class EncoderNodeVariableBoolTest extends Samples
{

	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new EncoderNodeVariableBool('method', 'isTest');
	}

	/**
	 * @return EncoderNodeVariableBool
	 */
	protected function variable()
	{
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$boolVariable = new EncoderNodeVariableBool('test', 'isTest');
		$this->assertNotNull($boolVariable);
		$this->assertTrue($boolVariable instanceof EncoderNodeVariableBool);

		$this->assertEquals(EncoderNodeVariable::TYPE_BOOL, $boolVariable->getType());
		$this->assertEquals('isTest', $boolVariable->getObjectSetter()->getMethod());
		$this->assertEquals('isTest', $boolVariable->getObjectGetter()->getMethod());
	}
}
?>