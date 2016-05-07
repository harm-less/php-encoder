<?php

namespace PE\Nodes\Children;

final class NodeChildSetter extends NodeChildAccessor {

	private $setAfterChildren = true;
	private $setAfterAttributes = true;

	function __construct($method) {
		parent::__construct($method);
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

?>