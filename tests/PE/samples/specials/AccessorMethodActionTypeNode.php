<?php

namespace PE\Samples\Specials;

class AccessorMethodActionTypeNode {

	private $special;
	function __construct() {

	}

	public function setSpecial($value) {
		$this->special = $value;
	}
	public function getSpecial() {
		return $this->special;
	}
}