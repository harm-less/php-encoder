<?php
/**
 * Is called after the object is made during decoding
 */

namespace PE\Variables\Types;

use PE\Exceptions\VariableTypeException;
use PE\Library\Inflector;
use PE\Variables\VariableType;

/**
 * Class ObjectVariable
 * @package PE\Variables\Types
 */
abstract class ObjectAccessor extends VariableType {

	private $mustBeUnique;

	function __construct($method = null) {
		parent::__construct($method);
	}

	/**
	 * This node variable value must unique in relation to other nodes in a series
	 *
	 * @param null|bool $bool Set to true to enable the this variable to be unique. Leave empty to retrieve the current value
	 * @return bool Default is false
	 */
	public function mustBeUnique($bool = null) {
		if ($bool !== null && is_bool($bool)) {
			$this->mustBeUnique = $bool;
		}
		return (bool) $this->mustBeUnique;
	}

	/**
	 * @param string $str Spinal cased string
	 * @return string Camel cased string
	 */
	protected function camelCased($str) {
		return ucfirst(Inflector::camelize($str, true, '-'));
	}

	/**
	 * Applies parameters to a certain object method
	 *
	 * @param object $object The object you want to have called
	 * @param mixed $parameters The parameters this method should receive
	 * @return mixed Returns whatever the object returns
	 */
	protected function _apply($object, $parameters) {
		$methodName = $this->getMethod();
		if (!method_exists($object, $methodName)) {
			throw new VariableTypeException(sprintf('Method "%s" does not exist for class %s does not exist', $methodName, get_class($object)));
		}
		else {
			return call_user_func_array(array($object, $methodName), $parameters);
		}
	}
}