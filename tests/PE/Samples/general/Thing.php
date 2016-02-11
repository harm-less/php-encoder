<?php

namespace PE\Samples\General;

class Thing {

	private $thingVar;

	function __construct() {
	}

	public function setThingVar($var) {
		$this->thingVar = $var;
	}
	public function getThingVar() {
		return $this->thingVar;
	}

}