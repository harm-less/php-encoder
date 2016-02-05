<?php

namespace PE\Samples\Specials;

class EncoderNodeVariableApplyToSetter {

	private $var;

	function __construct() {

	}

	public function setVar($value) {
		$this->var = $value;

		return true;
	}
	public function getVar() {
		return $this->var;
	}
}