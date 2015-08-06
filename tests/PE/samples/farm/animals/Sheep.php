<?php

namespace PE\Samples\Farm\Animals;

use PE\Samples\Farm\Animal;

class Sheep extends Animal {

	function __construct() {
		parent::__construct('Sheep');
		$this->setType('sheep');
	}
}