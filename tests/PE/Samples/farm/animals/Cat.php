<?php

namespace PE\Samples\Farm\Animals;

use PE\Samples\Farm\Animal;

class Cat extends Animal {

	function __construct() {
		parent::__construct('Cat');
		$this->setType('cat');
	}

}