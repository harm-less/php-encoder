<?php

namespace PE\Samples\Specials;

class RequiredVariable {

	protected $required;

	function setRequired($value) {
		$this->required = $value;
	}
	function getRequired() {
		return $this->required;
	}
}