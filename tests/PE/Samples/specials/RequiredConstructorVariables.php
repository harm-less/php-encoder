<?php

namespace PE\Samples\Specials;

class RequiredConstructorVariables {

	private $name;
	private $variableCategory;
	private $optional;

	function __construct($name, $variableCategory, $optional = true) {
		$this->name = $name;
		$this->setVariableCategory($variableCategory);
		$this->setOptional($optional);
	}

	function setName($value) {
		$this->name = $value;
	}
	function getName() {
		return $this->name;
	}
	function setVariableCategory($value) {
		$this->variableCategory = $value;
	}
	function getVariableCategory() {
		return $this->variableCategory;
	}

	function setOptional($value) {
		$this->optional = $value;
	}
	function getOptional() {
		return $this->optional;
	}
}