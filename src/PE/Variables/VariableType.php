<?php
/**
 * Variable type that determines when a variable can be called
 */

namespace PE\Variables;

/**
 * Class VariableType
 * @package PE\Variables
 */
abstract class VariableType {

	private $method;

	private $alwaysExecute;

	/**
	 * @var Variable
	 */
	private $variable;

	function __construct($method) {
		$this->method = $method;
	}

	public function getMethod() {
		return $this->method;
	}

	/**
	 * Sets the Variable the type is connected to
	 *
	 * @param $variable
	 */
	public function setVariable($variable) {
		$this->variable = $variable;
	}
	/**
	 * Gets the Variable the type is connected to
	 */
	public function getVariable() {
		return $this->variable;
	}

	/**
	 * This node variable is always executed even though there is not value set. This way you can force a value
	 * to be set manually
	 *
	 * @param null|bool $bool Set to true to enable the this variable to always be executed. Leave empty to retrieve the current value
	 * @return bool Default is false
	 */
	public function alwaysExecute($bool = null) {
		if ($bool !== null && is_bool($bool)) {
			$this->alwaysExecute = $bool;
		}
		return (bool) $this->alwaysExecute;
	}
}