<?php

namespace PE\Samples\Erroneous;

class NonArrayGetterMethod {

	protected $vars = array();

	function __construct() {

	}
	function addThing($var) {
		array_push($this->vars, $var);
		return $var;
	}
	function getThings() {
		return 'I\'m a string but I should be an array';
	}
}