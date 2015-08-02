<?php

namespace PE\Nodes;

use PE\Exceptions\EncoderNodeChildException;

class EncoderNodeChildren {

	/**
	 * @var EncoderNodeChild[]
	 */
	private $children;

	function __construct() {
		$this->children = array();
	}

	public function addChild(EncoderNodeChild $child) {
        $nodeName = $child->getNodeName();
		if ($this->childExists($nodeName)) {
			return false;
		}
		$this->children[$nodeName] = $child;
		return $child;
	}

	public function getChild($childName) {
		$childName = strtolower($childName);
		if ($this->isChild($childName)) {
			return $this->children[$childName];
		}
		return null;
	}
	public function getChildren() {
		return $this->children;
	}

	public function childExists($childName) {
		$nodeName = strtolower($childName);
		return isset($this->children[$nodeName]);
	}
	public function isChild($nodeName) {
		return $this->childExists($nodeName);
	}

	public function addChildrenToObject($childName, $target, $values) {
		$child = strtolower($childName);
		$childNode = $this->getChild($child);
		if ($childNode) {
			$methodName = $childNode->getSetterMethod();
			if ($methodName === null) {
				throw new EncoderNodeChildException(sprintf('Setter method (%s) for class "%s" does not exist', $child, get_class($target)));
			}
			else if (method_exists($target, $methodName)) {
				foreach ($values as $value) {
					$target->$methodName($childNode->processValue($value));
				}
			}
			else {
				throw new EncoderNodeChildException(sprintf('Trying to add children to "%s" with method "%s", but this method does not exist', get_class($target), $methodName));
			}

			return true;
		}
		return false;
	}
} 