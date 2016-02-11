<?php

namespace PE\Samples\Erroneous;

class NoGetterMethod {

	protected $vars = array();

	function __construct() {

	}
	function addThing($var) {
		array_push($this->vars, $var);
		return $var;
	}
}