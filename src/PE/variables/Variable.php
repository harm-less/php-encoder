<?php

namespace PE\Variables;

use PE\Exceptions\EncoderException;
use PE\Library\Inflector;

class Variable {

	private $order;
	private $setterMethod;
	private $getterMethod;
	private $type;
	private $defaultValue;

	const TYPE_BOOL = 'bool';
	const TYPE_ARRAY = 'array';
	const TYPE_STRING = 'string';

	function __construct($options = null) {
		$this->parseOptions($options);
	}

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

	public function setSetterMethod($setterMethodName) {
		if ($setterMethodName === '') {
			throw new EncoderException('Setter method name cannot be empty');
		}
		$this->setterMethod = $setterMethodName;
	}
	public function getSetterMethod() {
		return $this->setterMethod;
	}

	public function setGetterMethod($getterMethodName) {
		if ($getterMethodName === '') {
			throw new EncoderException('Getter method name cannot be empty');
		}
		$this->getterMethod = $getterMethodName;
	}
	public function getGetterMethod() {
		return $this->getterMethod;
	}

	public function setType($type) {
		$this->type = $type;
	}
	public function getType() {
		return $this->type;
	}

	public function setDefaultValue($value) {
		$this->defaultValue = $value;
	}
	public function getDefaultValue() {
		return $this->defaultValue;
	}

	public function processValue($value) {
		$type = $this->getType();
		if ($type !== null) {
			switch ($type) {
				case self::TYPE_BOOL :
					if (is_string($value)) {
						return !($value === 'false' || $value === '0');
					}
					else {
						return (bool) $value;
					}
					break;
				default :
					throw new \Exception(sprintf('Can\'t process value "%s" because the data type "%s" isn\'t recognized.', (string) $value, $type));
			}
		}
		return $value;
	}

	public function containsName($name) {
		return false;
	}

	public function camelCased($str) {
		return ucfirst(Inflector::camelize($str, true, '-'));
	}

	public function setOrder($index) {
		$this->order = $index;
	}
	public function getOrder() {
		return $this->order;
	}
}