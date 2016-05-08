<?php
/**
 * Is called after the object is made during decoding
 */

namespace PE\Variables\Types;

use PE\Exceptions\VariableTypeException;
use PE\Nodes\EncoderNodeVariable;

/**
 * Class ObjectSetter
 * @package PE\Variables\Types
 */
final class ObjectSetter extends ObjectAccessor {

	function __construct($method = null) {
		parent::__construct($method);
	}

	/**
	 * @return false|string Returns the setter method if available. Otherwise it returns false
	 */
	public function getMethod() {
		$method = parent::getMethod();
		if (!empty($method)) {
			return $method;
		}
		return 'set' . $this->camelCased($this->getVariable()->getId());
	}

	public function apply($object, $value) {
		return $this->_apply($object, array($this->processValue($value)));
	}

	/**
	 * Processes a value based on the set data type of the variable
	 * @param $value
	 * @return mixed
	 */
	public function processValue($value) {
		$type = $this->getVariable()->getType();
		if ($type !== null) {
			switch ($type) {
				case EncoderNodeVariable::TYPE_BOOL :
					if (is_string($value)) {
						return ($value === 'true' || $value === '1');
					}
					else {
						return (bool) $value;
					}
					break;
				case EncoderNodeVariable::TYPE_ARRAY :
					if (is_string($value)) {
						return (array) json_decode($value);
					}
					else {
						throw new VariableTypeException(sprintf('The set data type is array but the value cannot be processed'));
					}
					break;
				case EncoderNodeVariable::TYPE_STRING :
					return (string) $value;
					break;
				default :
					throw new VariableTypeException(sprintf('Can\'t process value "%s" because the data type "%s" isn\'t recognized.', (string) $value, $type));
			}
		}
		return $value;
	}
}