<?php

namespace PE\Nodes\Farm\Buildings;

use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;
use PE\Nodes\EncoderNodeChild;

class AnimalsBuildingNode extends CustomBuilding {

	function __construct($nodeTypeName) {
		parent::__construct($nodeTypeName);

		$animals = $this->addChildNode(new EncoderNodeChild('animals'));
		$animals->setter(new NodeChildSetter('addAnimal'));
		$animals->getter(new NodeChildGetter('getAnimals'));
	}

}