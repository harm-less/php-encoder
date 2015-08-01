<?php

namespace PH\Tests;

use PE\Encoder;
use PHPUnit_Framework_TestCase;

/**
 * Base test class for PHP Unit testing
 */
abstract class AbstractPETest extends PHPUnit_Framework_TestCase
{

	/**
	 * The automatically created test PH instance
	 * (for easy testing and less boilerplate)
	 *
	 * @type Encoder
	 */
	protected $_phApp;

	protected $_nonPublicMethodObject;

	/**
	 * Setup our test
	 * (runs before each test)
	 *
	 * @return void
	 */
	protected function setUp()
	{
		// Create a new FQ app,
		// since we need one pretty much everywhere
		$this->_phApp = new Encoder();
	}

	protected function nonPublicMethodObject($object = null) {
		if ($object !== null) {
			$this->_nonPublicMethodObject = $object;
		}
		return $this->_nonPublicMethodObject;
	}

	protected function callNonPublicMethod($name, $args) {
		return $this->callObjectWithNonPublicMethod($this->nonPublicMethodObject(), $name, $args);
	}
	protected function callObjectWithNonPublicMethod($obj, $name, $args) {
		$class = new \ReflectionClass($obj);
		$method = $class->getMethod($name);
		$method->setAccessible(true);

		$args = is_array($args) ? $args : (array) $args;
		return $method->invokeArgs($obj, $args);
	}
}
