<?php

namespace PE\Samples\Farm\Animals;

use PE\Samples\Farm\Animal;

class Chicken extends Animal {

	function __construct() {
		parent::__construct('Chicken');
		$this->setType('chicken');
	}

}