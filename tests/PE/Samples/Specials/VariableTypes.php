<?php

namespace PE\Samples\Specials;

class VariableTypes {

	protected $required;
	protected $optional;

	function __construct($required) {
		$this->required = $required;
	}
	function getRequired() {
		return $this->required;
	}

	function setOptional($value) {
		$this->optional = $value;
	}
	function getOptional() {
		return $this->optional;
	}
}