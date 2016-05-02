<?php
/**
 * Is called after the object is made during decoding
 */

namespace PE\Variables\Types;

/**
 * Class ObjectGetter
 * @package PE\Variables\Types
 */
final class ObjectGetter extends ObjectAccessor {

	function __construct($method = null) {
		parent::__construct($method);
	}

	/**
	 * @return false|string Returns the getter method if available. Otherwise it returns false
	 */
	public function getMethod() {
		$method = parent::getMethod();
		if (!empty($method)) {
			return $method;
		}
		return 'get' . $this->camelCased($this->getVariable()->getId());
	}

	public function apply($object) {
		return $this->_apply($object, array());
	}
}