<?php

namespace PE\Samples\General;

class Things {

	private $things;

	function __construct() {
		$this->things = array();
	}

	public function addThing($thing) {
		array_push($this->things, $thing);
	}
	public function getThings() {
		return $this->things;
	}
}