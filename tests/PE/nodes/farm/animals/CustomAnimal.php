<?php

namespace PE\Nodes\Farm\Animals;

use PE\Nodes\Farm\AnimalNode;

class CustomAnimal extends AnimalNode {

	function __construct() {
		parent::__construct('\\PE\\Samples\\Farm\\Animals');
	}
}