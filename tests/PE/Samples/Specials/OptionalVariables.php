<?php

namespace PE\Samples\Specials;

class OptionalVariables {

	private $name;
	private $otherVariable;

	function __construct() {
	}

	function setName($value) {
		$this->name = $value;
	}
	function getName() {
		return $this->name;
	}

	function setOtherVariable($value) {
		$this->otherVariable = $value;
	}
	function getOtherVariable() {
		return $this->otherVariable;
	}
}