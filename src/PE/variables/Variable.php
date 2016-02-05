<?php
/**
 * Variable/attribute of a EncoderNode
 */

namespace PE\Variables;

use PE\Exceptions\VariableException;

/**
 * Class Variable
 * @package PE\Variables
 */
class Variable {

	/**
	 * @var string
	 */
	private $id;
	/**
	 * @var int
	 */
	private $order;
	/**
	 * @var string
	 */
	private $setterMethod;
	/**
	 * @var string
	 */
	private $getterMethod;
	/**
	 * @var string
	 */
	private $type;

	/**
	 * Boolean variable type
	 */
	const TYPE_BOOL = 'bool';
	/**
	 * Array variable type
	 */
	const TYPE_ARRAY = 'array';
	/**
	 * String variable type
	 */
	const TYPE_STRING = 'string';

	/**
	 * @param array|null $options Construct options
	 * @param string|null $id Id used in collection
	 */
	function __construct($options = null, $id = null) {
		$this->setId($id);
		$this->parseOptions($options);
	}

	/**
	 * Easy setup method for this class
	 * @param array $options
	 */
	public function parseOptions($options) {
		$options = (array) $options;
		foreach ($options as $option => $value) {
			switch ($option) {
				case 'method' :
					$this->setSetterMethod($value);
					$this->setGetterMethod($value);
					break;
				case 'setter' :
					$this->setSetterMethod($value);
					break;
				case 'getter' :
					$this->setGetterMethod($value);
					break;
				default :
					$method = 'set' . ucfirst($option);
					if (method_exists($this, $method)) {
						$this->$method($value);
					}
					else if (method_exists($this, $option)) {
						$this->$option($value);
					}
					break;
			}
		}
	}

	/**
	 * @param string $id
	 * @return null|string
	 */
	public function setId($id) {
		$this->id = $id;
		return $id;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * Method name of the corresponding node that sets the variable value
	 * @param string $setterMethodName
	 */
	public function setSetterMethod($setterMethodName) {
		if ($setterMethodName !== null && (!$setterMethodName || empty($setterMethodName))) {
			throw new VariableException(sprintf('A setter method for (%s) cannot be set because it has the wrong data type', $this->getId()));
		}
		$this->setterMethod = $setterMethodName;
	}
	/**
	 * @return string
	 */
	public function getSetterMethod() {
		return $this->setterMethod;
	}

	/**
	 * Method name of the corresponding node that gets the variable value
	 * @param string $getterMethodName
	 */
	public function setGetterMethod($getterMethodName) {
		if ($getterMethodName !== null && (!$getterMethodName || empty($getterMethodName))) {
			throw new VariableException(sprintf('A getter method for (%s) cannot be set because it has the wrong data type', $this->getId()));
		}
		$this->getterMethod = $getterMethodName;
	}
	/**
	 * @return string
	 */
	public function getGetterMethod() {
		return $this->getterMethod;
	}

	/**
	 * Sets the data type of the variable
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}
	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Set the order the variable (when it is a collection)
	 * @param int $index
	 */
	public function setOrder($index) {
		$this->order = $index;
	}
	/**
	 * @return int
	 */
	public function getOrder() {
		return $this->order;
	}

	/**
	 * Processes a value based on the set data type of the variable
	 * @param $value
	 * @return bool
	 */
	public function processValue($value) {
		$type = $this->getType();
		if ($type !== null) {
			switch ($type) {
				case self::TYPE_BOOL :
					if (is_string($value)) {
						return ($value === 'true' || $value === '1');
					}
					else {
						return (bool) $value;
					}
					break;
				case self::TYPE_ARRAY :
					if (is_string($value)) {
						return (array) json_decode($value);
					}
					else {
						throw new VariableException(sprintf('The set data type is array but the value cannot be processed'));
					}
					break;
				case self::TYPE_STRING :
					return (string) $value;
					break;
				default :
					throw new VariableException(sprintf('Can\'t process value "%s" because the data type "%s" isn\'t recognized.', (string) $value, $type));
			}
		}
		return $value;
	}
}