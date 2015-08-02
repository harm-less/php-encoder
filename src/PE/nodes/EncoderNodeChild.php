<?php

namespace PE\Nodes;

use PE\Exceptions\EncoderNodeChildException;

class EncoderNodeChild extends EncoderNodeVariable {

	private $nodeName;
	private $isArray = true;
	private $setAfterChildren = true;
	private $setAfterAttributes = true;

	function __construct($nodeName, $options = null) {

		parent::__construct('', $options);

		$this->setNodeName($nodeName);
	}

	public function setNodeName($nodeName) {
		if ($nodeName === null || $nodeName === '') {
			throw new EncoderNodeChildException('Node name cannot be null or empty');
		}
		$this->nodeName = $nodeName;
	}
	public function getNodeName() {
		return $this->nodeName;
	}

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
}