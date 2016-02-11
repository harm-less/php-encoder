<?php

namespace PE\Nodes;

use PE\Exceptions\EncoderNodeChildException;

class EncoderNodeChild extends EncoderNodeVariable {

	private $childNodeName;
	private $isArray = true;
	private $setAfterChildren = true;
	private $setAfterAttributes = true;

	function __construct($nodeName, $options = null) {
		parent::__construct('', $options);
		$this->setChildNodeName($nodeName);
	}

	public function setChildNodeName($childNodeName) {
		if ($childNodeName === null || $childNodeName === '') {
			throw new EncoderNodeChildException('Node name cannot be null or empty');
		}
		$this->childNodeName = $childNodeName;
	}
	public function getChildNodeName() {
		return $this->childNodeName;
	}

	// @todo Figure out if this feature is still necessary. Because if you use a single node, can't we simply assume it isn't an array?
	public function isArray($bool = null) {
		if ($bool !== null) {
			$this->isArray = $bool;
		}
		return $this->isArray;
	}

	public function setAfterChildren($bool = null) {
		if ($bool !== null) {
			$this->setAfterChildren = $bool;
		}
		return $this->setAfterChildren;
	}

	public function setAfterAttributes($bool = null) {
		if ($bool !== null) {
			$this->setAfterAttributes = $bool;
		}
		return $this->setAfterAttributes;
	}

	/**
	 * Adds values to an object
	 *
	 * @param object $target The target object where the values are supposed to be added to
	 * @param array $values The values you want to add
	 * @return bool Returns true if the action succeeded and false when it couldn't find the child
	 */
	public function addChildrenToObject($target, $values) {
		$methodName = $this->getSetterMethod();
		if ($methodName === null) {
			throw new EncoderNodeChildException(sprintf('Setter method (%s) for class "%s" does not exist', $this->getChildNodeName(), get_class($target)));
		}
		else if (method_exists($target, $methodName)) {
			foreach ($values as $value) {
				$target->$methodName($this->processValue($value));
			}
		}
		else {
			throw new EncoderNodeChildException(sprintf('Trying to add children to "%s" with method "%s", but this method does not exist', get_class($target), $methodName));
		}

		return true;
	}
}