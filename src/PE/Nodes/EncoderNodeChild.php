<?php

namespace PE\Nodes;

use PE\Exceptions\EncoderNodeChildException;
use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;

class EncoderNodeChild {

	/**
	 * @var string
	 */
	private $childNodeName;

	/**
	 * @var NodeChildSetter Setter of the child objects
	 */
	private $setter;

	/**
	 * @var NodeChildGetter Setter of the child objects
	 */
	private $getter;

	/**
	 * @var bool
	 */
	private $isArray = true;

	function __construct($nodeName, NodeChildSetter $setter = null, NodeChildGetter $getter = null) {
		$this->setChildNodeName($nodeName);

		if ($setter) $this->setter($setter);
		if ($getter) $this->getter($getter);
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

	/**
	 * @param NodeChildSetter $setter
	 */
	protected function setter(NodeChildSetter $setter) {
		$this->setter = $setter;
	}
	/**
	 * @return NodeChildSetter
	 */
	public function getSetter() {
		return $this->setter;
	}

	/**
	 * @param NodeChildGetter $getter
	 */
	protected function getter(NodeChildGetter $getter) {
		$this->getter = $getter;
	}
	/**
	 * @return NodeChildGetter
	 */
	public function getGetter() {
		return $this->getter;
	}

	/**
	 * @param null $bool
	 * @return bool
	 */
	public function isArray($bool = null) {
		if ($bool !== null) {
			$this->isArray = (bool) $bool;
		}
		return $this->isArray;
	}

	/**
	 * Adds values to an object
	 *
	 * @param object $target The target object where the values are supposed to be added to
	 * @param array $values The values you want to add
	 * @return bool Returns true if the action succeeded and false when it couldn't find the child
	 */
	public function addChildrenToObject($target, $values) {
		$methodName = $this->getSetter()->getMethod();
		if ($methodName === null) {
			throw new EncoderNodeChildException(sprintf('Setter method (%s) for class "%s" does not exist', $this->getChildNodeName(), get_class($target)));
		}
		else if (method_exists($target, $methodName)) {
			foreach ($values as $value) {
				$target->$methodName($value);
			}
		}
		else {
			throw new EncoderNodeChildException(sprintf('Trying to add children to "%s" with method "%s", but this method does not exist', get_class($target), $methodName));
		}

		return true;
	}
}