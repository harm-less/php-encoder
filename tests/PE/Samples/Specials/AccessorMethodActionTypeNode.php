<?php

namespace PE\Samples\Specials;

class AccessorMethodActionTypeNode {

	private $special;

	public function setSpecial($value) {
		$this->special = $value;
	}
	public function getSpecial() {
		return $this->special;
	}
}