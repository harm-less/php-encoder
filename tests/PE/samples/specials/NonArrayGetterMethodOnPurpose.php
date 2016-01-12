<?php

namespace PE\Samples\Specials;

class NonArrayGetterMethodOnPurpose {

	protected $vars = array();

	function __construct() {

	}
	function addThing($var) {
		array_push($this->vars, $var);
		return $var;
	}
	function getThing() {
		return $this->vars[0];
	}
}