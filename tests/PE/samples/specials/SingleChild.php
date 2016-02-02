<?php

namespace PE\Samples\Specials;

class SingleChild {

	protected $thing;

	function __construct() {

	}
	function setThing($thing) {
		$this->thing = $thing;
	}
	function getThing() {
		return $this->thing;
	}
}