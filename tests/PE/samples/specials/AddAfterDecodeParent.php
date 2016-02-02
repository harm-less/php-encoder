<?php

namespace PE\Samples\Specials;

class AddAfterDecodeParent {

	private $children;
	private $childrenRequire;
	function __construct() {
		$this->children = array();
		$this->childrenRequire = array();
	}

	public function addChild($value) {
		array_push($this->children, $value);
	}

	/**
	 * @return AddAfterDecodeChild[]
	 */
	public function getChildren() {
		return $this->children;
	}


	public function addChildRequires($value) {
		array_push($this->childrenRequire, $value);
	}

	/**
	 * @return AddAfterDecodeChildRequires[]
	 */
	public function getChildrenRequire() {
		return $this->childrenRequire;
	}
}