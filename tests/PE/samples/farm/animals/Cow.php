<?php

namespace PE\Samples\Farm\Animals;

use PE\Samples\Farm\Animal;

class Cow extends Animal {

	function __construct() {
		parent::__construct('Cow');
		$this->setType('cow');
	}
}