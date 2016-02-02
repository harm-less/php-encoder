<?php

namespace PE\Samples\Specials;

class AddAfterDecodeParent {

	private $name;

	private $child;
	private $childrenRequire;
	function __construct() {
		$this->child = array();
		$this->childrenRequire = array();
	}

	public function setName($value) {
		$this->name = $value;
	}
	public function getName() {
		return $this->name;
	}

	public function addChild($value) {
		$this->child = $value;
	}

	/**
	 * @return AddAfterDecodeChild
	 */
	public function getChild() {
		return $this->child;
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