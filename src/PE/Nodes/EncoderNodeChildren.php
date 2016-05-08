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

	/**
	 * @param EncoderNodeChild $child Child you want to add to the node
	 * @return false|EncoderNodeChild Return child object if succeeded. Returns false if it failed
	 */
	public function addChild(EncoderNodeChild $child) {
        $nodeName = $child->getChildNodeName();
		if ($this->childExists($nodeName)) {
			return false;
		}
		$this->children[$nodeName] = $child;
		return $child;
	}

	/**
	 * @param string $childName Name of the child you want to return
	 * @return null|EncoderNodeChild Returns the requested child or returns null if it's not found
	 */
	public function getChild($childName) {
		if ($this->childExists($childName)) {
			return $this->children[$childName];
		}
		return null;
	}

	/**
	 * @return EncoderNodeChild[] Returns all registered children
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * @param string $childName Name of the child
	 * @return bool Returns true if the child exists based on its name
	 */
	public function childExists($childName) {
		return isset($this->children[$childName]);
	}

	/**
	 * Adds values to an object based on a single child
	 *
	 * @param string $childName Name of the child so it knows what method to use
	 * @param object $target The target object where the values are supposed to be added to
	 * @param array $values The values you want to add
	 * @return bool Returns true if the action succeeded and false when it couldn't find the child
	 */
	public function addChildrenToObject($childName, $target, $values) {
		$childNode = $this->getChild($childName);
		if (!$childNode) {
			throw new EncoderNodeChildException(sprintf('Trying to add children to object, but the child "%s" could not be found', $childName));
		}
		return $childNode->addChildrenToObject($target, $values);
	}
} 