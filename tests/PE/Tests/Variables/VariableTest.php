<?php

namespace PE\Tests\Variables;

use PE\Tests\AbstractPETest;
use PE\Variables\Variable;

class VariableTest extends AbstractPETest {

	protected function setUp()
	{
		parent::setUp();
		$this->_peApp = new Variable();
	}

	/**
	 * @return Variable
	 */
	protected function variable() {
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$variable = new Variable();
		$this->assertNotNull($variable);
		$this->assertTrue($variable instanceof Variable);
	}


}