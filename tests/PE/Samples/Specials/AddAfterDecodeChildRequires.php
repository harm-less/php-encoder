<?php

namespace PE\Samples\Specials;

class AddAfterDecodeChildRequires {

	private $name;

	function __construct() {

	}

	public function setName($value) {
		$this->name = $value;
	}
	public function getName() {
		return $this->name;
	}
}